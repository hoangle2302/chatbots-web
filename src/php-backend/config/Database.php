<?php
/**
 * Database connection handler - MySQL
 */
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;
    private $conn;
    
    public function __construct() {
        // Load từ config.env
        $envFile = __DIR__ . '/../../config.env';
        // Fallback nếu không tìm thấy
        if (!file_exists($envFile)) {
            $envFile = dirname(__DIR__, 3) . '/config.env';
        }
        // Debug path
        error_log("Looking for config at: " . $envFile);
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    $_ENV[$key] = $value;
                    // Debug all config
                    error_log("Config loaded: $key = " . (empty($value) ? 'EMPTY' : 'SET'));
                }
            }
        } else {
            error_log("Config file not found: $envFile");
        }
        
        // Kiểm tra loại database
        if (isset($_ENV['DATABASE_PATH']) && !empty($_ENV['DATABASE_PATH']) && strpos($_ENV['DATABASE_PATH'], '#') !== 0) {
            // SQLite
            $this->db_name = __DIR__ . '/../../' . $_ENV['DATABASE_PATH'];
            $this->host = null;
            $this->username = null;
            $this->password = null;
            $this->port = null;
            error_log("Using SQLite: " . $this->db_name);
        } else {
            // MySQL
            $this->host = $_ENV['DB_HOST'] ?? 'localhost';
            $this->db_name = $_ENV['DB_NAME'] ?? 'thuvien_ai';
            $this->username = $_ENV['DB_USERNAME'] ?? 'root';
            $this->password = $_ENV['DB_PASSWORD'] ?? '';
            $this->port = $_ENV['DB_PORT'] ?? '3306';
            error_log("Using MySQL: " . $this->host . ":" . $this->port . "/" . $this->db_name);
            error_log("MySQL Username: " . $this->username);
            error_log("MySQL Password: " . (empty($this->password) ? 'EMPTY' : 'SET (' . strlen($this->password) . ' chars)'));
            
            // Nếu password rỗng, thử kết nối không password trước
            if (empty($this->password)) {
                error_log("Warning: MySQL password is empty, trying connection without password");
            }
        }
    }
    
    public function getConnection() {
        $this->conn = null;
        
        try {
            if ($this->host === null) {
                // SQLite
                $this->conn = new PDO("sqlite:" . $this->db_name);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } else {
                // MySQL với logic rõ ràng: chỉ fallback không mật khẩu nếu cấu hình không có mật khẩu
                $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4";
                $hadPassword = !empty($this->password);
                
                try {
                    // Thử kết nối với password
                    $this->conn = new PDO($dsn, $this->username, $this->password);
                    error_log("MySQL connected with password");
                } catch (PDOException $e) {
                    // Auto-create DB if not exists (error 1049)
                    if (strpos($e->getMessage(), '1049') !== false) {
                        $this->createMySqlDatabaseIfNotExists();
                        $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4";
                        $this->conn = new PDO($dsn, $this->username, $this->password);
                        error_log("MySQL connected with password after creating DB");
                    } else if (strpos($e->getMessage(), 'Access denied') !== false) {
                        // Chỉ thử không mật khẩu nếu ban đầu KHÔNG có mật khẩu cấu hình
                        if (!$hadPassword) {
                            error_log("MySQL connection failed, trying without password");
                            $this->password = '';
                            $this->conn = new PDO($dsn, $this->username, $this->password);
                            error_log("MySQL connected without password");
                        } else {
                            // Có mật khẩu cấu hình nhưng bị từ chối → ném lỗi ngay để người dùng sửa đúng mật khẩu
                            throw $e;
                        }
                    } else {
                        throw $e;
                    }
                }
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            }
            
            // Tạo bảng nếu chưa tồn tại
            $this->createTables();
            // Tạo sẵn admin duy nhất nếu chưa có
            $this->ensureSingleAdmin();
            
        } catch(PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
            throw new Exception("Database connection failed: " . $exception->getMessage());
        }
        
        return $this->conn;
    }
    
    private function createTables() {
        if ($this->host === null) {
            // SQLite schema
            $sql = "
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username VARCHAR(80) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                role VARCHAR(20) DEFAULT 'user',
                is_active BOOLEAN DEFAULT 1,
                failed_login_count INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            
            CREATE TABLE IF NOT EXISTS logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                action VARCHAR(100) NOT NULL,
                detail VARCHAR(500),
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id)
            );
            
            CREATE TABLE IF NOT EXISTS ai_query_history (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                query TEXT NOT NULL,
                response TEXT NOT NULL,
                model VARCHAR(50),
                tokens_used INTEGER DEFAULT 0,
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id)
            );
            
            CREATE TABLE IF NOT EXISTS documents (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                filename VARCHAR(255) NOT NULL,
                original_name VARCHAR(255) NOT NULL,
                file_path VARCHAR(500) NOT NULL,
                file_type VARCHAR(50) NOT NULL,
                file_size INTEGER NOT NULL,
                content TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id)
            );
            ";
        } else {
            // MySQL schema
            $sql = "
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(80) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                role VARCHAR(20) DEFAULT 'user',
                is_active BOOLEAN DEFAULT TRUE,
                failed_login_count INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            
            CREATE TABLE IF NOT EXISTS logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                action VARCHAR(100) NOT NULL,
                detail VARCHAR(500),
                timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            
            CREATE TABLE IF NOT EXISTS ai_query_history (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                query TEXT NOT NULL,
                response LONGTEXT NOT NULL,
                model VARCHAR(50),
                tokens_used INT DEFAULT 0,
                timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
                INDEX idx_user_timestamp (user_id, timestamp),
                INDEX idx_model (model)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            
            CREATE TABLE IF NOT EXISTS documents (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                filename VARCHAR(255) NOT NULL,
                original_name VARCHAR(255) NOT NULL,
                file_path VARCHAR(500) NOT NULL,
                file_type VARCHAR(50) NOT NULL,
                file_size BIGINT NOT NULL,
                content LONGTEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_user_created (user_id, created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
        }
        
        $this->conn->exec($sql);
    }

    private function createMySqlDatabaseIfNotExists() {
        $dsn = "mysql:host={$this->host};port={$this->port};charset=utf8mb4";
        $pdo = new PDO($dsn, $this->username, $this->password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbName = preg_replace('/[^a-zA-Z0-9_]/', '', $this->db_name);
        $sql = "CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        $pdo->exec($sql);
    }

    private function ensureSingleAdmin() {
        try {
            // Đã có admin chưa?
            $check = $this->conn->prepare("SELECT COUNT(*) AS c FROM users WHERE role = 'admin'");
            $check->execute();
            $row = $check->fetch();
            $count = $row ? intval($row['c']) : 0;
            if ($count > 0) {
                return; // đã có admin
            }
            // Tạo admin mặc định
            $username = 'admin';
            $passwordPlain = 'admin';
            $password = password_hash($passwordPlain, PASSWORD_DEFAULT);
            $role = 'admin';
            $isActive = 1;
            $stmt = $this->conn->prepare("INSERT INTO users (username, password, role, is_active) VALUES (:u, :p, :r, :a)");
            $stmt->bindParam(':u', $username);
            $stmt->bindParam(':p', $password);
            $stmt->bindParam(':r', $role);
            $stmt->bindParam(':a', $isActive, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            // Không chặn khởi động DB nếu seed lỗi; chỉ log
            error_log('Seed admin failed: ' . $e->getMessage());
        }
    }
}
?>
