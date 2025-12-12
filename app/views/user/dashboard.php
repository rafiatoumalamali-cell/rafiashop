<?php include __DIR__ . '/../header.php'; ?>

<div class="container">
    <div class="user-header">
        <h1>👋 Welcome back, <?= htmlspecialchars($_SESSION['user']['first_name']) ?>!</h1>
        <p>Manage your orders and account settings</p>
    </div>

    <div class="user-nav" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            <a href="?page=user&action=dashboard" 
            style="padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px;"
            class="<?php echo ($_GET['action'] ?? 'dashboard') === 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        
            <a href="?page=user&action=orders" 
            style="padding: 10px 20px; background: #2ecc71; color: white; text-decoration: none; border-radius: 5px;"
            class="<?php echo ($_GET['action'] ?? '') === 'orders' ? 'active' : ''; ?>">
                <i class="fas fa-history"></i> Order History
            </a>
        
            <a href="?page=user&action=profile" 
            style="padding: 10px 20px; background: #9b59b6; color: white; text-decoration: none; border-radius: 5px;"
            class="<?php echo ($_GET['action'] ?? '') === 'profile' ? 'active' : ''; ?>">
                <i class="fas fa-user-edit"></i> Profile
            </a>
        
            <a href="?page=user&action=update-profile" 
            style="padding: 10px 20px; background: #e67e22; color: white; text-decoration: none; border-radius: 5px;"
            class="<?php echo ($_GET['action'] ?? '') === 'update-profile' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i> Update Profile
            </a>
        
            <a href="?page=logout" 
            style="padding: 10px 20px; background: #e74c3c; color: white; text-decoration: none; border-radius: 5px;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Orders</h3>
            <div class="stat-number"><?= $stats['total_orders'] ?></div>
        </div>
        <div class="stat-card">
            <h3>Pending Orders</h3>
            <div class="stat-number"><?= $stats['pending_orders'] ?></div>
        </div>
        <div class="stat-card">
            <h3>Completed Orders</h3>
            <div class="stat-number"><?= $stats['completed_orders'] ?></div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="recent-orders">
        <h2>Recent Orders</h2>
        <?php if (!empty($recentOrders)): ?>
            <div class="orders-list">
                <?php foreach ($recentOrders as $order): ?>
                    <div class="order-item">
                        <div class="order-info">
                            <div>
                                <strong>Order #<?= $order['id'] ?></strong>
                                <div class="order-date"><?= date('M j, Y', strtotime($order['created_at'])) ?></div>
                            </div>
                            <div class="order-status">
                                <span class="status-badge status-<?= $order['status'] ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </div>
                            <div class="order-actions">
                                <a href="?page=order-details&id=<?= $order['id'] ?>" class="view-details">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="view-all-orders">
                <a href="?page=order-history" class="btn-primary">View All Orders</a>
            </div>
        <?php else: ?>
            <div class="no-orders">
                <p>You haven't placed any orders yet.</p>
                <a href="?page=products" class="btn-primary">Start Shopping</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>