<?php
// debug_order.php
session_start();
require_once 'app/models/Database.php';
require_once 'app/models/Inventory.php';

$db = Database::getConnection();
$inventory = new Inventory();

echo "<h2>Order Debug Information</h2>";

// Check a specific order
$order_id = 1; // Change this to your order ID

// 1. Check order details
echo "<h3>1. Order Details</h3>";
$query = "SELECT * FROM orders WHERE id = :order_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':order_id', $order_id);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "Order not found";
    exit;
}

echo "<pre>";
print_r($order);
echo "</pre>";

// 2. Check order items
echo "<h3>2. Order Items</h3>";
$query = "SELECT oi.*, p.name as product_name, pv.id as variant_id 
          FROM order_items oi
          LEFT JOIN products p ON oi.product_id = p.id
          LEFT JOIN product_variants pv ON oi.variant_id = pv.id
          WHERE oi.order_id = :order_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':order_id', $order_id);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1'>";
echo "<tr><th>Product</th><th>Variant ID</th><th>Quantity</th></tr>";
foreach ($items as $item) {
    echo "<tr>";
    echo "<td>{$item['product_name']}</td>";
    echo "<td>{$item['variant_id']}</td>";
    echo "<td>{$item['quantity']}</td>";
    echo "</tr>";
}
echo "</table>";

// 3. Test stock adjustment
echo "<h3>3. Test Stock Adjustment</h3>";
if (method_exists($inventory, 'handleOrderStatusChange')) {
    echo "Method handleOrderStatusChange exists.<br>";
    
    // Test confirming the order
    $result = $inventory->handleOrderStatusChange($order_id, 'confirmed', 'pending');
    echo "handleOrderStatusChange result: " . ($result ? 'SUCCESS' : 'FAILED') . "<br>";
    
    if (!$result) {
        echo "Error occurred. Check error logs.";
    }
} else {
    echo "Method handleOrderStatusChange NOT FOUND!";
}

// 4. Check current stock after adjustment
echo "<h3>4. Current Stock After Adjustment</h3>";
foreach ($items as $item) {
    if ($item['variant_id']) {
        $stockInfo = $inventory->getDisplayStock($item['product_id'], $item['variant_id']);
    } else {
        $stockInfo = $inventory->getDisplayStock($item['product_id']);
    }
    
    echo "<p><strong>{$item['product_name']}:</strong> ";
    echo "Total: {$stockInfo['total_stock']}, ";
    echo "Reserved: {$stockInfo['reserved_quantity']}, ";
    echo "Available: {$stockInfo['available_stock']}";
    echo "</p>";
}
?>