<?php
class AdminController {
    
    public static function dashboard() {
        self::checkAdminAccess();
        
        // Debug: Check session
        error_log("=== ADMIN DASHBOARD ===");
        error_log("Session user: " . print_r($_SESSION['user'] ?? 'NOT SET', true));
        error_log("User role: " . ($_SESSION['user']['role'] ?? 'NOT SET'));
        
        $stats = Admin::getDashboardStats();
        $recentOrders = Admin::getAllOrders();
        
        include '../app/views/admin/dashboard.php';
    }
    
    public static function products() {
        self::checkAdminAccess();
        $products = Admin::getAllProducts();
        $categories = Database::fetchAll("SELECT * FROM categories");
        include '../app/views/admin/products.php';
    }
    
    public static function orders() {
        self::checkAdminAccess();
        $orders = Admin::getAllOrders();
        include '../app/views/admin/orders.php';
    }
    
    public static function users() {
        self::checkAdminAccess();
        $users = Admin::getAllUsers();
        include '../app/views/admin/users.php';
    }
    
    // ========== NEW INVENTORY METHODS ==========
    
    public static function inventory() {
        self::checkAdminAccess();
        
        // Include the Inventory model
        require_once __DIR__ . '/../models/Inventory.php';
        $inventoryModel = new Inventory();
        
        // Get pagination
        $page = isset($_GET['page_num']) ? intval($_GET['page_num']) : 1;
        $perPage = 20;
        
        // Check for export request
        if (isset($_GET['export']) && $_GET['export'] === 'csv') {
            self::exportInventoryCSV($inventoryModel);
            return;
        }
        
        // Get data for view
        $data = [
            'inventory' => $inventoryModel->getAllInventory($page, $perPage),
            'summary' => $inventoryModel->getInventorySummary(),
            'low_stock' => $inventoryModel->getLowStockItems(),
            'out_of_stock' => $inventoryModel->getOutOfStockItems(),
            'movements' => $inventoryModel->getStockMovements(10),
            'current_page' => $page,
            'total_pages' => ceil($inventoryModel->getTotalProducts() / $perPage)
        ];
        
        // Extract data to variables for the view
        extract($data);
        
        // Load view
        include '../app/views/admin/inventory.php';
    }
    
    public static function updateStock() {
        self::checkAdminAccess();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        require_once __DIR__ . '/../models/Inventory.php';
        $inventoryModel = new Inventory();
        
        $productId = $_POST['product_id'] ?? null;
        $variantId = $_POST['variant_id'] ?? null;
        $quantity = $_POST['quantity'] ?? null;
        $reason = $_POST['reason'] ?? 'Manual adjustment';
        
        if (!$productId || !is_numeric($quantity)) {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
            return;
        }
        
        $success = $inventoryModel->updateStock(
            $productId, 
            intval($quantity), 
            $variantId,  // Now third parameter
            $reason,
            $_SESSION['user_id'] ?? null
        );
        
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Stock updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update stock']);
        }
    }
    
    private static function exportInventoryCSV($inventoryModel) {
        $inventory = $inventoryModel->getAllInventory(1, 1000); // Get all
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="inventory_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Headers
        fputcsv($output, [
            'Product ID',
            'Product Name',
            'Category',
            'Variant Details',
            'Total Stock',
            'Reserved Stock',
            'Available Stock',
            'Base Price',
            'Low Stock Alerts'
        ]);
        
        // Data
        foreach ($inventory as $item) {
            $availableStock = $item['total_stock'] - $item['total_reserved'];
            fputcsv($output, [
                $item['product_id'],
                $item['product_name'],
                $item['category_name'],
                $item['variant_details'],
                $item['total_stock'],
                $item['total_reserved'],
                $availableStock,
                $item['base_price'],
                $item['low_stock_alerts_count']
            ]);
        }
        
        fclose($output);
        exit();
    }
    
    // ========== END OF INVENTORY METHODS ==========
    
    public static function addProduct() {
        self::checkAdminAccess();
        
        if ($_POST) {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $category_id = $_POST['category_id'];
            $base_price = $_POST['base_price'];
            $featured = isset($_POST['featured']) ? 1 : 0;
            $stock_quantity = $_POST['stock_quantity'] ?? 10;
            
            // Handle image upload
            $image_url = self::handleImageUpload();
            
            if ($image_url === false) {
                // FIXED: Use correct URL format
                header('Location: ?page=admin&action=products&error=Image upload failed!');
                exit;
            }
            
            Admin::addProductWithImageAndStock($name, $description, $category_id, $base_price, $featured, $image_url, $stock_quantity);
            // FIXED: Use correct URL format
            header('Location: ?page=admin&action=products&success=Product added successfully!');
            exit;
        }
    }
    
    public static function updateProduct() {
        self::checkAdminAccess();
        
        if ($_POST) {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $description = $_POST['description'];
            $category_id = $_POST['category_id'];
            $base_price = $_POST['base_price'];
            $featured = isset($_POST['featured']) ? 1 : 0;
            $stock_quantity = $_POST['stock_quantity'] ?? 10;
            
            // Handle image upload
            $image_url = self::handleImageUpload();
            
            if ($image_url === false) {
                // FIXED: Use correct URL format
                header('Location: ?page=admin&action=products&error=Image upload failed!');
                exit;
            }
            
            // If new image uploaded, use it, otherwise keep existing
            if ($image_url) {
                Admin::updateProductWithImageAndStock($id, $name, $description, $category_id, $base_price, $featured, $image_url, $stock_quantity);
            } else {
                Admin::updateProductWithStock($id, $name, $description, $category_id, $base_price, $featured, $stock_quantity);
            }
            
            // FIXED: Use correct URL format
            header('Location: ?page=admin&action=products&success=Product updated successfully!');
            exit;
        }
    }
    
    public static function editProduct() {
        self::checkAdminAccess();
        
        // If product ID is provided, show edit form
        if (isset($_GET['id'])) {
            $productId = $_GET['id'];
            $product = Admin::getProductById($productId);
            $categories = Database::fetchAll("SELECT * FROM categories");
            
            if ($product) {
                include '../app/views/admin/edit_product.php';
            } else {
                // FIXED: Use correct URL format
                header('Location: ?page=admin&action=products&error=Product not found!');
                exit;
            }
        }
    }
    
    public static function deleteProduct() {
        self::checkAdminAccess();
        
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            Admin::deleteProduct($id);
            // FIXED: Use correct URL format
            header('Location: ?page=admin&action=products&success=Product deleted successfully!');
            exit;
        }
    }
    
    public static function updateOrderStatus() {
        self::checkAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order_id = $_POST['order_id'] ?? 0;
            $new_status = $_POST['status'] ?? '';
        
            if (!$order_id || !$new_status) {
                $_SESSION['error'] = 'Invalid order data';
                header('Location: ?page=admin&action=orders');
                exit();
            }
        
            // Get current status
            $db = Database::getConnection();
            $query = "SELECT status FROM orders WHERE id = :order_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':order_id', $order_id);
            $stmt->execute();
            $old_status = $stmt->fetchColumn();
        
            error_log("Order Status Change: #$order_id - $old_status → $new_status");
        
            // Update order status
            $query = "UPDATE orders SET status = :status, updated_at = NOW() WHERE id = :order_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':status', $new_status);
            $stmt->bindParam(':order_id', $order_id);
            $result = $stmt->execute();
        
            if (!$result) {
                $_SESSION['error'] = 'Failed to update order status';
                header('Location: ?page=admin&action=orders');
                exit();
            }
        
            // Handle stock changes
            require_once __DIR__ . '/../models/Inventory.php';
            $inventory = new Inventory();
        
            if ($new_status === 'confirmed' && $old_status !== 'confirmed') {
                // Use the new simple method
                if (method_exists($inventory, 'confirmOrderStock')) {
                    error_log("Calling confirmOrderStock for order #$order_id");
                    $stockResult = $inventory->confirmOrderStock($order_id);
                
                    if ($stockResult) {
                        $_SESSION['success'] = 'Order confirmed and stock updated successfully';
                    } else {
                        $_SESSION['warning'] = 'Order confirmed, but stock adjustment failed. Check logs.';
                    }
                } else {
                    // Fallback to manual update
                    error_log("confirmOrderStock method not found, using manual update");
                    $manualResult = self::manualStockUpdate($order_id);
                
                    if ($manualResult) {
                        $_SESSION['success'] = 'Order confirmed and stock updated';
                    } else {
                        $_SESSION['warning'] = 'Order confirmed, but stock may not be updated.';
                    }
                }
            } 
            elseif ($new_status === 'cancelled' && $old_status === 'confirmed') {
            // Restore stock for cancelled orders
                $restoreResult = self::restoreOrderStock($order_id);
                if ($restoreResult) {
                    $_SESSION['success'] = 'Order cancelled and stock restored';
                } else {
                    $_SESSION['warning'] = 'Order cancelled, but stock restoration failed';
                }
            }
            else {
                $_SESSION['success'] = 'Order status updated successfully';
            }
        
            header('Location: ?page=admin&action=orders');
            exit();
        } else {
            header('Location: ?page=admin&action=orders');
            exit();
        }
    }

// Add this helper method to AdminController.php
private static function manualStockUpdate($order_id) {
    try {
        $db = Database::getConnection();
        $db->beginTransaction();
        
        // Get order items
        $query = "SELECT oi.*, p.stock_quantity as current_stock 
                 FROM order_items oi
                 JOIN products p ON oi.product_id = p.id
                 WHERE oi.order_id = :order_id";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($items as $item) {
            $new_stock = $item['current_stock'] - $item['quantity'];
            
            // Update product stock
            $updateQuery = "UPDATE products SET stock_quantity = :new_stock WHERE id = :product_id";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(':new_stock', $new_stock);
            $updateStmt->bindParam(':product_id', $item['product_id']);
            $updateStmt->execute();
            
            error_log("Manual update: Product {$item['product_id']} - {$item['current_stock']} → $new_stock");
        }
        
        $db->commit();
        return true;
        
    } catch (Exception $e) {
        $db->rollBack();
        error_log("Manual stock update failed: " . $e->getMessage());
        return false;
    }
}
    // Helper: Reduce stock for confirmed order
    private static function reduceOrderStock($order_id) {
        try {
            $db = Database::getConnection();
            $db->beginTransaction();
            
            // Get order items
            $query = "SELECT oi.*, pv.id as variant_id 
                     FROM order_items oi
                     LEFT JOIN product_variants pv ON oi.variant_id = pv.id
                     WHERE oi.order_id = :order_id";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':order_id', $order_id);
            $stmt->execute();
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($items as $item) {
                if ($item['variant_id']) {
                    // Update variant stock (reduce actual stock, clear reserved)
                    $updateQuery = "UPDATE product_variants 
                                   SET stock_quantity = stock_quantity - :quantity,
                                       reserved_quantity = GREATEST(0, reserved_quantity - :quantity)
                                   WHERE id = :variant_id";
                    
                    $updateStmt = $db->prepare($updateQuery);
                    $updateStmt->bindParam(':quantity', $item['quantity']);
                    $updateStmt->bindParam(':variant_id', $item['variant_id']);
                    $updateStmt->execute();
                    
                } else {
                    // For base products
                    $updateQuery = "UPDATE products 
                                   SET stock_quantity = stock_quantity - :quantity 
                                   WHERE id = :product_id";
                    
                    $updateStmt = $db->prepare($updateQuery);
                    $updateStmt->bindParam(':quantity', $item['quantity']);
                    $updateStmt->bindParam(':product_id', $item['product_id']);
                    $updateStmt->execute();
                }
            }
            
            $db->commit();
            return true;
            
        } catch (Exception $e) {
            $db->rollBack();
            error_log("Stock reduction failed: " . $e->getMessage());
            return false;
        }
    }
    
    // Helper: Restore stock for cancelled order
    private static function restoreOrderStock($order_id) {
        try {
            $db = Database::getConnection();
            $db->beginTransaction();
            
            // Get order items
            $query = "SELECT oi.*, pv.id as variant_id 
                     FROM order_items oi
                     LEFT JOIN product_variants pv ON oi.variant_id = pv.id
                     WHERE oi.order_id = :order_id";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':order_id', $order_id);
            $stmt->execute();
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($items as $item) {
                if ($item['variant_id']) {
                    // Update variant stock
                    $updateQuery = "UPDATE product_variants 
                                   SET stock_quantity = stock_quantity + :quantity 
                                   WHERE id = :variant_id";
                    
                    $updateStmt = $db->prepare($updateQuery);
                    $updateStmt->bindParam(':quantity', $item['quantity']);
                    $updateStmt->bindParam(':variant_id', $item['variant_id']);
                    $updateStmt->execute();
                } else {
                    // For base products
                    $updateQuery = "UPDATE products 
                                   SET stock_quantity = stock_quantity + :quantity 
                                   WHERE id = :product_id";
                    
                    $updateStmt = $db->prepare($updateQuery);
                    $updateStmt->bindParam(':quantity', $item['quantity']);
                    $updateStmt->bindParam(':product_id', $item['product_id']);
                    $updateStmt->execute();
                }
            }
            
            $db->commit();
            return true;
            
        } catch (Exception $e) {
            $db->rollBack();
            error_log("Stock restoration failed: " . $e->getMessage());
            return false;
        }
    }
    
    
    // Handle image upload (private static method)
    private static function handleImageUpload() {
        if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] !== UPLOAD_ERR_OK) {
            return null; // No image uploaded is okay
        }
        
        // Use absolute Windows path
        $upload_dir = 'C:/xampp/htdocs/rafiashop/public/uploads/products/';
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        $file = $_FILES['product_image'];
        
        // Validate file type
        if (!in_array($file['type'], $allowed_types)) {
            return false;
        }
        
        // Validate file size
        if ($file['size'] > $max_size) {
            return false;
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'product_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $upload_dir . $filename;
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return 'uploads/products/' . $filename;
        }
        
        return false;
    }
    
    // Check if user is admin (IMPROVED VERSION)
    private static function checkAdminAccess() {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Debug: Log session data
        error_log("=== CHECK ADMIN ACCESS ===");
        error_log("Session ID: " . session_id());
        error_log("Session data: " . print_r($_SESSION, true));
        
        // Check if user is logged in - multiple session formats
        $isLoggedIn = false;
        $userRole = null;
        
        // Check session format from AJAX login
        if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
            $isLoggedIn = true;
            $userRole = $_SESSION['user']['role'] ?? 'user';
            error_log("Found user in session['user'] array, role: $userRole");
        }
        // Check session format from regular login
        elseif (isset($_SESSION['user_role'])) {
            $isLoggedIn = true;
            $userRole = $_SESSION['user_role'];
            error_log("Found user_role in session: $userRole");
        }
        // Check user_id with database lookup
        elseif (isset($_SESSION['user_id'])) {
            $isLoggedIn = true;
            // Get role from database
            require_once __DIR__ . '/../models/User.php';
            $user = User::getById($_SESSION['user_id']);
            if ($user) {
                $userRole = $user['role'] ?? 'user';
                error_log("Found user_id, looked up role from DB: $userRole");
            }
        }
        
        // Check if user is admin
        if (!$isLoggedIn) {
            error_log("User NOT logged in, redirecting to home");
            header('Location: ?page=home');
            exit;
        }
        
        if ($userRole !== 'admin') {
            error_log("User is NOT admin (role: $userRole), redirecting to user dashboard");
            header('Location: ?page=user&action=dashboard');
            exit;
        }
        
        error_log("Admin access GRANTED for role: $userRole");
    }
}
?>