<?php
require_once "../APICooperativa/config.php";

header("Content-Type: application/json");

$cedula = $_GET['cedula'] ?? null;

if (!$cedula) {
    echo json_encode(["error" => "Cédula no proporcionada"]);
    exit;
}

$sql = "UPDATE notificacion SET estado = 1 WHERE cedula = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $cedula);
mysqli_stmt_execute($stmt);

echo json_encode(["success" => true]);
?>