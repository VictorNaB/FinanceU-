<?php
require_once 'modelo/Usuario.php';

class ControladorEstudiante
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new Usuario();
    }

    public function mostrarLogin()
    {
        require 'vista/Login.php';
    }

    public function mostrarDasboard()
    {
        require 'vista/app.php';
    }

    public function mostrarAdmin()
    {
        require 'vista/administrador.php';
    }

    public function iniciarSesion($correo, $contrasena)
    {
        if ($this->modelo->verificarCredenciales($correo, $contrasena)) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Obtener toda la información del usuario
            $infoUsuario = $this->modelo->obtenerInformacionUsuario($correo);

            // Si no se obtuvo información, redirigir al login con mensaje
            if (!$infoUsuario || !is_array($infoUsuario)) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['error'] = 'No se pudo cargar la información del usuario.';
                header('Location: index.php?action=mostrarLogin');
                exit();
            }

            // Guardar todas las variables de sesión antes de redirigir
            $_SESSION['id_usuario']   = $infoUsuario['id_usuario'];
            $_SESSION['id_rol']       = isset($infoUsuario['id_rol']) ? (string)$infoUsuario['id_rol'] : null;
            $_SESSION['usuario']      = $infoUsuario['nombre'];
            $_SESSION['apellido']     = $infoUsuario['apellido'];
            $_SESSION['correo']       = $infoUsuario['correo'];
            $_SESSION['universidad']  = $infoUsuario['universidad'];
            $_SESSION['programa']     = $infoUsuario['programa'];

            // Redirigir según rol: admin (1) -> panel administrador, otro -> app
            if (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] === '1') {
                header('Location: index.php?action=administrador');
                exit();
            }

            header('Location: index.php?action=app');
            exit();
        } else {
            echo "Credenciales incorrectas.";
        }
    }
    public function cerrarSesion()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        // Redirige al login o index
        header('Location: index.php?action=mostrarLogin');
        exit();
    }



    public function mostrarRegistro()
    {
        require 'vista/Registrar.php';
    }

    public function registrar($nombre, $apellido, $correo, $contrasena, $universidad, $idRol, $programa)
    {
        if ($this->modelo->registrar($nombre, $apellido, $correo, $contrasena, $universidad, $idRol, $programa)) {
            require 'vista/login.php';
        } else {
            echo "Error al registrar.";
        }
    }

    public function mostrarPerfil()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['correo'])) {
            header('Location: login.php');
            exit();
        }

        $correo = $_SESSION['correo'];
        $usuario = $this->modelo->obtenerInformacionUsuario($correo);

        include 'vista/perfil.php'; // tu vista de perfil
    }

    public function cambiarContrasena($id_usuario, $nuevaContrasena)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id_usuario'])) {
            header('Location: index.php?action=mostrarLogin');
            exit();
        }

        if ($this->modelo->actualizarContrasena($id_usuario, $nuevaContrasena)) {
            $_SESSION['mensaje'] = "Contraseña actualizada correctamente.";
            header('Location: index.php?action=mostrarPerfil');
        } else {
            $_SESSION['error'] = "Error al actualizar la contraseña: " . $this->modelo->getLastError();
            header('Location: index.php?action=mostrarPerfil');
        }
        exit();
    }



        
}
