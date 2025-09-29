<?php
/**
 * JWT Authentication Middleware
 */
require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {
    private $secret_key;
    private $algorithm = 'HS256';
    
    public function __construct() {
        // Load JWT secret from environment
        $envFile = __DIR__ . '/../../config.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                    list($key, $value) = explode('=', $line, 2);
                    $_ENV[trim($key)] = trim($value);
                }
            }
        }
        
        $this->secret_key = $_ENV['JWT_SECRET'] ?? 'your-secret-key-change-this-in-production';
    }
    
    /**
     * Generate JWT token
     */
    public function generateToken($user_id, $username, $role = 'user') {
        $payload = [
            'iss' => 'thuvien-ai', // Issuer
            'aud' => 'thuvien-ai-users', // Audience
            'iat' => time(), // Issued at
            'exp' => time() + (24 * 60 * 60), // Expires in 24 hours
            'data' => [
                'user_id' => $user_id,
                'username' => $username,
                'role' => $role
            ]
        ];
        
        return JWT::encode($payload, $this->secret_key, $this->algorithm);
    }
    
    /**
     * Verify JWT token
     */
    public function verifyToken($token) {
        try {
            $decoded = JWT::decode($token, new Key($this->secret_key, $this->algorithm));
            return (array) $decoded->data;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Get current user from token
     */
    public function getCurrentUser($token) {
        $user_data = $this->verifyToken($token);
        if ($user_data) {
            return [
                'user_id' => $user_data['user_id'],
                'username' => $user_data['username'],
                'role' => $user_data['role']
            ];
        }
        return false;
    }
    
    /**
     * Check if user is authenticated
     */
    public function isAuthenticated() {
        $headers = getallheaders();
        $auth_header = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        
        if (empty($auth_header)) {
            return false;
        }
        
        if (strpos($auth_header, 'Bearer ') !== 0) {
            return false;
        }
        
        $token = substr($auth_header, 7);
        return $this->verifyToken($token);
    }
    
    /**
     * Require authentication middleware
     */
    public function requireAuth() {
        if (!$this->isAuthenticated()) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Unauthorized. Please login first.',
                'code' => 'UNAUTHORIZED'
            ]);
            exit;
        }
    }
    
    /**
     * Require admin role middleware
     */
    public function requireAdmin() {
        $this->requireAuth();
        
        $headers = getallheaders();
        $auth_header = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        $token = substr($auth_header, 7);
        $user_data = $this->getCurrentUser($token);
        
        if (!$user_data || $user_data['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Access denied. Admin role required.',
                'code' => 'FORBIDDEN'
            ]);
            exit;
        }
    }
    
    /**
     * Get token from request
     */
    public function getTokenFromRequest() {
        $headers = getallheaders();
        $auth_header = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        
        if (strpos($auth_header, 'Bearer ') === 0) {
            return substr($auth_header, 7);
        }
        
        return null;
    }
}
?>

