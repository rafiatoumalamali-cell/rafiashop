<?php
// app/models/Order.php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Product.php';
require_once __DIR__ . '/Inventory.php';

class Order {
    private static $db;
    
    private static function getDB() {
        if (self::$db === null) {
            self::$db = Database::getConnection();
        }
        return self::$db;
    }
    
    // Create new order (updated for hybrid system)
    public static function create($orderData) {
        $pdo = self::getDB();
        $pdo->beginTransaction();
        
        try {
            $userId = $orderData['user_id'];
            $addressData = $orderData['address_data'];
            $cartItems = $orderData['cart_items'];
            $paymentMethod = $orderData['payment_method'] ?? 'cash';
            $total = $orderData['total'] ?? 0;
            
            // 1. Create user address
            $addressSql = "INSERT INTO user_addresses (user_id, address_line1, address_line2, city, postal_code, is_default) 
                          VALUES (?, ?, ?, ?, ?, ?)";
            Database::query($addressSql, [
                $userId, 
                $addressData['address_line1'],
                $addressData['address_line2'] ?? '',
                $addressData['city'], 
                $addressData['postal_code'] ?? '',
                1 // Mark as default
            ]);
            $addressId = $pdo->lastInsertId();
            
            // 2. Create order with payment method
            $orderSql = "INSERT INTO orders (user_id, address_id, status, payment_method, payment_status) 
                        VALUES (?, ?, 'pending', ?, 'pending')";
            Database::query($orderSql, [$userId, $addressId, $paymentMethod]);
            $orderId = $pdo->lastInsertId();
            
            // 3. Add order items with variant_id
            foreach ($cartItems as $item) {
                $product = Product::getById($item['product_id']);
                
                // Prepare custom notes
                $customNotes = "";
                if (!empty($item['size']) || !empty($item['color'])) {
                    $customNotes = "Size: {$item['size']}, Color: {$item['color']}";
                }
                if (!empty($item['instructions'])) {
                    $customNotes .= ". " . $item['instructions'];
                }
                
                $itemSql = "INSERT INTO order_items 
                           (order_id, product_id, variant_id, quantity, unit_price, custom_notes) 
                           VALUES (?, ?, ?, ?, ?, ?)";
                
                Database::query($itemSql, [
                    $orderId,
                    $item['product_id'],
                    $item['variant_id'] ?? null,
                    $item['quantity'],
                    $product['base_price'],
                    $customNotes
                ]);
            }
            
            $pdo->commit();
            return $orderId;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Order creation failed: " . $e->getMessage());
            throw new Exception("Failed to create order: " . $e->getMessage());
        }
    }
    
    // Update order status (hybrid stock management)
    public static function updateStatus($orderId, $newStatus, $adminId = null) {
        $pdo = self::getDB();
        
        try {
            // Get current order info
            $query = "SELECT status, payment_method FROM orders WHERE id = :order_id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':order_id', $orderId);
            $stmt->execute();
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$order) {
                throw new Exception("Order not found");
            }
            
            $oldStatus = $order['status'];
            
            // Update status
            $query = "UPDATE orders SET status = :status, updated_at = NOW() WHERE id = :order_id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':status', $newStatus);
            $stmt->bindParam(':order_id', $orderId);
            $stmt->execute();
            
            // 🔥 HYBRID STOCK MANAGEMENT
            $inventory = new Inventory();
            
            // Stock reduction when order is confirmed
            if ($newStatus === 'confirmed' && $oldStatus !== 'confirmed') {
                if ($order['payment_method'] === 'cod') {
                    // For COD orders: admin confirms → reduce stock
                    $inventory->reduceStockForOrder($orderId);
                }
                // Stripe orders already reduced stock in paymentSuccess()
            }
            
            // Stock release/restoration when order is cancelled
            if ($newStatus === 'cancelled') {
                if ($oldStatus === 'pending') {
                    // Release reserved stock
                    $inventory->releaseReservedStock($orderId);
                } elseif ($oldStatus === 'confirmed') {
                    // Restore stock (if it was reduced)
                    // We need to add restoreStockFromOrder method to Inventory
                    // For now, we'll create a simpler restoration
                    self::restoreStockForOrder($orderId);
                }
            }
            
            // Stock restoration when order is returned
            if ($newStatus === 'returned' && $oldStatus === 'delivered') {
                self::restoreStockForOrder($orderId);
            }
            
            // Log status change
            self::logStatusChange($orderId, $oldStatus, $newStatus, $adminId);
            
            return true;
            
        } catch (Exception $e) {
            error_log("Status update failed: " . $e->getMessage());
            return false;
        }
    }
    
    // Helper: Restore stock for cancelled/returned order
    private static function restoreStockForOrder($orderId) {
        try {
            $inventory = new Inventory();
            $pdo = self::getDB();
            
            // Get order items
            $query = "SELECT oi.*, pv.id as variant_id 
                     FROM order_items oi
                     LEFT JOIN product_variants pv ON oi.variant_id = pv.id
                     WHERE oi.order_id = :order_id";
            
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':order_id', $orderId);
            $stmt->execute();
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($items as $item) {
                if ($item['variant_id']) {
                    // We need to add a stock increase method to Inventory
                    // For now, we'll update directly
                    $updateQuery = "UPDATE product_variants 
                                   SET stock_quantity = stock_quantity + :quantity 
                                   WHERE id = :variant_id";
                    
                    $updateStmt = $pdo->prepare($updateQuery);
                    $updateStmt->bindParam(':quantity', $item['quantity']);
                    $updateStmt->bindParam(':variant_id', $item['variant_id']);
                    $updateStmt->execute();
                    
                    // Log the restoration
                    self::logStockRestoration($item['variant_id'], $item['quantity'], $orderId);
                }
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Stock restoration failed: " . $e->getMessage());
            return false;
        }
    }
    
    // Log status change
    private static function logStatusChange($orderId, $oldStatus, $newStatus, $adminId = null) {
        try {
            $pdo = self::getDB();
            $query = "INSERT INTO order_status_logs 
                     (order_id, old_status, new_status, changed_by, created_at) 
                     VALUES (?, ?, ?, ?, NOW())";
            
            Database::query($query, [$orderId, $oldStatus, $newStatus, $adminId]);
            
        } catch (Exception $e) {
            error_log("Failed to log status change: " . $e->getMessage());
        }
    }
    
    // Log stock restoration
    private static function logStockRestoration($variantId, $quantity, $orderId) {
        try {
            $pdo = self::getDB();
            
            // Get product info
            $query = "SELECT pv.*, p.name as product_name 
                     FROM product_variants pv
                     JOIN products p ON pv.product_id = p.id
                     WHERE pv.id = :variant_id";
            
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':variant_id', $variantId);
            $stmt->execute();
            $variant = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($variant) {
                // Log in stock_movements table
                $logQuery = "INSERT INTO stock_movements 
                            (product_id, variant_id, movement_type, quantity, reason, product_name, created_at) 
                            VALUES (?, ?, 'return', ?, ?, ?, NOW())";
                
                Database::query($logQuery, [
                    $variant['product_id'],
                    $variantId,
                    $quantity,
                    "Order #{$orderId} cancelled/returned",
                    $variant['product_name']
                ]);
            }
            
        } catch (Exception $e) {
            error_log("Failed to log stock restoration: " . $e->getMessage());
        }
    }
    
    // Get user's orders
    public static function getByUserId($userId, $limit = null) {
        $sql = "SELECT o.*, ua.address_line1, ua.city, ua.postal_code 
                FROM orders o 
                JOIN user_addresses ua ON o.address_id = ua.id 
                WHERE o.user_id = ? 
                ORDER BY o.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        return Database::fetchAll($sql, [$userId]);
    }
    
    // Get order details
    public static function getById($orderId) {
        $sql = "SELECT o.*, ua.address_line1, ua.address_line2, ua.city, ua.postal_code 
                FROM orders o 
                JOIN user_addresses ua ON o.address_id = ua.id 
                WHERE o.id = ?";
        return Database::fetch($sql, [$orderId]);
    }
    
    // Get order items with product info
    public static function getItems($orderId) {
        $sql = "SELECT oi.*, p.name as product_name, p.image_url,
                       s.name as size_name, c.name as color_name
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id
                LEFT JOIN product_variants pv ON oi.variant_id = pv.id
                LEFT JOIN sizes s ON pv.size_id = s.id
                LEFT JOIN colors c ON pv.color_id = c.id
                WHERE oi.order_id = ?";
        return Database::fetchAll($sql, [$orderId]);
    }
    
    // Get all orders (for admin)
    public static function getAll($status = null, $limit = 50) {
        $sql = "SELECT o.*, u.email, u.first_name, u.last_name,
                       ua.address_line1, ua.city
                FROM orders o
                JOIN users u ON o.user_id = u.id
                JOIN user_addresses ua ON o.address_id = ua.id";
        
        $params = [];
        if ($status) {
            $sql .= " WHERE o.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY o.created_at DESC LIMIT ?";
        $params[] = $limit;
        
        return Database::fetchAll($sql, $params);
    }
    
    // Update payment status
    public static function updatePaymentStatus($orderId, $paymentStatus) {
        $sql = "UPDATE orders SET payment_status = ?, updated_at = NOW() WHERE id = ?";
        return Database::query($sql, [$paymentStatus, $orderId]);
    }
    
    // Get order status
    public static function getStatus($orderId) {
        $sql = "SELECT status FROM orders WHERE id = ?";
        $result = Database::fetch($sql, [$orderId]);
        return $result ? $result['status'] : null;
    }

    
}
?>