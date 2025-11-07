<?php
// modelo/Usuario.php

class Usuario {
    private $conn;
    private $table = 'usuarios';

    // Constantes para roles
    const ROL_ADMIN = 'admin';
    const ROL_USUARIO = 'usuario';

    // Constantes para estados
    const ESTADO_ACTIVO = 1;
    const ESTADO_INACTIVO = 0;

    // Propiedades del modelo
    public $id;
    public $nombre;
    public $email;
    public $contrasena;
    public $rol;
    public $estado;
    public $fecha_creacion;
    public $ultimo_login;

    /**
     * Constructor
     * @param mysqli $db Conexión a la base de datos
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Login de usuario
     * @param string $email Email del usuario
     * @param string $password Contraseña sin hashear
     * @return array Resultado con usuario o mensaje de error
     */
    public function login($email, $password) {
        // Validar formato de email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Formato de email inválido'
            ];
        }

        // Sanitizar email
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        $sql = "SELECT * FROM {$this->table} WHERE email = ? AND estado = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            return [
                'success' => false,
                'message' => 'Error en la preparación de la consulta: ' . $this->conn->error
            ];
        }

        $estado_activo = self::ESTADO_ACTIVO;
        $stmt->bind_param("si", $email, $estado_activo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return [
                'success' => false,
                'message' => 'Usuario no encontrado o inactivo'
            ];
        }

        $row = $result->fetch_assoc();

        // Verificar contraseña
        if (!password_verify($password, $row['contrasena'])) {
            return [
                'success' => false,
                'message' => 'Contraseña incorrecta'
            ];
        }

        // Actualizar último login
        $this->updateLastLogin($row['id']);

        // Devolver datos del usuario (sin contraseña)
        return [
            'success' => true,
            'user' => [
                'id' => $row['id'],
                'nombre' => $row['nombre'],
                'email' => $row['email'],
                'rol' => $row['rol']
            ]
        ];
    }

    /**
     * Obtener todos los usuarios activos
     * @return array Lista de usuarios
     */
    public function getAll() {
        $query = "SELECT id, nombre, email, rol, estado, fecha_creacion, ultimo_login 
                  FROM {$this->table} 
                  WHERE estado = ?
                  ORDER BY nombre ASC";
        
        $stmt = $this->conn->prepare($query);
        $estado_activo = self::ESTADO_ACTIVO;
        $stmt->bind_param("i", $estado_activo);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $usuarios = [];
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        
        return $usuarios;
    }

    /**
     * Obtener usuario por ID
     * @param int $id ID del usuario
     * @return array|null Datos del usuario o null
     */
    public function getById($id) {
        $query = "SELECT id, nombre, email, rol, estado, fecha_creacion, ultimo_login 
                  FROM {$this->table} 
                  WHERE id = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    /**
     * Crear nuevo usuario
     * @param array $data Datos del usuario: nombre, email, contrasena, rol
     * @return array Resultado de la operación
     */
    public function create($data) {
        // Validar datos requeridos
        if (empty($data['nombre']) || empty($data['email']) || empty($data['contrasena'])) {
            return [
                'success' => false,
                'message' => 'Todos los campos son obligatorios'
            ];
        }

        // Validar longitud de nombre
        if (strlen($data['nombre']) < 3 || strlen($data['nombre']) > 100) {
            return [
                'success' => false,
                'message' => 'El nombre debe tener entre 3 y 100 caracteres'
            ];
        }

        // Validar formato de email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Formato de email inválido'
            ];
        }

        // Sanitizar email
        $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);

        // Verificar si email ya existe
        if ($this->emailExists($email)) {
            return [
                'success' => false,
                'message' => 'El email ya está registrado'
            ];
        }

        // Validar contraseña
        if (strlen($data['contrasena']) < 6) {
            return [
                'success' => false,
                'message' => 'La contraseña debe tener al menos 6 caracteres'
            ];
        }

        // Hash de la contraseña
        $hashed_password = password_hash($data['contrasena'], PASSWORD_DEFAULT);

        $query = "INSERT INTO {$this->table} 
                  (nombre, email, contrasena, rol, estado, fecha_creacion) 
                  VALUES (?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            return [
                'success' => false,
                'message' => 'Error al preparar consulta: ' . $this->conn->error
            ];
        }

        $rol = $data['rol'] ?? self::ROL_USUARIO;
        $estado = self::ESTADO_ACTIVO;
        
        $stmt->bind_param("ssssi", 
            $data['nombre'],
            $email,
            $hashed_password,
            $rol,
            $estado
        );
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Usuario creado correctamente',
                'id' => $stmt->insert_id
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al crear usuario: ' . $stmt->error
            ];
        }
    }

    /**
     * Actualizar usuario
     * @param int $id ID del usuario
     * @param array $data Datos a actualizar
     * @return array Resultado de la operación
     */
    public function update($id, $data) {
        // Validar que el usuario exista
        if (!$this->getById($id)) {
            return [
                'success' => false,
                'message' => 'Usuario no encontrado'
            ];
        }

        $updates = [];
        $params = [];
        $types = "";

        // Campos permitidos para actualizar
        $allowed_fields = ['nombre', 'email', 'rol'];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowed_fields)) {
                // Validaciones específicas
                if ($key === 'nombre') {
                    if (strlen($value) < 3 || strlen($value) > 100) {
                        return [
                            'success' => false,
                            'message' => 'El nombre debe tener entre 3 y 100 caracteres'
                        ];
                    }
                }
                
                if ($key === 'email') {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        return [
                            'success' => false,
                            'message' => 'Formato de email inválido'
                        ];
                    }
                    $value = filter_var($value, FILTER_SANITIZE_EMAIL);
                    
                    // Verificar si el email ya existe (excluyendo el usuario actual)
                    if ($this->emailExists($value, $id)) {
                        return [
                            'success' => false,
                            'message' => 'El email ya está registrado por otro usuario'
                        ];
                    }
                }

                $updates[] = "$key = ?";
                $params[] = $value;
                $types .= $this->getParamType($value);
            }
        }

        if (empty($updates)) {
            return [
                'success' => false,
                'message' => 'No se proporcionaron datos para actualizar'
            ];
        }

        $query = "UPDATE {$this->table} 
                  SET " . implode(", ", $updates) . ", 
                      fecha_creacion = fecha_creacion 
                  WHERE id = ?";
        
        $params[] = $id;
        $types .= "i";

        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            return [
                'success' => false,
                'message' => 'Error al preparar consulta: ' . $this->conn->error
            ];
        }

        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Usuario actualizado correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al actualizar usuario: ' . $stmt->error
            ];
        }
    }

    /**
     * Eliminar usuario (eliminación lógica)
     * @param int $id ID del usuario
     * @return array Resultado de la operación
     */
    public function delete($id) {
        // No permitir eliminar el único admin
        if ($this->isLastAdmin($id)) {
            return [
                'success' => false,
                'message' => 'No se puede eliminar el único administrador del sistema'
            ];
        }

        // Verificar que el usuario exista
        if (!$this->getById($id)) {
            return [
                'success' => false,
                'message' => 'Usuario no encontrado'
            ];
        }

        $query = "UPDATE {$this->table} 
                  SET estado = ? 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $estado_inactivo = self::ESTADO_INACTIVO;
        $stmt->bind_param("ii", $estado_inactivo, $id);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Usuario eliminado correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al eliminar usuario: ' . $stmt->error
            ];
        }
    }

    /**
     * Verificar si email existe
     * @param string $email Email a verificar
     * @param int|null $exclude_id ID a excluir (para update)
     * @return bool
     */
    public function emailExists($email, $exclude_id = null) {
        $query = "SELECT id FROM {$this->table} 
                  WHERE email = ? AND estado = ?";
        
        if ($exclude_id) {
            $query .= " AND id != ?";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            return false;
        }

        $estado_activo = self::ESTADO_ACTIVO;
        
        if ($exclude_id) {
            $stmt->bind_param("sii", $email, $estado_activo, $exclude_id);
        } else {
            $stmt->bind_param("si", $email, $estado_activo);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0;
    }

    /**
     * Actualizar último login
     * @param int $usuario_id ID del usuario
     * @return bool
     */
    private function updateLastLogin($usuario_id) {
        $query = "UPDATE {$this->table} 
                  SET ultimo_login = NOW() 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $usuario_id);
        return $stmt->execute();
    }

    /**
     * Obtener usuarios activos (simplificado para selects)
     * @return array Lista de usuarios activos
     */
    public function getActiveUsuarios() {
        return $this->getAll(); // Reutiliza el método getAll que ya filtra por estado activo
    }

    /**
     * Contar total de usuarios activos
     * @return int Total de usuarios
     */
    public function countUsuarios() {
        $query = "SELECT COUNT(*) as total 
                  FROM {$this->table} 
                  WHERE estado = ?";
        
        $stmt = $this->conn->prepare($query);
        $estado_activo = self::ESTADO_ACTIVO;
        $stmt->bind_param("i", $estado_activo);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc()['total'] ?? 0;
    }

    /**
     * Verificar si es el último administrador
     * @param int $id ID del usuario a verificar
     * @return bool
     */
    private function isLastAdmin($id) {
        $query = "SELECT COUNT(*) as total 
                  FROM {$this->table} 
                  WHERE rol = ? AND estado = ? AND id != ?";
        
        $stmt = $this->conn->prepare($query);
        $rol_admin = self::ROL_ADMIN;
        $estado_activo = self::ESTADO_ACTIVO;
        $stmt->bind_param("sii", $rol_admin, $estado_activo, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'] == 0;
    }

    /**
     * Cambiar contraseña de usuario
     * @param int $usuario_id ID del usuario
     * @param string $new_password Nueva contraseña
     * @return array Resultado de la operación
     */
    public function changePassword($usuario_id, $new_password) {
        // Validar longitud de contraseña
        if (strlen($new_password) < 6) {
            return [
                'success' => false,
                'message' => 'La contraseña debe tener al menos 6 caracteres'
            ];
        }

        $query = "UPDATE {$this->table} 
                  SET contrasena = ? 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt->bind_param("si", $hashed_password, $usuario_id);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Contraseña actualizada correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al actualizar contraseña: ' . $stmt->error
            ];
        }
    }

    /**
     * Obtener usuario actual desde sesión
     * @return array|null Datos del usuario actual
     */
    public static function getCurrentUser() {

        
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'nombre' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'rol' => $_SESSION['user_role']
        ];
    }

    /**
     * Verificar si el usuario actual es administrador
     * @return bool
     */
    public static function isAdmin() {
        $user = self::getCurrentUser();
        return $user && $user['rol'] === self::ROL_ADMIN;
    }

    /**
     * Helper para determinar el tipo de parámetro
     * @param mixed $value Valor a evaluar
     * @return string Tipo de parámetro (i, d, s)
     */
    private function getParamType($value) {
        if (is_int($value)) return "i";
        if (is_double($value)) return "d";
        return "s";
    }
}
?>