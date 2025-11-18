<?php
require_once "../APIAdmin/config.php";

if(isset($_POST['id'], $_POST['accion'], $_POST['adminId'])) {

    $id_comprobante = intval($_POST['id']);
    $id_admin = intval($_POST['adminId']);
    $accion = $_POST['accion'];

    // Definir nuevo estado
    if($accion === "Aceptar") {
        $estado = "Aceptado";
    } elseif($accion === "Invalido") {
        $estado = "Comprobante Inválido";
    } else {
        exit("Acción inválida");
    }

    // ---------------------------
    // 1) Obtener datos del comprobante
    // ---------------------------
    $sql_c = "SELECT cedula, nombre_comprobante, tipo, fecha_optativa FROM comprobante WHERE id_comprobante = ?";
    $stmt_c = mysqli_prepare($conn, $sql_c);
    mysqli_stmt_bind_param($stmt_c, "i", $id_comprobante);
    mysqli_stmt_execute($stmt_c);
    $result_c = mysqli_stmt_get_result($stmt_c);
    $comp = mysqli_fetch_assoc($result_c);
    mysqli_stmt_close($stmt_c);

    if (!$comp) {
        exit("Error: comprobante no encontrado");
    }

    $cedula = $comp["cedula"];
    $nombre = $comp["nombre_comprobante"];
    $tipo = $comp["tipo"];
    $fecha_optativa = $comp["fecha_optativa"];

    // ---------------------------
    // 2) Actualizar estado del comprobante
    // ---------------------------
    $sql = "UPDATE comprobante SET estado=? WHERE id_comprobante=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $estado, $id_comprobante);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // ---------------------------
    // 2.5) SI ES COMPENSATORIO Y FUE ACEPTADO, ACTUALIZAR horas_semanales
    // ---------------------------
    if ($tipo === "compensatorio" && $estado === "Aceptado" && $fecha_optativa) {
        // Calcular el lunes de esa semana para encontrar el registro en horas_semanales
        $fecha_obj = new DateTime($fecha_optativa);
        $dia_semana = $fecha_obj->format('w'); // 0=domingo, 1=lunes
        
        // Si es domingo (0) o sábado (6), ajustar para obtener el lunes de esa semana
        if ($dia_semana == 0) {
            $fecha_obj->modify('-1 day'); // Domingo -> Sábado (fin de semana anterior)
            $fecha_obj->modify('monday this week'); // Lunes de esa semana
        } elseif ($dia_semana == 6) {
            $fecha_obj->modify('monday this week'); // Lunes
        } else {
            $fecha_obj->modify('monday this week'); // Lunes de esa semana
        }
        
        $fecha_semana = $fecha_obj->format('Y-m-d');
        
        // Actualizar el estado en horas_semanales a "Compensado"
        $sql_horas = "UPDATE horas_semanales SET estado = 'Compensado' WHERE cedula = ? AND fecha = ?";
        $stmt_horas = mysqli_prepare($conn, $sql_horas);
        mysqli_stmt_bind_param($stmt_horas, "ss", $cedula, $fecha_semana);
        mysqli_stmt_execute($stmt_horas);
        mysqli_stmt_close($stmt_horas);
    }

    // ---------------------------
    // 3) Registrar gestión (lo tuyo original)
    // ---------------------------
    $sql2 = "INSERT INTO gestiona (id_admin, id_comprobante)
             VALUES (?, ?)
             ON DUPLICATE KEY UPDATE id_admin = VALUES(id_admin)";
    $stmt2 = mysqli_prepare($conn, $sql2);
    mysqli_stmt_bind_param($stmt2, "ii", $id_admin, $id_comprobante);
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);

    // ---------------------------
    // 4) CREAR NOTIFICACIÓN
    // ---------------------------
    if ($estado === "Aceptado") {
        $accion_texto = "aceptado";
    } else {
        $accion_texto = "denegado";
    }

    // Guardar tipo y fecha en descripcion_breve en formato JSON para el API pueda decodificarlo
    $datos_json = json_encode([
        "tipo" => $tipo,
        "fecha" => $fecha_optativa,
        "accion" => $accion_texto
    ]);

    $sql3 = "INSERT INTO notificacion (descripcion_breve, titulo, cedula, estado)
             VALUES (?, ?, ?, 0)";
    $stmt3 = mysqli_prepare($conn, $sql3);
    mysqli_stmt_bind_param($stmt3, "sss", $datos_json, $titulo, $cedula);
    mysqli_stmt_execute($stmt3);
    mysqli_stmt_close($stmt3);

    // ---------------------------
    // 5) Redirigir
    // ---------------------------
    header("Location: comprobantes.php");
    exit;
}
?>