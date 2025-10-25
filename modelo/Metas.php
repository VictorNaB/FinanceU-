<?php
require_once 'modelo/Conexion.php';

class Meta {
    private $db;

    public function __construct() {
        $this->db = Conexion::getConexion();
    }

    
    public function crearMeta(int $idUsuario, string $nombre, float $montoObjetivo, string $fechaLimite): int {
        $sql = "INSERT INTO metas (id_usuario, nombre, monto_objetivo, fecha_limite, creado_en)
                VALUES (:id_usuario, :nombre, :monto_objetivo, :fecha_limite, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_usuario'     => $idUsuario,
            ':nombre'         => $nombre,
            ':monto_objetivo' => $montoObjetivo,
            ':fecha_limite'   => $fechaLimite
        ]);
        return (int)$this->db->lastInsertId();
        $metaEstablecidas = 1; 
        $stmt2 = $this->conexion->prepare("INSERT INTO EstadisticasUso (id_usuario, metas_establecidas) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE metas_establecidas = metas_establecidas + 1");
        $stmt2->bind_param("ii", $id_usuario, $metaEstablecidas);
        return $stmt2->execute();
    }

    
    public function obtenerMetas(int $idUsuario): array {
        $sql = "SELECT m.*,
                       COALESCE(SUM(p.monto),0) AS monto_acumulado
                  FROM metas m
             LEFT JOIN metas_progreso p ON p.id_meta = m.id
                 WHERE m.id_usuario = :id_usuario
              GROUP BY m.id
              ORDER BY m.creado_en DESC, m.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_usuario' => $idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function obtenerMetaPorId(int $idMeta): ?array {
        $sql = "SELECT m.*,
                       COALESCE(SUM(p.monto),0) AS monto_acumulado
                  FROM metas m
             LEFT JOIN metas_progreso p ON p.id_meta = m.id
                 WHERE m.id = :id
              GROUP BY m.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $idMeta]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    
    public function actualizarMeta(int $idMeta, string $nombre, float $montoObjetivo, ?string $fechaLimite = null): bool {
        $sql = "UPDATE metas
                   SET nombre = :nombre,
                       monto_objetivo = :monto_objetivo,
                       fecha_limite = :fecha_limite
                 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre'         => $nombre,
            ':monto_objetivo' => $montoObjetivo,
            ':fecha_limite'   => $fechaLimite,
            ':id'             => $idMeta
        ]);
    }

    
    public function eliminarMeta(int $idMeta): bool {
        try {
            $this->db->beginTransaction();

            $stmt1 = $this->db->prepare("DELETE FROM metas_progreso WHERE id_meta = :id");
            $stmt1->execute([':id' => $idMeta]);

            $stmt2 = $this->db->prepare("DELETE FROM metas WHERE id = :id");
            $stmt2->execute([':id' => $idMeta]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    
    public function registrarProgreso(int $idMeta, float $monto, ?string $fecha = null, ?string $nota = null): int {
        $sql = "INSERT INTO metas_progreso (id_meta, monto, fecha, nota, creado_en)
                VALUES (:id_meta, :monto, :fecha, :nota, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_meta' => $idMeta,
            ':monto'   => $monto,
            ':fecha'   => $fecha ?: date('Y-m-d'),
            ':nota'    => $nota
        ]);
        return (int)$this->db->lastInsertId();
    }

    
    public function obtenerProgresos(int $idMeta): array {
        $sql = "SELECT * FROM metas_progreso
                 WHERE id_meta = :id_meta
              ORDER BY fecha DESC, id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_meta' => $idMeta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
