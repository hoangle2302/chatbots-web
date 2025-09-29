<?php
/**
 * User model
 */
class User {
    private $conn;
    private $table_name = "users";
    
    public $id;
    public $username;
    public $password;
    public $role;
    public $is_active;
    public $failed_login_count;
    public $created_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (username, password, role, is_active) 
                  VALUES (:username, :password, :role, :is_active)";
        
        $stmt = $this->conn->prepare($query);
        
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $this->role = $this->role ?? 'user';
        $this->is_active = $this->is_active ?? 1;
        
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":is_active", $this->is_active);
        
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    public function getByUsername($username) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    public function verifyPassword($password) {
        return password_verify($password, $this->password);
    }
    
    public function updateFailedLogin() {
        $query = "UPDATE " . $this->table_name . " 
                  SET failed_login_count = failed_login_count + 1 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }
    
    public function resetFailedLogin() {
        $query = "UPDATE " . $this->table_name . " 
                  SET failed_login_count = 0 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }
}
?>
