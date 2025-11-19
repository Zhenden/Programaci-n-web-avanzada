<?php
// acciones/solicitar.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php?action=login');
    exit;
}

$rol = $_SESSION['rol'] ?? 'lector';
$nombre = $_SESSION['nombre'] ?? 'Usuario';
$action = $_GET['action'] ?? 'inicio';

require_once __DIR__ . '/../controllers/PrestamoController.php';
require_once __DIR__ . '/../controllers/LibroController.php';

// Validar sesión
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php?action=login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['libro_id'])) {
    $libro_id = intval($_POST['libro_id']);
    $usuario_id = $_SESSION['usuario_id'];

    $prestamoCtrl = new PrestamoController();
    $libroCtrl = new LibroController();

    // Obtener libro y verificar disponibilidad
    $libro = $libroCtrl->ver($libro_id);
    if (!$libro) {
        $_SESSION['error'] = 'Libro no encontrado.';
        header('Location: ../views/dashboard.php?action=catalogo');
        exit;
    }

    // Verificar disponibilidad con mayor seguridad
    if ($libro['disponible'] <= 0) {
        $_SESSION['error'] = 'Este libro no está disponible actualmente.';
        header('Location: ../views/dashboard.php?action=catalogo');
        exit;
    }

    // Verificar si el usuario ya tiene este libro prestado
    $prestamosUsuario = $prestamoCtrl->listarPorUsuario($usuario_id, 'lector');
    $libroPrestado = false;
    foreach ($prestamosUsuario as $prestamo) {
        if ($prestamo['libro_id'] == $libro_id && $prestamo['estado'] === 'prestado') {
            $libroPrestado = true;
            break;
        }
    }

    if ($libroPrestado) {
        $_SESSION['error'] = 'Ya tienes este libro prestado.';
        header('Location: ../views/dashboard.php?action=catalogo');
        exit;
    }

    // Registrar préstamo
    if ($prestamoCtrl->registrarPrestamo($libro_id, $usuario_id)) {
        // Reducir disponibilidad del libro con validación
        $nuevoDisponible = $libro['disponible'] - 1;
        
        // Validar que no sea negativo
        if ($nuevoDisponible < 0) {
            $nuevoDisponible = 0;
        }

        $libroCtrl->actualizar(
            $libro_id,
            $libro['titulo'],
            $libro['autor'],
            $libro['total_copias'],
            $nuevoDisponible
        );

        $_SESSION['success'] = 'Préstamo registrado correctamente.';
        header('Location: ../index.php?action=catalogo'); // <-- así
        exit;

    } else {
        $_SESSION['error'] = 'Error al registrar el préstamo.';
        header('Location: ../index.php?action=catalogo');
        exit;
    }
} else {
    header('Location: ../index.php?action=catalogo');
    exit;
}
?>