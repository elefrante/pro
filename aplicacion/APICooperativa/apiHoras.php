<?php
require_once 'config.php';
require_once '../APIUsuarios/usuario.php';
require_once 'horas_semanales.php';

header('Content-Type: application/json');

$horasObj = new Horas($conn);
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    // Registrar horas trabajadas o enviar solicitud
    case 'POST':
        $data = $_POST;

        if (isset($data['solicitud']) && $data['solicitud'] === "true") {
            // Enviar solicitud de exoneración
            $result = $horasObj->addSolicitud($data);
        } else {
            // Registrar horas de la semana
            $result = $horasObj->addHoras($data);
        }

        if ($result === true) {
            http_response_code(201);
            echo json_encode(['success' => true]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => $result]);
        }
        break;

    // Obtener horas por cédula
    case 'GET':
        if (isset($_GET['cedula'])) {
            $cedula = $_GET['cedula'];
            $horas = $horasObj->getHorasByCedula($cedula);
            echo json_encode($horas);
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
