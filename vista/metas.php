<section id="goals-section" class="content-section active">
  <div class="section-header section-header--split">
    <h1>Metas Financieras</h1>
    <button class="btn-primary" onclick="openGoalModal()">
      <i class="fas fa-plus"></i> Nueva Meta
    </button>
  </div>

  <!-- Modal de Meta -->
  <div id="goal-modal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="goal-modal-title">Nueva Meta</h3>
        <button class="modal-close" onclick="closeGoalModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="modal-body" >
        <form id="goal-form" class="modal-form" method="post" action="index.php?action=crearMeta">
          <input type="hidden" id="goal-id" name="id_meta" value="">
          <div class="form-group">
            <label for="goal-title">Título de la Meta</label>
            <input type="text" id="goal-title" name="titulo_meta" required>
          </div>

          <div class="form-group">
            <label for="goal-amount">Monto Objetivo (COP)</label>
            <input type="number" id="goal-amount" name="monto_objetivo" min="0" step="1000" required>
          </div>

          <div class="form-group">
            <label for="goal-deadline">Fecha Límite</label>
            <input type="date" id="goal-deadline" name="fecha_limite" required>
          </div>

          <div class="form-group">
            <label for="goal-description">Descripción</label>
            <textarea id="goal-description" name="descripcion" rows="3"></textarea>
          </div>

          <div class="modal-actions">
            <button type="button" class="btn-secondary" onclick="closeGoalModal()">Cancelar</button>
            <button type="submit" class="btn-primary" id="goal-submit-button">Crear Meta</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- grid de metas -->
  <!-- Modal para añadir progreso a una meta -->
  <div id="add-progress-modal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Agregar Progreso a la Meta</h3>
        <button class="modal-close" onclick="closeAddProgressModal()"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        <form id="add-progress-form" class="modal-form">
          <div class="form-group">
            <label for="progress-amount">Monto a Agregar (COP)</label>
            <input type="number" id="progress-amount" name="amount" min="0" step="1000" placeholder="0" required />
          </div>

          <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-top:1rem;">
            <div id="add-progress-current" style="color:var(--muted-foreground);">Actual: $0</div>
            <div id="add-progress-target" style="color:var(--muted-foreground);">Meta: $0</div>
          </div>

          <div class="progress-bar" style="margin-top:1rem;height:12px;background:var(--muted);border-radius:8px;">
            <div id="add-progress-preview-fill" class="progress-fill" style="width:0%;height:12px;background:var(--primary);border-radius:8px;"></div>
          </div>
          <div id="add-progress-percent" style="margin-top:.5rem;color:var(--muted-foreground);">0% completado</div>

          <div class="modal-actions" style="margin-top:1.25rem;">
            <button type="button" class="btn-secondary" onclick="closeAddProgressModal()">Cancelar</button>
            <button type="submit" class="btn-primary">Agregar Progreso</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div id="goals-grid" class="goals-grid"></div>
</section>