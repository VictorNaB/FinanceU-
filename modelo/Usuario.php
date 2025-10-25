<?php
require_once 'modelo/Conexion.php';

class Usuario
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }

    public function registrar($nombre, $apellido, $correo, $contrasena, $universidad, $idRol, $programa)
    {
        $contrasena_hashed = password_hash($contrasena, PASSWORD_DEFAULT);
        // Buscar universidad
        $stmt = $this->conexion->prepare("SELECT id_universidad FROM universidad WHERE nombre = ?");
        $stmt->bind_param("s", $universidad);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();
        $id_universidad = $fila ? $fila['id_universidad'] : null;
        // Si no existe, insertarla
        if (!$id_universidad) {
            $stmt = $this->conexion->prepare("INSERT INTO universidad (nombre) VALUES (?)");
            $stmt->bind_param("s", $universidad);
            $stmt->execute();
            $id_universidad = $this->conexion->insert_id;
        }

        $cod_rol = (int)$idRol;
        // Insertar estudiante
        $stmt = $this->conexion->prepare("INSERT INTO usuarios (nombre, apellido, id_universidad,id_rol,programa_estudio) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiis", $nombre, $apellido, $id_universidad, $cod_rol, $programa);
        $stmt->execute();
        $id_usuario = $this->conexion->insert_id;
        // Insertar acceso
        $stmt = $this->conexion->prepare("INSERT INTO accesos (id_usuario, correo, contrasena) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $id_usuario, $correo, $contrasena_hashed);
        return $stmt->execute();
    }

    public function verificarCredenciales($correo, $contrasena)
    {
        $stmt = $this->conexion->prepare("SELECT contrasena FROM accesos WHERE correo= ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $stmt->bind_result($contrasena_hashed);
        $stmt->fetch();
        return password_verify($contrasena, $contrasena_hashed);
    }

    public function obtenerNombreUsuario($correo)
    {
        $stmt = $this->conexion->prepare("SELECT u.nombre FROM usuarios u JOIN accesos a ON u.id_usuario = a.id_usuario WHERE a.correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $stmt->bind_result($nombre);
        $stmt->fetch();
        return $nombre;
    }

    public function obtenerInformacionUsuario($correo)
    {
        $stmt = $this->conexion->prepare("
        SELECT 
            u.id_usuario,
            u.nombre,
            u.apellido,
            a.correo,
            uni.nombre AS universidad,
            u.programa_estudio AS programa
        FROM usuarios u
        JOIN accesos a ON u.id_usuario = a.id_usuario
        JOIN universidad uni ON u.id_universidad = uni.id_universidad
        WHERE a.correo = ?
    ");
        if (!$stmt) {
            die('Error en prepare: ' . $this->conexion->error);
        }

        $stmt->bind_param('s', $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }
}
