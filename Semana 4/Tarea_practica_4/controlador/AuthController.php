<?php
// controllers/AuthController.php


class AuthController {
    private $user;
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->user = new Usuario($this->db);
    }

    // Mostrar formulario de login
    public function showLogin() {
        // Si ya está logueado, redirigir al dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?action=dashboard');
            exit;
        }

        $message = $_GET['message'] ?? '';
        $error = $_GET['error'] ?? '';
        
        // Limpiar mensajes después de mostrarlos
        if (isset($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
        }
        if (isset($_SESSION['flash_error'])) {
            $error = $_SESSION['flash_error'];
            unset($_SESSION['flash_error']);
        }

        include 'vista/login.php';
    }

    // Procesar login vía AJAX
    public function login() {
        // Limpieza de buffer y headers JSON
        ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        // Validar datos de entrada
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email y contraseña son obligatorios']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Formato de email inválido']);
            return;
        }

        // Intentar login
        $result = $this->user->login($email, $password);

        if ($result['success']) {
            
            // Regenerar ID de sesión por seguridad
            session_regenerate_id(true);
            
            // Guardar datos de usuario en sesión
            $_SESSION['user_id'] = $result['user']['id'];
            $_SESSION['user_name'] = $result['user']['nombre'];
            $_SESSION['user_email'] = $result['user']['email'];
            $_SESSION['user_role'] = $result['user']['rol'];
            $_SESSION['login_time'] = time();

            echo json_encode([
                'success' => true,
                'message' => 'Login exitoso',
                'redirect' => 'index.php?action=dashboard'
            ]);
        } else {
            // Registrar intento fallido
            $this->logFailedAttempt($email);
            
            echo json_encode(['success' => false, 'message' => $result['message']]);
        }
    }

    // Cerrar sesión
    public function logout() {
        $user_name = $_SESSION['user_name'] ?? 'Usuario';
        
        // Destruir todas las variables de sesión
        $_SESSION = array();
        
        // Si se usa cookie de sesión, destruirla
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destruir la sesión
        session_destroy();
        
        // Redirigir con mensaje
        header('Location: index.php?action=login&message=' . urlencode("¡Hasta pronto, $user_name!"));
        exit;
    }

    // Cambiar contraseña (implementación completa)
    public function changePassword() {
        if (!$this->checkAuth()) {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        $current_password = trim($_POST['current_password'] ?? '');
        $new_password = trim($_POST['new_password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');
        $user_id = $_SESSION['user_id'];

        // Validaciones
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
            return;
        }

        if ($new_password !== $confirm_password) {
            echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden']);
            return;
        }

        if (strlen($new_password) < 6) {
            echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres']);
            return;
        }

        // Verificar contraseña actual
        $user_data = $this->user->getById($user_id);
        if (!$user_data || !password_verify($current_password, $user_data['contrasena'])) {
            echo json_encode(['success' => false, 'message' => 'Contraseña actual incorrecta']);
            return;
        }

        // Actualizar contraseña
        $result = $this->user->changePassword($user_id, $new_password);
        
        if ($result['success']) {
            // Cerrar sesión en otros dispositivos (regenerar sesión)
            session_regenerate_id(true);
        }
        
        echo json_encode($result);
    }

    // Verificar si el usuario está autenticado
    public static function checkAuth() {
        return isset($_SESSION['user_id']) && isset($_SESSION['login_time']);
    }

    // Obtener datos del usuario actual
    public static function getCurrentUser() {
        if (!self::checkAuth()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role'],
            'login_time' => $_SESSION['login_time']
        ];
    }

    // Requerir autenticación (helper)
    public static function requireAuth() {
        if (!self::checkAuth()) {
            // Guardar la URL a la que intentaba acceder
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            header('Location: index.php?action=login');
            exit;
        }
    }

    // Requerir rol de administrador
    public static function requireAdmin() {
        self::requireAuth();
        if ($_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?action=dashboard&error=' . urlencode('Acceso denegado: Se requieren privilegios de administrador'));
            exit;
        }
    }

    // Registrar intentos fallidos de login (mejora de seguridad)
    private function logFailedAttempt($email) {
        // En un sistema real, guardaría en base de datos o archivo
        $ip = $_SERVER['REMOTE_ADDR'];
        $time = date('Y-m-d H:i:s');
        error_log("Login fallido desde IP: $ip para email: $email en $time");
    }

    // Verificar tiempo de sesión (opcional)
    public static function checkSessionTimeout($max_time = 3600) {
        if (!self::checkAuth()) {
            return false;
        }
        
        if (time() - $_SESSION['login_time'] > $max_time) {
            self::logout();
            return false;
        }
        
        return true;
    }
}