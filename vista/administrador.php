<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario es administrador
if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] !== '1') {
    header('Location: index.php');
    exit;
}
?>
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="vista/css/styles.css" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <title>FinanceU</title>
</head>
<body>

<section id="admin-dashboard" class="content-section active">
    <div class="section-header">
        <h1>Panel de Administración</h1>
        <p>Gestión de usuarios y estadísticas del sistema</p>
    </div>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-success">
            <?php 
                echo htmlspecialchars($_SESSION['mensaje']); 
                unset($_SESSION['mensaje']);
            ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php 
                echo htmlspecialchars($_SESSION['error']); 
                unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Total Usuarios</div>
                <div class="stat-value" id="total-users">0</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-university"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Universidades Registradas</div>
                <div class="stat-value" id="total-universities">0</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Transacciones Totales</div>
                <div class="stat-value" id="total-transactions">0</div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="dashboard-card">
        <div class="card-header">
            <h3>Usuarios Registrados</h3>
            <div class="header-actions">
                <input type="text" id="search-users" placeholder="Buscar usuarios..." class="search-input">
                <select id="filter-university" class="filter-select">
                    <option value="">Todas las universidades</option>
                </select>
            </div>
        </div>
        <div class="card-content">
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Correo</th>
                            <th>Universidad</th>
                            <th>Programa</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="users-table-body">
                        <!-- La tabla se llenará dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Confirmar Eliminación</h3>
                <button class="modal-close" onclick="closeDeleteModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que quieres eliminar a este usuario? Esta acción no se puede deshacer.</p>
                <div class="modal-actions">
                    <button class="btn-secondary" onclick="closeDeleteModal()">Cancelar</button>
                    <button class="btn-danger" id="confirm-delete">Eliminar Usuario</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- El JavaScript de esta vista se carga desde vista/js/script.js -->

</body>