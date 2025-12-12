<?php
// app/views/cart/checkout.php
include '../app/views/header.php';

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get cart items
$cartItems = $_SESSION['cart'] ?? [];
$cartTotal = 0;

foreach ($cartItems as $item) {
    $cartTotal += ($item['price'] ?? 0) * $item['quantity'];
}
?>

<div class="container mt-4">
    <h1 class="mb-4">Checkout</h1>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <!-- Order Summary -->
        <div class="col-md-5 order-md-2 mb-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Order Summary</h4>
                    
                    <ul class="list-group mb-3">
                        <?php foreach ($cartItems as $index => $item): ?>
                        <li class="list-group-item d-flex justify-content-between lh-condensed">
                            <div>
                                <h6 class="my-0"><?php echo htmlspecialchars($item['product_name'] ?? 'Product'); ?></h6>
                                <small class="text-muted">
                                    <?php 
                                    $details = [];
                                    if (!empty($item['size'])) $details[] = "Size: " . $item['size'];
                                    if (!empty($item['color'])) $details[] = "Color: " . $item['color'];
                                    echo implode(', ', $details);
                                    ?>
                                </small>
                                <br>
                                <small>Quantity: <?php echo $item['quantity']; ?></small>
                            </div>
                            <span class="text-muted">$<?php echo number_format(($item['price'] ?? 0) * $item['quantity'], 2); ?></span>
                        </li>
                        <?php endforeach; ?>
                        
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Total (USD)</span>
                            <strong>$<?php echo number_format($cartTotal, 2); ?></strong>
                        </li>
                    </ul>
                    
                    <a href="?page=cart" class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-arrow-left"></i> Back to Cart
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Checkout Form -->
        <div class="col-md-7 order-md-1">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Shipping Address</h4>
                    
                    <form method="POST" action="?page=checkout&action=process" id="checkoutForm">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="address_line1">Address Line 1 *</label>
                                <input type="text" class="form-control" id="address_line1" 
                                       name="address_line1" required>
                                <div class="invalid-feedback">
                                    Please enter your shipping address.
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address_line2">Address Line 2 <span class="text-muted">(Optional)</span></label>
                            <input type="text" class="form-control" id="address_line2" 
                                   name="address_line2" placeholder="Apartment, suite, etc.">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city">City *</label>
                                <input type="text" class="form-control" id="city" 
                                       name="city" required>
                                <div class="invalid-feedback">
                                    Please provide a valid city.
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="postal_code">Postal Code</label>
                                <input type="text" class="form-control" id="postal_code" 
                                       name="postal_code">
                            </div>
                        </div>
                        
                        <hr class="mb-4">
                        
                        <h4 class="mb-3">Payment Method</h4>
                        
                        <div class="d-block my-3">
                            <div class="custom-control custom-radio">
                                <input id="cash" name="payment_method" type="radio" 
                                       class="custom-control-input" value="cash" checked required>
                                <label class="custom-control-label" for="cash">
                                    <i class="fas fa-money-bill-wave"></i> Cash on Delivery (COD)
                                </label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input id="stripe" name="payment_method" type="radio" 
                                       class="custom-control-input" value="stripe" required>
                                <label class="custom-control-label" for="stripe">
                                    <i class="fab fa-cc-stripe"></i> Credit/Debit Card (Stripe)
                                </label>
                            </div>
                        </div>
                        
                        <!-- Stripe Card Info (Hidden by default) -->
                        <div id="stripe-card-info" class="border p-3 rounded mb-4" style="display: none;">
                            <div class="mb-3">
                                <label for="card-element">Credit/Debit Card</label>
                                <div id="card-element" class="form-control">
                                    <!-- A Stripe Element will be inserted here. -->
                                </div>
                                <div id="card-errors" role="alert" class="text-danger mt-2"></div>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-lock"></i> Your payment is secure and encrypted
                            </small>
                        </div>
                        
                        <hr class="mb-4">
                        
                        <button class="btn btn-primary btn-lg btn-block" type="submit">
                            <i class="fas fa-check-circle"></i> Place Order
                        </button>
                        
                        <p class="mt-3 text-center text-muted">
                            <small>
                                By placing your order, you agree to our 
                                <a href="#">Terms of Service</a> and 
                                <a href="#">Privacy Policy</a>
                            </small>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
/* ===== RAFIASHOP CHECKOUT THEME ===== */
:root {
    --primary-color: #2c3e50;     /* Dark blue - professional */
    --secondary-color: #3498db;   /* Bright blue - accents */
    --accent-color: #e74c3c;      /* Red - calls to action */
    --success-color: #27ae60;     /* Green - success messages */
    --warning-color: #f39c12;     /* Orange - warnings */
    --light-bg: #f8f9fa;          /* Light background */
    --border-color: #e1e5eb;      /* Borders */
    --text-dark: #2c3e50;         /* Dark text */
    --text-light: #7b8a8b;        /* Light text */
}

/* Checkout Page Container */
.checkout-page {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    min-height: 100vh;
}

/* Cards and Summary Box */
.summary-card, .card {
    background: #ffffff;
    border: 1px solid var(--border-color);
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(44, 62, 80, 0.08);
    padding: 25px;
    margin-bottom: 25px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.summary-card:hover, .card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(44, 62, 80, 0.12);
}

.card-title {
    color: var(--primary-color);
    font-weight: 700;
    border-bottom: 2px solid var(--secondary-color);
    padding-bottom: 12px;
    margin-bottom: 20px;
    font-size: 1.5rem;
}

/* Form Styling */
.form-group label {
    color: var(--primary-color);
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.form-control {
    border: 2px solid var(--border-color);
    border-radius: 8px;
    padding: 12px 15px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #fff;
}

.form-control:focus {
    border-color: var(--secondary-color);
    box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
    background: #fff;
}

.form-control::placeholder {
    color: #95a5a6;
    font-style: italic;
}

/* Radio Buttons (Payment Methods) */
.custom-control-input:checked~.custom-control-label::before {
    border-color: var(--secondary-color);
    background-color: var(--secondary-color);
}

.custom-control-label {
    color: var(--text-dark);
    font-weight: 500;
    cursor: pointer;
    padding-left: 5px;
}

.custom-control-label:hover {
    color: var(--secondary-color);
}

/* Payment Method Icons */
.custom-control-label i {
    margin-right: 10px;
    font-size: 1.2rem;
    vertical-align: middle;
}

#cash ~ .custom-control-label i { color: #27ae60; }
#stripe ~ .custom-control-label i { color: #635bff; }

/* Stripe Card Info Section */
#stripe-card-info {
    background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);
    border: 2px dashed #635bff;
    border-radius: 10px;
    padding: 20px;
    margin-top: 15px;
    animation: fadeIn 0.5s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Buttons */
.btn-primary {
    background: linear-gradient(135deg, var(--primary-color) 0%, #1a2530 100%);
    border: none;
    border-radius: 10px;
    padding: 18px;
    font-size: 1.1rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(44, 62, 80, 0.2);
}

.btn-primary:hover {
    background: linear-gradient(135deg, var(--secondary-color) 0%, #2980b9 100%);
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(52, 152, 219, 0.3);
}

.btn-primary:active {
    transform: translateY(-1px);
}

.btn-outline-secondary {
    border: 2px solid var(--border-color);
    color: var(--text-dark);
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    border-color: var(--secondary-color);
    background-color: rgba(52, 152, 219, 0.1);
    color: var(--secondary-color);
}

/* Order Summary Items */
.list-group-item {
    border: none;
    border-bottom: 2px solid var(--border-color);
    padding: 18px 0;
    background: transparent;
    transition: background 0.3s ease;
}

.list-group-item:hover {
    background: rgba(52, 152, 219, 0.05);
}

.list-group-item:last-child {
    border-bottom: none;
}

.list-group-item h6 {
    color: var(--primary-color);
    font-weight: 600;
    margin-bottom: 5px;
}

.list-group-item small {
    color: var(--text-light);
    font-size: 0.9rem;
}

/* Total Row */
.total {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 20px;
    border-radius: 10px;
    margin-top: 15px;
    border: 2px solid var(--border-color);
}

.total strong {
    color: var(--primary-color);
    font-size: 1.2rem;
}

/* Alert Messages */
.alert {
    border-radius: 10px;
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    font-weight: 500;
}

.alert-danger {
    background: linear-gradient(135deg, #ffeaea 0%, #ffd6d6 100%);
    color: #c0392b;
    border-left: 4px solid #e74c3c;
}

.alert-success {
    background: linear-gradient(135deg, #e8f6ef 0%, #d4efdf 100%);
    color: #27ae60;
    border-left: 4px solid #2ecc71;
}

/* Responsive Design */
@media (max-width: 768px) {
    .order-md-1 {
        order: 2;
    }
    .order-md-2 {
        order: 1;
        margin-bottom: 25px;
    }
    
    .card {
        padding: 20px;
    }
    
    .btn-primary {
        padding: 16px;
        font-size: 1rem;
    }
}

@media (max-width: 576px) {
    .card {
        padding: 15px;
    }
    
    .form-control {
        padding: 10px 12px;
    }
    
    .btn-primary {
        padding: 14px;
        font-size: 0.95rem;
    }
}

/* Loading Animation */
.btn-primary:disabled {
    background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
    cursor: not-allowed;
}

.btn-primary i.fa-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Section Dividers */
hr.mb-4 {
    border: none;
    height: 2px;
    background: linear-gradient(to right, transparent, var(--border-color), transparent);
    margin: 30px 0;
}

/* Optional: Add RafiaShop branding */
.checkout-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, #1a2530 100%);
    color: white;
    padding: 25px 0;
    border-radius: 0 0 20px 20px;
    margin-bottom: 40px;
    text-align: center;
}

.checkout-header h1 {
    font-weight: 700;
    margin-bottom: 10px;
    font-size: 2.5rem;
}

.checkout-header p {
    opacity: 0.9;
    font-size: 1.1rem;
}

/* Security Badge */
.security-badge {
    display: inline-flex;
    align-items: center;
    background: #27ae60;
    color: white;
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 0.9rem;
    margin-top: 10px;
}

.security-badge i {
    margin-right: 8px;
}

/* Quantity Badges */
.quantity-badge {
    background: var(--secondary-color);
    color: white;
    border-radius: 50%;
    width: 25px;
    height: 25px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: bold;
    margin-left: 5px;
}
</style>

<script>
// Show/hide Stripe card info based on payment method
document.addEventListener('DOMContentLoaded', function() {
    const cashRadio = document.getElementById('cash');
    const stripeRadio = document.getElementById('stripe');
    const stripeCardInfo = document.getElementById('stripe-card-info');
    
    function toggleStripeCardInfo() {
        if (stripeRadio.checked) {
            stripeCardInfo.style.display = 'block';
        } else {
            stripeCardInfo.style.display = 'none';
        }
    }
    
    cashRadio.addEventListener('change', toggleStripeCardInfo);
    stripeRadio.addEventListener('change', toggleStripeCardInfo);
    
    // Form validation
    const form = document.getElementById('checkoutForm');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        form.classList.add('was-validated');
        
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        submitBtn.disabled = true;
        
        // Re-enable after 5 seconds if still on page
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 5000);
    });
    
    // Address auto-complete (simple example)
    const addressInput = document.getElementById('address_line1');
    addressInput.addEventListener('blur', function() {
        if (this.value && !document.getElementById('city').value) {
            // You could add auto-complete logic here
            // For Niger, you might suggest common cities
        }
    });
});
</script>

<?php include '../app/views/footer.php'; ?>