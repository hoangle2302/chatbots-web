<?php
/**
 * AI Query History model
 */
class AIQueryHistory {
    private $conn;
    private $table_name = "ai_query_history";
    
    public $id;
    public $user_id;
    public $query;
    public $response;
    public $model;
    public $timestamp;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (user_id, query, response, model) 
                  VALUES (:user_id, :query, :response, :model)";
        
        $stmt = $this->conn->prepare($query);
        
        $this->user_id = $this->user_id ?? null;
        $this->query = $this->query;
        $this->response = $this->response;
        $this->model = $this->model ?? 'unknown';
        
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":query", $this->query);
        $stmt->bindParam(":response", $this->response);
        $stmt->bindParam(":model", $this->model);
        
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    public function getByUserId($user_id, $limit = 50) {
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
        $query = "SELECT h.*, u.username 
                  FROM " . $this->table_name . " h 
                  LEFT JOIN users u ON h.user_id = u.id 
                  ORDER BY h.timestamp DESC 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function deleteByUserId($user_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        return $stmt->execute();
    }
}
?>
