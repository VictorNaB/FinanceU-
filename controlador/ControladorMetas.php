<?php
require_once 'modelo/Metas.php';


class ControladorMeta {
    private $modelo;

    public function __construct() {
        $this->modelo = new Meta();
    }

    
    public function crear() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['id_usuario'])) {
            header('Location: index.php?action=mostrarLogin');
            exit;
        }

        $idUsuario = (int)$_SESSION['id_usuario'];
        $nombre = trim($_POST['titulo_meta'] ?? '');
        $montoObjetivo = (float)($_POST['monto_objetivo'] ?? 0);
        $fechaLimite = $_POST['fecha_limite'] ?? date('Y-m-d');
        $descripcion = trim($_POST['descripcion'] ?? '');


        if ($nombre === '' || $montoObjetivo <= 0) {
            echo "Datos inválidos"; exit;
        }

        try {
            $this->modelo->crearMeta($idUsuario, $nombre, $montoObjetivo, $fechaLimite,$descripcion);
            header('Location: index.php?action=app&page=metas');
            exit;
        } catch (Exception $e) {
            echo "Error al crear la meta: " . $e->getMessage();
        }
    }

    
    public function mostrarMetas($id_usuario) {
        $metas = $this->modelo->obtenerMetas($id_usuario);
        require 'vista/metas.php';
    }

   
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idMeta = (int)($_POST['id_meta'] ?? 0);
            $nombre = trim($_POST['nombre'] ?? '');
            $montoObjetivo = (float)($_POST['monto_objetivo'] ?? 0);
            $fechaLimite = $_POST['fecha_limite'] ?? '';

            if ($idMeta <= 0 || $nombre === '' || $montoObjetivo <= 0) {
                echo "Datos inválidos"; exit;
            }

            try {
                $this->modelo->actualizarMeta($idMeta, $nombre, $montoObjetivo, $fechaLimite);
                header('Location: index.php?action=app&page=metas');
                exit;
            } catch (Exception $e) {
                echo "Error al actualizar la meta: " . $e->getMessage();
            }
        }
    }

  
    public function eliminar() {
        $idMeta = (int)($_GET['id'] ?? 0);

        if ($idMeta <= 0) {
            echo "ID de meta inválido"; exit;
        }

        try {
            $this->modelo->eliminarMeta($idMeta);
            header('Location: index.php?action=app&page=metas');
            exit;
        } catch (Exception $e) {
            echo "Error al eliminar la meta: " . $e->getMessage();
        }
    }

    public function registrarProgreso() {
        $idMeta = (int)($_POST['id_meta'] ?? 0);
        $monto = (float)($_POST['monto'] ?? 0);

        if ($idMeta <= 0 || $monto <= 0) {
            echo "Datos inválidos"; exit;
        }

        try {
            $this->modelo->registrarProgreso($idMeta, $monto);
            header('Location: index.php?action=app&page=metas');
            exit;
        } catch (Exception $e) {
            echo "Error al registrar el progreso: " . $e->getMessage();
        }
    }
}
