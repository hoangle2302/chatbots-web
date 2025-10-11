<?php
/**
 * Admin API - Quản trị người dùng và danh sách model
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../services/QwenService.php';

// Init
$database = new Database();
$db = $database->getConnection();

$userModel = new User($db);
$userService = new UserService($db);
$log = new Log($db);
$auth = new AuthMiddleware();
$qwen = new QwenService();

// Bootstrap: tạo tài khoản admin mặc định nếu chưa có admin nào
try {
    if (method_exists($userModel, 'countAdmins') && $userModel->countAdmins() === 0) {
        $userModel->username = 'admin';
        $userModel->email = null;
        $userModel->display_name = 'Administrator';
        $userModel->password = 'admin'; // sẽ được hash trong create()
        $userModel->role = 'admin';
        $userModel->is_active = 1;
        $userModel->create();
    }
} catch (Exception $e) {
    // best-effort: không làm gián đoạn API nếu bootstrap thất bại
}

// Ensure schema compatibility: thêm cột credits nếu chưa có (cho bản DB cũ)
try {
    $check = $db->query("SHOW COLUMNS FROM users LIKE 'credits'");
    if ($check && $check->rowCount() === 0) {
        $db->exec("ALTER TABLE users ADD COLUMN credits INT DEFAULT 0");
    }
} catch (Exception $e) {
    // bỏ qua nếu quyền hạn chế
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($method) {
        case 'POST':
            switch ($action) {
                case 'login':
                    handleAdminLogin($userModel, $log, $auth);
                    break;
                default:
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Action not found']);
            }
            break;

        case 'GET':
            switch ($action) {
                case 'me':
                    requireAuth($auth);
                    $token = $auth->getTokenFromRequest();
                    $u = $auth->getCurrentUser($token);
                    echo json_encode(['success' => true, 'data' => $u]);
                    break;
                case 'users':
                    requireAdmin($auth);
                    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 200;
                    // Chỉ lấy user không phải admin
                    $stmt = $db->prepare("SELECT id, username, email, display_name, role, is_active, credits, created_at FROM users WHERE role <> 'admin' ORDER BY created_at DESC LIMIT :limit");
                    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                    $stmt->execute();
                    echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
                    break;
                case 'users_all':
                    requireAdmin($auth);
                    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 200;
                    $stmt = $db->prepare("SELECT id, username, email, display_name, role, is_active, credits, created_at FROM users ORDER BY created_at DESC LIMIT :limit");
                    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                    $stmt->execute();
                    echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
                    break;
                case 'models':
                    requireAdmin($auth);
                    // Thu thập models từ Key4UService.php (phân tích tĩnh) + QwenService danh sách cơ bản
                    $all = [];
                    // 1) Lấy nhanh từ QwenService
                    $all = array_merge($all, $qwen->getAvailableModels());
                    // 2) Phân tích Key4UService.php để lấy toàn bộ models định nghĩa trong mảng
                    $k4uFile = __DIR__ . '/../services/Key4UService.php';
                    if (file_exists($k4uFile)) {
                        $src = file_get_contents($k4uFile);
                        if ($src !== false) {
                            // Bắt tất cả chuỗi trong các mảng model: '...'
                            if (preg_match_all('/\'([^\']+)\'/', $src, $m)) {
                                foreach ($m[1] as $val) {
                                    // Loại bỏ các tên file/namespace thường gặp không phải model
                                    if (strpos($val, 'php') !== false || strpos($val, 'autoloa') !== false) continue;
                                    if ($val === 'Key4U API key not configured') continue;
                                    $all[] = $val;
                                }
                            }
                        }
                    }
                    // 3) Đọc thêm từ AI_MODELS_LIST.md (các tên trong backtick)
                    $mdFile = __DIR__ . '/../../AI_MODELS_LIST.md';
                    if (file_exists($mdFile)) {
                        $content = file_get_contents($mdFile);
                        if ($content !== false && preg_match_all('/`([^`]+)`/', $content, $m2)) {
                            $all = array_merge($all, $m2[1]);
                        }
                    }
                    // Unique + sort
                    $all = array_values(array_unique($all));
                    sort($all, SORT_NATURAL | SORT_FLAG_CASE);
                    echo json_encode(['success' => true, 'data' => $all]);
                    break;
                default:
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Action not found']);
            }
            break;

        case 'PUT':
            switch ($action) {
                case 'update_user':
                    requireAdmin($auth);
                    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                    if ($id <= 0) {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => 'Invalid user id']);
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
                        echo json_encode(['success' => false, 'message' => 'No valid fields to update']);
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
                        echo json_encode(['success' => false, 'message' => 'Invalid id or amount']);
                        break;
                    }
                    $user = new User($db);
                    $ok = $user->addCredits($id, $amount);
                    echo json_encode(['success' => (bool)$ok]);
                    break;
                default:
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Action not found']);
            }
            break;

        case 'DELETE':
            switch ($action) {
                case 'delete_user':
                    requireAdmin($auth);
                    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                    if ($id <= 0) {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => 'Invalid user id']);
                        break;
                    }
                    $ok = $userService->delete($id);
                    echo json_encode(['success' => (bool)$ok]);
                    break;
                default:
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Action not found']);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    error_log('Admin API error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Internal server error']);
}

// --- Handlers & helpers ---
function handleAdminLogin($userModel, $log, $auth) {
    $input = json_decode(file_get_contents('php://input'), true);
    if (empty($input['username']) || empty($input['password'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Username and password are required']);
        return;
    }

    $username = trim($input['username']);
    $password = $input['password'];

    $userData = $userModel->getByUsername($username);
    if (!$userData || !$userData['is_active']) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        return;
    }

    if (!password_verify($password, $userData['password'])) {
        $userModel->id = $userData['id'];
        $userModel->updateFailedLogin();
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        return;
    }

    if ($userData['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Admin role required']);
        return;
    }

    $userModel->id = $userData['id'];
    $userModel->resetFailedLogin();

    $token = $auth->generateToken($userData['id'], $userData['username'], $userData['role']);

    $log->user_id = $userData['id'];
    $log->action = 'admin_login_success';
    $log->detail = "Admin logged in: {$username}";
    $log->create();

    echo json_encode([
        'success' => true,
        'message' => 'Admin login successful',
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

function requireAuth($auth) {
    if (!$auth->isAuthenticated()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }
}

function requireAdmin($auth) {
    $headers = getallheaders();
    $auth_header = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    if (strpos($auth_header, 'Bearer ') !== 0) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }
    $token = substr($auth_header, 7);
    $user = $auth->getCurrentUser($token);
    if (!$user || $user['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit();
    }
}

?>


