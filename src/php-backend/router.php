<?php
/**
 * Simple Router for PHP Development Server
 */

// Set CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json; charset=utf-8');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get request URI and method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Remove .php extension from URI for routing (backward compatibility)
$uri = preg_replace('/\.php$/', '', $uri);

// Log request
error_log("Request: $method $uri");

// Route to appropriate file
if ($uri === '/api/health' || $uri === '/health') {
    require __DIR__ . '/api/health.php';
    exit;
}

// Route /api/auth.php?action=... or /api/auth/...
if (preg_match('/^\/api\/auth\/(\w+)$/', $uri, $matches)) {
    // Format: /api/auth/login
    $_GET['action'] = $matches[1];
    require __DIR__ . '/api/auth.php';
    exit;
}

if ($uri === '/api/auth') {
    // Format: /api/auth.php?action=login
    // Action will be in $_GET['action']
    require __DIR__ . '/api/auth.php';
    exit;
}

if ($uri === '/api/admin' || strpos($uri, '/api/admin/') === 0) {
    require __DIR__ . '/api/admin.php';
    exit;
}

if ($uri === '/api/chat') {
    require __DIR__ . '/api/chat-simple.php';
    exit;
}

if ($uri === '/api/chat-real') {
    require __DIR__ . '/api/chat-real.php';
    exit;
}

if ($uri === '/api/documents' || strpos($uri, '/api/documents/') === 0) {
    require __DIR__ . '/api/documents.php';
    exit;
}

if ($uri === '/api/models') {
    require __DIR__ . '/api/models.php';
    exit;
}

if ($uri === '/api/ai-tool') {
    require __DIR__ . '/api/ai-tool.php';
    exit;
}

// Route /api/user/* endpoints to index.php
if (strpos($uri, '/api/user/') === 0) {
    require __DIR__ . '/index.php';
    exit;
}

// Route /api/history to index.php
if ($uri === '/api/history') {
    require __DIR__ . '/index.php';
    exit;
}

// Check if file exists
$file = __DIR__ . $uri;
if ($uri !== '/' && file_exists($file) && is_file($file)) {
    return false; // Serve the file directly
}

// Default to index.php
if ($uri === '/' || $uri === '') {
    require __DIR__ . '/index.php';
    exit;
}

// Fallback to index.php for other routes
require __DIR__ . '/index.php';
?>
