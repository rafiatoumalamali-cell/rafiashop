<?php 
// app/views/products/detail.php
include '../app/views/header.php'; 

// Check if product exists
if (!$product): ?>
    <div class="container mt-5">
        <div class="alert alert-danger shadow-sm">
            <i class="fas fa-exclamation-circle me-2"></i> Product not found.
        </div>
    </div>
    <?php include '../app/views/footer.php'; ?>
    <?php exit; ?>
<?php endif; ?>

<!-- Load Inventory Model -->
<?php
require_once __DIR__ . '/../../models/Inventory.php';
$inventory = new Inventory();

// Get variant ID from URL or form
$variantId = $_GET['variant_id'] ?? ($product['variant_id'] ?? null);
$productId = $product['id'] ?? 0;

// Get accurate stock information
if ($variantId) {
    $stockInfo = $inventory->getDisplayStock($productId, $variantId);
} else {
    $stockInfo = $inventory->getDisplayStock($productId);
}

$availableStock = $stockInfo['available_stock'] ?? 0;
$totalStock = $stockInfo['total_stock'] ?? 0;
$reservedStock = $stockInfo['reserved_quantity'] ?? 0;

// Determine stock status
if ($availableStock > 10) {
    $stockClass = 'stock-in';
    $stockIcon = 'fa-check-circle';
    $stockText = 'In Stock';
} elseif ($availableStock > 0 && $availableStock <= 10) {
    $stockClass = 'stock-low';
    $stockIcon = 'fa-exclamation-triangle';
    $stockText = 'Low Stock';
} else {
    $stockClass = 'stock-out';
    $stockIcon = 'fa-times-circle';
    $stockText = 'Out of Stock';
}
?>

<!-- Product Detail Styles -->
<style>
.product-detail-page {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    min-height: 100vh;
}

.product-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(44, 62, 80, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
}

.product-image-container {
    position: relative;
    overflow: hidden;
    border-radius: 12px;
    height: 500px;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: contain; 
    transition: transform 0.5s ease;
    background-color: #f5f5f5; 
}

.product-image:hover {
    transform: scale(1.03);
}

.product-badges {
    position: absolute;
    top: 15px;
    left: 15px;
    z-index: 10;
}

.product-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-right: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.badge-category {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    color: white;
}

.badge-featured {
    background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
    color: white;
}

.badge-stock-info {
    background: linear-gradient(135deg, #2c3e50 0%, #1a2530 100%);
    color: white;
    font-size: 0.75rem;
    cursor: help;
}

.product-price {
    font-size: 2.5rem;
    font-weight: 800;
    color: #2c3e50;
    margin: 20px 0;
}

.product-price::before {
    content: 'CFA';
    font-size: 1.8rem;
    vertical-align: super;
    margin-right: 2px;
}

.stock-indicator {
    padding: 15px;
    border-radius: 12px;
    margin: 20px 0;
    border-left: 5px solid;
    animation: slideIn 0.5s ease;
}

.stock-in {
    background: linear-gradient(135deg, #e8f6ef 0%, #d4efdf 100%);
    border-left-color: #27ae60;
    color: #27ae60;
}

.stock-low {
    background: linear-gradient(135deg, #fef9e7 0%, #fcf3cf 100%);
    border-left-color: #f39c12;
    color: #f39c12;
}

.stock-out {
    background: linear-gradient(135deg, #ffeaea 0%, #ffd6d6 100%);
    border-left-color: #e74c3c;
    color: #e74c3c;
}

.stock-details {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-top: 10px;
    border: 1px dashed #dee2e6;
}

.stock-detail-item {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
    font-size: 0.9rem;
}

.stock-detail-label {
    color: #6c757d;
    font-weight: 500;
}

.stock-detail-value {
    color: #2c3e50;
    font-weight: 600;
}

.stock-warning {
    color: #dc3545;
    font-size: 0.85rem;
    margin-top: 5px;
    animation: pulse 2s infinite;
}

.customization-section {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 25px;
    margin: 25px 0;
    border: 2px solid #e9ecef;
}

.customization-title {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #3498db;
}

.option-select {
    border: 2px solid #e1e5eb;
    border-radius: 10px;
    padding: 12px 15px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: white;
}

.option-select:focus {
    border-color: #3498db;
    box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
    transform: translateY(-2px);
}

.option-select:hover {
    border-color: #3498db;
}

.quantity-control {
    width: 180px;
}

.quantity-btn {
    width: 40px;
    height: 40px;
    border: 2px solid #3498db;
    background: white;
    color: #3498db;
    font-weight: bold;
    transition: all 0.3s ease;
}

.quantity-btn:hover {
    background: #3498db;
    color: white;
    transform: scale(1.1);
}

.quantity-btn:disabled {
    border-color: #adb5bd;
    color: #adb5bd;
    cursor: not-allowed;
}

.quantity-btn:disabled:hover {
    background: white;
    transform: none;
}

.quantity-input {
    border: 2px solid #e1e5eb;
    border-left: none;
    border-right: none;
    text-align: center;
    font-weight: 600;
    color: #2c3e50;
}

.quantity-input:disabled {
    background-color: #e9ecef;
    color: #6c757d;
}

.add-to-cart-btn {
    background: wheat;
    border: none;
    border-radius: 12px;
    padding: 18px;
    font-size: 1.2rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    transition: all 0.3s ease;
    box-shadow: 0 6px 20px rgba(44, 62, 80, 0.2);
}

.add-to-cart-btn:hover {
    background: green;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(52, 152, 219, 0.3);
}

.add-to-cart-btn:disabled {
    background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
    transform: none;
    box-shadow: none;
    cursor: not-allowed;
}

.notify-btn {
    background: white;
    border: 2px solid #3498db;
    color: #3498db;
    border-radius: 12px;
    padding: 18px;
    font-size: 1.1rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.notify-btn:hover {
    background: #3498db;
    color: white;
    transform: translateY(-2px);
}

.product-details-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    margin-top: 30px;
    border: 2px solid #f1f1f1;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #f1f1f1;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    color: #7b8a8b;
    font-weight: 500;
}

.detail-value {
    color: #2c3e50;
    font-weight: 600;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.6; }
    100% { opacity: 1; }
}

.highlight {
    animation: pulse 2s infinite;
}

.tooltip {
    position: relative;
    display: inline-block;
}

.tooltip .tooltiptext {
    visibility: hidden;
    width: 200px;
    background-color: #2c3e50;
    color: white;
    text-align: center;
    border-radius: 6px;
    padding: 8px;
    position: absolute;
    z-index: 1;
    bottom: 125%;
    left: 50%;
    margin-left: -100px;
    opacity: 0;
    transition: opacity 0.3s;
    font-size: 0.85rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.tooltip .tooltiptext::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: #2c3e50 transparent transparent transparent;
}

.tooltip:hover .tooltiptext {
    visibility: visible;
    opacity: 1;
}

@media (max-width: 768px) {
    .product-image-container {
        height: 350px;
    }
    
    .product-price {
        font-size: 2rem;
    }
    
    .add-to-cart-btn, .notify-btn {
        padding: 15px;
        font-size: 1rem;
    }
    
    .stock-details {
        padding: 10px;
    }
}
</style>

<div class="product-detail-page">
    <div class="container py-5">
        <div class="product-card p-4">
            <div class="row g-4">
                <!-- Product Image Column -->
                <div class="col-lg-6">
                    <div class="product-image-container position-relative">
                        <img src="<?= htmlspecialchars($product['image_url'] ?? 'assets/images/placeholder.jpg') ?>" 
                             alt="<?= htmlspecialchars($product['name']) ?>" 
                             class="product-image">
                        
                        <div class="product-badges">
                            <span class="product-badge badge-category">
                                <?= htmlspecialchars($product['category_name'] ?? 'Uncategorized') ?>
                            </span>
                            <?php if ($product['featured'] ?? false): ?>
                                <span class="product-badge badge-featured">
                                    <i class="fas fa-star me-1"></i> Featured
                                </span>
                            <?php endif; ?>
                            <!-- Stock Info Badge -->
                            <span class="product-badge badge-stock-info tooltip">
                                <i class="fas fa-info-circle me-1"></i> Stock Info
                                <span class="tooltiptext">
                                    Total: <?= $totalStock ?><br>
                                    Available: <?= $availableStock ?><br>
                                    Reserved: <?= $reservedStock ?>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Product Info Column -->
                <div class="col-lg-6">
                    <!-- Product Title -->
                    <h1 class="display-5 fw-bold text-dark mb-3">
                        <?= htmlspecialchars($product['name']) ?>
                    </h1>
                    
                    <!-- Product Description -->
                    <div class="product-description mb-4">
                        <p class="text-muted fs-5 lh-lg">
                            <?= nl2br(htmlspecialchars($product['description'] ?? '')) ?>
                        </p>
                    </div>
                    
                    <!-- Product Price -->
                    <div class="product-price highlight">
                        <?= CurrencyHelper::format($product['base_price']) ?>
                    </div>
                    
                    <!-- Stock Display -->
                    <div class="stock-indicator <?= $stockClass ?>">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas <?= $stockIcon ?> fa-2x"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 fw-bold"><?= $stockText ?></h4>
                                <p class="mb-2">
                                    <?php if ($availableStock > 0): ?>
                                        <strong><?= $availableStock ?></strong> item(s) available for purchase
                                    <?php else: ?>
                                        This product is currently out of stock
                                    <?php endif; ?>
                                </p>
                                
                                <!-- Stock Details (Visible to all users) -->
                                <div class="stock-details">
                                    <div class="stock-detail-item">
                                        <span class="stock-detail-label">Total Stock:</span>
                                        <span class="stock-detail-value"><?= $totalStock ?></span>
                                    </div>
                                    <div class="stock-detail-item">
                                        <span class="stock-detail-label">Available:</span>
                                        <span class="stock-detail-value text-success fw-bold"><?= $availableStock ?></span>
                                    </div>
                                    <div class="stock-detail-item">
                                        <span class="stock-detail-label">Reserved:</span>
                                        <span class="stock-detail-value"><?= $reservedStock ?></span>
                                    </div>
                                </div>
                                
                                <!-- Warning if cart quantity exceeds available -->
                                <?php 
                                // Check if user has this in cart
                                if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                                    $cartQuantity = 0;
                                    foreach ($_SESSION['cart'] as $cartItem) {
                                        if ($cartItem['product_id'] == $productId && 
                                            ($cartItem['variant_id'] ?? null) == $variantId) {
                                            $cartQuantity = $cartItem['quantity'];
                                            break;
                                        }
                                    }
                                    
                                    if ($cartQuantity > 0 && $cartQuantity > $availableStock): ?>
                                        <div class="stock-warning mt-2">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            Warning: You have <?= $cartQuantity ?> in cart, but only <?= $availableStock ?> available.
                                        </div>
                                    <?php endif;
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Add to Cart Form -->
                    <div class="customization-section">
                        <h3 class="customization-title">
                            <i class="fas fa-palette me-2"></i> Customize Your Product
                        </h3>
                        
                        <form method="POST" action="?page=cart&action=add" id="addToCartForm">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <?php if ($variantId): ?>
                                <input type="hidden" name="variant_id" value="<?= $variantId ?>">
                            <?php endif; ?>
                            
                            <!-- Size Selection -->
                            <div class="mb-4">
                                <label for="size" class="form-label fw-bold fs-5">
                                    <i class="fas fa-ruler-vertical me-2"></i> Size
                                </label>
                                <select name="size" id="size" class="form-select option-select">
                                    <option value="">Select Size</option>
                                    <option value="Small">Small</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Large">Large</option>
                                </select>
                            </div>
                            
                            <!-- Color Selection -->
                            <div class="mb-4">
                                <label for="color" class="form-label fw-bold fs-5">
                                    <i class="fas fa-palette me-2"></i> Color
                                </label>
                                <select name="color" id="color" class="form-select option-select">
                                    <option value="">Select Color</option>
                                    <option value="Red" style="color: #e74c3c;">Red</option>
                                    <option value="Blue" style="color: #3498db;">Blue</option>
                                    <option value="Green" style="color: #27ae60;">Green</option>
                                    <option value="Black" style="color: #2c3e50;">Black</option>
                                    <option value="White" style="color: #95a5a6;">White</option>
                                </select>
                            </div>
                            
                            <!-- Quantity -->
                            <div class="mb-4">
                                <label class="form-label fw-bold fs-5">
                                    <i class="fas fa-layer-group me-2"></i> Quantity
                                </label>
                                <div class="d-flex align-items-center">
                                    <div class="quantity-control input-group me-3">
                                        <button type="button" 
                                                class="btn quantity-btn" 
                                                onclick="changeQuantity(-1)"
                                                <?= $availableStock <= 0 ? 'disabled' : '' ?>>
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" 
                                               name="quantity" 
                                               id="quantity" 
                                               value="1" 
                                               min="1" 
                                               max="<?= $availableStock ?>" 
                                               class="form-control quantity-input fs-5"
                                               onchange="validateQuantity()"
                                               <?= $availableStock <= 0 ? 'disabled' : '' ?>>
                                        <button type="button" 
                                                class="btn quantity-btn" 
                                                onclick="changeQuantity(1)"
                                                <?= $availableStock <= 0 ? 'disabled' : '' ?>>
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <span class="text-muted">
                                        Max: <strong><?= $availableStock ?></strong> available
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Special Instructions -->
                            <div class="mb-4">
                                <label for="instructions" class="form-label fw-bold fs-5">
                                    <i class="fas fa-edit me-2"></i> Special Instructions (Optional)
                                </label>
                                <textarea name="instructions" 
                                          id="instructions" 
                                          class="form-control option-select" 
                                          rows="3" 
                                          placeholder="Tell us about your customization preferences..."></textarea>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="d-grid gap-3">
                                <button type="submit" 
                                        id="addToCartBtn"
                                        class="btn add-to-cart-btn"
                                        <?= $availableStock <= 0 ? 'disabled' : '' ?>>
                                    <i class="fas fa-shopping-cart me-2"></i>
                                    <?php if ($availableStock > 0): ?>
                                        
                                        Add to Cart - <?= CurrencyHelper::format($product['base_price']) ?> CFA
                                    <?php else: ?>
                                        Out of Stock
                                    <?php endif; ?>
                                </button>
                                
                                <?php if ($availableStock <= 0): ?>
                                    <button type="button" class="btn notify-btn" id="notifyBtn">
                                        <i class="fas fa-bell me-2"></i> Notify Me When Available
                                    </button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Product Details -->
                    <div class="product-details-card">
                        <h4 class="fw-bold text-dark mb-4">
                            <i class="fas fa-info-circle me-2"></i> Product Details
                        </h4>
                        
                        <div class="product-details-list">
                            <div class="detail-item">
                                <span class="detail-label">Product ID</span>
                                <span class="detail-value">#<?= $product['id'] ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Category</span>
                                <span class="detail-value"><?= htmlspecialchars($product['category_name']) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Base Price</span>
                                <span class="detail-value text-success fw-bold"><?= CurrencyHelper::format($product['base_price']) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Stock Status</span>
                                <span class="detail-value text-<?= $availableStock > 0 ? ($availableStock > 10 ? 'success' : 'warning') : 'danger' ?> fw-bold">
                                    <?= $availableStock > 0 ? "{$availableStock} available" : "Out of stock" ?>
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Total Inventory</span>
                                <span class="detail-value"><?= $totalStock ?> units</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Reserved Orders</span>
                                <span class="detail-value text-info"><?= $reservedStock ?> units</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
// Stock variables from PHP
const availableStock = <?= $availableStock ?>;
let currentCartQuantity = 0;

// Check if user has this item in cart
<?php 
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $cartQuantity = 0;
    foreach ($_SESSION['cart'] as $cartItem) {
        if ($cartItem['product_id'] == $productId && 
            ($cartItem['variant_id'] ?? null) == $variantId) {
            $cartQuantity = $cartItem['quantity'];
            break;
        }
    }
    echo "currentCartQuantity = $cartQuantity;";
}
?>

function changeQuantity(change) {
    const quantityInput = document.getElementById('quantity');
    const minusBtn = quantityInput.previousElementSibling;
    const plusBtn = quantityInput.nextElementSibling;
    
    let current = parseInt(quantityInput.value) || 1;
    const newValue = current + change;
    
    // Validate against available stock AND current cart quantity
    const maxAvailable = Math.max(0, availableStock - currentCartQuantity);
    
    if (newValue >= 1 && newValue <= maxAvailable) {
        quantityInput.value = newValue;
    }
    
    // Update button states
    updateQuantityButtons();
}

function validateQuantity() {
    const quantityInput = document.getElementById('quantity');
    const minusBtn = quantityInput.previousElementSibling;
    const plusBtn = quantityInput.nextElementSibling;
    
    let value = parseInt(quantityInput.value) || 1;
    const maxAvailable = Math.max(0, availableStock - currentCartQuantity);
    
    if (value < 1) {
        quantityInput.value = 1;
    } else if (value > maxAvailable) {
        quantityInput.value = maxAvailable;
    }
    
    updateQuantityButtons();
}

function updateQuantityButtons() {
    const quantityInput = document.getElementById('quantity');
    const minusBtn = quantityInput.previousElementSibling;
    const plusBtn = quantityInput.nextElementSibling;
    
    const current = parseInt(quantityInput.value) || 1;
    const maxAvailable = Math.max(0, availableStock - currentCartQuantity);
    
    // Disable minus button if quantity is 1
    minusBtn.disabled = current <= 1;
    
    // Disable plus button if quantity equals max available
    plusBtn.disabled = current >= maxAvailable;
    
    // Update input max attribute
    quantityInput.max = maxAvailable;
    
    // Show warning if trying to add more than available
    if (current > maxAvailable) {
        quantityInput.classList.add('border-danger');
        quantityInput.classList.remove('border-success');
    } else {
        quantityInput.classList.remove('border-danger');
        quantityInput.classList.add('border-success');
    }
}

// Form submission with validation
document.getElementById('addToCartForm').addEventListener('submit', function(e) {
    const size = document.getElementById('size').value;
    const color = document.getElementById('color').value;
    const quantity = parseInt(document.getElementById('quantity').value) || 1;
    const maxAvailable = Math.max(0, availableStock - currentCartQuantity);
    
    
    // Validate quantity against available stock
    if (quantity > maxAvailable) {
        e.preventDefault();
        alert(`Only ${maxAvailable} items available (${currentCartQuantity} already in your cart).`);
        return;
    }
    
    if (quantity < 1) {
        e.preventDefault();
        alert('Please select at least 1 item.');
        return;
    }
    
    // Show loading state
    const btn = document.getElementById('addToCartBtn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Adding to Cart...';
    btn.disabled = true;
    
    // Re-enable after 5 seconds if still on page
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }, 5000);
});

// Real-time validation
document.getElementById('size').addEventListener('change', function() {
    this.classList.remove('border-danger', 'border-success');
    this.classList.add(this.value ? 'border-success' : 'border-danger');
});

document.getElementById('color').addEventListener('change', function() {
    this.classList.remove('border-danger', 'border-success');
    this.classList.add(this.value ? 'border-success' : 'border-danger');
});

// Quantity input validation
document.getElementById('quantity').addEventListener('input', function() {
    const maxAvailable = Math.max(0, availableStock - currentCartQuantity);
    if (this.value > maxAvailable) {
        this.value = maxAvailable;
        this.classList.add('border-warning');
    } else {
        this.classList.remove('border-warning');
    }
    
    updateQuantityButtons();
});

// Notify button
document.getElementById('notifyBtn')?.addEventListener('click', function() {
    const email = prompt('Please enter your email to be notified when this product is back in stock:');
    if (email && email.includes('@')) {
        // AJAX call to save notification request
        fetch('?page=product&action=notify', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=<?= $productId ?>&variant_id=<?= $variantId ?>&email=${encodeURIComponent(email)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('You will be notified when this product is back in stock!');
                this.innerHTML = '<i class="fas fa-check me-2"></i> Notification Set';
                this.disabled = true;
            } else {
                alert('Failed to set notification. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    } else if (email !== null) {
        alert('Please enter a valid email address.');
    }
});

// Initialize button states on page load
document.addEventListener('DOMContentLoaded', function() {
    updateQuantityButtons();
    
    // Update max attribute on quantity input
    const quantityInput = document.getElementById('quantity');
    const maxAvailable = Math.max(0, availableStock - currentCartQuantity);
    quantityInput.max = maxAvailable;
    
    // Show warning if user already has maximum in cart
    if (currentCartQuantity > 0 && currentCartQuantity >= availableStock) {
        const warningDiv = document.createElement('div');
        warningDiv.className = 'alert alert-warning mt-3';
        warningDiv.innerHTML = `
            <i class="fas fa-exclamation-triangle me-2"></i>
            You already have ${currentCartQuantity} of this item in your cart, which is all that's available.
        `;
        document.querySelector('.customization-section').appendChild(warningDiv);
    }
});

// Live stock update (optional - for real-time updates)
function checkStockUpdate() {
    fetch(`?page=ajax&action=stock-check&product_id=<?= $productId ?>&variant_id=<?= $variantId ?>`)
        .then(response => response.json())
        .then(data => {
            if (data.available !== availableStock) {
                // Stock changed, update display
                const stockElement = document.querySelector('.stock-indicator h4');
                if (data.available > 0) {
                    stockElement.innerHTML = `${data.available} Available`;
                    document.querySelector('.add-to-cart-btn').disabled = false;
                    document.querySelector('.add-to-cart-btn').innerHTML = 
                        '<i class="fas fa-shopping-cart me-2"></i> Add to Cart';
                }
            }
        })
        .catch(error => console.error('Stock check error:', error));
}

// Check stock every 30 seconds (optional)
// setInterval(checkStockUpdate, 30000);
</script>

<?php include '../app/views/footer.php'; ?>