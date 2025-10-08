<?php
/**
 * UserModel - User Management Model
 * Elevate Portal - Job Portal Application
 * 
 * Handles user registration, authentication, and profile management
 * Implements secure password handling and user validation
 */

require_once __DIR__ . '/DatabaseModel.php';

class UserModel extends DatabaseModel {
    protected $table = 'users';
    
    /**
     * Register a new user
     * @param array $userData User registration data
     * @return array Result with success status and message
     */
    public function register($userData) {
        // Sanitize input data
        $userData = $this->sanitize_input($userData);
        
        // Validate required fields
        $required_fields = ['username', 'email', 'password', 'user_type', 'full_name'];
        $errors = $this->validate_required_fields($userData, $required_fields);
        
        // Validate email format
        if (!empty($userData['email']) && !$this->validate_email($userData['email'])) {
            $errors[] = "Please enter a valid email address.";
        }
        
        // Validate password strength
        if (!empty($userData['password']) && strlen($userData['password']) < 6) {
            $errors[] = "Password must be at least 6 characters long.";
        }
        
        // Validate user type
        if (!empty($userData['user_type']) && !in_array($userData['user_type'], ['seeker', 'employer'])) {
            $errors[] = "Invalid user type selected.";
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Check if username or email already exists
        if ($this->username_exists($userData['username'])) {
            return ['success' => false, 'errors' => ['Username already exists.']];
        }
        
        if ($this->email_exists($userData['email'])) {
            return ['success' => false, 'errors' => ['Email address already registered.']];
        }
        
        try {
            // Hash password securely
            $password_hash = password_hash($userData['password'], PASSWORD_BCRYPT);
            
            $sql = "INSERT INTO users (username, email, password_hash, user_type, full_name, phone) 
                    VALUES (:username, :email, :password_hash, :user_type, :full_name, :phone)";
            
            $params = [
                ':username' => $userData['username'],
                ':email' => $userData['email'],
                ':password_hash' => $password_hash,
                ':user_type' => $userData['user_type'],
                ':full_name' => $userData['full_name'],
                ':phone' => $userData['phone'] ?? null
            ];
            
            $this->execute_query($sql, $params);
            $user_id = $this->get_last_insert_id();
            
            return [
                'success' => true, 
                'message' => 'Registration successful!',
                'user_id' => $user_id
            ];
            
        } catch (Exception $e) {
            error_log("User Registration Error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Registration failed. Please try again.']];
        }
    }
    
    /**
     * Verify user login credentials
     * @param string $username Username or email
     * @param string $password Plain text password
     * @return array|false User data if valid, false otherwise
     */
    public function verifyLogin($username, $password) {
        $sql = "SELECT user_id, username, email, password_hash, user_type, full_name 
                FROM users 
                WHERE username = :username OR email = :email";
        
        $user = $this->fetch_single($sql, [
            ':username' => $username,
            ':email' => $username
        ]);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Remove password hash from returned data
            unset($user['password_hash']);
            return $user;
        }
        
        return false;
    }
    
    /**
     * Find user by ID
     * @param int $user_id User ID
     * @return array|false User data if found, false otherwise
     */
    public function findUserById($user_id) {
        $sql = "SELECT user_id, username, email, user_type, full_name, phone, created_at 
                FROM users 
                WHERE user_id = :user_id";
        
        return $this->fetch_single($sql, [':user_id' => $user_id]);
    }
    
    /**
     * Update user profile
     * @param int $user_id User ID
     * @param array $userData Updated user data
     * @return array Result with success status and message
     */
    public function updateProfile($user_id, $userData) {
        $userData = $this->sanitize_input($userData);
        
        // Validate email if provided
        if (!empty($userData['email']) && !$this->validate_email($userData['email'])) {
            return ['success' => false, 'errors' => ['Please enter a valid email address.']];
        }
        
        // Check if email is already taken by another user
        if (!empty($userData['email']) && $this->email_exists($userData['email'], $user_id)) {
            return ['success' => false, 'errors' => ['Email address already in use.']];
        }
        
        try {
            $sql = "UPDATE users 
                    SET full_name = :full_name, email = :email, phone = :phone, updated_at = CURRENT_TIMESTAMP
                    WHERE user_id = :user_id";
            
            $params = [
                ':full_name' => $userData['full_name'],
                ':email' => $userData['email'],
                ':phone' => $userData['phone'] ?? null,
                ':user_id' => $user_id
            ];
            
            $this->execute_query($sql, $params);
            
            return ['success' => true, 'message' => 'Profile updated successfully!'];
            
        } catch (Exception $e) {
            error_log("Profile Update Error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Profile update failed. Please try again.']];
        }
    }
    
    /**
     * Change user password
     * @param int $user_id User ID
     * @param string $current_password Current password
     * @param string $new_password New password
     * @return array Result with success status and message
     */
    public function changePassword($user_id, $current_password, $new_password) {
        // Validate new password strength
        if (strlen($new_password) < 6) {
            return ['success' => false, 'errors' => ['New password must be at least 6 characters long.']];
        }
        
        // Verify current password
        $sql = "SELECT password_hash FROM users WHERE user_id = :user_id";
        $user = $this->fetch_single($sql, [':user_id' => $user_id]);
        
        if (!$user || !password_verify($current_password, $user['password_hash'])) {
            return ['success' => false, 'errors' => ['Current password is incorrect.']];
        }
        
        try {
            $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);
            
            $sql = "UPDATE users 
                    SET password_hash = :password_hash, updated_at = CURRENT_TIMESTAMP
                    WHERE user_id = :user_id";
            
            $this->execute_query($sql, [
                ':password_hash' => $new_password_hash,
                ':user_id' => $user_id
            ]);
            
            return ['success' => true, 'message' => 'Password changed successfully!'];
            
        } catch (Exception $e) {
            error_log("Password Change Error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Password change failed. Please try again.']];
        }
    }
    
    /**
     * Check if username exists
     * @param string $username Username to check
     * @param int $exclude_user_id User ID to exclude from check (for updates)
     * @return bool
     */
    private function username_exists($username, $exclude_user_id = null) {
        $sql = "SELECT COUNT(*) FROM users WHERE username = :username";
        $params = [':username' => $username];
        
        if ($exclude_user_id) {
            $sql .= " AND user_id != :user_id";
            $params[':user_id'] = $exclude_user_id;
        }
        
        return $this->get_count($sql, $params) > 0;
    }
    
    /**
     * Check if email exists
     * @param string $email Email to check
     * @param int $exclude_user_id User ID to exclude from check (for updates)
     * @return bool
     */
    private function email_exists($email, $exclude_user_id = null) {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $params = [':email' => $email];
        
        if ($exclude_user_id) {
            $sql .= " AND user_id != :user_id";
            $params[':user_id'] = $exclude_user_id;
        }
        
        return $this->get_count($sql, $params) > 0;
    }
    
    /**
     * Get all seekers for admin purposes
     * @return array List of seekers
     */
    public function getAllSeekers() {
        $sql = "SELECT user_id, username, email, full_name, phone, created_at 
                FROM users 
                WHERE user_type = 'seeker' 
                ORDER BY created_at DESC";
        
        return $this->fetch_all($sql);
    }
    
    /**
     * Get all employers for admin purposes
     * @return array List of employers
     */
    public function getAllEmployers() {
        $sql = "SELECT u.user_id, u.username, u.email, u.full_name, u.phone, u.created_at,
                       c.company_name, c.company_location
                FROM users u
                LEFT JOIN companies c ON u.user_id = c.user_id
                WHERE u.user_type = 'employer' 
                ORDER BY u.created_at DESC";
        
        return $this->fetch_all($sql);
    }
}
?>
