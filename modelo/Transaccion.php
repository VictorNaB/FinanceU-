<?php
require_once 'modelo/Conexion.php';
require_once __DIR__ . '/../modelo/Transaccion.php';
require_once __DIR__ . '/../modelo/EstadisticasUso.php';


class Transaccion
{
    private $conexion;
    private $eu;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->eu = new EstadisticasUso();
    }


    public function registrarTransaccion($id_usuario, $idtipo_transaccion, $descripcion, $monto, $idCategoriaTransaccion, $Fecha)
    {
        $stmt = $this->conexion->prepare("INSERT INTO Transaccion(id_usuario, idtipo_transaccion,descripcion,monto,idCategoriaTransaccion,fecha) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("iisdis", $id_usuario, $idtipo_transaccion, $descripcion, $monto, $idCategoriaTransaccion, $Fecha);
        $stmt->execute();
        //Insercion en tabla estadisticas
        $transaccionesRegistradas = 1;
        $stmt2 = $this->conexion->prepare("INSERT INTO EstadisticasUso (id_usuario, transacciones_registradas) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE transacciones_registradas = transacciones_registradas + 1");
        $stmt2->bind_param("ii", $id_usuario, $transaccionesRegistradas);
        $dias = $this->diasConsecutivos($id_usuario);
        $this->eu->setDiasConsecutivos($id_usuario, (int)$dias);
        return $stmt2->execute();
    }

    public function contarPorUsuario($id_usuario)
    {
        $sql = "SELECT COUNT(*) AS total FROM EstadisticasUso WHERE id_usuario = ?";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error prepare(count): " . $this->conexion->error);
        }
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return (int)($res['total'] ?? 0);
    }

    public function diasConsecutivos($idUsuario)
    {
        $sql = "SELECT fecha AS d
            FROM Transaccion 
            WHERE id_usuario = ?
              AND fecha <= CURDATE()
            GROUP BY fecha
            ORDER BY fecha DESC
            LIMIT 60";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $rs = $stmt->get_result();

        $hoy = new DateTimeImmutable(date('Y-m-d'));
        $prev = $hoy;
        $count = 0;

        while ($row = $rs->fetch_assoc()) {
            $d = new DateTimeImmutable($row['d']);
            if ($count === 0) {
                if ($d->format('Y-m-d') !== $hoy->format('Y-m-d')) break;
                $count = 1;
                $prev = $hoy->sub(new DateInterval('P1D'));
            } else {
                if ($d->format('Y-m-d') === $prev->format('Y-m-d')) {
                    $count++;
                    $prev = $prev->sub(new DateInterval('P1D'));
                } else {
                    break;
                }
            }
        }
        return $count;
    }



    public function obtenerTransacciones($id_usuario)
    {
        $stmt = $this->conexion->prepare("SELECT * FROM Transaccion WHERE id_usuario = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        return $stmt->get_result();
    }

    /**
     * Elimina una transacción por su id_transaccion y actualiza EstadisticasUso
     */
    public function eliminarTransaccion($idTransaccion)
    {
        $this->conexion->begin_transaction();
        try {
            $stmt = $this->conexion->prepare("SELECT id_usuario FROM Transaccion WHERE id_transaccion = ? LIMIT 1");
            if (!$stmt) {
                $this->conexion->rollback();
                return false;
            }
            $stmt->bind_param("i", $idTransaccion);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res ? $res->fetch_assoc() : null;
            $stmt->close();

            if (!$row) {
                $this->conexion->rollback();
                return false;
            }
            $idUsuario = (int)$row['id_usuario'];

            $del = $this->conexion->prepare("DELETE FROM Transaccion WHERE id_transaccion = ?");
            if (!$del) {
                $this->conexion->rollback();
                return false;
            }
            $del->bind_param("i", $idTransaccion);
            $ok = $del->execute();
            $del->close();

            if (!$ok) {
                $this->conexion->rollback();
                return false;
            }

            // Decrementar transacciones_registradas (sin bajar de 0)
            $stmt2 = $this->conexion->prepare("UPDATE EstadisticasUso SET transacciones_registradas = GREATEST(transacciones_registradas - 1, 0) WHERE id_usuario = ?");
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


    public function actualizarTransaccion($id_transaccion,$idtipo_transaccion, $descripcion, $monto, $idCategoriaTransaccion, $fecha)
    {
        $stmt = $this->conexion->prepare("
        UPDATE Transaccion
        SET idtipo_transaccion = ?, descripcion = ?, monto = ?, idCategoriaTransaccion = ?, fecha = ?
        WHERE id_transaccion = ?
    ");
        if (!$stmt) {
            throw new Exception("Error prepare(update): " . $this->conexion->error);
        }
        $stmt->bind_param("isdisi",$idtipo_transaccion, $descripcion, $monto, $idCategoriaTransaccion, $fecha, $id_transaccion);
        $ok = $stmt->execute();
        return $ok;
    }
}
