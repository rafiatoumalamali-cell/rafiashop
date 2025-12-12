<?php
// At the VERY TOP of index.php
ob_start(); // Start output buffering
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Add this to check for output
if (headers_sent($filename, $linenum)) {
    echo "Headers already sent in $filename on line $linenum\n";
    exit;
}

// Include all required files
require_once '../app/models/Database.php';
require_once '../app/models/Product.php';
require_once '../app/models/Cart.php';
require_once '../app/models/User.php';
require_once '../app/models/Order.php';
require_once '../app/models/Admin.php';
require_once '../app/models/Payment.php';
require_once '../app/models/Analytics.php';
require_once '../app/models/Inventory.php';

require_once '../app/controllers/ProductController.php';
require_once '../app/controllers/CartController.php';
require_once '../app/controllers/AuthController.php';
require_once '../app/controllers/CheckoutController.php';
require_once '../app/controllers/AdminController.php';
require_once '../app/controllers/SearchController.php';
require_once '../app/controllers/PasswordController.php';
require_once '../app/controllers/UserController.php';
require_once '../app/controllers/AnalyticsController.php';
require_once __DIR__ . '/../app/helpers/CurrencyHelper.php';


// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get page and action parameters
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? '';

// DEBUG: Log what's being requested
error_log("=== ROUTING DEBUG ===");
error_log("Page: $page, Action: $action");
error_log("====================");


// Route the request
switch($page) {
    case 'home':
        include '../app/views/home.php';
        break;
        
    case 'products':
        ProductController::list();
        break;
        
    case 'product':
        $id = $_GET['id'] ?? 1;
        ProductController::show($id);
        break;
        
    // ===== ADD SEARCH ROUTE HERE =====
    case 'search':
        SearchController::search();
        break;
    // =================================
        
    case 'cart':
        // Handle cart actions
        switch($action) {
            case 'add':
                CartController::add();
                break;
            case 'remove':
                CartController::remove();
                break;
            case 'update-quantity':
                CartController::updateQuantity();
                break;
            case 'checkout':
                CartController::checkout();
                break;
            case 'clear':
                CartController::clear();
                break;
            default:
                CartController::view();
        }
        break;
        
    // Auth routes
    case 'register':
        if ($action === 'process') {
            AuthController::register();
        } else {
            AuthController::showRegister();
        }
        break;
        
    case 'login':
        if ($action === 'process') {
            AuthController::login();
        } elseif ($action === 'ajax') {
            AuthController::ajaxLogin();
        } else {
            AuthController::showLogin();
        }
        break;
        
    case 'logout':
        AuthController::logout();
        break;
    
    case 'ajax-login':
        AuthController::ajaxLogin();
        break;
        
    // Checkout routes
    case 'checkout':
        if ($action === 'process') {
            CheckoutController::process();
        } else {
            CheckoutController::show();
        }
        break;
        
    case 'order-confirmation':
        CheckoutController::confirmation();
        break;
        
    // Admin routes - FIXED WITH CORRECT ACTION NAMES
    case 'admin':
        switch($action) {
            case 'dashboard':
                AdminController::dashboard();
                break;
            case 'products':
                AdminController::products();
                break;
            case 'orders':
                AdminController::orders();
                break;
            case 'users':
                AdminController::users();
                break;
            case 'add-product':
                AdminController::addProduct();
                break;
            case 'edit-product':
                AdminController::editProduct();
                break;
            case 'update-product':
                AdminController::updateProduct();
                break;
            case 'delete-product':
                AdminController::deleteProduct();
                break;
            case 'update-order':
                AdminController::updateOrderStatus();
                break;
            case 'analytics':
                AnalyticsController::dashboard();
                break;
            case 'inventory':
                AdminController::inventory();
                break;
            case 'update-stock':
                AdminController::updateStock();
                break;
            default:
                AdminController::dashboard();
        }
        break;
        
    // Payment routes
    case 'payment':
        switch($action) {
            case 'success':
                CheckoutController::paymentSuccess();
                break;
            case 'cancel':
                CheckoutController::paymentCancel();
                break;
            case 'failed':
                CheckoutController::paymentFailed();
                break;
            case 'stripe-checkout':
                CheckoutController::stripeCheckout();
                break;
        }
        break;
        
    // Password reset routes
    case 'password':
        switch($action) {
            case 'forgot':
                PasswordController::showForgotPassword();
                break;
            case 'verify-security':
                PasswordController::verifySecurityQuestion();
                break;
            case 'process-security':
                PasswordController::processSecurityAnswer();
                break;
            case 'update':
                PasswordController::updatePassword();
                break;
        }
        break;
        
    // User Dashboard Routes
    case 'user':
        switch($action) {
            case 'dashboard':
                UserController::dashboard();
                break;
            case 'orders':
                UserController::orderHistory();
                break;
            case 'order-details':
                UserController::orderDetails();
                break;
            case 'profile':
                UserController::profile();
                break;
            case 'update-profile':
                UserController::updateProfile();
                break;
            case 'cancel-order':
                UserController::cancelOrder();
            default:
                UserController::dashboard();

        }
        break;
        
        
        
    // Analytics API
    case 'api':
        if ($action === 'sales-data') {
            AnalyticsController::getSalesData();
        }
        break;
        
    // ===== ADD FALLBACK FOR UPDATE-STOCK (if accessed directly) =====
    case 'update-stock':
        AdminController::updateStock();
        break;
    // ================================================================
        
    default:
        include '../app/views/home.php';
        break;
}
?>