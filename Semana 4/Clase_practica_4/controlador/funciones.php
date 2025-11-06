<?php
require_once '../base_de_datos/conexion.php';

function getEstudiantes() {
    $conexion = conectar();
    
    // Solo estudiantes activos (perfil_id = 3 y estado = 1)
    $sql = "SELECT id, nombre, email, perfil_id 
            FROM usuarios 
            WHERE perfil_id = 3 AND estado = 1 
            ORDER BY nombre";
    
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $estudiantes = [];
    while($row = $result->fetch_assoc()) {
        $estudiantes[] = $row;
    }
    
    $stmt->close();
    $conexion->close();
    
    return $estudiantes;
}

function getDocentes() {
    $conexion = conectar();
    
    // Solo docentes activos (perfil_id = 2 y estado = 1)
    $sql = "SELECT id, nombre, email, perfil_id 
            FROM usuarios 
            WHERE perfil_id = 2 AND estado = 1 
            ORDER BY nombre";
    
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $docentes = [];
    while($row = $result->fetch_assoc()) {
        $docentes[] = $row;
    }
    
    $stmt->close();
    $conexion->close();
    
    return $docentes;
}

function getAsignaturas() {
    $conexion = conectar();
    $sql = "SELECT id, nombre, descripcion FROM asignaturas WHERE estado = 1 ORDER BY nombre";
    
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $asignaturas = [];
    while($row = $result->fetch_assoc()) {
        $asignaturas[] = $row;
    }
    
    $stmt->close();
    $conexion->close();
    
    return $asignaturas;
}

function getNotasCompletas($filtros = []) {
    $conexion = conectar();
    
    $sql = "SELECT n.id, u.nombre as estudiante, u.id as estudiante_id, 
                   a.nombre as asignatura, a.id as asignatura_id,
                   n.parcial, n.teoria, n.practica, 
                   (n.teoria + n.practica) / 2 as promedio,
                   n.obs, n.fecha_creacion, p.nombre as perfil_estudiante
            FROM notas n
            INNER JOIN usuarios u ON n.usuario_id = u.id
            INNER JOIN asignaturas a ON n.asignatura_id = a.id
            INNER JOIN perfiles p ON u.perfil_id = p.id
            WHERE u.estado = 1 AND a.estado = 1";
    
    // Aplicar filtros
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
    
    if (isset($filtros['parcial']) && !empty($filtros['parcial'])) {
        $sql .= " AND n.parcial = ?";
        $params[] = $filtros['parcial'];
        $types .= "i";
    }
    
    $sql .= " ORDER BY u.nombre, a.nombre, n.parcial";
    
    $stmt = $conexion->prepare($sql);
    
    // Bind parameters si existen
    if (!empty($params)) {
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

function getNotasPorEstudiante($estudiante_id) {
    $conexion = conectar();
    
    $sql = "SELECT n.id, a.nombre as asignatura, a.id as asignatura_id, n.parcial, 
                   n.teoria, n.practica, (n.teoria + n.practica) / 2 as promedio,
                   n.obs, n.fecha_creacion
            FROM notas n
            INNER JOIN asignaturas a ON n.asignatura_id = a.id
            WHERE n.usuario_id = ? AND a.estado = 1
            ORDER BY a.nombre, n.parcial";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $estudiante_id);
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

function getPromedioGeneral($estudiante_id) {
    $conexion = conectar();
    
    $sql = "SELECT AVG((teoria + practica) / 2) as promedio_general
            FROM notas 
            WHERE usuario_id = ? AND parcial IN (1,2)";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $estudiante_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $promedio_data = $result->fetch_assoc();
    $promedio_general = $promedio_data['promedio_general'] ?? 0;
    
    $stmt->close();
    $conexion->close();
    
    return number_format($promedio_general, 2);
}

function getEstadisticasNotas($estudiante_id = null) {
    $conexion = conectar();
    
    $sql = "SELECT 
                COUNT(*) as total_notas,
                AVG((teoria + practica) / 2) as promedio_general,
                MAX((teoria + practica) / 2) as nota_maxima,
                MIN((teoria + practica) / 2) as nota_minima,
                COUNT(CASE WHEN (teoria + practica) / 2 >= 7 THEN 1 END) as notas_aprobadas,
                COUNT(CASE WHEN (teoria + practica) / 2 < 7 THEN 1 END) as notas_reprobadas
            FROM notas 
            WHERE 1=1";
    
    $params = [];
    $types = "";
    
    if ($estudiante_id) {
        $sql .= " AND usuario_id = ?";
        $params[] = $estudiante_id;
        $types .= "i";
    }
    
    $sql .= " AND parcial IN (1,2)";
    
    $stmt = $conexion->prepare($sql);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $estadisticas = $result->fetch_assoc();
    
    $stmt->close();
    $conexion->close();
    
    // Calcular porcentajes
    if ($estadisticas['total_notas'] > 0) {
        $estadisticas['porcentaje_aprobadas'] = number_format(
            ($estadisticas['notas_aprobadas'] / $estadisticas['total_notas']) * 100, 2
        );
        $estadisticas['porcentaje_reprobadas'] = number_format(
            ($estadisticas['notas_reprobadas'] / $estadisticas['total_notas']) * 100, 2
        );
        $estadisticas['promedio_general'] = number_format($estadisticas['promedio_general'], 2);
    } else {
        $estadisticas['porcentaje_aprobadas'] = 0;
        $estadisticas['porcentaje_reprobadas'] = 0;
        $estadisticas['promedio_general'] = 0;
    }
    
    return $estadisticas;
}

function getAsignaturasPorEstudiante($estudiante_id) {
    $conexion = conectar();
    
    $sql = "SELECT DISTINCT a.id, a.nombre
            FROM notas n
            INNER JOIN asignaturas a ON n.asignatura_id = a.id
            WHERE n.usuario_id = ? AND a.estado = 1
            ORDER BY a.nombre";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $estudiante_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $asignaturas = [];
    while($row = $result->fetch_assoc()) {
        $asignaturas[] = $row;
    }
    
    $stmt->close();
    $conexion->close();
    
    return $asignaturas;
}

function verificarNotaExistente($usuario_id, $asignatura_id, $parcial) {
    $conexion = conectar();
    
    $sql = "SELECT id FROM notas 
            WHERE usuario_id = ? AND asignatura_id = ? AND parcial = ?";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iii", $usuario_id, $asignatura_id, $parcial);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $existe = $result->num_rows > 0;
    
    $stmt->close();
    $conexion->close();
    
    return $existe;
}

function getPerfiles() {
    $conexion = conectar();
    
    $sql = "SELECT id, nombre, descripcion, permisos 
            FROM perfiles 
            WHERE estado = 1 
            ORDER BY nombre";
    
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $perfiles = [];
    while($row = $result->fetch_assoc()) {
        $perfiles[] = $row;
    }
    
    $stmt->close();
    $conexion->close();
    
    return $perfiles;
}

function validarEmailUnico($email, $usuario_id = null) {
    $conexion = conectar();
    
    $sql = "SELECT id FROM usuarios WHERE email = ? AND estado = 1";
    $params = [$email];
    $types = "s";
    
    if ($usuario_id) {
        $sql .= " AND id != ?";
        $params[] = $usuario_id;
        $types .= "i";
    }
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $disponible = $result->num_rows === 0;
    
    $stmt->close();
    $conexion->close();
    
    return $disponible;
}

// Función auxiliar para debug (puede ser removida en producción)
function debugData($data, $title = 'Debug') {
    if (isset($_GET['debug'])) {
        echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px 0; border: 1px solid #ccc;'>";
        echo "<h3>$title</h3>";
        echo "<pre>" . print_r($data, true) . "</pre>";
        echo "</div>";
    }
}
?>