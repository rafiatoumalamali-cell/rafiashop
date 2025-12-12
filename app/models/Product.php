<?php
class Product {
    public static function getAll() {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                JOIN categories c ON p.category_id = c.id 
                ORDER BY p.created_at DESC";
        return Database::fetchAll($sql);
    }
    

    public static function getById($id) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                JOIN categories c ON p.category_id = c.id 
                WHERE p.id = ?";
        return Database::fetch($sql, [$id]);
    }

    // Search products by name or description
    public static function search($query) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                JOIN categories c ON p.category_id = c.id 
                WHERE p.name LIKE ? OR p.description LIKE ? 
                ORDER BY p.created_at DESC";
        $searchTerm = "%$query%";
        return Database::fetchAll($sql, [$searchTerm, $searchTerm]);
    }

    // Filter products by category
    public static function filterByCategory($categoryId) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                JOIN categories c ON p.category_id = c.id 
                WHERE p.category_id = ? 
                ORDER BY p.created_at DESC";
        return Database::fetchAll($sql, [$categoryId]);
    }

    // Filter products by price range
    public static function filterByPrice($minPrice, $maxPrice) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                JOIN categories c ON p.category_id = c.id 
                WHERE p.base_price BETWEEN ? AND ? 
                ORDER BY p.base_price ASC";
        return Database::fetchAll($sql, [$minPrice, $maxPrice]);
    }

    // Get all categories for filter dropdown
    public static function getAllCategories() {
        $sql = "SELECT * FROM categories ORDER BY name";
        return Database::fetchAll($sql);
    }

    // Update product with image
    public static function updateWithImage($id, $name, $description, $category_id, $base_price, $featured, $image_url = null) {
        if ($image_url) {
            $sql = "UPDATE products SET name = ?, description = ?, category_id = ?, base_price = ?, featured = ?, image_url = ? WHERE id = ?";
            return Database::query($sql, [$name, $description, $category_id, $base_price, $featured, $image_url, $id]);
        } else {
            $sql = "UPDATE products SET name = ?, description = ?, category_id = ?, base_price = ?, featured = ? WHERE id = ?";
            return Database::query($sql, [$name, $description, $category_id, $base_price, $featured, $id]);
        }
    }

    // Add product with image
    public static function addWithImage($name, $description, $category_id, $base_price, $featured, $image_url) {
        $sql = "INSERT INTO products (name, description, category_id, base_price, featured, image_url) VALUES (?, ?, ?, ?, ?, ?)";
        return Database::query($sql, [$name, $description, $category_id, $base_price, $featured, $image_url]);
    }

    // Update product stock (when order is placed)
    public static function updateStock($productId, $quantity) {
        $sql = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?";
        return Database::query($sql, [$quantity, $productId, $quantity]);
    }

    // Get product stock level
    public static function getStockLevel($productId) {
        $product = self::getById($productId); // FIXED: Added $ before product
        return $product ? $product['stock_quantity'] : 0;
    }
}
?>