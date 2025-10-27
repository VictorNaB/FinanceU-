<?php
require_once __DIR__ . '/../modelo/RecordatorioModelo.php';

class ControladorRecordatorios
{
	private $modelo;

	public function __construct()
	{
		$this->modelo = new RecordatorioModelo();
	}

	public function crear()
	{
		if (session_status() === PHP_SESSION_NONE) session_start();
		if (!isset($_SESSION['id_usuario'])) {
			http_response_code(403);
			echo json_encode(['success' => false, 'message' => 'No autenticado']);
			exit;
		}

		$idUsuario = (int)$_SESSION['id_usuario'];
		$titulo = trim($_POST['titulo'] ?? ($_POST['title'] ?? ''));

		// aceptar ambos nombres de campo: monto / amount
		$monto = null;
		if (isset($_POST['monto'])) $monto = $_POST['monto'];
		if ($monto === null && isset($_POST['amount'])) $monto = $_POST['amount'];
		$monto = ($monto !== null && $monto !== '') ? (float)$monto : 0.0;
		// fecha
		$fecha = $_POST['fecha'] ?? $_POST['date'] ?? date('Y-m-d');
		$idtipo = 0;
		if (isset($_POST['idtipo_recordatorio']) && is_numeric($_POST['idtipo_recordatorio'])) {
			$idtipo = (int)$_POST['idtipo_recordatorio'];
		} elseif (isset($_POST['type']) && trim($_POST['type']) !== '') {
			$idtipo = $this->modelo->getOrCreateTipoByName($_POST['type']);
		}
        
		$idrecurrente = 0;
		if (isset($_POST['idrecurrente']) && is_numeric($_POST['idrecurrente'])) {
			$idrecurrente = (int)$_POST['idrecurrente'];
		} elseif (isset($_POST['recurring']) && trim($_POST['recurring']) !== '') {
			$idrecurrente = $this->modelo->getOrCreateRecurrenteByName($_POST['recurring']);
		}
		$descripcion = trim($_POST['descripcion'] ?? ($_POST['description'] ?? ''));

		if ($titulo === '' || $fecha === '') {
			echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
			exit;
		}

		try {
			$insertId = $this->modelo->crearRecordatorio($idUsuario, $titulo, $monto, $fecha, $idtipo, $idrecurrente, $descripcion);
			echo json_encode(['success' => true, 'id' => $insertId]);
		} catch (Exception $e) {
			http_response_code(500);
			echo json_encode(['success' => false, 'message' => $e->getMessage()]);
		}
	}

	public function obtenerProximos()
	{
		if (session_status() === PHP_SESSION_NONE) session_start();
		if (!isset($_SESSION['id_usuario'])) {
			http_response_code(403);
			echo json_encode([]);
			exit;
		}

		$idUsuario = (int)$_SESSION['id_usuario'];
		try {
			$res = $this->modelo->obtenerProximos($idUsuario, 10);
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($res);
		} catch (Exception $e) {
			http_response_code(500);
			echo json_encode(['success' => false, 'message' => $e->getMessage()]);
		}
	}

	public function eliminar()
	{
		if (session_status() === PHP_SESSION_NONE) session_start();
		if (!isset($_SESSION['id_usuario'])) {
			http_response_code(403);
			echo json_encode(['success' => false, 'message' => 'No autenticado']);
			exit;
		}
		$id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
		if ($id <= 0) {
			echo json_encode(['success' => false, 'message' => 'ID inválido']);
			exit;
		}

		try {
			$ok = $this->modelo->eliminarRecordatorio($id);
			echo json_encode(['success' => (bool)$ok]);
		} catch (Exception $e) {
			http_response_code(500);
			echo json_encode(['success' => false, 'message' => $e->getMessage()]);
		}
	}

	public function actualizar()
	{
		if (session_status() === PHP_SESSION_NONE) session_start();
		if (!isset($_SESSION['id_usuario'])) {
			http_response_code(403);
			echo json_encode(['success' => false, 'message' => 'No autenticado']);
			exit;
		}

		$id = (int)($_POST['id'] ?? 0);
		if ($id <= 0) {
			echo json_encode(['success' => false, 'message' => 'ID inválido']);
			exit;
		}

		$titulo = trim($_POST['titulo'] ?? ($_POST['title'] ?? ''));
		$monto = null;
		if (isset($_POST['monto'])) $monto = $_POST['monto'];
		if ($monto === null && isset($_POST['amount'])) $monto = $_POST['amount'];
		$monto = ($monto !== null && $monto !== '') ? (float)$monto : 0.0;
		$fecha = $_POST['fecha'] ?? $_POST['date'] ?? date('Y-m-d');
		$idtipo = 0;
		if (isset($_POST['idtipo_recordatorio']) && is_numeric($_POST['idtipo_recordatorio'])) {
			$idtipo = (int)$_POST['idtipo_recordatorio'];
		} elseif (isset($_POST['type']) && trim($_POST['type']) !== '') {
			$idtipo = $this->modelo->getOrCreateTipoByName($_POST['type']);
		}
		$idrecurrente = 0;
		if (isset($_POST['idrecurrente']) && is_numeric($_POST['idrecurrente'])) {
			$idrecurrente = (int)$_POST['idrecurrente'];
		} elseif (isset($_POST['recurring']) && trim($_POST['recurring']) !== '') {
			$idrecurrente = $this->modelo->getOrCreateRecurrenteByName($_POST['recurring']);
		}
		$descripcion = trim($_POST['descripcion'] ?? ($_POST['description'] ?? ''));

		try {
			$ok = $this->modelo->actualizarRecordatorio($id, $titulo, $monto, $fecha, $idtipo, $idrecurrente, $descripcion);
			echo json_encode(['success' => (bool)$ok]);
		} catch (Exception $e) {
			http_response_code(500);
			echo json_encode(['success' => false, 'message' => $e->getMessage()]);
		}
	}
}

