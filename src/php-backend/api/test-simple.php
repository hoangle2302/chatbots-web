<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

$message = $input['message'] ?? 'No message';
$model = $input['model'] ?? 'default';

echo json_encode([
    'success' => true,
    'data' => [
        'content' => "Xin chào! Tôi là AI và tôi đã nhận được tin nhắn: " . $message . " (Model: " . $model . ")",
        'model' => $model,
        'mode' => 'single',
        'tokens_used' => 10,
        'response_time' => 50,
        'ai_source' => 'test'
    ]
], JSON_UNESCAPED_UNICODE);
?>


