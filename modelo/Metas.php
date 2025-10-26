<?php
require_once 'modelo/Conexion.php';

class Meta {
    private $conexion;

    public function __construct() {
        $this->conexion = (new Conexion())->getConexion();
    }

    
    public function crearMeta($idUsuario,$nombre,$montoObjetivo,$fechaLimite,$descripcion){
        $stmt=$this->conexion->prepare("INSERT INTO Metas (id_usuario, titulo_meta, monto_objetivo, fecha_limite, descripcion)
                                        VALUES (?,?,?,?,?)");
        $stmt->bind_param("isdss", $idUsuario, $nombre, $montoObjetivo, $fechaLimite, $descripcion);
        $stmt->execute();
        //Insercion en tabla estadisticas
        $metasRegistradas=1;
        $stmt2 = $this->conexion->prepare("INSERT INTO EstadisticasUso (id_usuario, metas_establecidas) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE metas_establecidas = metas_establecidas + 1");
        $stmt2->bind_param("ii", $idUsuario, $metasRegistradas);
        return $stmt2->execute();

    }

    
    public function obtenerMetas(int $idUsuario){
        $stmt = $this->conexion->prepare("SELECT * FROM Metas WHERE id_usuario = ?");
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        return $stmt->get_result();
    }

    
    public function obtenerMetaPorId(int $idMeta){
        
    }

    
    public function actualizarMeta(int $idMeta, string $nombre, float $montoObjetivo, ?string $fechaLimite = null){
        $sql = "UPDATE metas
                   SET nombre = :nombre,
                       monto_objetivo = :monto_objetivo,
                       fecha_limite = :fecha_limite
                 WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':nombre'         => $nombre,
            ':monto_objetivo' => $montoObjetivo,
            ':fecha_limite'   => $fechaLimite,
            ':id'             => $idMeta
        ]);
    }

    
    public function eliminarMeta(int $idMeta){
        try {
            $this->db->beginTransaction();

            $stmt1 = $this->conexion->prepare("DELETE FROM metas_progreso WHERE id_meta = :id");
            $stmt1->execute([':id' => $idMeta]);

            $stmt2 = $this->conexion->prepare("DELETE FROM metas WHERE id = :id");
            $stmt2->execute([':id' => $idMeta]);

            $this->conexion->commit();
            return true;
        } catch (Exception $e) {
            $this->conexion->rollBack();
            throw $e;
        }
    }

    
    public function registrarProgreso(int $idMeta, float $monto, ?string $fecha = null, ?string $nota = null){
        $sql = "INSERT INTO metas_progreso (id_meta, monto, fecha, nota, creado_en)
                VALUES (:id_meta, :monto, :fecha, :nota, NOW())";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':id_meta' => $idMeta,
            ':monto'   => $monto,
            ':fecha'   => $fecha ?: date('Y-m-d'),
            ':nota'    => $nota
        ]);
        return (int)$this->conexion->lastInsertId();
    }

    
    public function obtenerProgresos(int $idMeta){
        $sql = "SELECT * FROM metas_progreso
                 WHERE id_meta = :id_meta
              ORDER BY fecha DESC, id DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id_meta' => $idMeta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
