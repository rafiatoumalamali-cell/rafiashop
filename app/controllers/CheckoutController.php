<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// app/controllers/CheckoutController.php
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Inventory.php';

class CheckoutController {
    
    public static function show() {
        // Start session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        // DEBUG: Log session info
        error_log("=== CheckoutController::show() ===");
        error_log("Session user_id: " . ($_SESSION['user_id'] ?? 'NOT SET'));
        error_log("Session user: " . print_r($_SESSION['user'] ?? 'NOT SET', true));
        error_log("========================");
    
        // Check if user is logged in - check BOTH possible session formats
        $isLoggedIn = false;
        $userId = null;
    
        if (isset($_SESSION['user_id'])) {
            $isLoggedIn = true;
            $userId = $_SESSION['user_id'];
            error_log("User logged in via user_id: $userId");
        } elseif (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
            $isLoggedIn = true;
            $userId = $_SESSION['user']['id'];
            $_SESSION['user_id'] = $userId; // Set for consistency
            error_log("User logged in via user array, ID: $userId");
        }
    
        if (!$isLoggedIn) {
            $_SESSION['error'] = 'Please login to checkout';
            $_SESSION['redirect_to'] = 'checkout';
            error_log("User NOT logged in, redirecting to login");
            header('Location: ?page=login');
            exit;
        }
    
        // Get cart items
        $cartItems = Cart::getItems();
    
        // Check if cart is empty
        if (empty($cartItems)) {
            $_SESSION['error'] = 'Your cart is empty';
            header('Location: ?page=cart');
            exit;
        }
    
        // Validate stock - FIXED THIS PART
        $cart_items = Cart::getItemsForValidation();
        $inventory = new Inventory();
        $stockValidation = $inventory->validateCartStock($cart_items);
    
        if (!$stockValidation['valid']) {
            // Store the insufficient stock items (not the entire validation result)
            $_SESSION['stock_errors'] = $stockValidation['insufficient_stock'] ?? [];
            $_SESSION['error'] = 'Some items in your cart have insufficient stock';
        
            error_log("Stock validation failed: " . print_r($stockValidation, true));
            header('Location: ?page=cart');
            exit;
        }
    
        // Get user addresses if needed for checkout - FIXED WITH ERROR HANDLING
        $userAddresses = [];
        try {
            if (file_exists(__DIR__ . '/../models/User.php')) {
                require_once __DIR__ . '/../models/User.php';
                
                // Check if method exists
                if (method_exists('User', 'getUserAddresses')) {
                    $userAddresses = User::getUserAddresses($userId);
                } else {
                    // Fallback: Query directly if method doesn't exist
                    $userAddresses = self::getUserAddressesFallback($userId);
                }
            } else {
                // User model doesn't exist, use fallback
                $userAddresses = self::getUserAddressesFallback($userId);
            }
        } catch (Exception $e) {
            error_log("Error getting user addresses: " . $e->getMessage());
            $userAddresses = [];
        }
    
        // Get cart total
        $cartTotal = Cart::getTotal();
    
        // Include checkout view
        error_log("Loading checkout view...");
        include '../app/views/cart/checkout.php';
    }
    
    // Fallback method if User::getUserAddresses doesn't exist
    private static function getUserAddressesFallback($userId) {
        try {
            $db = Database::getConnection();
            
            $query = "SELECT * FROM user_addresses WHERE user_id = :user_id ORDER BY is_default DESC, created_at DESC";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Fallback address query failed: " . $e->getMessage());
            return [];
        }
    }
    
    public static function process() {
        // Start session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id']) && !isset($_SESSION['user'])) {
            $_SESSION['error'] = 'Please login to checkout';
            $_SESSION['redirect_to'] = 'checkout';
            header('Location: ?page=login');
            exit;
        }
        
        // Get user ID
        $userId = $_SESSION['user_id'] ?? ($_SESSION['user']['id'] ?? 0);
        
        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?page=checkout');
            exit;
        }
        
        // Get form data
        $paymentMethod = $_POST['payment_method'] ?? 'cash';
        $addressData = [
            'address_line1' => trim($_POST['address_line1'] ?? ''),
            'address_line2' => trim($_POST['address_line2'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'postal_code' => trim($_POST['postal_code'] ?? '')
        ];
        
        // Validate address
        if (empty($addressData['address_line1']) || empty($addressData['city'])) {
            $_SESSION['error'] = 'Please fill in all required address fields';
            header('Location: ?page=checkout');
            exit;
        }
        
        // Get cart items
        $cartItems = Cart::getItems();
        $cartTotal = Cart::getTotal();
        
        // Check if cart is empty
        if (empty($cartItems)) {
            $_SESSION['error'] = 'Your cart is empty';
            header('Location: ?page=cart');
            exit;
        }
        
        // Final stock validation - FIXED: Use proper validation structure
        $cart_items = Cart::getItemsForValidation();
        $inventory = new Inventory();
        $stockValidation = $inventory->validateCartStock($cart_items);
        
        if (!$stockValidation['valid']) {
            $_SESSION['stock_errors'] = $stockValidation['insufficient_stock'] ?? [];
            $_SESSION['error'] = 'Some items have insufficient stock';
            header('Location: ?page=cart');
            exit;
        }
        
        try {
            // Create order
            $orderData = [
                'user_id' => $userId,
                'address_data' => $addressData,
                'cart_items' => $cartItems,
                'payment_method' => $paymentMethod,
                'total' => $cartTotal
            ];
            
            error_log("Creating order with data: " . print_r($orderData, true));
            
            // Note: Your Order::create() method should accept this array format
            // If not, update it to handle this structure
            $orderId = Order::create($orderData);
            
            if (!$orderId) {
                throw new Exception("Failed to create order");
            }
            
            error_log("Order created successfully: #$orderId");
            
            // Prepare cart items for reservation
            $cartItemsForReservation = [];
            foreach ($cartItems as $item) {
                $cartItemsForReservation[] = [
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'quantity' => $item['quantity']
                ];
            }
            
            error_log("Cart items for reservation: " . print_r($cartItemsForReservation, true));
            
            // Handle payment method
            if ($paymentMethod === 'stripe') {
                // Check if Payment::createStripeSession exists
                if (!method_exists('Payment', 'createStripeSession')) {
                    throw new Exception("Stripe payment not configured");
                }
                
                // Reserve stock for 1 hour
                if (method_exists($inventory, 'reserveStockForOrder')) {
                    $reservationResult = $inventory->reserveStockForOrder($orderId, $cartItemsForReservation);
                    error_log("Stripe stock reservation result: " . ($reservationResult ? 'SUCCESS' : 'FAILED'));
                }
                
                // Create Stripe session
                $sessionId = Payment::createStripeSession($orderId, $cartTotal);
                $stripeKey = Payment::getPublishableKey();
                
                $_SESSION['pending_order'] = $orderId;
                $_SESSION['stripe_data'] = [
                    'sessionId' => $sessionId,
                    'stripeKey' => $stripeKey,
                    'orderId' => $orderId
                ];
                
                header('Location: ?page=payment&action=stripe-checkout');
                exit;
                
            } elseif ($paymentMethod === 'cash' || $paymentMethod === 'cod') {
                // For COD: Reserve stock for 24 hours
                if (method_exists($inventory, 'reserveStockForOrder')) {
                    $reservationResult = $inventory->reserveStockForOrder($orderId, $cartItemsForReservation);
                    error_log("COD stock reservation result: " . ($reservationResult ? 'SUCCESS' : 'FAILED'));
                }
                
                // Clear cart
                Cart::clear();
                
                // Update order status to pending (for COD)
                if (method_exists('Order', 'updateStatus')) {
                    Order::updateStatus($orderId, 'pending');
                }
                
                header('Location: ?page=order-confirmation&id=' . $orderId . '&payment=cod');
                exit;
            } else {
                throw new Exception("Invalid payment method: $paymentMethod");
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Order failed: ' . $e->getMessage();
            error_log("Checkout process error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            header('Location: ?page=checkout');
            exit;
        }
    }
    
    public static function stripeCheckout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['stripe_data'])) {
            $_SESSION['error'] = 'No payment session found';
            header('Location: ?page=checkout');
            exit;
        }
        
        $stripeData = $_SESSION['stripe_data'];
        include '../app/views/payment/stripe-checkout.php';
    }
    
    public static function paymentSuccess() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $sessionId = $_GET['session_id'] ?? '';
        $orderId = $_GET['order_id'] ?? 0;
        
        error_log("=== Payment Success ===");
        error_log("Session ID: $sessionId");
        error_log("Order ID: $orderId");
        
        if (empty($sessionId) || empty($orderId)) {
            $_SESSION['error'] = 'Invalid payment confirmation';
            header('Location: ?page=cart');
            exit;
        }
        
        // Check if Payment::verifyStripePayment exists
        if (!method_exists('Payment', 'verifyStripePayment')) {
            $_SESSION['error'] = 'Payment system not configured';
            header('Location: ?page=cart');
            exit;
        }
        
        // Verify payment with Stripe
        $paymentVerified = Payment::verifyStripePayment($sessionId);
        
        if ($paymentVerified) {
            // Payment successful
            if (method_exists('Order', 'updateStatus')) {
                Order::updateStatus($orderId, 'confirmed');
            }
            
            // Reduce stock using the new method
            $inventory = new Inventory();
            if (method_exists($inventory, 'confirmOrderStock')) {
                $stockReduced = $inventory->confirmOrderStock($orderId);
                
                if (!$stockReduced) {
                    error_log("WARNING: Stock reduction failed for order #$orderId");
                    $_SESSION['warning'] = "Stock adjustment may need manual review for order #$orderId";
                } else {
                    error_log("Stock reduced successfully for order #$orderId");
                }
            } else {
                // Fallback to old method
                error_log("confirmOrderStock method not found, using old method");
                if (method_exists($inventory, 'reduceStockForOrder')) {
                    $inventory->reduceStockForOrder($orderId);
                }
            }
            
            // Clear cart and session data
            Cart::clear();
            unset($_SESSION['pending_order']);
            unset($_SESSION['stripe_data']);
            
            // Redirect to confirmation
            header('Location: ?page=order-confirmation&id=' . $orderId . '&payment=stripe');
            exit;
        } else {
            // Payment verification failed
            error_log("Payment verification failed for session: $sessionId");
            
            // Release reserved stock
            if ($orderId) {
                $inventory = new Inventory();
                if (method_exists($inventory, 'releaseReservedStock')) {
                    $inventory->releaseReservedStock($orderId);
                }
            }
            
            $_SESSION['error'] = 'Payment verification failed. Please contact support.';
            header('Location: ?page=payment-failed&order_id=' . $orderId);
            exit;
        }
    }
    
    public static function paymentCancel() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $orderId = $_GET['order_id'] ?? 0;
        
        if ($orderId) {
            // Release reserved stock
            $inventory = new Inventory();
            if (method_exists($inventory, 'releaseReservedStock')) {
                $inventory->releaseReservedStock($orderId);
            }
        }
        
        $_SESSION['warning'] = 'Payment was cancelled. Stock has been released.';
        header('Location: ?page=cart');
        exit;
    }
    
    public static function paymentFailed() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $orderId = $_GET['order_id'] ?? 0;
        
        if ($orderId) {
            // Release reserved stock
            $inventory = new Inventory();
            if (method_exists($inventory, 'releaseReservedStock')) {
                $inventory->releaseReservedStock($orderId);
            }
        }
        
        $_SESSION['error'] = 'Payment failed. Please try again or contact support.';
        header('Location: ?page=checkout');
        exit;
    }
    
    public static function confirmation() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $orderId = $_GET['id'] ?? 0;
        $paymentMethod = $_GET['payment'] ?? 'cash';
        
        if (!$orderId) {
            $_SESSION['error'] = 'Invalid order';
            header('Location: ?page=cart');
            exit;
        }
        
        // Get order details
        if (!method_exists('Order', 'getById')) {
            $_SESSION['error'] = 'Order system error';
            header('Location: ?page=cart');
            exit;
        }
        
        $order = Order::getById($orderId);
        
        if (!$order) {
            $_SESSION['error'] = 'Order not found';
            header('Location: ?page=cart');
            exit;
        }
        
        // Check if user owns this order
        $userId = $_SESSION['user_id'] ?? ($_SESSION['user']['id'] ?? 0);
        if ($order['user_id'] != $userId) {
            $_SESSION['error'] = 'Access denied';
            header('Location: ?page=user-dashboard');
            exit;
        }
        
        // Get order items
        $orderItems = Order::getItems($orderId);
        
        include '../app/views/cart/confirmation.php';
    }
    
    // Helper method to check login status
    private static function checkLogin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['user_id'])) {
            return $_SESSION['user_id'];
        }
        
        if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
            $_SESSION['user_id'] = $_SESSION['user']['id']; // Set for consistency
            return $_SESSION['user']['id'];
        }
        
        return false;
    }
}
?>