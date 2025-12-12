<?php
class InventoryController {
    private $inventoryModel;
    private $productModel;
    
    public function __construct() {
        $this->inventoryModel = new Inventory();
        $this->productModel = new Product();
        require_once '../app/utils/auth.php';
        
        // Check if user is admin
        if (!isAdmin()) {
            header('Location: ?page=login');
            exit();
        }
    }
    
    // Show inventory dashboard
    public function index() {
        $page = isset($_GET['page_num']) ? intval($_GET['page_num']) : 1;
        $perPage = 20;
        
        $data = [
            'inventory' => $this->inventoryModel->getAllInventory($page, $perPage),
            'summary' => $this->inventoryModel->getInventorySummary(),
            'low_stock' => $this->inventoryModel->getLowStockItems(),
            'out_of_stock' => $this->inventoryModel->getOutOfStockItems(),
            'movements' => $this->inventoryModel->getStockMovements(10),
            'current_page' => $page,
            'total_pages' => ceil($this->inventoryModel->getTotalProducts() / $perPage)
        ];
        
        // Load view
        require_once '../app/views/admin/inventory.php';
    }
    
    // Update stock via AJAX
    public function updateStock() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $productId = $_POST['product_id'] ?? null;
        $variantId = $_POST['variant_id'] ?? null;
        $quantity = $_POST['quantity'] ?? null;
        $reason = $_POST['reason'] ?? 'Manual adjustment';
        
        if (!$productId || !is_numeric($quantity)) {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
            return;
        }
        
        $success = $this->inventoryModel->updateStock(
            $productId, 
            $variantId, 
            intval($quantity), 
            $reason,
            $_SESSION['user_id'] ?? null
        );
        
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Stock updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update stock']);
        }
    }
    
    // Export inventory to CSV
    public function exportCSV() {
        $inventory = $this->inventoryModel->getAllInventory(1, 1000); // Get all
        
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
}
?>