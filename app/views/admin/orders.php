<?php include '../app/views/header.php'; ?>

<div class="admin-header">
    <h1>Manage Orders</h1>
    <p>View and update order status</p>
</div>


<div class="admin-nav">
    <a href="?page=admin&action=dashboard">Dashboard</a>
    <a href="?page=admin&action=products">Products</a>
    <a href="?page=admin&action=orders">Orders</a>
    <a href="?page=admin&action=users">Users</a>
    <a href="?page=admin&action=inventory">Inventory</a>
    <a href="?page=admin&action=analytics">Analytics</a>
</div>



<div class="orders-list">
    <?php if (empty($orders)): ?>
        <p>No orders found.</p>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-item">
                <div class="order-header">
                    <h3>Order #<?= $order['id'] ?></h3>
                    <span class="status-badge status-<?= $order['status'] ?>">
                        <?= ucfirst($order['status']) ?>
                    </span>
                </div>
                <div class="order-details">
                    <p><strong>Customer:</strong> <?= $order['first_name'] ?> <?= $order['last_name'] ?> (<?= $order['email'] ?>)</p>
                    <p><strong>Items:</strong> <?= $order['item_count'] ?></p>
                    <p><strong>Total:</strong> $<?= number_format($order['total_amount'], 2) ?></p>
                    <p><strong>Date:</strong> <?= $order['created_at'] ?></p>
                </div>
                <form method="POST" action= "?page=admin&action=update-order" class="status-form">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <select name="status">
                        <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="confirmed" <?= $order['status'] == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                        <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                        <option value="delivered" <?= $order['status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                        <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                    <button type="submit">Update Status</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include '../app/views/footer.php'; ?>