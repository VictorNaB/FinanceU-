<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}


$page = strtolower($_GET['page'] ?? 'dashboard');
$allowed = ['dashboard', 'transacciones', 'perfil', 'metas', 'analisis', 'calendario'];
if (!in_array($page, $allowed, true)) {
  $page = 'dashboard';
}

if ($page === 'transacciones') {
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
  if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php?action=mostrarLogin');
    exit;
  }
  require_once __DIR__ . '/../modelo/Transaccion.php'; // ajusta ruta si tu app.php no está en /vista
  $txModel = new Transaccion();
  $transacciones = $txModel->obtenerTransacciones((int)$_SESSION['id_usuario']);
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="vista/css/styles.css?v=11" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <title>FinanceU</title>
</head>

<body>
  <div id="main-app" class="page active">

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
            <div class="user-name"><?php echo htmlspecialchars($_SESSION['usuario']); ?></div>
            <div class="user-email"><?php echo htmlspecialchars($_SESSION['correo']); ?></div>
          </div>
        </div>

        <ul class="nav-menu">
          <li class="nav-item">
            <a href="index.php?action=app&page=dashboard" class="nav-link <?php echo $page === 'dashboard' ? 'active' : ''; ?>">
              <i class="fas fa-chart-line"></i><span>Dashboard</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php?action=app&page=transacciones" class="nav-link <?php echo $page === 'transacciones' ? 'active' : ''; ?>">
              <i class="fas fa-exchange-alt"></i><span>Transacciones</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php?action=app&page=metas" class="nav-link <?php echo $page === 'metas' ? 'active' : ''; ?>">
              <i class="fas fa-piggy-bank"></i><span>Metas</span>
            </a>
          </li>

          <li class="nav-item">
            <a href="index.php?action=app&page=analisis" class="nav-link <?php echo $page === 'analisis' ? 'active' : ''; ?>">
              <i class="fas fa-chart-bar"></i><span>Análisis</span>
            </a>
          </li>

          <li class="nav-item">
            <a href="index.php?action=app&page=calendario" class="nav-link <?php echo $page === 'calendario' ? 'active' : ''; ?>">
              <i class="fas fa-calendar-alt"></i><span>Calendario</span>
            </a>
          </li>

          <li class="nav-item">
            <a href="index.php?action=app&page=perfil" class="nav-link <?php echo $page === 'perfil' ? 'active' : ''; ?>">
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

        if ($page === 'metas') {
          try {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $uid = (int)($_SESSION['id_usuario'] ?? 0);

            require_once __DIR__ . '/../modelo/Metas.php'; // usa la ruta/nombre reales
            $m = new Meta();
            $metas = $m->obtenerMetas($uid); // ahora es array

            echo '<script>',
            'window.__PAGE="metas";',
            'console.log("DBG metas:", ', json_encode($metas), ');',
            'window.goalsData = ', json_encode($metas), ';',
            'document.addEventListener("DOMContentLoaded",function(){',
            '  console.log("DBG hydrate? ", typeof hydrateGoalsFromServer);',
            '  if (window.hydrateGoalsFromServer) window.hydrateGoalsFromServer();',
            '});',
            '</script>';
          } catch (Throwable $e) {
            echo '<script>console.error("Metas error:", ', json_encode($e->getMessage()), ');</script>';
          }
        }


        if ($page === 'perfil') {
          require_once __DIR__ . '/../modelo/EstadisticasUso.php';
          $eu = new EstadisticasUso();
          $stats = $eu->getByUsuario((int)$_SESSION['id_usuario']);

          echo '<script>',
          'window.usageStats = ', json_encode($stats), ';',
          'document.addEventListener("DOMContentLoaded", function(){',
          'if (window.updateProfile) window.updateProfile();',
          '});',
          '</script>';
        }

        if ($page === 'analisis') {
          require_once __DIR__ . '/../modelo/AnalisisSemanal.php';
          $as  = new AnalisisSemanal();
          $hoy = date('Y-m-d');

          // ya lo tenías:
          $as->recalcularSemana((int)$_SESSION['id_usuario'], $hoy);
          $semanaActual = $as->getSemanaActual((int)$_SESSION['id_usuario'], $hoy);
          $ultimas      = $as->getUltimasSemanas((int)$_SESSION['id_usuario'], 8);

          // NUEVO:
          $gastosDia    = $as->getGastosPorDiaSemana((int)$_SESSION['id_usuario'], $hoy);
          $topMes       = $as->getTopCategoriasMes((int)$_SESSION['id_usuario'], date('Y-m'));
          $cmp          = $as->getGastosSemanasActualPrev((int)$_SESSION['id_usuario'], $hoy);

          echo '<script>',
          'window.weeklySummary = ', json_encode($semanaActual ?: []), ';',
          'window.weeklySeries  = ', json_encode($ultimas), ';',
          'window.dailyExpenses = ', json_encode($gastosDia), ';',
          'window.topCategories = ', json_encode($topMes), ';',
          'window.weekCompare   = ', json_encode($cmp), ';',
          // hidrata al cargar
          'document.addEventListener("DOMContentLoaded", function(){',
          'if (window.hydrateWeeklyFromServer) window.hydrateWeeklyFromServer();',
          'if (typeof updateAnalysis === "function") updateAnalysis();',
          '});',
          '</script>';
        }

        if ($page === 'dashboard') {
          try {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $uid = (int)($_SESSION['id_usuario'] ?? 0);

            $tot = ['ingresos' => 0.0, 'gastos' => 0.0, 'balance' => 0.0, 'ahorro' => 0.0];
            $cats = [];
            $recent = [];
            $goals = [];
            $errors = [];

            require_once __DIR__ . '/../modelo/Dashboard.php';
            $dash = new Dashboard();

            // rango mes actual
            $start = date('Y-m-01');
            $end   = date('Y-m-t');

            // (debug opcional)
            echo '<script>console.log("DBG uid:",', json_encode($uid), ',"rango:",', json_encode([$start, $end]), ');</script>';

            // sonda
            try {
              $dash->pingTransaccion($uid);
            } catch (Throwable $e) {
              $errors[] = $e->getMessage();
            }

            // consultas
            try {
              $tot    = $dash->getTotalesRango($uid, $start, $end);
            } catch (Throwable $e) {
              $errors[] = $e->getMessage();
            }
            try {
              $cats   = $dash->getGastosPorCategoriaRango($uid, $start, $end);
            } catch (Throwable $e) {
              $errors[] = $e->getMessage();
            }
            try {
              $recent = $dash->getTransaccionesRecientes($uid, 5);
            } catch (Throwable $e) {
              $errors[] = $e->getMessage();
            }
            try {
              $goals  = $dash->getMetasUsuario($uid, 3);
            } catch (Throwable $e) {
              $errors[] = $e->getMessage();
            }

            echo '<script>',
            'window.dashboardData = ', json_encode([
              'totals' => $tot,
              'expensesByCategory' => $cats,
              'recentTransactions' => $recent,
              'goals' => $goals,
            ]), ';', (!empty($errors)
              ? 'console.error("Dashboard SQL errors:", ' . json_encode($errors) . ');'
              : 'console.info("Dashboard SQL OK");'),
            'document.addEventListener("DOMContentLoaded",function(){',
            '  if (window.hydrateDashboardFromServer) window.hydrateDashboardFromServer();',
            '});',
            '</script>';
          } catch (Throwable $e) {
            echo '<script>',
            'window.dashboardData = {totals:{ingresos:0,gastos:0,balance:0,ahorro:0},expensesByCategory:[],recentTransactions:[],goals:[]};',
            'console.error("Dashboard fatal error:", ', json_encode($e->getMessage()), ');',
            'document.addEventListener("DOMContentLoaded",function(){',
            '  if (window.hydrateDashboardFromServer) window.hydrateDashboardFromServer();',
            '});',
            '</script>';
          }
        }
      } else {
        echo "<section class='content-section'><h2>Vista no encontrada</h2><p>{$page}.php</p></section>";
      }
      ?>

      <div id="toast-container" class="toast-container"></div>
    </main>
  </div>

  <script src="vista/js/script.js?v=11"></script>

</body>

</html>