<?php
class Inventory {
    private $db;
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    // Get all products with their variants and stock
    public function getAllInventory($page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;
        
        $query = "
            SELECT 
                p.id AS product_id,
                p.name AS product_name,
                p.image_url,
                p.base_price,
                p.stock_quantity as base_stock,
                c.name AS category_name,
                IFNULL(pv.id, 0) AS has_variants,
                IFNULL(GROUP_CONCAT(
                    CONCAT(
                        'Size: ', IFNULL(sz.name, 'N/A'),
                        ', Color: ', IFNULL(cl.name, 'N/A'),
                        ', Material: ', IFNULL(m.name, 'N/A'),
                        ' (Total: ', IFNULL(pv.stock_quantity, p.stock_quantity),
                        ', Reserved: ', IFNULL(pv.reserved_quantity, 0),
                        ', Available: ', IFNULL(pv.stock_quantity, p.stock_quantity) - IFNULL(pv.reserved_quantity, 0),
                        ')'
                    ) SEPARATOR ' | '
                ), 'Base Product') AS variant_details,
                COALESCE(
                    (SELECT SUM(stock_quantity) FROM product_variants WHERE product_id = p.id),
                    p.stock_quantity
                ) AS total_stock,
                COALESCE(
                    (SELECT SUM(reserved_quantity) FROM product_variants WHERE product_id = p.id),
                    0
                ) AS total_reserved,
                COALESCE(
                    (SELECT SUM(stock_quantity) - SUM(reserved_quantity) 
                     FROM product_variants 
                     WHERE product_id = p.id),
                    p.stock_quantity
                ) AS total_available,
                COALESCE(
                    (SELECT COUNT(*) FROM low_stock_alerts lsa 
                     JOIN product_variants pv2 ON lsa.variant_id = pv2.id 
                     WHERE pv2.product_id = p.id),
                    0
                ) AS low_stock_alerts_count
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN product_variants pv ON p.id = pv.product_id
            LEFT JOIN sizes sz ON pv.size_id = sz.id
            LEFT JOIN colors cl ON pv.color_id = cl.id
            LEFT JOIN materials m ON pv.material_id = m.id
            GROUP BY p.id
            ORDER BY p.name ASC
            LIMIT :offset, :perPage
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get total count for pagination
    public function getTotalProducts() {
        $query = "SELECT COUNT(*) as total FROM products";
        $stmt = $this->db->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    // Get low stock items - ADD THIS METHOD
    public function getLowStockItems($threshold = 5) {
        $query = "
            SELECT 
                p.id AS product_id,
                p.name AS product_name,
                p.image_url,
                IFNULL(pv.id, 'base') AS variant_id,
                IFNULL(
                    CONCAT('Size: ', sz.name, ', Color: ', cl.name, ', Material: ', m.name),
                    'Base Product'
                ) AS variant_info,
                COALESCE(pv.stock_quantity, p.stock_quantity) AS current_stock,
                COALESCE(pv.reserved_quantity, 0) AS reserved_stock,
                (COALESCE(pv.stock_quantity, p.stock_quantity) - COALESCE(pv.reserved_quantity, 0)) AS available_stock,
                COALESCE(pv.low_stock_threshold, :threshold) AS threshold,
                -- FIXED: Calculate below threshold properly
                (COALESCE(pv.low_stock_threshold, :threshold) - 
                (COALESCE(pv.stock_quantity, p.stock_quantity) - COALESCE(pv.reserved_quantity, 0))) AS below_threshold_by,
                c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN product_variants pv ON p.id = pv.product_id
            LEFT JOIN sizes sz ON pv.size_id = sz.id
            LEFT JOIN colors cl ON pv.color_id = cl.id
            LEFT JOIN materials m ON pv.material_id = m.id
            WHERE 
                (COALESCE(pv.stock_quantity, p.stock_quantity) - COALESCE(pv.reserved_quantity, 0)) < COALESCE(pv.low_stock_threshold, :threshold)
                AND (COALESCE(pv.stock_quantity, p.stock_quantity) - COALESCE(pv.reserved_quantity, 0)) > 0
            ORDER BY available_stock ASC
        ";
    
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':threshold', $threshold, PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get out of stock items
    public function getOutOfStockItems() {
        $query = "
            SELECT 
                p.id AS product_id,
                p.name AS product_name,
                p.image_url,
                IFNULL(pv.id, 'base') AS variant_id,
                IFNULL(
                    CONCAT('Size: ', sz.name, ', Color: ', cl.name, ', Material: ', m.name),
                    'Base Product'
                ) AS variant_info,
                COALESCE(pv.stock_quantity, p.stock_quantity) AS current_stock,
                COALESCE(pv.reserved_quantity, 0) AS reserved_stock,
                (COALESCE(pv.stock_quantity, p.stock_quantity) - COALESCE(pv.reserved_quantity, 0)) AS available_stock,
                c.name AS category_name,
                p.created_at
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN product_variants pv ON p.id = pv.product_id
            LEFT JOIN sizes sz ON pv.size_id = sz.id
            LEFT JOIN colors cl ON pv.color_id = cl.id
            LEFT JOIN materials m ON pv.material_id = m.id
            WHERE (COALESCE(pv.stock_quantity, p.stock_quantity) - COALESCE(pv.reserved_quantity, 0)) <= 0
            ORDER BY p.created_at DESC
        ";
        
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get stock movement history
    public function getStockMovements($limit = 50) {
        $query = "
            SELECT 
                sm.*,
                p.name AS product_name,
                p.image_url,
                IFNULL(
                    CONCAT('Size: ', sz.name, ', Color: ', cl.name, ', Material: ', m.name),
                    'Base Product'
                ) AS variant_info,
                u.first_name,
                u.last_name
            FROM stock_movements sm
            LEFT JOIN products p ON sm.product_id = p.id
            LEFT JOIN product_variants pv ON sm.variant_id = pv.id
            LEFT JOIN sizes sz ON pv.size_id = sz.id
            LEFT JOIN colors cl ON pv.color_id = cl.id
            LEFT JOIN materials m ON pv.material_id = m.id
            LEFT JOIN users u ON sm.reference_id = u.id
            ORDER BY sm.created_at DESC
            LIMIT :limit
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Update stock for product or variant
    public function updateStock($productId, $newQuantity, $variantId = null, $reason = 'Manual adjustment', $referenceId = null) {
        try {
            $this->db->beginTransaction();
            
            // Get current stock and product name
            if ($variantId) {
                $query = "SELECT pv.stock_quantity, p.name 
                         FROM product_variants pv
                         JOIN products p ON pv.product_id = p.id
                         WHERE pv.id = :variant_id";
                $stmt = $this->db->prepare($query);
                $stmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
                $stmt->execute();
                $current = $stmt->fetch(PDO::FETCH_ASSOC);
                $previousQuantity = $current['stock_quantity'];
                $productName = $current['name'];
                $actualProductId = $productId;
                
                // Update variant stock
                $updateQuery = "UPDATE product_variants SET stock_quantity = :quantity WHERE id = :variant_id";
                $updateStmt = $this->db->prepare($updateQuery);
                $updateStmt->bindValue(':quantity', $newQuantity, PDO::PARAM_INT);
                $updateStmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
                $updateStmt->execute();
                
            } else {
                $query = "SELECT stock_quantity, name FROM products WHERE id = :product_id";
                $stmt = $this->db->prepare($query);
                $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
                $stmt->execute();
                $current = $stmt->fetch(PDO::FETCH_ASSOC);
                $previousQuantity = $current['stock_quantity'];
                $productName = $current['name'];
                $actualProductId = $productId;
                
                // Update product stock
                $updateQuery = "UPDATE products SET stock_quantity = :quantity WHERE id = :product_id";
                $updateStmt = $this->db->prepare($updateQuery);
                $updateStmt->bindValue(':quantity', $newQuantity, PDO::PARAM_INT);
                $updateStmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
                $updateStmt->execute();
            }
            
            // Log stock movement
            $movementType = $newQuantity > $previousQuantity ? 'in' : ($newQuantity < $previousQuantity ? 'out' : 'adjustment');
            $quantityChanged = abs($newQuantity - $previousQuantity);
            
            $logQuery = "
                INSERT INTO stock_movements 
                (product_id, variant_id, movement_type, quantity, previous_quantity, new_quantity, reason, reference_id, product_name)
                VALUES (:product_id, :variant_id, :movement_type, :quantity, :previous_quantity, :new_quantity, :reason, :reference_id, :product_name)
            ";
            
            $logStmt = $this->db->prepare($logQuery);
            $logStmt->bindValue(':product_id', $actualProductId, PDO::PARAM_INT);
            $logStmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
            $logStmt->bindValue(':movement_type', $movementType);
            $logStmt->bindValue(':quantity', $quantityChanged, PDO::PARAM_INT);
            $logStmt->bindValue(':previous_quantity', $previousQuantity, PDO::PARAM_INT);
            $logStmt->bindValue(':new_quantity', $newQuantity, PDO::PARAM_INT);
            $logStmt->bindValue(':reason', $reason);
            $logStmt->bindValue(':reference_id', $referenceId, PDO::PARAM_INT);
            $logStmt->bindValue(':product_name', $productName);
            $logStmt->execute();
            
            // Check and update low stock alerts
            $this->updateLowStockAlerts($actualProductId, $variantId, $newQuantity);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Inventory update failed: " . $e->getMessage());
            return false;
        }
    }
    
    // Update low stock alerts
    private function updateLowStockAlerts($productId, $variantId = null, $newQuantity = 0) {
        $threshold = 5;
        
        if ($variantId) {
            // Get variant threshold
            $query = "SELECT low_stock_threshold, reserved_quantity FROM product_variants WHERE id = :variant_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
            $stmt->execute();
            $variant = $stmt->fetch(PDO::FETCH_ASSOC);
            $threshold = $variant['low_stock_threshold'] ?? 5;
            $reserved = $variant['reserved_quantity'] ?? 0;
            $availableStock = $newQuantity - $reserved;
            
            // Check if alert exists
            $checkQuery = "SELECT id FROM low_stock_alerts WHERE variant_id = :variant_id";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
            $checkStmt->execute();
            
            if ($availableStock < $threshold && $availableStock > 0) {
                if ($checkStmt->rowCount() > 0) {
                    $updateQuery = "UPDATE low_stock_alerts SET current_stock = :current_stock, updated_at = NOW() WHERE variant_id = :variant_id";
                    $updateStmt = $this->db->prepare($updateQuery);
                    $updateStmt->bindValue(':current_stock', $availableStock, PDO::PARAM_INT);
                    $updateStmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
                    $updateStmt->execute();
                } else {
                    $productQuery = "SELECT p.name FROM products p JOIN product_variants pv ON p.id = pv.product_id WHERE pv.id = :variant_id";
                    $productStmt = $this->db->prepare($productQuery);
                    $productStmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
                    $productStmt->execute();
                    $product = $productStmt->fetch(PDO::FETCH_ASSOC);
                    
                    $insertQuery = "
                        INSERT INTO low_stock_alerts (variant_id, product_name, current_stock, threshold)
                        VALUES (:variant_id, :product_name, :current_stock, :threshold)
                    ";
                    $insertStmt = $this->db->prepare($insertQuery);
                    $insertStmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
                    $insertStmt->bindValue(':product_name', $product['name']);
                    $insertStmt->bindValue(':current_stock', $availableStock, PDO::PARAM_INT);
                    $insertStmt->bindValue(':threshold', $threshold, PDO::PARAM_INT);
                    $insertStmt->execute();
                }
            } elseif ($availableStock >= $threshold || $availableStock == 0) {
                $deleteQuery = "DELETE FROM low_stock_alerts WHERE variant_id = :variant_id";
                $deleteStmt = $this->db->prepare($deleteQuery);
                $deleteStmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
                $deleteStmt->execute();
            }
        }
    }
    
    // Get inventory summary statistics
    public function getInventorySummary() {
        $summary = [];
        
        // Total products
        $query = "SELECT COUNT(*) as total FROM products";
        $stmt = $this->db->query($query);
        $summary['total_products'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Total variants
        $query = "SELECT COUNT(*) as total FROM product_variants";
        $stmt = $this->db->query($query);
        $summary['total_variants'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Total stock value
        $query = "
            SELECT 
                COALESCE(SUM(
                    (COALESCE(pv.stock_quantity, p.stock_quantity)) * 
                    (p.base_price + COALESCE(pv.price_adjustment, 0))
                ), 0) as total_value
            FROM products p
            LEFT JOIN product_variants pv ON p.id = pv.product_id
        ";
        $stmt = $this->db->query($query);
        $summary['total_stock_value'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_value'];
        
        // Low stock items count
        $query = "SELECT COUNT(*) as total FROM low_stock_alerts";
        $stmt = $this->db->query($query);
        $summary['low_stock_items'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Out of stock items count (available stock <= 0)
        $query = "
            SELECT COUNT(DISTINCT 
                CASE 
                    WHEN pv.id IS NOT NULL THEN CONCAT('v', pv.id)
                    ELSE CONCAT('p', p.id)
                END
            ) as total
            FROM products p
            LEFT JOIN product_variants pv ON p.id = pv.product_id
            WHERE (COALESCE(pv.stock_quantity, p.stock_quantity) - COALESCE(pv.reserved_quantity, 0)) <= 0
        ";
        $stmt = $this->db->query($query);
        $summary['out_of_stock_items'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        return $summary;
    }
    
    // ADD THIS METHOD - Unified stock handler for order status changes
    public function handleOrderStatusChange($orderId, $newStatus, $oldStatus) {
        error_log("Handling order status change: Order #$orderId from $oldStatus to $newStatus");
        
        try {
            if ($newStatus === 'confirmed' && $oldStatus !== 'confirmed') {
                return $this->processOrderConfirmation($orderId);
            } elseif ($newStatus === 'cancelled' && ($oldStatus === 'confirmed' || $oldStatus === 'pending')) {
                return $this->processOrderCancellation($orderId, $oldStatus);
            } elseif ($newStatus === 'pending' && $oldStatus !== 'pending') {
                return $this->processOrderPending($orderId);
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Order status change handling failed: " . $e->getMessage());
            return false;
        }
    }
    
    // Process order confirmation
    private function processOrderConfirmation($orderId) {
        try {
            $this->db->beginTransaction();
            
            // Get order info
            $orderQuery = "SELECT order_number, payment_method FROM orders WHERE id = :order_id";
            $orderStmt = $this->db->prepare($orderQuery);
            $orderStmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
            $orderStmt->execute();
            $order = $orderStmt->fetch(PDO::FETCH_ASSOC);
            
            // Get order items
            $query = "SELECT oi.*, pv.id as variant_id, p.name as product_name 
                     FROM order_items oi
                     LEFT JOIN product_variants pv ON oi.variant_id = pv.id
                     LEFT JOIN products p ON oi.product_id = p.id
                     WHERE oi.order_id = :order_id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
            $stmt->execute();
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($items as $item) {
                $productId = $item['product_id'];
                $variantId = $item['variant_id'];
                $quantity = $item['quantity'];
                $productName = $item['product_name'];
                
                if ($variantId) {
                    // Get current stock
                    $stockQuery = "SELECT stock_quantity, reserved_quantity FROM product_variants WHERE id = :variant_id";
                    $stockStmt = $this->db->prepare($stockQuery);
                    $stockStmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
                    $stockStmt->execute();
                    $stockInfo = $stockStmt->fetch(PDO::FETCH_ASSOC);
                    
                    $currentStock = $stockInfo['stock_quantity'];
                    $currentReserved = $stockInfo['reserved_quantity'];
                    $newStock = $currentStock - $quantity;
                    $newReserved = max(0, $currentReserved - $quantity);
                    
                    // Update variant stock (reduce stock, adjust reserved)
                    $updateQuery = "
                        UPDATE product_variants 
                        SET stock_quantity = :new_stock,
                            reserved_quantity = :new_reserved
                        WHERE id = :variant_id
                    ";
                    
                    $updateStmt = $this->db->prepare($updateQuery);
                    $updateStmt->bindValue(':new_stock', $newStock, PDO::PARAM_INT);
                    $updateStmt->bindValue(':new_reserved', $newReserved, PDO::PARAM_INT);
                    $updateStmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
                    $updateStmt->execute();
                    
                    // Log stock movement
                    $logQuery = "
                        INSERT INTO stock_movements 
                        (product_id, variant_id, movement_type, quantity, previous_quantity, new_quantity, reason, reference_id, product_name)
                        VALUES (:product_id, :variant_id, 'out', :quantity, :previous_quantity, :new_quantity, :reason, :reference_id, :product_name)
                    ";
                    
                    $logStmt = $this->db->prepare($logQuery);
                    $logStmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
                    $logStmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
                    $logStmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
                    $logStmt->bindValue(':previous_quantity', $currentStock, PDO::PARAM_INT);
                    $logStmt->bindValue(':new_quantity', $newStock, PDO::PARAM_INT);
                    $logStmt->bindValue(':reason', "Order #{$order['order_number']} confirmed ({$order['payment_method']})");
                    $logStmt->bindValue(':reference_id', $orderId, PDO::PARAM_INT);
                    $logStmt->bindValue(':product_name', $productName);
                    $logStmt->execute();
                    
                    // Update low stock alerts
                    $this->updateLowStockAlerts($productId, $variantId, $newStock);
                    
                } else {
                    // Handle base product without variant
                    $stockQuery = "SELECT stock_quantity FROM products WHERE id = :product_id";
                    $stockStmt = $this->db->prepare($stockQuery);
                    $stockStmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
                    $stockStmt->execute();
                    $currentStock = $stockStmt->fetch(PDO::FETCH_ASSOC)['stock_quantity'];
                    $newStock = $currentStock - $quantity;
                    
                    // Update product stock
                    $updateQuery = "UPDATE products SET stock_quantity = :new_stock WHERE id = :product_id";
                    $updateStmt = $this->db->prepare($updateQuery);
                    $updateStmt->bindValue(':new_stock', $newStock, PDO::PARAM_INT);
                    $updateStmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
                    $updateStmt->execute();
                    
                    // Log stock movement
                    $logQuery = "
                        INSERT INTO stock_movements 
                        (product_id, movement_type, quantity, previous_quantity, new_quantity, reason, reference_id, product_name)
                        VALUES (:product_id, 'out', :quantity, :previous_quantity, :new_quantity, :reason, :reference_id, :product_name)
                    ";
                    
                    $logStmt = $this->db->prepare($logQuery);
                    $logStmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
                    $logStmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
                    $logStmt->bindValue(':previous_quantity', $currentStock, PDO::PARAM_INT);
                    $logStmt->bindValue(':new_quantity', $newStock, PDO::PARAM_INT);
                    $logStmt->bindValue(':reason', "Order #{$order['order_number']} confirmed ({$order['payment_method']})");
                    $logStmt->bindValue(':reference_id', $orderId, PDO::PARAM_INT);
                    $logStmt->bindValue(':product_name', $productName);
                    $logStmt->execute();
                }
            }
            
            // Clear reservations for this order
            $this->clearOrderReservations($orderId);
            
            $this->db->commit();
            error_log("Order #$orderId confirmed successfully, stock reduced");
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Order confirmation failed: " . $e->getMessage());
            return false;
        }
    }
    
    // Process order cancellation
    private function processOrderCancellation($orderId, $oldStatus) {
        try {
            $this->db->beginTransaction();
            
            // Get order info
            $orderQuery = "SELECT order_number FROM orders WHERE id = :order_id";
            $orderStmt = $this->db->prepare($orderQuery);
            $orderStmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
            $orderStmt->execute();
            $order = $orderStmt->fetch(PDO::FETCH_ASSOC);
            
            // Get order items
            $query = "SELECT oi.*, pv.id as variant_id, p.name as product_name 
                     FROM order_items oi
                     LEFT JOIN product_variants pv ON oi.variant_id = pv.id
                     LEFT JOIN products p ON oi.product_id = p.id
                     WHERE oi.order_id = :order_id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
            $stmt->execute();
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($items as $item) {
                $productId = $item['product_id'];
                $variantId = $item['variant_id'];
                $quantity = $item['quantity'];
                $productName = $item['product_name'];
                
                if ($oldStatus === 'confirmed') {
                    // Order was confirmed, need to restore stock
                    if ($variantId) {
                        // Get current stock
                        $stockQuery = "SELECT stock_quantity FROM product_variants WHERE id = :variant_id";
                        $stockStmt = $this->db->prepare($stockQuery);
                        $stockStmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
                        $stockStmt->execute();
                        $currentStock = $stockStmt->fetch(PDO::FETCH_ASSOC)['stock_quantity'];
                        $newStock = $currentStock + $quantity;
                        
                        // Restore variant stock
                        $updateQuery = "UPDATE product_variants SET stock_quantity = :new_stock WHERE id = :variant_id";
                        $updateStmt = $this->db->prepare($updateQuery);
                        $updateStmt->bindValue(':new_stock', $newStock, PDO::PARAM_INT);
                        $updateStmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
                        $updateStmt->execute();
                        
                        // Log stock movement
                        $logQuery = "
                            INSERT INTO stock_movements 
                            (product_id, variant_id, movement_type, quantity, previous_quantity, new_quantity, reason, reference_id, product_name)
                            VALUES (:product_id, :variant_id, 'in', :quantity, :previous_quantity, :new_quantity, :reason, :reference_id, :product_name)
                        ";
                        
                        $logStmt = $this->db->prepare($logQuery);
                        $logStmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
                        $logStmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
                        $logStmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
                        $logStmt->bindValue(':previous_quantity', $currentStock, PDO::PARAM_INT);
                        $logStmt->bindValue(':new_quantity', $newStock, PDO::PARAM_INT);
                        $logStmt->bindValue(':reason', "Order #{$order['order_number']} cancelled (was confirmed)");
                        $logStmt->bindValue(':reference_id', $orderId, PDO::PARAM_INT);
                        $logStmt->bindValue(':product_name', $productName);
                        $logStmt->execute();
                        
                        // Update low stock alerts
                        $this->updateLowStockAlerts($productId, $variantId, $newStock);
                        
                    } else {
                        // Handle base product
                        $stockQuery = "SELECT stock_quantity FROM products WHERE id = :product_id";
                        $stockStmt = $this->db->prepare($stockQuery);
                        $stockStmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
                        $stockStmt->execute();
                        $currentStock = $stockStmt->fetch(PDO::FETCH_ASSOC)['stock_quantity'];
                        $newStock = $currentStock + $quantity;
                        
                        // Restore product stock
                        $updateQuery = "UPDATE products SET stock_quantity = :new_stock WHERE id = :product_id";
                        $updateStmt = $this->db->prepare($updateQuery);
                        $updateStmt->bindValue(':new_stock', $newStock, PDO::PARAM_INT);
                        $updateStmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
                        $updateStmt->execute();
                        
                        // Log stock movement
                        $logQuery = "
                            INSERT INTO stock_movements 
                            (product_id, movement_type, quantity, previous_quantity, new_quantity, reason, reference_id, product_name)
                            VALUES (:product_id, 'in', :quantity, :previous_quantity, :new_quantity, :reason, :reference_id, :product_name)
                        ";
                        
                        $logStmt = $this->db->prepare($logQuery);
                        $logStmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
                        $logStmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
                        $logStmt->bindValue(':previous_quantity', $currentStock, PDO::PARAM_INT);
                        $logStmt->bindValue(':new_quantity', $newStock, PDO::PARAM_INT);
                        $logStmt->bindValue(':reason', "Order #{$order['order_number']} cancelled (was confirmed)");
                        $logStmt->bindValue(':reference_id', $orderId, PDO::PARAM_INT);
                        $logStmt->bindValue(':product_name', $productName);
                        $logStmt->execute();
                    }
                } elseif ($oldStatus === 'pending') {
                    // Order was pending, just release reserved stock
                    if ($variantId) {
                        // Release reserved quantity
                        $releaseQuery = "UPDATE product_variants 
                                    SET reserved_quantity = GREATEST(0, reserved_quantity - :quantity) 
                                    WHERE id = :variant_id";
                        
                        $releaseStmt = $this->db->prepare($releaseQuery);
                        $releaseStmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
                        $releaseStmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
                        $releaseStmt->execute();
                    }
                }
            }
            
            // Clear reservations for this order
            $this->clearOrderReservations($orderId);
            
            $this->db->commit();
            error_log("Order #$orderId cancelled, stock adjusted");
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Order cancellation failed: " . $e->getMessage());
            return false;
        }
    }
    
    // Process order pending (reserve stock)
    private function processOrderPending($orderId) {
        try {
            $this->db->beginTransaction();
            
            // Get order items
            $query = "SELECT oi.*, pv.id as variant_id 
                     FROM order_items oi
                     LEFT JOIN product_variants pv ON oi.variant_id = pv.id
                     WHERE oi.order_id = :order_id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
            $stmt->execute();
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($items as $item) {
                $variantId = $item['variant_id'];
                $quantity = $item['quantity'];
                
                if ($variantId) {
                    // Reserve stock
                    $reserveQuery = "UPDATE product_variants 
                                    SET reserved_quantity = reserved_quantity + :quantity 
                                    WHERE id = :variant_id 
                                    AND (stock_quantity - reserved_quantity) >= :quantity";
                    
                    $reserveStmt = $this->db->prepare($reserveQuery);
                    $reserveStmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
                    $reserveStmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
                    $reserveStmt->execute();
                    
                    if ($reserveStmt->rowCount() === 0) {
                        throw new Exception("Failed to reserve stock for variant ID: $variantId");
                    }
                    
                    // Add reservation record
                    $reservationQuery = "
                        INSERT INTO stock_reservations (variant_id, order_id, quantity, expires_at)
                        VALUES (:variant_id, :order_id, :quantity, DATE_ADD(NOW(), INTERVAL 24 HOUR))
                    ";
                    $reservationStmt = $this->db->prepare($reservationQuery);
                    $reservationStmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
                    $reservationStmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
                    $reservationStmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
                    $reservationStmt->execute();
                }
            }
            
            $this->db->commit();
            error_log("Order #$orderId set to pending, stock reserved");
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Order pending processing failed: " . $e->getMessage());
            return false;
        }
    }
    
    // Clear order reservations
    private function clearOrderReservations($orderId) {
        try {
            // Delete reservation records
            $deleteQuery = "DELETE FROM stock_reservations WHERE order_id = :order_id";
            $deleteStmt = $this->db->prepare($deleteQuery);
            $deleteStmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
            $deleteStmt->execute();
            
            return true;
        } catch (Exception $e) {
            error_log("Clear reservations failed: " . $e->getMessage());
            return false;
        }
    }
    
    // Validate cart stock
    public function validateCartStock($cartItems) {
        $errors = [];
        $insufficientStock = [];
    
        foreach ($cartItems as $item) {
            $productId = $item['product_id'] ?? null;
            $quantity = $item['quantity'] ?? 0;
            $variantId = $item['variant_id'] ?? null;
        
            if (!$productId || $quantity <= 0) {
                $errors[] = "Invalid cart item data";
                continue;
            }
        
            if ($variantId) {
                $query = "SELECT pv.stock_quantity, pv.reserved_quantity, p.name 
                        FROM product_variants pv
                        JOIN products p ON pv.product_id = p.id
                        WHERE pv.id = :variant_id";
                $stmt = $this->db->prepare($query);
                $stmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
                $stmt->execute();
                $stockInfo = $stmt->fetch(PDO::FETCH_ASSOC);
            
                if ($stockInfo) {
                    $availableStock = ($stockInfo['stock_quantity'] ?? 0) - ($stockInfo['reserved_quantity'] ?? 0);
                    if ($quantity > $availableStock) {
                        $insufficientStock[] = [
                            'product_name' => $stockInfo['name'] ?? 'Unknown Product',
                            'requested' => $quantity,
                            'available' => $availableStock,
                            'variant_id' => $variantId,
                            'product_id' => $productId
                        ];
                    }
                } else {
                    $errors[] = "Variant not found for product ID: $productId";
                }
            } else {
                $query = "SELECT stock_quantity, name FROM products WHERE id = :product_id";
                $stmt = $this->db->prepare($query);
                $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
                $stmt->execute();
                $stockInfo = $stmt->fetch(PDO::FETCH_ASSOC);
            
                if ($stockInfo) {
                    $availableStock = $stockInfo['stock_quantity'] ?? 0;
                    if ($quantity > $availableStock) {
                        $insufficientStock[] = [
                            'product_name' => $stockInfo['name'] ?? 'Unknown Product',
                            'requested' => $quantity,
                            'available' => $availableStock,
                            'variant_id' => null,
                            'product_id' => $productId
                        ];
                    }
                } else {
                    $errors[] = "Product not found: ID $productId";
                }
            }
        }
    
        return [
            'valid' => empty($insufficientStock) && empty($errors),
            'insufficient_stock' => $insufficientStock,
            'errors' => $errors
        ];
    }

    // Reserve stock for order (used at checkout)
    public function reserveStockForOrder($orderId, $items) {
        try {
            $this->db->beginTransaction();
        
            foreach ($items as $item) {
                $variantId = $item['variant_id'] ?? null;
                $quantity = $item['quantity'];
                $productId = $item['product_id'] ?? null;
            
                if ($variantId) {
                    // Reserve variant stock
                    $query = "UPDATE product_variants 
                            SET reserved_quantity = reserved_quantity + :quantity 
                            WHERE id = :variant_id 
                            AND (stock_quantity - reserved_quantity) >= :quantity";
                
                    $stmt = $this->db->prepare($query);
                    $stmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
                    $stmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
                    $stmt->execute();
                
                    if ($stmt->rowCount() === 0) {
                        throw new Exception("Insufficient stock for variant ID: $variantId");
                    }
                
                    // Add reservation record
                    $reservationQuery = "
                        INSERT INTO stock_reservations (variant_id, order_id, quantity, expires_at)
                        VALUES (:variant_id, :order_id, :quantity, DATE_ADD(NOW(), INTERVAL 24 HOUR))
                    ";
                    $reservationStmt = $this->db->prepare($reservationQuery);
                    $reservationStmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
                    $reservationStmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
                    $reservationStmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
                    $reservationStmt->execute();
                
                } else {
                    // For base products without variants
                    $query = "UPDATE products 
                            SET stock_quantity = stock_quantity - :quantity 
                            WHERE id = :product_id 
                            AND stock_quantity >= :quantity";
                    
                    $stmt = $this->db->prepare($query);
                    $stmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
                    $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
                    $stmt->execute();
                    
                    if ($stmt->rowCount() === 0) {
                        throw new Exception("Insufficient stock for product ID: $productId");
                    }
                    
                    // Log immediate stock reduction for base products
                    $logQuery = "
                        INSERT INTO stock_movements 
                        (product_id, movement_type, quantity, previous_quantity, new_quantity, reason, reference_id)
                        SELECT 
                            :product_id,
                            'out',
                            :quantity,
                            stock_quantity + :quantity,
                            stock_quantity,
                            'Order reserved (COD)',
                            :order_id
                        FROM products WHERE id = :product_id
                    ";
                    
                    $logStmt = $this->db->prepare($logQuery);
                    $logStmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
                    $logStmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
                    $logStmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
                    $logStmt->execute();
                }
            }
        
            $this->db->commit();
            return true;
        
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Stock reservation failed: " . $e->getMessage());
            return false;
        }
    }

    // Release reserved stock
    public function releaseReservedStock($orderId) {
        try {
            $this->db->beginTransaction();
        
            // Get reservations for this order
            $query = "SELECT * FROM stock_reservations WHERE order_id = :order_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
            $stmt->execute();
            $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            foreach ($reservations as $reservation) {
                // Release reserved quantity
                $releaseQuery = "UPDATE product_variants 
                            SET reserved_quantity = GREATEST(0, reserved_quantity - :quantity) 
                            WHERE id = :variant_id";
            
                $releaseStmt = $this->db->prepare($releaseQuery);
                $releaseStmt->bindValue(':quantity', $reservation['quantity'], PDO::PARAM_INT);
                $releaseStmt->bindValue(':variant_id', $reservation['variant_id'], PDO::PARAM_INT);
                $releaseStmt->execute();
            }
            
            // Delete reservations
            $deleteQuery = "DELETE FROM stock_reservations WHERE order_id = :order_id";
            $deleteStmt = $this->db->prepare($deleteQuery);
            $deleteStmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
            $deleteStmt->execute();
        
            $this->db->commit();
            return true;
        
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Stock release failed: " . $e->getMessage());
            return false;
        }
    }

    // Get available stock for display
    public function getAvailableStock($variantId = null, $productId = null) {
        if ($variantId) {
            $query = "SELECT (stock_quantity - reserved_quantity) as available 
                    FROM product_variants 
                    WHERE id = :variant_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['available'] ?? 0;
        } elseif ($productId) {
            $query = "SELECT stock_quantity as available FROM products WHERE id = :product_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['available'] ?? 0;
        }
        return 0;
    }
    
    // Get product stock info for display
    public function getDisplayStock($productId, $variantId = null) {
        if ($variantId) {
            $query = "SELECT 
                        stock_quantity as total_stock,
                        reserved_quantity,
                        (stock_quantity - reserved_quantity) as available_stock
                      FROM product_variants 
                      WHERE id = :variant_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $query = "SELECT 
                        stock_quantity as total_stock,
                        0 as reserved_quantity,
                        stock_quantity as available_stock
                      FROM products 
                      WHERE id = :product_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
    
    // Clean up expired reservations (cron job)
    public function cleanupExpiredReservations() {
        try {
            $this->db->beginTransaction();
            
            // Get expired reservations
            $query = "SELECT * FROM stock_reservations WHERE expires_at < NOW()";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $expiredReservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($expiredReservations as $reservation) {
                // Release reserved stock
                $releaseQuery = "UPDATE product_variants 
                                SET reserved_quantity = GREATEST(0, reserved_quantity - :quantity) 
                                WHERE id = :variant_id";
                
                $releaseStmt = $this->db->prepare($releaseQuery);
                $releaseStmt->bindValue(':quantity', $reservation['quantity'], PDO::PARAM_INT);
                $releaseStmt->bindValue(':variant_id', $reservation['variant_id'], PDO::PARAM_INT);
                $releaseStmt->execute();
                
                // Update order status to cancelled if still pending
                $orderQuery = "UPDATE orders SET status = 'cancelled' 
                              WHERE id = :order_id AND status = 'pending'";
                $orderStmt = $this->db->prepare($orderQuery);
                $orderStmt->bindValue(':order_id', $reservation['order_id'], PDO::PARAM_INT);
                $orderStmt->execute();
            }
            
            // Delete expired reservations
            $deleteQuery = "DELETE FROM stock_reservations WHERE expires_at < NOW()";
            $deleteStmt = $this->db->prepare($deleteQuery);
            $deleteStmt->execute();
            
            $this->db->commit();
            return count($expiredReservations);
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Cleanup expired reservations failed: " . $e->getMessage());
            return false;
        }
    }
}
?>