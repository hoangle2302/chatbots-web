<?php
/**
 * Simple Login API Endpoint
 * Xử lý đăng nhập user - phiên bản đơn giản
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

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed. Only POST is supported.',
        'code' => 'METHOD_NOT_ALLOWED'
    ]);
    exit();
}

try {
    // Get request data
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }

    // Validate required fields
    if (empty($input['username'])) {
        throw new Exception('Username is required');
    }
    
    if (empty($input['password'])) {
        throw new Exception('Password is required');
    }

    $username = trim($input['username']);
    $password = $input['password'];

    // Basic validation
    if (strlen($username) < 3) {
        throw new Exception('Username must be at least 3 characters long');
    }
    
    if (strlen($password) < 6) {
        throw new Exception('Password must be at least 6 characters long');
    }

    // For now, just simulate successful login
    // In production, you would verify credentials against database here
    
    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'user' => [
            'id' => rand(1000, 9999), // Simulate user ID
            'username' => $username,
            'credits' => 150, // Simulate user credits
            'role' => 'user'
        ],
        'token' => 'simulated_token_' . time(), // Simulate JWT token
        'expires_in' => 24 * 60 * 60 // 24 hours in seconds
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'code' => 'VALIDATION_ERROR'
    ]);
}
?>
