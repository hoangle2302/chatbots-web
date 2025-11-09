<?php
/**
 * 🔐 API XÁC THỰC NGƯỜI DÙNG
 * Xử lý đăng ký, đăng nhập, đăng xuất
 */

// ===== HEADERS =====
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Xử lý preflight OPTIONS request
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ===== INCLUDES =====
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

// ===== INITIALIZATION =====
$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$log = new Log($db);
$auth = new AuthMiddleware();

// ===== ROUTING =====
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'GET':
        switch ($action) {
            case 'profile':
                handleGetProfile($user, $auth);
                break;
            case 'me':
                handleGetMe($user, $auth);
                break;
            default:
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Action không tồn tại']);
        }
        break;
        
    case 'POST':
        switch ($action) {
            case 'register':
                handleRegister($user, $log, $auth);
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
                echo json_encode(['success' => false, 'message' => 'Action không tồn tại']);
        }
        break;
    
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method không được phép']);
}

// ===== HANDLERS =====

/**
 * Xử lý đăng ký người dùng
 */
function handleRegister($user, $log, $auth) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validation
    if (empty($input['username']) || empty($input['password'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Username và password là bắt buộc']);
        return;
    }
    
    $username = trim($input['username']);
    $password = $input['password'];
    $email = $input['email'] ?? null;
    $displayName = $input['display_name'] ?? null;
    
    // Kiểm tra username đã tồn tại
    if ($user->getByUsername($username)) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Username đã tồn tại']);
        return;
    }
    
    // Tạo user mới
    $user->username = $username;
    $user->password = $password;
    $user->email = $email;
    $user->display_name = $displayName;
    $user->role = 'user';
    $user->is_active = true;
    $user->credits = 10; // Credits mặc định
    
    if ($user->create()) {
        // Log hoạt động
        $log->user_id = $user->id;
        $log->action = 'user_register';
        $log->detail = "User đăng ký: {$username}";
        $log->create();
        
        // Lấy thông tin user đầy đủ sau khi tạo
        $userData = $user->getById($user->id);
        
        // Tạo token để tự động đăng nhập
        $token = $auth->generateToken($userData['id'], $userData['username'], $userData['role']);
        
        // Log đăng nhập tự động
        $log->user_id = $user->id;
        $log->action = 'user_login';
        $log->detail = "User tự động đăng nhập sau đăng ký: {$username}";
        $log->create();
        
        echo json_encode([
            'success' => true,
            'message' => 'Đăng ký thành công',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $userData['id'],
                    'username' => $userData['username'],
                    'email' => $userData['email'],
                    'display_name' => $userData['display_name'],
                    'role' => $userData['role'],
                    'credits' => $userData['credits'] ?? 0,
                    'last_daily_credit_at' => $userData['last_daily_credit_at'] ?? null
                ],
                'expires_in' => 24 * 60 * 60 // 24 hours
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Lỗi tạo tài khoản']);
    }
}

/**
 * Xử lý đăng nhập
 */
function handleLogin($user, $log, $auth) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validation
    if (empty($input['username']) || empty($input['password'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Username và password là bắt buộc']);
        return;
    }
    
    $username = trim($input['username']);
    $password = $input['password'];
    
    // Tìm user
    $userData = $user->getByUsername($username);
    if (!$userData || !$userData['is_active']) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Thông tin đăng nhập không chính xác']);
        return;
    }
    
    // Kiểm tra password
    if (!password_verify($password, $userData['password'])) {
        // Tăng số lần đăng nhập thất bại
        $user->id = $userData['id'];
        $user->updateFailedLogin();
        
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Thông tin đăng nhập không chính xác']);
        return;
    }
    
    // Reset failed login count
    $user->id = $userData['id'];
    $user->resetFailedLogin();
    
    // Tạo token
    $token = $auth->generateToken($userData['id'], $userData['username'], $userData['role']);

    // Cộng credit hàng ngày nếu cần
    $dailyBonus = $user->grantDailyCreditsIfNeeded($userData['id'], 5);
    if (!empty($dailyBonus['granted'])) {
        $userData['credits'] = $dailyBonus['credits'];
        $userData['last_daily_credit_at'] = $dailyBonus['last_daily_credit_at'];

        // Ghi log thưởng daily credit
        $log->user_id = $userData['id'];
        $log->action = 'daily_credit_bonus';
        $log->detail = 'Hệ thống cộng 5 credits hàng ngày khi đăng nhập.';
        $log->create();
    } elseif ($dailyBonus['credits'] !== null) {
        $userData['credits'] = $dailyBonus['credits'];
        $userData['last_daily_credit_at'] = $dailyBonus['last_daily_credit_at'];
    }
    
    // Log hoạt động
    $log->user_id = $userData['id'];
    $log->action = 'user_login';
    $log->detail = "User đăng nhập: {$username}";
    $log->create();
    
    echo json_encode([
        'success' => true,
        'message' => 'Đăng nhập thành công',
        'data' => [
            'token' => $token,
            'user' => [
                'id' => $userData['id'],
                'username' => $userData['username'],
                'email' => $userData['email'],
                'display_name' => $userData['display_name'],
                'role' => $userData['role'],
                'credits' => $userData['credits'] ?? 0,
                'last_daily_credit_at' => $userData['last_daily_credit_at'] ?? null
            ],
            'expires_in' => 24 * 60 * 60 // 24 hours
        ]
    ]);
}

/**
 * Xử lý đăng xuất
 */
function handleLogout($log, $auth) {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    
    if (strpos($authHeader, 'Bearer ') === 0) {
        $token = substr($authHeader, 7);
        $user = $auth->getCurrentUser($token);
        
        if ($user) {
            // Log hoạt động
            $log->user_id = $user['id'];
            $log->action = 'user_logout';
            $log->detail = "User đăng xuất: {$user['username']}";
            $log->create();
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Đăng xuất thành công'
    ]);
}

/**
 * Refresh token
 */
function handleRefreshToken($auth) {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    
    if (strpos($authHeader, 'Bearer ') !== 0) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Token không hợp lệ']);
        return;
    }
    
    $token = substr($authHeader, 7);
    $user = $auth->getCurrentUser($token);
    
    if (!$user) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Token không hợp lệ']);
        return;
    }
    
    // Tạo token mới
    $newToken = $auth->generateToken($user['id'], $user['username'], $user['role']);
    
    echo json_encode([
        'success' => true,
        'message' => 'Token đã được refresh',
        'data' => [
            'token' => $newToken,
            'expires_in' => 24 * 60 * 60
        ]
    ]);
}

/**
 * Kiểm tra username có tồn tại
 */
function handleCheckUsername($user) {
    $input = json_decode(file_get_contents('php://input'), true);
    $username = $input['username'] ?? '';
    
    if (empty($username)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Username là bắt buộc']);
        return;
    }
    
    $exists = $user->getByUsername($username) !== false;
    
    echo json_encode([
        'success' => true,
        'data' => [
            'username' => $username,
            'exists' => $exists,
            'available' => !$exists
        ]
    ]);
}

/**
 * Lấy thông tin profile người dùng
 */
function handleGetProfile($user, $auth) {
    $token = $auth->getTokenFromRequest();
    if (!$token) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Token không được cung cấp']);
        return;
    }
    
    $user_data = $auth->getCurrentUser($token);
    if (!$user_data) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Token không hợp lệ']);
        return;
    }
    
    $userInfo = $user->getById($user_data['user_id']);
    if (!$userInfo) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Người dùng không tồn tại']);
        return;
    }
    
    // Cộng credit hàng ngày nếu cần
    $bonus = $user->grantDailyCreditsIfNeeded($userInfo['id'], 5);
    if ($bonus['credits'] !== null) {
        $userInfo['credits'] = $bonus['credits'];
        $userInfo['last_daily_credit_at'] = $bonus['last_daily_credit_at'];
    }

    // Xóa password khỏi response
    unset($userInfo['password']);
    
    echo json_encode([
        'success' => true,
        'data' => $userInfo
    ]);
}

/**
 * Lấy thông tin người dùng hiện tại (cho admin)
 */
function handleGetMe($user, $auth) {
    $token = $auth->getTokenFromRequest();
    if (!$token) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Token không được cung cấp']);
        return;
    }
    
    $user_data = $auth->getCurrentUser($token);
    if (!$user_data) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Token không hợp lệ']);
        return;
    }
    
    $userInfo = $user->getById($user_data['user_id']);
    if (!$userInfo) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Người dùng không tồn tại']);
        return;
    }
    
    // Cộng credit hàng ngày nếu cần
    $bonus = $user->grantDailyCreditsIfNeeded($userInfo['id'], 5);
    if ($bonus['credits'] !== null) {
        $userInfo['credits'] = $bonus['credits'];
        $userInfo['last_daily_credit_at'] = $bonus['last_daily_credit_at'];
    }

    // Xóa password khỏi response
    unset($userInfo['password']);
    
    echo json_encode([
        'success' => true,
        'data' => $userInfo
    ]);
}
?>