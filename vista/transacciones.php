<section id="transactions-section" class="content-section active">
  <div class="section-header">
    <h1>Gestión de Transacciones</h1>
    <button class="btn-primary" onclick="openTransactionModal()">
      <i class="fas fa-plus"></i>
      Nueva Transacción
    </button>
  </div>

  <div class="transactions-filters">
    <div class="filter-group">
      <label>Tipo:</label>
      <select id="transaction-type-filter">
        <option value="all">Todos</option>
        <option value="income">Ingresos</option>
        <option value="expense">Gastos</option>
      </select>
    </div>

    <div class="filter-group">
      <label>Categoría:</label>
      <select id="transaction-category-filter">
        <option value="all">Todas</option>
        <option value="food">Alimentación</option>
        <option value="transport">Transporte</option>
        <option value="education">Educación</option>
        <option value="entertainment">Entretenimiento</option>
        <option value="health">Salud</option>
        <option value="shopping">Compras</option>
        <option value="other">Otros</option>
      </select>
    </div>

    <div class="filter-group">
      <label>Fecha:</label>
      <input type="date" id="transaction-date-filter">
    </div>

    <button class="btn-secondary" onclick="clearFilters()">
      <i class="fas fa-times"></i>
      Limpiar
    </button>
  </div>

  <div class="transactions-table-container">
    <table class="transactions-table" id="transactions-table">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Descripción</th>
          <th>Categoría</th>
          <th>Tipo</th>
          <th>Monto</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="transactions-tbody">
        <tr><td colspan="6" class="text-center">No se encontraron transacciones</td></tr>
      </tbody>
    </table>
  </div>

  <!-- 🧾 Modal de Nueva Transacción -->
  <div id="transaction-modal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="transaction-modal-title">Nueva Transacción</h3>
        <button class="modal-close" onclick="closeTransactionModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="modal-body">
        <form id="transaction-form" class="modal-form">
          <div class="form-group">
            <label for="transaction-type">Tipo</label>
            <select id="transaction-type" name="type" required>
              <option value="">Selecciona el tipo</option>
              <option value="income">Ingreso</option>
              <option value="expense">Gasto</option>
            </select>
          </div>

          <div class="form-group">
            <label for="transaction-description">Descripción</label>
            <input type="text" id="transaction-description" name="description" required>
          </div>

          <div class="form-group">
            <label for="transaction-amount">Monto (COP)</label>
            <input type="number" id="transaction-amount" name="amount" min="0" step="100" required>
          </div>

          <div class="form-group">
            <label for="transaction-category">Categoría</label>
            <select id="transaction-category" name="category" required>
              <option value="">Selecciona la categoría</option>
              <option value="food">Alimentación</option>
              <option value="transport">Transporte</option>
              <option value="education">Educación</option>
              <option value="entertainment">Entretenimiento</option>
              <option value="health">Salud</option>
              <option value="shopping">Compras</option>
              <option value="other">Otros</option>
            </select>
          </div>

          <div class="form-group">
            <label for="transaction-date">Fecha</label>
            <input type="date" id="transaction-date" name="date" required>
          </div>

          <div class="modal-actions">
            <button type="button" class="btn-secondary" onclick="closeTransactionModal()">Cancelar</button>
            <button type="submit" class="btn-primary">Guardar Transacción</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
    
    
    
   