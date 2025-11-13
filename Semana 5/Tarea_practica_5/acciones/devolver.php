<?php
// acciones/devolver.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../controllers/PrestamoController.php';
require_once __DIR__ . '/../controllers/LibroController.php';

// Validar sesión
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php?action=login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prestamo_id'], $_POST['libro_id'])) {
    $prestamo_id = intval($_POST['prestamo_id']);
    $libro_id = intval($_POST['libro_id']);

    $prestamoCtrl = new PrestamoController();
    $libroCtrl = new LibroController();

    // Verificar si el préstamo existe y no está ya devuelto
    $prestamo = $prestamoCtrl->ver($prestamo_id);
    if (!$prestamo || $prestamo['estado'] === 'devuelto') {
        $_SESSION['error'] = 'Préstamo no válido o ya devuelto.';
        header('Location: ../views/dashboard.php?action=prestamos');
        exit;
    }

    // Marcar préstamo como devuelto
    if ($prestamoCtrl->marcarDevuelto($prestamo_id)) {
        // Aumentar disponibilidad del libro con validación
        $libro = $libroCtrl->ver($libro_id);
        
        // Validar que no exceda el total de copias
        $nuevoDisponible = $libro['disponible'] + 1;
        if ($nuevoDisponible > $libro['total_copias']) {
            $nuevoDisponible = $libro['total_copias']; // No puede exceder el total
        }

        $libroCtrl->actualizar(
            $libro_id,
            $libro['titulo'],
            $libro['autor'],
            $libro['isbn'],
            $libro['descripcion'],
            $libro['total_copias'],
            $nuevoDisponible
        );
        
        $_SESSION['success'] = 'Préstamo registrado correctamente.';
        header('Location: ../index.php?action=prestamos'); // <-- así
        exit;

    } else {
        $_SESSION['error'] = 'Error al registrar la devolución.';
        header('Location: ../views/dashboard.php?action=prestamos');
        exit;
    }
} else {
    header('Location: ../views/dashboard.php?action=prestamos');
    exit;
}
?>