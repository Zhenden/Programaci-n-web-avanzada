<?php
// Simple diagnostic script to check DB connectivity and password verification
error_reporting(E_ALL);
ini_set('display_errors', 1);

$email = $_GET['email'] ?? 'admin@hotel.com';
$password = $_GET['password'] ?? 'admin123';

echo "<h2>Debug Login</h2>";
echo "<p>Comprobando usuario: <strong>" . htmlspecialchars($email) . "</strong> con contraseña de prueba: <strong>" . htmlspecialchars($password) . "</strong></p>";

$conn = new mysqli("localhost", "root", "", "reservas_hotel");
if ($conn->connect_error) {
    echo "<p style='color:red'>Error conectando a la base de datos: " . htmlspecialchars($conn->connect_error) . "</p>";
    exit;
}

$stmt = $conn->prepare("SELECT id, name, email, password, role_id FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result();
$user = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$user) {
    echo "<p style='color:orange'>Usuario no encontrado con ese email.</p>";
    exit;
}

echo "<p>Usuario encontrado: <strong>" . htmlspecialchars($user['name']) . "</strong></p>";
echo "<p>Email: " . htmlspecialchars($user['email']) . "</p>";
echo "<p>Role ID: " . htmlspecialchars($user['role_id']) . "</p>";
echo "<p>Hash almacenado: <code>" . htmlspecialchars($user['password']) . "</code></p>";

$verified = password_verify($password, $user['password']);
echo "<p>password_verify para la contraseña de prueba: <strong>" . ($verified ? 'OK' : 'NO') . "</strong></p>";

if (!$verified) {
    echo "<p style='color:red'>La verificación falló. Prueba con la contraseña real del usuario o registra un nuevo usuario para probar.</p>";
}

echo "<p>Consejos: si password_verify devuelve NO, asegúrate de que la contraseña en la BD esté hasheada con password_hash().</p>";

?>
