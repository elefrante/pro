<?php
class Comprobante {
    private $conn;
    private $table_name = "comprobante";

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Agregar un comprobante
    public function addComprobante($data, $file) {
        if (!isset($data['cedula']) || !isset($data['tipo']) || !isset($data['fecha_optativa'])) {
            return "Datos incompletos";
        }

        $cedula = mysqli_real_escape_string($this->conn, $data['cedula']);
        $tipo = mysqli_real_escape_string($this->conn, $data['tipo']);
        $fecha_optativa = mysqli_real_escape_string($this->conn, $data['fecha_optativa']);
        $descripcion = mysqli_real_escape_string($this->conn, $data['descripcion'] ?? '');
        $estado = "En proceso";
        $fecha = date("Y-m-d");

        // Subida de archivo
        if (isset($file['comprobante']) && $file['comprobante']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . "/comprobantes/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $nombreArchivo = uniqid() . "_" . basename($file['comprobante']['name']);
            $rutaCompleta = $uploadDir . $nombreArchivo;
            $rutaRelativa = "comprobantes/" . $nombreArchivo;

            if (move_uploaded_file($file['comprobante']['tmp_name'], $rutaCompleta)) {
                $query = "INSERT INTO $this->table_name 
                          (nombre_comprobante, fecha_optativa, tipo, descripcion, estado, fecha, cedula) 
                          VALUES 
                          ('$rutaRelativa', '$fecha_optativa', '$tipo', '$descripcion', '$estado', '$fecha', '$cedula')";
                return mysqli_query($this->conn, $query);
            } else {
                return "Error al mover el archivo";
            }
        } else {
            return "No se recibió un archivo válido";
        }
    }

    // Obtener comprobantes por cédula
    public function getComprobantesByCedula($cedula) {
        $cedula = mysqli_real_escape_string($this->conn, $cedula);
        $query = "SELECT * FROM $this->table_name WHERE cedula = '$cedula' ORDER BY fecha DESC";
        $result = mysqli_query($this->conn, $query);
        $comprobantes = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $comprobantes[] = $row;
        }
        return $comprobantes;
    }
}