<?php include '../app/views/header.php'; ?>

<h1>Shopping Cart</h1>

<?php if (empty($cartItems)): ?>
    <div class="empty-cart">
        <p>Your cart is empty.</p>
        <a href="?page=products" class="continue-shopping">Continue Shopping</a>
    </div>
<?php else: ?>
    <!-- Stock Error Display -->
    <?php if (!empty($stock_errors) && is_array($stock_errors)): ?>
        <div class="stock-warning" style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <h3 style="color: #856404; margin-top: 0;">
                <i class="fas fa-exclamation-triangle"></i> Stock Issues
            </h3>
            <ul style="margin-bottom: 10px;">
                <?php foreach ($stock_errors as $error): 
                    // Skip if error is not an array or missing required keys
                    if (!is_array($error) || !isset($error['product_name'])) continue;
                ?>
                    <li style="margin-bottom: 5px;">
                        <strong><?php echo htmlspecialchars($error['product_name'] ?? 'Unknown Product'); ?></strong>
                        <?php if (!empty($error['variant_details'])): ?>
                            <span style="color: #6c757d;"><?php echo htmlspecialchars($error['variant_details']); ?></span>
                        <?php endif; ?>
                        : Only <?php echo $error['available'] ?? 0; ?> available 
                        (you requested: <?php echo $error['requested'] ?? 0; ?>)
                    </li>
                <?php endforeach; ?>
            </ul>
            <p style="margin-bottom: 0; font-size: 0.9em;">
                Please adjust quantities before proceeding to checkout.
            </p>
        </div>
    <?php endif; ?>
    
    <div class="cart-container">
        <div class="cart-items">
            <?php foreach ($cartItems as $index => $item): ?>
                <?php 
                $product = Product::getById($item['product_id']);
                if ($product): 
                ?>
                    <div class="cart-item">
                        <div class="item-image">
                            <?php if ($product['image_url']): ?>
                                <img src="<?= $product['image_url'] ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                            <?php else: ?>
                                <div class="no-image">No Image</div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="item-details">
                            <h3><?= htmlspecialchars($product['name']) ?></h3>
                            <?php if (isset($item['size']) || isset($item['color'])): ?>
                            <p class="item-customization">
                                <?php if (isset($item['size'])): ?>
                                    <strong>Size:</strong> <?= htmlspecialchars($item['size']) ?>
                                <?php endif; ?>
                                <?php if (isset($item['size']) && isset($item['color'])): ?> | <?php endif; ?>
                                <?php if (isset($item['color'])): ?>
                                    <strong>Color:</strong> <?= htmlspecialchars($item['color']) ?>
                                <?php endif; ?>
                            </p>
                            <?php endif; ?>
                            <?php if (!empty($item['instructions'])): ?>
                                <p class="item-instructions">
                                    <strong>Instructions:</strong> <?= htmlspecialchars($item['instructions']) ?>
                                </p>
                            <?php endif; ?>
                            
                            <!-- Stock Status - FIXED: Removed Inventory dependency -->
                            <div class="stock-status">
                                <?php 
                                // Simple stock check using product stock_quantity
                                $available_stock = $product['stock_quantity'] ?? 0;
                                
                                // If variant stock is tracked differently, you would check here
                                // For now, using base product stock
                                
                                if ($available_stock >= $item['quantity']): ?>
                                    <span class="in-stock">✓ In Stock (<?= $available_stock ?> available)</span>
                                <?php elseif ($available_stock > 0): ?>
                                    <span class="low-stock">⚠ Only <?= $available_stock ?> left</span>
                                <?php else: ?>
                                    <span class="out-of-stock">✗ Out of Stock</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="item-controls">
                            <!-- Quantity Update -->
                            <form method="POST" action="?page=cart&action=update-quantity" class="quantity-form">
                                <input type="hidden" name="index" value="<?= $index ?>">
                                <label>Qty:</label>
                                <input type="number" 
                                       name="quantity" 
                                       value="<?= $item['quantity'] ?>" 
                                       min="1" 
                                       max="<?= max($available_stock, $item['quantity']) ?>" 
                                       class="quantity-input">
                                <button type="submit" class="btn-update">Update</button>
                            </form>
                            
                            <p class="item-price">$<?= number_format($product['base_price'] * $item['quantity'], 2) ?></p>
                            <p class="unit-price">$<?= number_format($product['base_price'], 2) ?> each</p>
                            
                            <!-- Remove Button -->
                            <a href="?page=cart&action=remove&index=<?= $index ?>" 
                               class="btn-remove" 
                               onclick="return confirm('Remove this item from cart?')">Remove</a>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        
        <div class="cart-summary">
            <div class="summary-card">
                <h3>Order Summary</h3>
                <div class="summary-row">
                    <span>Items (<?= Cart::getTotalItems() ?>):</span>
                    <span>$<?= number_format(Cart::getTotal(), 2) ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping:</span>
                    <span>FREE</span>
                </div>
                <div class="summary-row total">
                    <span><strong>Total:</strong></span>
                    <span><strong>$<?= number_format(Cart::getTotal(), 2) ?></strong></span>
                </div>

                <div class="cart-actions">
                    <a href="?page=products" class="continue-shopping">Continue Shopping</a>
    
                    <!-- Checkout Button (with stock validation) -->
                    <?php if (empty($stock_errors) || !is_array($stock_errors)): ?>
                        <a href="?page=checkout" class="checkout-btn">Proceed to Checkout</a>
                    <?php else: ?>
                        <button class="checkout-btn disabled" disabled style="opacity: 0.6; cursor: not-allowed;">
                            Fix Stock Issues First
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include '../app/views/footer.php'; ?>