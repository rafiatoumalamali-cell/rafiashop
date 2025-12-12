<?php
class Analytics {
    
    // Get total revenue - FIXED for normalized schema
    public static function getTotalRevenue() {
        try {
            $sql = "SELECT SUM(oi.quantity * oi.unit_price) as total_revenue 
                    FROM order_items oi
                    JOIN orders o ON oi.order_id = o.id
                    WHERE o.status != 'cancelled'";
            $result = Database::fetch($sql);
            return $result['total_revenue'] ?? 0;
        } catch (Exception $e) {
            error_log("getTotalRevenue error: " . $e->getMessage());
            return 0;
        }
    }

    // Get total orders - FIXED
    public static function getTotalOrders() {
        try {
            $sql = "SELECT COUNT(*) as total_orders FROM orders WHERE status != 'cancelled'";
            $result = Database::fetch($sql);
            return $result['total_orders'] ?? 0;
        } catch (Exception $e) {
            error_log("getTotalOrders error: " . $e->getMessage());
            return 0;
        }
    }
    
    // Get total customers - FIXED (count distinct users who have placed orders)
    public static function getTotalCustomers() {
        try {
            $sql = "SELECT COUNT(DISTINCT user_id) as total_customers 
                    FROM orders 
                    WHERE status != 'cancelled'";
            $result = Database::fetch($sql);
            return $result['total_customers'] ?? 0;
        } catch (Exception $e) {
            error_log("getTotalCustomers error: " . $e->getMessage());
            return 0;
        }
    }
    
    // Get average order value - FIXED
    public static function getAverageOrderValue() {
        try {
            $sql = "SELECT AVG(order_total) as avg_value 
                    FROM (
                        SELECT oi.order_id, SUM(oi.quantity * oi.unit_price) as order_total 
                        FROM order_items oi 
                        JOIN orders o ON oi.order_id = o.id 
                        WHERE o.status != 'cancelled' 
                        GROUP BY oi.order_id
                    ) as order_totals";
            $result = Database::fetch($sql);
            return $result['avg_value'] ?? 0;
        } catch (Exception $e) {
            error_log("getAverageOrderValue error: " . $e->getMessage());
            return 0;
        }
    }
    
    // Get sales by date range - FIXED
    public static function getSalesByDate($startDate, $endDate) {
        try {
            $sql = "SELECT DATE(o.created_at) as date, 
                           COUNT(DISTINCT o.id) as order_count,
                           SUM(oi.quantity * oi.unit_price) as revenue
                    FROM orders o 
                    JOIN order_items oi ON o.id = oi.order_id 
                    WHERE o.created_at BETWEEN ? AND ? 
                    AND o.status != 'cancelled'
                    GROUP BY DATE(o.created_at) 
                    ORDER BY date";
            return Database::fetchAll($sql, [$startDate, $endDate]);
        } catch (Exception $e) {
            error_log("getSalesByDate error: " . $e->getMessage());
            return [];
        }
    }
    
    // Get top selling products - FIXED
    public static function getTopProducts($limit = 10) {
        try {
            $sql = "SELECT p.id, p.name, 
                           c.name as category,
                           SUM(oi.quantity) as total_sold,
                           SUM(oi.quantity * oi.unit_price) as revenue
                    FROM products p 
                    JOIN order_items oi ON p.id = oi.product_id 
                    JOIN orders o ON oi.order_id = o.id 
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE o.status != 'cancelled'
                    GROUP BY p.id 
                    ORDER BY total_sold DESC 
                    LIMIT ?";
            return Database::fetchAll($sql, [$limit]);
        } catch (Exception $e) {
            error_log("getTopProducts error: " . $e->getMessage());
            return [];
        }
    }
    
    // Get orders by status - FIXED
    public static function getOrdersByStatus() {
        try {
            $sql = "SELECT status, COUNT(*) as count 
                    FROM orders 
                    WHERE status != 'cancelled'
                    GROUP BY status 
                    ORDER BY FIELD(status, 'pending', 'confirmed', 'shipped', 'delivered')";
            $result = Database::fetchAll($sql);
            
            // Also get cancelled count separately
            $sqlCancelled = "SELECT COUNT(*) as count FROM orders WHERE status = 'cancelled'";
            $cancelled = Database::fetch($sqlCancelled);
            
            if ($cancelled && $cancelled['count'] > 0) {
                $result[] = ['status' => 'cancelled', 'count' => $cancelled['count']];
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("getOrdersByStatus error: " . $e->getMessage());
            return [];
        }
    }
    
    // Get recent orders with calculated totals - FIXED
    public static function getRecentOrders($limit = 10) {
        try {
            $sql = "SELECT o.*, 
                           u.first_name, 
                           u.last_name, 
                           u.email,
                           ua.address_line1,
                           ua.city,
                           (SELECT SUM(oi.quantity * oi.unit_price) 
                            FROM order_items oi 
                            WHERE oi.order_id = o.id) as order_total
                    FROM orders o 
                    JOIN users u ON o.user_id = u.id 
                    JOIN user_addresses ua ON o.address_id = ua.id
                    ORDER BY o.created_at DESC 
                    LIMIT ?";
            $orders = Database::fetchAll($sql, [$limit]);
            
            // Get order items for each order
            foreach ($orders as &$order) {
                $order['items'] = self::getOrderItems($order['id']);
            }
            
            return $orders;
        } catch (Exception $e) {
            error_log("getRecentOrders error: " . $e->getMessage());
            return [];
        }
    }
    
    // Helper method to get order items
    private static function getOrderItems($orderId) {
        try {
            $sql = "SELECT oi.*, p.name as product_name
                    FROM order_items oi
                    JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id = ?";
            return Database::fetchAll($sql, [$orderId]);
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Get customer registration trend - FIXED
    public static function getCustomerRegistrationTrend($days = 30) {
        try {
            $sql = "SELECT DATE(created_at) as date, COUNT(*) as registrations 
                    FROM users 
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY) 
                    GROUP BY DATE(created_at) 
                    ORDER BY date";
            return Database::fetchAll($sql, [$days]);
        } catch (Exception $e) {
            error_log("getCustomerRegistrationTrend error: " . $e->getMessage());
            return [];
        }
    }
    
    // Get revenue by category - FIXED
    public static function getRevenueByCategory() {
        try {
            $sql = "SELECT c.name as category, 
                           SUM(oi.quantity * oi.unit_price) as revenue,
                           COUNT(DISTINCT oi.order_id) as order_count
                    FROM categories c 
                    JOIN products p ON c.id = p.category_id 
                    JOIN order_items oi ON p.id = oi.product_id 
                    JOIN orders o ON oi.order_id = o.id 
                    WHERE o.status != 'cancelled'
                    GROUP BY c.id 
                    ORDER BY revenue DESC";
            return Database::fetchAll($sql);
        } catch (Exception $e) {
            error_log("getRevenueByCategory error: " . $e->getMessage());
            return [];
        }
    }
    
    // Get monthly revenue - FIXED
    public static function getMonthlyRevenue($months = 6) {
        try {
            $sql = "SELECT DATE_FORMAT(o.created_at, '%Y-%m') as month,
                           SUM(oi.quantity * oi.unit_price) as revenue,
                           COUNT(DISTINCT o.id) as order_count
                    FROM orders o 
                    JOIN order_items oi ON o.id = oi.order_id 
                    WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH) 
                    AND o.status != 'cancelled'
                    GROUP BY DATE_FORMAT(o.created_at, '%Y-%m') 
                    ORDER BY month";
            return Database::fetchAll($sql, [$months]);
        } catch (Exception $e) {
            error_log("getMonthlyRevenue error: " . $e->getMessage());
            return [];
        }
    }
    
    // Get daily sales for the last 7 days - NEW
    public static function getWeeklySales() {
        try {
            $sql = "SELECT DATE(o.created_at) as date,
                           DAYNAME(o.created_at) as day_name,
                           SUM(oi.quantity * oi.unit_price) as revenue,
                           COUNT(DISTINCT o.id) as order_count
                    FROM orders o 
                    JOIN order_items oi ON o.id = oi.order_id 
                    WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
                    AND o.status != 'cancelled'
                    GROUP BY DATE(o.created_at)
                    ORDER BY date";
            return Database::fetchAll($sql);
        } catch (Exception $e) {
            error_log("getWeeklySales error: " . $e->getMessage());
            return [];
        }
    }
    
    // Get best selling categories - NEW
    public static function getBestSellingCategories($limit = 5) {
        try {
            $sql = "SELECT c.name as category,
                           COUNT(DISTINCT oi.order_id) as order_count,
                           SUM(oi.quantity) as units_sold,
                           SUM(oi.quantity * oi.unit_price) as revenue
                    FROM categories c 
                    JOIN products p ON c.id = p.category_id 
                    JOIN order_items oi ON p.id = oi.product_id 
                    JOIN orders o ON oi.order_id = o.id 
                    WHERE o.status != 'cancelled'
                    GROUP BY c.id 
                    ORDER BY revenue DESC
                    LIMIT ?";
            return Database::fetchAll($sql, [$limit]);
        } catch (Exception $e) {
            error_log("getBestSellingCategories error: " . $e->getMessage());
            return [];
        }
    }
    
    // Get customer lifetime value - NEW
    public static function getCustomerLifetimeValue() {
        try {
            $sql = "SELECT 
                        u.id,
                        CONCAT(u.first_name, ' ', u.last_name) as customer_name,
                        u.email,
                        COUNT(DISTINCT o.id) as total_orders,
                        SUM(oi.quantity * oi.unit_price) as total_spent,
                        MAX(o.created_at) as last_order_date
                    FROM users u
                    LEFT JOIN orders o ON u.id = o.user_id AND o.status != 'cancelled'
                    LEFT JOIN order_items oi ON o.id = oi.order_id
                    GROUP BY u.id
                    HAVING total_spent > 0
                    ORDER BY total_spent DESC";
            return Database::fetchAll($sql);
        } catch (Exception $e) {
            error_log("getCustomerLifetimeValue error: " . $e->getMessage());
            return [];
        }
    }
    
    // Get sales performance by hour - NEW
    public static function getSalesByHour() {
        try {
            $sql = "SELECT 
                        HOUR(o.created_at) as hour,
                        COUNT(DISTINCT o.id) as order_count,
                        SUM(oi.quantity * oi.unit_price) as revenue
                    FROM orders o 
                    JOIN order_items oi ON o.id = oi.order_id 
                    WHERE o.status != 'cancelled'
                    AND o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    GROUP BY HOUR(o.created_at)
                    ORDER BY hour";
            return Database::fetchAll($sql);
        } catch (Exception $e) {
            error_log("getSalesByHour error: " . $e->getMessage());
            return [];
        }
    }
}
?>