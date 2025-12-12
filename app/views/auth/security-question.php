<?php include __DIR__ . '/../header.php'; ?>

<div class="auth-container">
    <h2>🔒 Security Verification</h2>
    
    <?php if (isset($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="POST" action="?page=process-security-answer">
        <div class="form-group">
            <label>Security Question:</label>
            <p class="security-question"><?= htmlspecialchars($securityQuestion) ?></p>
        </div>
        
        <div class="form-group">
            <label>Your Answer:</label>
            <input type="text" name="security_answer" required class="form-control" placeholder="Enter your answer">
        </div>
        
        <button type="submit" class="btn btn-primary">Verify Answer</button>
    </form>
    
    <div class="auth-links">
        <a href="?page=forgot-password">← Back</a>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>