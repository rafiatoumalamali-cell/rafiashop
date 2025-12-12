<?php
class Admin {
    // Get all products with categories for admin
    public static function getAllProducts() {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                JOIN categories c ON p.category_id = c.id 
                ORDER BY p.created_at DESC";
        return Database::fetchAll($sql);
    }
    
    // Get all orders with user info
    public static function getAllOrders() {
        $sql = "SELECT o.*, u.first_name, u.last_name, u.email, 
                       COUNT(oi.id) as item_count,
                       SUM(oi.quantity * oi.unit_price) as total_amount
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                LEFT JOIN order_items oi ON o.id = oi.order_id 
                GROUP BY o.id 
                ORDER BY o.created_at DESC";
        return Database::fetchAll($sql);
    }
    
    // Get all users
    public static function getAllUsers() {
        $sql = "SELECT id, first_name, last_name, email, phone, created_at 
                FROM users 
                ORDER BY created_at DESC";
        return Database::fetchAll($sql);
    }
    
    // Add new product
    public static function addProduct($name, $description, $category_id, $base_price, $featured = false) {
        $sql = "INSERT INTO products (name, description, category_id, base_price, featured) 
                VALUES (?, ?, ?, ?, ?)";
        return Database::query($sql, [$name, $description, $category_id, $base_price, $featured]);
    }
    
    // Update product
    public static function updateProduct($id, $name, $description, $category_id, $base_price, $featured) {
        $sql = "UPDATE products 
                SET name = ?, description = ?, category_id = ?, base_price = ?, featured = ? 
                WHERE id = ?";
        return Database::query($sql, [$name, $description, $category_id, $base_price, $featured, $id]);
    }
    
    // Delete product
    public static function deleteProduct($id) {
        $sql = "DELETE FROM products WHERE id = ?";
        return Database::query($sql, [$id]);
    }
    
    // Update order status
    public static function updateOrderStatus($orderId, $status) {
        $sql = "UPDATE orders SET status = ? WHERE id = ?";
        return Database::query($sql, [$status, $orderId]);
    }
    
    // Get single product by ID
    public static function getProductById($id) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                JOIN categories c ON p.category_id = c.id 
                WHERE p.id = ?";
        return Database::fetch($sql, [$id]);
    }
    
    // Get dashboard stats
    public static function getDashboardStats() {
        $stats = [];
        
        // Total users
        $stats['total_users'] = Database::fetch("SELECT COUNT(*) as count FROM users")['count'];
        
        // Total products
        $stats['total_products'] = Database::fetch("SELECT COUNT(*) as count FROM products")['count'];
        
        // Total orders
        $stats['total_orders'] = Database::fetch("SELECT COUNT(*) as count FROM orders")['count'];
        
        // Total revenue
        $revenue = Database::fetch("
            SELECT SUM(oi.quantity * oi.unit_price) as total 
            FROM order_items oi 
            JOIN orders o ON oi.order_id = o.id 
            WHERE o.status = 'delivered'
        ");
        $stats['total_revenue'] = $revenue['total'] ?? 0;
        
        return $stats;
    }

    // Add product with image
    public static function addProductWithImage($name, $description, $category_id, $base_price, $featured, $image_url) {
        $sql = "INSERT INTO products (name, description, category_id, base_price, featured, image_url) VALUES (?, ?, ?, ?, ?, ?)";
        return Database::query($sql, [$name, $description, $category_id, $base_price, $featured, $image_url]);
    }

    // Update product with image
    public static function updateProductWithImage($id, $name, $description, $category_id, $base_price, $featured, $image_url) {
        $sql = "UPDATE products SET name = ?, description = ?, category_id = ?, base_price = ?, featured = ?, image_url = ? WHERE id = ?";
        return Database::query($sql, [$name, $description, $category_id, $base_price, $featured, $image_url, $id]);
    }

    // NEW: Add product with image and stock quantity
    public static function addProductWithImageAndStock($name, $description, $category_id, $base_price, $featured, $image_url, $stock_quantity = 10) {
        $sql = "INSERT INTO products (name, description, category_id, base_price, featured, image_url, stock_quantity) VALUES (?, ?, ?, ?, ?, ?, ?)";
        return Database::query($sql, [$name, $description, $category_id, $base_price, $featured, $image_url, $stock_quantity]);
    }

    // NEW: Update product with image and stock quantity
    public static function updateProductWithImageAndStock($id, $name, $description, $category_id, $base_price, $featured, $image_url, $stock_quantity) {
        $sql = "UPDATE products SET name = ?, description = ?, category_id = ?, base_price = ?, featured = ?, image_url = ?, stock_quantity = ? WHERE id = ?";
        return Database::query($sql, [$name, $description, $category_id, $base_price, $featured, $image_url, $stock_quantity, $id]);
    }

    // NEW: Update product without image but with stock quantity
    public static function updateProductWithStock($id, $name, $description, $category_id, $base_price, $featured, $stock_quantity) {
        $sql = "UPDATE products SET name = ?, description = ?, category_id = ?, base_price = ?, featured = ?, stock_quantity = ? WHERE id = ?";
        return Database::query($sql, [$name, $description, $category_id, $base_price, $featured, $stock_quantity, $id]);
    }
}
?>