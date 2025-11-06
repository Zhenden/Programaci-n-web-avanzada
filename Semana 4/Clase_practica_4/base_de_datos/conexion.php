<?php
// 📦 Parámetros de conexión - DEFINIR CORRECTAMENTE
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', '01_calif');

// 🚪 Crear conexión - FORMA SEGURA
function conectar() {
    $conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // 🛡️ Verificar conexión
    if ($conexion->connect_error) {
        die("❌ Error de conexión: " . $conexion->connect_error);
    }
    
    // Establecer charset
    $conexion->set_charset("utf8mb4");
    
    return $conexion;
}

// Función para verificar conexión (opcional)
function verificarConexion() {
    try {
        $conexion = conectar();
        echo "✅ Conexión exitosa a la base de datos<br>";
        $conexion->close();
        return true;
    } catch (Exception $e) {
        echo "❌ Error de conexión: " . $e->getMessage();
        return false;
    }
}
?>