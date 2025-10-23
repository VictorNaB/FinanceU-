<section id="dashboard-section" class="content-section active">
  <div class="section-header">
    <h1>¡Bienvenido <?php echo htmlspecialchars($_SESSION['usuario']);?> a tu Dashboard Financiero!</h1>
    <br>
    <p>Resumen de tu situación financiera actual</p>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon income"><i class="fas fa-arrow-up"></i></div>
      <div class="stat-content">
        <div class="stat-label">Ingresos del Mes</div>
        <div class="stat-value" id="monthly-income">$0</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon expense"><i class="fas fa-arrow-down"></i></div>
      <div class="stat-content">
        <div class="stat-label">Gastos del Mes</div>
        <div class="stat-value" id="monthly-expenses">$0</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon balance"><i class="fas fa-wallet"></i></div>
      <div class="stat-content">
        <div class="stat-label">Balance Total</div>
        <div class="stat-value" id="total-balance">$0</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon savings"><i class="fas fa-piggy-bank"></i></div>
      <div class="stat-content">
        <div class="stat-label">Total Ahorrado</div>
        <div class="stat-value" id="total-savings">$0</div>
      </div>
    </div>
  </div>

  <div class="dashboard-grid">
    <div class="dashboard-card">
      <div class="card-header"><h3>Gastos por Categoría</h3></div>
      <div class="card-content"><canvas id="expenses-chart"></canvas></div>
    </div>

    <div class="dashboard-card">
      <div class="card-header"><h3>Transacciones Recientes</h3></div>
      <div class="card-content"><div id="recent-transactions" class="transactions-list"></div></div>
    </div>

    <div class="dashboard-card">
      <div class="card-header"><h3>Progreso de Metas</h3></div>
      <div class="card-content"><div id="goals-progress" class="goals-list"></div></div>
    </div>

    <div class="dashboard-card">
      <div class="card-header"><h3>Tendencia Mensual</h3></div>
      <div class="card-content"><canvas id="trend-chart"></canvas></div>
    </div>
  </div>
</section>
