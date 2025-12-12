<?php include '../app/views/header.php'; ?>

<div class="admin-header">
    <h1>Manage Products</h1>
    <p>Add, edit, or delete products</p>
</div>

<div class="admin-nav">
    <a href="?page=admin&action=dashboard">Dashboard</a>
    <a href="?page=admin&action=products">Products</a>
    <a href="?page=admin&action=orders">Orders</a>
    <a href="?page=admin&action=users">Users</a>
    <a href="?page=admin&action=inventory">Inventory</a>
    <a href="?page=admin&action=analytics">Analytics</a>
</div>



<!-- Success/Error Messages -->
<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>

<!-- Add Product Form -->
<div class="admin-form">
    <h3>Add New Product</h3>
    <form method="POST" action="?page=admin&action=add-product" enctype="multipart/form-data">
        <div class="form-group">
            <label>Product Name:</label>
            <input type="text" name="name" required class="form-control">
        </div>
        <div class="form-group">
            <label>Description:</label>
            <textarea name="description" required class="form-control" rows="3"></textarea>
        </div>
        <div class="form-group">
            <label>Category:</label>
            <select name="category_id" required class="form-control">
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Price:</label>
            <input type="number" name="base_price" step="0.01" required class="form-control">
        </div>
        <div class="form-group">
            <label>Stock Quantity:</label>
            <input type="number" name="stock_quantity" value="10" min="0" required class="form-control">
        </div>
        <div class="form-group">
            <label>Product Image:</label>
            <input type="file" name="product_image" accept="image/*" class="form-control">
            <small class="text-muted">Accepted formats: JPG, PNG, GIF, WebP (Max 5MB)</small>
        </div>
        <div class="form-group">
            <div class="form-check">
                <input type="checkbox" name="featured" class="form-check-input" id="featuredCheck">
                <label class="form-check-label" for="featuredCheck">Featured Product</label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Add Product</button>
    </form>
</div>

<!-- Products List -->
<h3 style="margin-top: 40px; margin-bottom: 20px;">All Products (<?= count($products) ?>)</h3>

<?php if (empty($products)): ?>
    <div class="alert alert-info">
        No products found. Add your first product!
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= $product['id'] ?></td>
                    <td>
                        <?php if (!empty($product['image_url'])): ?>
                            <img src="<?= $product['image_url'] ?>" 
                                 alt="<?= htmlspecialchars($product['name']) ?>" 
                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                        <?php else: ?>
                            <div style="width: 60px; height: 60px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                                <i class="fas fa-image text-muted"></i>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong><?= htmlspecialchars($product['name']) ?></strong>
                        <div class="text-muted small"><?= htmlspecialchars(substr($product['description'], 0, 50)) ?>...</div>
                    </td>
                    <td><?= htmlspecialchars($product['category_name']) ?></td>
                    <td>
                        <span class="badge bg-success">$<?= number_format($product['base_price'], 2) ?></span>
                    </td>
                    <td>
                        <?php 
                        $stock = $product['stock_quantity'] ?? 0;
                        $badgeClass = $stock == 0 ? 'bg-danger' : ($stock < 5 ? 'bg-warning' : 'bg-success');
                        ?>
                        <span class="badge <?= $badgeClass ?>"><?= $stock ?></span>
                    </td>
                    <td>
                        <?php if ($product['featured']): ?>
                            <span class="badge bg-info">Featured</span>
                        <?php endif; ?>
                        <?php if ($stock == 0): ?>
                            <span class="badge bg-danger">Out of Stock</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="?page=admin&action=edit-product&id=<?= $product['id'] ?>" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="?page=admin&action=delete-product&id=<?= $product['id'] ?>" 
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Are you sure you want to delete <?= htmlspecialchars(addslashes($product['name'])) ?>?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                            <a href="?page=admin&action=inventory" 
                               class="btn btn-sm btn-outline-info"
                               title="Update Stock">
                                <i class="fas fa-box"></i> Stock
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<style>
.admin-header {
    background: linear-gradient(135deg, #5d23e6 0%, #4a1bb9 100%);
    color: white;
    padding: 30px;
    border-radius: 10px;
    margin-bottom: 30px;
}

.admin-nav {
    display: flex;
    gap: 15px;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e9ecef;
}

.admin-nav a {
    padding: 10px 20px;
    background: #f8f9fa;
    border-radius: 5px;
    text-decoration: none;
    color: #495057;
    font-weight: 500;
    transition: all 0.3s;
}

.admin-nav a:hover {
    background: #e9ecef;
    transform: translateY(-2px);
}

.admin-nav a.active {
    background: #5d23e6;
    color: white;
}

.admin-form {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-bottom: 40px;
}

.admin-form h3 {
    margin-top: 0;
    margin-bottom: 25px;
    color: #5d23e6;
    padding-bottom: 10px;
    border-bottom: 2px solid #f0f0f0;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #495057;
}

.form-control {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #ced4da;
    border-radius: 5px;
    font-size: 16px;
    transition: border-color 0.15s;
}

.form-control:focus {
    border-color: #5d23e6;
    box-shadow: 0 0 0 0.2rem rgba(93, 35, 230, 0.25);
    outline: none;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s;
}

.btn-primary {
    background: #5d23e6;
    color: white;
}

.btn-primary:hover {
    background: #4a1bb9;
    transform: translateY(-2px);
}

.btn-outline-primary {
    border: 1px solid #5d23e6;
    color: #5d23e6;
}

.btn-outline-primary:hover {
    background: #5d23e6;
    color: white;
}

.btn-outline-danger {
    border: 1px solid #dc3545;
    color: #dc3545;
}

.btn-outline-danger:hover {
    background: #dc3545;
    color: white;
}

.alert {
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert-info {
    background: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th {
    background: #f8f9fa;
    padding: 12px;
    text-align: left;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    color: #495057;
}

.table td {
    padding: 12px;
    border-bottom: 1px solid #dee2e6;
    vertical-align: middle;
}

.table tr:hover {
    background: #f8f9fa;
}

.badge {
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 0.85em;
    font-weight: 500;
}

.bg-success { background: #28a745; color: white; }
.bg-warning { background: #ffc107; color: #212529; }
.bg-danger { background: #dc3545; color: white; }
.bg-info { background: #17a2b8; color: white; }
.bg-primary { background: #5d23e6; color: white; }

.btn-group {
    display: flex;
    gap: 5px;
}
</style>

<?php include '../app/views/footer.php'; ?>