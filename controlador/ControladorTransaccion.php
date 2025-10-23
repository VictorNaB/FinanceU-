<?php
require_once 'modelo/Transaccion.php';

class ControladorTransaccion{
    private $modelo;

    public function __construct(){
        $this->modelo=new Transaccion();
    }


    public function registrarTransaccion($id_usuario, $idtipo_transaccion, $descripcion, $monto, $idCategoriaTransaccion){
        if($this->modelo->registrarTransaccion($id_usuario, $idtipo_transaccion, $descripcion, $monto, $idCategoriaTransaccion)){
            echo "Transacción registrada con éxito.";
        } else {
            echo "Error al registrar la transacción.";
        }
    }

    public function mostrarTransacciones($id_usuario){
        $transacciones = $this->modelo->obtenerTransacciones($id_usuario);
        require 'vista/mostrarTransacciones.php';
    }
    

}
