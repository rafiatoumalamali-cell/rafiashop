<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

class AuthController {
    
    public static function showLogin() {
        include '../app/views/auth/login.php';
    }
    
    public static function showRegister() {
        include '../app/views/auth/register.php';
    }
    
    public static function login() {
        if ($_POST) {
            $email = trim($_POST['email']);
            $password = $_POST['password'];
        
            $user = User::login($email, $password);
        
            if ($user) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'role' => $user['role'] ?? 'user'
                ];
            
                if ($user['role'] === 'admin') {
                    header('Location: ?page=admin-dashboard');
                } else {
                    header('Location: ?page=user-dashboard');
                }
                exit;
            } else {
                $error = "Invalid email or password.";
                include '../app/views/auth/login.php';
            }
        }
    }
    
    public static function register() {
        if ($_POST) {
            $firstName = trim($_POST['first_name']);
            $lastName = trim($_POST['last_name']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];
            $securityQuestion = $_POST['security_question'];
            $securityAnswer = trim($_POST['security_answer']);
            
            if ($password !== $confirmPassword) {
                $error = "Passwords do not match.";
                include '../app/views/auth/register.php';
                return;
            }
            
            if (User::emailExists($email)) {
                $error = "Email already registered.";
                include '../app/views/auth/register.php';
                return;
            }
            
            if (User::create($firstName, $lastName, $email, $password, $securityQuestion, $securityAnswer)) {
                $success = "Registration successful! You can now login.";
                include '../app/views/auth/login.php';
            } else {
                $error = "Registration failed. Please try again.";
                include '../app/views/auth/register.php';
            }
        }
    }
    
    public static function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header('Location: ?page=login');
        exit;
    }

    // AJAX Login - FIXED VERSION
    // AJAX Login - FIXED VERSION
    public static function ajaxLogin() {
        // Start output buffering
        ob_start();
    
        // Debug: Log the request
        error_log("=== AJAX LOGIN REQUEST ===");
        error_log("Email: " . ($_POST['email'] ?? 'not set'));
        error_log("Request method: " . $_SERVER['REQUEST_METHOD']);
    
        // Set header for JSON response IMMEDIATELY
        header('Content-Type: application/json');
    
        // Clear any previous output
        ob_clean();
    
        // Check if it's a POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'error' => 'Invalid request method.'
            ]);
            exit;
        }
    
        // Check if POST data exists
        if (!isset($_POST['email']) || !isset($_POST['password'])) {
            echo json_encode([
                'success' => false,
                'error' => 'Email and password are required.'
            ]);
            exit;
        }
    
        $email = trim($_POST['email']);
        $password = $_POST['password'];
    
        // Basic validation
        if (empty($email) || empty($password)) {
            echo json_encode([
                'success' => false,
                'error' => 'Please fill in all fields.'
            ]);
            exit;
        }
    
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                'success' => false,
                'error' => 'Please enter a valid email address.'
            ]);
            exit;
        }
    
        // Try to login
        try {
            error_log("Calling User::login()...");
            $user = User::login($email, $password);
        
            if ($user) {
                error_log("User::login() returned user data");
            
                // Start session
                if (session_status() === PHP_SESSION_NONE) {
                session_start();
                }
            
                // Store user in session
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'first_name' => $user['first_name'] ?? '',
                    'last_name' => $user['last_name'] ?? '',
                    'role' => $user['role'] ?? 'user'
                ];
            
                // Also set user_id for compatibility
                $_SESSION['user_id'] = $user['id'];
            
                // Determine redirect URL
                $redirectUrl = '?page=user-dashboard';
                if (($user['role'] ?? 'user') === 'admin') {
                $redirectUrl = '?page=admin-dashboard';
                }
            
                // Return success
                $response = [
                    'success' => true,
                    'message' => 'Login successful',
                    'redirect' => $redirectUrl,
                    'user' => [
                        'id' => $user['id'],
                        'name' => ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')
                    ]
                ];
            
                error_log("Sending success response: " . json_encode($response));
                echo json_encode($response);
                exit;
            
            } else {
                error_log("User::login() returned false");
            
                // Login failed
                $response = [
                    'success' => false,
                    'error' => 'Invalid email or password.'
                ];
            
                error_log("Sending error response: " . json_encode($response));
                echo json_encode($response);
                exit;
            }
        
        } catch (Exception $e) {
            error_log("AJAX Login Exception: " . $e->getMessage());
            error_log("Exception trace: " . $e->getTraceAsString());
        
            // Return generic error
            echo json_encode([
                'success' => false,
                'error' => 'An error occurred. Please try again.',
                'debug' => $e->getMessage() // Remove in production
            ]);
            exit;
        }
    }
}
