<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Role.php';

class UserController extends BaseController {
    
    /**
     * Muestra la lista de usuarios con paginación y búsqueda
     */
    public function index() {
        $this->checkRole(['Administrator']);
        
        // Obtener parámetros de búsqueda y paginación
        $search = $_GET['search'] ?? '';
        $page = max(1, intval($_GET['page'] ?? 1));
        $per_page = 10;
        $offset = ($page - 1) * $per_page;
        
        try {
            $cache = CacheManager::getInstance();
            $keyList = "users:list:search={$search}:page={$page}:per={$per_page}";
            $keyTotal = "users:total:search={$search}";
            $keyRoles = "roles:all";

            $users = $cache->get($keyList);
            $total_users = $cache->get($keyTotal);
            $roles = $cache->get($keyRoles);

            if ($users === null || $total_users === null) {
                $users = $this->getUsersWithPagination($search, $per_page, $offset);
                $total_users = $this->getTotalUsers($search);
                $cache->set($keyList, $users, 600);
                $cache->set($keyTotal, $total_users, 600);
            }

            if ($roles === null) {
                $roleModel = new Role();
                $roles = $roleModel->all();
                $cache->set($keyRoles, $roles, 3600);
            }
            
            $this->view('users/index', [
                'users' => $users,
                'roles' => $roles,
                'search' => $search,
                'current_page' => $page,
                'total_pages' => ceil($total_users / $per_page),
                'total_users' => $total_users
            ]);
            
        } catch (Exception $e) {
            error_log("Error en UserController::index: " . $e->getMessage());
            SessionManager::set('error', 'Error al cargar la lista de usuarios');
            $this->view('users/index', [
                'users' => [],
                'roles' => [],
                'search' => $search,
                'current_page' => 1,
                'total_pages' => 1,
                'total_users' => 0
            ]);
        }
    }
    
    /**
     * Muestra el formulario de creación de usuarios
     */
    public function createForm() {
        $this->checkRole(['Administrator']);
        
        try {
            $roleModel = new Role();
            $roles = $roleModel->all();
            
            $this->view('users/create', [
                'roles' => $roles,
                'csrf_token' => $this->generateCSRFToken()
            ]);
            
        } catch (Exception $e) {
            error_log("Error en UserController::createForm: " . $e->getMessage());
            SessionManager::set('error', 'Error al cargar el formulario');
            $this->redirect('index.php?action=users');
        }
    }
    
    /**
     * Procesa la creación de un nuevo usuario
     */
    public function store() {
        $this->checkRole(['Administrator']);
        
        try {
            // Validar CSRF token
            if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('Token CSRF inválido');
            }
            
            // Validar datos del formulario
            $validation = $this->validateUserData($_POST);
            if (!$validation['valid']) {
                SessionManager::set('error', $validation['message']);
                $this->redirect('index.php?action=user_create');
                return;
            }
            
            // Verificar si el email ya existe
            $userModel = new User();
            if ($userModel->findByEmail($_POST['email'])) {
                SessionManager::set('error', 'El email ya está registrado');
                $this->redirect('index.php?action=user_create');
                return;
            }
            
            // Crear el usuario
            $user_id = $userModel->create(
                $_POST['username'],
                $_POST['email'],
                $_POST['password'],
                intval($_POST['role_id'])
            );
            
            if ($user_id) {
                SessionManager::set('success', 'Usuario creado exitosamente');
                CacheManager::getInstance()->deleteByPrefix('users:');
                $this->redirect('index.php?action=users');
            } else {
                throw new Exception('Error al crear el usuario');
            }
            
        } catch (Exception $e) {
            error_log("Error en UserController::store: " . $e->getMessage());
            SessionManager::set('error', 'Error al crear el usuario: ' . $e->getMessage());
            $this->redirect('index.php?action=user_create');
        }
    }
    
    /**
     * Elimina un usuario
     */
    public function delete() {
        $this->checkRole(['Administrator']);
        
        try {
            $user_id = intval($_POST['user_id'] ?? 0);
            $current_user_id = SessionManager::get('user_id');
            
            // Prevenir auto-eliminación
            if ($user_id === $current_user_id) {
                SessionManager::set('error', 'No puedes eliminar tu propio usuario');
                $this->redirect('index.php?action=users');
                return;
            }
            
            // Validar CSRF token
            if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('Token CSRF inválido');
            }
            
            // Eliminar el usuario
            $userModel = new User();
            try {
                if ($userModel->delete($user_id)) {
                    SessionManager::set('success', 'Usuario eliminado exitosamente');
                    CacheManager::getInstance()->deleteByPrefix('users:');
                } else {
                    SessionManager::set('error', 'Error al eliminar el usuario');
                }
            } catch (Exception $e) {
                // Capturar error de clave foránea u otros errores de BD
                $errorMessage = $e->getMessage();
                error_log("Error al eliminar usuario $user_id: " . $errorMessage);
                
                if (strpos($errorMessage, 'foreign key constraint') !== false) {
                    SessionManager::set('error', 'No se puede eliminar el usuario porque tiene registros relacionados (comentarios, pedidos, etc.)');
                } else {
                    SessionManager::set('error', 'Error al eliminar el usuario: ' . $errorMessage);
                }
            }
            
            $this->redirect('index.php?action=users');
            
        } catch (Exception $e) {
            error_log("Error en UserController::delete: " . $e->getMessage());
            SessionManager::set('error', 'Error al eliminar el usuario');
            $this->redirect('index.php?action=users');
        }
    }
    
    /**
     * Obtiene usuarios con paginación y búsqueda
     */
    private function getUsersWithPagination($search, $per_page, $offset) {
        $search_condition = '';
        $params = [];
        $types = '';
        
        if (!empty($search)) {
            $search_condition = "WHERE u.username LIKE ? OR u.email LIKE ?";
            $search_param = "%$search%";
            $params = [$search_param, $search_param];
            $types = 'ss';
        }
        
        $sql = "SELECT u.id, u.username, u.email, u.created_at, r.name as role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                $search_condition 
                ORDER BY u.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $params[] = $per_page;
        $params[] = $offset;
        $types .= 'ii';
        
        $stmt = $this->executePrepared($sql, $types, $params);
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Obtiene el total de usuarios para paginación
     */
    private function getTotalUsers($search) {
        $search_condition = '';
        $params = [];
        $types = '';
        
        if (!empty($search)) {
            $search_condition = "WHERE u.username LIKE ? OR u.email LIKE ?";
            $search_param = "%$search%";
            $params = [$search_param, $search_param];
            $types = 'ss';
        }
        
        $sql = "SELECT COUNT(*) as total FROM users u $search_condition";
        $stmt = $this->executePrepared($sql, $types, $params);
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'];
    }
    
    /**
     * Valida los datos del usuario
     */
    private function validateUserData($data) {
        // Validar username
        if (empty($data['username']) || strlen($data['username']) < 3) {
            return ['valid' => false, 'message' => 'El nombre de usuario debe tener al menos 3 caracteres'];
        }
        
        // Validar email
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'message' => 'El email no es válido'];
        }
        
        // Validar contraseña
        if (empty($data['password']) || strlen($data['password']) < 6) {
            return ['valid' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres'];
        }
        
        // Validar confirmación de contraseña
        if ($data['password'] !== $data['password_confirm']) {
            return ['valid' => false, 'message' => 'Las contraseñas no coinciden'];
        }
        
        // Validar rol
        if (empty($data['role_id']) || !is_numeric($data['role_id'])) {
            return ['valid' => false, 'message' => 'Debe seleccionar un rol válido'];
        }
        
        return ['valid' => true, 'message' => 'Datos válidos'];
    }
    
    /**
     * Obtiene la clase CSS para el badge del rol
     */
    public function getRoleBadgeClass($roleName) {
        $badgeClasses = [
            'Administrator' => 'danger',
            'User' => 'primary',
            'Guest' => 'secondary',
            'Manager' => 'warning',
            'Employee' => 'info'
        ];
        
        return $badgeClasses[$roleName] ?? 'secondary';
    }
    
    // Los métodos generateCSRFToken() y validateCSRFToken() ahora están en BaseController
}