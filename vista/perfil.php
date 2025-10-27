<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>

<h2>Perfil del Usuario</h2>
<section id="profile-section" class="content-section active">
    <div class="section-header">
        <h1>Perfil de Usuario</h1>
        <p>Gestiona tu información personal</p>
    </div>

    <div class="profile-grid">
        <div class="profile-card">
            <div class="card-header">
                <h3>Información Personal</h3>
            </div>
            <div class="card-content">
                <form id="profile-form" class="profile-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="profile-firstname">Nombre</label>
                            <input type="text" id="profile-firstname" name="firstName" value="<?php echo htmlspecialchars($_SESSION['usuario']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="profile-lastname">Apellido</label>
                            <input type="text" id="profile-lastname" name="lastName" value="<?php echo htmlspecialchars($_SESSION['apellido']); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="profile-email">Correo Electrónico</label>
                        <input type="email" id="profile-email" name="email" value="<?php echo htmlspecialchars($_SESSION['correo']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="profile-university">Universidad</label>
                        <select id="profile-university" name="university">
                            <?php
                            $universidad = trim($_SESSION['universidad'] ?? '');
                            $opciones = [
                                "Universidad Nacional de Colombia",
                                "Universidad de los Andes",
                                "Pontificia Universidad Javeriana",
                                "Universidad del Rosario",
                                "Universidad de La Sabana",
                                "Universidad EAFIT",
                                "Universidad del Norte",
                                "Universidad del Magdalena",
                                "Otra"
                            ];
                            echo '<option value="">Selecciona tu universidad</option>';
                            foreach ($opciones as $opt) {
                                $sel = ($opt === $universidad) ? 'selected' : '';
                                echo "<option value=\"" . htmlspecialchars($opt) . "\" $sel>" . htmlspecialchars($opt) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="profile-program">Programa de Estudio</label>
                        <input type="text" id="profile-program" name="studyProgram" value="<?php echo htmlspecialchars($_SESSION['programa']); ?>"
                            placeholder="Ej: Ingeniería de Sistemas">
                    </div>

                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i>
                        Guardar Cambios
                    </button>
                </form>
            </div>
        </div>

        <div class="profile-card">
            <div class="card-header">
                <h3>Estadísticas de Uso</h3>
            </div>
            <div class="card-content">
                <div class="usage-stats">
                    <div class="usage-stat">
                        <div class="usage-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <div class="usage-info">
                            <div class="usage-label">Transacciones Registradas</div>
                            <div class="usage-value" id="total-transactions">0</div>
                        </div>
                    </div>

                    <div class="usage-stat">
                        <div class="usage-icon">
                            <i class="fas fa-piggy-bank"></i>
                        </div>
                        <div class="usage-info">
                            <div class="usage-label">Metas Establecidas</div>
                            <div class="usage-value" id="total-goals">0</div>
                        </div>
                    </div>

                    <div class="usage-stat">
                        <div class="usage-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="usage-info">
                            <div class="usage-label">Días Consecutivos</div>
                            <div class="usage-value" id="consecutive-days">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>