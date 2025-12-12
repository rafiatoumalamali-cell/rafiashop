<?php include __DIR__ . '/../header.php'; ?>

<div class="container">
    <div class="user-header">
        <h1>📦 Order History</h1>
        <p>Your complete order history with RafiaShop</p>
    </div>

    <div class="user-nav">
        <!-- FIXED: Use correct routing format -->
        <a href="?page=user&action=dashboard">Dashboard</a>
        <a href="?page=user&action=orders" class="active">Order History</a>
        <a href="?page=user&action=profile">Profile</a>
    </div>

    <?php if (!empty($orders)): ?>
        <div class="orders-list">
            <?php foreach ($orders as $order): ?>
                <div class="order-item">
                    <div class="order-info">
                        <div class="order-main">
                            <strong>Order #<?= $order['id'] ?></strong>
                            <div class="order-date">Placed on <?= date('F j, Y', strtotime($order['created_at'])) ?></div>
                            <div class="order-address">
                                <?= htmlspecialchars($order['address_line1']) ?>, <?= htmlspecialchars($order['city']) ?>
                            </div>
                        </div>
                        <div class="order-status">
                            <span class="status-badge status-<?= $order['status'] ?>">
                                <?= ucfirst($order['status']) ?>
                            </span>
                        </div>
                        <div class="order-actions">
                            <!-- FIXED: Use correct routing format for order details -->
                            <a href="?page=user&action=order-details&id=<?= $order['id'] ?>" class="view-details">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-orders">
            <h3>No orders found</h3>
            <p>You haven't placed any orders yet.</p>
            <a href="?page=products" class="btn-primary">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../footer.php'; ?>