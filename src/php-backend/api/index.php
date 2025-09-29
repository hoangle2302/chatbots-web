<?php
/**
 * API Router
 * Xử lý routing cho các API endpoints
 */

// Get the requested path
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);

// Remove /api prefix if present
$path = str_replace('/api', '', $path);

// Route to appropriate endpoint
switch ($path) {
    case '/health':
    case '/health/':
        require_once __DIR__ . '/health.php';
        break;
        
    case '/models':
    case '/models/':
        require_once __DIR__ . '/models.php';
        break;
        
    case '/chat':
    case '/chat/':
        require_once __DIR__ . '/chat.php';
        break;
        
    case '/upload':
    case '/upload/':
        require_once __DIR__ . '/upload.php';
        break;
        
    case '/register':
    case '/register/':
        require_once __DIR__ . '/register.php';
        break;
        
    default:
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Endpoint not found'
        ]);
        break;
}
?>


