<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$html = '<div class="reminders-list">';
if (!isset($_SESSION['id_usuario'])) {
	$html .= '<p class="text-center">Inicia sesión para ver tus recordatorios</p>';
} else {
	try {
		$id = (int)$_SESSION['id_usuario'];
		$model = new RecordatorioModelo();
		$proximos = $model->obtenerProximos($id, 10);
		if (empty($proximos)) {
			$html .= '<p class="text-center">No hay recordatorios próximos</p>';
		} else {
			foreach ($proximos as $r) {
				$fecha = htmlspecialchars($r['fecha']);
				$titulo = htmlspecialchars($r['titulo']);
				$monto = number_format((float)$r['monto'], 0, ',', '.');
				$html .= "<div class=\"reminder-item\">";
				$html .= "<div class=\"reminder-title\">{$titulo}</div>";
				$html .= "<div class=\"reminder-date\">{$fecha}</div>";
				if (!empty($r['monto']) && $r['monto'] > 0) {
					$html .= "<div class=\"reminder-amount\">COP {$monto}</div>";
				}
				$html .= "</div>";
			}
		}
	} catch (Exception $e) {
		$html .= '<p class="text-center">Error cargando recordatorios</p>';
	}
}
$html .= '</div>';

echo $html;

