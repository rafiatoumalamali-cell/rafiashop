<?php 
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check admin access first
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') {
    echo "<div class='container mt-5'>
            <div class='alert alert-danger'>
                <h3>Access Denied</h3>
                <p>You don't have permission to access the admin dashboard.</p>
                <p><a href='?page=user-dashboard' class='btn btn-primary'>Go to User Dashboard</a></p>
            </div>
          </div>";
    exit;
}

include '../app/views/header.php'; 
?>

<div class="admin-header">
    <h1>Admin Dashboard</h1>
    <p>Welcome back, <?php echo $_SESSION['user']['first_name'] ?? 'Admin'; ?>!</p>
</div>

<div class="admin-nav">
    <!-- CORRECTED LINKS: Use ?page=admin&action=XXX -->
    <a href="?page=admin&action=dashboard" class="active">Dashboard</a>
    <a href="?page=admin&action=products">Products</a>
    <a href="?page=admin&action=orders">Orders</a>
    <a href="?page=admin&action=users">Users</a>
    <a href="?page=admin&action=analytics">Analytics</a>
    <a href="?page=admin&action=inventory">Inventory</a>
    
</div>



<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <h3>Total Users</h3>
        <div class="stat-number"><?= $stats['total_users'] ?? 0 ?></div>
    </div>
    <div class="stat-card">
        <h3>Total Products</h3>
        <div class="stat-number"><?= $stats['total_products'] ?? 0 ?></div>
    </div>
    <div class="stat-card">
        <h3>Total Orders</h3>
        <div class="stat-number"><?= $stats['total_orders'] ?? 0 ?></div>
    </div>
    <div class="stat-card">
        <h3>Total Revenue</h3>
        <div class="stat-number">$<?= number_format($stats['total_revenue'] ?? 0, 2) ?></div>
    </div>
</div>

<!-- Recent Orders -->
<div class="recent-orders">
    <h2>Recent Orders</h2>
    <?php if (empty($recentOrders)): ?>
        <p>No orders yet.</p>
    <?php else: ?>
        <div class="orders-list">
            <?php foreach (array_slice($recentOrders, 0, 5) as $order): ?>
                <div class="order-item">
                    <div class="order-info">
                        <strong>Order #<?= $order['id'] ?></strong>
                        <span class="status-badge status-<?= $order['status'] ?>">
                            <?= ucfirst($order['status']) ?>
                        </span>
                    </div>
                    <div class="order-details">
                        <span><?= $order['first_name'] ?? '' ?> <?= $order['last_name'] ?? '' ?></span>
                        <span><?= $order['item_count'] ?? 0 ?> items</span>
                        <span>$<?= number_format($order['total_amount'] ?? 0, 2) ?></span>
                        <span><?= date('M j, Y', strtotime($order['created_at'] ?? 'now')) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- CORRECTED LINK -->
        <a href="?page=admin&action=orders" class="view-all">View All Orders →</a>
    <?php endif; ?>
</div>

<?php include '../app/views/footer.php'; ?>