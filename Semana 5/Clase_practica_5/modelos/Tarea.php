<?php
class Tarea {
    private $conn;
    private $table = 'tareas';

    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_completado = 'completado';
    
    public $id;
    public $titulo;
    public $descripcion;
    public $usuario_id;
    public $estado;

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todas las tareas con filtros
    public function getAll($filters = []) {
        $query = "SELECT t.*, u.nombre as usuario_nombre 
                  FROM {$this->table} t
                  LEFT JOIN usuarios u ON t.usuario_id = u.id
                  WHERE 1=1";
        
        $params = [];
        $types = "";

        if (!empty($filters['usuario_id'])) {
            $query .= " AND t.usuario_id = ?";
            $params[] = $filters['usuario_id'];
            $types .= "i";
        }

        if (!empty($filters['estado'])) {
            $query .= " AND t.estado = ?";
            $params[] = $filters['estado'];
            $types .= "s";
        }

        if (!empty($filters['search'])) {
            $query .= " AND (t.titulo LIKE ? OR t.descripcion LIKE ?)";
            $search_term = "%{$filters['search']}%";
            $params[] = $search_term;
            $params[] = $search_term;
            $types .= "ss";
        }

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("Error preparando getAll: " . $this->conn->error);
            return [];
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $tasks = [];
        while ($row = $result->fetch_assoc()) {
            $tasks[] = $row;
        }
        
        return $tasks;
    }

    // Obtener tarea por ID
    public function getById($id) {
        $query = "SELECT t.*, u.nombre as usuario_nombre 
                  FROM {$this->table} t
                  LEFT JOIN usuarios u ON t.usuario_id = u.id
                  WHERE t.id = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("Error preparando getById: " . $this->conn->error);
            return null;
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

        public function create($data) {
    // Validar datos requeridos
    if (empty($data['titulo'])) {
        return ['success' => false, 'message' => 'El título es obligatorio'];
    }

    if (empty($data['usuario_id'])) {
        return ['success' => false, 'message' => 'El usuario asignado es obligatorio'];
    }

    if (strlen($data['titulo']) > 255) {
        return ['success' => false, 'message' => 'El título no puede exceder 255 caracteres'];
    }

    $estado = $data['estado'] ?? self::ESTADO_PENDIENTE;
    $descripcion = $data['descripcion'] ?? '';

    $query = "INSERT INTO {$this->table} (titulo, descripcion, usuario_id, estado)
              VALUES (?, ?, ?, ?)";

    $stmt = $this->conn->prepare($query);
    if (!$stmt) {
        return ['success' => false, 'message' => 'Error al preparar consulta: ' . $this->conn->error];
    }

    $stmt->bind_param("ssis", 
        $data['titulo'],
        $descripcion,
        $data['usuario_id'],
        $estado
    );

    if ($stmt->execute()) {
        return [
            'success' => true,
            'message' => 'Tarea creada correctamente',
            'id' => $stmt->insert_id
        ];
    } else {
        return ['success' => false, 'message' => 'Error al crear tarea: ' . $stmt->error];
    }
}



    // Actualizar tarea
    public function update($id, $data) {
        $existing_task = $this->getById($id);
        if (!$existing_task) {
            return ['success' => false, 'message' => 'Tarea no encontrada'];
        }

        $allowed_fields = ['titulo', 'descripcion', 'usuario_id', 'estado'];
        $updates = [];
        $params = [];
        $types = "";

        foreach ($data as $key => $value) {
            if (in_array($key, $allowed_fields)) {
                if ($key === 'titulo') {
                    if (empty($value)) {
                        return ['success' => false, 'message' => 'El título no puede estar vacío'];
                    }
                    if (strlen($value) > 255) {
                        return ['success' => false, 'message' => 'El título no puede exceder 255 caracteres'];
                    }
                }

                if ($key === 'estado' && !in_array($value, [self::ESTADO_PENDIENTE, self::ESTADO_completado])) {
                    return ['success' => false, 'message' => 'Estado inválido'];
                }

                $updates[] = "$key = ?";
                $params[] = $value;
                $types .= $this->getParamType($value);
            }
        }

        if (empty($updates)) {
            return ['success' => false, 'message' => 'No se proporcionaron datos válidos para actualizar'];
        }

        $query = "UPDATE {$this->table} SET " . implode(", ", $updates) . " WHERE id = ?";
        $params[] = $id;
        $types .= "i";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Error al preparar consulta: ' . $this->conn->error];
        }

        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Tarea actualizada correctamente'];
        } else {
            return ['success' => false, 'message' => 'Error al actualizar tarea: ' . $stmt->error];
        }
    }

    // Eliminar tarea
    public function delete($id) {
        if (!$this->getById($id)) {
            return ['success' => false, 'message' => 'Tarea no encontrada'];
        }

        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return ['success' => false, 'message' => 'Error al preparar consulta: ' . $this->conn->error];
        }

        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Tarea eliminada correctamente'];
        } else {
            return ['success' => false, 'message' => 'Error al eliminar tarea: ' . $stmt->error];
        }
    }

    // Marcar tarea como completado
    public function complete($id) {
    if (!$this->getById($id)) {
        return ['success' => false, 'message' => 'Tarea no encontrada'];
    }

    $query = "UPDATE {$this->table} SET estado = 'completado' WHERE id = ?";
    $stmt = $this->conn->prepare($query);

    if (!$stmt) {
        return ['success' => false, 'message' => 'Error al preparar consulta: ' . $this->conn->error];
    }

    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Tarea marcada como completado'];
    } else {
        return ['success' => false, 'message' => 'Error al completar tarea: ' . $stmt->error];
    }
}



    // Marcar tarea como pendiente
    public function pending($id) {
    if (!$this->getById($id)) {
        return ['success' => false, 'message' => 'Tarea no encontrada'];
    }

    $query = "UPDATE {$this->table} SET estado = 'pendiente' WHERE id = ?";
    $stmt = $this->conn->prepare($query);

    if (!$stmt) {
        return ['success' => false, 'message' => 'Error al preparar consulta: ' . $this->conn->error];
    }

    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Tarea reabierta'];
    } else {
        return ['success' => false, 'message' => 'Error al reabrir tarea: ' . $stmt->error];
    }
}


    // Estadísticas de tareas
    public function getStats($user_id = null) {
        $query = "SELECT 
                    COUNT(*) as total,
                    COUNT(CASE WHEN estado = 'completado' THEN 1 END) as completados,
                    COUNT(CASE WHEN estado = 'pendiente' THEN 1 END) as pendientes
                  FROM {$this->table}";
        
        if ($user_id) {
            $query .= " WHERE usuario_id = ?";
        }

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("Error preparando getStats: " . $this->conn->error);
            return ['total' => 0, 'completados' => 0, 'pendientes' => 0];
        }

        if ($user_id) {
            $stmt->bind_param("i", $user_id);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc() ?? ['total' => 0, 'completados' => 0, 'pendientes' => 0];
    }

    // Buscar tareas
    public function search($search_term, $user_id = null) {
        $query = "SELECT t.*, u.nombre as usuario_nombre 
                  FROM {$this->table} t
                  LEFT JOIN usuarios u ON t.usuario_id = u.id
                  WHERE (t.titulo LIKE ? OR t.descripcion LIKE ?)";
        
        if ($user_id) {
            $query .= " AND t.usuario_id = ?";
        }

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("Error preparando search: " . $this->conn->error);
            return [];
        }

        $search_pattern = "%" . $search_term . "%";
        
        if ($user_id) {
            $stmt->bind_param("ssi", $search_pattern, $search_pattern, $user_id);
        } else {
            $stmt->bind_param("ss", $search_pattern, $search_pattern);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $tasks = [];
        while ($row = $result->fetch_assoc()) {
            $tasks[] = $row;
        }
        
        return $tasks;
    }

    // Detectar tipo de parámetro
    private function getParamType($value) {
        if (is_int($value)) return "i";
        if (is_double($value) || is_float($value)) return "d";
        return "s";
    }
}
?>
