<?php
// index.php - Front Controller

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


require_once __DIR__ . '/BD/conexion.php';
require_once __DIR__ . '/controllers/UsuarioController.php';

$action = $_GET['action'] ?? 'login';
$usuarioController = new UsuarioController();


if ($action !== 'login' && !isset($_SESSION['usuario_id'])) {
    header('Location: index.php?action=login');
    exit;
}   

switch ($action) {
    case 'login':
        // Mostrar formulario de login
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Correo no válido.";
            } elseif (strlen($password) < 4) {
                $error = "Contraseña muy corta.";
            } elseif ($usuarioController->autenticar($email, $password)) {

                // Redirigir a dashboard a través del front controller
                header('Location: index.php?action=inicio');
                exit;
            } else {
                $error = "Correo o contraseña incorrectos.";
            }
        }
        include 'views/login_form.php'; // ver más abajo
        break;

    case 'prestamos':
        include 'views/dashboard.php'; // ver más abajo
        break;

    case 'catalogo':
        include 'views/dashboard.php'; // ver más abajo
        break;

    case 'agregar_libro':
        include 'views/dashboard.php'; // ver más abajo
        break;
    
    case 'usuarios':
        include 'views/dashboard.php'; // ver más abajo
        break;
    
    case 'inicio':
        include 'views/dashboard.php'; // ver más abajo
        break;

    case 'logout':
        $usuarioController->logout();
        break;

    default:
        header('Location: index.php?action=login');
        exit;
}