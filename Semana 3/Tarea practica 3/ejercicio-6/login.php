<?php
session_start();

// Arreglo de usuarios: clave = usuario, valor = password hash
$usuarios = [
    // password: 1234
    "admin" => '1234',
    // password: abcd
    "juan"  => 'abcd'
];

// Inicializa mensaje de error
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Filtrado básico de entrada
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    // Verificamos existencia del usuario en el arreglo
    if (isset($usuarios[$usuario]) && $usuarios[$usuario] === $password) {
        $_SESSION['usuario'] = $usuario;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Login (sin SQL)</title>
    <style>
        /*poner el flex en modo columna*/ 
        body { display:flex; justify-content:center; align-items:center; height:100vh; background:#f0f0f0; font-family:Arial, sans-serif; flex-direction: column; }
        form { background:#fff; padding:25px; border-radius:10px; box-shadow:0 0 10px #999; }
        input { display:block; margin:10px 0; padding:8px; width:220px; }
        button { padding:10px; background:#3399ff; color:#fff; border:none; border-radius:5px; cursor:pointer; }
        .error { color: red; }
        footer { margin-top: 15px; font-size: 0.9em; color: #555; text-align: center; }
    </style>
</head>
<body>
<form method="POST" action="">
    <h2>Iniciar sesión</h2>
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="password" placeholder="Contraseña" required>
    <button type="submit">Entrar</button>
    <?php if (!empty($error)) echo "<p class='error'>{$error}</p>"; ?>
</form>
<footer>
    <p>(usar admin 1234)</p>
</footer>
</body>
</html>
