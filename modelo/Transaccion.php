<?php
require_once 'modelo/conexion.php';

class Transaccion{
     private $conexion;

     public function __construct(){
        $this->conexion= (new Conexion())->getConexion();
     }


     public function registrarTransaccion($id_usuario, $idtipo_transaccion, $descripcion, $monto, $idCategoriaTransaccion){
        session_start();
        $id_usuario = $_SESSION['id_usuario']
        $stmt = $this->conexion->prepare("INSERT INTO Transaccion(id_usuario, idtipo_transaccion,descripcion,monto,idCategoriaTransaccion,fecha) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("iisdi", $id_usuario, $idtipo_transaccion, $descripcion, $monto, $idCategoriaTransaccion);
        return $stmt->execute();
     }

     public function obtenerTransacciones($id_usuario) {
        $stmt = $this->conexion->prepare("SELECT * FROM Transaccion WHERE id_usuario = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        return $stmt->get_result();
    }
}