<?php
/**
 * Simple Register API Endpoint
 * Xử lý đăng ký user mới - phiên bản đơn giản
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
    $email = $input['email'] ?? '';

    // Basic validation
    if (strlen($username) < 3) {
        throw new Exception('Username must be at least 3 characters long');
    }
    
    if (strlen($password) < 6) {
        throw new Exception('Password must be at least 6 characters long');
    }

    // For now, just simulate successful registration
    // In production, you would save to database here
    
    // Return success response
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'User registered successfully',
        'user' => [
            'id' => rand(1000, 9999), // Simulate user ID
            'username' => $username,
            'email' => $email,
            'credits' => 100, // Starting credits
            'role' => 'user',
            'created_at' => date('Y-m-d H:i:s')
        ]
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
