<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>

<section id="profile-section" class="content-section active">
    <div class="section-header">
        <h1>Perfil de Usuario</h1>
        <p>Gestiona tu información personal</p>
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

    <div class="profile-grid">
        <div class="goal-card">
            <div class="card-header">
                <h3>Información Personal</h3>
            </div>
            <div class="card-content">
                <form id="profile-form" class="profile-form" method="POST" action="index.php?action=actualizarPerfil">
                    <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($_SESSION['id_usuario']); ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="profile-firstname">Nombre</label>
                            <input type="text"  name="nombre" value="<?php echo htmlspecialchars($_SESSION['usuario']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="profile-lastname">Apellido</label>
                            <input type="text" name="apellido" value="<?php echo htmlspecialchars($_SESSION['apellido']); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="profile-email">Correo Electrónico</label>
                        <input type="email" name="correo" value="<?php echo htmlspecialchars($_SESSION['correo']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="profile-university">Universidad</label>
                        <select  name="universidad">
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
                        <input type="text" name="programa" value="<?php echo htmlspecialchars($_SESSION['programa']); ?>"
                            placeholder="Ej: Ingeniería de Sistemas">
                    </div>

                    <button type="submit" class="btn-primary" >
                        <i class="fas fa-save"></i>
                        Guardar Cambios
                    </button>
            
                </form>
            </div>
        </div>
        
    <div class="goal-card">
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

    <div class="goal-card profile-actions">
            <!-- Button to trigger modal -->
            <button type="button" class="btn-secondary" onclick="abrirModalContrasena()">
            <i class="fas fa-key"></i>
            Cambiar Contraseña
            </button>

            <!-- Modal para cambiar contraseña -->
            <div id="modalContrasena" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Cambiar Contraseña</h3>
                        <button class="modal-close" onclick="cerrarModalContrasena()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="goal-form" class="modal-form" method="POST" action="index.php?action=cambiarContrasena" onsubmit="return validarContrasenas();">
                            <div class="form-group">
                                <input type="password" class="form-group-input" name="nuevaContrasena" id="nuevaContrasena" placeholder="Nueva contraseña" required>
                                <input type="password" class="form-group-input" name="confirmarContrasena" id="confirmarContrasena" placeholder="Confirmar contraseña" required>
                            </div>
                            <div class="modal-actions">
                                <button type="button" class="btn-secondary" onclick="cerrarModalContrasena()">Cancelar</button>
                                <button type="submit" class="btn-primary">
                                    <i class="fas fa-save"></i>
                                    Guardar Contraseña
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <br>
            <br>

            <!--eliminar cuenta-->
            <form method="post" action="index.php?action=eliminarCuenta" onsubmit="return confirm('¿Estás seguro de que quieres eliminar tu cuenta? Esta acción no se puede deshacer.');">
                <button type="submit" class="btn-danger" id="delete-account-btn">
                    <i class="fas fa-user-slash"></i>
                    Eliminar Cuenta
                </button>
            </form>
        </div>
    </div>
</section>
<script>

// Funciones para el modal de cambio de contraseña (usa la clase "active" para centrar via CSS)
function abrirModalContrasena() {
    const modal = document.getElementById('modalContrasena');
    if (!modal) return;
    modal.classList.add('active');
}

function cerrarModalContrasena() {
    const modal = document.getElementById('modalContrasena');
    if (!modal) return;
    modal.classList.remove('active');
}

// Cerrar modal si se hace clic fuera de él (delegado global)
window.addEventListener('click', function(event) {
    const modal = document.getElementById('modalContrasena');
    if (!modal) return;
    if (event.target === modal) {
        cerrarModalContrasena();
    }
});

function validarContrasenas() {
    var nueva = document.getElementById('nuevaContrasena').value;
    var confirmacion = document.getElementById('confirmarContrasena').value;
    
    if (nueva !== confirmacion) {
        alert('Las contraseñas no coinciden');
        return false;
    }
    if (nueva.length < 6) {
        alert('La contraseña debe tener al menos 6 caracteres');
        return false;
    }
    return true;
}

// Inicializar el modal cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalContrasena');
    if (modal) modal.classList.remove('active');
});
</script>