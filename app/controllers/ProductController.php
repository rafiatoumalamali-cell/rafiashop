<?php
class ProductController {
    public static function list() {
        $products = Product::getAll();
        include '../app/views/products/list.php';
    }
    
    public static function show($id) {
        $product = Product::getById($id);
        include '../app/views/products/detail.php';
    }
}
?>