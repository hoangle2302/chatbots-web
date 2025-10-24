<?php
/**
 * 🤖 MODEL LỊCH SỬ AI QUERY
 * Quản lý lịch sử tương tác với AI
 */
class AIQueryHistory {
    private $conn;
    private $table_name = "ai_query_history";
    
    // Properties
    public $id;
    public $user_id;
    public $model;
    public $prompt;
    public $response;
    public $tokens_used;
    public $created_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Tạo record mới
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (user_id, model, prompt, response, tokens_used) 
                  VALUES (:user_id, :model, :prompt, :response, :tokens_used)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize input
        $this->model = htmlspecialchars(strip_tags($this->model));
        $this->prompt = htmlspecialchars(strip_tags($this->prompt));
        $this->response = htmlspecialchars(strip_tags($this->response));
        $this->tokens_used = $this->tokens_used ?? 0;
        
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":model", $this->model);
        $stmt->bindParam(":prompt", $this->prompt);
        $stmt->bindParam(":response", $this->response);
        $stmt->bindParam(":tokens_used", $this->tokens_used);
        
        return $stmt->execute();
    }
    
    /**
     * Lấy lịch sử theo user ID
     */
    public function getByUserId($userId, $limit = 50) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE user_id = :user_id 
                  ORDER BY created_at DESC 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy lịch sử theo model
     */
    public function getByModel($model, $limit = 50) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE model = :model 
                  ORDER BY created_at DESC 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":model", $model);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy tất cả lịch sử
     */
    public function getAll($limit = 100, $offset = 0) {
        $query = "SELECT h.*, u.username 
                  FROM " . $this->table_name . " h
                  LEFT JOIN users u ON h.user_id = u.id
                  ORDER BY h.created_at DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Thống kê tokens theo user
     */
    public function getTokenStatsByUser($userId) {
        $query = "SELECT 
                    COUNT(*) as total_queries,
                    SUM(tokens_used) as total_tokens,
                    AVG(tokens_used) as avg_tokens,
                    model
                  FROM " . $this->table_name . " 
                  WHERE user_id = :user_id 
                  GROUP BY model";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Xóa lịch sử cũ
     */
    public function deleteOldHistory($days = 30) {
        $query = "DELETE FROM " . $this->table_name . " 
                  WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":days", $days, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>