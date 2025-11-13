<?php
// modelo/Usuario.php

class Usuario {
    private $conn;
    private $table = 'usuarios';

    // Propiedades del modelo (coinciden con la BD)
    public $id;
    public $nombre;
    public $email;
    public $contraseña;
    public $rol_id;

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

        $sql = "SELECT * FROM {$this->table} WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            return [
                'success' => false,
                'message' => 'Error en la preparación de la consulta: ' . $this->conn->error
            ];
        }

        $stmt->bind_param("s", $email);
        
        if (!$stmt->execute()) {
            return [
                'success' => false,
                'message' => 'Error al ejecutar consulta: ' . $stmt->error
            ];
        }
        
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return [
                'success' => false,
                'message' => 'Usuario no encontrado'
            ];
        }

        $row = $result->fetch_assoc();

        // Verificar contraseña - campo corregido a 'contraseña'
        if (!password_verify($password, $row['contraseña'])) {
            return [
                'success' => false,
                'message' => 'Contraseña incorrecta'
            ];
        }

        // Devolver datos del usuario (sin contraseña)
        return [
            'success' => true,
            'user' => [
                'id' => $row['id'],
                'nombre' => $row['nombre'],
                'email' => $row['email'],
                'rol_id' => $row['rol_id']
            ]
        ];
    }

    /**
     * Obtener todos los usuarios
     * @return array Lista de usuarios
     */
    public function getAll() {
        $query = "SELECT u.id, u.nombre, u.email, u.rol_id, r.rol_nombre 
                  FROM {$this->table} u
                  LEFT JOIN roles r ON u.rol_id = r.rol_id
                  ORDER BY u.nombre ASC";
        
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            return []; // Retorna array vacío en caso de error
        }
        
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
        $query = "SELECT u.id, u.nombre, u.email, u.rol_id, r.rol_nombre 
                  FROM {$this->table} u
                  LEFT JOIN roles r ON u.rol_id = r.rol_id
                  WHERE u.id = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            return null;
        }
        
        $stmt->bind_param("i", $id);
        
        if (!$stmt->execute()) {
            return null;
        }
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }

    /**
     * Crear nuevo usuario
     * @param array $data Datos del usuario: nombre, email, contraseña, rol_id
     * @return array Resultado de la operación
     */
    public function create($data) {
        // Validar datos requeridos
        if (empty($data['nombre']) || empty($data['email']) || empty($data['contraseña'])) {
            return [
                'success' => false,
                'message' => 'Todos los campos son obligatorios'
            ];
        }

        // Validar longitud de nombre
        if (strlen($data['nombre']) < 3 || strlen($data['nombre']) > 50) {
            return [
                'success' => false,
                'message' => 'El nombre debe tener entre 3 y 50 caracteres'
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
        if (strlen($data['contraseña']) < 6) {
            return [
                'success' => false,
                'message' => 'La contraseña debe tener al menos 6 caracteres'
            ];
        }

        // Hash de la contraseña
        $hashed_password = password_hash($data['contraseña'], PASSWORD_DEFAULT);

        $query = "INSERT INTO {$this->table} 
                  (nombre, email, contraseña, rol_id) 
                  VALUES (?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            return [
                'success' => false,
                'message' => 'Error al preparar consulta: ' . $this->conn->error
            ];
        }

        $rol_id = $data['rol_id'] ?? 3; // Valor por defecto: Miembro del equipo
        
        $stmt->bind_param("sssi", 
            $data['nombre'],
            $email,
            $hashed_password,
            $rol_id
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
        $usuario = $this->getById($id);
        if (!$usuario) {
            return [
                'success' => false,
                'message' => 'Usuario no encontrado'
            ];
        }

        $updates = [];
        $params = [];
        $types = "";

        // Campos permitidos para actualizar
        $allowed_fields = ['nombre', 'email', 'rol_id'];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowed_fields)) {
                // Validaciones específicas
                if ($key === 'nombre') {
                    if (strlen($value) < 3 || strlen($value) > 50) {
                        return [
                            'success' => false,
                            'message' => 'El nombre debe tener entre 3 y 50 caracteres'
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
                  SET " . implode(", ", $updates) . " 
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

    public function updateRole($id, $rol) {
        if (!in_array($rol, [1, 2, 3])) {
            return ['success' => false, 'message' => 'Rol inválido'];
        }

        $query = "UPDATE {$this->table} SET rol = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $rol, $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Rol actualizado correctamente'];
        } else {
            return ['success' => false, 'message' => 'Error al actualizar rol: ' . $stmt->error];
        }
    }

    /**
     * Eliminar usuario
     * @param int $id ID del usuario
     * @return array Resultado de la operación
     */
    public function delete($id) {
        // Verificar que el usuario exista
        if (!$this->getById($id)) {
            return [
                'success' => false,
                'message' => 'Usuario no encontrado'
            ];
        }

        $query = "DELETE FROM {$this->table} WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            return [
                'success' => false,
                'message' => 'Error al preparar consulta: ' . $this->conn->error
            ];
        }
        
        $stmt->bind_param("i", $id);
        
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
                  SET contraseña = ? 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            return [
                'success' => false,
                'message' => 'Error al preparar consulta: ' . $this->conn->error
            ];
        }
        
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
     * Verificar si email existe
     * @param string $email Email a verificar
     * @param int $exclude_id ID de usuario a excluir (para updates)
     * @return bool
     */
    private function emailExists($email, $exclude_id = null) {
        $query = "SELECT id FROM {$this->table} WHERE email = ?";
        
        if ($exclude_id) {
            $query .= " AND id != ?";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            return false;
        }
        
        if ($exclude_id) {
            $stmt->bind_param("si", $email, $exclude_id);
        } else {
            $stmt->bind_param("s", $email);
        }
        
        if (!$stmt->execute()) {
            return false;
        }
        
        $result = $stmt->get_result();
        
        return $result->num_rows > 0;
    }

    /**
     * Helper para determinar el tipo de parámetro
     * @param mixed $value Valor a evaluar
     * @return string Tipo de parámetro (i, d, s, b)
     */
    private function getParamType($value) {
        if (is_int($value)) return "i";
        if (is_float($value)) return "d";
        if (is_bool($value)) return "i"; // MySQL no tiene tipo boolean, se usa INT
        return "s";
    }

    /**
     * Obtener usuario actual desde sesión
     * @return array|null Datos del usuario actual
     */
    public static function getCurrentUser() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'nombre' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'rol_id' => $_SESSION['user_role']
        ];
    }

    /**
     * Verificar si el usuario actual es administrador
     * @return bool
     */
    public static function isAdmin() {
        $user = self::getCurrentUser();
        return $user && $user['rol_id'] == 1; // 1 = Administrador en la BD
    }
}
?>