<?php
require_once 'modelo/Conexion.php';

class Usuario
{
    private $conexion;
    private $lastError = null;

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
        $stmt = $this->conexion->prepare("SELECT 
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

    /**
     * Busca una universidad por nombre y la crea si no existe.
     * Devuelve el id_universidad (int) o null si hubo un error.
     */
    public function getOrCreateUniversidad(string $nombre)
    {
        try {
            $nombre = trim($nombre);
            if ($nombre === '') {
                return null;
            }

            $stmt = $this->conexion->prepare("SELECT id_universidad FROM universidad WHERE nombre = ? LIMIT 1");
            if (!$stmt) {
                throw new Exception('Error en prepare getOrCreateUniversidad: ' . $this->conexion->error);
            }
            $stmt->bind_param('s', $nombre);
            if (!$stmt->execute()) {
                throw new Exception('Error ejecutando select universidad: ' . $stmt->error);
            }
            $resultado = $stmt->get_result();
            $fila = $resultado->fetch_assoc();
            if ($fila && isset($fila['id_universidad'])) {
                return (int)$fila['id_universidad'];
            }

            // No existe: insertar
            $stmt = $this->conexion->prepare("INSERT INTO universidad (nombre) VALUES (?)");
            if (!$stmt) {
                throw new Exception('Error en prepare insert universidad: ' . $this->conexion->error);
            }
            $stmt->bind_param('s', $nombre);
            if (!$stmt->execute()) {
                throw new Exception('Error insertando universidad: ' . $stmt->error);
            }
            return $this->conexion->insert_id;
        } catch (Exception $e) {
            return null;
        }
    }

    
    public function actualizar($id_usuario, $nombre, $apellido, $correo, $id_universidad, $programa) {
        $result = [
            'success' => false,
            'affectedUsuarios' => 0,
            'affectedAccesos' => 0,
            'error' => null
        ];

        try {
            // Iniciar transacción
            $this->conexion->begin_transaction();

            // 1. Actualizar la tabla usuarios
            $stmt = $this->conexion->prepare("UPDATE usuarios SET 
                nombre = ?, 
                apellido = ?, 
                id_universidad = ?, 
                programa_estudio = ? 
                WHERE id_usuario = ?");

            if (!$stmt) {
                throw new Exception("Error preparando la actualización de usuarios: " . $this->conexion->error);
            }

            // Validar que el ID de usuario sea un número
            $id_usuario = (int)$id_usuario;
            if ($id_usuario <= 0) {
                throw new Exception("ID de usuario inválido");
            }

            $stmt->bind_param("ssisi", $nombre, $apellido, $id_universidad, $programa, $id_usuario);

            if (!$stmt->execute()) {
                throw new Exception("Error actualizando usuarios: " . $stmt->error);
            }

            // Registrar cuántas filas fueron afectadas por la actualización de usuarios
            $result['affectedUsuarios'] = (int)$this->conexion->affected_rows;

            // 2. Actualizar el correo en la tabla accesos si se proporcionó uno
            if (!empty($correo)) {
                $stmt = $this->conexion->prepare("UPDATE accesos SET correo = ? WHERE id_usuario = ?");

                if (!$stmt) {
                    throw new Exception("Error preparando la actualización de accesos: " . $this->conexion->error);
                }

                $stmt->bind_param("si", $correo, $id_usuario);

                if (!$stmt->execute()) {
                    throw new Exception("Error actualizando accesos: " . $stmt->error);
                }
                $result['affectedAccesos'] = (int)$this->conexion->affected_rows;
            }

            // Si todo salió bien, confirmar los cambios
            $this->conexion->commit();
            $result['success'] = true;
            return $result;

        } catch (Exception $e) {
            // Si algo salió mal, deshacer los cambios
            $this->conexion->rollback();
            
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function eliminarUsuario($id_usuario) {
        try {
            $id_usuario = (int)$id_usuario;
            if ($id_usuario <= 0) {
                throw new Exception('ID de usuario inválido');
            }

            // Iniciar transacción para borrar accesos y usuario de forma atómica
            $this->conexion->begin_transaction();

            $stmt = $this->conexion->prepare("DELETE FROM accesos WHERE id_usuario = ?");
            if (!$stmt) throw new Exception('Error en prepare accesos: ' . $this->conexion->error);
            $stmt->bind_param("i", $id_usuario);
            if (!$stmt->execute()) throw new Exception('Error eliminando accesos: ' . $stmt->error);

            $stmt = $this->conexion->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
            if (!$stmt) throw new Exception('Error en prepare usuarios: ' . $this->conexion->error);
            $stmt->bind_param("i", $id_usuario);
            if (!$stmt->execute()) throw new Exception('Error eliminando usuario: ' . $stmt->error);

            $this->conexion->commit();
            return true;
        } catch (Exception $e) {
            // En caso de error deshacemos la transacción y devolvemos false
            if ($this->conexion->errno) {
                $this->conexion->rollback();
            }
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function actualizarContrasena($id_usuario, $nueva_contrasena) {
        try {
            $id_usuario = (int)$id_usuario;
            if ($id_usuario <= 0) {
                throw new Exception('ID de usuario inválido');
            }

            $contrasena_hashed = password_hash($nueva_contrasena, PASSWORD_DEFAULT);

            $stmt = $this->conexion->prepare("UPDATE accesos SET contrasena = ? WHERE id_usuario = ?");
            if (!$stmt) throw new Exception('Error en prepare cambiarContrasena: ' . $this->conexion->error);
            $stmt->bind_param("si", $contrasena_hashed, $id_usuario);
            if (!$stmt->execute()) throw new Exception('Error cambiando contraseña: ' . $stmt->error);

            return true;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function getLastError()
    {
        return $this->lastError;
    }

}