<?php include '../app/views/header.php'; ?>

<div class="admin-header">
    <h1>Edit Product</h1>
    <p>Update product details</p>
</div>

<div class="admin-nav">
    <!-- FIXED: Use correct URL format -->
    <a href="?page=admin&action=dashboard">Dashboard</a>
    <a href="?page=admin&action=products">Products</a>
    <a href="?page=admin&action=orders">Orders</a>
    <a href="?page=admin&action=users">Users</a>
    <a href="?page=admin&action=inventory">Inventory</a>
    <a href="?page=admin&action=analytics">Analytics</a>
</div>

<!-- Success/Error Messages -->
<?php if (isset($_GET['success'])): ?>
    <div class="success"><?= htmlspecialchars($_GET['success']) ?></div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="error"><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>

<div class="admin-form">
    <!-- FIXED: Use correct action -->
    <form method="POST" action="?page=admin&action=update-product" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $product['id'] ?>">
        
        <div class="form-group">
            <label>Product Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
        </div>
        
        <div class="form-group">
            <label>Description:</label>
            <textarea name="description" required><?= htmlspecialchars($product['description']) ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Category:</label>
            <select name="category_id" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" 
                        <?= $category['id'] == $product['category_id'] ? 'selected' : '' ?>>
                        <?= $category['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Price:</label>
            <input type="number" name="base_price" step="0.01" value="<?= $product['base_price'] ?>" required>
        </div>
        
        <div class="form-group">
            <label>Current Image:</label>
            <?php if ($product['image_url']): ?>
                <div style="margin-bottom: 10px;">
                    <img src="<?= $product['image_url'] ?>" alt="Current product image" style="max-width: 200px; border-radius: 8px;">
                </div>
            <?php else: ?>
                <p>No image currently</p>
            <?php endif; ?>
            <label>Update Image (optional):</label>
            <input type="file" name="product_image" accept="image/*">
            <small>Leave empty to keep current image. Accepted formats: JPG, PNG, GIF, WebP (Max 5MB)</small>
        </div>
        
        <div class="form-group">
            <label>
                <input type="checkbox" name="featured" <?= $product['featured'] ? 'checked' : '' ?>> Featured Product
            </label>
        </div>

        <div class="form-group">
            <label>Stock Quantity:</label>
            <input type="number" name="stock_quantity" value="<?= $product['stock_quantity'] ?? 10 ?>" min="0" required>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-save">Update Product</button>
            <!-- FIXED: Cancel button goes to correct URL -->
            <a href="?page=admin&action=products" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<?php include '../app/views/footer.php'; ?>