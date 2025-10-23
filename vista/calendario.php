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
                            <div id="reminders-list" class="reminders-list"></div>
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