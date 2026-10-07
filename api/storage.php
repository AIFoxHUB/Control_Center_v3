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

// Canonical Category Mapping (Keys to physical project directories)
$CATEGORY_MAP = [
    'brand_dna'    => '00_brand_dna',
    '00_brand_dna' => '00_brand_dna',
    'print'        => '01_print',
    '01_print'     => '01_print',
    'non_print'    => '02_non_print',
    '02_non_print' => '02_non_print',
    'commercial'   => '03_commercial',
    '03_commercial'=> '03_commercial'
];

$rawCat = $_POST['category'] ?? ($_GET['category'] ?? '02_non_print');
$folderName = $CATEGORY_MAP[$rawCat] ?? '02_non_print';
$targetDir = $BASE_STORAGE . '/' . $folderName;

if (!is_dir($targetDir)) {
    @mkdir($targetDir, 0755, true);
}

// Helper to recursively collect valid media files
function collectProjectFiles(string $dir, string $baseStorage): array {
    $results = [];
    if (!is_dir($dir)) {
        return $results;
    }

    $items = scandir($dir);
    if ($items === false) {
        return $results;
    }

    foreach ($items as $item) {
        if ($item === '.' || $item === '..' || $item === '.gitkeep') {
            continue;
        }

        $fullPath = $dir . '/' . $item;
        if (is_file($fullPath)) {
            $relPath = str_replace([$baseStorage, '\\'], ['', '/'], $fullPath);
            $relPath = ltrim($relPath, '/');
            $publicUrl = '/projects/PRJ-2026-0001_ControlCenter_v3/' . $relPath;

            $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'svg', 'gif', 'bmp', 'avif'], true);

            $results[] = [
                'name' => $item,
                'rel_path' => $relPath,
                'size' => filesize($fullPath),
                'modified' => date('c', filemtime($fullPath)),
                'url' => $publicUrl,
                'is_image' => $isImage,
                'extension' => $ext
            ];
        } elseif (is_dir($fullPath)) {
            $subResults = collectProjectFiles($fullPath, $baseStorage);
            $results = array_merge($results, $subResults);
        }
    }

    return $results;
}

// Handle File Upload (POST)
$reqMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($reqMethod === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Upload-Fehler: Code ' . $file['error']], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'svg', 'gif', 'avif', 'pdf', 'mp4', 'json', 'md', 'csv'];
    if (!in_array($ext, $allowedExtensions, true)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Dateityp nicht erlaubt: .' . $ext], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $sanitizedBase = preg_replace('/[^a-zA-Z0-9_\-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
    $newFilename = date('Ymd_His') . '_' . $sanitizedBase . '.' . $ext;
    $destination = $targetDir . '/' . $newFilename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        $relPath = str_replace([$BASE_STORAGE, '\\'], ['', '/'], $destination);
        $relPath = ltrim($relPath, '/');
        $publicUrl = '/projects/PRJ-2026-0001_ControlCenter_v3/' . $relPath;

        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'svg', 'gif', 'bmp', 'avif'], true);

        echo json_encode([
            'success' => true,
            'filename' => $newFilename,
            'category' => $folderName,
            'size' => $file['size'],
            'mime' => mime_content_type($destination) ?: 'application/octet-stream',
            'public_url' => $publicUrl,
            'rel_path' => $relPath,
            'is_image' => $isImage,
            'storage_path' => $destination
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Konnte Datei nicht im Zielverzeichnis speichern'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// Handle Listing (GET)
$files = collectProjectFiles($targetDir, $BASE_STORAGE);

// Sort files descending by modification time (newest first)
usort($files, function ($a, $b) {
    return strcmp($b['modified'], $a['modified']);
});

echo json_encode([
    'success' => true,
    'category' => $folderName,
    'folder' => $folderName,
    'count' => count($files),
    'files' => $files
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

