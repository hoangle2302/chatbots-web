<?php
/**
 * 👑 API QUẢN TRỊ ADMIN
 * Quản lý người dùng và danh sách model
 */

// ===== HEADERS =====
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Xử lý preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ===== INCLUDES =====
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../services/QwenService.php';

// ===== INITIALIZATION =====
$database = new Database();
$db = $database->getConnection();
$userModel = new User($db);
$userService = new UserService($db);
$log = new Log($db);
$auth = new AuthMiddleware();
$qwen = new QwenService();

// ===== BOOTSTRAP ADMIN =====
try {
    // Tạo admin mặc định nếu chưa có
    if (method_exists($userModel, 'countAdmins') && $userModel->countAdmins() === 0) {
        $userModel->username = 'admin';
        $userModel->email = null;
        $userModel->display_name = 'Administrator';
        $userModel->password = 'admin';
        $userModel->role = 'admin';
        $userModel->is_active = 1;
        $userModel->create();
    }
} catch (Exception $e) {
    // Không làm gián đoạn API nếu bootstrap thất bại
}

// ===== ROUTING =====
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($method) {
        case 'POST':
            switch ($action) {
                case 'login':
                    handleAdminLogin($userModel, $log, $auth);
                    break;
                case 'update_credits':
                    requireAdmin($auth);
                    handleUpdateCredits($userModel, $log);
                    break;
                
                case 'modify_credits':
                    requireAdmin($auth);
                    handleModifyCredits($userModel, $log);
                    break;
                default:
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Action không tồn tại']);
            }
            break;

        case 'GET':
            switch ($action) {
                case 'users_all':
                    requireAdmin($auth);
                    $users = $userService->getAll();
                    echo json_encode(['success' => true, 'data' => $users]);
                    break;
                
                case 'models':
                    requireAdmin($auth);
                    $models = getAvailableModels($qwen);
                    echo json_encode(['success' => true, 'data' => $models]);
                    break;
                
                case 'stats':
                    requireAdmin($auth);
                    $stats = getAdminStats($userModel, $db);
                    echo json_encode(['success' => true, 'data' => $stats]);
                    break;
                
                case 'me':
                    requireAdmin($auth);
                    $token = $auth->getTokenFromRequest();
                    $user = $token ? $auth->getCurrentUser($token) : null;
                    echo json_encode(['success' => true, 'data' => $user]);
                    break;
                
                case 'admin_info':
                    requireAdmin($auth);
                    $token = $auth->getTokenFromRequest();
                    $user = $token ? $auth->getCurrentUser($token) : null;
                    
                    // Get additional admin info
                    $stats = getAdminStats($userModel, $db);
                    $adminInfo = [
                        'user' => $user,
                        'stats' => $stats,
                        'timestamp' => date('Y-m-d H:i:s')
                    ];
                    
                    echo json_encode(['success' => true, 'data' => $adminInfo]);
                    break;
                
                default:
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Action không tồn tại']);
            }
            break;

        case 'PUT':
            switch ($action) {
                case 'update_user':
                    requireAdmin($auth);
                    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                    if ($id <= 0) {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => 'ID user không hợp lệ']);
                        break;
                    }
                    $input = json_decode(file_get_contents('php://input'), true) ?? [];
                    $allowed = ['username', 'email', 'display_name', 'password', 'role', 'is_active'];
                    $data = [];
                    foreach ($allowed as $key) {
                        if (array_key_exists($key, $input)) {
                            $data[$key] = $input[$key];
                        }
                    }
                    if (empty($data)) {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => 'Không có field nào để cập nhật']);
                        break;
                    }
                    $ok = $userService->update($id, $data);
                    echo json_encode(['success' => (bool)$ok]);
                    break;
                
                case 'add_credits':
                    requireAdmin($auth);
                    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                    $input = json_decode(file_get_contents('php://input'), true) ?? [];
                    $amount = isset($input['amount']) ? intval($input['amount']) : 0;
                    if ($id <= 0 || $amount === 0) {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => 'ID hoặc số tiền không hợp lệ']);
                        break;
                    }
                    $user = new User($db);
                    $ok = $user->addCredits($id, $amount);
                    echo json_encode(['success' => (bool)$ok]);
                    break;
                
                default:
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Action không tồn tại']);
            }
            break;

        case 'DELETE':
            switch ($action) {
                case 'delete_user':
                    requireAdmin($auth);
                    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                    if ($id <= 0) {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => 'ID user không hợp lệ']);
                        break;
                    }
                    $ok = $userService->delete($id);
                    echo json_encode(['success' => (bool)$ok]);
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
} catch (Exception $e) {
    error_log('Admin API error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi server']);
}

// ===== HANDLERS =====

/**
 * Xử lý đăng nhập admin
 */
function handleAdminLogin($userModel, $log, $auth) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['username']) || empty($input['password'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Username và password là bắt buộc']);
        return;
    }

    $username = trim($input['username']);
    $password = $input['password'];

    $userData = $userModel->getByUsername($username);
    if (!$userData || !$userData['is_active']) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Thông tin đăng nhập không chính xác']);
        return;
    }

    if (!password_verify($password, $userData['password'])) {
        $userModel->id = $userData['id'];
        $userModel->updateFailedLogin();
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Thông tin đăng nhập không chính xác']);
        return;
    }

    if ($userData['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Cần quyền admin']);
        return;
    }

    $userModel->id = $userData['id'];
    $userModel->resetFailedLogin();

    $token = $auth->generateToken($userData['id'], $userData['username'], $userData['role']);

    $log->user_id = $userData['id'];
    $log->action = 'admin_login_success';
    $log->detail = "Admin đăng nhập: {$username}";
    $log->create();

    echo json_encode([
        'success' => true,
        'message' => 'Đăng nhập admin thành công',
        'data' => [
            'token' => $token,
            'user' => [
                'id' => $userData['id'],
                'username' => $userData['username'],
                'role' => $userData['role']
            ],
            'expires_in' => 24 * 60 * 60
        ]
    ]);
}

/**
 * Yêu cầu authentication
 */
function requireAuth($auth) {
    if (!$auth->isAuthenticated()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
        exit;
    }
}

/**
 * Yêu cầu quyền admin
 */
function requireAdmin($auth) {
    $token = $auth->getTokenFromRequest();

    if (!$token) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
        exit;
    }

    $user = $auth->getCurrentUser($token);

    if (!$user || ($user['role'] ?? null) !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Cần quyền admin']);
        exit;
    }
}

/**
 * Lấy danh sách models có sẵn
 */
function getAvailableModels($qwen) {
    $all = [];
    
    // Lấy từ QwenService
    $all = array_merge($all, $qwen->getAvailableModels());
    
    // Lấy từ Key4UService.php
    $k4uFile = __DIR__ . '/../services/Key4UService.php';
    if (file_exists($k4uFile)) {
        $src = file_get_contents($k4uFile);
        if ($src !== false && preg_match_all('/\'([^\']+)\'/', $src, $matches)) {
            foreach ($matches[1] as $val) {
                if (strpos($val, 'php') !== false || strpos($val, 'autoloa') !== false) continue;
                if ($val === 'Key4U API key not configured') continue;
                $all[] = $val;
            }
        }
    }
    
    // Lấy từ AI_MODELS_LIST.md
    $mdFile = __DIR__ . '/../../AI_MODELS_LIST.md';
    if (file_exists($mdFile)) {
        $content = file_get_contents($mdFile);
        if ($content !== false && preg_match_all('/`([^`]+)`/', $content, $matches)) {
            $all = array_merge($all, $matches[1]);
        }
    }
    
    // Loại bỏ trùng lặp và sắp xếp
    $all = array_values(array_unique($all));
    sort($all, SORT_NATURAL | SORT_FLAG_CASE);
    
    return $all;
}

/**
 * Lấy thống kê admin
 */
function getAdminStats($userModel, $db) {
    try {
        // Tổng số users
        $totalUsers = $userModel->count();
        
        // Users hoạt động
        $activeUsers = $userModel->count(['is_active' => 1]);
        
        // Tổng credits
        $totalCredits = 0;
        $stmt = $db->prepare("SELECT SUM(credits) as total FROM users WHERE credits IS NOT NULL");
        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $totalCredits = intval($result['total'] ?? 0);
        }
        
        // Đếm models
        $qwen = new QwenService();
        $models = $qwen->getAvailableModels();
        $totalModels = count($models);
        
        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'total_credits' => $totalCredits,
            'total_models' => $totalModels
        ];
    } catch (Exception $e) {
        error_log('Stats error: ' . $e->getMessage());
        return [
            'total_users' => 0,
            'active_users' => 0,
            'total_credits' => 0,
            'total_models' => 0
        ];
    }
}

// ===== UPDATE CREDITS =====
function handleUpdateCredits($userModel, $log) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['user_id']) || !isset($input['action']) || !isset($input['amount'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Thiếu thông tin bắt buộc']);
        return;
    }
    
    $userId = intval($input['user_id']);
    $action = $input['action'];
    $amount = intval($input['amount']);
    
    if ($userId <= 0 || $amount < 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
        return;
    }
    
    // Lấy thông tin user hiện tại
    $user = $userModel->getById($userId);
    if (!$user) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy user']);
        return;
    }
    
    $currentCredits = intval($user['credits'] ?? 0);
    $newCredits = $currentCredits;
    
    // Tính toán credits mới
    switch ($action) {
        case 'add':
            $newCredits = $currentCredits + $amount;
            break;
        case 'subtract':
            $newCredits = max(0, $currentCredits - $amount);
            break;
        case 'set':
            $newCredits = $amount;
            break;
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Action không hợp lệ']);
            return;
    }
    
    // Cập nhật credits
    $userModel->id = $userId;
    $userModel->credits = $newCredits;
    
    if ($userModel->updateCredits()) {
        // Log action
        $log->user_id = $userId;
        $log->action = 'admin_update_credits';
        $log->detail = "Admin cập nhật credits: {$action} {$amount} (từ {$currentCredits} thành {$newCredits})";
        $log->create();
        
        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật credits thành công',
            'data' => [
                'user_id' => $userId,
                'old_credits' => $currentCredits,
                'new_credits' => $newCredits,
                'action' => $action,
                'amount' => $amount
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Không thể cập nhật credits']);
    }
}

/**
 * Xử lý modify credits (add, subtract, set)
 */
function handleModifyCredits($userModel, $log) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $userId = intval($input['user_id'] ?? 0);
    $operation = $input['operation'] ?? '';
    $amount = intval($input['amount'] ?? 0);
    
    if ($userId <= 0 || $amount < 0 || !in_array($operation, ['add', 'subtract', 'set'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
        return;
    }
    
    // Lấy thông tin user hiện tại
    $user = $userModel->getById($userId);
    if (!$user) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy user']);
        return;
    }
    
    $currentCredits = intval($user['credits'] ?? 0);
    $newCredits = $currentCredits;
    
    // Tính toán credits mới
    switch ($operation) {
        case 'add':
            $newCredits = $currentCredits + $amount;
            break;
        case 'subtract':
            $newCredits = max(0, $currentCredits - $amount);
            break;
        case 'set':
            $newCredits = $amount;
            break;
    }
    
    // Cập nhật credits
    $userModel->id = $userId;
    $userModel->credits = $newCredits;
    
    if ($userModel->updateCredits()) {
        // Log action
        $log->user_id = $userId;
        $log->action = 'admin_modify_credits';
        $log->detail = "Admin modify credits: {$operation} {$amount} (từ {$currentCredits} thành {$newCredits})";
        $log->create();
        
        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật credits thành công',
            'data' => [
                'user_id' => $userId,
                'old_credits' => $currentCredits,
                'new_credits' => $newCredits,
                'operation' => $operation,
                'amount' => $amount
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Không thể cập nhật credits']);
    }
}
?>