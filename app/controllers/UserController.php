<?php
class UserController {
    
    // User Dashboard
    public static function dashboard() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: ?page=login');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        
        // Get user's recent orders
        $recentOrders = Order::getByUserId($userId, 5); // Last 5 orders
        $allOrders = Order::getByUserId($userId); // All orders for stats
        
        // Calculate basic stats
        $totalOrders = count($allOrders);
        $pendingOrders = 0;
        $completedOrders = 0;
        
        foreach ($allOrders as $order) {
            if ($order['status'] === 'delivered') {
                $completedOrders++;
            } else if (in_array($order['status'], ['pending', 'confirmed', 'processing'])) {
                $pendingOrders++;
            }
        }
        
        $stats = [
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'completed_orders' => $completedOrders
        ];
        
        include '../app/views/user/dashboard.php';
    }
    
    // Order History
    public static function orderHistory() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?page=login');
            exit;
        }
    
        // Get database connection
        require_once __DIR__ . '/../config/database.php';
        $db = Database::getConnection();
    
        try {
            // Get orders with address info
            $query = "SELECT 
                    o.*,
                    ua.address_line1,
                    ua.city
                    FROM orders o
                    JOIN user_addresses ua ON o.address_id = ua.id
                    WHERE o.user_id = :user_id 
                    ORDER BY o.created_at DESC";
        
            $stmt = $db->prepare($query);
            $stmt->execute([':user_id' => $_SESSION['user_id']]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            require_once __DIR__ . '/../views/user/orders.php';
        
        } catch (PDOException $e) {
            error_log("Error in orderHistory: " . $e->getMessage());
            $_SESSION['error'] = "An error occurred while loading your orders.";
            header('Location: ?page=user&action=dashboard');
            exit();
        }
    }
    
    public static function orderDetails() {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?page=login');
            exit();
        }
    
        // Get order ID from URL
        $order_id = $_GET['id'] ?? 0;
    
        if (!$order_id) {
            header('Location: ?page=user&action=orders');
            exit();
        }
    
        // Get database connection
        require_once __DIR__ . '/../config/database.php';
        $db = Database::getConnection();
    
        try {
            // 1. Get order basic information WITH address details
            $order_query = "SELECT 
                            o.*, 
                            CONCAT(u.first_name, ' ', u.last_name) as user_name, 
                            u.email,
                            u.phone as user_phone,
                            ua.address_line1,
                            ua.address_line2,
                            ua.city,
                            ua.postal_code as pincode
                        FROM orders o 
                        JOIN users u ON o.user_id = u.id 
                        JOIN user_addresses ua ON o.address_id = ua.id
                        WHERE o.id = :order_id AND o.user_id = :user_id";
        
            $order_stmt = $db->prepare($order_query);
            $order_stmt->execute([
                ':order_id' => $order_id,
                ':user_id' => $_SESSION['user_id']
            ]);
        
            $order = $order_stmt->fetch(PDO::FETCH_ASSOC);
        
            if (!$order) {
                $_SESSION['error'] = "Order not found or you don't have permission to view it.";
                header('Location: ?page=user&action=orders');
                exit();
            }
        
            // 2. Get order items with product details
            $items_query = "SELECT 
                            oi.*, 
                            p.name, 
                            oi.unit_price as price,  -- Use unit_price from order_items
                            p.image_url
                        FROM order_items oi
                        JOIN products p ON oi.product_id = p.id
                        WHERE oi.order_id = :order_id";
        
            $items_stmt = $db->prepare($items_query);
            $items_stmt->execute([':order_id' => $order_id]);
            $order_items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
        
            // 3. Calculate totals
            $subtotal = 0;
            foreach ($order_items as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            $shipping = 50; // Fixed shipping cost
            $total = $subtotal + $shipping;
        
            // 4. Set address data for display
            $order['name'] = $order['user_name'];
            $order['state'] = 'State not specified'; // Your table doesn't have state column
            $order['phone'] = $order['user_phone'] ?? 'Not provided';
        
            // 5. Load the order details view
            require_once __DIR__ . '/../views/user/order-details.php';
        
        } catch (PDOException $e) {
            error_log("Database error in orderDetails: " . $e->getMessage());
            $_SESSION['error'] = "An error occurred while fetching the order details.";
            header('Location: ?page=user&action=orders');
            exit();
        }
    }
    
    public static function cancelOrder() {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?page=login');
            exit();
        }
    
        // Get order ID from URL
        $order_id = $_GET['id'] ?? 0;
    
        if (!$order_id) {
            header('Location: ?page=user&action=orders');
            exit();
        }
    
        // Get database connection
        require_once __DIR__ . '/../config/database.php';
        $db = Database::getConnection();
    
        try {
            // Start transaction
            $db->beginTransaction();
        
            // 1. Verify order belongs to user and is pending
            $verify_query = "SELECT id, status FROM orders 
                            WHERE id = :order_id AND user_id = :user_id AND status = 'pending'";
        
            $verify_stmt = $db->prepare($verify_query);
            $verify_stmt->execute([
                ':order_id' => $order_id,
                ':user_id' => $_SESSION['user_id']
            ]);
        
            $order = $verify_stmt->fetch(PDO::FETCH_ASSOC);
        
            if (!$order) {
                $_SESSION['error'] = "Order cannot be cancelled. It may already be processed or doesn't exist.";
                header('Location: ?page=user&action=orders');
                exit();
            }
        
            // 2. Get order items to restore stock
            $items_query = "SELECT product_id, quantity FROM order_items WHERE order_id = :order_id";
            $items_stmt = $db->prepare($items_query);
            $items_stmt->execute([':order_id' => $order_id]);
            $order_items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
        
            // 3. Restore stock for each item
            // 3. Restore stock for each item - use stock_quantity not stock
            foreach ($order_items as $item) {
                $restore_query = "UPDATE products SET stock_quantity = stock_quantity + :quantity WHERE id = :product_id";
                $restore_stmt = $db->prepare($restore_query);
                $restore_stmt->execute([
                    ':quantity' => $item['quantity'],
                    ':product_id' => $item['product_id']
                ]);
            }
            // 4. Update order status to cancelled
            $update_query = "UPDATE orders SET status = 'cancelled', updated_at = NOW() WHERE id = :order_id";
            $update_stmt = $db->prepare($update_query);
            $update_stmt->execute([':order_id' => $order_id]);
        
            // 5. Update payment status if paid
            $payment_query = "UPDATE orders SET payment_status = 'refunded' 
                            WHERE id = :order_id AND payment_status = 'paid'";
            $payment_stmt = $db->prepare($payment_query);
            $payment_stmt->execute([':order_id' => $order_id]);
        
            // Commit transaction
            $db->commit();
        
            $_SESSION['success'] = "Order #$order_id has been cancelled successfully. Stock has been restored.";
        
        } catch (PDOException $e) {
            // Rollback on error
            $db->rollBack();
            error_log("Error cancelling order: " . $e->getMessage());
            $_SESSION['error'] = "An error occurred while cancelling the order.";
        }
    
        header('Location: ?page=user&action=order-details&id=' . $order_id);
        exit();
    }
    
    // Profile Management
    public static function profile() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user'])) {
            header('Location: ?page=login');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        $user = User::getById($userId);
        
        include '../app/views/user/profile.php';
    }
    
    // Update Profile
    public static function updateProfile() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user'])) {
            header('Location: ?page=login');
            exit;
        }
        
        if ($_POST) {
            $userId = $_SESSION['user']['id'];
            $firstName = trim($_POST['first_name']);
            $lastName = trim($_POST['last_name']);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone'] ?? '');
            
            // Basic validation
            if (empty($firstName) || empty($lastName) || empty($email)) {
                $error = "Please fill in all required fields.";
                $user = User::getById($userId);
                include '../app/views/user/profile.php';
                return;
            }
            
            // Update user in database
            if (User::updateProfile($userId, $firstName, $lastName, $email, $phone)) {
                // Update session
                $_SESSION['user']['first_name'] = $firstName;
                $_SESSION['user']['last_name'] = $lastName;
                $_SESSION['user']['email'] = $email;
                
                $success = "Profile updated successfully!";
                $user = User::getById($userId);
                include '../app/views/user/profile.php';
            } else {
                $error = "Failed to update profile. Please try again.";
                $user = User::getById($userId);
                include '../app/views/user/profile.php';
            }
        }
    }
}
?>