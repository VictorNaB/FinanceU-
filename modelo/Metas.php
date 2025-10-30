<?php
require_once 'modelo/Conexion.php';

class Meta
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }


    public function crearMeta($idUsuario, $nombre, $montoObjetivo, $fechaLimite, $descripcion)
    {
        $stmt = $this->conexion->prepare("INSERT INTO Metas (id_usuario, titulo_meta, monto_objetivo, fecha_limite, descripcion)
                                        VALUES (?,?,?,?,?)");
        $stmt->bind_param("isdss", $idUsuario, $nombre, $montoObjetivo, $fechaLimite, $descripcion);
        $stmt->execute();
        //Insercion en tabla estadisticas
        $metasRegistradas = 1;
        $stmt2 = $this->conexion->prepare("INSERT INTO EstadisticasUso (id_usuario, metas_establecidas) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE metas_establecidas = metas_establecidas + 1");
        $stmt2->bind_param("ii", $idUsuario, $metasRegistradas);
        $stmt2->execute();
        return $stmt2->execute();
    }


    // modelo/Metas.php (o Meta.php, usa el nombre real del archivo)
    public function obtenerMetas(int $idUsuario)
    {
        $stmt = $this->conexion->prepare("SELECT id_meta, titulo_meta, monto_objetivo, fecha_limite, descripcion FROM Metas WHERE id_usuario = ?");
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        return $rows;
    }



    public function obtenerMetaPorId(int $idMeta) {}


    public function actualizarMeta(int $idMeta, string $nombre, float $montoObjetivo, ?string $fechaLimite = null)
    {
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
}
