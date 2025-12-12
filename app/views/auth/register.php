<?php include __DIR__ . '/../header.php'; ?>

<div class="auth-container">
    <h2>📝 Create Your Account</h2>
    
    <?php if (isset($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="POST" action="?page=register-process" class="auth-form">
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" name="first_name" id="first_name" required placeholder="Enter your first name">
        </div>
        
        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" name="last_name" id="last_name" required placeholder="Enter your last name">
        </div>
        
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" required placeholder="Enter your email">
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required placeholder="Create a password" minlength="6">
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" required placeholder="Confirm your password" minlength="6">
        </div>
        
        <div class="form-group">
            <label for="security_question">Security Question</label>
            <select name="security_question" id="security_question" required>
                <option value="">Select a security question</option>
                <option value="What city were you born in?">What city were you born in?</option>
                <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
                <option value="What was your first pet's name?">What was your first pet's name?</option>
                <option value="What elementary school did you attend?">What elementary school did you attend?</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="security_answer">Security Answer</label>
            <input type="text" name="security_answer" id="security_answer" required placeholder="Your answer">
        </div>
        
        <button type="submit" class="btn-primary">Create Account</button>
    </form>
    
    <div class="auth-links">
        <a href="?page=login">← Already have an account? Login</a>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>