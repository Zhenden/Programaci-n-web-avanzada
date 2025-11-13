<?php
// index.php - CONTROLADOR PRINCIPAL

session_start();

// Incluir configuraciones y modelos
require_once 'BD/conexion.php';
require_once 'modelos/Usuario.php';
require_once 'modelos/Tarea.php';

// Inicializar conexión a base de datos
$database = new Database();
$db = $database->getConnection();

// Obtener acción desde URL
$action = $_GET['action'] ?? 'login';

// Procesar la acción solicitada
switch ($action) {
    case 'login':
        handleLogin($db);
        break;
        
    case 'dashboard':
        handleDashboard($db);
        break;
        
    case 'task_index':
        handleTaskIndex($db);
        break;
        
    case 'task_create':
        handleTaskCreate($db);
        break;

    case 'task_form':
        include 'vista/task_form.php';
    break;

        
    case 'task_update':
        handleTaskUpdate($db);
        break;
        
    case 'task_complete':
        handleTaskComplete($db);
        break;
        
    case 'task_pending':
        handleTaskPending($db);
        break;
        
    case 'task_delete':
        handleTaskDelete($db);
        break;
        
    case 'user_create': 
        handleUserCreate($db); 
        break;

    case 'user_delete': 
        handleUserDelete($db); 
        break;

    case 'user_role_update': 
        handleUserRoleUpdate($db);
        break;

        
    case 'logout':
        handleLogout();
        break;
        
    default:
        // Redirigir al login por defecto
        header('Location: index.php?action=login');
        exit;
}

/**
 * Manejar login
 */
function handleLogin($db) {
    // Si ya está logueado, redirigir al dashboard
    if (isset($_SESSION['user_id'])) {
        header('Location: index.php?action=dashboard');
        exit;
    }

    // Procesar formulario de login
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        
        $usuario = new Usuario($db);
        $result = $usuario->login($email, $password);
        
        if ($result['success']) {
            $_SESSION['user_id'] = $result['user']['id'];
            $_SESSION['user_name'] = $result['user']['nombre'];
            $_SESSION['user_email'] = $result['user']['email'];
            $_SESSION['user_role'] = $result['user']['rol_id'];
            
            // Responder con JSON para AJAX
            if (isAjaxRequest()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Login exitoso',
                    'redirect' => 'index.php?action=dashboard'
                ]);
                exit;
            } else {
                header('Location: index.php?action=dashboard');
                exit;
            }
        } else {
            if (isAjaxRequest()) {
                echo json_encode([
                    'success' => false,
                    'message' => $result['message']
                ]);
                exit;
            } else {
                header('Location: index.php?action=login&error=' . urlencode($result['message']));
                exit;
            }
        }
    }
    
    // Mostrar vista de login
    include 'vista/login.php';
}

/**
 * Manejar dashboard
 */
function handleDashboard($db) {
    // Verificar autenticación
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?action=login');
        exit;
    }

    $usuarioModel = new Usuario($db);
    $tareaModel = new Tarea($db);
    
    $currentUser = [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role' => $_SESSION['user_role']
    ];
    
    // Obtener estadísticas según el rol_id
    if ($currentUser['role'] == 1) { // Admin
        $stats = $tareaModel->getStats();
    } else {
        $stats = $tareaModel->getStats($currentUser['id']);
    }
    
    // Obtener tareas recientes
    $filters = [];
    if ($currentUser['role'] != 1) { // No admin
        $filters['usuario_id'] = $currentUser['id'];
    }
    $recent_tasks = $tareaModel->getAll($filters);
    
    // Preparar datos para la vista
    $viewData = [
        'user_id' => $currentUser['id'],
        'user_name' => $currentUser['name'],
        'user_role' => $currentUser['role'] == 1 ? 'admin' : ($currentUser['role'] == 2 ? 'gerente' : 'miembro'),
        'stats' => $stats
    ];
    
    // Incluir vista del dashboard
    include 'vista/dashboard.php';
}

/**
 * Manejar listado de tareas
 */
function handleTaskIndex($db) {
    // Verificar autenticación
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?action=login');
        exit;
    }

    $tareaModel = new Tarea($db);
    $usuarioModel = new Usuario($db);
    
    $currentUser = [
        'id' => $_SESSION['user_id'],
        'role' => $_SESSION['user_role']
    ];
    
    // Aplicar filtros
    $filters = [];
    
    // Si no es admin, solo puede ver sus tareas
    if ($currentUser['role'] != 1) {
        $filters['usuario_id'] = $currentUser['id'];
    }
    
    if (!empty($_GET['estado'])) {
        $filters['estado'] = $_GET['estado'];
    }
    
    // Obtener tareas
    $tasks = $tareaModel->getAll($filters);
    
    // Obtener usuarios (solo para admin)
    $users = [];
    if ($currentUser['role'] == 1) {
        $users = $usuarioModel->getAll();
    }
    
    // Preparar datos para la vista
    $viewData = [
        'user_id' => $currentUser['id'],
        'user_role' => $currentUser['role'] == 1 ? 'admin' : ($currentUser['role'] == 2 ? 'gerente' : 'miembro'),
        'tasks' => $tasks,
        'users' => $users
    ];
    
    // Extraer variables para la vista
    extract($viewData);
    
    // Incluir vista de tareas
    include 'vista/Tarea.php';
}

/**
 * Manejar creación de tareas
 */
function handleTaskCreate($db) {
    // Verificar autenticación y permisos
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'No autenticado']);
        exit;
    }

    $currentUserRole = $_SESSION['user_role'];
    
    // Solo admin y gerentes pueden crear tareas
    if ($currentUserRole != 1 && $currentUserRole != 2) {
        echo json_encode(['success' => false, 'message' => 'No tienes permisos para crear tareas']);
        exit;
    }

    $tareaModel = new Tarea($db);
    
    // Preparar datos
    $data = [
        'titulo' => trim($_POST['titulo'] ?? ''),
        'descripcion' => trim($_POST['descripcion'] ?? ''),
        'estado' => trim($_POST['estado'] ?? '')
    ];
    
    // Asignar usuario
    if ($currentUserRole == 1) { // Admin puede asignar a cualquier usuario
        $data['usuario_id'] = $_POST['usuario_id'] ?? $_SESSION['user_id'];
    } else { // Gerente solo puede asignar a miembros del equipo o a sí mismo
        $assignedUserId = $_POST['usuario_id'] ?? $_SESSION['user_id'];
        // Aquí deberías validar que el usuario asignado sea miembro del equipo
        $data['usuario_id'] = $assignedUserId;
    }
    
    $result = $tareaModel->create($data);
    
    // Responder con JSON
    echo json_encode($result);
    exit;
}

/**
 * Manejar actualización de tareas
 */
function handleTaskUpdate($db) {
    // Verificar autenticación
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'No autenticado']);
        exit;
    }

    $tareaModel = new Tarea($db);
    $currentUserRole = $_SESSION['user_role'];
    
    // Verificar que la tarea existe y tiene permisos
    $taskId = $_POST['id'] ?? '';
    $existingTask = $tareaModel->getById($taskId);
    
    if (!$existingTask) {
        echo json_encode(['success' => false, 'message' => 'Tarea no encontrada']);
        exit;
    }
    
    // Verificar permisos (solo admin, gerente o el usuario asignado)
    $canEdit = $currentUserRole == 1 || // Admin
               $currentUserRole == 2 || // Gerente
               $existingTask['usuario_id'] == $_SESSION['user_id']; // Usuario asignado
    
    if (!$canEdit) {
        echo json_encode(['success' => false, 'message' => 'No tienes permisos para editar esta tarea']);
        exit;
    }
    
    // Preparar datos para actualizar
    $data = [];
    if (isset($_POST['titulo'])) $data['titulo'] = trim($_POST['titulo']);
    if (isset($_POST['descripcion'])) $data['descripcion'] = trim($_POST['descripcion']);
    if (isset($_POST['prioridad'])) $data['prioridad'] = $_POST['prioridad'];
    
    // Solo admin puede cambiar el usuario asignado
    if ($currentUserRole == 1 && isset($_POST['usuario_id'])) {
        $data['usuario_id'] = $_POST['usuario_id'];
    }
    
    $result = $tareaModel->update($taskId, $data);
    echo json_encode($result);
    exit;
}

/**
 * Manejar completar tarea
 */
function handleTaskComplete($db) {
    // Verificar autenticación
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'No autenticado']);
        exit;
    }

    $tareaModel = new Tarea($db);
    $taskId = $_POST['id'] ?? '';
    
    // Verificar que la tarea existe y tiene permisos
    $existingTask = $tareaModel->getById($taskId);
    
    if (!$existingTask) {
        echo json_encode(['success' => false, 'message' => 'Tarea no encontrada']);
        exit;
    }
    
    // Verificar permisos (solo admin, gerente o el usuario asignado)
    $canComplete = $_SESSION['user_role'] == 1 || // Admin
                   $_SESSION['user_role'] == 2 || // Gerente
                   $existingTask['usuario_id'] == $_SESSION['user_id']; // Usuario asignado
    
    if (!$canComplete) {
        echo json_encode(['success' => false, 'message' => 'No tienes permisos para completar esta tarea']);
        exit;
    }
    
    $result = $tareaModel->complete($taskId);
    echo json_encode($result);
    exit;
}

/**
 * Manejar marcar tarea como pendiente
 */
function handleTaskPending($db) {
    // Verificar autenticación
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'No autenticado']);
        exit;
    }

    $tareaModel = new Tarea($db);
    $taskId = $_POST['id'] ?? '';
    
    // Verificar permisos (similar a completar tarea)
    $existingTask = $tareaModel->getById($taskId);
    
    if (!$existingTask) {
        echo json_encode(['success' => false, 'message' => 'Tarea no encontrada']);
        exit;
    }
    
    $canChange = $_SESSION['user_role'] == 1 || // Admin
                 $_SESSION['user_role'] == 2 || // Gerente
                 $existingTask['usuario_id'] == $_SESSION['user_id']; // Usuario asignado
    
    if (!$canChange) {
        echo json_encode(['success' => false, 'message' => 'No tienes permisos para cambiar esta tarea']);
        exit;
    }
    
    $result = $tareaModel->pending($taskId);
    echo json_encode($result);
    exit;
}

/**
 * Manejar eliminación de tareas
 */
function handleTaskDelete($db) {
    // Verificar autenticación
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'No autenticado']);
        exit;
    }

    $tareaModel = new Tarea($db);
    $taskId = $_POST['id'] ?? '';
    
    // Verificar permisos (solo admin y gerentes pueden eliminar)
    if ($_SESSION['user_role'] != 1 && $_SESSION['user_role'] != 2) {
        echo json_encode(['success' => false, 'message' => 'No tienes permisos para eliminar tareas']);
        exit;
    }
    
    // Verificar que la tarea existe
    $existingTask = $tareaModel->getById($taskId);
    if (!$existingTask) {
        echo json_encode(['success' => false, 'message' => 'Tarea no encontrada']);
        exit;
    }
    
    $result = $tareaModel->delete($taskId);
    echo json_encode($result);
    exit;
}

function handleUserCreate($db) {
    header('Content-Type: application/json');
    $usuario = new Usuario($db);

    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contraseña = $_POST['contraseña'] ?? '';
    $rol_id = (int)($_POST['rol_id'] ?? 3);

    if (!$nombre || !$email || !$contraseña) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
        return;
    }

    $hash = password_hash($contraseña, PASSWORD_BCRYPT);
    $query = "INSERT INTO usuarios (nombre, email, contraseña, rol_id) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($query);

    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Error preparando la consulta: ' . $db->error]);
        return;
    }

    $stmt->bind_param("sssi", $nombre, $email, $hash, $rol_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Usuario creado correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al crear usuario: ' . $stmt->error]);
    }
}

function handleUserDelete($db) {
    header('Content-Type: application/json');
    $id = $_POST['id'] ?? null;
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID inválido']);
        return;
    }

    // 🔒 Prevención de borrado de sí mismo
    session_start();
    if ($_SESSION['user_id'] == $id) {
        echo json_encode(['success' => false, 'message' => 'No puedes eliminar tu propio usuario']);
        return;
    }

    $query = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Usuario eliminado correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar usuario']);
    }
}

function handleUserRoleUpdate($db) {
    header('Content-Type: application/json');
    $id = $_POST['id'] ?? null;
    $rol_id = $_POST['rol_id'] ?? null;

    if (!$id || !$rol_id) {
        echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
        return;
    }

    // 🔒 Prevención de auto degradarse (Admin no puede quitarse su rol_id)
    session_start();
    if ($_SESSION['user_id'] == $id && $rol_id != 1) {
        echo json_encode(['success' => false, 'message' => 'No puedes cambiar tu propio rol_id']);
        return;
    }

    $query = "UPDATE usuarios SET rol_id = ? WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("ii", $rol_id, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Rol actualizado correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar rol_id']);
    }
}

/**
 * Manejar logout
 */
function handleLogout() {
    // Destruir sesión
    session_destroy();
    
    // Redirigir al login
    header('Location: index.php?action=login');
    exit;
}

/**
 * Verificar si es una petición AJAX
 */
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

// Cerrar conexión a la base de datos
$db->close();
?>