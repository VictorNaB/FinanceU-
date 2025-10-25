<section id="analysis-section" class="content-section active">
    <div class="section-header">
        <h1>Análisis Semanal</h1>
        <p>Análisis detallado de tus patrones de gasto</p>
    </div>

    <div class="analysis-grid">
        <div class="analysis-card">
            <div class="card-header">
                <h3>Resumen Semanal</h3>
            </div>
            <div class="card-content">
                <div class="weekly-stats">
                    <div class="weekly-stat">
                        <span class="stat-label">Ingresos:</span>
                        <span class="stat-value income" id="weekly-income">$0</span>
                    </div>
                    <div class="weekly-stat">
                        <span class="stat-label">Gastos:</span>
                        <span class="stat-value expense" id="weekly-expenses">$0</span>
                    </div>
                    <div class="weekly-stat">
                        <span class="stat-label">Balance:</span>
                        <span class="stat-value" id="weekly-balance">$0</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="analysis-card">
            <div class="card-header">
                <h3>Gastos por Día</h3>
            </div>
            <div class="card-content">
                <canvas id="daily-expenses-chart"></canvas>
            </div>
        </div>

        <div class="analysis-card">
            <div class="card-header">
                <h3>Categorías más Gastadas</h3>
            </div>
            <div class="card-content">
                <div id="top-categories" class="categories-list"></div>
            </div>
        </div>

        <div class="analysis-card">
            <div class="card-header">
                <h3>Comparación con Semana Anterior</h3>
            </div>
            <div class="card-content">
                <div id="comparison-chart" class="comparison-content"></div>
            </div>
        </div>
    </div>
</section>