<?php
require_once __DIR__ . '/../config/config.php';
$cartCount = getCartCount();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-top">
            <div class="container">
                <div class="header-top-content">
                    <div class="header-top-left">
                        <span><i class="fas fa-phone"></i> Hotline: 1900 1234</span>
                        <span><i class="fas fa-envelope"></i> support@wearsy.com</span>
                    </div>
                    <div class="header-top-right">
                        <?php if (isLoggedIn()): ?>
                            <a href="<?php echo SITE_URL; ?>/profile.php"><i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?></a>
                            <a href="<?php echo SITE_URL; ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                            <?php if (isAdmin()): ?>
                                <a href="<?php echo SITE_URL; ?>/admin/index.php"><i class="fas fa-cog"></i> Admin</a>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="<?php echo SITE_URL; ?>/login.php"><i class="fas fa-sign-in-alt"></i> Đăng nhập</a>
                            <a href="<?php echo SITE_URL; ?>/register.php"><i class="fas fa-user-plus"></i> Đăng ký</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="header-main">
            <div class="container">
                <div class="header-main-content">
                    <div class="logo">
                        <a href="<?php echo SITE_URL; ?>/index.php" class="logo-link">
                            <img src="<?php echo SITE_URL; ?>/assets/images/logo.svg" alt="<?php echo SITE_NAME; ?>" class="logo-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                            <span class="logo-text" style="display: none;"><?php echo SITE_NAME; ?></span>
                        </a>
                    </div>
                    
                    <div class="search-box">
                        <form action="<?php echo SITE_URL; ?>/products.php" method="GET">
                            <input type="text" name="search" placeholder="Tìm kiếm sản phẩm..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                    
                    <div class="header-actions">
                        <a href="<?php echo SITE_URL; ?>/wishlist.php" class="action-btn">
                            <i class="fas fa-heart"></i>
                            <span>Yêu thích</span>
                        </a>
                        <a href="<?php echo SITE_URL; ?>/cart.php" class="action-btn cart-btn">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Giỏ hàng</span>
                            <?php if ($cartCount > 0): ?>
                                <span class="cart-count"><?php echo $cartCount; ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <nav class="main-nav">
            <div class="container">
                <ul class="nav-menu">
                    <li><a href="<?php echo SITE_URL; ?>/index.php">Trang chủ</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/products.php?category=ao">Áo</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/products.php?category=quan">Quần</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/products.php?category=vay">Váy</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/products.php?category=giay-dep">Giày dép</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/products.php?category=phu-kien">Phụ kiện</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/products.php?category=dong-ho">Đồng hồ</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/products.php">Tất cả sản phẩm</a></li>
                </ul>
            </div>
        </nav>
    </header>

