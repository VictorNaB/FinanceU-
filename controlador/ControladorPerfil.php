<?php
require_once 'modelo/Usuario.php';
require_once 'modelo/Conexion.php';

class ControladorPerfil {
    private $modelo;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->modelo = new Usuario();
    }

    public function mostrarPerfil() {
        // Verificar si el usuario está autenticado
        if (!isset($_SESSION['id_usuario'])) {
            header('Location: index.php?action=mostrarLogin');
            exit;
        }

        // Obtener información actualizada del usuario
        $informacionUsuario = $this->modelo->obtenerInformacionUsuario($_SESSION['correo']);
        
        if ($informacionUsuario) {
            // Actualizar la sesión con la información más reciente
            $_SESSION['usuario'] = $informacionUsuario['nombre'];
            $_SESSION['apellido'] = $informacionUsuario['apellido'];
            $_SESSION['correo'] = $informacionUsuario['correo'];
            $_SESSION['universidad'] = $informacionUsuario['universidad'];
            $_SESSION['programa'] = $informacionUsuario['programa'];
        }

        // Mostrar dentro del shell de la app para que se carguen CSS/JS y el layout
        // Creamos la variable de página esperada por app.php y lo incluimos
        $_GET['page'] = 'perfil';
        require 'vista/app.php';
    }

    public function actualizarPerfil() {
        // Verificar si el usuario está autenticado
        if (!isset($_SESSION['id_usuario'])) {
            header('Location: index.php?action=mostrarLogin');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar y obtener los datos del formulario
            $id_usuario = isset($_SESSION['id_usuario']) ? (int)$_SESSION['id_usuario'] : 0;
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $apellido = isset($_POST['apellido']) ? trim($_POST['apellido']) : '';
            $correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
            $universidad = isset($_POST['universidad']) ? trim($_POST['universidad']) : '';
            $programa = isset($_POST['programa']) ? trim($_POST['programa']) : '';

            // Validaciones básicas
            if (empty($id_usuario) || empty($nombre) || empty($apellido) || 
                empty($universidad) || empty($programa)) {
                header('Location: index.php?action=app&page=perfil&mensaje=campos_vacios');
                exit;
            }

            try {
                // Obtener o crear universidad usando el modelo (misma conexión)
                $id_universidad = $this->modelo->getOrCreateUniversidad($universidad);

                // Si hubo un error al obtener/crear la universidad, lanzar excepción
                if ($universidad !== '' && $id_universidad === null) {
                    throw new Exception('No fue posible obtener o crear la universidad');
                }

                // Actualizar el perfil
                $resultado = $this->modelo->actualizar(
                    $id_usuario,
                    $nombre,
                    $apellido,
                    $correo,
                    $id_universidad,
                    $programa
                );

                // Si estamos en modo debug, devolver JSON con la respuesta detallada
                // aceptamos debug tanto por GET como por POST
                $debug = (isset($_REQUEST['debug']) && $_REQUEST['debug'] == '1');
                if ($debug) {
                    header('Content-Type: application/json; charset=utf-8');
                    // Asegurar que $resultado sea un array
                    if (!is_array($resultado)) {
                        $resultado = [
                            'success' => (bool)$resultado,
                            'affectedUsuarios' => null,
                            'affectedAccesos' => null,
                            'error' => null
                        ];
                    }

                    // Obtener estado actual en BD para este usuario (para comparar)
                    try {
                        $conexion = (new Conexion())->getConexion();
                        $stmt = $conexion->prepare("SELECT u.id_usuario, u.nombre, u.apellido, u.programa_estudio, a.correo, uni.nombre AS universidad FROM usuarios u JOIN accesos a ON u.id_usuario = a.id_usuario LEFT JOIN universidad uni ON u.id_universidad = uni.id_universidad WHERE u.id_usuario = ? LIMIT 1");
                        $stmt->bind_param('i', $id_usuario);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        $current = $res ? $res->fetch_assoc() : null;
                    } catch (Exception $e) {
                        $current = null;
                    }

                    $debugResponse = [
                        'result' => $resultado,
                        'posted' => $_POST,
                        'session_id' => $_SESSION['id_usuario'] ?? null,
                        'db_current' => $current,
                    ];

                    echo json_encode($debugResponse);
                    exit;
                }

                // Normal flow: redirecciones
                $success = is_array($resultado) ? ($resultado['success'] ?? false) : (bool)$resultado;
                    if ($success) {
                    // Actualizar la información en la sesión
                    $_SESSION['usuario'] = $nombre;
                    $_SESSION['apellido'] = $apellido;
                    $_SESSION['correo'] = $correo;
                    $_SESSION['universidad'] = $universidad;
                    $_SESSION['programa'] = $programa;
                    
                    header('Location: index.php?action=app&page=perfil&mensaje=success');
                    exit;
                } else {
                    // Log de error si vino en la respuesta (temporal eliminado)
                    header('Location: index.php?action=app&page=perfil&mensaje=error');
                    exit;
                }
            } catch (Exception $e) {
                header('Location: index.php?action=app&page=perfil&mensaje=error_db');
                exit;
            }
        } else {
            // Si no es una petición POST, redirigir a la vista del perfil dentro de la app
            header('Location: index.php?action=app&page=perfil');
            exit;
        }
    }

    public function EliminarCuenta() {
        // Solo permitir POST para eliminar (evita borrados por GET)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=app&page=perfil');
            exit;
        }

        // Verificar si el usuario está autenticado
        if (!isset($_SESSION['id_usuario'])) {
            header('Location: index.php?action=mostrarLogin');
            exit;
        }

        $id_usuario = (int)$_SESSION['id_usuario'];

        try {
            $deleted = $this->modelo->eliminarUsuario($id_usuario);

            if ($deleted) {
                // Cerrar sesión después de eliminar la cuenta
                session_unset();
                session_destroy();

                // Redirigir al inicio con mensaje de cuenta eliminada
                header('Location: index.php?action=mostrar&mensaje=cuenta_eliminada');
                exit;
            } else {
                // Si se solicitó debug, devolver detalle del error
                $debug = (isset($_REQUEST['debug']) && $_REQUEST['debug'] == '1');
                if ($debug) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode([
                        'success' => false,
                        'error' => $this->modelo->getLastError(),
                    ]);
                    exit;
                }

                // Error al eliminar la cuenta (sin debug)
                header('Location: index.php?action=app&page=perfil&mensaje=error_eliminar');
                exit;
            }
        } catch (Exception $e) {
            header('Location: index.php?action=app&page=perfil&mensaje=error_db');
            exit;
        }
    }
        

}
?>