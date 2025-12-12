<?php
// app/models/Cart.php
require_once __DIR__ . '/Database.php';

class Cart {
    
    /**
     * Add item to cart
     */
    public static function addItem($productId, $size, $color, $instructions, $quantity = 1) {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Debug
        error_log("Cart::addItem() - Product ID: $productId, Size: $size, Color: $color, Qty: $quantity");
        
        // Initialize cart if not exists
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Get product info to store in cart
        require_once __DIR__ . '/Product.php';
        $product = Product::getById($productId);
        
        if (!$product) {
            error_log("Cart::addItem() - Product not found: $productId");
            return false;
        }
        
        // Get variant ID if size/color selected
        $variant_id = null;
        if (!empty($size) || !empty($color)) {
            $variant_id = self::getVariantId($productId, $size, $color);
        }
        
        // Check if item already exists in cart (same product, size, color)
        $itemIndex = self::findCartItem($productId, $size, $color);
        
        if ($itemIndex !== false) {
            // Update quantity of existing item
            $_SESSION['cart'][$itemIndex]['quantity'] += $quantity;
            $_SESSION['cart'][$itemIndex]['instructions'] = $instructions;
            error_log("Cart::addItem() - Updated existing item at index $itemIndex");
        } else {
            // Create new cart item
            $item = [
                'product_id' => $productId,
                'variant_id' => $variant_id,
                'size' => $size,
                'color' => $color,
                'instructions' => $instructions,
                'quantity' => $quantity,
                'product_name' => $product['name'],
                'price' => $product['base_price'],
                'image_url' => $product['image_url'] ?? 'assets/images/placeholder.jpg',
                'added_at' => date('Y-m-d H:i:s')
            ];
            
            // Add to cart
            $_SESSION['cart'][] = $item;
            error_log("Cart::addItem() - Added new item to cart");
        }
        
        error_log("Cart::addItem() - Cart now has " . count($_SESSION['cart']) . " items");
        return true;
    }
    
    /**
     * Find item in cart
     */
    private static function findCartItem($productId, $size, $color) {
        if (!isset($_SESSION['cart'])) {
            return false;
        }
        
        foreach ($_SESSION['cart'] as $index => $item) {
            if ($item['product_id'] == $productId && 
                $item['size'] == $size && 
                $item['color'] == $color) {
                return $index;
            }
        }
        
        return false;
    }
    
    /**
     * Get variant ID from size/color
     */
    private static function getVariantId($productId, $size, $color) {
        try {
            $db = Database::getConnection();
            
            // Build query based on what's provided
            $query = "SELECT id FROM product_variants WHERE product_id = :product_id";
            $params = [':product_id' => $productId];
            
            if (!empty($size)) {
                $query .= " AND size_id = (SELECT id FROM sizes WHERE name = :size LIMIT 1)";
                $params[':size'] = $size;
            } else {
                $query .= " AND size_id IS NULL";
            }
            
            if (!empty($color)) {
                $query .= " AND color_id = (SELECT id FROM colors WHERE name = :color LIMIT 1)";
                $params[':color'] = $color;
            } else {
                $query .= " AND color_id IS NULL";
            }
            
            $query .= " LIMIT 1";
            
            $stmt = $db->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            
            $variant_id = $stmt->fetchColumn();
            
            if ($variant_id) {
                error_log("Cart::getVariantId() - Found variant ID: $variant_id for product $productId");
            } else {
                error_log("Cart::getVariantId() - No variant found for product $productId");
            }
            
            return $variant_id ?: null;
            
        } catch (Exception $e) {
            error_log("Cart::getVariantId() - Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get all cart items
     */
    public static function getItems() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return $_SESSION['cart'] ?? [];
    }
    
    /**
     * Get cart items for inventory validation
     */
    public static function getItemsForValidation() {
        $cart_items = self::getItems();
        
        // Transform to format expected by inventory system
        $formatted_items = [];
        foreach ($cart_items as $index => $item) {
            $formatted_items[] = [
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'] ?? null,
                'quantity' => $item['quantity'],
                'product_name' => $item['product_name'] ?? 'Product',
                'variant_details' => self::getVariantDetails($item)
            ];
        }
        
        return $formatted_items;
    }
    
    /**
     * Get variant details as string
     */
    private static function getVariantDetails($item) {
        $details = [];
        if (!empty($item['size'])) $details[] = "Size: " . $item['size'];
        if (!empty($item['color'])) $details[] = "Color: " . $item['color'];
        return implode(', ', $details);
    }
    
    /**
     * Get cart count (number of unique items)
     */
    public static function getCount() {
        $items = self::getItems();
        return count($items);
    }
    
    /**
     * Get total items (including quantities)
     */
    public static function getTotalItems() {
        $items = self::getItems();
        $total = 0;
        foreach ($items as $item) {
            $total += $item['quantity'];
        }
        return $total;
    }
    
    /**
     * Calculate cart total
     */
    public static function getTotal() {
        $items = self::getItems();
        $total = 0;
        
        foreach ($items as $item) {
            $total += ($item['price'] ?? 0) * $item['quantity'];
        }
        
        return $total;
    }
    
    /**
     * Clear cart
     */
    public static function clear() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['cart'] = [];
        return true;
    }
    
    /**
     * Update item quantity
     */
    public static function updateQuantity($index, $quantity) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['cart'][$index]) && $quantity > 0) {
            $_SESSION['cart'][$index]['quantity'] = $quantity;
            return true;
        }
        
        return false;
    }
    
    /**
     * Remove item from cart
     */
    public static function removeItem($index) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['cart'][$index])) {
            array_splice($_SESSION['cart'], $index, 1);
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if cart is empty
     */
    public static function isEmpty() {
        $items = self::getItems();
        return empty($items);
    }
    
    /**
     * Get item by index
     */
    public static function getItem($index) {
        $items = self::getItems();
        return $items[$index] ?? null;
    }
}
?>