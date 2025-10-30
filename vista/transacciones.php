<section id="transactions-section" class="content-section active">
  <div class="section-header">
    <h1>Gestión de Transacciones</h1>
    <button class="btn-primary" type="button" onclick="openTransactionModal()">
      <i class="fas fa-plus"></i>
      Nueva Transacción
    </button>
  </div>

  <!-- Filtros -->
  <div class="transactions-filters">
    <div class="filter-group">
      <label>Tipo:</label>
      <select id="transaction-type-filter">
        <option value="all">Todos</option>
        <option value="1">Ingresos</option>
        <option value="2">Gastos</option>
      </select>
    </div>

    <div class="filter-group">
      <label>Categoría:</label>
      <select id="transaction-category-filter">
        <option value="all">Todas</option>
        <option value="1">Alimentación</option>
        <option value="2">Transporte</option>
        <option value="3">Educación</option>
        <option value="4">Entretenimiento</option>
        <option value="5">Salud</option>
        <option value="6">Compras</option>
        <option value="7">Otros</option>
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

  <!-- Tabla -->
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
        <?php if (isset($transacciones) && $transacciones->num_rows > 0): ?>
          <?php
          // (Opcional) mapas simples para mostrar nombre legible
          $mapTipos = [1 => 'Ingreso', 2 => 'Gasto'];
          $mapCategorias = [
            1 => 'Alimentación',
            2 => 'Transporte',
            3 => 'Educación',
            4 => 'Entretenimiento',
            5 => 'Salud',
            6 => 'Compras',
            7 => 'Otros'
          ];
          ?>
          <?php while ($t = $transacciones->fetch_assoc()):
            $idTransaccion = (int)$t['id_transaccion']; // <— define $idT
            $idTipo = (int)$t['idtipo_transaccion'];
            $idCat  = (int)$t['idCategoriaTransaccion'];
          ?>
            <tr
              data-id-transaccion="<?=  $idTransaccion ?>"
              data-id-tipo="<?= $idTipo ?>"
              data-id-categoria="<?= $idCat ?>"
              data-descripcion="<?= htmlspecialchars($t['descripcion']) ?>"
              data-monto="<?= htmlspecialchars($t['monto']) ?>"
              data-fecha="<?= htmlspecialchars($t['fecha']) ?>">
              <td><?= htmlspecialchars($t['fecha']) ?></td>
              <td><?= htmlspecialchars($t['descripcion']) ?></td>
              <td><?= htmlspecialchars($mapCategorias[$idCat] ?? $idCat) ?></td>
              <td><?= htmlspecialchars($mapTipos[$idTipo] ?? $idTipo) ?></td>
              <td><?= number_format((float)$t['monto'], 2) ?></td>
              <td>
                <button class="btn-icon" onclick="editServerTransaction('<?=  $idTransaccion ?>')">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn-icon danger" onclick="deleteServerTransaction('<?=  $idTransaccion ?>')">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          <?php endwhile; ?>  <?php else: ?> <tr>
            <td colspan="6" class="text-center">No se encontraron transacciones</td>
          </tr> <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Modal (dentro de la MISMA sección) -->
  <div id="transaction-modal" class="modal modal--in-section">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="transaction-modal-title">Nueva Transacción</h3>
        <button class="modal-close" type="button" onclick="closeTransactionModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <div class="modal-body">
        <form id="transaction-form" class="modal-form" method="post" action="index.php?action=crearTransaccion">
          <input type="hidden" id="transaction-id" name="id_transaccion" value="">
          <div class="form-group">
            <label for="transaction-type">Tipo</label>
            <select id="transaction-type" name="id_tipo" required>
              <option value="">Selecciona el tipo</option>
              <option value="1">Ingreso</option>
              <option value="2">Gasto</option>
            </select>
          </div>

          <div class="form-group">
            <label for="transaction-description">Descripción</label>
            <input type="text" id="transaction-description" name="descripcion" required>
          </div>

          <div class="form-group">
            <label for="transaction-amount">Monto (COP)</label>
            <input type="number" id="transaction-amount" name="monto" min="0" step="100" required>
          </div>

          <div class="form-group">
            <label for="transaction-category">Categoría</label>
            <select id="transaction-category" name="id_categoria" required>
              <option value="">Selecciona la categoría</option>
              <option value="1">Alimentación</option>
              <option value="2">Transporte</option>
              <option value="3">Educación</option>
              <option value="4">Entretenimiento</option>
              <option value="5">Salud</option>
              <option value="6">Compras</option>
              <option value="7">Otros</option>
            </select>
          </div>

          <div class="form-group">
            <label for="transaction-date">Fecha</label>
            <input type="date" id="transaction-date" name="fecha" value="<?= date('Y-m-d') ?>" required>
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