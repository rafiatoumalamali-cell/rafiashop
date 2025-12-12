<?php include '../app/views/header.php'; ?>

<h1>Our Products</h1>

<div class="products-grid">
    <?php if (empty($products)): ?>
        <p>No products found. Add some products to the database.</p>
    <?php else: ?>
        <?php foreach ($products as $product): ?>
            <?php if (!empty($product) && is_array($product)): // Check if product exists and is valid ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if (!empty($product['image_url'])): ?>
                            <img src="<?= $product['image_url'] ?>" alt="<?= htmlspecialchars($product['name'] ?? 'Product') ?>">
                        <?php else: ?>
                            <div style="color: #6c757d; text-align: center; padding: 20px;">No Image Available</div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3><?= htmlspecialchars($product['name'] ?? 'Unnamed Product') ?></h3>
                        <p class="description"><?= htmlspecialchars($product['description'] ?? 'No description available') ?></p>
             
                        <span class="price"><?= CurrencyHelper::format($product['base_price']) ?></span>
                        
                        <!-- Stock Level Display -->
                        <div class="stock-info">
                            <?php if (($product['stock_quantity'] ?? 0) > 10): ?>
                                <span class="in-stock">✓ In Stock</span>
                            <?php elseif (($product['stock_quantity'] ?? 0) > 0): ?>
                                <span class="low-stock">Only <?= $product['stock_quantity'] ?> left</span>
                            <?php else: ?>
                                <span class="out-of-stock">Out of Stock</span>
                            <?php endif; ?>
                        </div>
                        
                        <p class="category">Category: <?= $product['category_name'] ?? 'Uncategorized' ?></p>
                        <a href="?page=product&id=<?= $product['id'] ?? 0 ?>" class="view-details">View Details</a>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include '../app/views/footer.php'; ?>