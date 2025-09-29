<?php
/**
 * Simple test endpoint
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

echo json_encode([
    'status' => 'ok',
    'message' => 'Server is working',
    'timestamp' => date('Y-m-d H:i:s'),
    'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
    'path' => $_SERVER['REQUEST_URI'] ?? '/'
]);
?>

