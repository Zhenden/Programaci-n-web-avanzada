<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Reservation System</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <a href="index.php" class="logo">🏨 Hotel Luxury</a>
            <ul class="nav-menu">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="index.php?action=dashboard" class="nav-link">Dashboard</a></li>
                    
                    <?php if (in_array($_SESSION['user_role'], ['Administrator', 'Hotel Manager', 'Receptionist'])): ?>
                        <li><a href="index.php?action=rooms" class="nav-link">Habitaciones</a></li>
                        <li><a href="index.php?action=bookings" class="nav-link">Reservas</a></li>
                    <?php endif; ?>
                    
                    <?php if (in_array($_SESSION['user_role'], ['Administrator', 'Hotel Manager'])): ?>
                        <li><a href="index.php?action=supplies" class="nav-link">Suministros</a></li>
                    <?php endif; ?>
                    
                    <?php if ($_SESSION['user_role'] === 'Customer'): ?>
                        <li><a href="index.php?action=booking_create" class="nav-link">Reservar</a></li>
                        <li><a href="index.php?action=my_bookings" class="nav-link">Mis Reservas</a></li>
                    <?php endif; ?>
                    
                    <li><a href="index.php?action=logout" class="nav-link">Cerrar Sesión</a></li>
                <?php else: ?>
                    <li><a href="index.php?action=login" class="nav-link">Iniciar Sesión</a></li>
                    <li><a href="index.php?action=register" class="nav-link">Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    
    <main class="container">