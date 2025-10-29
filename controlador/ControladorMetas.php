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
            // Nombres de los inputs coinciden con el formulario en vista/metas.php
            $nombre = trim($_POST['titulo_meta'] ?? '');
            $montoObjetivo = (float)($_POST['monto_objetivo'] ?? 0);
            $fechaLimite = $_POST['fecha_limite'] ?? '';
            $descripcion = trim($_POST['descripcion'] ?? '');

            if ($idMeta <= 0 || $nombre === '' || $montoObjetivo <= 0) {
                echo "Datos inválidos"; exit;
            }

            try {
                $this->modelo->actualizarMeta($idMeta, $nombre, $montoObjetivo, $fechaLimite, $descripcion);
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
            $deleted = $this->modelo->eliminarMeta($idMeta);

            // Si la petición viene por AJAX, devolvemos JSON
            $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => (bool)$deleted]);
                exit;
            }

            // Redirección normal
            header('Location: index.php?action=app&page=metas');
            exit;
        } catch (Exception $e) {
            echo "Error al eliminar la meta: " . $e->getMessage();
        }
    }

    public function registrarProgreso() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $idUsuario = (int)($_SESSION['id_usuario'] ?? 0);

        $idMeta = (int)($_POST['id_meta'] ?? 0);
        $monto = (float)($_POST['monto'] ?? 0);

        header('Content-Type: application/json');

        if ($idUsuario <= 0) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'No autenticado']);
            exit;
        }

        if ($idMeta <= 0 || $monto <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            exit;
        }

        try {
            $meta = $this->modelo->obtenerMetaPorId($idMeta);
            if (!$meta || (int)$meta['id_usuario'] !== $idUsuario) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Meta no encontrada o no pertenece al usuario']);
                exit;
            }

            $nuevo = $this->modelo->sumarProgreso($idMeta, $monto);
            if ($nuevo === false) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error al actualizar el progreso en la base de datos']);
                exit;
            }

            $objetivo = floatval($meta['monto_objetivo'] ?? 0);
            $porcentaje = $objetivo > 0 ? round(($nuevo / $objetivo) * 100, 2) : 0;

            echo json_encode([
                'success' => true,
                'new_amount' => $nuevo,
                'objective' => $objetivo,
                'percentage' => $porcentaje,
            ]);
            exit;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al registrar el progreso: ' . $e->getMessage()]);
            exit;
        }
    }
}
