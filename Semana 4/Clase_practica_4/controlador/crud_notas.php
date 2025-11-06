<?php
require_once '../modelo/perfiles.php';

class CRUDNotas {
    private $sistemaPerfiles;
    
    public function __construct() {
        $this->sistemaPerfiles = new SistemaPerfiles();
    }
    
    // CREATE - Docente y Administrador
    public function crearNota($usuario_actual_id, $datos) {
        if (!$this->sistemaPerfiles->tienePermiso($usuario_actual_id, 'notas', 'crear')) {
            return ['error' => 'No tiene permisos para crear notas'];
        }
        
        $conexion = conectar();
        
        // Validar datos requeridos
        if (empty($datos['asignatura_id']) || empty($datos['usuario_id']) || empty($datos['parcial'])) {
            return ['error' => 'Datos incompletos: asignatura_id, usuario_id y parcial son obligatorios'];
        }
        
        // Validar que las notas estén en rango
        $teoria = isset($datos['teoria']) ? floatval($datos['teoria']) : 0;
        $practica = isset($datos['practica']) ? floatval($datos['practica']) : 0;
        
        if ($teoria < 0 || $teoria > 10 || $practica < 0 || $practica > 10) {
            return ['error' => 'Las notas deben estar entre 0 y 10'];
        }
        
        // Verificar si ya existe una nota para este estudiante, asignatura y parcial
        $sql_check = "SELECT id FROM notas WHERE usuario_id = ? AND asignatura_id = ? AND parcial = ?";
        $stmt_check = $conexion->prepare($sql_check);
        $stmt_check->bind_param("iii", $datos['usuario_id'], $datos['asignatura_id'], $datos['parcial']);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows > 0) {
            return ['error' => 'Ya existe una calificación para este estudiante, asignatura y parcial'];
        }
        
        // Calcular promedio
        $promedio = ($teoria + $practica) / 2;
        $obs = isset($datos['obs']) ? $datos['obs'] : '';
        
        $sql = "INSERT INTO notas (asignatura_id, usuario_id, parcial, teoria, practica, obs, usuario_id_creacion, fecha_creacion, hora_creacion) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), CURTIME())";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("iiiddsd", 
            $datos['asignatura_id'], 
            $datos['usuario_id'], 
            $datos['parcial'], 
            $teoria,
            $practica,
            $obs,
            $usuario_actual_id
        );
        
        if ($stmt->execute()) {
            return ['success' => 'Nota creada correctamente', 'id' => $stmt->insert_id];
        } else {
            return ['error' => 'Error al crear nota: ' . $stmt->error];
        }
    }
    
    // READ - Todos los perfiles pueden ver (con limitaciones)
   public function obtenerNotas($usuario_actual_id, $filtros = []) {
    if (!$this->sistemaPerfiles->tienePermiso($usuario_actual_id, 'notas', 'leer')) {
        return ['error' => 'No tiene permisos para ver notas'];
    }
    
    $conexion = conectar();
    $perfil = $this->sistemaPerfiles->obtenerPerfil($usuario_actual_id);
    
    // Construir consulta según perfil
    if ($perfil['nombre'] == 'Estudiante') {
        $sql = "SELECT n.id, a.nombre as asignatura, n.parcial, n.teoria, n.practica, 
                       (n.teoria + n.practica) / 2 as promedio, n.obs, n.fecha_creacion,
                       u.nombre as estudiante
                FROM notas n
                INNER JOIN asignaturas a ON n.asignatura_id = a.id
                INNER JOIN usuarios u ON n.usuario_id = u.id
                WHERE n.usuario_id = ?";
        
        // Ordenar por asignatura alfabéticamente y luego por parcial
        $sql .= " ORDER BY a.nombre ASC, n.parcial ASC";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $usuario_actual_id);
        
    } else if ($perfil['nombre'] == 'Docente') {
        $sql = "SELECT n.id, u.nombre as estudiante, a.nombre as asignatura, n.parcial, 
                       n.teoria, n.practica, (n.teoria + n.practica) / 2 as promedio, 
                       n.obs, n.fecha_creacion
                FROM notas n
                INNER JOIN usuarios u ON n.usuario_id = u.id
                INNER JOIN asignaturas a ON n.asignatura_id = a.id
                WHERE 1=1";
        
        // Docente solo ve estudiantes (perfil_id = 3)
        $sql .= " AND u.perfil_id = 3";
        
        // Ordenar por estudiante alfabéticamente, luego por asignatura y parcial
        $sql .= " ORDER BY u.nombre ASC, a.nombre ASC, n.parcial ASC";
        
        $stmt = $conexion->prepare($sql);
        
    } else {
        // Administrador ve todo
        $sql = "SELECT n.id, u.nombre as estudiante, a.nombre as asignatura, n.parcial, 
                       n.teoria, n.practica, (n.teoria + n.practica) / 2 as promedio, 
                       n.obs, n.fecha_creacion
                FROM notas n
                INNER JOIN usuarios u ON n.usuario_id = u.id
                INNER JOIN asignaturas a ON n.asignatura_id = a.id
                WHERE 1=1";
        
        // Ordenar por estudiante alfabéticamente, luego por asignatura y parcial
        $sql .= " ORDER BY u.nombre ASC, a.nombre ASC, n.parcial ASC";
        
        $stmt = $conexion->prepare($sql);
    }
    
    // Aplicar filtros si existen
    $params = [];
    $types = "";
    
    if (isset($filtros['estudiante_id']) && !empty($filtros['estudiante_id'])) {
        $sql .= " AND n.usuario_id = ?";
        $params[] = $filtros['estudiante_id'];
        $types .= "i";
    }
    
    if (isset($filtros['asignatura_id']) && !empty($filtros['asignatura_id'])) {
        $sql .= " AND n.asignatura_id = ?";
        $params[] = $filtros['asignatura_id'];
        $types .= "i";
    }
    
    // Re-preparar la consulta si hay filtros para evitar errores de parámetros
    if (!empty($params)) {
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $notas = [];
    
    while($row = $result->fetch_assoc()) {
        $notas[] = $row;
    }
    
    $stmt->close();
    $conexion->close();
    
    return $notas;
}
    
    // UPDATE - Docente y Administrador
    public function actualizarNota($usuario_actual_id, $nota_id, $datos) {
        if (!$this->sistemaPerfiles->tienePermiso($usuario_actual_id, 'notas', 'actualizar')) {
            return ['error' => 'No tiene permisos para actualizar notas'];
        }
        
        $conexion = conectar();
        
        // Verificar si la nota existe
        $sql_check = "SELECT id FROM notas WHERE id = ?";
        $stmt_check = $conexion->prepare($sql_check);
        $stmt_check->bind_param("i", $nota_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows === 0) {
            return ['error' => 'Nota no encontrada'];
        }
        
        // Validar que las notas estén en rango
        $teoria = isset($datos['teoria']) ? floatval($datos['teoria']) : null;
        $practica = isset($datos['practica']) ? floatval($datos['practica']) : null;
        
        if ($teoria !== null && ($teoria < 0 || $teoria > 100)) {
            return ['error' => 'La nota de teoría debe estar entre 0 y 100'];
        }
        
        if ($practica !== null && ($practica < 0 || $practica > 100)) {
            return ['error' => 'La nota de práctica debe estar entre 0 y 100'];
        }
        
        // Construir consulta dinámica
        $updates = [];
        $params = [];
        $types = "";
        
        if ($teoria !== null) {
            $updates[] = "teoria = ?";
            $params[] = $teoria;
            $types .= "d";
        }
        
        if ($practica !== null) {
            $updates[] = "practica = ?";
            $params[] = $practica;
            $types .= "d";
        }
        
        if (isset($datos['obs'])) {
            $updates[] = "obs = ?";
            $params[] = $datos['obs'];
            $types .= "s";
        }
        
        // Siempre actualizar campos de auditoría
        $updates[] = "usuario_id_actualizacion = ?";
        $params[] = $usuario_actual_id;
        $types .= "i";
        $updates[] = "fecha_actualizacion = NOW()";
        $updates[] = "hora_actualizacion = CURTIME()";
        
        if (empty($updates)) {
            return ['error' => 'No se proporcionaron datos para actualizar'];
        }
        
        $sql = "UPDATE notas SET " . implode(", ", $updates) . " WHERE id = ?";
        $params[] = $nota_id;
        $types .= "i";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            return ['success' => 'Nota actualizada correctamente'];
        } else {
            return ['error' => 'Error al actualizar nota: ' . $stmt->error];
        }
    }
    
    // DELETE - Solo Administrador
    public function eliminarNota($usuario_actual_id, $nota_id) {
        if (!$this->sistemaPerfiles->tienePermiso($usuario_actual_id, 'notas', 'eliminar')) {
            return ['error' => 'No tiene permisos para eliminar notas'];
        }
        
        $conexion = conectar();
        
        // Verificar si la nota existe
        $sql_check = "SELECT id FROM notas WHERE id = ?";
        $stmt_check = $conexion->prepare($sql_check);
        $stmt_check->bind_param("i", $nota_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows === 0) {
            return ['error' => 'Nota no encontrada'];
        }
        
        $sql = "DELETE FROM notas WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $nota_id);
        
        if ($stmt->execute()) {
            return ['success' => 'Nota eliminada correctamente'];
        } else {
            return ['error' => 'Error al eliminar nota: ' . $stmt->error];
        }
    }
    
    // Obtener nota por ID
    public function obtenerNotaPorId($nota_id) {
        $conexion = conectar();
        
        $sql = "SELECT n.id, u.nombre as estudiante, a.nombre as asignatura, n.parcial, 
                       n.teoria, n.practica, (n.teoria + n.practica) / 2 as promedio, 
                       n.obs, n.fecha_creacion,
                       n.usuario_id, n.asignatura_id
                FROM notas n
                INNER JOIN usuarios u ON n.usuario_id = u.id
                INNER JOIN asignaturas a ON n.asignatura_id = a.id
                WHERE n.id = ?";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $nota_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        $nota = $result->fetch_assoc();
        
        $stmt->close();
        $conexion->close();
        
        return $nota;
    }
    
    // Obtener promedio general por estudiante
    public function obtenerPromedioGeneral($estudiante_id) {
        $conexion = conectar();
        
        $sql = "SELECT AVG((teoria + practica) / 2) as promedio_general 
                FROM notas 
                WHERE usuario_id = ? AND parcial IN (1,2)";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $estudiante_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $promedio = $result->fetch_assoc();
        
        $stmt->close();
        $conexion->close();
        
        return $promedio['promedio_general'] ?? 0;
    }
}
?>