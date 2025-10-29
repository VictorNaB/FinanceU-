<?php

require_once 'controlador/ControladorUsuario.php';
require_once 'controlador/ControladorTransaccion.php';
require_once 'controlador/ControladorMetas.php';
// Crear una instancia del controlador 

$controlador = new ControladorEstudiante();
$controladorTransaccion = new ControladorTransaccion();
$controladorMetas = new ControladorMeta();


// Verificar si se ha especificado una acción en la URL 
if (isset($_GET['action'])) {

    switch ($_GET['action']) {

        case 'iniciarSesion':
            // Llamar al método que maneja el inicio de sesión 
            $controlador->iniciarSesion($_POST['correo'], $_POST['contrasena']);
            break;
        case 'registrar':
            // Llamar al método que maneja el registro de un nuevo estudiante 
            $controlador->registrar($_POST['nombre'], $_POST['apellido'], $_POST['correo'], $_POST['contrasena'], $_POST['universidad'], $_POST['idRol'], $_POST['programa']);
            break;
        case 'mostrarRegistro':
            // Mostrar el formulario de registro 
            $controlador->mostrarRegistro();
            break;
        case 'mostrarLogin':
            // Mostrar el formulario de inicio de sesión
            $controlador->mostrarLogin();
            break;
        case 'mostrar':
            // Mostrar el formulario de inicio de sesión
            require 'vista/index.php';
            break;
        case 'app':
            require 'vista/app.php';
            break;
        case 'crearTransaccion':
            // Llamar al método que maneja la creación de una transacción
            $controladorTransaccion->crear();
            break;
        case 'cerrarSesion':
            // Llamar al método que maneja el cierre de sesión 
            $controlador->cerrarSesion();
            break;
        case 'crearMeta':
            $controladorMetas->crear();
            break;
        case 'registrarProgreso':
            $controladorMetas->registrarProgreso();
            break;
        case 'eliminarMeta':
            $controladorMetas->eliminar();
            break;
        default:
            // Si la acción no es reconocida, redirigir al formulario de inicio de sesión 
            require 'vista/index.php';
            break;
    }
} else {
    // Si no se ha especificado ninguna acción, mostrar el formulario de inicio de sesión por defecto 
    require 'vista/index.php';
}




