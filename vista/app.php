<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}


$page = strtolower($_GET['page'] ?? 'dashboard');      // nombres simples
$allowed = ['dashboard','transacciones','perfil','metas','analisis','calendario'];     // agrega más si hace falta
if (!in_array($page, $allowed)) { $page = 'dashboard'; }

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="vista/css/styles.css" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <title>FinanceU</title>
</head>
<body>
  <div id="main-app" class="page">

    <!-- SIDEBAR -->
    <nav class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <div class="logo"><i class="fas fa-graduation-cap"></i><span>FinanceU</span></div>
        <button class="sidebar-toggle" id="sidebar-toggle"><i class="fas fa-bars"></i></button>
      </div>

      <div class="sidebar-content">
        <div class="user-info" id="user-info">
          <div class="user-avatar"><i class="fas fa-user"></i></div>
          <div class="user-details">
            <div class="user-name"><?php echo htmlspecialchars($_SESSION['usuario']);?></div>
            <div class="user-email"><?php echo htmlspecialchars($_SESSION['correo']);?></div>
          </div>
        </div>

        <ul class="nav-menu">
          <li class="nav-item">
            <a href="index.php?action=app&page=dashboard" class="nav-link <?php echo $page==='dashboard'?'active':'';?>">
              <i class="fas fa-chart-line"></i><span>Dashboard</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php?action=app&page=transacciones" class="nav-link <?php echo $page==='transacciones'?'active':'';?>">
              <i class="fas fa-exchange-alt"></i><span>Transacciones</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php?action=app&page=metas" class="nav-link <?php echo $page==='metas'?'active':'';?>">
              <i class="fas fa-piggy-bank"></i><span>Metas</span>
            </a>
          </li>

           <li class="nav-item">
            <a href="index.php?action=app&page=analisis" class="nav-link <?php echo $page==='analisis'?'active':'';?>">
              <i class="fas fa-chart-bar"></i><span>Análisis</span>
            </a>
          </li>

           <li class="nav-item">
            <a href="index.php?action=app&page=calendario" class="nav-link <?php echo $page==='calendario'?'active':'';?>">
               <i class="fas fa-calendar-alt"></i><span>Calendario</span>
            </a>
          </li>

          <li class="nav-item">
            <a href="index.php?action=app&page=perfil" class="nav-link <?php echo $page==='perfil'?'active':'';?>">
              <i class="fas fa-user-cog"></i><span>Perfil</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="sidebar-footer">
        <a class="logout-btn" href="index.php?action=mostrarLogin">
          <i class="fas fa-sign-out-alt"></i><span>Cerrar Sesión</span>
        </a>
      </div>
    </nav>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="main-content">
      <header class="mobile-header">
        <button class="mobile-menu-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
        <div class="logo"><i class="fas fa-graduation-cap"></i><span>FinanceU</span></div>
        <div class="header-actions"><button class="notification-btn"><i class="fas fa-bell"></i></button></div>
      </header>

      <!-- Aquí se incluye la vista solicitada: vista/dashboard.php, vista/transactions.php, etc. -->
      <?php
        $file = __DIR__ . "/{$page}.php";
        if (is_file($file)) {
          include $file;
        } else {
          echo "<section class='content-section'><h2>Vista no encontrada</h2><p>{$page}.php</p></section>";
        }
      ?>

      <div id="toast-container" class="toast-container"></div>
    </main>
  </div>

 <script src="vista/js/script.js?v=6"></script>

</body>
</html>
