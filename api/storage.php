<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Auth-Token');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$BASE_STORAGE = dirname(__DIR__);
$ALLOWED_CATEGORIES = [
    'print' => $BASE_STORAGE . '/01_print',
    'non_print' => $BASE_STORAGE . '/02_non_print',
    'commercial' => $BASE_STORAGE . '/03_commercial',
    'brand_dna' => $BASE_STORAGE . '/00_brand_dna'
];

$category = $_POST['category'] ?? ($_GET['category'] ?? 'non_print');
$targetDir = $ALLOWED_CATEGORIES[$category] ?? $ALLOWED_CATEGORIES['non_print'];
@mkdir($targetDir, 0755, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Upload-Fehler: Code ' . $file['error']]);
        exit;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'svg', 'pdf', 'mp4', 'json', 'md', 'csv'];
    if (!in_array($ext, $allowedExtensions, true)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Dateityp nicht erlaubt: .' . $ext]);
        exit;
    }

    $sanitizedBase = preg_replace('/[^a-zA-Z0-9_\-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
    $newFilename = date('Ymd_His') . '_' . $sanitizedBase . '.' . $ext;
    $destination = $targetDir . '/' . $newFilename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        $relPath = str_replace(dirname(__DIR__), '', $destination);
        $publicUrl = '/projects/PRJ-2026-0001_ControlCenter_v3' . str_replace('\\', '/', $relPath);
        
        echo json_encode([
            'success' => true,
            'filename' => $newFilename,
            'category' => $category,
            'size' => $file['size'],
            'mime' => mime_content_type($destination) ?: 'application/octet-stream',
            'public_url' => $publicUrl,
            'storage_path' => $destination
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Konnte Datei nicht im Zielverzeichnis speichern']);
    }
    exit;
}

// GET: List files in category
$files = [];
if (is_dir($targetDir)) {
    foreach (scandir($targetDir) as $f) {
        if ($f !== '.' && $f !== '..' && is_file($targetDir . '/' . $f)) {
            $files[] = [
                'name' => $f,
                'size' => filesize($targetDir . '/' . $f),
                'modified' => date('c', filemtime($targetDir . '/' . $f)),
                'url' => '/projects/PRJ-2026-0001_ControlCenter_v3/' . $category . '/' . $f
            ];
        }
    }
}

echo json_encode([
    'success' => true,
    'category' => $category,
    'count' => count($files),
    'files' => $files
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
