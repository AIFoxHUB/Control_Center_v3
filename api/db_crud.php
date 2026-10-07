<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Auth-Token, Authorization');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$DB_PATH = dirname(__DIR__) . '/data/ccv3_master.sqlite';
@mkdir(dirname($DB_PATH), 0755, true);

try {
    $pdo = new PDO('sqlite:' . $DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Auto-create essential tables if not existing
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS projects (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            code TEXT UNIQUE NOT NULL,
            name TEXT NOT NULL,
            domain TEXT NOT NULL,
            status TEXT DEFAULT 'active',
            metadata TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS assets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            project_code TEXT,
            filename TEXT NOT NULL,
            file_type TEXT NOT NULL,
            file_size INTEGER,
            storage_path TEXT NOT NULL,
            public_url TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Datenbank-Initialisierung fehlgeschlagen: ' . $e->getMessage()]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$table = preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['table'] ?? 'projects');
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

// Allow-list for tables
$allowedTables = ['projects', 'assets', 'settings', 'logs'];
if (!in_array($table, $allowedTables, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Ungültige Tabelle']);
    exit;
}

try {
    switch ($method) {
        case 'GET':
            if ($id !== null) {
                $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE id = :id LIMIT 1");
                $stmt->execute([':id' => $id]);
                $item = $stmt->fetch();
                echo json_encode(['success' => true, 'data' => $item ?: null]);
            } else {
                $limit = min(intval($_GET['limit'] ?? 100), 500);
                $stmt = $pdo->prepare("SELECT * FROM {$table} ORDER BY id DESC LIMIT :limit");
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->execute();
                echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            }
            break;

        case 'POST':
            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true) ?? $_POST;
            if (empty($data)) {
                echo json_encode(['success' => false, 'error' => 'Keine Daten empfangen']);
                exit;
            }
            
            $cols = [];
            $placeholders = [];
            $values = [];
            foreach ($data as $k => $v) {
                $col = preg_replace('/[^a-zA-Z0-9_]/', '', $k);
                if ($col && $col !== 'id') {
                    $cols[] = $col;
                    $placeholders[] = ':' . $col;
                    $values[':' . $col] = is_array($v) ? json_encode($v, JSON_UNESCAPED_UNICODE) : $v;
                }
            }
            
            $sql = "INSERT INTO {$table} (" . implode(',', $cols) . ") VALUES (" . implode(',', $placeholders) . ")";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
            
            $newId = $pdo->lastInsertId();
            echo json_encode(['success' => true, 'id' => $newId, 'message' => 'Datensatz erfolgreich erstellt']);
            break;

        case 'DELETE':
            if ($id === null) {
                echo json_encode(['success' => false, 'error' => 'ID erforderlich für DELETE']);
                exit;
            }
            $stmt = $pdo->prepare("DELETE FROM {$table} WHERE id = :id");
            $stmt->execute([':id' => $id]);
            echo json_encode(['success' => true, 'message' => 'Datensatz gelöscht']);
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Methode nicht erlaubt']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
