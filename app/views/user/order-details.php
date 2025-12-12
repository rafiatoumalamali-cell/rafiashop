<?php include __DIR__ . '/../header.php'; ?>

<div class="container">
    <div class="user-header">
        <h1>📋 Order Details</h1>
        <p>Order #<?= $order['id'] ?> - <?= date('F j, Y', strtotime($order['created_at'])) ?></p>
    </div>

    <div class="user-nav">
        <a href="?page=user&action=dashboard">Dashboard</a>
        <a href="?page=user&action=orders">Order History</a>
        <a href="?page=user&action=profile">Profile</a>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="order-details-container">
        <!-- Order Summary Card -->
        <div class="order-summary-card">
            <h3>Order Summary</h3>
            <div class="summary-grid">
                <div class="summary-item">
                    <span class="summary-label">Order Status:</span>
                    <span class="status-badge status-<?= $order['status'] ?>">
                        <?= ucfirst($order['status']) ?>
                    </span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Order Date:</span>
                    <span><?= date('F j, Y, g:i a', strtotime($order['created_at'])) ?></span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Payment Method:</span>
                    <span><?= ucfirst($order['payment_method']) ?></span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Payment Status:</span>
                    <span class="payment-status"><?= ucfirst($order['payment_status']) ?></span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Order Total:</span>
                    <span class="total-amount"><?= CurrencyHelper::format($total) ?></span>
                </div>
            </div>
        </div>

        <!-- Shipping Address -->
        <div class="address-card">
            <h3>Shipping Address</h3>
            <div class="address-details">
                <p><strong><?= htmlspecialchars($order['name']) ?></strong></p>
                <p><?= htmlspecialchars($order['address_line1']) ?></p>
                <?php if (!empty($order['address_line2'])): ?>
                    <p><?= htmlspecialchars($order['address_line2']) ?></p>
                <?php endif; ?>
                <p><?= htmlspecialchars($order['city']) ?><?= !empty($order['state']) ? ', ' . htmlspecialchars($order['state']) : '' ?> - <?= htmlspecialchars($order['pincode']) ?></p>
                <p>Phone: <?= htmlspecialchars($order['phone']) ?></p>
            </div>
        </div>

        <!-- Order Items -->
        <div class="order-items-card">
            <h3>Order Items (<?= count($order_items) ?>)</h3>
            
            <div class="order-items-list">
                <?php foreach ($order_items as $item): ?>
                    <div class="order-item-row">
                        <div class="item-image">
                            <?php if (!empty($item['image_url'])): ?>
                                <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                            <?php else: ?>
                                <div class="no-image">No Image</div>
                            <?php endif; ?>
                        </div>
                        <div class="item-details">
                            <h4><?= htmlspecialchars($item['name']) ?></h4>
                            <p class="item-price"><?= CurrencyHelper::format($item['price']) ?></p>
                            <p class="item-quantity">Quantity: <?= $item['quantity'] ?></p>
                        </div>
                        <div class="item-total">
                            <?= CurrencyHelper::format($item['price'] * $item['quantity']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Order Totals -->
            <div class="order-totals">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span><?= CurrencyHelper::format($subtotal) ?></span>
                </div>
                <div class="total-row">
                    <span>Shipping:</span>
                    <span><?= CurrencyHelper::format($shipping) ?></span>
                </div>
                <div class="total-row grand-total">
                    <span>Total:</span>
                    <span><?= CurrencyHelper::format($total) ?></span>
                </div>
            </div>
        </div>

        <!-- Order Actions -->
        <div class="order-actions-card">
            <h3>Order Actions</h3>
            <div class="action-buttons">
                <?php if ($order['status'] == 'pending'): ?>
                    <a href="?page=user&action=cancel-order&id=<?= $order['id'] ?>" 
                       class="btn btn-danger" 
                       onclick="return confirm('Are you sure you want to cancel this order?');">
                        Cancel Order
                    </a>
                <?php endif; ?>
                
                <?php if ($order['status'] == 'shipped'): ?>
                    <a href="#" class="btn btn-primary">Track Package</a>
                <?php endif; ?>
                
                <a href="?page=user&action=orders" class="btn btn-secondary">Back to Orders</a>
                
                <?php if ($order['payment_method'] == 'stripe' && $order['payment_status'] == 'pending'): ?>
                    <a href="?page=payment&action=stripe-checkout&order_id=<?= $order['id'] ?>" class="btn btn-success">
                        Complete Payment
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>