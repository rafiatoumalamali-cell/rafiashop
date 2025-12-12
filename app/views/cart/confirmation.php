<?php include '../app/views/header.php'; ?>

<div class="confirmation">
    <h1>🎉 Order Confirmed!</h1>
    
    <div class="confirmation-details">
        <p><strong>Order ID:</strong> #<?= $order['id'] ?></p>
        <p><strong>Status:</strong> <span class="status-badge status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></p>
        <p><strong>Payment Method:</strong> <?= $paymentMethod === 'stripe' ? 'Credit Card' : 'Cash on Delivery' ?></p>
        
        <h3>Order Items:</h3>
        <div class="order-items">
            <?php foreach ($orderItems as $item): ?>
                <div class="order-item">
                    <p><strong><?= htmlspecialchars($item['product_name']) ?></strong></p>
                    <p>Quantity: <?= $item['quantity'] ?></p>
                    <p>Price: $<?= number_format($item['unit_price'], 2) ?></p>
                    <?php if (!empty($item['custom_notes'])): ?>
                        <p class="custom-notes"><?= htmlspecialchars($item['custom_notes']) ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <h3>Shipping Address:</h3>
        <p><?= htmlspecialchars($order['address_line1']) ?><br>
           <?= htmlspecialchars($order['city']) ?><br>
           <?= htmlspecialchars($order['postal_code']) ?></p>
    </div>
    
    <!-- ✅ ADD DASHBOARD LINK -->
    <div class="confirmation-actions">
        <a href="?page=user-dashboard" class="btn-primary">📊 View My Dashboard</a>
        <a href="?page=products" class="btn-secondary">🛍️ Continue Shopping</a>
    </div>
</div>

<?php include '../app/views/footer.php'; ?>