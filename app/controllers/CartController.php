<?php
// app/controllers/CartController.php
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Inventory.php';

class CartController {
    
    public static function add() {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if it's a POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['product_id'] ?? '';
            $size = $_POST['size'] ?? '';
            $color = $_POST['color'] ?? '';
            $instructions = $_POST['instructions'] ?? '';
            $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
            
            // Validate product ID
            if (empty($productId)) {
                $_SESSION['error'] = 'Invalid product';
                header('Location: ?page=products');
                exit;
            }
            
            // Validate quantity
            if ($quantity < 1) {
                $quantity = 1;
            }
            
            // Add to cart
            $success = Cart::addItem($productId, $size, $color, $instructions, $quantity);
            
            if ($success) {
                $_SESSION['success'] = 'Product added to cart successfully!';
            } else {
                $_SESSION['error'] = 'Failed to add product to cart. Please try again.';
            }
            
            // Redirect to cart page
            header('Location: ?page=cart');
            exit;
        } else {
            // If not POST, redirect to cart
            header('Location: ?page=cart');
            exit;
        }
    }
    
    public static function view() {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Get cart items
        $cartItems = Cart::getItems();
        
        // Get stock errors if any
        $stock_errors = [];
        if (isset($_SESSION['stock_errors'])) {
            $stock_errors = $_SESSION['stock_errors'];
            unset($_SESSION['stock_errors']);
        }
        
        // Add stock info to each cart item
        if (!empty($cartItems)) {
            $inventory = new Inventory();
            
            foreach ($cartItems as $index => $item) {
                // Check if we need to get variant stock or product stock
                if (!empty($item['variant_id'])) {
                    // Get variant stock
                    $cartItems[$index]['available_stock'] = $inventory->getAvailableStock($item['variant_id']);
                } else {
                    // Get product stock (simplified)
                    $cartItems[$index]['available_stock'] = $item['stock_quantity'] ?? 0;
                }
                
                // Check if item is low stock
                $cartItems[$index]['is_low_stock'] = $cartItems[$index]['available_stock'] <= 5;
                $cartItems[$index]['is_out_of_stock'] = $cartItems[$index]['available_stock'] <= 0;
            }
        }
        
        // Include the view
        include '../app/views/cart/view.php';
    }
    
    public static function remove() {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_GET['index'])) {
            $index = intval($_GET['index']);
            
            // Validate index
            $cartItems = Cart::getItems();
            if ($index >= 0 && $index < count($cartItems)) {
                Cart::removeItem($index);
                $_SESSION['success'] = 'Item removed from cart';
            } else {
                $_SESSION['error'] = 'Invalid item index';
            }
        }
        
        header('Location: ?page=cart');
        exit;
    }
    
    public static function updateQuantity() {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['index'], $_POST['quantity'])) {
            $index = intval($_POST['index']);
            $quantity = intval($_POST['quantity']);
            
            // Validate quantity
            if ($quantity > 0) {
                // Check available stock before updating
                $cartItems = Cart::getItems();
                if (isset($cartItems[$index])) {
                    $item = $cartItems[$index];
                    
                    // Get available stock
                    $inventory = new Inventory();
                    $availableStock = 0;
                    
                    if (!empty($item['variant_id'])) {
                        $availableStock = $inventory->getAvailableStock($item['variant_id']);
                    } else {
                        $availableStock = $item['stock_quantity'] ?? 0;
                    }
                    
                    // Check if requested quantity exceeds available stock
                    if ($quantity > $availableStock) {
                        $_SESSION['error'] = "Only $availableStock items available for this product";
                    } else {
                        // Update quantity
                        $updated = Cart::updateQuantity($index, $quantity);
                        
                        if ($updated) {
                            $_SESSION['success'] = 'Quantity updated';
                        } else {
                            $_SESSION['error'] = 'Failed to update quantity';
                        }
                    }
                } else {
                    $_SESSION['error'] = 'Invalid item';
                }
            } else {
                $_SESSION['error'] = 'Quantity must be at least 1';
            }
        }
        
        header('Location: ?page=cart');
        exit;
    }
    
    public static function checkout() {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Please login to checkout';
            $_SESSION['redirect_to'] = 'checkout';
            header('Location: ?page=login');
            exit;
        }
        
        // Get cart items in validation format
        $cart_items = Cart::getItemsForValidation();
        
        // Check if cart is empty
        if (empty($cart_items)) {
            $_SESSION['error'] = 'Your cart is empty';
            header('Location: ?page=cart');
            exit;
        }
        
        // Stock validation before checkout
        $inventory = new Inventory();
        $stock_errors = $inventory->validateCartStock($cart_items);
        
        if (!empty($stock_errors)) {
            $_SESSION['stock_errors'] = $stock_errors;
            header('Location: ?page=cart');
            exit;
        }
        
        // If stock validation passes, proceed to checkout
        header('Location: ?page=checkout');
        exit;
    }
    
    public static function clear() {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        Cart::clear();
        $_SESSION['success'] = 'Cart cleared successfully';
        
        header('Location: ?page=cart');
        exit;
    }
    
    public static function getCartCount() {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        echo json_encode(['count' => Cart::getCount()]);
        exit;
    }

    

    public static function show() {
        $cartItems = Cart::getItems();
    
        // Initialize empty stock_errors array
        $stock_errors = [];
    
        // Check if there are stock errors from previous checkout attempt
        if (isset($_SESSION['stock_errors'])) {
            // Make sure it's an array
            $stock_errors = is_array($_SESSION['stock_errors']) ? $_SESSION['stock_errors'] : [];
            unset($_SESSION['stock_errors']); // Clear after displaying
        }
    
        // Debug: Log what we're sending to the view
        error_log("CartController::show() - Stock errors: " . print_r($stock_errors, true));
    
        // Include view with proper variables
        include '../app/views/cart/view.php';
    }
}
