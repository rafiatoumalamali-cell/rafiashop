<?php
class SearchController {
    public static function search() {
        $query = $_GET['q'] ?? '';
        $category = $_GET['category'] ?? '';
        $minPrice = $_GET['min_price'] ?? '';
        $maxPrice = $_GET['max_price'] ?? '';
        
        $products = [];
        $categories = Product::getAllCategories();
        
        if (!empty($query)) {
            // Search by query
            $products = Product::search($query);
        } elseif (!empty($category)) {
            // Filter by category
            $products = Product::filterByCategory($category);
        } elseif (!empty($minPrice) || !empty($maxPrice)) {
            // Filter by price range
            $minPrice = $minPrice ?: 0;
            $maxPrice = $maxPrice ?: 999999;
            $products = Product::filterByPrice($minPrice, $maxPrice);
        } else {
            // Show all products if no filters
            $products = Product::getAll();
        }
        
        include '../app/views/products/search.php';
    }
}
?>