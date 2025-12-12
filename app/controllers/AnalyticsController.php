<?php
class AnalyticsController {
    
    // Show analytics dashboard
    public static function dashboard() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is admin
        if (!isset($_SESSION['user'])) {
            header('Location: ?page=login');
            exit;
        }
        
        // Include the Analytics model if not already included
        if (!class_exists('Analytics')) {
            require_once __DIR__ . '/../models/Analytics.php';
        }
        
        // Get REAL analytics data from your database
        try {
            $data = [
                'total_revenue' => Analytics::getTotalRevenue(),
                'total_orders' => Analytics::getTotalOrders(),
                'total_customers' => Analytics::getTotalCustomers(),
                'avg_order_value' => Analytics::getAverageOrderValue(),
                'top_products' => Analytics::getTopProducts(5),
                'orders_by_status' => Analytics::getOrdersByStatus(),
                'recent_orders' => Analytics::getRecentOrders(5),
                'revenue_by_category' => Analytics::getRevenueByCategory(),
                'monthly_revenue' => Analytics::getMonthlyRevenue(6),
                'registration_trend' => Analytics::getCustomerRegistrationTrend(30),
                'weekly_sales' => Analytics::getWeeklySales(),
                'best_categories' => Analytics::getBestSellingCategories(3),
                'customer_value' => Analytics::getCustomerLifetimeValue(),
            'sales_by_hour' => Analytics::getSalesByHour()
    ];
        } catch (Exception $e) {
            // If there's an error, show a user-friendly message
            error_log("Analytics Error: " . $e->getMessage());
            $data = [
                'error' => 'Unable to load analytics data. Please try again later.',
                'total_revenue' => 0,
                'total_orders' => 0,
                'total_customers' => 0,
                'avg_order_value' => 0,
                'top_products' => [],
                'orders_by_status' => [],
                'recent_orders' => [],
                'revenue_by_category' => [],
                'monthly_revenue' => [],
                'registration_trend' => []
            ];
        }
        
        include '../app/views/admin/analytics.php';
    }
    
    // Get sales data for charts (AJAX)
    public static function getSalesData() {
        header('Content-Type: application/json');
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is admin
        if (!isset($_SESSION['user'])) {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        
        // Include Analytics model if needed
        if (!class_exists('Analytics')) {
            require_once __DIR__ . '/../models/Analytics.php';
        }
        
        $period = $_GET['period'] ?? 'monthly'; // daily, weekly, monthly
        
        try {
            switch ($period) {
                case 'daily':
                    $data = Analytics::getSalesByDate(
                        date('Y-m-d', strtotime('-30 days')),
                        date('Y-m-d')
                    );
                    break;
                case 'monthly':
                    $data = Analytics::getMonthlyRevenue(12);
                    break;
                default:
                    $data = Analytics::getMonthlyRevenue(6);
            }
            
            echo json_encode([
                'success' => true,
                'data' => $data,
                'period' => $period
            ]);
            
        } catch (Exception $e) {
            error_log("getSalesData Error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Unable to load sales data'
            ]);
        }
        exit;
    }
}
?>