<?php
/**
 * Health Check API Endpoint
 * Kiểm tra trạng thái hệ thống - Phiên bản đơn giản
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Simple health check without database dependency
echo json_encode([
    'success' => true,
    'data' => [
        'status' => 'ok',
        'timestamp' => date('Y-m-d H:i:s'),
        'version' => '1.0.0',
        'message' => 'Thư Viện AI Backend is running',
        'php_version' => PHP_VERSION,
        'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'PHP Built-in Server'
    ]
]);
?>


