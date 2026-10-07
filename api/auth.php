<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Auth-Token, Authorization');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

session_start();

$MASTER_KEY = 'fuchs_power_2026';
$SESSION_LIFETIME = 86400 * 30; // 30 Tage

function jsonResponse(bool $success, array $data = [], string $error = '', int $status = 200): void {
    http_response_code($status);
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'error' => $error,
        'timestamp' => date('c')
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$action = $_GET['action'] ?? ($_POST['action'] ?? 'status');

// 1. Check Auth Token Helper
function verifyAuthToken(string $token): bool {
    global $MASTER_KEY;
    if ($token === $MASTER_KEY) return true;
    if (isset($_SESSION['ccv3_auth']) && $_SESSION['ccv3_auth'] === true) return true;
    return false;
}

$receivedToken = $_SERVER['HTTP_X_AUTH_TOKEN'] ?? ($_GET['token'] ?? ($_POST['token'] ?? ''));

// Action Router
switch ($action) {
    case 'login':
        $raw = file_get_contents('php://input');
        $body = json_decode($raw, true) ?? $_POST;
        $password = $body['password'] ?? '';
        
        if ($password === $MASTER_KEY || $password === 'itsigns2026') {
            $_SESSION['ccv3_auth'] = true;
            $_SESSION['user'] = 'patrice';
            $_SESSION['role'] = 'admin';
            
            $sessionToken = hash('sha256', $password . '_ccv3_salt_' . date('Y-m-d'));
            
            jsonResponse(true, [
                'token' => $sessionToken,
                'user' => [
                    'username' => 'patrice',
                    'role' => 'admin',
                    'auth_level' => 'executive'
                ],
                'message' => 'Erfolgreich angemeldet.'
            ]);
        } else {
            jsonResponse(false, [], 'Ungültiges Passwort.', 401);
        }
        break;

    case 'logout':
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        jsonResponse(true, ['message' => 'Erfolgreich abgemeldet.']);
        break;

    case 'verify':
    case 'status':
    default:
        $isAuth = verifyAuthToken($receivedToken);
        jsonResponse($isAuth, [
            'authenticated' => $isAuth,
            'user' => $isAuth ? ($_SESSION['user'] ?? 'executive') : null,
            'role' => $isAuth ? ($_SESSION['role'] ?? 'admin') : null
        ]);
        break;
}
