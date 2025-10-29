<?php
require_once __DIR__ . '/Conexion.php';

class Dashboard {
  private mysqli $cn;
  public function __construct() {
    $this->cn = (new Conexion())->getConexion();
    if (!$this->cn) throw new Exception('Sin conexión a BD');
  }

  public function getTotalesRango(int $uid, string $desde, string $hasta){
    $sql = "SELECT
              COALESCE(SUM(CASE WHEN idtipo_transaccion=1 THEN monto END),0) AS ingresos,
              COALESCE(SUM(CASE WHEN idtipo_transaccion=2 THEN monto END),0) AS gastos
            FROM Transaccion
            WHERE id_usuario=? AND DATE(fecha) BETWEEN ? AND ?";
    $st = $this->cn->prepare($sql);
    if (!$st) throw new Exception('prepare totals: '.$this->cn->error);
    $st->bind_param('iss', $uid, $desde, $hasta);
    $st->execute();
    $res = $st->get_result()->fetch_assoc() ?: ['ingresos'=>0,'gastos'=>0];
    $st->close();
    $res = ['ingresos'=>(float)$res['ingresos'], 'gastos'=>(float)$res['gastos']];
    $res['balance'] = $res['ingresos'] - $res['gastos'];
    // Calculamos ahorro como la suma de monto_actual en las metas del usuario
    try {
      $st2 = $this->cn->prepare("SELECT COALESCE(SUM(monto_actual),0) AS ahorro FROM Metas WHERE id_usuario=?");
      if ($st2) {
        $st2->bind_param('i', $uid);
        $st2->execute();
        $r2 = $st2->get_result()->fetch_assoc() ?: ['ahorro' => 0];
        $st2->close();
        $res['ahorro'] = (float)$r2['ahorro'];
      } else {
        $res['ahorro'] = 0;
      }
    } catch (Throwable $e) {
      $res['ahorro'] = 0;
    }
    return $res;
  }

  public function getGastosPorCategoriaRango(int $uid, string $desde, string $hasta){
    $sql = "SELECT idCategoriaTransaccion AS id_categoria, SUM(monto) AS total
            FROM Transaccion
            WHERE id_usuario=? AND idtipo_transaccion=2
              AND DATE(fecha) BETWEEN ? AND ?
            GROUP BY idCategoriaTransaccion
            ORDER BY total DESC";
    $st = $this->cn->prepare($sql);
    if (!$st) throw new Exception('prepare cats: '.$this->cn->error);
    $st->bind_param('iss', $uid, $desde, $hasta);
    $st->execute();
    $rows = $st->get_result()->fetch_all(MYSQLI_ASSOC) ?: [];
    $st->close();
    return $rows;
  }

  public function getTransaccionesRecientes(int $uid, int $limit=5) {
    $sql = "SELECT idtipo_transaccion, descripcion, monto, idCategoriaTransaccion, fecha
            FROM Transaccion
            WHERE id_usuario=?
            ORDER BY fecha DESC, idTransaccion DESC
            LIMIT ?";
    $st = $this->cn->prepare($sql);
    if (!$st) throw new Exception('prepare recent: '.$this->cn->error);
    $st->bind_param('ii', $uid, $limit);
    $st->execute();
    $rows = $st->get_result()->fetch_all(MYSQLI_ASSOC) ?: [];
    $st->close();
    return $rows;
  }

  public function getMetasUsuario(int $uid, int $limit=3){
    $sql = "SELECT id_meta, titulo_meta, monto_objetivo, monto_actual, fecha_limite
      FROM Metas WHERE id_usuario=?
      ORDER BY fecha_limite ASC
      LIMIT ?";
    $st = $this->cn->prepare($sql);
    if (!$st) throw new Exception('prepare metas: '.$this->cn->error);
    $st->bind_param('ii', $uid, $limit);
    $st->execute();
    $rows = $st->get_result()->fetch_all(MYSQLI_ASSOC) ?: [];
    $st->close();
    return $rows;
  }

  // (opcional) sonda para depurar
  public function pingTransaccion(int $uid){
    $st = $this->cn->prepare("SELECT COUNT(*) c FROM Transaccion WHERE id_usuario=?");
    if (!$st) throw new Exception('prepare ping: '.$this->cn->error);
    $st->bind_param('i', $uid);
    $st->execute(); $st->close();
  }
}
