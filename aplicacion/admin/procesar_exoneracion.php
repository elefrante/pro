<?php
require_once "../APIAdmin/config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: exoneraciones.php");
    exit;
}

if (!isset($_POST['cedula'], $_POST['fecha'], $_POST['accion'], $_POST['adminId'])) {
    header("Location: exoneraciones.php");
    exit;
}

$cedula = mysqli_real_escape_string($conn, $_POST['cedula']);
$fecha = mysqli_real_escape_string($conn, $_POST['fecha']);
$adminId = intval($_POST['adminId']);
$accionRaw = $_POST['accion'];

// Normalizar estado que se guardará en horas_semanales
if ($accionRaw === "Exonerado") {
    $estado = "Exonerado";
} else {
    $estado = "Exoneración Denegada - Incompleto"; // ← Ahora queda como vos querés
}

// 1) Verificar que el admin existe
$sqlCheck = "SELECT id_admin, nombre_apellidos FROM administrativo WHERE id_admin = ?";
$stmt = mysqli_prepare($conn, $sqlCheck);
mysqli_stmt_bind_param($stmt, "i", $adminId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$adminRow = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$adminRow) {
    // admin no válido -> regresar sin insertar para evitar error FK
    // Podés mostrar un mensaje más amigable o registrar en log
    header("Location: exoneraciones.php");
    exit;
}

// 2) Actualizar estado en horas_semanales (prepared)
$sqlUpdate = "UPDATE horas_semanales SET estado = ? WHERE cedula = ? AND fecha = ?";
$stmt2 = mysqli_prepare($conn, $sqlUpdate);
mysqli_stmt_bind_param($stmt2, "sss", $estado, $cedula, $fecha);
mysqli_stmt_execute($stmt2);
mysqli_stmt_close($stmt2);

// 3) Insertar (o actualizar) en Exonera la relación admin<->fecha<->cedula
// Usamos el mismo formato de fecha que viene de 'fecha' (YYYY-MM-DD). 
// Suponemos que Exonera.fecha es del mismo tipo (date).
$sqlInsert = "INSERT INTO exonera (id_admin, fecha, cedula) VALUES (?, ?, ?)
              ON DUPLICATE KEY UPDATE id_admin = VALUES(id_admin)";
$stmt3 = mysqli_prepare($conn, $sqlInsert);
mysqli_stmt_bind_param($stmt3, "iss", $adminId, $fecha, $cedula);
mysqli_stmt_execute($stmt3);
mysqli_stmt_close($stmt3);

// redirigir de vuelta
header("Location: exoneraciones.php");
exit;
?>
