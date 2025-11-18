<?php
require_once 'config.php';
require_once '../APIUsuarios/usuario.php';  // Ajusta la ruta según tu estructura
require_once 'comprobantes.php';

header('Content-Type: application/json');

$comprobanteObj = new Comprobante($conn);
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    // Crear comprobante
    case 'POST':
        $data = $_POST;
        $file = $_FILES;

        $result = $comprobanteObj->addComprobante($data, $file);
        if ($result === true) {
            http_response_code(201);
            echo json_encode(['success' => true]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => $result]);
        }
        break;

    // Obtener comprobantes por cédula
    case 'GET':
        if (isset($_GET['cedula'])) {
            $cedula = $_GET['cedula'];
            $comprobantes = $comprobanteObj->getComprobantesByCedula($cedula);
            
            echo json_encode($comprobantes);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'No se proporcionó cédula']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}
