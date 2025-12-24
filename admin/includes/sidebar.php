<?php
// Admin Sidebar Navigation
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="admin-sidebar">
    <div class="admin-logo">
        <h2><i class="fas fa-store"></i> Wearsy Admin</h2>
    </div>
    
    <nav class="admin-nav">
        <ul>
            <li class="<?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
                <a href="<?php echo SITE_URL; ?>/admin/index.php">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            
            <li class="nav-section">
                <span class="nav-section-title">Quản lý bán hàng</span>
            </li>
            
            <li class="<?php echo $current_page === 'orders.php' ? 'active' : ''; ?>">
                <a href="<?php echo SITE_URL; ?>/admin/orders.php">
                    <i class="fas fa-shopping-cart"></i> Đơn hàng
                    <?php
                    try {
                        if (!isset($sidebar_conn)) {
                            $sidebar_conn = getDBConnection();
                        }
                        $pending_result = $sidebar_conn->query("SELECT COUNT(*) as count FROM orders WHERE order_status = 'pending'");
                        $pending_orders = $pending_result->fetch_assoc()['count'];
                        if ($pending_orders > 0):
                    ?>
                        <span class="badge"><?php echo $pending_orders; ?></span>
                    <?php 
                        endif;
                    } catch (Exception $e) {
                        // Ignore errors in sidebar
                    }
                    ?>
                </a>
            </li>
            
            <li class="<?php echo $current_page === 'revenue.php' ? 'active' : ''; ?>">
                <a href="<?php echo SITE_URL; ?>/admin/revenue.php">
                    <i class="fas fa-chart-line"></i> Doanh thu
                </a>
            </li>
            
            <li class="<?php echo $current_page === 'shipping.php' ? 'active' : ''; ?>">
                <a href="<?php echo SITE_URL; ?>/admin/shipping.php">
                    <i class="fas fa-truck"></i> Giao hàng
                </a>
            </li>
            
            <li class="nav-section">
                <span class="nav-section-title">Quản lý sản phẩm</span>
            </li>
            
            <li class="<?php echo $current_page === 'products.php' ? 'active' : ''; ?>">
                <a href="<?php echo SITE_URL; ?>/admin/products.php">
                    <i class="fas fa-box"></i> Sản phẩm
                </a>
            </li>
            
            <li class="nav-section">
                <span class="nav-section-title">Quản lý người dùng</span>
            </li>
            
            <li class="<?php echo $current_page === 'users.php' ? 'active' : ''; ?>">
                <a href="<?php echo SITE_URL; ?>/admin/users.php">
                    <i class="fas fa-users"></i> Tài khoản
                </a>
            </li>
            
            <li class="nav-section">
                <span class="nav-section-title">Khác</span>
            </li>
            
            <li>
                <a href="<?php echo SITE_URL; ?>/index.php" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Xem website
                </a>
            </li>
            
            <li>
                <a href="<?php echo SITE_URL; ?>/logout.php">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </a>
            </li>
        </ul>
    </nav>
</div>

