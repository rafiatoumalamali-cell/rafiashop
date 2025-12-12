<?php include __DIR__ . '/../header.php'; ?>

<div class="auth-container">
    <h2>🔐 Forgot Password</h2>
    
    <?php if (isset($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="POST" action="?page=verify-security">
        <div class="form-group">
            <label>Email Address:</label>
            <input type="email" name="email" required class="form-control" placeholder="Enter your email">
        </div>
        
        <button type="submit" class="btn btn-primary">Continue</button>
    </form>
    
    <div class="auth-links">
        <a href="?page=login">← Back to Login</a>
    </div>
</div>


<?php include __DIR__ . '/../footer.php'; ?>