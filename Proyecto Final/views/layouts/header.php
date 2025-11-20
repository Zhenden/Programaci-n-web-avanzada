<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$user = $_SESSION['user'] ?? null;
?><!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Gestión - Gimnasio</title>
    <link rel="stylesheet" href="/Proyecto Final/assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container">
        <h1 class="brand">Gimnasio</h1>
        <nav>
            <a href="?route=home">Inicio</a>
            <?php if (!$user): ?>
                <a href="?route=auth/loginForm">Login</a>
            <?php else: ?>
                <?php if ($user['role'] === 'admin'): ?>
                    <a href="?route=admin/dashboard">Admin</a>
                    <a href="?route=admin/members">Miembros</a>
                    <a href="?route=admin/classes">Clases</a>
                    <a href="?route=admin/instructors">Instructores</a>
                    <a href="?route=admin/facilities">Instalaciones</a>
                <?php elseif ($user['role'] === 'instructor'): ?>
                    <a href="?route=instructor/dashboard">Dashboard</a>
                    <a href="?route=instructor/classes">Mis Clases</a>
                <?php else: ?>
                    <a href="?route=member/dashboard">Mi Perfil</a>
                    <a href="?route=member/classes">Clases</a>
                    <a href="?route=member/history">Historial</a>
                <?php endif; ?>
                <a href="?route=auth/logout">Cerrar sesión (<?php echo htmlspecialchars($user['username']); ?>)</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container">
