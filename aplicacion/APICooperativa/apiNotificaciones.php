<?php
require_once "../APICooperativa/config.php";

header("Content-Type: application/json");
$cedula = $_GET['cedula'] ?? null;

if (!$cedula) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT id_noti, titulo, descripcion_breve, estado
        FROM notificacion
        WHERE cedula = ?
        ORDER BY id_noti DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $cedula);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$notificaciones = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Decodificar JSON de descripcion_breve
    $datos = json_decode($row['descripcion_breve'], true);
    
    if ($datos && isset($datos['tipo']) && isset($datos['fecha'])) {
        // Generar título con el mismo formato de comprobantes.html
        $row['titulo'] = generarTitulo($datos['tipo'], $datos['fecha']);
        // Mejorar descripción según la acción
        if ($datos['accion'] === 'aceptado') {
            $row['descripcion_breve'] = "Comprobante aceptado";
        } else {
            $row['descripcion_breve'] = "Comprobante denegado";
        }
    }
    
    $notificaciones[] = $row;
}

function generarTitulo($tipo, $fecha) {
    if (!$fecha) return "Actualización de comprobante";
    
    if ($tipo === "mensual") {
        $fecha_obj = new DateTime($fecha);
        $meses_es = [
            "January" => "enero",
            "February" => "febrero",
            "March" => "marzo",
            "April" => "abril",
            "May" => "mayo",
            "June" => "junio",
            "July" => "julio",
            "August" => "agosto",
            "September" => "septiembre",
            "October" => "octubre",
            "November" => "noviembre",
            "December" => "diciembre"
        ];
        
        $mes = $fecha_obj->format("F");
        $año = $fecha_obj->format("Y");
        $mes_es = $meses_es[$mes] ?? $mes;
        return "Pago Mensual: " . ucfirst($mes_es) . " de " . $año;
    } 
    else if ($tipo === "compensatorio") {
        $fecha_obj = new DateTime($fecha);
        $inicio_semana = new DateTime($fecha_obj->format("Y-m-d"));
        $inicio_semana->modify('monday this week');
        $fin_semana = clone $inicio_semana;
        $fin_semana->modify('+6 days');
        
        $inicio_str = $inicio_semana->format("d/m/Y");
        $fin_str = $fin_semana->format("d/m/Y");
        
        return "Semana de $inicio_str - $fin_str";
    } 
    else if ($tipo === "inicial") {
        return "Pago Inicial";
    }
    
    return "Actualización de comprobante";
}

echo json_encode($notificaciones);
?>