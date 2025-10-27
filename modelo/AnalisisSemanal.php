<?php
require_once 'modelo/Conexion.php';

class AnalisisSemanal
{
    private $cn;

    public function __construct()
    {
        $this->cn = (new Conexion())->getConexion();
    }

    /** Devuelve [semana_inicio, semana_fin] (domingo a sábado) para una fecha Y-m-d */
    private function rangoSemana(string $fecha)
    {
        $d = new DateTimeImmutable($fecha);
        // Lunes de esta semana
        $start = $d->modify('monday this week');
        // Domingo de esta semana
        $end   = $start->modify('sunday this week');
        return [$start->format('Y-m-d'), $end->format('Y-m-d')];
    }

    /**
     * Acumula monto en la semana:
     *  $idTipo: 1 = ingreso, 2 = gasto (ajústalo a tu catálogo)
     */
    public function acumular(int $idUsuario, string $fecha, int $idTipo, float $monto)
    {
        list($semanaInicio, $semanaFin) = $this->rangoSemana($fecha);

        $ing = ($idTipo === 1) ? $monto : 0.0;
        $gas = ($idTipo === 2) ? $monto : 0.0;
        $bal = $ing - $gas;

        // Inserta la fila de la semana o acumula si ya existe
        $sql = "INSERT INTO AnalisisSemanal
                  (id_usuario, semana_inicio, semana_fin, ingresos_totales, gastos_totales, balance)
                VALUES (?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                  ingresos_totales = ingresos_totales + ?,
                  gastos_totales   = gastos_totales   + ?,
                  balance          = ingresos_totales - gastos_totales";

        $stmt = $this->cn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->cn->error);
        }

        // TIPOS: i s s d d d d d  (8 placeholders)
        $stmt->bind_param(
            "issddddd",
            $idUsuario,
            $semanaInicio,
            $semanaFin,
            $ing,
            $gas,
            $bal,
            $ing,   // para el UPDATE
            $gas    // para el UPDATE
        );

        return $stmt->execute();
    }

    public function recalcularSemana(int $idUsuario, string $fecha)
    {
        list($semanaInicio, $semanaFin) = $this->rangoSemana($fecha);

        $sql = "SELECT
              SUM(CASE WHEN idtipo_transaccion = 1 THEN monto ELSE 0 END) AS ingresos,
              SUM(CASE WHEN idtipo_transaccion = 2 THEN monto ELSE 0 END) AS gastos
            FROM Transaccion
            WHERE id_usuario = ? AND fecha BETWEEN ? AND ?";
        $stmt = $this->cn->prepare($sql);
        $stmt->bind_param("iss", $idUsuario, $semanaInicio, $semanaFin);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        $ingresos = (float)($res['ingresos'] ?? 0);
        $gastos   = (float)($res['gastos'] ?? 0);
        $balance  = $ingresos - $gastos;

        $sql2 = "INSERT INTO AnalisisSemanal (id_usuario, semana_inicio, semana_fin, ingresos_totales, gastos_totales, balance)
             VALUES (?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
               ingresos_totales = VALUES(ingresos_totales),
               gastos_totales   = VALUES(gastos_totales),
               balance          = VALUES(balance)";
        $stmt2 = $this->cn->prepare($sql2);
        $stmt2->bind_param("issddd", $idUsuario, $semanaInicio, $semanaFin, $ingresos, $gastos, $balance);

        return $stmt2->execute();
    }

    public function getSemanaActual(int $idUsuario, string $fecha)
    {
        list($ini, $fin) = $this->rangoSemana($fecha);
        $sql = "SELECT id_usuario, semana_inicio, semana_fin,
                       ingresos_totales, gastos_totales, balance
                  FROM AnalisisSemanal
                 WHERE id_usuario=? AND semana_inicio=?";
        $stmt = $this->cn->prepare($sql);
        $stmt->bind_param("is", $idUsuario, $ini);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return $res ?: null;
    }

    public function getUltimasSemanas(int $idUsuario, int $n = 8)
    {
        $sql = "SELECT semana_inicio, semana_fin, ingresos_totales, gastos_totales, balance
                  FROM AnalisisSemanal
                 WHERE id_usuario=?
              ORDER BY semana_inicio DESC
                 LIMIT ?";
        $stmt = $this->cn->prepare($sql);
        $stmt->bind_param("ii", $idUsuario, $n);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // 1.a) Gastos por día de la semana de una fecha dada
    public function getGastosPorDiaSemana(int $idUsuario, string $fecha)
    {
        // lunes-domingo de esa semana
        [$ini, $fin] = $this->rangoSemana($fecha);

        $sql = "SELECT DATE(fecha) AS d,
                   SUM(CASE WHEN idtipo_transaccion = 2 THEN monto ELSE 0 END) AS gastos
            FROM Transaccion
            WHERE id_usuario = ? AND fecha BETWEEN ? AND ?
            GROUP BY DATE(fecha)";

        $stmt = $this->cn->prepare($sql);
        $stmt->bind_param("iss", $idUsuario, $ini, $fin);
        $stmt->execute();
        $raw = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // normaliza a 7 días (0 si no hubo gasto ese día)
        $map = [];
        foreach ($raw as $r) $map[$r['d']] = (float)$r['gastos'];

        $out = [];
        $cursor = new DateTimeImmutable($ini);
        $end    = new DateTimeImmutable($fin);
        while ($cursor <= $end) {
            $k = $cursor->format('Y-m-d');
            $out[] = ['fecha' => $k, 'gastos' => ($map[$k] ?? 0)];
            $cursor = $cursor->modify('+1 day');
        }
        return $out;
    }

    // 1.b) Top categorías del MES (solo gastos)
    public function getTopCategoriasMes(int $idUsuario, string $ym /* 'YYYY-MM' */, int $limit = 5)
    {
        $sql = "SELECT idCategoriaTransaccion AS categoria,
                   SUM(CASE WHEN idtipo_transaccion = 2 THEN monto ELSE 0 END) AS total
            FROM Transaccion
            WHERE id_usuario = ?
              AND DATE_FORMAT(fecha, '%Y-%m') = ?
            GROUP BY idCategoriaTransaccion
            ORDER BY total DESC
            LIMIT ?";
        $stmt = $this->cn->prepare($sql);
        $stmt->bind_param("isi", $idUsuario, $ym, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // (opcional) si quieres un helper para current vs previous week usando AnalisisSemanal
    public function getGastosSemanasActualPrev(int $idUsuario, string $fecha)
    {
        [$ini] = $this->rangoSemana($fecha);
        $cur = $this->getSemanaActual($idUsuario, $fecha);

        // semana anterior
        $prevFecha = (new DateTimeImmutable($ini))->modify('-1 day')->format('Y-m-d');
        $prev = $this->getSemanaActual($idUsuario, $prevFecha);

        return [
            'actual' => $cur ? (float)$cur['gastos_totales'] : 0,
            'previa' => $prev ? (float)$prev['gastos_totales'] : 0,
        ];
    }
}
