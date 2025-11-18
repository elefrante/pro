<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

class Administrativo
{
    private $conn;
    private $table_name = "administrativo";

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Obtener un administrativo por id
    public function getAdministrativoByID($id)
    {
        $id = mysqli_real_escape_string($this->conn, $id);
        $query = "SELECT * FROM $this->table_name WHERE id_admin = '$id'";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }

   public function loginAdministrativo($nombre, $pass)
{
    session_start();

    $query = "SELECT id_admin, nombre_apellidos, contrasena FROM administrativo WHERE nombre_apellidos = '$nombre'";
    $result = mysqli_query($this->conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);
        if (password_verify($pass, $admin['contrasena'])) {
            return $admin; // Ahora incluye id_admin
        } else {
            return false; // Contraseña incorrecta
        }
    } else {
        return false; // Usuario no encontrado
    }
}

}
?>
