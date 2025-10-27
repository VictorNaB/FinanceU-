<?php
require_once 'modelo/Conexion.php';

class EstadisticasUso {
  private $conexion;

  public function __construct() {
    $this->conexion = (new Conexion())->getConexion();
  }

  public function getByUsuario($idUsuario){
    $stmt = $this->conexion->prepare("SELECT id_usuario, transacciones_registradas, metas_establecidas, dias_consecutivos
            FROM EstadisticasUso WHERE id_usuario = ?");
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    return $res ?: ["id_usuario"=>$idUsuario,"transacciones_registradas"=>0,"metas_establecidas"=>0,"dias_consecutivos"=>0];
  }

  public function setDiasConsecutivos($idUsuario,$dias){
    $sql = "INSERT INTO EstadisticasUso (id_usuario, dias_consecutivos)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE dias_consecutivos = VALUES(dias_consecutivos)";
    $stmt = $this->conexion->prepare($sql);
    $stmt->bind_param("ii", $idUsuario, $dias);
    return $stmt->execute();
  }
}
