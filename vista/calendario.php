<section id="calendar-section" class="content-section active">
    <div class="section-header">
        <h1>Calendario y Recordatorios</h1>
        <button class="btn-primary" onclick="openReminderModal()">
            <i class="fas fa-plus"></i>
            Nuevo Recordatorio
        </button>
    </div>

    <div class="calendar-grid">
        <div class="calendar-card">
            <div class="card-header">
                <h3>Calendario</h3>
                <div class="calendar-nav">
                    <button id="prev-month" class="calendar-nav-btn">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span id="current-month">Diciembre 2024</span>
                    <button id="next-month" class="calendar-nav-btn">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            <div class="card-content">
                <div class="calendar" id="calendar"></div>
            </div>
        </div>

        <div class="reminders-card">
            <div class="card-header">
                <h3>Próximos Recordatorios</h3>
            </div>
            <div class="card-content">
                <?php
                if (session_status() === PHP_SESSION_NONE) session_start();
                require_once __DIR__ . '/../modelo/RecordatorioModelo.php';

                echo '<div id="reminders-list" class="reminders-list" data-server-rendered="1">';

                if (!isset($_SESSION['id_usuario'])) {
                    echo '<p class="text-center">Inicia sesión para ver tus recordatorios</p>';
                } else {
                    try {
                        $model = new RecordatorioModelo();
                        $proximos = $model->obtenerProximos((int)$_SESSION['id_usuario'], 10);
                        if (empty($proximos)) {
                            echo '<p class="text-center">No hay recordatorios próximos</p>';
                        } else {
                                    function formatoFechaEsp($fecha)
                                    {
                                        $m = [
                                            '01' => 'ene', '02' => 'feb', '03' => 'mar', '04' => 'abr', '05' => 'may', '06' => 'jun',
                                            '07' => 'jul', '08' => 'ago', '09' => 'sep', '10' => 'oct', '11' => 'nov', '12' => 'dic'
                                        ];
                                        $d = new DateTime($fecha);
                                        $dd = $d->format('j');
                                        $mm = $d->format('m');
                                        $yy = $d->format('Y');
                                        return sprintf('%s de %s de %s', $dd, $m[$mm] ?? $mm, $yy);
                                    }

                                    foreach ($proximos as $r) {
                                        $fecha_raw = $r['fecha'];
                                        $fecha = htmlspecialchars($fecha_raw);
                                        $titulo = htmlspecialchars($r['titulo']);
                                        $monto_val = (isset($r['monto']) ? (float)$r['monto'] : 0.0);
                                        $monto = $monto_val > 0 ? '$ ' . number_format($monto_val, 0, ',', '.') : '';
                                        $idr = (int)$r['id_recordatorio'];
                                        $desc = htmlspecialchars($r['descripcion'] ?? '');

                                        // calcular días restantes
                                        $hoy = new DateTimeImmutable(date('Y-m-d'));
                                        $dobj = new DateTimeImmutable($fecha_raw);
                                        $interval = $hoy->diff($dobj);
                                        $dias = (int)$interval->format('%r%a');
                                        if ($dias === 0) $diasTxt = '(Hoy)';
                                        elseif ($dias === 1) $diasTxt = '(Mañana)';
                                        elseif ($dias < 0) $diasTxt = '(' . abs($dias) . ' días ago)';
                                        else $diasTxt = '(' . $dias . ' días)';

                                        echo '<div class="reminder-item" '
                                            . 'data-id="' . $idr . '" '
                                            . 'data-title="' . $titulo . '" '
                                            . 'data-date="' . $fecha . '" '
                                            . 'data-amount="' . htmlspecialchars((string)$monto_val) . '" '
                                            . 'data-description="' . $desc . '">';

                                        echo '<div class="reminder-title">' . $titulo . '</div>';
                                        echo '<div class="reminder-date">' . formatoFechaEsp($fecha_raw) . ' ' . $diasTxt . '</div>';
                                        if ($monto !== '') {
                                            echo '<div class="reminder-amount">' . $monto . '</div>';
                                        }
                                        echo '<div style="margin-top:.5rem;">';
                                        echo '<button class="btn-secondary" onclick="editServerReminder(' . $idr . ')" style="padding:.25rem .5rem;font-size:.875rem;"><i class="fas fa-edit"></i></button> ';
                                        echo '<button class="btn-danger" onclick="deleteServerReminder(' . $idr . ')" style="padding:.25rem .5rem;font-size:.875rem;"><i class="fas fa-trash"></i></button>';
                                        echo '</div>';

                                        echo '</div>';
                                    }
                        }
                    } catch (Exception $e) {
                        echo '<p class="text-center">Error cargando recordatorios</p>';
                    }
                }

                echo '</div>';
                ?>
            </div>
        </div>
    </div>


    <!-- Reminder Modal -->
    <div id="reminder-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="reminder-modal-title">Nuevo Recordatorio</h3>
                <button class="modal-close" onclick="closeReminderModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="reminder-form" class="modal-form">
                    <div class="form-group">
                        <label for="reminder-title">Título</label>
                        <input type="text" id="reminder-title" name="title" required>
                    </div>

                    <div class="form-group">
                        <label for="reminder-amount">Monto (COP)</label>
                        <input type="number" id="reminder-amount" name="amount" min="0" step="100">
                    </div>

                    <div class="form-group">
                        <label for="reminder-date">Fecha</label>
                        <input type="date" id="reminder-date" name="date" required>
                    </div>

                    <div class="form-group">
                        <label for="reminder-type">Tipo</label>
                        <select id="reminder-type" name="type" required>
                            <option value="">Selecciona el tipo</option>
                            <option value="payment">Pago</option>
                            <option value="income">Ingreso Esperado</option>
                            <option value="goal">Meta Financiera</option>
                            <option value="other">Otro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="reminder-recurring">Recurrente</label>
                        <select id="reminder-recurring" name="recurring">
                            <option value="none">No recurrente</option>
                            <option value="daily">Diario</option>
                            <option value="weekly">Semanal</option>
                            <option value="monthly">Mensual</option>
                            <option value="yearly">Anual</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="reminder-description">Descripción</label>
                        <textarea id="reminder-description" name="description" rows="3"></textarea>
                    </div>

                    <div class="modal-actions">
                        <button type="button" class="btn-secondary" onclick="closeReminderModal()">
                            Cancelar
                        </button>
                        <button type="submit" class="btn-primary">
                            Crear Recordatorio
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>