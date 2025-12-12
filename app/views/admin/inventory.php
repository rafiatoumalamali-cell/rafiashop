<?php
// Get absolute path to header
require_once __DIR__ . '/../header.php';
?>

<!-- Custom Inventory CSS -->
<style>
/* Modern Design System */
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    --warning-gradient: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);
    --danger-gradient: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
    --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

/* Card Enhancements */
.inventory-card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    overflow: hidden;
    background: white;
}

.inventory-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.inventory-card-header {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-bottom: 1px solid rgba(0,0,0,0.05);
    padding: 1.25rem 1.5rem;
}

/* Summary Cards */
.summary-card {
    border: none;
    border-radius: 16px;
    overflow: hidden;
    position: relative;
    transition: all 0.3s ease;
    height: 100%;
    background: white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.summary-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.summary-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--primary-gradient);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.summary-card:hover::before {
    opacity: 1;
}

/* Gradient Borders */
.border-gradient-primary {
    border: 2px solid transparent;
    background: linear-gradient(white, white) padding-box,
                var(--primary-gradient) border-box;
}

.border-gradient-success {
    border: 2px solid transparent;
    background: linear-gradient(white, white) padding-box,
                var(--success-gradient) border-box;
}

.border-gradient-warning {
    border: 2px solid transparent;
    background: linear-gradient(white, white) padding-box,
                var(--warning-gradient) border-box;
}

.border-gradient-danger {
    border: 2px solid transparent;
    background: linear-gradient(white, white) padding-box,
                var(--danger-gradient) border-box;
}

/* Table Improvements */
.table-inventory {
    --bs-table-bg: transparent;
    --bs-table-striped-bg: rgba(0,0,0,0.02);
    --bs-table-hover-bg: rgba(102, 126, 234, 0.05);
}

.table-inventory thead th {
    border-bottom: 2px solid #667eea;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    font-weight: 600;
    color: #4a5568;
    text-transform: uppercase;
    font-size: 0.85em;
    letter-spacing: 0.5px;
    padding: 1rem 1.25rem;
}

.table-inventory tbody td {
    padding: 1rem 1.25rem;
    vertical-align: middle;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

/* Status Badges */
.badge-status {
    padding: 0.4em 1em;
    border-radius: 20px;
    font-weight: 500;
    font-size: 0.8em;
    letter-spacing: 0.3px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.badge-in-stock {
    background: var(--success-gradient);
    color: white;
}

.badge-low-stock {
    background: var(--warning-gradient);
    color: white;
}

.badge-out-of-stock {
    background: var(--danger-gradient);
    color: white;
}

/* Progress Bar Styling */
.stock-progress {
    height: 10px;
    border-radius: 10px;
    background: #e2e8f0;
    overflow: hidden;
    position: relative;
}

.stock-progress-bar {
    height: 100%;
    border-radius: 10px;
    position: relative;
    overflow: hidden;
    transition: width 0.6s ease;
}

.stock-progress-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, 
        rgba(255,255,255,0.1) 25%, 
        rgba(255,255,255,0.3) 50%, 
        rgba(255,255,255,0.1) 75%);
    background-size: 200% 100%;
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

/* Product Image Styling */
.product-img-container {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    overflow: hidden;
    position: relative;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.product-img-container:hover {
    transform: scale(1.05);
}

.product-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-img:hover {
    transform: scale(1.1);
}

/* Button Enhancements */
.btn-inventory {
    border-radius: 10px;
    padding: 0.5rem 1.25rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    position: relative;
    overflow: hidden;
}

.btn-inventory::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.btn-inventory:hover::before {
    width: 300px;
    height: 300px;
}

.btn-inventory-primary {
    background: var(--primary-gradient);
    color: white;
}

.btn-inventory-secondary {
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    color: #4a5568;
}

/* Modal Styling */
.modal-inventory {
    border-radius: 20px;
    border: none;
    overflow: hidden;
}

.modal-inventory .modal-header {
    background: var(--primary-gradient);
    color: white;
    border-bottom: none;
    padding: 1.5rem 2rem;
}

.modal-inventory .modal-footer {
    border-top: 1px solid rgba(0,0,0,0.05);
    padding: 1.5rem 2rem;
}

/* Search and Filter */
.search-box {
    position: relative;
    max-width: 300px;
}

.search-box input {
    padding-left: 2.5rem;
    border-radius: 10px;
    border: 2px solid #e2e8f0;
    transition: all 0.3s ease;
}

.search-box input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.search-box i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #a0aec0;
}

.filter-select {
    border-radius: 10px;
    border: 2px solid #e2e8f0;
    transition: all 0.3s ease;
    padding: 0.5rem 1rem;
}

.filter-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* Animation Classes */
.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .table-inventory {
        font-size: 0.9em;
    }
    
    .summary-card .display-6 {
        font-size: 1.5rem;
    }
}
</style>

<div class="container-fluid py-4 fade-in">
    <!-- Enhanced Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="h2 mb-2 fw-bold text-gradient-primary">
                <i class="bi bi-box-seam me-2"></i>Inventory Management
            </h1>
            <p class="text-muted mb-0">
                <i class="bi bi-info-circle me-1"></i>
                Track stock levels, manage inventory, and monitor product availability in real-time
            </p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-inventory btn-inventory-secondary" onclick="exportToCSV()">
                <i class="bi bi-download me-2"></i> Export CSV
            </button>
            <button class="btn btn-inventory btn-inventory-primary" data-bs-toggle="modal" data-bs-target="#bulkUpdateModal">
                <i class="bi bi-upload me-2"></i> Bulk Update
            </button>
        </div>
    </div>

    <!-- Enhanced Summary Cards -->
    <div class="row mb-5 g-4">
        <div class="col-xl-3 col-md-6">
            <div class="summary-card border-gradient-primary">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase mb-2">Total Products</h6>
                            <h2 class="display-6 fw-bold mb-0 text-primary"><?= number_format($summary['total_products']) ?></h2>
                            <small class="text-muted">Active in inventory</small>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                            <i class="bi bi-box-seam text-primary fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="summary-card border-gradient-warning">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase mb-2">Low Stock</h6>
                            <h2 class="display-6 fw-bold mb-0 text-warning"><?= number_format($summary['low_stock_items']) ?></h2>
                            <small class="text-muted">Need restocking</small>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                            <i class="bi bi-exclamation-triangle text-warning fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="summary-card border-gradient-danger">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase mb-2">Out of Stock</h6>
                            <h2 class="display-6 fw-bold mb-0 text-danger"><?= number_format($summary['out_of_stock_items']) ?></h2>
                            <small class="text-muted">Unavailable items</small>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded-circle">
                            <i class="bi bi-x-circle text-danger fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="summary-card border-gradient-success">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase mb-2">Stock Value</h6>
                            <h2 class="display-6 fw-bold mb-0 text-success">$<?= number_format($summary['total_stock_value'], 2) ?></h2>
                            <small class="text-muted">Total inventory value</small>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                            <i class="bi bi-currency-dollar text-success fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Inventory Table -->
    <div class="inventory-card mb-5">
        <div class="inventory-card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-grid-3x3-gap me-2"></i>Product Inventory
                </h5>
                <div class="d-flex gap-3">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" class="form-control" placeholder="Search products..." 
                               id="inventorySearch">
                    </div>
                    <select class="form-select filter-select" style="width: 180px;" id="categoryFilter">
                        <option value="">All Categories</option>
                        <option value="Clothing">👕 Clothing</option>
                        <option value="Shoes">👟 Shoes</option>
                        <option value="Kitchen">🍳 Kitchen</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-inventory mb-0" id="inventoryTable">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Product</th>
                            <th style="width: 12%;">Category</th>
                            <th style="width: 10%;">Type</th>
                            <th style="width: 18%;">Stock Level</th>
                            <th style="width: 8%;">Reserved</th>
                            <th style="width: 10%;">Available</th>
                            <th style="width: 10%;">Price</th>
                            <th style="width: 7%;">Status</th>
                            <th style="width: 10%;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inventory as $item): 
                            $availableStock = $item['total_stock'] - $item['total_reserved'];
                            $stockPercentage = ($availableStock / ($item['total_stock'] ?: 1)) * 100;
                            $isLowStock = $availableStock > 0 && $availableStock < 5;
                        ?>
                        <tr class="align-middle">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="product-img-container me-3">
                                        <img src="<?= htmlspecialchars($item['image_url'] ?: 'assets/images/placeholder.jpg') ?>" 
                                             alt="<?= htmlspecialchars($item['product_name']) ?>" 
                                             class="product-img"
                                             onerror="this.src='assets/images/placeholder.jpg'">
                                    </div>
                                    <div>
                                        <strong class="d-block"><?= htmlspecialchars($item['product_name']) ?></strong>
                                        <small class="text-muted">ID: #<?= $item['product_id'] ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark px-3 py-2">
                                    <i class="bi bi-tag me-1"></i><?= htmlspecialchars($item['category_name']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark px-3 py-2">
                                    <?php if ($item['has_variants']): ?>
                                        <i class="bi bi-layers me-1"></i>With Variants
                                    <?php else: ?>
                                        <i class="bi bi-box me-1"></i>Single
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="flex-grow-1">
                                        <div class="stock-progress">
                                            <div class="stock-progress-bar 
                                                <?= $availableStock == 0 ? 'bg-danger' : 
                                                   ($isLowStock ? 'bg-warning' : 'bg-success') ?>"
                                                 style="width: <?= min($stockPercentage, 100) ?>%">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-nowrap">
                                        <span class="fw-bold"><?= $availableStock ?></span>
                                        <span class="text-muted">/ <?= $item['total_stock'] ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                                    <i class="bi bi-clock me-1"></i><?= $item['total_reserved'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge 
                                    <?= $availableStock == 0 ? 'bg-danger' : 
                                       ($isLowStock ? 'bg-warning' : 'bg-success') ?> 
                                    px-3 py-2">
                                    <?= $availableStock ?>
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-success">$<?= number_format($item['base_price'], 2) ?></span>
                            </td>
                            <td>
                                <?php if ($availableStock == 0): ?>
                                    <span class="badge-status badge-out-of-stock">
                                        <i class="bi bi-x-circle me-1"></i>Out
                                    </span>
                                <?php elseif ($isLowStock): ?>
                                    <span class="badge-status badge-low-stock">
                                        <i class="bi bi-exclamation-triangle me-1"></i>Low
                                    </span>
                                <?php else: ?>
                                    <span class="badge-status badge-in-stock">
                                        <i class="bi bi-check-circle me-1"></i>In Stock
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-sm btn-outline-primary rounded-circle" 
                                            onclick="showUpdateModal(<?= $item['product_id'] ?>, null, '<?= htmlspecialchars($item['product_name']) ?>')"
                                            title="Update Stock"
                                            style="width: 38px; height: 38px;">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a href="?page=admin&action=edit_product&id=<?= $item['product_id'] ?>" 
                                       class="btn btn-sm btn-outline-secondary rounded-circle" 
                                       title="Edit Product"
                                       style="width: 38px; height: 38px;">
                                        <i class="bi bi-gear"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-info rounded-circle" 
                                            onclick="viewProductDetails(<?= $item['product_id'] ?>)"
                                            title="View Details"
                                            style="width: 38px; height: 38px;">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Enhanced Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="border-top py-3 px-4">
                <nav aria-label="Inventory pagination">
                    <ul class="pagination justify-content-center mb-0">
                        <li class="page-item <?= $current_page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link rounded-start" 
                               href="?page=admin&action=inventory&page_num=<?= $current_page - 1 ?>">
                                <i class="bi bi-chevron-left"></i> Previous
                            </a>
                        </li>
                        
                        <?php 
                        $start = max(1, $current_page - 2);
                        $end = min($total_pages, $current_page + 2);
                        
                        if ($start > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=admin&action=inventory&page_num=1">1</a>
                            </li>
                            <?php if ($start > 2): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php for ($i = $start; $i <= $end; $i++): ?>
                            <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=admin&action=inventory&page_num=<?= $i ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($end < $total_pages): ?>
                            <?php if ($end < $total_pages - 1): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=admin&action=inventory&page_num=<?= $total_pages ?>">
                                    <?= $total_pages ?>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <li class="page-item <?= $current_page >= $total_pages ? 'disabled' : '' ?>">
                            <a class="page-link rounded-end" 
                               href="?page=admin&action=inventory&page_num=<?= $current_page + 1 ?>">
                                Next <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Alert Sections -->
    <div class="row g-4">
        
        <!-- Low Stock Alerts -->
        
<?php if (!empty($low_stock)): ?>
<div class="col-lg-8">
    <div class="inventory-card">
        <div class="inventory-card-header bg-warning bg-opacity-10">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-warning">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Low Stock Alerts
                    <span class="badge bg-warning ms-2"><?= count($low_stock) ?></span>
                </h5>
                <button class="btn btn-sm btn-warning" onclick="exportLowStockCSV()">
                    <i class="bi bi-download me-1"></i> Export
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Variant</th>
                            <th>Current Stock</th>
                            <th>Threshold</th>
                            <th>Below By</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($low_stock as $alert): ?>
                        <?php
                        // Safely get all values
                        $current_stock = $alert['current_stock'] ?? 0;
                        $threshold = $alert['threshold'] ?? 5;
                        $below_by = $alert['below_threshold_by'] ?? 0;
                        
                        // Calculate if not provided
                        if (!isset($alert['below_threshold_by']) || $below_by === null) {
                            $below_by = max(0, $threshold - $current_stock);
                        }
                        ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="product-img-container me-3">
                                        <img src="<?= htmlspecialchars($alert['image_url'] ?: 'assets/images/placeholder.jpg') ?>" 
                                             alt="<?= htmlspecialchars($alert['product_name']) ?>"
                                             class="product-img">
                                    </div>
                                    <div>
                                        <strong><?= htmlspecialchars($alert['product_name']) ?></strong>
                                        <div class="text-muted small"><?= htmlspecialchars($alert['category_name']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><small class="text-muted"><?= htmlspecialchars($alert['variant_info'] ?? 'Base Product') ?></small></td>
                            <td>
                                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2">
                                    <i class="bi bi-box me-1"></i><?= $current_stock ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2">
                                    <?= $threshold ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2">
                                    <i class="bi bi-arrow-down me-1"></i>
                                    <?php 
                                    // Safe calculation
                                    if (is_numeric($below_by)) {
                                        echo abs($below_by);
                                    } else {
                                        echo '0';
                                    }
                                    ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning" 
                                        onclick="showUpdateModal(<?= $alert['product_id'] ?>, <?= $alert['variant_id'] == 'base' ? 'null' : "'{$alert['variant_id']}'" ?>, '<?= htmlspecialchars($alert['product_name']) ?>')">
                                    <i class="bi bi-plus-circle me-1"></i> Restock
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

        <!-- Quick Stats -->
        <div class="col-lg-4">
            <div class="inventory-card h-100">
                <div class="inventory-card-header">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-speedometer2 me-2"></i>Quick Stats
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <span>In Stock Products</span>
                            </div>
                            <span class="badge bg-success"><?= $summary['total_products'] - $summary['out_of_stock_items'] ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <i class="bi bi-arrow-clockwise text-primary me-2"></i>
                                <span>Total Variants</span>
                            </div>
                            <span class="badge bg-primary"><?= $summary['total_variants'] ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <i class="bi bi-clock-history text-info me-2"></i>
                                <span>Reserved Items</span>
                            </div>
                            <span class="badge bg-info"><?= array_sum(array_column($inventory, 'total_reserved')) ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <i class="bi bi-graph-up text-success me-2"></i>
                                <span>Avg. Stock Value</span>
                            </div>
                            <span class="badge bg-success">
                                $<?= number_format($summary['total_stock_value'] / max(1, $summary['total_products']), 2) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h6 class="fw-bold mb-3">Quick Actions</h6>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary" onclick="showUpdateModal(null, null, 'Bulk Update')">
                                <i class="bi bi-plus-circle me-2"></i> Add New Stock
                            </button>
                            <a href="?page=admin&action=products" class="btn btn-outline-secondary">
                                <i class="bi bi-box me-2"></i> Manage Products
                            </a>
                            <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#bulkUpdateModal">
                                <i class="bi bi-upload me-2"></i> Import CSV
                            </button>
                            <button class="btn btn-outline-warning" onclick="generateStockReport()">
                                <i class="bi bi-file-earmark-text me-2"></i> Generate Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="inventory-card mt-4">
        <div class="inventory-card-header">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-clock-history me-2"></i>Recent Stock Activity
            </h5>
        </div>
        <div class="card-body">
            <?php if (!empty($movements)): ?>
            <div class="row g-4">
                <?php foreach ($movements as $movement): 
                    $icon = $movement['movement_type'] == 'in' ? 'bi-arrow-down-left-circle-fill text-success' : 
                           ($movement['movement_type'] == 'out' ? 'bi-arrow-up-right-circle-fill text-danger' : 
                           'bi-arrow-left-right-circle-fill text-info');
                    $badgeColor = $movement['movement_type'] == 'in' ? 'success' : 
                                 ($movement['movement_type'] == 'out' ? 'danger' : 'info');
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="border rounded p-3 h-100">
                        <div class="d-flex align-items-start mb-2">
                            <div class="flex-shrink-0">
                                <i class="bi <?= $icon ?> fs-4"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1"><?= htmlspecialchars($movement['product_name']) ?></h6>
                                <small class="text-muted d-block mb-2"><?= htmlspecialchars($movement['variant_info']) ?></small>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-<?= $badgeColor ?> bg-opacity-10 text-<?= $badgeColor ?>">
                                        <?= $movement['movement_type'] == 'in' ? '+' : '-' ?><?= $movement['quantity'] ?> units
                                    </span>
                                    <small class="text-muted"><?= date('M d, H:i', strtotime($movement['created_at'])) ?></small>
                                </div>
                            </div>
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-info-circle me-1"></i><?= htmlspecialchars($movement['reason']) ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-clock-history fs-1 text-muted opacity-50"></i>
                <p class="text-muted mt-3 mb-0">No recent stock activity</p>
                <small class="text-muted">Stock updates will appear here</small>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Enhanced Update Stock Modal -->
<div class="modal fade modal-inventory" id="updateStockModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-box-seam me-2"></i>Update Stock
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="updateStockForm">
                <div class="modal-body p-4">
                    <div id="updateAlert" class="alert" style="display: none;"></div>
                    
                    <input type="hidden" id="updateProductId" name="product_id">
                    <input type="hidden" id="updateVariantId" name="variant_id">
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold mb-2">Product</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-box"></i>
                            </span>
                            <input type="text" class="form-control" id="updateProductName" readonly>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold mb-2">New Stock Quantity</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-123"></i>
                            </span>
                            <input type="number" class="form-control" id="updateQuantity" name="quantity" 
                                   min="0" required placeholder="Enter new stock quantity">
                        </div>
                        <small class="text-muted">Enter the total quantity, not the change amount</small>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold mb-2">Reason for Update</label>
                        <select class="form-select" id="updateReason" name="reason">
                            <option value="Manual adjustment">📝 Manual adjustment</option>
                            <option value="Received shipment">📦 Received shipment</option>
                            <option value="Returned items">🔄 Returned items</option>
                            <option value="Damage/Loss">💔 Damage/Loss</option>
                            <option value="Stock take">📊 Stock take</option>
                            <option value="Other">❓ Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold mb-2">Notes (Optional)</label>
                        <textarea class="form-control" id="updateNotes" rows="3" 
                                  placeholder="Add any additional notes or details about this stock update..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Update Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Enhanced Bulk Update Modal -->
<div class="modal fade modal-inventory" id="bulkUpdateModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-upload me-2"></i>Bulk Stock Update
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info border-0 bg-info bg-opacity-10">
                    <div class="d-flex">
                        <i class="bi bi-info-circle-fill text-info fs-5 me-3"></i>
                        <div>
                            <h6 class="alert-heading">How to use bulk update</h6>
                            <p class="mb-2">Upload a CSV file with the following columns:</p>
                            <ul class="mb-0">
                                <li><code>Product ID</code> - Required</li>
                                <li><code>Variant ID</code> - Optional (leave empty for base products)</li>
                                <li><code>New Quantity</code> - Required</li>
                                <li><code>Reason</code> - Optional (default: "Bulk import")</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <form id="bulkUpdateForm" enctype="multipart/form-data" class="mt-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">CSV File</label>
                            <div class="input-group">
                                <input type="file" class="form-control" id="csvFile" name="csv_file" accept=".csv" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="downloadTemplate()">
                                    <i class="bi bi-download"></i>
                                </button>
                            </div>
                            <small class="text-muted">Max file size: 5MB</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Update Reason</label>
                            <select class="form-select" name="bulk_reason">
                                <option value="Bulk import">📦 Bulk import</option>
                                <option value="Inventory count">📊 Inventory count</option>
                                <option value="Seasonal update">🍂 Seasonal update</option>
                                <option value="Supplier delivery">🚚 Supplier delivery</option>
                            </select>
                        </div>
                    </div>
                </form>
                
                <div class="mt-4">
                    <h6 class="fw-bold mb-3">Preview</h6>
                    <div class="border rounded p-3 bg-light">
                        <div id="csvPreview" class="text-muted small">
                            <div class="text-center py-3">
                                <i class="bi bi-file-earmark-text fs-1 opacity-50"></i>
                                <p class="mt-2 mb-0">Upload a CSV file to preview data</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" onclick="processBulkUpdate()">
                    <i class="bi bi-upload me-2"></i>Process Update
                </button>
            </div>
        </div>
    </div>
</div>




<?php require_once __DIR__ . '/../footer.php'; ?>

<!-- Enhanced JavaScript -->
<script>
// Show update stock modal
function showUpdateModal(productId, variantId, productName) {
    document.getElementById('updateProductId').value = productId;
    document.getElementById('updateVariantId').value = variantId || '';
    document.getElementById('updateProductName').value = productName;
    
    // Reset form
    document.getElementById('updateQuantity').value = '';
    document.getElementById('updateReason').value = 'Manual adjustment';
    document.getElementById('updateNotes').value = '';
    
    // Clear alert
    const alert = document.getElementById('updateAlert');
    alert.style.display = 'none';
    alert.className = 'alert';
    
    const modal = new bootstrap.Modal(document.getElementById('updateStockModal'));
    modal.show();
}

// Handle stock update form submission
document.getElementById('updateStockForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const alert = document.getElementById('updateAlert');
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.innerHTML = '<i class="bi bi-arrow-clockwise spin me-2"></i>Updating...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('?page=admin&action=update-stock', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert.className = 'alert alert-success';
            alert.innerHTML = `
                <div class="d-flex">
                    <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                    <div>
                        <h6 class="alert-heading mb-1">Success!</h6>
                        <p class="mb-0">${result.message}</p>
                    </div>
                </div>`;
            alert.style.display = 'block';
            
            // Refresh page after 1.5 seconds
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            alert.className = 'alert alert-danger';
            alert.innerHTML = `
                <div class="d-flex">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                    <div>
                        <h6 class="alert-heading mb-1">Error!</h6>
                        <p class="mb-0">${result.message}</p>
                    </div>
                </div>`;
            alert.style.display = 'block';
            
            // Reset button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    } catch (error) {
        alert.className = 'alert alert-danger';
        alert.innerHTML = `
            <div class="d-flex">
                <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                <div>
                    <h6 class="alert-heading mb-1">Network Error!</h6>
                    <p class="mb-0">An error occurred. Please try again.</p>
                </div>
            </div>`;
        alert.style.display = 'block';
        console.error(error);
        
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});

// Export to CSV
function exportToCSV() {
    window.location.href = '?page=admin&action=inventory&export=csv';
}



// Export low stock CSV
function exportLowStockCSV() {
    alert('Low stock export would be implemented based on your requirements.');
}

// View product details
function viewProductDetails(productId) {
    window.location.href = `?page=product&id=${productId}`;
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
                COALESCE(pv.low_stock_threshold, 5) AS threshold,
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

// Download CSV template
function downloadTemplate() {
    const csvContent = "Product ID,Variant ID,New Quantity,Reason\n1,,50,Manual adjustment\n2,1,100,Received shipment\n3,,30,Stock take";
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'stock_update_template.csv';
    a.style.display = 'none';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
    
    // Show success message
    const toast = document.createElement('div');
    toast.className = 'position-fixed bottom-0 end-0 p-3';
    toast.innerHTML = `
        <div class="toast show" role="alert">
            <div class="toast-header bg-success text-white">
                <i class="bi bi-check-circle me-2"></i>
                <strong class="me-auto">Template Downloaded</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                Template CSV file has been downloaded successfully.
            </div>
        </div>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Preview CSV file
document.getElementById('csvFile').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    const reader = new FileReader();
    reader.onload = function(event) {
        const content = event.target.result;
        const lines = content.split('\n').slice(0, 6);
        const preview = lines.map(line => 
            `<div class="d-flex">
                ${line.split(',').map(cell => 
                    `<span class="badge bg-light text-dark me-2 mb-1">${cell}</span>`
                ).join('')}
            </div>`
        ).join('');
        
        document.getElementById('csvPreview').innerHTML = preview;
    };
    reader.readAsText(file);
});

// Process bulk update
function processBulkUpdate() {
    const form = document.getElementById('bulkUpdateForm');
    const formData = new FormData(form);
    
    // You would need to implement this on the server side
    const alert = document.createElement('div');
    alert.className = 'alert alert-info mt-3';
    alert.innerHTML = `
        <div class="d-flex">
            <i class="bi bi-info-circle-fill fs-4 me-3"></i>
            <div>
                <h6 class="alert-heading mb-1">Coming Soon!</h6>
                <p class="mb-0">Bulk update functionality will be available in the next update.</p>
            </div>
        </div>
    `;
    document.querySelector('#bulkUpdateModal .modal-body').appendChild(alert);
    
    setTimeout(() => alert.remove(), 5000);
}

// Generate stock report
function generateStockReport() {
    // You can implement PDF or detailed report generation here
    const modal = new bootstrap.Modal(document.getElementById('bulkUpdateModal'));
    modal.hide();
    
    setTimeout(() => {
        alert('📊 Report generation feature coming soon!');
    }, 300);
}

// Add this method to your Inventory.php class
public function confirmOrderStock($orderId) {
    error_log("=== CONFIRM ORDER STOCK ===");
    error_log("Order ID: $orderId");
    
    try {
        $this->db->beginTransaction();
        
        // Get order info
        $orderQuery = "SELECT order_number FROM orders WHERE id = :order_id";
        $orderStmt = $this->db->prepare($orderQuery);
        $orderStmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $orderStmt->execute();
        $order = $orderStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$order) {
            throw new Exception("Order not found: #$orderId");
        }
        
        // Get all order items with their details
        $query = "
            SELECT 
                oi.id as order_item_id,
                oi.product_id,
                oi.variant_id,
                oi.quantity,
                p.name as product_name,
                p.stock_quantity as product_stock,
                pv.stock_quantity as variant_stock,
                pv.reserved_quantity as variant_reserved
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            LEFT JOIN product_variants pv ON oi.variant_id = pv.id
            WHERE oi.order_id = :order_id
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($items)) {
            throw new Exception("No items found for order #$orderId");
        }
        
        error_log("Processing " . count($items) . " items");
        
        foreach ($items as $item) {
            $productId = $item['product_id'];
            $variantId = $item['variant_id'];
            $quantity = $item['quantity'];
            $productName = $item['product_name'];
            
            error_log("Item: $productName, Qty: $quantity, Variant: " . ($variantId ?: 'None'));
            
            if ($variantId && $variantId > 0) {
                // Handle VARIANT stock reduction
                error_log("Reducing VARIANT stock: ID $variantId");
                
                $currentVariantStock = $item['variant_stock'] ?? 0;
                $currentReserved = $item['variant_reserved'] ?? 0;
                $newVariantStock = $currentVariantStock - $quantity;
                $newReserved = max(0, $currentReserved - $quantity);
                
                // Update variant
                $updateQuery = "
                    UPDATE product_variants 
                    SET stock_quantity = :new_stock,
                        reserved_quantity = :new_reserved
                    WHERE id = :variant_id
                ";
                
                $updateStmt = $this->db->prepare($updateQuery);
                $updateStmt->bindValue(':new_stock', $newVariantStock, PDO::PARAM_INT);
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
                $logStmt->bindValue(':previous_quantity', $currentVariantStock, PDO::PARAM_INT);
                $logStmt->bindValue(':new_quantity', $newVariantStock, PDO::PARAM_INT);
                $logStmt->bindValue(':reason', "Order #{$order['order_number']} confirmed");
                $logStmt->bindValue(':reference_id', $orderId, PDO::PARAM_INT);
                $logStmt->bindValue(':product_name', $productName);
                $logStmt->execute();
                
                error_log("Variant updated: $currentVariantStock → $newVariantStock");
                
            } else {
                // Handle BASE PRODUCT stock reduction (no variant)
                error_log("Reducing BASE PRODUCT stock: ID $productId");
                
                $currentProductStock = $item['product_stock'] ?? 0;
                $newProductStock = $currentProductStock - $quantity;
                
                // Update base product
                $updateQuery = "UPDATE products SET stock_quantity = :new_stock WHERE id = :product_id";
                $updateStmt = $this->db->prepare($updateQuery);
                $updateStmt->bindValue(':new_stock', $newProductStock, PDO::PARAM_INT);
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
                $logStmt->bindValue(':previous_quantity', $currentProductStock, PDO::PARAM_INT);
                $logStmt->bindValue(':new_quantity', $newProductStock, PDO::PARAM_INT);
                $logStmt->bindValue(':reason', "Order #{$order['order_number']} confirmed");
                $logStmt->bindValue(':reference_id', $orderId, PDO::PARAM_INT);
                $logStmt->bindValue(':product_name', $productName);
                $logStmt->execute();
                
                error_log("Product updated: $currentProductStock → $newProductStock");
            }
        }
        
        // Clear any reservations
        $this->clearOrderReservations($orderId);
        
        $this->db->commit();
        error_log("Order #{$order['order_number']} stock confirmed successfully!");
        return true;
        
    } catch (Exception $e) {
        $this->db->rollBack();
        error_log("ERROR confirming order stock: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        return false;
    }
}

// Search and filter inventory table
document.getElementById('inventorySearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase().trim();
    const rows = document.querySelectorAll('#inventoryTable tbody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const isVisible = text.includes(searchTerm);
        row.style.display = isVisible ? '' : 'none';
        if (isVisible) visibleCount++;
    });
    
    // Update count badge
    const badge = document.querySelector('.inventory-card-header h5 .badge');
    if (badge) {
        badge.textContent = `${visibleCount} products`;
    }
});

document.getElementById('categoryFilter').addEventListener('change', function(e) {
    const category = e.target.value;
    const rows = document.querySelectorAll('#inventoryTable tbody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const categoryCell = row.querySelector('td:nth-child(2)').textContent;
        const isVisible = !category || categoryCell.includes(category);
        row.style.display = isVisible ? '' : 'none';
        if (isVisible) visibleCount++;
    });
    
    // Update count badge
    const badge = document.querySelector('.inventory-card-header h5 .badge');
    if (badge) {
        badge.textContent = `${visibleCount} products`;
    }
});

// Add spin animation for loading icons
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .spin {
        animation: spin 1s linear infinite;
    }
`;
document.head.appendChild(style);

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>