<?php
class User {
    
    // Register new user
    public static function register($email, $password, $firstName, $lastName, $phone = '', $role = 'user') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (email, password_hash, first_name, last_name, phone, role) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        return Database::query($sql, [$email, $hashedPassword, $firstName, $lastName, $phone, $role]);
    }
    
    // Login user - ✅ FIXED: Explicit columns including role
   // In app/models/User.php
    public static function login($email, $password) {
    try {
        $db = Database::getConnection();
        
        $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && isset($user['password_hash'])) {
            if (password_verify($password, $user['password_hash'])) {
                // Remove sensitive data
                unset($user['password_hash']);
                
                // Ensure all required fields exist
                $user['first_name'] = $user['first_name'] ?? '';
                $user['last_name'] = $user['last_name'] ?? '';
                $user['role'] = $user['role'] ?? 'user';
                
                return $user;
            }
        }
        
        return false;
        
    } catch (Exception $e) {
        error_log("User::login() error: " . $e->getMessage());
        return false;
    }
}
    
    // Check if email exists
    public static function emailExists($email) {
        $sql = "SELECT id FROM users WHERE email = ?";
        return Database::fetch($sql, [$email]);
    }
    
    // Get user by ID - ✅ FIXED: Includes role
    public static function getById($id) {
        $sql = "SELECT id, first_name, last_name, email, phone, role, created_at 
                FROM users WHERE id = ?";
        return Database::fetch($sql, [$id]);
    }

    // Create user with security questions
    public static function create($firstName, $lastName, $email, $password, $securityQuestion, $securityAnswer, $role = 'user') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $hashedAnswer = password_hash(strtolower(trim($securityAnswer)), PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (first_name, last_name, email, password_hash, security_question, security_answer, role, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        
        return Database::query($sql, [
            $firstName, 
            $lastName, 
            $email, 
            $hashedPassword, 
            $securityQuestion, 
            $hashedAnswer,
            $role
        ]);
    }

    // Update password
    public static function updatePassword($email, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password_hash = ? WHERE email = ?";
        return Database::query($sql, [$hashedPassword, $email]);
    }

    // Find user by email - ✅ FIXED: Includes role
    public static function findByEmail($email) {
        $sql = "SELECT id, email, first_name, last_name, password_hash, role, phone, created_at 
                FROM users WHERE email = ?";
        return Database::fetch($sql, [$email]);
    }
    
    // Get security question
    public static function getSecurityQuestion($email) {
        $sql = "SELECT security_question FROM users WHERE email = ?";
        $user = Database::fetch($sql, [$email]);
        return $user ? $user['security_question'] : null;
    }
    
    // Verify security answer
    public static function verifySecurityAnswer($email, $answer) {
        $sql = "SELECT security_answer FROM users WHERE email = ?";
        $user = Database::fetch($sql, [$email]);
        
        if ($user && password_verify(strtolower(trim($answer)), $user['security_answer'])) {
            return true;
        }
        return false;
    }
    
    // Update profile - ✅ FIXED: Includes role update if needed
    public static function updateProfile($userId, $firstName, $lastName, $email, $phone = '', $role = null) {
        if ($role) {
            $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, role = ? WHERE id = ?";
            return Database::query($sql, [$firstName, $lastName, $email, $phone, $role, $userId]);
        } else {
            $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ? WHERE id = ?";
            return Database::query($sql, [$firstName, $lastName, $email, $phone, $userId]);
        }
    }
    
    // Verify credentials (alias for login)
    public static function verifyCredentials($email, $password) {
        return self::login($email, $password);
    }
    
    // ✅ NEW: Get all users (for admin)
    public static function getAll() {
        $sql = "SELECT id, first_name, last_name, email, phone, role, created_at 
                FROM users ORDER BY created_at DESC";
        return Database::fetchAll($sql);
    }
    
    // ✅ NEW: Update user role (admin function)
    public static function updateRole($userId, $role) {
        $sql = "UPDATE users SET role = ? WHERE id = ?";
        return Database::query($sql, [$role, $userId]);
    }

    public static function getUserAddresses($userId) {
        $db = Database::getConnection();
    
        $query = "SELECT * FROM user_addresses WHERE user_id = :user_id ORDER BY is_default DESC, created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>