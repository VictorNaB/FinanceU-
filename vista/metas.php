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
          <div class="form-group">
            <label for="goal-title">Título de la Meta</label>
            <input type="text" id="goal-title" name="title" required>
          </div>

          <div class="form-group">
            <label for="goal-amount">Monto Objetivo (COP)</label>
            <input type="number" id="goal-amount" name="targetAmount" min="0" step="1000" required>
          </div>

          <div class="form-group">
            <label for="goal-deadline">Fecha Límite</label>
            <input type="date" id="goal-deadline" name="deadline" required>
          </div>

          <div class="form-group">
            <label for="goal-description">Descripción</label>
            <textarea id="goal-description" name="description" rows="3"></textarea>
          </div>

          <div class="modal-actions">
            <button type="button" class="btn-secondary" onclick="closeGoalModal()">Cancelar</button>
            <button type="submit" class="btn-primary">Crear Meta</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- grid de metas -->
  <div id="goals-grid" class="goals-grid"></div>
</section>