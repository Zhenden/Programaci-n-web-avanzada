<?php
// acciones/agregar_usuario.php

session_start();

// Solo admin puede agregar usuarios
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1) {
    $_SESSION['error'] = "No autorizado para crear usuarios.";
    header('Location: ../index.php?action=usuarios');
    exit;
}

require_once __DIR__ . '/../BD/conexion.php';

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol_id = intval($_POST['rol_id'] ?? 3);

    // Validaciones
    if ($nombre === '' || $email === '' || $password === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Datos inválidos. Revisa el formulario.";
        header('Location: ../index.php?action=usuarios');
        exit;
    }

    if (!in_array($rol_id, [1,2,3])) {
        $_SESSION['error'] = "Rol inválido.";
        header('Location: ../index.php?action=usuarios');
        exit;
    }

    // Hashear contraseña
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Conectar a DB
    $conn = conectar(); // Debe devolver mysqli

    // Verificar si el email ya existe
    $stmtCheck = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmtCheck->bind_param("s", $email);
    $stmtCheck->execute();
    $stmtCheck->store_result();

    if ($stmtCheck->num_rows > 0) {
        $_SESSION['error'] = "El correo ya está registrado.";
        $stmtCheck->close();
        $conn->close();
        header('Location: ../index.php?action=usuarios');
        exit;
    }
    $stmtCheck->close();

    // Insertar nuevo usuario
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol_id) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        $_SESSION['error'] = "Error en la consulta: " . $conn->error;
        $conn->close();
        header('Location: ../index.php?action=usuarios');
        exit;
    }

    $stmt->bind_param("sssi", $nombre, $email, $hash, $rol_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Usuario creado correctamente.";
    } else {
        $_SESSION['error'] = "Error al crear usuario: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    // Redirigir al dashboard
    header('Location: ../index.php?action=usuarios');
    exit;

} else {
    // Si no es POST, redirigir
    header('Location: ../index.php?action=usuarios');
    exit;
}
