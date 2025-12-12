<?php
class PasswordController {
    
    // Show forgot password form
    public static function showForgotPassword() {
        include '../app/views/auth/forgot-password.php';
    }
    
    // Verify security question
    public static function verifySecurityQuestion() {
        if ($_POST) {
            $email = trim($_POST['email']);
            
            // Check if user exists
            $user = User::findByEmail($email);
            if (!$user) {
                $error = "No account found with that email address.";
                include '../app/views/auth/forgot-password.php';
                return;
            }
            
            // Get security question
            $securityQuestion = User::getSecurityQuestion($email);
            
            // Store email in session for next step
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['reset_email'] = $email;
            
            // Show security question form
            include '../app/views/auth/security-question.php';
        }
    }
    
    // Process security answer and show password reset form
    public static function processSecurityAnswer() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['reset_email'])) {
            header('Location: ?page=forgot-password');
            exit;
        }
        
        if ($_POST) {
            $email = $_SESSION['reset_email'];
            $answer = trim($_POST['security_answer']);
            
            // Verify security answer
            if (User::verifySecurityAnswer($email, $answer)) {
                // Answer correct - show password reset form
                include '../app/views/auth/reset-password.php';
            } else {
                $error = "Incorrect answer. Please try again.";
                $securityQuestion = User::getSecurityQuestion($email);
                include '../app/views/auth/security-question.php';
            }
        }
    }
    
    // Update password
    public static function updatePassword() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['reset_email'])) {
            header('Location: ?page=forgot-password');
            exit;
        }
        
        if ($_POST) {
            $email = $_SESSION['reset_email'];
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];
            
            // Validate passwords
            if ($newPassword !== $confirmPassword) {
                $error = "Passwords do not match.";
                include '../app/views/auth/reset-password.php';
                return;
            }
            
            if (strlen($newPassword) < 6) {
                $error = "Password must be at least 6 characters long.";
                include '../app/views/auth/reset-password.php';
                return;
            }
            
            // Update password
            if (User::updatePassword($email, $newPassword)) {
                // Clear session and show success
                unset($_SESSION['reset_email']);
                $success = "Password updated successfully! You can now login with your new password.";
                include '../app/views/auth/login.php';
            } else {
                $error = "Failed to update password. Please try again.";
                include '../app/views/auth/reset-password.php';
            }
        }
    }
}
?>