<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $destino = $_POST["ruta"];
    if (!empty($destino)) {
        header("Location: $destino");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Redirección dinámica</title>
</head>
<body>
    <h2>Selecciona a dónde quieres ir:</h2>
    <form method="post">
        <select name="ruta">
            <option value="">-- Elige una opción --</option>
            <option value="/Tarea practica 3/">Tarea pratica 3</option>
            <option value="/Clase practica 3/">Clase practica 3</option>
        </select>
        <button type="submit">Ir</button>
    </form>
</body>
</html>

