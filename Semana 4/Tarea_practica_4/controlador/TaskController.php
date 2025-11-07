<?php
// controlador/TaskController.php - VERSIÓN FINAL CORREGIDA

class TaskController {
    private $task;
    private $user;
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->task = new Task($this->db);
        $this->user = new Usuario($this->db);
    }

    /**
     * INDEX - Carga la vista principal de gestión de tareas
     * URL: index.php?action=task_index
     */
    public function index() {
        // Verificar autenticación
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $user_role = $_SESSION['user_role'];

        // Construir filtros desde GET
        $filters = [];
        
        if (isset($_GET['estado']) && !empty($_GET['estado'])) {
            $filters['estado'] = $_GET['estado'];
        }
        
        if (isset($_GET['prioridad']) && !empty($_GET['prioridad'])) {
            $filters['prioridad'] = $_GET['prioridad'];
        }
        
        if (isset($_GET['fecha_vencimiento']) && !empty($_GET['fecha_vencimiento'])) {
            $filters['fecha_vencimiento'] = $_GET['fecha_vencimiento'];
        }

        // Filtro especial: tareas vencidas
        if (isset($_GET['filtro']) && $_GET['filtro'] === 'vencidas') {
            $filters['fecha_vencimiento'] = date('Y-m-d');
            $filters['estado'] = 'pendiente';
        }

        // Obtener tareas según rol
        if ($user_role == 'admin') {
            $tasks = $this->task->getAll($filters);
        } else {
            $filters['usuario_id'] = $user_id;
            $tasks = $this->task->getAll($filters);
        }

        // Obtener usuarios para admin
        $users = ($user_role == 'admin') ? $this->user->getActiveUsuarios() : [];

        // Mensajes flash
        $message = $_SESSION['flash_message'] ?? '';
        $error = $_SESSION['flash_error'] ?? '';
        unset($_SESSION['flash_message'], $_SESSION['flash_error']);

        // Verificar que $tasks sea siempre un array
        if (!is_array($tasks)) {
            $tasks = [];
        }

        // Cargar vista
        include BASE_PATH . '/vista/tasks.php';
    }

    /**
     * CREATE - Crea una tarea nueva (AJAX)
     * URL: index.php?action=task_create (POST)
     */
    public function create() {
    try {
    // Verificar sesión
    if (!isset($_SESSION['user_id'])) {
        $this->jsonResponse(['success' => false, 'message' => 'No autorizado']);
        return;
    }

    // Solo POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->jsonResponse(['success' => false, 'message' => 'Método no permitido']);
        return;
    }

    // Leer datos
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $usuario_id = $_SESSION['user_id'];
    $user_role = $_SESSION['user_role'];
    $usuario_asignado = intval($_POST['usuario_id'] ?? $usuario_id);
    $prioridad = $_POST['prioridad'] ?? 'media';
    $fecha_vencimiento = $_POST['fecha_vencimiento'] ?? null;

    // Validaciones
    if (empty($titulo)) {
        $this->jsonResponse(['success' => false, 'message' => 'El título es obligatorio']);
        return;
    }

    if ($user_role !== 'admin' && $usuario_asignado != $usuario_id) {
        $this->jsonResponse(['success' => false, 'message' => 'No tienes permisos para asignar tareas a otros usuarios']);
        return;
    }

    // Validar que el usuario asignado exista (solo admin)
    if ($user_role == 'admin' && $usuario_asignado != $usuario_id) {
        $assigned_user = $this->user->getById($usuario_asignado);
        if (!$assigned_user) {
            $this->jsonResponse(['success' => false, 'message' => 'El usuario asignado no existe']);
            return;
        }
    }

    // Crear tarea
    $data = [
        'titulo' => $titulo,
        'descripcion' => $descripcion,
        'usuario_id' => $usuario_asignado,
        'prioridad' => $prioridad,
        'fecha_vencimiento' => $fecha_vencimiento,
        'estado' => 'pendiente'
    ];

    $result = $this->task->create($data);
    
    // Mensaje flash
    if ($result['success']) {
        $_SESSION['flash_message'] = '✅ Tarea creada exitosamente';
    }

    $this->jsonResponse($result);
    } catch (Throwable $e) {
        // ⚠️ Cualquier error PHP lo capturamos y devolvemos JSON
        error_log("Error en create(): " . $e->getMessage());
        $this->jsonResponse([
            'success' => false,
            'message' => 'Error interno: ' . $e->getMessage()
        ]);
    }
}

    /**
     * UPDATE - Actualiza una tarea existente (AJAX)
     * URL: index.php?action=task_update (POST)
     */
    public function update() {
        // Verificar sesión
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'message' => 'No autorizado']);
            return;
        }

        // Solo POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        $task_id = intval($_POST['id'] ?? 0);
        $user_role = $_SESSION['user_role'];

        if ($task_id === 0) {
            $this->jsonResponse(['success' => false, 'message' => 'ID de tarea inválido']);
            return;
        }

        // Verificar permisos
        if (!$this->canModifyTask($task_id)) {
            $this->jsonResponse(['success' => false, 'message' => 'No tienes permisos para modificar esta tarea']);
            return;
        }

        // Preparar datos
        $data = [];
        if (isset($_POST['titulo'])) $data['titulo'] = trim($_POST['titulo']);
        if (isset($_POST['descripcion'])) $data['descripcion'] = trim($_POST['descripcion']);
        if (isset($_POST['prioridad'])) $data['prioridad'] = $_POST['prioridad'];
        if (isset($_POST['fecha_vencimiento'])) $data['fecha_vencimiento'] = $_POST['fecha_vencimiento'];

        // Solo admin puede cambiar estado y usuario asignado
        if ($user_role == 'admin') {
            if (isset($_POST['estado'])) $data['estado'] = $_POST['estado'];
            if (isset($_POST['usuario_id'])) $data['usuario_id'] = intval($_POST['usuario_id']);
        }

        if (empty($data)) {
            $this->jsonResponse(['success' => false, 'message' => 'No se proporcionaron datos para actualizar']);
            return;
        }

        // Actualizar
        $result = $this->task->update($task_id, $data);
        
        if ($result['success']) {
            $_SESSION['flash_message'] = '✅ Tarea actualizada exitosamente';
        }

        $this->jsonResponse($result);
    }

    /**
     * DELETE - Elimina una tarea (AJAX)
     * URL: index.php?action=task_delete (POST)
     */
    public function delete() {
        // Verificar sesión
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'message' => 'No autorizado']);
            return;
        }

        // Solo POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        $task_id = intval($_POST['id'] ?? 0);

        if ($task_id === 0) {
            $this->jsonResponse(['success' => false, 'message' => 'ID de tarea inválido']);
            return;
        }

        // Verificar permisos
        if (!$this->canModifyTask($task_id)) {
            $this->jsonResponse(['success' => false, 'message' => 'No tienes permisos para eliminar esta tarea']);
            return;
        }

        // Eliminar
        $result = $this->task->delete($task_id);
        
        if ($result['success']) {
            $_SESSION['flash_message'] = '🗑️ Tarea eliminada exitosamente';
        }

        $this->jsonResponse($result);
    }

    /**
     * COMPLETE - Marca tarea como completada (AJAX)
     * URL: index.php?action=task_complete (POST)
     */
    public function complete() {
        // Verificar sesión
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'message' => 'No autorizado']);
            return;
        }

        // Solo POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        $task_id = intval($_POST['id'] ?? 0);

        if ($task_id === 0) {
            $this->jsonResponse(['success' => false, 'message' => 'ID de tarea inválido']);
            return;
        }

        // Verificar permisos
        if (!$this->canModifyTask($task_id)) {
            $this->jsonResponse(['success' => false, 'message' => 'No tienes permisos para modificar esta tarea']);
            return;
        }

        // Completar
        $result = $this->task->complete($task_id);
        
        if ($result['success']) {
            $_SESSION['flash_message'] = '✅ Tarea completada';
        }

        $this->jsonResponse($result);
    }

    /**
     * PENDING - Marca tarea como pendiente (AJAX)
     * URL: index.php?action=task_pending (POST)
     */
    public function pending() {
        // Verificar sesión
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'message' => 'No autorizado']);
            return;
        }

        // Solo POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        $task_id = intval($_POST['id'] ?? 0);

        if ($task_id === 0) {
            $this->jsonResponse(['success' => false, 'message' => 'ID de tarea inválido']);
            return;
        }

        // Verificar permisos
        if (!$this->canModifyTask($task_id)) {
            $this->jsonResponse(['success' => false, 'message' => 'No tienes permisos para modificar esta tarea']);
            return;
        }

        // Marcar como pendiente
        $result = $this->task->pending($task_id);
        
        if ($result['success']) {
            $_SESSION['flash_message'] = '⏳ Tarea marcada como pendiente';
        }

        $this->jsonResponse($result);
    }

    /**
     * SEARCH - Busca tareas por texto (AJAX)
     * URL: index.php?action=task_search&q=termino
     */
    public function search() {
        // Verificar sesión
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'message' => 'No autorizado']);
            return;
        }

        $search_term = trim($_GET['q'] ?? '');
        $user_role = $_SESSION['user_role'];
        $user_id = $_SESSION['user_id'];

        if (empty($search_term)) {
            $this->jsonResponse(['success' => false, 'message' => 'Término de búsqueda vacío']);
            return;
        }

        // Buscar
        $tasks = $this->task->search($search_term, $user_role == 'admin' ? null : $user_id);
        
        $this->jsonResponse([
            'success' => true,
            'tasks' => $tasks,
            'count' => count($tasks)
        ]);
    }

    /**
     * HELPER: Verifica si el usuario puede modificar una tarea
     * @param int $task_id ID de la tarea
     * @return bool
     */
    private function canModifyTask($task_id) {
        // Admin puede todo
        if ($_SESSION['user_role'] == 'admin') {
            return true;
        }

        // Usuario normal solo puede modificar sus propias tareas
        $task = $this->task->getById($task_id);
        return $task && isset($task['usuario_id']) && $task['usuario_id'] == $_SESSION['user_id'];
    }

    /**
     * HELPER: Envía respuesta JSON y termina ejecución
     * @param array $data Datos a enviar como JSON
     */
    private function jsonResponse($data) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }
}