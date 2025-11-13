<?php
// controladores/TareaController.php

class TareaController {
    private $tarea;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->tarea = new Tarea($db);
    }

    /**
     * Manejar las solicitudes HTTP
     */
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $parts = explode('/', $path);
        $endpoint = end($parts);

        try {
            switch ($method) {
                case 'GET':
                    $this->handleGet($endpoint);
                    break;
                case 'POST':
                    $this->handlePost($endpoint);
                    break;
                case 'PUT':
                    $this->handlePut($endpoint);
                    break;
                case 'DELETE':
                    $this->handleDelete($endpoint);
                    break;
                default:
                    $this->sendResponse(405, [
                        'success' => false,
                        'message' => 'Método no permitido'
                    ]);
            }
        } catch (Exception $e) {
            $this->sendResponse(500, [
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Manejar solicitudes GET
     */
    private function handleGet($endpoint) {
        switch ($endpoint) {
            case 'tareas':
                $this->getAllTareas();
                break;
            case 'tarea':
                $this->getTareaById();
                break;
            case 'stats':
                $this->getStats();
                break;
            case 'search':
                $this->searchTareas();
                break;
            default:
                $this->sendResponse(404, [
                    'success' => false,
                    'message' => 'Endpoint no encontrado'
                ]);
        }
    }

    /**
     * Manejar solicitudes POST
     */
    private function handlePost($endpoint) {
        $data = $this->getInputData();

        switch ($endpoint) {
            case 'tareas':
                $this->createTarea($data);
                break;
            case 'complete':
                $this->completeTarea($data);
                break;
            case 'pending':
                $this->pendingTarea($data);
                break;
            default:
                $this->sendResponse(404, [
                    'success' => false,
                    'message' => 'Endpoint no encontrado'
                ]);
        }
    }

    /**
     * Manejar solicitudes PUT
     */
    private function handlePut($endpoint) {
        if ($endpoint === 'tareas') {
            $data = $this->getInputData();
            $this->updateTarea($data);
        } else {
            $this->sendResponse(404, [
                'success' => false,
                'message' => 'Endpoint no encontrado'
            ]);
        }
    }

    /**
     * Manejar solicitudes DELETE
     */
    private function handleDelete($endpoint) {
        if ($endpoint === 'tareas') {
            $data = $this->getInputData();
            $this->deleteTarea($data);
        } else {
            $this->sendResponse(404, [
                'success' => false,
                'message' => 'Endpoint no encontrado'
            ]);
        }
    }

    /**
     * Obtener todas las tareas con filtros
     */
    private function getAllTareas() {
        $currentUser = Usuario::getCurrentUser();
        if (!$currentUser) {
            $this->sendResponse(401, [
                'success' => false,
                'message' => 'No autenticado'
            ]);
            return;
        }

        $filters = [];
        
        // Si no es admin, solo puede ver sus propias tareas
        if (!Usuario::isAdmin()) {
            $filters['usuario_id'] = $currentUser['id'];
        } else if (isset($_GET['usuario_id'])) {
            $filters['usuario_id'] = (int)$_GET['usuario_id'];
        }

        // Filtro por estado
        if (isset($_GET['estado']) && in_array($_GET['estado'], ['pendiente', 'completada'])) {
            $filters['estado'] = $_GET['estado'];
        }

        // Búsqueda
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $filters['search'] = $_GET['search'];
        }

        $tareas = $this->tarea->getAll($filters);
        
        $this->sendResponse(200, [
            'success' => true,
            'data' => $tareas,
            'total' => count($tareas),
            'filters' => $filters
        ]);
    }

    /**
     * Obtener tarea por ID
     */
    private function getTareaById() {
        $currentUser = Usuario::getCurrentUser();
        if (!$currentUser) {
            $this->sendResponse(401, [
                'success' => false,
                'message' => 'No autenticado'
            ]);
            return;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

        if (!$id) {
            $this->sendResponse(400, [
                'success' => false,
                'message' => 'ID de tarea no proporcionado'
            ]);
            return;
        }

        $tarea = $this->tarea->getById($id);

        if (!$tarea) {
            $this->sendResponse(404, [
                'success' => false,
                'message' => 'Tarea no encontrada'
            ]);
            return;
        }

        // Verificar permisos (solo el usuario asignado o admin)
        if ($tarea['usuario_id'] != $currentUser['id'] && !Usuario::isAdmin()) {
            $this->sendResponse(403, [
                'success' => false,
                'message' => 'No tiene permisos para ver esta tarea'
            ]);
            return;
        }

        $this->sendResponse(200, [
            'success' => true,
            'data' => $tarea
        ]);
    }

    /**
     * Obtener estadísticas de tareas
     */
    private function getStats() {
        $currentUser = Usuario::getCurrentUser();
        if (!$currentUser) {
            $this->sendResponse(401, [
                'success' => false,
                'message' => 'No autenticado'
            ]);
            return;
        }

        // Si es admin, puede ver stats de todos o de un usuario específico
        $user_id = null;
        if (Usuario::isAdmin() && isset($_GET['usuario_id'])) {
            $user_id = (int)$_GET['usuario_id'];
        } else if (!Usuario::isAdmin()) {
            $user_id = $currentUser['id'];
        }

        $stats = $this->tarea->getStats($user_id);
        
        $this->sendResponse(200, [
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Buscar tareas
     */
    private function searchTareas() {
        $currentUser = Usuario::getCurrentUser();
        if (!$currentUser) {
            $this->sendResponse(401, [
                'success' => false,
                'message' => 'No autenticado'
            ]);
            return;
        }

        $search_term = isset($_GET['q']) ? trim($_GET['q']) : '';

        if (empty($search_term)) {
            $this->sendResponse(400, [
                'success' => false,
                'message' => 'Término de búsqueda no proporcionado'
            ]);
            return;
        }

        // Si no es admin, solo puede buscar en sus tareas
        $user_id = Usuario::isAdmin() ? null : $currentUser['id'];

        $tareas = $this->tarea->search($search_term, $user_id);
        
        $this->sendResponse(200, [
            'success' => true,
            'data' => $tareas,
            'total' => count($tareas),
            'search_term' => $search_term
        ]);
    }

    /**
     * Crear nueva tarea
     */
    private function createTarea($data) {
        $currentUser = Usuario::getCurrentUser();
        if (!$currentUser) {
            $this->sendResponse(401, [
                'success' => false,
                'message' => 'No autenticado'
            ]);
            return;
        }

        // Validar datos requeridos
        $required = ['titulo', 'usuario_id'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $this->sendResponse(400, [
                    'success' => false,
                    'message' => "El campo {$field} es requerido"
                ]);
                return;
            }
        }

        // Si no es admin, solo puede asignarse tareas a sí mismo
        if (!Usuario::isAdmin() && $data['usuario_id'] != $currentUser['id']) {
            $this->sendResponse(403, [
                'success' => false,
                'message' => 'Solo puede asignarse tareas a sí mismo'
            ]);
            return;
        }

        $result = $this->tarea->create($data);

        if ($result['success']) {
            $this->sendResponse(201, $result);
        } else {
            $this->sendResponse(400, $result);
        }
    }

    /**
     * Actualizar tarea
     */
    private function updateTarea($data) {
        $currentUser = Usuario::getCurrentUser();
        if (!$currentUser) {
            $this->sendResponse(401, [
                'success' => false,
                'message' => 'No autenticado'
            ]);
            return;
        }

        if (empty($data['id'])) {
            $this->sendResponse(400, [
                'success' => false,
                'message' => 'ID de tarea no proporcionado'
            ]);
            return;
        }

        $id = (int)$data['id'];

        // Verificar que la tarea exista y tenga permisos
        $existing_tarea = $this->tarea->getById($id);
        if (!$existing_tarea) {
            $this->sendResponse(404, [
                'success' => false,
                'message' => 'Tarea no encontrada'
            ]);
            return;
        }

        // Verificar permisos (solo el usuario asignado o admin)
        if ($existing_tarea['usuario_id'] != $currentUser['id'] && !Usuario::isAdmin()) {
            $this->sendResponse(403, [
                'success' => false,
                'message' => 'No tiene permisos para actualizar esta tarea'
            ]);
            return;
        }

        // Si no es admin, no puede cambiar el usuario asignado
        if (!Usuario::isAdmin() && isset($data['usuario_id']) && $data['usuario_id'] != $existing_tarea['usuario_id']) {
            $this->sendResponse(403, [
                'success' => false,
                'message' => 'No puede cambiar el usuario asignado'
            ]);
            return;
        }

        $result = $this->tarea->update($id, $data);

        if ($result['success']) {
            $this->sendResponse(200, $result);
        } else {
            $this->sendResponse(400, $result);
        }
    }

    /**
     * Eliminar tarea
     */
    private function deleteTarea($data) {
        $currentUser = Usuario::getCurrentUser();
        if (!$currentUser) {
            $this->sendResponse(401, [
                'success' => false,
                'message' => 'No autenticado'
            ]);
            return;
        }

        if (empty($data['id'])) {
            $this->sendResponse(400, [
                'success' => false,
                'message' => 'ID de tarea no proporcionado'
            ]);
            return;
        }

        $id = (int)$data['id'];

        // Verificar que la tarea exista y tenga permisos
        $existing_tarea = $this->tarea->getById($id);
        if (!$existing_tarea) {
            $this->sendResponse(404, [
                'success' => false,
                'message' => 'Tarea no encontrada'
            ]);
            return;
        }

        // Verificar permisos (solo el usuario asignado o admin)
        if ($existing_tarea['usuario_id'] != $currentUser['id'] && !Usuario::isAdmin()) {
            $this->sendResponse(403, [
                'success' => false,
                'message' => 'No tiene permisos para eliminar esta tarea'
            ]);
            return;
        }

        $result = $this->tarea->delete($id);

        if ($result['success']) {
            $this->sendResponse(200, $result);
        } else {
            $this->sendResponse(400, $result);
        }
    }

    /**
     * Marcar tarea como completada
     */
    private function completeTarea($data) {
        $currentUser = Usuario::getCurrentUser();
        if (!$currentUser) {
            $this->sendResponse(401, [
                'success' => false,
                'message' => 'No autenticado'
            ]);
            return;
        }

        if (empty($data['id'])) {
            $this->sendResponse(400, [
                'success' => false,
                'message' => 'ID de tarea no proporcionado'
            ]);
            return;
        }

        $id = (int)$data['id'];

        // Verificar que la tarea exista y tenga permisos
        $existing_tarea = $this->tarea->getById($id);
        if (!$existing_tarea) {
            $this->sendResponse(404, [
                'success' => false,
                'message' => 'Tarea no encontrada'
            ]);
            return;
        }

        // Verificar permisos (solo el usuario asignado o admin)
        if ($existing_tarea['usuario_id'] != $currentUser['id'] && !Usuario::isAdmin()) {
            $this->sendResponse(403, [
                'success' => false,
                'message' => 'No tiene permisos para modificar esta tarea'
            ]);
            return;
        }

        $result = $this->tarea->complete($id);

        if ($result['success']) {
            $this->sendResponse(200, $result);
        } else {
            $this->sendResponse(400, $result);
        }
    }

    /**
     * Marcar tarea como pendiente
     */
    private function pendingTarea($data) {
        $currentUser = Usuario::getCurrentUser();
        if (!$currentUser) {
            $this->sendResponse(401, [
                'success' => false,
                'message' => 'No autenticado'
            ]);
            return;
        }

        if (empty($data['id'])) {
            $this->sendResponse(400, [
                'success' => false,
                'message' => 'ID de tarea no proporcionado'
            ]);
            return;
        }

        $id = (int)$data['id'];

        // Verificar que la tarea exista y tenga permisos
        $existing_tarea = $this->tarea->getById($id);
        if (!$existing_tarea) {
            $this->sendResponse(404, [
                'success' => false,
                'message' => 'Tarea no encontrada'
            ]);
            return;
        }

        // Verificar permisos (solo el usuario asignado o admin)
        if ($existing_tarea['usuario_id'] != $currentUser['id'] && !Usuario::isAdmin()) {
            $this->sendResponse(403, [
                'success' => false,
                'message' => 'No tiene permisos para modificar esta tarea'
            ]);
            return;
        }

        $result = $this->tarea->pending($id);

        if ($result['success']) {
            $this->sendResponse(200, $result);
        } else {
            $this->sendResponse(400, $result);
        }
    }

    /**
     * Obtener datos de entrada JSON
     */
    private function getInputData() {
        $input = file_get_contents('php://input');
        return json_decode($input, true) ?? [];
    }

    /**
     * Enviar respuesta JSON
     */
    private function sendResponse($statusCode, $data) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

// Archivo: api_tareas.php (punto de entrada para tareas)
/*
// Ejemplo de uso:

require_once 'config/database.php';
require_once 'modelo/Usuario.php';
require_once 'modelo/Tarea.php';
require_once 'controladores/TareaController.php';

// Configurar CORS si es necesario
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$database = new Database();
$db = $database->getConnection();

$controller = new TareaController($db);
$controller->handleRequest();
*/
?>