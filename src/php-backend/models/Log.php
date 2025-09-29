<?php
/**
 * Log model
 */
class Log {
    private $conn;
    private $table_name = "logs";
    
    public $id;
    public $user_id;
    public $action;
    public $detail;
    public $timestamp;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (user_id, action, detail) 
                  VALUES (:user_id, :action, :detail)";
        
        $stmt = $this->conn->prepare($query);
        
        $this->user_id = $this->user_id ?? null;
        $this->action = htmlspecialchars(strip_tags($this->action));
        $this->detail = htmlspecialchars(strip_tags($this->detail));
        
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":action", $this->action);
        $stmt->bindParam(":detail", $this->detail);
        
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    public function getByUserId($user_id, $limit = 100) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE user_id = :user_id 
                  ORDER BY timestamp DESC 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function getAll($limit = 1000) {
        $query = "SELECT l.*, u.username 
                  FROM " . $this->table_name . " l 
                  LEFT JOIN users u ON l.user_id = u.id 
                  ORDER BY l.timestamp DESC 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
?>
