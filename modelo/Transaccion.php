<?php
require_once 'modelo/Conexion.php';
require_once __DIR__ . '/../modelo/Transaccion.php';


class Transaccion{
     private $conexion;

     public function __construct(){
        $this->conexion= (new Conexion())->getConexion();
     }


     public function registrarTransaccion($id_usuario, $idtipo_transaccion, $descripcion, $monto, $idCategoriaTransaccion, $Fecha){
        $stmt = $this->conexion->prepare("INSERT INTO Transaccion(id_usuario, idtipo_transaccion,descripcion,monto,idCategoriaTransaccion,fecha) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("iisdis", $id_usuario, $idtipo_transaccion, $descripcion, $monto, $idCategoriaTransaccion, $Fecha);
        $stmt->execute();
        //Insercion en tabla estadisticas
        $transaccionesRegistradas = 1; 
        $stmt2 = $this->conexion->prepare("INSERT INTO EstadisticasUso (id_usuario, transacciones_registradas) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE transacciones_registradas = transacciones_registradas + 1");
        $stmt2->bind_param("ii", $id_usuario, $transaccionesRegistradas);
        return $stmt2->execute();


     }

        public function contarPorUsuario($id_usuario) {
        $sql = "SELECT COUNT(*) AS total FROM EstadisticasUso WHERE id_usuario = ?";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) { throw new Exception("Error prepare(count): ".$this->conexion->error); }
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return (int)($res['total'] ?? 0);
    }

      public function diasConsecutivos($idUsuario) {
        // Devuelve cuántos días consecutivos (hacia atrás desde hoy) tiene con transacciones
        $sql = "SELECT DISTINCT DATE(fecha) AS d
                FROM Transaccion
                WHERE id_usuario = ?
                ORDER BY d DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $rs = $stmt->get_result();

        $hoy = new DateTimeImmutable(date('Y-m-d'));
        $count = 0;
        $prev = $hoy;

        while ($row = $rs->fetch_assoc()) {
            $d = new DateTimeImmutable($row['d']);
            if ($count === 0) {
                // primer día debe ser hoy para empezar racha; si no es hoy, racha = 0
                if ($d->format('Y-m-d') !== $hoy->format('Y-m-d')) break;
                $count = 1;
                $prev = $hoy->sub(new DateInterval('P1D'));
            } else {
                // siguientes deben ser previos consecutivos
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

    
     public function obtenerTransacciones($id_usuario) {
        $stmt = $this->conexion->prepare("SELECT * FROM Transaccion WHERE id_usuario = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        return $stmt->get_result();
    }
}
     

