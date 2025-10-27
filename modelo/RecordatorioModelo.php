<?php
require_once __DIR__ . '/Conexion.php';

class RecordatorioModelo
{
	private $conexion;

	public function __construct()
	{
		$this->conexion = (new Conexion())->getConexion();
	}

	public function crearRecordatorio($id_usuario, $titulo, $monto, $fecha, $idtipo_recordatorio, $idrecurrente, $descripcion)
	{
		$sql = "INSERT INTO Recordatorios (id_usuario, titulo, monto, fecha, idtipo_recordatorio, idrecurrente, descripcion) VALUES (?,?,?,?,?,?,?)";
		$stmt = $this->conexion->prepare($sql);
		if (!$stmt) {
			throw new Exception('Error prepare(insert): ' . $this->conexion->error);
		}

		$stmt->bind_param('isdsiis', $id_usuario, $titulo, $monto, $fecha, $idtipo_recordatorio, $idrecurrente, $descripcion);
		$res = $stmt->execute();
		if (!$res) {
			throw new Exception('Error execute(insert): ' . $stmt->error);
		}
		return $this->conexion->insert_id;
	}

	/**
	 * Buscar un tipo de recordatorio por nombre; si no existe, lo crea y devuelve el id
	 */
	public function getOrCreateTipoByName($nombre)
	{
		$nombre = trim($nombre);
		if ($nombre === '') return 0;
		$sql = "SELECT idtipo_recordatorio FROM TiposRecordatorio WHERE nombre = ? LIMIT 1";
		$stmt = $this->conexion->prepare($sql);
		if (!$stmt) throw new Exception('Error prepare(getTipo): ' . $this->conexion->error);
		$stmt->bind_param('s', $nombre);
		$stmt->execute();
		$res = $stmt->get_result()->fetch_assoc();
		if ($res && isset($res['idtipo_recordatorio'])) return (int)$res['idtipo_recordatorio'];

		// Insertar nuevo tipo
		$ins = $this->conexion->prepare("INSERT INTO TiposRecordatorio (nombre) VALUES (?)");
		if (!$ins) throw new Exception('Error prepare(insertTipo): ' . $this->conexion->error);
		$ins->bind_param('s', $nombre);
		if (!$ins->execute()) throw new Exception('Error execute(insertTipo): ' . $ins->error);
		return $this->conexion->insert_id;
	}

	public function getOrCreateRecurrenteByName($nombre)
	{
		$nombre = trim($nombre);
		if ($nombre === '') return 0;
		$sql = "SELECT idrecurrente FROM Recurrente WHERE nombre = ? LIMIT 1";
		$stmt = $this->conexion->prepare($sql);
		if (!$stmt) throw new Exception('Error prepare(getRecurrente): ' . $this->conexion->error);
		$stmt->bind_param('s', $nombre);
		$stmt->execute();
		$res = $stmt->get_result()->fetch_assoc();
		if ($res && isset($res['idrecurrente'])) return (int)$res['idrecurrente'];

		$ins = $this->conexion->prepare("INSERT INTO Recurrente (nombre) VALUES (?)");
		if (!$ins) throw new Exception('Error prepare(insertRecurrente): ' . $this->conexion->error);
		$ins->bind_param('s', $nombre);
		if (!$ins->execute()) throw new Exception('Error execute(insertRecurrente): ' . $ins->error);
		return $this->conexion->insert_id;
	}

	public function obtenerProximos($id_usuario, $limite = 10)
	{
		$lim = (int)$limite;
        
		$sql = "SELECT id_recordatorio, id_usuario, titulo, monto, fecha, idtipo_recordatorio, idrecurrente, descripcion FROM Recordatorios WHERE id_usuario = ? AND fecha >= CURDATE() ORDER BY fecha ASC LIMIT " . $lim;
		$stmt = $this->conexion->prepare($sql);
		if (!$stmt) {
			throw new Exception('Error prepare(select proximos): ' . $this->conexion->error);
		}
		$stmt->bind_param('i', $id_usuario);
		$stmt->execute();
		$res = $stmt->get_result();
		return $res->fetch_all(MYSQLI_ASSOC);
	}

	public function obtenerPorUsuario($id_usuario)
	{
		$sql = "SELECT * FROM Recordatorios WHERE id_usuario = ? ORDER BY fecha DESC";
		$stmt = $this->conexion->prepare($sql);
		if (!$stmt) {
			throw new Exception('Error prepare(select usuario): ' . $this->conexion->error);
		}
		$stmt->bind_param('i', $id_usuario);
		$stmt->execute();
		$res = $stmt->get_result();
		return $res->fetch_all(MYSQLI_ASSOC);
	}

	public function actualizarRecordatorio($id_recordatorio, $titulo, $monto, $fecha, $idtipo_recordatorio, $idrecurrente, $descripcion)
	{
		$sql = "UPDATE Recordatorios SET titulo = ?, monto = ?, fecha = ?, idtipo_recordatorio = ?, idrecurrente = ?, descripcion = ? WHERE id_recordatorio = ?";
		$stmt = $this->conexion->prepare($sql);
		if (!$stmt) {
			throw new Exception('Error prepare(update): ' . $this->conexion->error);
		}
		$stmt->bind_param('sdsiisi', $titulo, $monto, $fecha, $idtipo_recordatorio, $idrecurrente, $descripcion, $id_recordatorio);
		return $stmt->execute();
	}

	public function eliminarRecordatorio($id_recordatorio)
	{
		$sql = "DELETE FROM Recordatorios WHERE id_recordatorio = ?";
		$stmt = $this->conexion->prepare($sql);
		if (!$stmt) {
			throw new Exception('Error prepare(delete): ' . $this->conexion->error);
		}
		$stmt->bind_param('i', $id_recordatorio);
		return $stmt->execute();
	}
}


