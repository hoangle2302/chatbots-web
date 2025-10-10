<?php
/**
 * JWT Authentication Middleware (no external dependencies)
 */

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
        
        // Build JWT without external libs (HS256)
        $header = [ 'typ' => 'JWT', 'alg' => 'HS256' ];
        $segments = [];
        $segments[] = $this->base64UrlEncode(json_encode($header));
        $segments[] = $this->base64UrlEncode(json_encode($payload));
        $signingInput = implode('.', $segments);
        $signature = hash_hmac('sha256', $signingInput, $this->secret_key, true);
        $segments[] = $this->base64UrlEncode($signature);
        return implode('.', $segments);
    }
    
    /**
     * Verify JWT token
     */
    public function verifyToken($token) {
        try {
            $parts = explode('.', $token);
            if (count($parts) !== 3) return false;
            list($h64, $p64, $s64) = $parts;
            $signingInput = $h64 . '.' . $p64;
            $signature = $this->base64UrlDecode($s64);
            $calc = hash_hmac('sha256', $signingInput, $this->secret_key, true);
            if (!hash_equals($calc, $signature)) return false;
            $payload = json_decode($this->base64UrlDecode($p64), true);
            if (!$payload) return false;
            if (isset($payload['exp']) && time() >= $payload['exp']) return false;
            return isset($payload['data']) ? (array)$payload['data'] : false;
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

    private function base64UrlEncode($data) {
        $b64 = base64_encode($data);
        return rtrim(strtr($b64, '+/', '-_'), '=');
    }

    private function base64UrlDecode($data) {
        $b64 = strtr($data, '-_', '+/');
        return base64_decode($b64 . str_repeat('=', (4 - strlen($b64) % 4) % 4));
    }
}
?>

