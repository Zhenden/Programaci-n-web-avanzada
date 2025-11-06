<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Activar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../controlador/crud_usuarios.php';
require_once '../controlador/crud_notas.php';
require_once '../controlador/funciones.php';
require_once '../controlador/Auth.php';

// Configurar headers para JSON
header('Content-Type: application/json');

try {
    $auth = new Auth();
    $auth->requireAuth();
    $usuario_info = $auth->getUserInfo();
    $usuario_actual_id = $usuario_info['id'];

    // Verificar que sea una petición POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido. Solo se aceptan peticiones POST.');
    }

    $crudUsuarios = new CRUDUsuarios();
    $crudNotas = new CRUDNotas();

    $accion = $_POST['accion'] ?? null;

    if (empty($accion)) {
        throw new Exception('No se especificó ninguna acción');
    }

    switch ($accion) {
        case 'crear_usuario':
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $contrasena = trim($_POST['contrasena'] ?? '');
            $perfil_id = intval($_POST['perfil_id'] ?? 0);
            $rol = intval($_POST['rol'] ?? 3); // Rol por defecto: 3
            $obs = trim($_POST['obs'] ?? '');

            // Validaciones
            if (empty($nombre) || empty($email) || empty($contrasena) || $perfil_id === 0) {
                throw new Exception('Todos los campos obligatorios deben ser completados: nombre, email, contraseña y perfil');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('El formato del email no es válido');
            }

            if (strlen($contrasena) < 6) {
                throw new Exception('La contraseña debe tener al menos 6 caracteres');
            }

            $resultado = $crudUsuarios->crearUsuario($usuario_actual_id, [
                'nombre' => $nombre,
                'email' => $email,
                'contrasena' => $contrasena,
                'perfil_id' => $perfil_id,
                'rol' => $rol,
                'obs' => $obs
            ]);
            echo json_encode($resultado);
            break;

        case 'actualizar_usuario':
            $usuario_id = intval($_POST['id'] ?? 0);
            
            if ($usuario_id === 0) {
                throw new Exception('ID de usuario no válido');
            }

            $datos = [];
            if (isset($_POST['nombre'])) $datos['nombre'] = trim($_POST['nombre']);
            if (isset($_POST['email'])) $datos['email'] = trim($_POST['email']);
            if (isset($_POST['perfil_id'])) $datos['perfil_id'] = intval($_POST['perfil_id']);

            if (empty($datos)) {
                throw new Exception('No se proporcionaron datos para actualizar');
            }

            $resultado = $crudUsuarios->actualizarUsuario($usuario_actual_id, $usuario_id, $datos);
            echo json_encode($resultado);
            break;

        case 'eliminar_usuario':
            $usuario_id = intval($_POST['id'] ?? 0);
            
            if ($usuario_id === 0) {
                throw new Exception('ID de usuario no válido');
            }

            $resultado = $crudUsuarios->eliminarUsuario($usuario_actual_id, $usuario_id);
            echo json_encode($resultado);
            break;

        case 'crear_nota':
            $asignatura_id = intval($_POST['asignatura_id'] ?? 0);
            $usuario_id = intval($_POST['usuario_id'] ?? 0);
            $parcial = intval($_POST['parcial'] ?? 0);
            $teoria = floatval($_POST['teoria'] ?? 0);
            $practica = floatval($_POST['practica'] ?? 0);
            $obs = trim($_POST['obs'] ?? '');

            // Validaciones
            if ($asignatura_id === 0 || $usuario_id === 0 || $parcial === 0) {
                throw new Exception('Los campos asignatura, estudiante y parcial son obligatorios');
            }

            $resultado = $crudNotas->crearNota($usuario_actual_id, [
                'asignatura_id' => $asignatura_id,
                'usuario_id' => $usuario_id,
                'parcial' => $parcial,
                'teoria' => $teoria,
                'practica' => $practica,
                'obs' => $obs
            ]);
            echo json_encode($resultado);
            break;

        case 'actualizar_nota':
            $nota_id = intval($_POST['id'] ?? 0);
            
            if ($nota_id === 0) {
                throw new Exception('ID de nota no válido');
            }

            $datos = [];
            if (isset($_POST['teoria'])) $datos['teoria'] = floatval($_POST['teoria']);
            if (isset($_POST['practica'])) $datos['practica'] = floatval($_POST['practica']);
            if (isset($_POST['obs'])) $datos['obs'] = trim($_POST['obs']);

            if (empty($datos)) {
                throw new Exception('No se proporcionaron datos para actualizar');
            }

            $resultado = $crudNotas->actualizarNota($usuario_actual_id, $nota_id, $datos);
            echo json_encode($resultado);
            break;

        case 'eliminar_nota':
            $nota_id = intval($_POST['id'] ?? 0);
            
            if ($nota_id === 0) {
                throw new Exception('ID de nota no válido');
            }

            $resultado = $crudNotas->eliminarNota($usuario_actual_id, $nota_id);
            echo json_encode($resultado);
            break;

        default:
            throw new Exception('Acción no válida: ' . $accion);
    }

} catch (Exception $e) {
    // Log del error
    error_log("Error en acciones.php: " . $e->getMessage());
    
    // Respuesta de error en formato JSON
    echo json_encode([
        'error' => 'Error: ' . $e->getMessage()
    ]);
}
?>