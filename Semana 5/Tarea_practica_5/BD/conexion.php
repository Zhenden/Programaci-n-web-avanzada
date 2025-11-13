<?php
// config/database.php
// Uso: require_once __DIR__ . '/../config/database.php';
// Devuelve una instancia PDO para operaciones seguras con la base de datos.

class Database {
    private $host = '127.0.0.1';
    private $db_name = 'biblioteca_db';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    private $pdo;

    /* ------------------------------------------------------------------ */
    /* BD/conexion.php - wrapper mysqli para código legado que lo requiera */
    /* ------------------------------------------------------------------ */
    
    // archivo: BD/conexion.php
    // Uso: require_once __DIR__ . '/../BD/conexion.php'; $conn = conectar();
    
}
    function conectar() {
        $host = '127.0.0.1';
        $usuario = 'root';
        $contrasena = '';
        $base = 'biblioteca_db';

        $conn = mysqli_connect($host, $usuario, $contrasena, $base);
        if (!$conn) {
            die('Error de conexión MySQL: ' . mysqli_connect_error());
        }
        // Establecer charset
        mysqli_set_charset($conn, 'utf8mb4');
        return $conn;
    }

?>
