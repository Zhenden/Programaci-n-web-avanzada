<?php
// index.php - VERSIÓN FINAL CORREGIDA Y TESTEADA

// === 1. session_start ÚNICO Y PRIMERO ===
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// === 2. CONFIGURACIÓN BÁSICA ===
date_default_timezone_set('America/Mexico_City');
define('BASE_PATH', __DIR__);

// === 3. AUTOLOAD ANTES DE CUALQUIER CÓDIGO ===
function autoload($class) {
    ob_start();
    // Limpiar namespace si existe
    $class = basename(str_replace('\\', '/', $class));
    
    // Rutas posibles
    $paths = [
        BASE_PATH . '/modelo/' . $class . '.php',
        BASE_PATH . '/controlador/' . $class . '.php',
    ];
    
    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
}

spl_autoload_register('autoload');

// === 4. CONEXIÓN A BD ===
require_once BASE_PATH . '/base_de_datos/conexion.php';

// === 5. HELPERS ===
function requireAuth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?action=login');
        exit;
    }
}

function requireAdmin() {
    requireAuth();
    if ($_SESSION['user_role'] !== 'admin') {
        header('Location: index.php?action=dashboard&error=' . urlencode('No tienes permisos de administrador'));
        exit;
    }
}

// === 6. DETECTAR AJAX Y LIMPIAR BÚFERES ===
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

if ($isAjax) {
    header('Content-Type: application/json; charset=utf-8');
    // Limpiar cualquier output buffer existente
    while (ob_get_level()) {
        ob_end_clean();
    }
}

// === 7. ROUTER PRINCIPAL ===
$action = $_GET['action'] ?? 'dashboard';

try {
    switch ($action) {
        // --- AUTHCONTROLLER ---
        case 'login':
            $controller = new AuthController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->login();
            } else {
                $controller->showLogin();
            }
            break;

        case 'logout':
            $controller = new AuthController();
            $controller->logout();
            break;

        // --- TASKCONTROLLER ---
        case 'task_index':
            requireAuth();
            $controller = new TaskController();
            $controller->index();
            break;

        case 'task_create':
            requireAuth();
            $controller = new TaskController();
            $controller->create();
            break;

        case 'task_update':
            requireAuth();
            $controller = new TaskController();
            $controller->update();
            break;

        case 'task_delete':
            requireAuth();
            $controller = new TaskController();
            $controller->delete();
            break;

        case 'task_complete':
            requireAuth();
            $controller = new TaskController();
            $controller->complete();
            break;

        case 'task_pending':
            requireAuth();
            $controller = new TaskController();
            $controller->pending();
            break;

        case 'task_search':
            requireAuth();
            $controller = new TaskController();
            $controller->search();
            break;

        // --- DASHBOARD (VISTA DIRECTA) ---
        case 'dashboard':
        default:
            requireAuth();
            
            // PROTEGER CONTRA ACCESO AJAX
            if ($isAjax) {
                echo json_encode(['success' => false, 'message' => 'Ruta no válida para AJAX']);
                exit;
            }
            
            // Cargar datos para el dashboard
            $db = (new Database())->getConnection();
            $task = new Task($db);
            $user = new Usuario($db);

            $user_id = $_SESSION['user_id'];
            $user_role = $_SESSION['user_role'];
            $user_name = $_SESSION['user_name'];

            $stats = $task->getStats($user_role == 'admin' ? null : $user_id);
            $upcoming_tasks = $task->getUpcomingTasks($user_role == 'admin' ? null : $user_id);
            
            $recent_tasks = $task->getAll([
                'usuario_id' => $user_role == 'admin' ? null : $user_id
            ]);
            $recent_tasks = array_slice($recent_tasks, 0, 5);

            // Mensajes flash
            $message = $_SESSION['flash_message'] ?? '';
            $error = $_SESSION['flash_error'] ?? '';
            unset($_SESSION['flash_message'], $_SESSION['flash_error']);

            // Cargar vista
            include BASE_PATH . '/vista/dashboard.php';
            break;
            
    }
} catch (Exception $e) {
    ob_end_clean(); // <--- LIMPIA CUALQUIER SALIDA ANTERIOR
    error_log("Error: " . $e->getMessage());
    
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
    } else {
        header('Location: index.php?action=dashboard&error=' . urlencode('Error: ' . $e->getMessage()));
    }
    exit;
}