<?php

require_once 'controlador/ControladorUsuario.php';
require_once 'controlador/ControladorTransaccion.php';
require_once 'controlador/ControladorMetas.php';
require_once 'controlador/ControladorPerfil.php';
require_once 'controlador/ControladorRecordatorios.php';
require_once 'controlador/ControladorAdministrador.php';
// Crear una instancia del controlador 

$controlador = new ControladorEstudiante();
$controladorTransaccion = new ControladorTransaccion();
$controladorMetas = new ControladorMeta();
$controladorPerfil = new ControladorPerfil();
$controladorAdmin = new ControladorAdministrador();
$ctrl = new ControladorRecordatorios();


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
        case 'eliminarTransaccion':
            $controladorTransaccion->eliminar();
            break;
        case 'getTransacciones':
            $controladorTransaccion->listarJson();
            break;
        case 'actualizarTransaccion':
            $controladorTransaccion->actualizar();
            break; 
        case 'cerrarSesion':
            // Llamar al método que maneja el cierre de sesión 
            $controlador->cerrarSesion();
            break;
        case 'mostrarPerfil':
                // Mostrar la vista de perfil
                $controladorPerfil->mostrarPerfil();
                break;
        case 'crearMeta':
            $controladorMetas->crear();
            break;
        case 'actualizarMeta':
            $controladorMetas->actualizar();
            break;
        case 'registrarProgreso':
            $controladorMetas->registrarProgreso();
            break;
        case 'eliminarMeta':
            $controladorMetas->eliminar();
            break;
        case 'actualizarPerfil':
            $controladorPerfil->actualizarPerfil();
            break;
        case 'eliminarCuenta':
            $controladorPerfil->eliminarCuenta();
            break;
        case 'cambiarContrasena':
            $controlador->cambiarContrasena($_SESSION['id_usuario'], $_POST['nuevaContrasena']);
            break;
        case 'crear':
            $ctrl->crear();
            break;
        case 'actualizar':
            $ctrl->actualizar();
            break;
        case 'eliminar':
            $ctrl->eliminar();
            break;
        case 'getProximos':
            $ctrl->obtenerProximos();
            break;
        case 'administrador':
            $controladorAdmin->mostrarDashboard();
            break;
        case 'getUsuarios':
            // Devuelve la lista de usuarios en JSON (usado por la vista administrador)
            $controladorAdmin->getUsuarios();
            break;
        case 'eliminarUsuarioAdmin':
            // Endpoint para eliminar usuarios (POST)
            $controladorAdmin->eliminarUsuarioAdmin();
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



