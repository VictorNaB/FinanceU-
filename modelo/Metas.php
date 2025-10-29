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
        // Insertamos monto_actual = 0 al crear la meta
        $stmt = $this->conexion->prepare("INSERT INTO Metas (id_usuario, titulo_meta, monto_objetivo, monto_actual, fecha_limite, descripcion)
                                        VALUES (?,?,?,?,?,?)");
        $montoActual = 0.0;
    $stmt->bind_param("isddss", $idUsuario, $nombre, $montoObjetivo, $montoActual, $fechaLimite, $descripcion);
    $ok = $stmt->execute();
    // Inserción en tabla estadisticas
    $metasRegistradas = 1;
    $stmt2 = $this->conexion->prepare("INSERT INTO EstadisticasUso (id_usuario, metas_establecidas) VALUES (?, ?)
        ON DUPLICATE KEY UPDATE metas_establecidas = metas_establecidas + 1");
    $stmt2->bind_param("ii", $idUsuario, $metasRegistradas);
    $stmt2->execute();
    return $ok && $stmt2 ? true : false;
    }


    // modelo/Metas.php (o Meta.php, usa el nombre real del archivo)
    public function obtenerMetas(int $idUsuario)
    {
        $stmt = $this->conexion->prepare("SELECT id_meta, titulo_meta, monto_objetivo, monto_actual, fecha_limite, descripcion, id_usuario FROM Metas WHERE id_usuario = ?");
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        return $rows;
    }



    public function obtenerMetaPorId(int $idMeta) {
        $stmt = $this->conexion->prepare("SELECT id_meta, id_usuario, titulo_meta, monto_objetivo, monto_actual, fecha_limite, descripcion FROM Metas WHERE id_meta = ? LIMIT 1");
        $stmt->bind_param("i", $idMeta);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        return $row;
    }

    /**
     * Suma un monto al campo monto_actual de la meta y devuelve el nuevo monto_actual.
     * Retorna false en caso de error.
     */
    public function sumarProgreso(int $idMeta, float $monto)
    {
        // actualizar monto_actual = monto_actual + monto, sin superar monto_objetivo
        $this->conexion->begin_transaction();
        try {
            $meta = $this->obtenerMetaPorId($idMeta);
            if (!$meta) {
                $this->conexion->rollback();
                return false;
            }
            $actual = floatval($meta['monto_actual'] ?? 0);
            $objetivo = floatval($meta['monto_objetivo'] ?? 0);
            $nuevo = $actual + $monto;
            if ($nuevo > $objetivo) $nuevo = $objetivo;

            $stmt = $this->conexion->prepare("UPDATE Metas SET monto_actual = ? WHERE id_meta = ?");
            $stmt->bind_param("di", $nuevo, $idMeta);
            $ok = $stmt->execute();
            $stmt->close();

            if (!$ok) {
                $this->conexion->rollback();
                return false;
            }

            $this->conexion->commit();
            return $nuevo;
        } catch (Throwable $e) {
            $this->conexion->rollback();
            return false;
        }
    }


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

    /**
     * Elimina una meta por su id_meta.
     * Retorna true si la eliminación fue exitosa, false en caso contrario.
     */
    public function eliminarMeta(int $idMeta)
    {
        // Hacemos todo en una transacción: borrar la meta y decrementar el contador en EstadisticasUso
        $this->conexion->begin_transaction();
        try {
            $meta = $this->obtenerMetaPorId($idMeta);
            if (!$meta) {
                $this->conexion->rollback();
                return false;
            }

            $idUsuario = (int)$meta['id_usuario'];

            $stmt = $this->conexion->prepare("DELETE FROM Metas WHERE id_meta = ?");
            if (!$stmt) {
                $this->conexion->rollback();
                return false;
            }
            $stmt->bind_param("i", $idMeta);
            $ok = $stmt->execute();
            $stmt->close();

            if (!$ok) {
                $this->conexion->rollback();
                return false;
            }

            // Decrementar metas_establecidas (sin bajar de 0)
            $stmt2 = $this->conexion->prepare("UPDATE EstadisticasUso SET metas_establecidas = GREATEST(metas_establecidas - 1, 0) WHERE id_usuario = ?");
            if ($stmt2) {
                $stmt2->bind_param("i", $idUsuario);
                $stmt2->execute();
                $stmt2->close();
            }

            $this->conexion->commit();
            return true;
        } catch (Throwable $e) {
            $this->conexion->rollback();
            return false;
        }
    }
}
