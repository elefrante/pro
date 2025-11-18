<?php
class Horas {
    private $conn;
    private $table_name = "horas_semanales";

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Convierte hh:mm a decimal (ej: 01:30 -> 1.5)
    private function horasAMinutosDecimal($horas_str) {
        if (strpos($horas_str, ":") !== false) {
            list($h, $m) = explode(":", $horas_str);
            return intval($h) + (intval($m) / 60);
        }
        return floatval($horas_str);
    }

    // Determina el lunes de la semana a partir de una fecha
    private function inicioSemana($fecha) {
    $ts = strtotime($fecha);
    $dia_sem = date('N', $ts); // 1 = lunes ... 7 = domingo

     //Si es domingo, moverlo al lunes siguiente
    if ($dia_sem == 7) {
        return date('Y-m-d', strtotime("next monday", $ts - 86400));
    }

    // Si es cualquier otro día, buscar el lunes de esa semana
    return date('Y-m-d', strtotime("monday this week", $ts));
}


    // Registrar horas de la semana
public function addHoras($data) {
    if (!isset($data['cedula']) || !isset($data['fecha']) || !isset($data['cantidad_hs'])) {
        return "Datos incompletos";
    }

    $cedula = mysqli_real_escape_string($this->conn, $data['cedula']);
    $fecha = mysqli_real_escape_string($this->conn, $data['fecha']);
    $cantidad_hs = $this->horasAMinutosDecimal($data['cantidad_hs']); 
    $solicitud = 0; // false
    $motivo = "";

    $semana = $this->inicioSemana($fecha); // lunes de la semana
    $fin_semana = date('Y-m-d', strtotime($semana . " +6 days"));
    $hoy = date('Y-m-d');

    // Buscar si ya hay registro de horas de esa semana
    $queryCheck = "SELECT * FROM $this->table_name 
                   WHERE cedula='$cedula' AND fecha='$semana' AND solicitud=0";
    $res = mysqli_query($this->conn, $queryCheck);

    if(mysqli_num_rows($res) > 0){
        // Sumar horas existentes
        $row = mysqli_fetch_assoc($res);
        $total = $row['cantidad_hs'] + $cantidad_hs;
    } else {
        $total = $cantidad_hs;
    }

    // Calcular estado según las reglas
    if ($total >= 21) {
        $estado = "Completado";
    } elseif ($hoy > $fin_semana) {
        $estado = "A compensar";
    } else {
        $estado = "Incompleto";
    }

    if(mysqli_num_rows($res) > 0){
        $query = "UPDATE $this->table_name 
                  SET cantidad_hs=$total, estado='$estado' 
                  WHERE cedula='$cedula' AND fecha='$semana' AND solicitud=0";
    } else {
        $query = "INSERT INTO $this->table_name (fecha, solicitud, cantidad_hs, motivo, estado, cedula)
                  VALUES ('$semana', $solicitud, $total, '$motivo', '$estado', '$cedula')";
    }

    return mysqli_query($this->conn, $query) ? true : mysqli_error($this->conn);
}


    // Enviar solicitud de exoneración de horas restantes
public function addSolicitud($data) {
    if (!isset($data['cedula']) || !isset($data['fecha']) || !isset($data['motivo'])) {
        return "Datos incompletos";
    }

    $cedula = mysqli_real_escape_string($this->conn, $data['cedula']);
    $fecha = mysqli_real_escape_string($this->conn, $data['fecha']);
    $motivo = mysqli_real_escape_string($this->conn, $data['motivo']);
    $estado = "En proceso";
    $solicitud = 1;

    $semana = $this->inicioSemana($fecha);

    // Primero verificamos si ya hay un registro (con o sin horas) de esta semana
    $queryCheck = "SELECT * FROM $this->table_name 
                   WHERE cedula='$cedula' AND fecha='$semana' AND solicitud=0";
    $res = mysqli_query($this->conn, $queryCheck);

    if (mysqli_num_rows($res) > 0) {
        // Si existe registro de horas, solo actualizamos esa fila
        $query = "UPDATE $this->table_name 
                  SET solicitud=$solicitud, motivo='$motivo', estado='$estado' 
                  WHERE cedula='$cedula' AND fecha='$semana' AND solicitud=0";
    } else {
        // Si no hay registro, insertamos uno nuevo
        $cantidad_hs = 0;
        $query = "INSERT INTO $this->table_name (fecha, solicitud, cantidad_hs, motivo, estado, cedula)
                  VALUES ('$semana', $solicitud, $cantidad_hs, '$motivo', '$estado', '$cedula')";
    }

    return mysqli_query($this->conn, $query) ? true : mysqli_error($this->conn);
}


    // Obtener todas las semanas de un usuario
    public function getHorasByCedula($cedula) {
        $cedula = mysqli_real_escape_string($this->conn, $cedula);
        $query = "SELECT * FROM $this->table_name 
                  WHERE cedula = '$cedula' ORDER BY fecha DESC";
        $result = mysqli_query($this->conn, $query);

        $horas = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $horas[] = $row;
        }

        return $horas;
    }
}
