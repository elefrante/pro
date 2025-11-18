<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

class Usuario
{
    private $conn;
    private $table_name = "usuario";

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Obtener todos los usuarios
    public function getAllUsuarios()
    {
        $query = "SELECT * FROM $this->table_name";
        $result = mysqli_query($this->conn, $query);
        $usuarios = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $usuarios[] = $row;
        }
        return $usuarios;
    }

    // Buscar usuario por cédula
    public function getUsuarioByCI($ci)
    {
        $ci = mysqli_real_escape_string($this->conn, $ci);
        $query = "SELECT * FROM $this->table_name WHERE cedula = '$ci'";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }

    // Agregar usuario
    public function addUsuario($data)
    {
        if (
            !isset($data['nombre']) || !isset($data['apellido']) || !isset($data['email']) ||
            !isset($data['contrasena']) || !isset($data['fecha_nacimiento']) || !isset($data['telefono']) ||
            !isset($data['perfil']) || !isset($data['cedula'])
        ) {
            http_response_code(400);
            return "Datos incompletos";
        }

        $cedula = mysqli_real_escape_string($this->conn, $data['cedula']);
        $nombre = mysqli_real_escape_string($this->conn, $data['nombre']);
        $apellido = mysqli_real_escape_string($this->conn, $data['apellido']);
        $email = mysqli_real_escape_string($this->conn, $data['email']);
        $fecha_nac = mysqli_real_escape_string($this->conn, $data['fecha_nacimiento']);
        $telefono = mysqli_real_escape_string($this->conn, $data['telefono']);
        $pass = password_hash($data['contrasena'], PASSWORD_DEFAULT);

        $rutaRelativa = $this->guardarImagen($data['perfil']);
        if (!$rutaRelativa) {
            http_response_code(400);
            return "Formato de imagen inválido o error al guardar";
        }

        $query = "INSERT INTO $this->table_name 
            (cedula, nombre, apellido, telefono, email, contrasena, aceptado, perfil, fecha_nacimiento, fecha_registro) 
            VALUES 
            ('$cedula', '$nombre', '$apellido', '$telefono', '$email', '$pass', 0, '$rutaRelativa', '$fecha_nac', NOW())";

        return mysqli_query($this->conn, $query);
    }

    // Login de usuario
    public function loginUsuario($cedula, $pass)
    {
        $email = mysqli_real_escape_string($this->conn, $cedula);
        $query = "SELECT * FROM $this->table_name WHERE cedula = '$cedula'";
        $result = mysqli_query($this->conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $usuario = mysqli_fetch_assoc($result);
            if (password_verify($pass, $usuario['contrasena'])) {
                return $usuario;
            }
        }
        return false;
    }

    // Actualizar email, teléfono y aceptado
public function updateUsuario($ci, $data = [])
{
    $ci = mysqli_real_escape_string($this->conn, $ci);
    $campos = [];

    if (isset($data['email'])) {
        $email = mysqli_real_escape_string($this->conn, $data['email']);
        $campos[] = "email='$email'";
    }
    if (isset($data['telefono'])) {
        $tel = mysqli_real_escape_string($this->conn, $data['telefono']);
        $campos[] = "telefono='$tel'";
    }
    if (isset($data['aceptado'])) {   // <-- Agregar esta línea
        $aceptado = intval($data['aceptado']); // asegurar que sea número
        $campos[] = "aceptado=$aceptado";
    }
    if (isset($data['unidad_habitacional'])) {
        $unidad = mysqli_real_escape_string($this->conn, $data['unidad_habitacional']);
        $campos[] = "unidad_habitacional='$unidad'";
    }

    if (empty($campos)) return false;

    $query = "UPDATE $this->table_name SET " . implode(", ", $campos) . " WHERE cedula='$ci'";
    return mysqli_query($this->conn, $query);
}


    // Actualizar foto de perfil
    public function updateFotoPerfil($ci, $img_data)
    {
        $ci = mysqli_real_escape_string($this->conn, $ci);

        // Eliminar foto anterior
        $query = "SELECT perfil FROM $this->table_name WHERE cedula = '$ci'";
        $result = mysqli_query($this->conn, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $usuario = mysqli_fetch_assoc($result);
            $fotoAnterior = __DIR__ . "/" . $usuario['perfil'];
            if (file_exists($fotoAnterior)) unlink($fotoAnterior);
        }

        $rutaRelativa = $this->guardarImagen($img_data);
        if (!$rutaRelativa) return false;

        $queryUpdate = "UPDATE $this->table_name SET perfil = '$rutaRelativa' WHERE cedula = '$ci'";
        if (mysqli_query($this->conn, $queryUpdate)) {
            return $rutaRelativa;
        }
        return false;
    }

    // Eliminar usuario
    public function deleteUsuario($ci)
    {
        $ci = mysqli_real_escape_string($this->conn, $ci);

        $queryFoto = "SELECT perfil FROM $this->table_name WHERE cedula = '$ci'";
        $result = mysqli_query($this->conn, $queryFoto);
        if ($result && mysqli_num_rows($result) > 0) {
            $usuario = mysqli_fetch_assoc($result);
            $fotoRuta = __DIR__ . "/" . $usuario['perfil'];
            if (file_exists($fotoRuta)) unlink($fotoRuta);
        }

        $query = "DELETE FROM $this->table_name WHERE cedula = '$ci'";
        return mysqli_query($this->conn, $query);
    }

    // Guardar imágenes
    private function guardarImagen($img_data)
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $img_data, $type)) {
            $img_data = substr($img_data, strpos($img_data, ',') + 1);
            $img_data = base64_decode($img_data);
            $ext = strtolower($type[1]);
            $img_name = uniqid() . "." . $ext;
            $rutaCarpeta = __DIR__ . "/fotosPerfiles/";
            $rutaCompleta = $rutaCarpeta . $img_name;
            $rutaRelativa = "fotosPerfiles/" . $img_name;

            if (!is_dir($rutaCarpeta)) mkdir($rutaCarpeta, 0777, true);

            if (file_put_contents($rutaCompleta, $img_data) === false) return false;
            return $rutaRelativa;
        }
        return false;
    }
}
