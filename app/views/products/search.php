<?php include '../app/views/header.php'; ?>

<div class="search-header">
    <h1>Product Search</h1>
    
    <!-- Search and Filter Form -->
    <div class="search-filters">
        <form method="GET" action="?page=search" class="filter-form">
            <input type="hidden" name="page" value="search">
            
            <!-- Search Box -->
            <div class="search-box">
                <input type="text" name="q" placeholder="Search products..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                <button type="submit">🔍 Search</button>
            </div>
            
            <!-- Filters -->
            <div class="filter-options">
                <div class="filter-group">
                    <label>Category:</label>
                    <select name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($_GET['category'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Price Range:</label>
                    <input type="number" name="min_price" placeholder="Min CFA" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>" step="0.01">
                    <span>to</span>
                    <input type="number" name="max_price" placeholder="Max CFA" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>" step="0.01">
                    
                </div>
                
                <button type="submit" class="filter-btn">Apply Filters</button>
                <a href="?page=search" class="clear-filters">Clear All</a>
            </div>
        </form>
    </div>
</div>

<!-- Search Results -->
<div class="search-results">
    <h2>
        <?php if (!empty($query)): ?>
            Search results for "<?= htmlspecialchars($query) ?>"
        <?php elseif (!empty($category)): ?>
            <?php
            $catName = 'Category';
            foreach ($categories as $cat) {
                if ($cat['id'] == $category) {
                    $catName = $cat['name'];
                    break;
                }
            }
            ?>
            <?= htmlspecialchars($catName) ?> Products
        <?php elseif (!empty($minPrice) || !empty($maxPrice)): ?>
            Products <?= CurrencyHelper::format($minPrice ?? 0) ?> - <?= CurrencyHelper::format($maxPrice ?? 999) ?>
        <?php else: ?>
            All Products
        <?php endif; ?>
        <span class="result-count">(<?= count($products) ?> products)</span>
    </h2>
    
    <?php if (empty($products)): ?>
        <div class="no-results">
            <p>No products found. Try different search terms or filters.</p>
            <a href="?page=products" class="btn-primary">Browse All Products</a>
        </div>
    <?php else: ?>
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if (!empty($product['image_url'])): ?>
                            <img src="<?= $product['image_url'] ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <?php else: ?>
                            <div style="color: #6c757d; text-align: center; padding: 20px;">No Image Available</div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="description"><?= htmlspecialchars($product['description']) ?></p>
                        <p class="price"><?= CurrencyHelper::format($product['base_price']) ?> CFA</p>
                        
                        <!-- Stock Level Display -->
                        <div class="stock-info">
                            <?php if ($product['stock_quantity'] > 10): ?>
                                <span class="in-stock">✓ In Stock</span>
                            <?php elseif ($product['stock_quantity'] > 0): ?>
                                <span class="low-stock">Only <?= $product['stock_quantity'] ?> left</span>
                            <?php else: ?>
                                <span class="out-of-stock">Out of Stock</span>
                            <?php endif; ?>
                        </div>
                        
                        <p class="category">Category: <?= $product['category_name'] ?></p>
                        <a href="?page=product&id=<?= $product['id'] ?>" class="view-details">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../app/views/footer.php'; ?>