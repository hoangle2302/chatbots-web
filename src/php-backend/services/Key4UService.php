<?php
/**
 * Key4U Service - Xử lý tất cả các loại API Key4U với Guzzle HTTP
 */

require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class Key4UService {
    private $config;
    private $apiKey;
    private $baseUrl = 'https://api.key4u.shop/v1';
    private $client;
    
    // Danh sách tất cả models có sẵn
    private $chatModels = [
        'gpt-4-turbo',
        'gpt-4',
        'gpt-3.5-turbo',
        'claude-3-5-sonnet',
        'gemini-pro',
        'llama-3-70b',
        'mixtral-8x7b'
    ];
    
    private $imageModels = [
        'flux-kontext-max',
        'flux-dev',
        'flux-pro',
        'dall-e-3',
        'dall-e-2',
        'midjourney',
        'stable-diffusion-xl'
    ];
    
    private $audioModels = [
        'whisper-1',
        'tts-1',
        'tts-1-hd'
    ];
    
    public function __construct() {
        $this->config = new Config();
        $this->apiKey = $this->config->getKey4UApiKey();
        
        if (!$this->apiKey) {
            throw new Exception('Key4U API key not configured');
        }
        
        // Khởi tạo Guzzle Client
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 60,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json'
            ]
        ]);
    }
    
    /**
     * Chat với AI model
     */
    public function chat($message, $model = 'gpt-4-turbo', $options = []) {
        if (!in_array($model, $this->chatModels)) {
            $model = 'gpt-4-turbo'; // Fallback
        }
        
        $requestData = [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $message
                ]
            ],
            'temperature' => $options['temperature'] ?? 0.7,
            'max_tokens' => $options['max_tokens'] ?? 1000
        ];
        
        try {
            $response = $this->client->request('POST', '/chat/completions', [
                'json' => $requestData,
                'timeout' => 30
            ]);
            
            $responseBody = $response->getBody()->getContents();
            return json_decode($responseBody, true);
            
        } catch (ClientException $e) {
            throw new Exception('Key4U API Client Error: ' . $e->getMessage());
        } catch (ServerException $e) {
            throw new Exception('Key4U API Server Error: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Key4U API Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Tạo ảnh từ text
     */
    public function generateImage($prompt, $model = 'flux-kontext-max', $options = []) {
        if (!in_array($model, $this->imageModels)) {
            $model = 'flux-kontext-max'; // Fallback
        }
        
        $requestData = [
            'model' => $model,
            'prompt' => $prompt,
            'n' => $options['n'] ?? 1,
            'size' => $options['size'] ?? '1024x1024',
            'response_format' => $options['response_format'] ?? 'url'
        ];
        
        try {
            $response = $this->client->request('POST', '/images/generations', [
                'json' => $requestData,
                'timeout' => 60
            ]);
            
            $responseBody = $response->getBody()->getContents();
            return json_decode($responseBody, true);
            
        } catch (ClientException $e) {
            throw new Exception('Key4U Image Generation Client Error: ' . $e->getMessage());
        } catch (ServerException $e) {
            throw new Exception('Key4U Image Generation Server Error: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Key4U Image Generation Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Chỉnh sửa ảnh - Sử dụng code Guzzle của bạn
     */
    public function editImage($prompt, $imagePath, $model = 'flux-kontext-max', $options = []) {
        if (!in_array($model, $this->imageModels)) {
            $model = 'flux-kontext-max'; // Fallback
        }
        
        if (!file_exists($imagePath)) {
            throw new Exception("Image file not found: $imagePath");
        }
        
        // Tạo mảng dữ liệu multipart – dựa theo tham số từ Apifox
        $multipart = [
            [
                'name' => 'model',
                'contents' => $model
            ],
            [
                'name' => 'prompt',
                'contents' => $prompt
            ],
            [
                'name' => 'n',
                'contents' => $options['n'] ?? 1
            ],
            [
                'name' => 'size',
                'contents' => $options['size'] ?? '1024x1024'
            ],
            [
                'name' => 'response_format',
                'contents' => $options['response_format'] ?? 'b64_json'
            ]
        ];
        
        // Nếu có tệp hình ảnh, thêm dữ liệu tệp vào
        if ($imagePath !== false && file_exists($imagePath)) {
            $multipart[] = [
                'name' => 'image',
                'contents' => fopen($imagePath, 'r'),
                'filename' => basename($imagePath),
                'headers' => [
                    'Content-Type' => $this->getImageMimeType($imagePath)
                ]
            ];
        } else {
            throw new Exception("Cảnh báo: Tệp hình ảnh không tồn tại hoặc đường dẫn sai: $imagePath");
        }
        
        try {
            $response = $this->client->request('POST', '/images/edits', [
                'multipart' => $multipart,
                'timeout' => 60, // Tăng thời gian timeout lên 60 giây
                'debug' => false // Tắt debug để tránh spam log
            ]);
            
            $responseBody = $response->getBody()->getContents();
            return json_decode($responseBody, true);
            
        } catch (ClientException $e) {
            throw new Exception('Key4U Image Edit Client Error: ' . $e->getMessage());
        } catch (ServerException $e) {
            throw new Exception('Key4U Image Edit Server Error: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Key4U Image Edit Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Chuyển đổi text thành giọng nói
     */
    public function textToSpeech($text, $model = 'tts-1', $options = []) {
        if (!in_array($model, $this->audioModels)) {
            $model = 'tts-1'; // Fallback
        }
        
        $requestData = [
            'model' => $model,
            'input' => $text,
            'voice' => $options['voice'] ?? 'alloy',
            'response_format' => $options['response_format'] ?? 'mp3'
        ];
        
        return $this->makeRequest('/audio/speech', $requestData);
    }
    
    /**
     * Chuyển đổi giọng nói thành text
     */
    public function speechToText($audioPath, $model = 'whisper-1', $options = []) {
        if (!in_array($model, $this->audioModels)) {
            $model = 'whisper-1'; // Fallback
        }
        
        if (!file_exists($audioPath)) {
            throw new Exception("Audio file not found: $audioPath");
        }
        
        $multipart = [
            [
                'name' => 'model',
                'contents' => $model
            ],
            [
                'name' => 'file',
                'contents' => fopen($audioPath, 'r'),
                'filename' => basename($audioPath),
                'headers' => [
                    'Content-Type' => $this->getAudioMimeType($audioPath)
                ]
            ],
            [
                'name' => 'response_format',
                'contents' => $options['response_format'] ?? 'json'
            ]
        ];
        
        return $this->makeMultipartRequest('/audio/transcriptions', $multipart);
    }
    
    /**
     * Lấy danh sách models
     */
    public function getAvailableModels() {
        return [
            'chat' => $this->chatModels,
            'image' => $this->imageModels,
            'audio' => $this->audioModels
        ];
    }
    
    
    /**
     * Lấy MIME type của ảnh
     */
    private function getImageMimeType($filePath) {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp'
        ];
        return $mimeTypes[$extension] ?? 'image/jpeg';
    }
    
    /**
     * Lấy MIME type của audio
     */
    private function getAudioMimeType($filePath) {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'm4a' => 'audio/mp4',
            'ogg' => 'audio/ogg'
        ];
        return $mimeTypes[$extension] ?? 'audio/mpeg';
    }
}
?>
