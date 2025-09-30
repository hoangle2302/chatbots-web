<?php
/**
 * Real Chat API Endpoint
 * Kết nối với Key4U API để gọi AI models thật
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
    if (empty($input['message'])) {
        throw new Exception('Message is required');
    }

    $message = trim($input['message']);
    $model = $input['model'] ?? 'gpt-4-turbo';
    $mode = $input['mode'] ?? 'single';
    
    // Check if ensemble mode is requested
    $isEnsemble = ($model === 'ensemble');

    // Basic validation
    if (strlen($message) < 1) {
        throw new Exception('Message cannot be empty');
    }
    
    if (strlen($message) > 2000) {
        throw new Exception('Message too long (max 2000 characters)');
    }

    // Load configuration
    require_once __DIR__ . '/../config/Config.php';
    
    // Try to get API key from config
    $config = new Config();
    $apiKey = $config->getKey4UApiKey();
    
    $response = "";
    $source = "simulated";
    $tokensUsed = 0;
    $responseTime = 0;
    $ensembleResponses = [];
    
    if ($isEnsemble) {
        // Ensemble mode - only call Qwen API
        $ensembleResult = handleQwenOnlyMode($message);
        $response = $ensembleResult['content'];
        $ensembleResponses = $ensembleResult['responses'];
        $source = 'ensemble';
        $tokensUsed = strlen($message) + strlen($response);
        $responseTime = 2; // Ensemble takes longer
    } elseif (!$apiKey || $apiKey === 'your_key4u_api_key_here') {
        // Fallback to simulated response if no API key
        $response = generateSimulatedResponse($message, $model, $mode);
        $source = 'simulated';
    } else {
        // Call real Key4U API
        try {
            $response = callKey4UAPI($message, $model, $apiKey);
            $source = 'key4u';
            $tokensUsed = strlen($message) + strlen($response);
            $responseTime = 1; // Simulate response time
        } catch (Exception $e) {
            error_log("Key4U API Error: " . $e->getMessage());
            $response = "Xin chào! Tôi là AI assistant của Thư Viện AI. Có lỗi khi kết nối với AI models thật: " . $e->getMessage() . ". Hiện tại tôi đang chạy ở chế độ mô phỏng.";
            $source = 'simulated_error';
        }
    }

    // Return success response
    http_response_code(200);
    $responseData = [
        'content' => $response,
        'model' => $isEnsemble ? 'ensemble' : $model,
        'mode' => $mode,
        'source' => $source,
        'tokens_used' => $tokensUsed,
        'response_time' => $responseTime,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    // Add ensemble responses if available
    if ($isEnsemble && !empty($ensembleResponses)) {
        $responseData['ensemble_responses'] = $ensembleResponses;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $responseData
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'code' => 'API_ERROR'
    ]);
}


/**
 * Handle Qwen Only Mode - Only call Qwen API
 */
function handleQwenOnlyMode($message) {
    $responses = [];
    $errors = [];
    
    // Try Qwen API only
    try {
        $qwenServicePath = __DIR__ . '/../services/QwenService.php';
        if (file_exists($qwenServicePath)) {
            // Suppress errors during include
            $oldErrorReporting = error_reporting(0);
            $includeResult = include_once $qwenServicePath;
            error_reporting($oldErrorReporting);
            
            if ($includeResult && class_exists('QwenService')) {
                $qwenService = new QwenService();
                $qwenResponse = $qwenService->chat($message, 'qwen3-235b-a22b');
                
                if ($qwenResponse['success']) {
                    $responses['qwen'] = [
                        'provider' => 'Qwen',
                        'model' => 'qwen3-235b-a22b',
                        'content' => $qwenResponse['content'],
                        'success' => true
                    ];
                } else {
                    $responses['qwen'] = [
                        'provider' => 'Qwen',
                        'model' => 'qwen3-235b-a22b',
                        'content' => 'Lỗi từ Qwen API',
                        'success' => false
                    ];
                }
            } else {
                $responses['qwen'] = [
                    'provider' => 'Qwen',
                    'model' => 'qwen3-235b-a22b',
                    'content' => 'Không thể load QwenService class',
                    'success' => false
                ];
            }
        } else {
            $responses['qwen'] = [
                'provider' => 'Qwen',
                'model' => 'qwen3-235b-a22b',
                'content' => 'QwenService.php không tồn tại',
                'success' => false
            ];
        }
    } catch (Exception $e) {
        $errors['qwen'] = $e->getMessage();
        $responses['qwen'] = [
            'provider' => 'Qwen',
            'model' => 'qwen3-235b-a22b',
            'content' => 'Lỗi kết nối: ' . $e->getMessage(),
            'success' => false
        ];
    } catch (Error $e) {
        $errors['qwen'] = $e->getMessage();
        $responses['qwen'] = [
            'provider' => 'Qwen',
            'model' => 'qwen3-235b-a22b',
            'content' => 'Lỗi PHP: ' . $e->getMessage(),
            'success' => false
        ];
    }
    
    // Combine responses
    $combinedResponse = "🤖 **QWEN AI RESPONSE**\n\n";
    
    foreach ($responses as $provider => $response) {
        $status = $response['success'] ? '✅' : '❌';
        $combinedResponse .= "**{$status} {$response['provider']} ({$response['model']}):**\n";
        $combinedResponse .= $response['content'] . "\n\n";
    }
    
    // Add summary
    $successCount = count(array_filter($responses, function($r) { return $r['success']; }));
    if ($successCount === 0) {
        $combinedResponse .= "⚠️ **Qwen AI gặp lỗi. Đang sử dụng response mô phỏng.**\n\n";
        $combinedResponse .= "**🤖 Qwen AI (Simulated):**\n";
        $combinedResponse .= "Xin chào! Tôi là Qwen AI. Hiện tại tôi đang gặp vấn đề kết nối, nhưng tôi vẫn có thể giúp bạn. Bạn có câu hỏi gì không?\n\n";
        $combinedResponse .= "ℹ️ **Lưu ý:** Để sử dụng Qwen AI thật, vui lòng kiểm tra cookies và API endpoint.";
    } else {
        $combinedResponse .= "✨ **Qwen AI hoạt động tốt!**";
    }
    
    return [
        'content' => $combinedResponse,
        'responses' => $responses
    ];
}

/**
 * Handle Ensemble Mode - Call both Key4U and Qwen APIs
 */
function handleEnsembleMode($message, $apiKey) {
    
    $responses = [];
    $errors = [];
    
    // Try Key4U API first
    if ($apiKey && $apiKey !== 'your_key4u_api_key_here') {
        try {
            $key4uResponse = callKey4UAPI($message, 'gpt-4-turbo', $apiKey);
            $responses['key4u'] = [
                'provider' => 'Key4U',
                'model' => 'gpt-4-turbo',
                'content' => $key4uResponse,
                'success' => true
            ];
        } catch (Exception $e) {
            $errors['key4u'] = $e->getMessage();
            $responses['key4u'] = [
                'provider' => 'Key4U',
                'model' => 'gpt-4-turbo',
                'content' => 'Lỗi kết nối: ' . $e->getMessage(),
                'success' => false
            ];
        }
    } else {
        $responses['key4u'] = [
            'provider' => 'Key4U',
            'model' => 'gpt-4-turbo',
            'content' => 'API key chưa được cấu hình',
            'success' => false
        ];
    }
    
    // Try Qwen API
    try {
        $qwenServicePath = __DIR__ . '/../services/QwenService.php';
        if (file_exists($qwenServicePath)) {
            // Suppress errors during include
            $oldErrorReporting = error_reporting(0);
            $includeResult = include_once $qwenServicePath;
            error_reporting($oldErrorReporting);
            
            if ($includeResult && class_exists('QwenService')) {
                $qwenService = new QwenService();
                $qwenResponse = $qwenService->chat($message, 'qwen3-235b-a22b');
                
                if ($qwenResponse['success']) {
                    $responses['qwen'] = [
                        'provider' => 'Qwen',
                        'model' => 'qwen3-235b-a22b',
                        'content' => $qwenResponse['content'],
                        'success' => true
                    ];
                } else {
                    $responses['qwen'] = [
                        'provider' => 'Qwen',
                        'model' => 'qwen3-235b-a22b',
                        'content' => 'Lỗi từ Qwen API',
                        'success' => false
                    ];
                }
            } else {
                $responses['qwen'] = [
                    'provider' => 'Qwen',
                    'model' => 'qwen3-235b-a22b',
                    'content' => 'Không thể load QwenService class',
                    'success' => false
                ];
            }
        } else {
            $responses['qwen'] = [
                'provider' => 'Qwen',
                'model' => 'qwen3-235b-a22b',
                'content' => 'QwenService.php không tồn tại',
                'success' => false
            ];
        }
    } catch (Exception $e) {
        $errors['qwen'] = $e->getMessage();
        $responses['qwen'] = [
            'provider' => 'Qwen',
            'model' => 'qwen3-235b-a22b',
            'content' => 'Lỗi kết nối: ' . $e->getMessage(),
            'success' => false
        ];
    } catch (Error $e) {
        $errors['qwen'] = $e->getMessage();
        $responses['qwen'] = [
            'provider' => 'Qwen',
            'model' => 'qwen3-235b-a22b',
            'content' => 'Lỗi PHP: ' . $e->getMessage(),
            'success' => false
        ];
    }
    
    // Store responses for detailed return (return as part of response)
    
    // Combine responses
    $combinedResponse = "🤖 **ENSEMBLE AI RESPONSE**\n\n";
    
    foreach ($responses as $provider => $response) {
        $status = $response['success'] ? '✅' : '❌';
        $combinedResponse .= "**{$status} {$response['provider']} ({$response['model']}):**\n";
        $combinedResponse .= $response['content'] . "\n\n";
    }
    
    // Add summary if both failed
    $successCount = count(array_filter($responses, function($r) { return $r['success']; }));
    if ($successCount === 0) {
        $combinedResponse .= "⚠️ **Tất cả AI models đều gặp lỗi. Vui lòng kiểm tra cấu hình API.**";
    } elseif ($successCount === 1) {
        $combinedResponse .= "ℹ️ **Chỉ có 1 AI model hoạt động. Vui lòng kiểm tra cấu hình cho model còn lại.**";
    } else {
        $combinedResponse .= "✨ **Cả 2 AI models đều hoạt động tốt!**";
    }
    
    return [
        'content' => $combinedResponse,
        'responses' => $responses
    ];
}

/**
 * Call Key4U API
 */
function callKey4UAPI($message, $model, $apiKey) {
    $url = 'https://api.key4u.shop/v1/chat/completions';
    
    $data = [
        'model' => $model,
        'messages' => [
            [
                'role' => 'user',
                'content' => $message
            ]
        ],
        'max_tokens' => 1000,
        'temperature' => 0.7,
        'stream' => false
    ];
    
    // Use cURL instead of file_get_contents for better HTTPS support
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For development only
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($result === false || !empty($error)) {
        throw new Exception('Failed to connect to Key4U API: ' . $error);
    }
    
    if ($httpCode !== 200) {
        throw new Exception('Key4U API returned HTTP ' . $httpCode);
    }
    
    $response = json_decode($result, true);
    
    if (!$response || !isset($response['choices'][0]['message']['content'])) {
        throw new Exception('Invalid response from Key4U API');
    }
    
    return $response['choices'][0]['message']['content'];
}

/**
 * Generate simulated response (fallback)
 */
function generateSimulatedResponse($message, $model, $mode) {
    $message = strtolower($message);
    
    // Check if user is asking about API key
    if (strpos($message, 'api key') !== false || strpos($message, 'key4u') !== false) {
        return "🔑 Để sử dụng AI models thật, bạn cần cấu hình KEY4U_API_KEY trong file config.env. Hiện tại tôi đang sử dụng response mô phỏng. Để kích hoạt AI thật, vui lòng thêm API key vào config.env và restart server.";
    }
    
    // Greeting responses
    if (strpos($message, 'xin chào') !== false || strpos($message, 'hello') !== false) {
        return "Xin chào! Tôi là AI assistant của Thư Viện AI. Hiện tại tôi đang chạy ở chế độ mô phỏng. Để kết nối với AI models thật, vui lòng cấu hình KEY4U_API_KEY trong config.env.";
    }
    
    // Default response
    return "🤖 Tôi đang chạy ở chế độ mô phỏng. Để sử dụng AI models thật (GPT-4, Claude, Gemini...), vui lòng cấu hình KEY4U_API_KEY trong file config.env. Sau đó restart server để kích hoạt kết nối thật đến các AI models.";
}
?>
