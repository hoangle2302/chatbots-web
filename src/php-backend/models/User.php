<?php
/**
 * User model
 */
class User {
    private $conn;
    private $table_name = "users";
    
    public $id;
    public $username;
    public $email;
    public $display_name;
    public $password;
    public $role;
    public $is_active;
    public $failed_login_count;
    public $last_login_at;
    public $created_at;
    public $credits;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (username, email, display_name, password, role, is_active, credits) 
                  VALUES (:username, :email, :display_name, :password, :role, :is_active, :credits)";
        
        $stmt = $this->conn->prepare($query);
        
        $this->username = htmlspecialchars(strip_tags($this->username));
        if ($this->email !== null) {
            $this->email = htmlspecialchars(strip_tags($this->email));
        }
        if ($this->display_name !== null) {
            $this->display_name = htmlspecialchars(strip_tags($this->display_name));
        }
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $this->role = $this->role ?? 'user';
        $this->is_active = $this->is_active ?? 1;
        $this->credits = $this->credits ?? 0;
        
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":display_name", $this->display_name);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":is_active", $this->is_active);
        $stmt->bindParam(":credits", $this->credits);
        
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }

    public function addCredits($id, $amount) {
        $query = "UPDATE " . $this->table_name . " SET credits = credits + :amount WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":amount", $amount, PDO::PARAM_INT);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
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

    public function updateLastLoginAt() {
        $query = "UPDATE " . $this->table_name . " 
                  SET last_login_at = CURRENT_TIMESTAMP 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }

    public function countAdmins() {
        $query = "SELECT COUNT(*) AS c FROM " . $this->table_name . " WHERE role = 'admin'";
        $stmt = $this->conn->query($query);
        $row = $stmt->fetch();
        return $row ? intval($row['c']) : 0;
    }
}
?>
