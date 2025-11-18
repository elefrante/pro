<?php
require_once 'config.php';
require_once 'administrativo.php';

$adminObj = new Administrativo($conn);
$method = $_SERVER['REQUEST_METHOD'];
$endpoint = $_SERVER['PATH_INFO'] ?? '/';
header('Content-Type: application/json');

switch ($method) {
    case 'GET':
        // Obtener administrativo por ID
        if (preg_match('/^\/administrativos\/([^\/]+)$/', $endpoint, $matches)) {
            $id = $matches[1];
            $admin = $adminObj->getAdministrativoByID($id);
            if ($admin) {
                echo json_encode($admin);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Administrativo no encontrado']);
            }
        }
        break;

    case 'POST':
        // Login administrativo
        if ($endpoint === '/login') {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!isset($data['nombre_apellidos']) || !isset($data['contrasena'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Datos incompletos']);
                exit;
            }
            $admin = $adminObj->loginAdministrativo($data['nombre_apellidos'], $data['contrasena']);
            if ($admin) {
                echo json_encode(['success' => true, 'administrativo' => $admin]);
            } else {
                http_response_code(401);
                echo json_encode(['error' => 'Credenciales incorrectas']);
            }
        }
        break;

    default:
        header('Allow: GET, POST');
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}
?>
