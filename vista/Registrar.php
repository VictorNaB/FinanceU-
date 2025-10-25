<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="vista/css/styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Registro</title>
</head>

<body>
    <div id="register" class="active">
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <a href="?action=mostrarLanding">
                        <button class="back-btn" onclick="showLanding()">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                    </a>
                    <div class="logo">
                        <i class="fas fa-graduation-cap"></i>
                        <span>FinanceU</span>
                    </div>
                </div>

                <div class="auth-content">
                    <h2>Crear Cuenta</h2>
                    <p>Únete a miles de estudiantes que ya gestionan sus finanzas</p>

                    <form id="register-form" class="auth-form" method="POST" action="index.php?action=registrar">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="register-firstname">Nombre</label>
                                <input type="text" id="register-firstname" name="nombre" required>
                            </div>

                            <div class="form-group">
                                <label for="register-lastname">Apellido</label>
                                <input type="text" id="register-lastname" name="apellido" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="register-email">Correo Electrónico</label>
                            <input type="email" id="register-email" name="correo" required>
                        </div>

                        <div class="form-group">
                            <label for="register-password">Contraseña</label>
                            <input type="password" id="register-password" name="contrasena" required>
                        </div>

                        <div class="form-group">
                            <label for="register-university">Universidad</label>
                            <select id="register-university" name="universidad" required>
                                <option value="">Selecciona tu universidad</option>
                                <option value="Universidad del Magdalena">Universidad del Magdalena</option>
                                <option value="Universidad Cooperativa de Colombia">Universidad Cooperativa de Colombia</option>
                                <option value="Universidad Nacional de Colombia">Universidad Nacional de Colombia
                                </option>
                                <option value="Universidad de los Andes">Universidad de los Andes</option>
                                <option value="Pontificia Universidad Javeriana">Pontificia Universidad Javeriana
                                </option>
                                <option value="Universidad del Rosario">Universidad del Rosario</option>
                                <option value="Universidad de La Sabana">Universidad de La Sabana</option>
                                <option value="Universidad EAFIT">Universidad EAFIT</option>
                                <option value="Universidad del Norte">Universidad del Norte</option>
                                <option value="Otra">Otra</option>
                            </select>
                        </div>

                        <input type="hidden" name="idRol" value="2">

                        <div class="form-group">
                            <label for="register-program">Programa</label>
                            <input type="text" id="register-program" name="programa" required>

                        </div>

                        <div class="auth-error" id="register-error"></div>

                        <a href="?action=mostrarLogin">
                            <button type="submit" class="btn-primary btn-full">
                                Crear Cuenta
                            </button>
                        </a>
                    </form>

                    <div class="auth-footer">
                        <p>¿Ya tienes cuenta? <a href="?action=mostrarLogin" onclick="showLogin()">Inicia sesión aquí</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>