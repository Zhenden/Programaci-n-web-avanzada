<?php
// controladores/UsuarioController.php

class UsuarioController {
    private $usuario;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->usuario = new Usuario($db);
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
            case 'usuarios':
                $this->getAllUsuarios();
                break;
            case 'usuario':
                $this->getUsuarioById();
                break;
            case 'current':
                $this->getCurrentUser();
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
            case 'login':
                $this->login($data);
                break;
            case 'usuarios':
                $this->createUsuario($data);
                break;
            case 'change-password':
                $this->changePassword($data);
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
        if ($endpoint === 'usuarios') {
            $data = $this->getInputData();
            $this->updateUsuario($data);
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
        if ($endpoint === 'usuarios') {
            $data = $this->getInputData();
            $this->deleteUsuario($data);
        } else {
            $this->sendResponse(404, [
                'success' => false,
                'message' => 'Endpoint no encontrado'
            ]);
        }
    }

    /**
     * Obtener todos los usuarios
     */
    private function getAllUsuarios() {
        // Verificar permisos (solo administradores)
        if (!Usuario::isAdmin()) {
            $this->sendResponse(403, [
                'success' => false,
                'message' => 'No tiene permisos para realizar esta acción'
            ]);
            return;
        }

        $usuarios = $this->usuario->getAll();
        
        $this->sendResponse(200, [
            'success' => true,
            'data' => $usuarios,
            'total' => count($usuarios)
        ]);
    }

    /**
     * Obtener usuario por ID
     */
    private function getUsuarioById() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

        if (!$id) {
            $this->sendResponse(400, [
                'success' => false,
                'message' => 'ID de usuario no proporcionado'
            ]);
            return;
        }

        // Verificar permisos (solo el propio usuario o administrador)
        $currentUser = Usuario::getCurrentUser();
        if (!$currentUser || ($currentUser['id'] != $id && !Usuario::isAdmin())) {
            $this->sendResponse(403, [
                'success' => false,
                'message' => 'No tiene permisos para ver este usuario'
            ]);
            return;
        }

        $usuario = $this->usuario->getById($id);

        if (!$usuario) {
            $this->sendResponse(404, [
                'success' => false,
                'message' => 'Usuario no encontrado'
            ]);
            return;
        }

        $this->sendResponse(200, [
            'success' => true,
            'data' => $usuario
        ]);
    }

    /**
     * Obtener usuario actual
     */
    private function getCurrentUser() {
        $currentUser = Usuario::getCurrentUser();

        if (!$currentUser) {
            $this->sendResponse(401, [
                'success' => false,
                'message' => 'No hay usuario autenticado'
            ]);
            return;
        }

        $this->sendResponse(200, [
            'success' => true,
            'data' => $currentUser
        ]);
    }

    /**
     * Login de usuario
     */
    private function login($data) {
        // Validar datos requeridos
        if (empty($data['email']) || empty($data['contraseña'])) {
            $this->sendResponse(400, [
                'success' => false,
                'message' => 'Email y contraseña son requeridos'
            ]);
            return;
        }

        $result = $this->usuario->login($data['email'], $data['contraseña']);

        if ($result['success']) {
            // Iniciar sesión
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION['user_id'] = $result['user']['id'];
            $_SESSION['user_name'] = $result['user']['nombre'];
            $_SESSION['user_email'] = $result['user']['email'];
            $_SESSION['user_role'] = $result['user']['rol_id'];

            $this->sendResponse(200, $result);
        } else {
            $this->sendResponse(401, $result);
        }
    }

    /**
     * Crear nuevo usuario
     */
    private function createUsuario($data) {
        // Verificar permisos (solo administradores pueden crear usuarios con roles específicos)
        if (isset($data['rol_id']) && $data['rol_id'] != 3 && !Usuario::isAdmin()) {
            $this->sendResponse(403, [
                'success' => false,
                'message' => 'No tiene permisos para crear usuarios con este rol'
            ]);
            return;
        }

        // Validar datos requeridos
        $required = ['nombre', 'email', 'contraseña'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $this->sendResponse(400, [
                    'success' => false,
                    'message' => "El campo {$field} es requerido"
                ]);
                return;
            }
        }

        $result = $this->usuario->create($data);

        if ($result['success']) {
            $this->sendResponse(201, $result);
        } else {
            $this->sendResponse(400, $result);
        }
    }

    /**
     * Actualizar usuario
     */
    private function updateUsuario($data) {
        if (empty($data['id'])) {
            $this->sendResponse(400, [
                'success' => false,
                'message' => 'ID de usuario no proporcionado'
            ]);
            return;
        }

        $id = (int)$data['id'];

        // Verificar permisos (solo el propio usuario o administrador)
        $currentUser = Usuario::getCurrentUser();
        if (!$currentUser || ($currentUser['id'] != $id && !Usuario::isAdmin())) {
            $this->sendResponse(403, [
                'success' => false,
                'message' => 'No tiene permisos para actualizar este usuario'
            ]);
            return;
        }

        // Solo administradores pueden cambiar el rol
        if (isset($data['rol_id']) && !Usuario::isAdmin()) {
            unset($data['rol_id']); // Remover rol_id si no es admin
        }

        $result = $this->usuario->update($id, $data);

        if ($result['success']) {
            $this->sendResponse(200, $result);
        } else {
            $this->sendResponse(400, $result);
        }
    }

    /**
     * Eliminar usuario
     */
    private function deleteUsuario($data) {
        if (empty($data['id'])) {
            $this->sendResponse(400, [
                'success' => false,
                'message' => 'ID de usuario no proporcionado'
            ]);
            return;
        }

        // Verificar permisos (solo administradores)
        if (!Usuario::isAdmin()) {
            $this->sendResponse(403, [
                'success' => false,
                'message' => 'No tiene permisos para eliminar usuarios'
            ]);
            return;
        }

        $id = (int)$data['id'];

        // No permitir que un usuario se elimine a sí mismo
        $currentUser = Usuario::getCurrentUser();
        if ($currentUser && $currentUser['id'] == $id) {
            $this->sendResponse(400, [
                'success' => false,
                'message' => 'No puede eliminar su propio usuario'
            ]);
            return;
        }

        $result = $this->usuario->delete($id);

        if ($result['success']) {
            $this->sendResponse(200, $result);
        } else {
            $this->sendResponse(400, $result);
        }
    }

    /**
     * Cambiar contraseña
     */
    private function changePassword($data) {
        $required = ['usuario_id', 'nueva_contraseña'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $this->sendResponse(400, [
                    'success' => false,
                    'message' => "El campo {$field} es requerido"
                ]);
                return;
            }
        }

        $usuario_id = (int)$data['usuario_id'];

        // Verificar permisos (solo el propio usuario o administrador)
        $currentUser = Usuario::getCurrentUser();
        if (!$currentUser || ($currentUser['id'] != $usuario_id && !Usuario::isAdmin())) {
            $this->sendResponse(403, [
                'success' => false,
                'message' => 'No tiene permisos para cambiar la contraseña de este usuario'
            ]);
            return;
        }

        $result = $this->usuario->changePassword($usuario_id, $data['nueva_contraseña']);

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

    /**
     * Cerrar sesión
     */
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Destruir todas las variables de sesión
        $_SESSION = array();

        // Destruir la sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();

        $this->sendResponse(200, [
            'success' => true,
            'message' => 'Sesión cerrada correctamente'
        ]);
    }
}

// Archivo: api.php (punto de entrada)
/*
// Ejemplo de uso:

require_once 'config/database.php';
require_once 'modelo/Usuario.php';
require_once 'controladores/UsuarioController.php';

// Configurar CORS si es necesario
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

$database = new Database();
$db = $database->getConnection();

$controller = new UsuarioController($db);

// Manejar logout separadamente
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    $controller->logout();
} else {
    $controller->handleRequest();
}
*/
?>