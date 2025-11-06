<?php
require_once '../base_de_datos/conexion.php';

class SistemaPerfiles {
    
    // Verificar permisos del usuario
    public function tienePermiso($usuario_id, $modulo, $accion) {
        $conexion = conectar();
        
        $sql = "SELECT p.permisos 
                FROM usuarios u 
                INNER JOIN perfiles p ON u.perfil_id = p.id 
                WHERE u.id = ?";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $usuario = $result->fetch_assoc();
            $permisos = json_decode($usuario['permisos'], true);
            
            return in_array($accion, $permisos[$modulo] ?? []);
        }
        
        return false;
    }
    
    // Obtener perfil del usuario
    public function obtenerPerfil($usuario_id) {
        $conexion = conectar();
        
        $sql = "SELECT p.* 
                FROM usuarios u 
                INNER JOIN perfiles p ON u.perfil_id = p.id 
                WHERE u.id = ?";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    // Obtener módulos accesibles según perfil
    public function obtenerModulosAccesibles($usuario_id) {
        $perfil = $this->obtenerPerfil($usuario_id);
        $permisos = json_decode($perfil['permisos'], true);
        
        $modulos = [];
        foreach ($permisos as $modulo => $acciones) {
            if (!empty($acciones) && in_array('leer', $acciones)) {
                $modulos[] = $modulo;
            }
        }
        
        return $modulos;
    }
}
?>