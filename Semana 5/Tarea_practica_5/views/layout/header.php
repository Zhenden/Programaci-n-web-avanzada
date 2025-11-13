
<!-- views/layout/header.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca Online</title>
    <link rel="stylesheet" href="/Tarea_practica_5/assets/css/style.css">
</head>
<body>
<header>
    <nav>
        <a href="index.php">Inicio</a>
        <a href="index.php?action=catalogo">Catálogo</a>
        <?php if(isset($_SESSION['usuario_id'])): ?>
            <a href="index.php?action=prestamos">Mis Préstamos</a>
            <?php if($_SESSION['rol_id'] == 1): ?>
                <a href="index.php?action=admin_usuarios">Usuarios</a>
            <?php endif; ?>
            <a href="index.php?action=logout">Salir (<?= htmlspecialchars($_SESSION['nombre']) ?>)</a>
        <?php else: ?>
            <a href="index.php?action=login">Iniciar Sesión</a>
        <?php endif; ?>
    </nav>
</header>
</body>
