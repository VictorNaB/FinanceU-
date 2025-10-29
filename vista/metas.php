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
          <input type="hidden" id="goal-id" name="id_meta" value="" />
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
            <button type="submit" id="goal-submit-button" class="btn-primary">Crear Meta</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- grid de metas -->
  <div id="goals-grid" class="goals-grid"></div>
  
  <!-- Modal para agregar progreso a una meta -->
  <div id="add-progress-modal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="add-progress-modal-title">Agregar Progreso a la Meta</h3>
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="modal-body">
        <form id="add-progress-form" class="modal-form" method="post" action="#">
          <div class="form-group">
            <label for="progress-amount">Monto a Agregar (COP)</label>
            <input type="number" id="progress-amount" name="amount" min="0" step="1000" required />
          </div>

          <div class="form-group" style="margin-top:.5rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;font-size:.9rem;color:var(--muted-foreground);">
              <span id="add-progress-current">Actual: $0</span>
              <span id="add-progress-target">Meta: $0</span>
            </div>
            <div class="progress-bar" style="margin-top:.5rem;"><div id="add-progress-preview-fill" class="progress-fill" style="width:0%;"></div></div>
            <div id="add-progress-percent" class="progress-percentage" style="margin-top:.25rem;font-size:.85rem;color:var(--muted-foreground);">0% completado</div>
          </div>

          <div class="modal-actions">
            <button type="button" class="btn-secondary" onclick="closeAddProgressModal()">Cancelar</button>
            <button type="submit" class="btn-primary">Agregar Progreso</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>