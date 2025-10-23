<?php 

require_once 'controlador/ControladorUsuario.php'; 
// Crear una instancia del controlador 

    $controlador = new ControladorEstudiante();
// Verificar si se ha especificado una acción en la URL 
if (isset($_GET['action'])) { 

    switch ($_GET['action']) { 

        case 'iniciarSesion': 
            // Llamar al método que maneja el inicio de sesión 
            $controlador->iniciarSesion($_POST['correo'], $_POST['contrasena']);
            break; 
        case 'registrar': 
            // Llamar al método que maneja el registro de un nuevo estudiante 
            $controlador->registrar($_POST['nombre'], $_POST['apellido'], $_POST['correo'], $_POST['contrasena'], $_POST['universidad']); 
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
        case 'cerrarSesion':
            // Llamar al método que maneja el cierre de sesión 
            $controlador->cerrarSesion(); 
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