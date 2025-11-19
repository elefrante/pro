<?php
// Configuración de la base de datos con variables de entorno
// Compatible con Docker y variables de entorno del sistema
$hostname = getenv('DB_HOST') ?: 'db-master';
$username = getenv('DB_USER') ?: 'app_user';
$password = getenv('DB_PASSWORD') ?: 'user_pass';
$database = getenv('DB_NAME') ?: 'solucionesceleste_db';
$port = getenv('DB_PORT') ?: '3306';

// Establecer conexión a la base de datos
$conn = mysqli_connect($hostname, $username, $password, $database, $port);

// Verificar la conexión - no imprimir al cliente. Loguear y dejar $conn false para que el caller maneje el error.
if (!$conn) {
    error_log('DB connection failed: ' . mysqli_connect_error());
    error_log('DB Details - Host: ' . $hostname . ', User: ' . $username . ', DB: ' . $database);
    $conn = false;
}
?>
