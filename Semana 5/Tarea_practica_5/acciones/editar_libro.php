<?php
// acciones/editar_libro.php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php?action=login");
    exit;
}

// Solo admin o bibliotecario
if ($_SESSION['rol_id'] !== 1 && $_SESSION['rol_id'] !== 2) {
    $_SESSION['error'] = "No tienes permisos para editar libros.";
    header("Location: ../index.php?action=catalogo");
    exit;
}

require_once __DIR__ . '/../controllers/LibroController.php';

$libroCtrl = new LibroController();

// Validar datos
$id = $_POST['id'] ?? null;
$titulo = trim($_POST['titulo'] ?? '');
$autor = trim($_POST['autor'] ?? '');
$disponible = $_POST['disponible'] ?? null;

if (!$id || $titulo === '' || $autor === '' || $disponible === null) {
    $_SESSION['error'] = "Todos los campos son obligatorios.";
    header("Location: ../index.php?action=catalogo");
    exit;
}

// Llamar al controlador
$resultado = $libroCtrl->editar($id, $titulo, $autor, $disponible);

if ($resultado) {
    $_SESSION['success'] = "Libro actualizado correctamente.";
} else {
    $_SESSION['error'] = "Error al actualizar el libro.";
}

header("Location: ../index.php?action=catalogo");
exit;
