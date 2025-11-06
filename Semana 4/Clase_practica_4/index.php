<?php

session_start();

//redireccionar a login.php si no se ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: vista/login.php");
    exit;
}
?>