<?php
require_once 'config.php';
require_once 'reportes.php';

header('Content-Type: application/json');

$reporteObj = new Reporte($conn);
$method = $_SERVER['REQUEST_METHOD'];

switch($method) {

    // Obtener todos los reportes
    case 'GET':
        if (isset($_GET['archivo']) && isset($_GET['id'])) {
            // Descargar archivo
            $id = $_GET['id'];
            $archivo = $reporteObj->getArchivo($id);

            if ($archivo && file_exists(__DIR__ . "/reportes/" . $archivo)) {
                // Enviar el archivo
                $ruta = __DIR__ . "/reportes/" . $archivo;
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($ruta) . '"');
                readfile($ruta);
                exit;
            } else {
                echo json_encode(["error" => "Archivo no encontrado"]);
            }
        } 
        else {
            // Listar reportes
            $reportes = $reporteObj->getReportes();
            echo json_encode($reportes);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
