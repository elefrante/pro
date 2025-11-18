<?php
class Reporte {
    private $conn;
    private $table_name = "reporte";

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Obtener todos los reportes
    public function getReportes() {
    $query = "SELECT id_reporte, titulo, descripcion, nombre_arch, fecha 
              FROM $this->table_name 
              ORDER BY fecha DESC";
    $result = mysqli_query($this->conn, $query);

    $reportes = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $reportes[] = $row;
    }
    return $reportes;
}


    // Obtener archivo del reporte por id
    public function getArchivo($id_reporte) {
        $id_reporte = mysqli_real_escape_string($this->conn, $id_reporte);
        $query = "SELECT nombre_arch FROM $this->table_name WHERE id_reporte='$id_reporte'";
        $result = mysqli_query($this->conn, $query);

        if ($row = mysqli_fetch_assoc($result)) {
            return $row['nombre_arch'];
        }
        return null;
    }
}
