<?php include __DIR__ . '/../header.php'; ?>

<div class="auth-container">
    <h2>🔄 Reset Password</h2>
    
    <?php if (isset($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="POST" action="?page=update-password">
        <div class="form-group">
            <label>New Password:</label>
            <input type="password" name="new_password" required class="form-control" placeholder="Enter new password" minlength="6">
        </div>
        
        <div class="form-group">
            <label>Confirm New Password:</label>
            <input type="password" name="confirm_password" required class="form-control" placeholder="Confirm new password" minlength="6">
        </div>
        
        <button type="submit" class="btn btn-primary">Reset Password</button>
    </form>
    
    <div class="auth-links">
        <a href="?page=login">← Back to Login</a>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>