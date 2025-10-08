<?php
/**
 * AuthController - Authentication Controller
 * Elevate Portal - Job Portal Application
 * 
 * Handles user authentication, registration, and session management
 * Implements secure authentication practices with bcrypt password hashing
 */

require_once __DIR__ . '/../Models/UserModel.php';
require_once __DIR__ . '/../Models/CompanyModel.php';

class AuthController {
    private $userModel;
    private $companyModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
        $this->companyModel = new CompanyModel();
        $this->startSecureSession();
    }
    
    /**
     * Start secure session with security settings
     */
    private function startSecureSession() {
        if (session_status() == PHP_SESSION_NONE) {
            // Set secure session parameters
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS
            ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
            
            session_start();
            
            // Regenerate session ID periodically for security
            if (!isset($_SESSION['created'])) {
                $_SESSION['created'] = time();
            } else if (time() - $_SESSION['created'] > 1800) { // 30 minutes
                session_regenerate_id(true);
                $_SESSION['created'] = time();
            }
        }
    }
    
    /**
     * Display login form
     */
    public function showLogin() {
        // Redirect if already logged in
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }
        
        $data = [
            'title' => 'Login - ' . APP_NAME,
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null
        ];
        
        // Clear flash messages
        unset($_SESSION['error'], $_SESSION['success']);
        
        $this->loadView('auth/login', $data);
    }
    
    /**
     * Display registration form
     */
    public function showRegister() {
        // Redirect if already logged in
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }
        
        $data = [
            'title' => 'Register - ' . APP_NAME,
            'error' => $_SESSION['error'] ?? null,
            'old_input' => $_SESSION['old_input'] ?? []
        ];
        
        // Clear flash messages
        unset($_SESSION['error'], $_SESSION['old_input']);
        
        $this->loadView('auth/register', $data);
    }
    
    /**
     * Process login form submission
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
            return;
        }
        
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember_me = isset($_POST['remember_me']);
        
        // Validate input
        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Please enter both username/email and password.';
            $this->redirect('/login');
            return;
        }
        
        // Rate limiting - prevent brute force attacks
        if ($this->isRateLimited($username)) {
            $_SESSION['error'] = 'Too many login attempts. Please try again later.';
            $this->redirect('/login');
            return;
        }
        
        // Verify credentials
        $user = $this->userModel->verifyLogin($username, $password);
        
        if ($user) {
            // Clear failed login attempts
            $this->clearLoginAttempts($username);
            
            // Set session data
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();
            
            // Set remember me cookie if requested
            if ($remember_me) {
                $this->setRememberMeCookie($user['user_id']);
            }
            
            // Redirect to appropriate dashboard
            $this->redirectToDashboard();
        } else {
            // Record failed login attempt
            $this->recordLoginAttempt($username);
            
            $_SESSION['error'] = 'Invalid username/email or password.';
            $this->redirect('/login');
        }
    }
    
    /**
     * Process registration form submission
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/register');
            return;
        }
        
        $userData = [
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? '',
            'user_type' => $_POST['user_type'] ?? '',
            'full_name' => trim($_POST['full_name'] ?? ''),
            'phone' => trim($_POST['phone'] ?? '')
        ];
        
        // Store old input for form repopulation
        $_SESSION['old_input'] = $userData;
        unset($_SESSION['old_input']['password'], $_SESSION['old_input']['confirm_password']);
        
        // Validate password confirmation
        if ($userData['password'] !== $userData['confirm_password']) {
            $_SESSION['error'] = 'Password confirmation does not match.';
            $this->redirect('/register');
            return;
        }
        
        // Register user
        $result = $this->userModel->register($userData);
        
        if ($result['success']) {
            unset($_SESSION['old_input']);
            $_SESSION['success'] = $result['message'];
            
            // Auto-login after successful registration
            $user = $this->userModel->findUserById($result['user_id']);
            if ($user) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['logged_in'] = true;
                $_SESSION['login_time'] = time();
                
                $this->redirectToDashboard();
            } else {
                $this->redirect('/login');
            }
        } else {
            $_SESSION['error'] = implode('<br>', $result['errors']);
            $this->redirect('/register');
        }
    }
    
    /**
     * Logout user and destroy session
     */
    public function logout() {
        // Clear remember me cookie
        if (isset($_COOKIE['remember_me'])) {
            setcookie('remember_me', '', time() - 3600, '/');
        }
        
        // Destroy session
        session_destroy();
        
        // Start new session for flash message
        session_start();
        $_SESSION['success'] = 'You have been logged out successfully.';
        
        $this->redirect('/login');
    }
    
    /**
     * Check if user is logged in
     * @return bool
     */
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Check if current user has specific role
     * @param string $role Required role
     * @return bool
     */
    public function hasRole($role) {
        return $this->isLoggedIn() && $_SESSION['user_type'] === $role;
    }
    
    /**
     * Get current user data
     * @return array|null
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'user_id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'user_type' => $_SESSION['user_type'],
            'full_name' => $_SESSION['full_name'],
            'email' => $_SESSION['email']
        ];
    }
    
    /**
     * Require authentication - redirect to login if not authenticated
     */
    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            $_SESSION['error'] = 'Please log in to access this page.';
            $this->redirect('/login');
            exit;
        }
        
        // Check session timeout
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > SESSION_LIFETIME) {
            $this->logout();
            return;
        }
    }
    
    /**
     * Require specific role - redirect if user doesn't have required role
     * @param string $role Required role
     */
    public function requireRole($role) {
        $this->requireAuth();
        
        if (!$this->hasRole($role)) {
            $_SESSION['error'] = 'Access denied. Insufficient permissions.';
            $this->redirectToDashboard();
            exit;
        }
    }
    
    /**
     * Redirect to appropriate dashboard based on user type
     */
    private function redirectToDashboard() {
        $redirect_url = $_SESSION['redirect_after_login'] ?? null;
        unset($_SESSION['redirect_after_login']);
        
        if ($redirect_url) {
            $this->redirect($redirect_url);
            return;
        }
        
        if ($this->hasRole('employer')) {
            $this->redirect('/employer/dashboard');
        } else if ($this->hasRole('seeker')) {
            $this->redirect('/seeker/dashboard');
        } else {
            $this->redirect('/dashboard');
        }
    }
    
    /**
     * Rate limiting for login attempts
     * @param string $username Username/email
     * @return bool True if rate limited, false otherwise
     */
    private function isRateLimited($username) {
        $key = 'login_attempts_' . md5($username . $_SERVER['REMOTE_ADDR']);
        
        if (!isset($_SESSION[$key])) {
            return false;
        }
        
        $attempts = $_SESSION[$key];
        $max_attempts = 5;
        $lockout_time = 900; // 15 minutes
        
        if ($attempts['count'] >= $max_attempts) {
            if (time() - $attempts['last_attempt'] < $lockout_time) {
                return true;
            } else {
                // Reset attempts after lockout period
                unset($_SESSION[$key]);
                return false;
            }
        }
        
        return false;
    }
    
    /**
     * Record failed login attempt
     * @param string $username Username/email
     */
    private function recordLoginAttempt($username) {
        $key = 'login_attempts_' . md5($username . $_SERVER['REMOTE_ADDR']);
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'last_attempt' => 0];
        }
        
        $_SESSION[$key]['count']++;
        $_SESSION[$key]['last_attempt'] = time();
    }
    
    /**
     * Clear failed login attempts
     * @param string $username Username/email
     */
    private function clearLoginAttempts($username) {
        $key = 'login_attempts_' . md5($username . $_SERVER['REMOTE_ADDR']);
        unset($_SESSION[$key]);
    }
    
    /**
     * Set remember me cookie
     * @param int $user_id User ID
     */
    private function setRememberMeCookie($user_id) {
        $token = bin2hex(random_bytes(32));
        $expires = time() + (30 * 24 * 60 * 60); // 30 days
        
        setcookie('remember_me', $token, $expires, '/', '', false, true);
        
        // Store token in session for validation
        $_SESSION['remember_token'] = $token;
    }
    
    /**
     * Load view file
     * @param string $view View file path
     * @param array $data Data to pass to view
     */
    private function loadView($view, $data = []) {
        extract($data);
        $view_file = __DIR__ . '/../Views/' . $view . '.php';
        
        if (file_exists($view_file)) {
            include $view_file;
        } else {
            die("View file not found: $view_file");
        }
    }
    
    /**
     * Redirect to URL
     * @param string $url URL to redirect to
     */
    private function redirect($url) {
        // If URL doesn't start with http, treat as relative and add base path
        if (!preg_match('/^https?:\/\//', $url)) {
            $base_path = '/Elevate/public';
            $url = $base_path . $url;
        }
        header("Location: $url");
        exit;
    }
    
    /**
     * Generate CSRF token
     * @return string CSRF token
     */
    public function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF token
     * @param string $token Token to verify
     * @return bool True if valid, false otherwise
     */
    public function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
?>
