<?php
session_start();
require_once '../base_de_datos/conexion.php';

class Auth {
    private $max_intentos = 3;
    private $tiempo_bloqueo = 900; // 15 minutos en segundos
    
    public function login($email, $password) {
        $conexion = conectar();
        
        // Buscar usuario por email
        $sql = "SELECT u.*, p.permisos, p.nombre as perfil_nombre 
                FROM usuarios u 
                INNER JOIN perfiles p ON u.perfil_id = p.id 
                WHERE u.email = ? AND u.estado = 1";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $stmt->close();
            $conexion->close();
            return ['success' => false, 'message' => 'Credenciales incorrectas'];
        }
        
        $usuario = $result->fetch_assoc();
        
        // Verificar si la cuenta está bloqueada
        if ($this->cuentaBloqueada($usuario)) {
            $stmt->close();
            $conexion->close();
            return ['success' => false, 'message' => 'Cuenta temporalmente bloqueada. Intente más tarde.'];
        }
        
        // Verificar contraseña
        if (password_verify($password, $usuario['contrasena'])) {
            // Login exitoso - resetear intentos fallidos
            $this->resetearIntentos($usuario['id']);
            
            // Actualizar último login
            $this->actualizarUltimoLogin($usuario['id']);
            
            // Crear sesión
            $this->crearSesion($usuario);
            
            $stmt->close();
            $conexion->close();
            
            return ['success' => true, 'message' => 'Login exitoso', 'redirect' => 'dashboard.php'];
            
        } else {
            // Login fallido - incrementar intentos
            $this->incrementarIntentos($usuario['id']);
            
            $intentos_restantes = $this->max_intentos - ($usuario['intentos_fallidos'] + 1);
            
            $stmt->close();
            $conexion->close();
            
            $mensaje = 'Credenciales incorrectas.';
            if ($intentos_restantes > 0) {
                $mensaje .= ' Intentos restantes: ' . $intentos_restantes;
            } else {
                $mensaje .= ' Cuenta bloqueada temporalmente.';
            }
            
            return [
                'success' => false, 
                'message' => $mensaje
            ];
        }
    }
    
    private function cuentaBloqueada($usuario) {
        // Si hay fecha de bloqueo y aún no ha pasado
        if ($usuario['bloqueado_hasta'] && strtotime($usuario['bloqueado_hasta']) > time()) {
            return true;
        }
        
        // Si excedió los intentos máximos
        if ($usuario['intentos_fallidos'] >= $this->max_intentos) {
            // Bloquear la cuenta si no está ya bloqueada
            if (!$usuario['bloqueado_hasta'] || strtotime($usuario['bloqueado_hasta']) <= time()) {
                $this->bloquearCuenta($usuario['id']);
            }
            return true;
        }
        
        return false;
    }
    
    private function bloquearCuenta($usuario_id) {
        $conexion = conectar();
        $bloqueado_hasta = date('Y-m-d H:i:s', time() + $this->tiempo_bloqueo);
        
        $sql = "UPDATE usuarios SET bloqueado_hasta = ? WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("si", $bloqueado_hasta, $usuario_id);
        $stmt->execute();
        $stmt->close();
        $conexion->close();
    }
    
    private function resetearIntentos($usuario_id) {
        $conexion = conectar();
        $sql = "UPDATE usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $stmt->close();
        $conexion->close();
    }
    
    private function incrementarIntentos($usuario_id) {
        $conexion = conectar();
        $sql = "UPDATE usuarios SET intentos_fallidos = intentos_fallidos + 1 WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $stmt->close();
        $conexion->close();
    }
    
    private function actualizarUltimoLogin($usuario_id) {
        $conexion = conectar();
        $sql = "UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $stmt->close();
        $conexion->close();
    }
    
    private function crearSesion($usuario) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_perfil'] = $usuario['perfil_nombre'];
        $_SESSION['usuario_perfil_id'] = $usuario['perfil_id'];
        $_SESSION['permisos'] = json_decode($usuario['permisos'], true);
        $_SESSION['login_time'] = time();
        $_SESSION['session_id'] = session_id();
        
        // Regenerar ID de sesión para prevenir fixation attacks
        session_regenerate_id(true);
    }
    
    public function logout() {
        // Guardar información de la sesión antes de destruirla
        $usuario_nombre = $_SESSION['usuario_nombre'] ?? '';
        
        // Destruir todas las variables de sesión
        $_SESSION = array();
        
        // Destruir la cookie de sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destruir la sesión
        session_destroy();
        
        return [
            'success' => true, 
            'message' => 'Sesión cerrada correctamente - Hasta luego ' . $usuario_nombre, 
            'redirect' => 'login.php'
        ];
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['usuario_id']) && 
               !empty($_SESSION['usuario_id']) && 
               isset($_SESSION['login_time']);
    }
    
    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
        
        // Verificar tiempo de sesión (opcional: expiración de sesión)
        $this->verificarExpiracionSesion();
    }
    
    private function verificarExpiracionSesion() {
        $tiempo_maximo_sesion = 8 * 60 * 60; // 8 horas
        
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > $tiempo_maximo_sesion)) {
            $this->logout();
            header('Location: login.php?expired=1');
            exit;
        }
        
        // Actualizar tiempo de sesión en cada request
        $_SESSION['login_time'] = time();
    }
    
    public function getUserInfo() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['usuario_id'],
                'nombre' => $_SESSION['usuario_nombre'],
                'email' => $_SESSION['usuario_email'],
                'perfil' => $_SESSION['usuario_perfil'],
                'perfil_id' => $_SESSION['usuario_perfil_id'],
                'permisos' => $_SESSION['permisos'],
                'login_time' => $_SESSION['login_time'],
                'session_id' => $_SESSION['session_id']
            ];
        }
        return null;
    }
    
    public function tienePermiso($modulo, $accion) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        $permisos = $_SESSION['permisos'] ?? [];
        
        // Si no hay permisos definidos, denegar acceso
        if (empty($permisos)) {
            return false;
        }
        
        // Verificar si el módulo existe y la acción está permitida
        return isset($permisos[$modulo]) && in_array($accion, $permisos[$modulo]);
    }
    
    public function verificarPermiso($modulo, $accion) {
        if (!$this->tienePermiso($modulo, $accion)) {
            http_response_code(403);
            die('Acceso denegado. No tiene permisos para realizar esta acción.');
        }
    }
    
    public function cambiarContrasena($usuario_id, $contrasena_actual, $nueva_contrasena) {
        $conexion = conectar();
        
        // Obtener contraseña actual
        $sql = "SELECT contrasena FROM usuarios WHERE id = ? AND estado = 1";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => 'Usuario no encontrado'];
        }
        
        $usuario = $result->fetch_assoc();
        
        // Verificar contraseña actual
        if (!password_verify($contrasena_actual, $usuario['contrasena'])) {
            return ['success' => false, 'message' => 'Contraseña actual incorrecta'];
        }
        
        // Actualizar contraseña
        $sql_update = "UPDATE usuarios SET contrasena = ?, usuario_id_actualizacion = ?, fecha_actualizacion = NOW() WHERE id = ?";
        $stmt_update = $conexion->prepare($sql_update);
        $nueva_contrasena_hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
        $stmt_update->bind_param("sii", $nueva_contrasena_hash, $usuario_id, $usuario_id);
        
        if ($stmt_update->execute()) {
            return ['success' => true, 'message' => 'Contraseña actualizada correctamente'];
        } else {
            return ['success' => false, 'message' => 'Error al actualizar contraseña'];
        }
    }
    
    public function getUsuariosConectados() {
        // Esta función podría implementarse con una tabla de sesiones en la base de datos
        // Por ahora retornamos un array vacío como placeholder
        return [];
    }
    
    public function registrarActividad($accion, $detalles = '') {
        $conexion = conectar();
        $usuario_info = $this->getUserInfo();
        
        if ($usuario_info) {
            $sql = "INSERT INTO log_actividades (usuario_id, accion, detalles, ip, user_agent, fecha_creacion) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            
            $stmt = $conexion->prepare($sql);
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'DESCONOCIDO';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'DESCONOCIDO';
            
            $stmt->bind_param("issss", $usuario_info['id'], $accion, $detalles, $ip, $user_agent);
            $stmt->execute();
            $stmt->close();
        }
        
        $conexion->close();
    }
}
?>