<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="vista/css/styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>login</title>
</head>

<body>


    <div id="login-page" class="active">
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <a href="index.php">
                        <button class="back-btn" onclick="showlanding()">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                    </a>
                    <div class="logo">
                        <i class="fas fa-graduation-cap"></i>
                        <span>FinanceU</span>
                    </div>
                </div>


                <div class="auth-content">
                    <h2>Iniciar Sesión</h2>
                    <p>Accede a tu cuenta para continuar gestionando tus finanzas</p>

                    <form id="login-form" class="auth-form" method="POST" action="index.php?action=iniciarSesion">
                        <div class="form-group">
                            <label for="login-email">Correo Electrónico</label>
                            <input type="email" id="login-email" name="correo" required>
                        </div>

                        <div class="form-group">
                            <label for="login-password">Contraseña</label>
                            <input type="password" id="login-password" name="contrasena" required>
                        </div>

                        <div class="auth-error" id="login-error"></div>

                        <button type="submit" class="btn-primary btn-full">
                            Iniciar Sesión
                        </button>
                    </form>

                    <div class="auth-divider">
                        <span>o</span>
                    </div>
                    <div class="auth-footer">
                        <p>¿No tienes cuenta? <a href="?action=mostrarRegistro" onclick="showRegister()">Regístrate aquí</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>

<script src="vista/js/script.js"></script>