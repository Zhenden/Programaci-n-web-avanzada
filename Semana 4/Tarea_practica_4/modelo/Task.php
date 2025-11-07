<?php
// modelo/Task.php

class Task {
    private $conn;
    private $table = 'tareas';

    // Constantes para estados y prioridades
    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_COMPLETADA = 'completada';
    
    const PRIORIDAD_BAJA = 'baja';
    const PRIORIDAD_MEDIA = 'media';
    const PRIORIDAD_ALTA = 'alta';

    // Propiedades del modelo
    public $id;
    public $titulo;
    public $descripcion;
    public $usuario_id;
    public $estado;
    public $prioridad;
    public $fecha_creacion;
    public $fecha_vencimiento;
    public $fecha_completada;

    /**
     * Constructor
     * @param mysqli $db Conexión a la base de datos
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Obtener todas las tareas con filtros avanzados
     * @param array $filters Filtros: usuario_id, estado, prioridad, fecha_vencimiento, search
     * @return array Lista de tareas
     */
    public function getAll($filters = []) {
        $query = "SELECT t.*, u.nombre as usuario_nombre 
                  FROM {$this->table} t
                  LEFT JOIN usuarios u ON t.usuario_id = u.id
                  WHERE 1=1";
        
        $params = [];
        $types = "";

        // Filtro por usuario (si no es admin)
        if (isset($filters['usuario_id']) && !empty($filters['usuario_id'])) {
            $query .= " AND t.usuario_id = ?";
            $params[] = $filters['usuario_id'];
            $types .= "i";
        }

        // Filtro por estado
        if (isset($filters['estado']) && !empty($filters['estado'])) {
            $query .= " AND t.estado = ?";
            $params[] = $filters['estado'];
            $types .= "s";
        }

        // Filtro por prioridad
        if (isset($filters['prioridad']) && !empty($filters['prioridad'])) {
            $query .= " AND t.prioridad = ?";
            $params[] = $filters['prioridad'];
            $types .= "s";
        }

        // Filtro por fecha de vencimiento exacta
        if (isset($filters['fecha_vencimiento']) && !empty($filters['fecha_vencimiento'])) {
            $query .= " AND t.fecha_vencimiento = ?";
            $params[] = $filters['fecha_vencimiento'];
            $types .= "s";
        }

        // Búsqueda por texto
        if (isset($filters['search']) && !empty($filters['search'])) {
            $query .= " AND (t.titulo LIKE ? OR t.descripcion LIKE ?)";
            $search_term = "%{$filters['search']}%";
            $params[] = $search_term;
            $params[] = $search_term;
            $types .= "ss";
        }

        // Ordenamiento: prioridad (Alta primero), fecha vencimiento, creación
        $query .= " ORDER BY 
                    FIELD(t.prioridad, 'alta', 'media', 'baja') ASC,
                    t.fecha_vencimiento ASC,
                    t.fecha_creacion DESC";

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

    /**
     * Obtener tarea por ID con verificación de permisos
     * @param int $id ID de la tarea
     * @return array|null Datos de la tarea o null
     */
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

    /**
     * Obtener tareas de un usuario específico
     * @param int $user_id ID del usuario
     * @param array $filters Filtros adicionales
     * @return array Lista de tareas
     */
    public function getByUser($user_id, $filters = []) {
        $filters['usuario_id'] = $user_id;
        return $this->getAll($filters);
    }

    /**
     * Crear nueva tarea
     * @param array $data Datos: titulo, descripcion, usuario_id, estado, prioridad, fecha_vencimiento
     * @return array Resultado con ID o mensaje de error
     */
    public function create($data) {
    // Validar datos requeridos
    if (empty($data['titulo'])) {
        return [
            'success' => false,
            'message' => 'El título es obligatorio'
        ];
    }

    if (empty($data['usuario_id'])) {
        return [
            'success' => false,
            'message' => 'El usuario asignado es obligatorio'
        ];
    }

    // Validar longitud de título
    if (strlen($data['titulo']) > 255) {
        return [
            'success' => false,
            'message' => 'El título no puede exceder 255 caracteres'
        ];
    }

    // Validar prioridad
    $prioridad = $data['prioridad'] ?? self::PRIORIDAD_MEDIA;
    if (!in_array($prioridad, [self::PRIORIDAD_BAJA, self::PRIORIDAD_MEDIA, self::PRIORIDAD_ALTA])) {
        $prioridad = self::PRIORIDAD_MEDIA;
    }

    // Validar estado
    $estado = $data['estado'] ?? self::ESTADO_PENDIENTE;
    if (!in_array($estado, [self::ESTADO_PENDIENTE, self::ESTADO_COMPLETADA])) {
        $estado = self::ESTADO_PENDIENTE;
    }

    // Definir variables locales (ANTES del bind_param)
    $titulo = trim($data['titulo']);
    $descripcion = trim($data['descripcion'] ?? '');
    $fecha_vencimiento = !empty($data['fecha_vencimiento']) ? $data['fecha_vencimiento'] : null;
    $usuario_asignado = (int)$data['usuario_id'];
    $usuario_creador_id = $_SESSION['user_id'] ?? $usuario_asignado;

    $query = "INSERT INTO {$this->table} 
              (titulo, descripcion, usuario_id, estado, prioridad, fecha_vencimiento, usuario_creador_id) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $this->conn->prepare($query);
    if (!$stmt) {
        return [
            'success' => false,
            'message' => 'Error al preparar consulta: ' . $this->conn->error
        ];
    }

    // Si fecha_vencimiento es null, hay que usar 's' pero pasar null explícito
    $stmt->bind_param("ssisssi",
        $titulo,
        $descripcion,
        $usuario_asignado,
        $estado,
        $prioridad,
        $fecha_vencimiento,
        $usuario_creador_id
    );

    if ($stmt->execute()) {
        return [
            'success' => true,
            'message' => 'Tarea creada correctamente',
            'id' => $stmt->insert_id
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Error al crear tarea: ' . $stmt->error
        ];
    }
}


    /**
     * Actualizar tarea existente
     * @param int $id ID de la tarea
     * @param array $data Datos a actualizar
     * @return array Resultado de la operación
     */
    public function update($id, $data) {
        // Verificar que la tarea exista
        $existing_task = $this->getById($id);
        if (!$existing_task) {
            return [
                'success' => false,
                'message' => 'Tarea no encontrada'
            ];
        }

        $allowed_fields = ['titulo', 'descripcion', 'usuario_id', 'estado', 'prioridad', 'fecha_vencimiento'];
        $updates = [];
        $params = [];
        $types = "";

        foreach ($data as $key => $value) {
            if (in_array($key, $allowed_fields)) {
                // Validaciones específicas
                if ($key === 'titulo') {
                    if (empty($value)) {
                        return [
                            'success' => false,
                            'message' => 'El título no puede estar vacío'
                        ];
                    }
                    if (strlen($value) > 255) {
                        return [
                            'success' => false,
                            'message' => 'El título no puede exceder 255 caracteres'
                        ];
                    }
                }

                if ($key === 'prioridad') {
                    if (!in_array($value, [self::PRIORIDAD_BAJA, self::PRIORIDAD_MEDIA, self::PRIORIDAD_ALTA])) {
                        return [
                            'success' => false,
                            'message' => 'Prioridad inválida'
                        ];
                    }
                }

                if ($key === 'estado') {
                    if (!in_array($value, [self::ESTADO_PENDIENTE, self::ESTADO_COMPLETADA])) {
                        return [
                            'success' => false,
                            'message' => 'Estado inválido'
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
                'message' => 'No se proporcionaron datos válidos para actualizar'
            ];
        }

        // Si se marca como completada, agregar fecha de completado
        if (isset($data['estado']) && $data['estado'] == self::ESTADO_COMPLETADA) {
            $updates[] = "fecha_completada = NOW()";
        }

        // Si se cambia de completada a pendiente, limpiar fecha de completado
        if (isset($data['estado']) && $data['estado'] == self::ESTADO_PENDIENTE) {
            $updates[] = "fecha_completada = NULL";
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
                'message' => 'Tarea actualizada correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al actualizar tarea: ' . $stmt->error
            ];
        }
    }

    /**
     * Eliminar tarea (eliminación física)
     * @param int $id ID de la tarea
     * @return array Resultado de la operación
     */
    public function delete($id) {
        // Verificar que la tarea exista
        if (!$this->getById($id)) {
            return [
                'success' => false,
                'message' => 'Tarea no encontrada'
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
                'message' => 'Tarea eliminada correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al eliminar tarea: ' . $stmt->error
            ];
        }
    }

    /**
     * Marcar tarea como completada
     * @param int $id ID de la tarea
     * @return array Resultado de la operación
     */
    public function complete($id) {
        // Verificar que la tarea exista
        if (!$this->getById($id)) {
            return [
                'success' => false,
                'message' => 'Tarea no encontrada'
            ];
        }

        $query = "UPDATE {$this->table} 
                  SET estado = ?, fecha_completada = NOW() 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            return [
                'success' => false,
                'message' => 'Error al preparar consulta: ' . $this->conn->error
            ];
        }

        $estado_completada = self::ESTADO_COMPLETADA;
        $stmt->bind_param("si", $estado_completada, $id);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Tarea marcada como completada'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al completar tarea: ' . $stmt->error
            ];
        }
    }

    /**
     * Marcar tarea como pendiente
     * @param int $id ID de la tarea
     * @return array Resultado de la operación
     */
    public function pending($id) {
        // Verificar que la tarea exista
        if (!$this->getById($id)) {
            return [
                'success' => false,
                'message' => 'Tarea no encontrada'
            ];
        }

        $query = "UPDATE {$this->table} 
                  SET estado = ?, fecha_completada = NULL 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            return [
                'success' => false,
                'message' => 'Error al preparar consulta: ' . $this->conn->error
            ];
        }

        $estado_pendiente = self::ESTADO_PENDIENTE;
        $stmt->bind_param("si", $estado_pendiente, $id);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Tarea marcada como pendiente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al cambiar estado de tarea: ' . $stmt->error
            ];
        }
    }

    /**
     * Obtener estadísticas de tareas
     * @param int|null $user_id ID del usuario (null para admin)
     * @return array Estadísticas
     */
    public function getStats($user_id = null) {
        $query = "SELECT 
                    COUNT(*) as total,
                    COUNT(CASE WHEN estado = 'completada' THEN 1 END) as completadas,
                    COUNT(CASE WHEN estado = 'pendiente' THEN 1 END) as pendientes,
                    COUNT(CASE WHEN fecha_vencimiento < CURDATE() AND estado = 'pendiente' THEN 1 END) as vencidas,
                    COUNT(CASE WHEN prioridad = 'alta' THEN 1 END) as alta_prioridad,
                    COUNT(CASE WHEN prioridad = 'media' THEN 1 END) as media_prioridad,
                    COUNT(CASE WHEN prioridad = 'baja' THEN 1 END) as baja_prioridad
                  FROM {$this->table}";
        
        if ($user_id) {
            $query .= " WHERE usuario_id = ?";
        }

        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            error_log("Error preparando getStats: " . $this->conn->error);
            return [
                'total' => 0,
                'completadas' => 0,
                'pendientes' => 0,
                'vencidas' => 0,
                'alta_prioridad' => 0,
                'media_prioridad' => 0,
                'baja_prioridad' => 0
            ];
        }

        if ($user_id) {
            $stmt->bind_param("i", $user_id);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc() ?? [
            'total' => 0,
            'completadas' => 0,
            'pendientes' => 0,
            'vencidas' => 0,
            'alta_prioridad' => 0,
            'media_prioridad' => 0,
            'baja_prioridad' => 0
        ];
    }

    /**
     * Obtener tareas próximas a vencer (próximos 3 días)
     * @param int|null $user_id ID del usuario (null para admin)
     * @return array Lista de tareas próximas a vencer
     */
    public function getUpcomingTasks($user_id = null) {
        $query = "SELECT t.*, u.nombre as usuario_nombre 
                  FROM {$this->table} t
                  LEFT JOIN usuarios u ON t.usuario_id = u.id
                  WHERE t.estado = ? 
                  AND t.fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)";
        
        if ($user_id) {
            $query .= " AND t.usuario_id = ?";
        }

        $query .= " ORDER BY t.fecha_vencimiento ASC, FIELD(t.prioridad, 'alta', 'media', 'baja') ASC";

        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            error_log("Error preparando getUpcomingTasks: " . $this->conn->error);
            return [];
        }

        $estado_pendiente = self::ESTADO_PENDIENTE;
        
        if ($user_id) {
            $stmt->bind_param("si", $estado_pendiente, $user_id);
        } else {
            $stmt->bind_param("s", $estado_pendiente);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $tasks = [];
        while ($row = $result->fetch_assoc()) {
            $tasks[] = $row;
        }
        
        return $tasks;
    }

    /**
     * Buscar tareas por término de búsqueda
     * @param string $search_term Término a buscar
     * @param int|null $user_id ID del usuario (null para admin)
     * @return array Lista de tareas encontradas
     */
    public function search($search_term, $user_id = null) {
        $query = "SELECT t.*, u.nombre as usuario_nombre 
                  FROM {$this->table} t
                  LEFT JOIN usuarios u ON t.usuario_id = u.id
                  WHERE (t.titulo LIKE ? OR t.descripcion LIKE ?)";
        
        if ($user_id) {
            $query .= " AND t.usuario_id = ?";
        }

        $query .= " ORDER BY t.fecha_creacion DESC";

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

    /**
     * Helper para determinar el tipo de parámetro
     * @param mixed $value Valor a evaluar
     * @return string Tipo de parámetro (i, d, s)
     */
    private function getParamType($value) {
        if (is_int($value)) return "i";
        if (is_double($value) || is_float($value)) return "d";
        return "s";
    }
}