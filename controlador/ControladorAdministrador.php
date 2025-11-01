<?php
require_once 'modelo/Usuario.php';

class ControladorAdministrador {
    private $modelo;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->modelo = new Usuario();
    }

    public function mostrarDashboard() {
        // Verificar si el usuario es administrador
        if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] !== '1') {
            header('Location: index.php');
            exit;
        }

    // Mostrar la vista del administrador dentro del layout para cargar CSS/JS
    $_GET['page'] = 'administrador';
    require 'vista/app.php';
    }

    public function getUsuarios() {
        // Verificar si el usuario es administrador
        if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] !== '1') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
        try {
            $usuarios = $this->modelo->obtenerTodosLosUsuarios();
            header('Content-Type: application/json');
            echo json_encode($usuarios);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function eliminarUsuarioAdmin() {
        // Verificar si el usuario es administrador
        if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] !== '1') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        $id_usuario = isset($_POST['id_usuario']) ? (int)$_POST['id_usuario'] : 0;
        if ($id_usuario <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'ID de usuario inválido']);
            exit;
        }

        try {
            $resultado = $this->modelo->eliminarUsuario($id_usuario);
            header('Content-Type: application/json');
            if ($resultado) {
                echo json_encode(['success' => true, 'message' => 'Usuario eliminado correctamente']);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'No se pudo eliminar el usuario',
                    'error' => $this->modelo->getLastError()
                ]);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}