<?php
/**
 * Document model
 */
class Document {
    private $conn;
    private $table_name = "documents";
    
    public $id;
    public $user_id;
    public $filename;
    public $original_name;
    public $file_path;
    public $file_type;
    public $file_size;
    public $content;
    public $created_at;
    public $updated_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Create new document record
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (user_id, filename, original_name, file_path, file_type, file_size, content) 
                  VALUES (:user_id, :filename, :original_name, :file_path, :file_type, :file_size, :content)";
        
        $stmt = $this->conn->prepare($query);
        
        $this->filename = htmlspecialchars(strip_tags($this->filename));
        $this->original_name = htmlspecialchars(strip_tags($this->original_name));
        $this->file_path = htmlspecialchars(strip_tags($this->file_path));
        $this->file_type = htmlspecialchars(strip_tags($this->file_type));
        $this->content = $this->content ? htmlspecialchars($this->content) : null;
        
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":filename", $this->filename);
        $stmt->bindParam(":original_name", $this->original_name);
        $stmt->bindParam(":file_path", $this->file_path);
        $stmt->bindParam(":file_type", $this->file_type);
        $stmt->bindParam(":file_size", $this->file_size);
        $stmt->bindParam(":content", $this->content);
        
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    /**
     * Get document by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Get documents by user ID
     */
    public function getByUserId($user_id, $limit = 50, $offset = 0) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE user_id = :user_id 
                  ORDER BY created_at DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get document count by user ID
     */
    public function getCountByUserId($user_id) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    /**
     * Search documents by user ID and keyword
     */
    public function searchByUserId($user_id, $keyword, $limit = 50, $offset = 0) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE user_id = :user_id 
                  AND (original_name LIKE :keyword OR content LIKE :keyword) 
                  ORDER BY created_at DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $search_term = "%{$keyword}%";
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":keyword", $search_term);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get documents by file type
     */
    public function getByFileType($user_id, $file_type, $limit = 50, $offset = 0) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE user_id = :user_id AND file_type = :file_type 
                  ORDER BY created_at DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":file_type", $file_type);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Update document
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET filename = :filename, 
                      original_name = :original_name, 
                      file_path = :file_path, 
                      file_type = :file_type, 
                      file_size = :file_size, 
                      content = :content,
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id = :id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->filename = htmlspecialchars(strip_tags($this->filename));
        $this->original_name = htmlspecialchars(strip_tags($this->original_name));
        $this->file_path = htmlspecialchars(strip_tags($this->file_path));
        $this->file_type = htmlspecialchars(strip_tags($this->file_type));
        $this->content = $this->content ? htmlspecialchars($this->content) : null;
        
        $stmt->bindParam(":filename", $this->filename);
        $stmt->bindParam(":original_name", $this->original_name);
        $stmt->bindParam(":file_path", $this->file_path);
        $stmt->bindParam(":file_type", $this->file_type);
        $stmt->bindParam(":file_size", $this->file_size);
        $stmt->bindParam(":content", $this->content);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":user_id", $this->user_id);
        
        return $stmt->execute();
    }
    
    /**
     * Delete document
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":user_id", $this->user_id);
        
        return $stmt->execute();
    }
    
    /**
     * Check if document belongs to user
     */
    public function belongsToUser($document_id, $user_id) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE id = :id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $document_id);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        
        return $stmt->fetch() !== false;
    }
    
    /**
     * Get file statistics by user
     */
    public function getFileStats($user_id) {
        $query = "SELECT 
                    COUNT(*) as total_files,
                    SUM(file_size) as total_size,
                    file_type,
                    COUNT(file_type) as count_by_type
                  FROM " . $this->table_name . " 
                  WHERE user_id = :user_id 
                  GROUP BY file_type
                  ORDER BY count_by_type DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
?>

