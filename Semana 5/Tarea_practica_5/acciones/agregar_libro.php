<?php
// acciones/agregar_libro.php

session_start();

// Verificar que el usuario esté logueado y tenga permisos
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['rol_id'], [1,2])) {
    $_SESSION['error'] = "No autorizado para agregar libros.";
    header('Location: ../views/dashboard.php?action=inicio');
    exit;
}

require_once __DIR__ . '/../BD/conexion.php';

// Verificar que el formulario se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $autor = trim($_POST['autor'] ?? '');
    $disponible = intval($_POST['disponible'] ?? 0);

    // Validaciones simples
    if ($titulo === '' || $autor === '' || $disponible < 0) {
        $_SESSION['error'] = "Datos inválidos, revisa el formulario.";
        header('Location: ../index.php?action=agregar_libro');
        exit;
    }

    // Conectar a la base de datos
    $conn = conectar(); // Debe devolver mysqli

    $stmt = $conn->prepare("INSERT INTO libros (titulo, autor, disponible) VALUES (?, ?, ?)");
    if (!$stmt) {
        $_SESSION['error'] = "Error en la preparación de la consulta: " . $conn->error;
        header('Location: ../index.php?action=agregar_libro');
        exit;
    }

    $stmt->bind_param("ssi", $titulo, $autor, $disponible);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Libro agregado correctamente.";
        header('Location: ../index.php?action=catalogo');
    } else {
        $_SESSION['error'] = "Error al agregar el libro: " . $stmt->error;
        header('Location: ../index.php?action=agregar_libro');
    }

    $stmt->close();
    $conn->close();
} else {
    // Si no es POST, redirigir al formulario
    header('Location: ../index.php?action=agregar_libro');
    exit;
}
