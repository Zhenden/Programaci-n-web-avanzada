<?php
require_once '../modelo/perfiles.php';

class CRUDUsuarios {
    private $sistemaPerfiles;
    
    public function __construct() {
        $this->sistemaPerfiles = new SistemaPerfiles();
    }
    
    // CREATE - Solo administrador
    public function crearUsuario($usuario_actual_id, $datos) {
        if (!$this->sistemaPerfiles->tienePermiso($usuario_actual_id, 'usuarios', 'crear')) {
            return ['error' => 'No tiene permisos para crear usuarios'];
        }
        
        $conexion = conectar();
        
        // Verificar si el email ya existe
        $sql_check = "SELECT id FROM usuarios WHERE email = ?";
        $stmt_check = $conexion->prepare($sql_check);
        $stmt_check->bind_param("s", $datos['email']);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows > 0) {
            $stmt_check->close();
            $conexion->close();
            return ['error' => 'El email ya está registrado'];
        }
        $stmt_check->close();
        
        $sql = "INSERT INTO usuarios (nombre, email, contrasena, perfil_id, rol, obs, usuario_id_creacion, fecha_creacion, hora_creacion) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), CURTIME())";
        
        // Asignar valores por defecto
        $rol = isset($datos['rol']) ? $datos['rol'] : 3; // Rol por defecto: 3 (usuario normal)
        $obs = isset($datos['obs']) ? $datos['obs'] : '';
        $contrasena_hash = password_hash($datos['contrasena'], PASSWORD_DEFAULT);
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssiisi", 
            $datos['nombre'], 
            $datos['email'], 
            $contrasena_hash,
            $datos['perfil_id'],
            $rol,
            $obs,
            $usuario_actual_id
        );
        
        if ($stmt->execute()) {
            $nuevo_id = $stmt->insert_id;
            $stmt->close();
            $conexion->close();
            return ['success' => 'Usuario creado correctamente', 'id' => $nuevo_id];
        } else {
            $error = $stmt->error;
            $stmt->close();
            $conexion->close();
            return ['error' => 'Error al crear usuario: ' . $error];
        }
    }
    
    // READ - Todos los perfiles pueden ver (con limitaciones)
    public function obtenerUsuarios($usuario_actual_id, $filtros = []) {
        if (!$this->sistemaPerfiles->tienePermiso($usuario_actual_id, 'usuarios', 'leer')) {
            return ['error' => 'No tiene permisos para ver usuarios'];
        }
        
        $conexion = conectar();
        $perfil = $this->sistemaPerfiles->obtenerPerfil($usuario_actual_id);
        
        // Restricciones según perfil
        if ($perfil['nombre'] == 'Docente') {
            $sql = "SELECT u.id, u.nombre, u.email, u.rol, p.nombre as perfil, p.id as perfil_id 
                    FROM usuarios u 
                    INNER JOIN perfiles p ON u.perfil_id = p.id 
                    WHERE u.perfil_id = 3 AND u.estado = 1"; // Solo estudiantes activos
        } else {
            $sql = "SELECT u.id, u.nombre, u.email, u.rol, p.nombre as perfil, p.id as perfil_id 
                    FROM usuarios u 
                    INNER JOIN perfiles p ON u.perfil_id = p.id 
                    WHERE u.estado = 1"; // Todos los usuarios activos
        }
        
        // Aplicar filtros si existen
        if (isset($filtros['busqueda']) && !empty($filtros['busqueda'])) {
            $busqueda = "%" . $filtros['busqueda'] . "%";
            $sql .= " AND (u.nombre LIKE ? OR u.email LIKE ?)";
        }
        
        $sql .= " ORDER BY u.nombre";
        
        $stmt = $conexion->prepare($sql);
        
        if (isset($filtros['busqueda']) && !empty($filtros['busqueda'])) {
            $stmt->bind_param("ss", $busqueda, $busqueda);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $usuarios = [];
        
        while($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        
        $stmt->close();
        $conexion->close();
        
        return $usuarios;
    }
    
    // UPDATE - Solo administrador
    public function actualizarUsuario($usuario_actual_id, $usuario_id, $datos) {
        if (!$this->sistemaPerfiles->tienePermiso($usuario_actual_id, 'usuarios', 'actualizar')) {
            return ['error' => 'No tiene permisos para actualizar usuarios'];
        }
        
        $conexion = conectar();
        
        // Verificar si el usuario existe
        $sql_check = "SELECT id FROM usuarios WHERE id = ? AND estado = 1";
        $stmt_check = $conexion->prepare($sql_check);
        $stmt_check->bind_param("i", $usuario_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows === 0) {
            return ['error' => 'Usuario no encontrado'];
        }
        
        // Verificar si el email ya existe en otro usuario
        if (isset($datos['email'])) {
            $sql_email = "SELECT id FROM usuarios WHERE email = ? AND id != ?";
            $stmt_email = $conexion->prepare($sql_email);
            $stmt_email->bind_param("si", $datos['email'], $usuario_id);
            $stmt_email->execute();
            $result_email = $stmt_email->get_result();
            
            if ($result_email->num_rows > 0) {
                return ['error' => 'El email ya está registrado por otro usuario'];
            }
        }
        
        // Construir consulta dinámica
        $updates = [];
        $params = [];
        $types = "";
        
        if (isset($datos['nombre'])) {
            $updates[] = "nombre = ?";
            $params[] = $datos['nombre'];
            $types .= "s";
        }
        
        if (isset($datos['email'])) {
            $updates[] = "email = ?";
            $params[] = $datos['email'];
            $types .= "s";
        }
        
        if (isset($datos['rol'])) {
            $updates[] = "rol = ?";
            $params[] = $datos['rol'];
            $types .= "i";
        }
        
        if (isset($datos['perfil_id'])) {
            $updates[] = "perfil_id = ?";
            $params[] = $datos['perfil_id'];
            $types .= "i";
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
        
        $sql = "UPDATE usuarios SET " . implode(", ", $updates) . " WHERE id = ?";
        $params[] = $usuario_id;
        $types .= "i";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            return ['success' => 'Usuario actualizado correctamente'];
        } else {
            return ['error' => 'Error al actualizar usuario: ' . $stmt->error];
        }
    }
    
    // DELETE - Solo administrador (eliminación lógica)
    public function eliminarUsuario($usuario_actual_id, $usuario_id) {
        if (!$this->sistemaPerfiles->tienePermiso($usuario_actual_id, 'usuarios', 'eliminar')) {
            return ['error' => 'No tiene permisos para eliminar usuarios'];
        }
        
        // No permitir eliminarse a sí mismo
        if ($usuario_actual_id == $usuario_id) {
            return ['error' => 'No puede eliminar su propio usuario'];
        }
        
        $conexion = conectar();
        
        // Verificar si el usuario existe
        $sql_check = "SELECT id FROM usuarios WHERE id = ? AND estado = 1";
        $stmt_check = $conexion->prepare($sql_check);
        $stmt_check->bind_param("i", $usuario_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows === 0) {
            return ['error' => 'Usuario no encontrado'];
        }
        
        // Eliminación lógica (cambiar estado a 0)
        $sql = "UPDATE usuarios SET estado = 0, usuario_id_actualizacion = ?, fecha_actualizacion = NOW(), hora_actualizacion = CURTIME() WHERE id = ?";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ii", $usuario_actual_id, $usuario_id);
        
        if ($stmt->execute()) {
            return ['success' => 'Usuario eliminado correctamente'];
        } else {
            return ['error' => 'Error al eliminar usuario: ' . $stmt->error];
        }
    }
    
    // Obtener usuario por ID
    public function obtenerUsuarioPorId($usuario_id) {
        $conexion = conectar();
        
        $sql = "SELECT u.id, u.nombre, u.email, u.rol, u.perfil_id, p.nombre as perfil, u.obs 
                FROM usuarios u 
                INNER JOIN perfiles p ON u.perfil_id = p.id 
                WHERE u.id = ? AND u.estado = 1";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        $usuario = $result->fetch_assoc();
        
        $stmt->close();
        $conexion->close();
        
        return $usuario;
    }
}
?>