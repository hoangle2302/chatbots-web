<?php
/**
 * AI Tool Proxy Endpoint
 * Gửi file và prompt từ PHP backend tới dịch vụ FastAPI nội bộ
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../services/AIToolService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Only POST is supported.',
        'code' => 'METHOD_NOT_ALLOWED'
    ]);
    exit;
}

try {
    $auth = new AuthMiddleware();
    $token = $auth->getTokenFromRequest();

    if (!$token && isset($_POST['auth_token'])) {
        $token = trim((string)$_POST['auth_token']);
    }

    if (!$token) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Authentication required'
        ]);
        exit;
    }

    $currentUser = $auth->getCurrentUser($token);

    if (!$currentUser) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid token'
        ]);
        exit;
    }

    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        throw new InvalidArgumentException('Vui lòng tải lên một file hợp lệ.');
    }

    $prompt = $_POST['prompt'] ?? $_POST['user_prompt'] ?? '';
    $outputFormat = $_POST['output_format'] ?? 'auto';

    $uploadedFile = $_FILES['file'];
    $filePath = $uploadedFile['tmp_name'];
    $originalName = $uploadedFile['name'];

    $service = new AIToolService();
    $result = $service->processFile($filePath, $originalName, $prompt, $outputFormat);

    if ($result['type'] === 'file') {
        $mime = $result['mime_type'] ?? 'application/octet-stream';
        $filename = $result['filename'] ?? 'result.bin';
        $path = $result['path'];

        if (!is_readable($path)) {
            throw new RuntimeException('Không thể đọc file kết quả.');
        }

        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($path));

        $handle = fopen($path, 'rb');
        if ($handle !== false) {
            fpassthru($handle);
            fclose($handle);
        }
        @unlink($path);
        exit;
    }

    header('Content-Type: application/json; charset=utf-8');
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $result['data'] ?? $result['type'],
        'type' => $result['type']
    ]);
} catch (InvalidArgumentException $e) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'code' => 'INVALID_INPUT'
    ]);
} catch (RuntimeException $e) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(502);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'code' => 'AI_TOOL_ERROR'
    ]);
} catch (Exception $e) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error',
        'code' => 'INTERNAL_ERROR'
    ]);
}


