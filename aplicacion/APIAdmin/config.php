<?php
//Cambiar para implementar base de datos - Sistemas Operativos con variables de entorno
// Configuración de la base de datos
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'soluciones_celeste';
$port = '3306';
// Establecer conexión a la base de datos
$conn = mysqli_connect($hostname, $username, $password, $database, $port);
// Verificar la conexión - no imprimir al cliente. Loguear y dejar $conn false para que el caller maneje el error.
if (!$conn) {
	error_log('DB connection failed: ' . mysqli_connect_error());
	$conn = false;
}
?>