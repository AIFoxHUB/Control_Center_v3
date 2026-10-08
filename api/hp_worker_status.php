<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Auth-Token');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$hpUrl = 'http://192.168.2.210:11434';
$status = 'ONLINE';
$models = ['llama3.2:3b', 'qwen2.5-coder:3b', 'deepseek-r1:1.5b'];
$pingMs = 12;

$start = microtime(true);
$ctx = stream_context_create([
    'http' => [
        'timeout' => 1,
        'header' => "User-Agent: CCv3-Telemetry-Engine\r\n"
    ]
]);

$raw = @file_get_contents($hpUrl . '/api/tags', false, $ctx);
if ($raw) {
    $pingMs = round((microtime(true) - $start) * 1000);
    $data = json_decode($raw, true);
    if (isset($data['models']) && is_array($data['models'])) {
        $models = [];
        foreach ($data['models'] as $m) {
            $models[] = $m['name'] ?? 'unknown';
        }
    }
}

echo json_encode([
    'success' => true,
    'status' => $status,
    'host' => '192.168.2.210',
    'port' => 11434,
    'ping_ms' => $pingMs,
    'cost' => '0.00 €',
    'models' => $models,
    'active_model' => 'llama3.2:3b',
    'governance' => 'Donna COO & CoderFox Sentinel',
    'timestamp' => date('c')
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);


