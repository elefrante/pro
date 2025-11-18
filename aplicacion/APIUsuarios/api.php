<?php
// Make sure we don't accidentally print HTML error pages to API clients
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);
ob_start(); // capture any unexpected output

require_once 'config.php';
require_once 'usuario.php';

header('Content-Type: application/json; charset=utf-8');

$usuarioObj = new Usuario($conn);
$method = $_SERVER['REQUEST_METHOD'];
// PATH_INFO may not be provided depending on server configuration. Try REQUEST_URI fallback.
$endpoint = $_SERVER['PATH_INFO'] ?? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($method) {
    case 'GET':
        if ($endpoint === '/usuarios') {
            echo json_encode($usuarioObj->getAllUsuarios());
        } elseif (preg_match('/^\/usuarios\/([^\/]+)$/', $endpoint, $matches)) {
            $usuarioCI = $matches[1];
            echo json_encode($usuarioObj->getUsuarioByCI($usuarioCI));
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if ($endpoint === '/usuarios') {
            $result = $usuarioObj->addUsuario($data);
            if ($result === true) {
                http_response_code(201);
                echo json_encode(['success' => true]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => $result]);
            }
        } elseif ($endpoint === '/login') {
            $result = $usuarioObj->loginUsuario($data['cedula'], $data['contrasena']);
            if ($result) {
                echo json_encode(['success' => true, 'usuario' => $result]);
            } else {
                http_response_code(401);
                echo json_encode(['error' => 'Credenciales incorrectas']);
            }
        }
        break;

    case 'PUT':
        if (preg_match('/^\/usuarios\/([^\/]+)$/', $endpoint, $matches)) {
            $usuarioCI = $matches[1];
            $data = json_decode(file_get_contents('php://input'), true);

            if (isset($data['perfil'])) {
                $result = $usuarioObj->updateFotoPerfil($usuarioCI, $data['perfil']);
                echo json_encode($result ? ['success' => true, 'perfil' => $result] : ['success' => false, 'error' => 'No se pudo actualizar la foto']);
            } else {
                $result = $usuarioObj->updateUsuario($usuarioCI, $data);
                echo json_encode(['success' => $result]);
            }
        }
        break;

    case 'DELETE':
        if (preg_match('/^\/usuarios\/([^\/]+)$/', $endpoint, $matches)) {
            $usuarioCI = $matches[1];
            $result = $usuarioObj->deleteUsuario($usuarioCI);
            echo json_encode(['success' => $result]);
        }
        break;

    default:
        header('Allow: GET, POST, PUT, DELETE');
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}

$raw = ob_get_clean();
// If some HTML (or other unexpected output) was produced, prefer returning a JSON error
$trim = ltrim($raw);
if ($trim !== '') {
    // If raw output looks like HTML or is not valid JSON, return a sanitized JSON error
    if (strpos($trim, '<') === 0 || @json_decode($trim) === null) {
        http_response_code(500);
        $msg = strlen($trim) > 1024 ? substr($trim, 0, 1024) . '...[truncated]' : $trim;
        echo json_encode(['success' => false, 'error' => 'Server produced unexpected output', 'raw' => $msg]);
        exit;
    } else {
        // If it was valid JSON already, just forward it
        echo $trim;
        exit;
    }
}