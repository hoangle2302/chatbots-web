<?php
/**
 * Authentication API endpoints
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize models
$user = new User($db);
$log = new Log($db);
$auth = new AuthMiddleware();

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Route requests
switch ($method) {
    case 'POST':
        switch ($action) {
            case 'register':
                handleRegister($user, $log);
                break;
            case 'login':
                handleLogin($user, $log, $auth);
                break;
            case 'logout':
                handleLogout($log, $auth);
                break;
            case 'refresh':
                handleRefreshToken($auth);
                break;
            case 'check_username':
                handleCheckUsername($user);
                break;
            default:
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Action not found',
                    'code' => 'NOT_FOUND'
                ]);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed',
            'code' => 'METHOD_NOT_ALLOWED'
        ]);
}

/**
 * Handle user registration
 */
function handleRegister($user, $log) {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate input
        if (empty($input['username']) || empty($input['password'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Username and password are required',
                'code' => 'MISSING_FIELDS'
            ]);
            return;
        }
        
        $username = trim($input['username']);
        $password = $input['password'];
        $role = $input['role'] ?? 'user';
        $email = isset($input['email']) ? trim($input['email']) : null;
        $display_name = isset($input['display_name']) ? trim($input['display_name']) : null;
        
        // Validate username
        if (strlen($username) < 3 || strlen($username) > 80) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Username must be between 3 and 80 characters',
                'code' => 'INVALID_USERNAME'
            ]);
            return;
        }
        
        // Validate password
        if (strlen($password) < 6) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Password must be at least 6 characters long',
                'code' => 'INVALID_PASSWORD'
            ]);
            return;
        }
        
        // Check if username already exists
        $existing_user = $user->getByUsername($username);
        if ($existing_user) {
            http_response_code(409);
            echo json_encode([
                'success' => false,
                'message' => 'Username already exists',
                'code' => 'USERNAME_EXISTS'
            ]);
            return;
        }
        
        // Create new user
        $user->username = $username;
        $user->email = $email;
        $user->display_name = $display_name;
        $user->password = $password;
        // Enforce single admin
        if ($role === 'admin') {
            if ($user->countAdmins() >= 1) {
                http_response_code(409);
                echo json_encode([
                    'success' => false,
                    'message' => 'Only one admin account is allowed',
                    'code' => 'ADMIN_LIMIT_REACHED'
                ]);
                return;
            }
            $user->role = 'admin';
        } else {
            $user->role = $role;
        }
        $user->is_active = 1;
        
        if ($user->create()) {
            // Log registration
            $log->user_id = $user->id;
            $log->action = 'user_registered';
            $log->detail = "User registered: {$username}";
            $log->create();
            
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'display_name' => $user->display_name,
                    'role' => $user->role
                ]
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create user',
                'code' => 'CREATE_FAILED'
            ]);
        }
        
    } catch (Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        error_log("Registration error trace: " . $e->getTraceAsString());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error: ' . $e->getMessage(),
            'code' => 'INTERNAL_ERROR'
        ]);
    }
}

/**
 * Handle user login
 */
function handleLogin($user, $log, $auth) {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate input
        if (empty($input['username']) || empty($input['password'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Username and password are required',
                'code' => 'MISSING_FIELDS'
            ]);
            return;
        }
        
        $username = trim($input['username']);
        $password = $input['password'];
        
        // Get user by username - chỉ user đã đăng ký mới tồn tại
        $user_data = $user->getByUsername($username);
        
        if (!$user_data) {
            // User chưa đăng ký - không cho phép đăng nhập
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Tài khoản chưa được đăng ký. Vui lòng đăng ký trước.',
                'code' => 'USER_NOT_REGISTERED'
            ]);
            return;
        }
        
        // Check if user is active
        if (!$user_data['is_active']) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Account is deactivated',
                'code' => 'ACCOUNT_DEACTIVATED'
            ]);
            return;
        }
        
        // Check failed login attempts
        if ($user_data['failed_login_count'] >= 5) {
            http_response_code(429);
            echo json_encode([
                'success' => false,
                'message' => 'Too many failed login attempts. Account temporarily locked.',
                'code' => 'ACCOUNT_LOCKED'
            ]);
            return;
        }
        
        // Verify password - chỉ user đã đăng ký mới có password
        if (!password_verify($password, $user_data['password'])) {
            // Update failed login count
            $user->id = $user_data['id'];
            $user->updateFailedLogin();
            
            // Log failed login
            $log->user_id = $user_data['id'];
            $log->action = 'login_failed';
            $log->detail = "Failed login attempt for registered user: {$username}";
            $log->create();
            
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Mật khẩu không đúng. Vui lòng kiểm tra lại.',
                'code' => 'INVALID_PASSWORD'
            ]);
            return;
        }
        
        // Reset failed login count
        $user->id = $user_data['id'];
        $user->resetFailedLogin();
        $user->updateLastLoginAt();
        
        // Generate JWT token
        $token = $auth->generateToken(
            $user_data['id'],
            $user_data['username'],
            $user_data['role']
        );
        
        // Log successful login
        $log->user_id = $user_data['id'];
        $log->action = 'login_success';
        $log->detail = "User logged in: {$username}";
        $log->create();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user_data['id'],
                    'username' => $user_data['username'],
                    'email' => $user_data['email'] ?? null,
                    'display_name' => $user_data['display_name'] ?? null,
                            'role' => $user_data['role'],
                            'credits' => $user_data['credits'] ?? 0
                ],
                'expires_in' => 24 * 60 * 60 // 24 hours in seconds
            ]
        ]);
        
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        error_log("Login error trace: " . $e->getTraceAsString());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error: ' . $e->getMessage(),
            'code' => 'INTERNAL_ERROR'
        ]);
    }
}

/**
 * Handle user logout
 */
function handleLogout($log, $auth) {
    try {
        $token = $auth->getTokenFromRequest();
        if (!$token) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'No token provided',
                'code' => 'NO_TOKEN'
            ]);
            return;
        }
        
        $user_data = $auth->getCurrentUser($token);
        if (!$user_data) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid token',
                'code' => 'INVALID_TOKEN'
            ]);
            return;
        }
        
        // Log logout
        $log->user_id = $user_data['user_id'];
        $log->action = 'logout';
        $log->detail = "User logged out: {$user_data['username']}";
        $log->create();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Logout successful'
        ]);
        
    } catch (Exception $e) {
        error_log("Logout error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error',
            'code' => 'INTERNAL_ERROR'
        ]);
    }
}

/**
 * Handle token refresh
 */
function handleRefreshToken($auth) {
    try {
        $token = $auth->getTokenFromRequest();
        if (!$token) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'No token provided',
                'code' => 'NO_TOKEN'
            ]);
            return;
        }
        
        $user_data = $auth->getCurrentUser($token);
        if (!$user_data) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid token',
                'code' => 'INVALID_TOKEN'
            ]);
            return;
        }
        
        // Generate new token
        $new_token = $auth->generateToken(
            $user_data['user_id'],
            $user_data['username'],
            $user_data['role']
        );
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Token refreshed successfully',
            'data' => [
                'token' => $new_token,
                'expires_in' => 24 * 60 * 60 // 24 hours in seconds
            ]
        ]);
        
    } catch (Exception $e) {
        error_log("Token refresh error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error',
            'code' => 'INTERNAL_ERROR'
        ]);
    }
}

/**
 * Handle username check - kiểm tra username đã đăng ký chưa
 */
function handleCheckUsername($user) {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate input
        if (empty($input['username'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Username is required',
                'code' => 'MISSING_USERNAME'
            ]);
            return;
        }
        
        $username = trim($input['username']);
        
        // Validate username format
        if (strlen($username) < 3 || strlen($username) > 80) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Username must be between 3 and 80 characters',
                'code' => 'INVALID_USERNAME_FORMAT'
            ]);
            return;
        }
        
        // Check if username exists
        $user_data = $user->getByUsername($username);
        
        if ($user_data) {
            // Username đã được đăng ký
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Username đã được đăng ký',
                'data' => [
                    'username' => $username,
                    'is_registered' => true,
                    'is_active' => $user_data['is_active']
                ]
            ]);
        } else {
            // Username chưa được đăng ký
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Username chưa được đăng ký',
                'data' => [
                    'username' => $username,
                    'is_registered' => false
                ]
            ]);
        }
        
    } catch (Exception $e) {
        error_log("Username check error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error',
            'code' => 'INTERNAL_ERROR'
        ]);
    }
}
?>