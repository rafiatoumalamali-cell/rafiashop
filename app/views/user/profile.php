<?php include __DIR__ . '/../header.php'; ?>

<div class="container">
    <div class="user-header">
        <h1><i class="fas fa-user-circle"></i> My Profile</h1>
        <p>Manage your account information</p>
    </div>

    <div class="user-nav" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            <a href="?page=user&action=dashboard" 
               style="padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px;"
               class="<?php echo ($_GET['action'] ?? '') === 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            
            <a href="?page=user&action=orders" 
               style="padding: 10px 20px; background: #2ecc71; color: white; text-decoration: none; border-radius: 5px;"
               class="<?php echo ($_GET['action'] ?? '') === 'orders' ? 'active' : ''; ?>">
                <i class="fas fa-history"></i> Order History
            </a>
            
            <a href="?page=user&action=profile" 
               style="padding: 10px 20px; background: #9b59b6; color: white; text-decoration: none; border-radius: 5px;"
               class="active">
                <i class="fas fa-user-edit"></i> Profile
            </a>
            
            <a href="?page=logout" 
               style="padding: 10px 20px; background: #e74c3c; color: white; text-decoration: none; border-radius: 5px;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <div class="profile-container">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" action="?page=user&action=update-profile" class="profile-form">
            <div class="form-section">
                <h3><i class="fas fa-user"></i> Personal Information</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" name="first_name" id="first_name" 
                               value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" 
                               class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" name="last_name" id="last_name" 
                               value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" 
                               class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" 
                           value="<?= htmlspecialchars($user['email'] ?? '') ?>" 
                           class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" name="phone" id="phone" 
                           value="<?= htmlspecialchars($user['phone'] ?? '') ?>" 
                           class="form-control" 
                           placeholder="Optional">
                </div>
            </div>

            <div class="form-section">
                <h3><i class="fas fa-info-circle"></i> Account Information</h3>
                <div class="account-info" style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
                    <p><strong><i class="fas fa-calendar"></i> Member since:</strong> <?= date('F Y', strtotime($user['created_at'] ?? 'now')) ?></p>
                    <p><strong><i class="fas fa-id-card"></i> Account ID:</strong> #<?= $user['id'] ?? 'N/A' ?></p>
                    <p><strong><i class="fas fa-user-tag"></i> Role:</strong> <?= ucfirst($user['role'] ?? 'user') ?></p>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 30px; display: flex; gap: 15px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Profile
                </button>
                <a href="?page=user&action=dashboard" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>

        <div class="profile-actions" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 40px;">
            <div class="action-card" style="background: white; border: 1px solid #e1e5eb; border-radius: 8px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <h4 style="color: #3498db;"><i class="fas fa-lock"></i> Security</h4>
                <p>Update your password and security settings</p>
                <a href="?page=password&action=forgot" class="btn btn-outline-primary">
                    <i class="fas fa-key"></i> Change Password
                </a>
            </div>
            
            <div class="action-card" style="background: white; border: 1px solid #e1e5eb; border-radius: 8px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <h4 style="color: #2ecc71;"><i class="fas fa-box"></i> Order History</h4>
                <p>View your complete order history</p>
                <a href="?page=user&action=orders" class="btn btn-outline-success">
                    <i class="fas fa-shopping-bag"></i> View Orders
                </a>
            </div>
            
            <div class="action-card" style="background: white; border: 1px solid #e1e5eb; border-radius: 8px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <h4 style="color: #9b59b6;"><i class="fas fa-question-circle"></i> Help & Support</h4>
                <p>Need help? Contact our support team</p>
                <a href="mailto:rafiatou.ali@ashesi.edu.gh" class="btn btn-outline-info">
                    <i class="fas fa-headset"></i> Contact Support
                </a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>