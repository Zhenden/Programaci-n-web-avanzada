<?php
// 📦 Parámetros de conexión
$host = '127.0.0.1';        // Servidor (puede ser IP o dominio)
$usuario = 'root';          // Usuario de MySQL
$contraseña = '';           // Contraseña del usuario
$base_datos = '01_calif'; // Nombre de la base de datos

// 🚪 Crear conexión
$conexion = new mysqli($host, $usuario, $contraseña, $base_datos);

// 🛡️ Verificar conexión
if ($conexion->connect_error) {
    die("❌ Error de conexión: " . $conexion->connect_error);
}

// ✅ Conexión exitosa
echo "✅ Conexión exitosa a la base de datos";
?>

