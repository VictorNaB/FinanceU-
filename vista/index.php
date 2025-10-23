<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FinanceU - Gestión Financiera para Estudiantes</title>
    <link rel="stylesheet" href="vista/css/styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div id="app">
        <!-- Landing Page -->
        <div id="landing" class="page active">
            <div class="landing-container">
                <header class="landing-header">
                    <div class="logo">
                        <i class="fas fa-graduation-cap"></i>
                        <span>FinanceU</span>
                    </div>
                    <nav class="landing-nav">
                        <a href="?action=mostrarLogin">
                            <button class="btn-secondary" onclick="showLogin()">Iniciar Sesión</button>
                        </a>

                       
                        <nav class="landing-nav">
                        <a href="?action=mostrarRegistro">
                             <button class="btn-primary" onclick="showRegister()">Registrarse</button>
                        </a>
                    </nav>
                </header>

                <main class="landing-main">
                    <div class="hero-section">
                        <div class="hero-content">
                            <h1>Controla tus finanzas como estudiante universitario</h1>
                            <p>FinanceU te ayuda a gestionar tus ingresos, gastos y metas financieras de manera
                                inteligente. Diseñado especialmente para estudiantes colombianos.</p>

                            <div class="hero-buttons">
                                <a href="?action=mostrarRegistro">
                                    <button class="btn-primary btn-large" onclick="showRegister()">
                                    Comenzar Gratis
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                                </a>
                                
                            </div>
                        </div>

                        <div class="hero-image">
                            <div class="finance-preview">
                                <div class="preview-card">
                                    <h3>Balance Total</h3>
                                    <div class="balance">$1.250.000 COP</div>
                                </div>
                                <div class="preview-chart">
                                    <canvas id="hero-chart" width="200" height="100"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="features-section">
                        <h2>Todo lo que necesitas para manejar tu dinero</h2>
                        <div class="features-grid">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <h3>Dashboard Financiero</h3>
                                <p>Visualiza tus ingresos, gastos y balance con gráficos interactivos y análisis
                                    detallado.</p>
                            </div>

                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-piggy-bank"></i>
                                </div>
                                <h3>Metas Financieras</h3>
                                <p>Define objetivos claros y haz seguimiento de tu progreso hacia tus metas financieras.</p>
                            </div>
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <h3>Recordatorios</h3>
                                <p>Nunca olvides un pago importante con nuestro sistema de recordatorios inteligente.
                                </p>
                            </div>
                        </div>
                    </div>
                </main>

                <footer class="landing-footer">
                    <p>&copy; 2025 FinanceU. Diseñado para estudiantes universitarios colombianos.</p>
                </footer>
            </div>
        </div>

        <script src="vista/js/script.js"></script>
</body>

</html>