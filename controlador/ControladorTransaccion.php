<?php
require_once 'modelo/Transaccion.php';
require_once 'modelo/AnalisisSemanal.php';

class ControladorTransaccion
{
    private $modelo;
    private $modeloAnalisis;

    public function __construct()
    {
        $this->modelo = new Transaccion();
        $this->modeloAnalisis = new AnalisisSemanal();
    }


    public function crear()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['id_usuario'])) {
            header('Location: index.php?action=mostrarLogin');
            exit;
        }

        $idUsuario  = (int)$_SESSION['id_usuario'];
        $idTipo     = (int)($_POST['id_tipo'] ?? 0);
        $idCategoria = (int)($_POST['id_categoria'] ?? 0);
        $desc       = trim($_POST['descripcion'] ?? '');
        $monto      = (float)($_POST['monto'] ?? 0);
        $fecha      = $_POST['fecha'] ?? date('Y-m-d');

        if ($idTipo <= 0 || $idCategoria <= 0 || $desc === '' || $monto <= 0) {
            echo "Datos inválidos";
            exit;
        }

        try {
            $this->modelo->registrarTransaccion($idUsuario, $idTipo, $desc, $monto, $idCategoria, $fecha);
            $this->modeloAnalisis->acumular($idUsuario, $fecha, $idTipo, $monto);
            header('Location: index.php?action=app&page=transacciones');
            exit;
        } catch (Exception $e) {
            echo "Error al crear transacción: " . $e->getMessage();
        }
    }


    public function mostrarTransacciones($id_usuario)
    {
        $transacciones = $this->modelo->obtenerTransacciones($id_usuario);
        require 'vista/transacciones.php';
    }

    public function eliminar()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo "ID inválido";
            exit;
        }
        try {
            $ok = $this->modelo->eliminarTransaccion($id);

            $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => (bool)$ok]);
                exit;
            }

            header('Location: index.php?action=app&page=transacciones');
            exit;
        } catch (Exception $e) {
            echo "Error al eliminar transacción: " . $e->getMessage();
        }
    }
}
