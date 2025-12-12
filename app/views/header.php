<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RafiaShop - Custom Fashion & Home Products</title>
    
    <!-- ADD THESE 3 LINES - Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Keep your existing Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Your Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Optional: Add Bootstrap JS at the bottom if needed -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
</head>
<body>
    <?php
    // Start session at the VERY BEGINNING
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    ?>
    
    <!-- KEEP YOUR EXISTING NAVIGATION EXACTLY AS IS -->
    <nav style="background: #5d23e6ff; color: white; padding: 15px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto;">
            <div>
                <a href="?page=home" style="font-size: 1.5rem; font-weight: bold; color: white; text-decoration: none;">
                    <i class="fas fa-shopping-bag"></i> RafiaShop
                </a>
            </div>
            <div style="display: flex; gap: 20px; align-items: center;">
                <a href="?page=home" style="color: white; text-decoration: none;">
                    <i class="fas fa-home"></i> Home
                </a>
                <a href="?page=products" style="color: white; text-decoration: none;">
                    <i class="fas fa-store"></i> Products
                </a>
                <a href="?page=cart" style="color: white; text-decoration: none; position: relative;">
                    <i class="fas fa-shopping-cart"></i> Cart
                    <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                        <span style="position: absolute; top: -8px; right: -8px; background: #e74c3c; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 0.7rem; display: flex; align-items: center; justify-content: center;">
                            <?php echo count($_SESSION['cart']); ?>
                        </span>
                    <?php endif; ?>
                </a>
                <a href="?page=search" style="color: white; text-decoration: none;">
                    <i class="fas fa-search"></i> Search
                </a>
                
                <?php if (isset($_SESSION['user'])): ?>
                    <!-- User Dashboard -->
                    <a href="?page=user" style="color: white; text-decoration: none;">
                        <i class="fas fa-user-circle"></i> My Account
                    </a>
                    
                    <!-- Admin Dashboard - Only for admins -->
                    <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin'): ?>
                        <a href="?page=admin" style="color: #f39c12; text-decoration: none; font-weight: bold;">
                            <i class="fas fa-crown"></i> Admin Panel
                        </a>
                    <?php endif; ?>
                    
                    <span style="color: #bdc3c7;">
                        <i class="fas fa-user"></i> 
                        <?= htmlspecialchars($_SESSION['user']['first_name'] ?? 'User') ?>
                    </span>
                    
                    <a href="?page=logout" style="color: #e74c3c; text-decoration: none;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                <?php else: ?>
                    <a href="?page=login" style="color: white; text-decoration: none;">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <a href="?page=register" style="color: #3498db; text-decoration: none;">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <!-- ENHANCE YOUR NOTIFICATIONS WITH BOOTSTRAP (OPTIONAL) -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show mb-0" style="border-radius: 0; text-align: center;">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-0" style="border-radius: 0; text-align: center;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['warning'])): ?>
        <div class="alert alert-warning alert-dismissible fade show mb-0" style="border-radius: 0; text-align: center;">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            <?php echo $_SESSION['warning']; unset($_SESSION['warning']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- UPDATE THE MAIN CONTAINER FOR BOOTSTRAP COMPATIBILITY -->
    <div class="container-fluid px-3 px-lg-4 py-4" style="max-width: 1400px; margin: 0 auto;">