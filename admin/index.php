<?php
require_once __DIR__ . '/../config/config.php';
requireAdmin();

$conn = getDBConnection();

// Get statistics
$stats = [];

// Total products
$result = $conn->query("SELECT COUNT(*) as total FROM products");
$stats['products'] = $result->fetch_assoc()['total'];

// Total orders
$result = $conn->query("SELECT COUNT(*) as total FROM orders");
$stats['orders'] = $result->fetch_assoc()['total'];

// Total users
$result = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
$stats['users'] = $result->fetch_assoc()['total'];

// Total revenue
$result = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE payment_status = 'paid'");
$stats['revenue'] = $result->fetch_assoc()['total'] ?? 0;

// Today's revenue
$result = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE payment_status = 'paid' AND DATE(created_at) = CURDATE()");
$stats['today_revenue'] = $result->fetch_assoc()['total'] ?? 0;

// Pending orders
$result = $conn->query("SELECT COUNT(*) as total FROM orders WHERE order_status = 'pending'");
$stats['pending_orders'] = $result->fetch_assoc()['total'] ?? 0;

// Recent orders
$recent_orders = $conn->query("SELECT o.*, u.full_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 10");

// Low stock products
$low_stock = $conn->query("SELECT * FROM products WHERE stock < 10 AND status = 'active' ORDER BY stock ASC LIMIT 5");

$pageTitle = 'Dashboard';
include '../includes/header.php';
?>

<div class="admin-container">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="admin-main">
        <div class="admin-header">
            <h1><i class="fas fa-home"></i> Dashboard - Quản trị hệ thống</h1>
        </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon products">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['products']; ?></h3>
                <p>Tổng sản phẩm</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon orders">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['orders']; ?></h3>
                <p>Tổng đơn hàng</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon users">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['users']; ?></h3>
                <p>Tổng khách hàng</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon revenue">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo formatPrice($stats['revenue']); ?></h3>
                <p>Tổng doanh thu</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo formatPrice($stats['today_revenue']); ?></h3>
                <p>Doanh thu hôm nay</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['pending_orders']; ?></h3>
                <p>Đơn chờ xử lý</p>
            </div>
        </div>
    </div>
    
    <div class="admin-content-grid">
        <div class="admin-section">
            <h2>Đơn hàng gần đây</h2>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày đặt</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $recent_orders->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                                <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                <td><?php echo formatPrice($order['total_amount']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $order['order_status']; ?>">
                                        <?php 
                                        $statuses = [
                                            'pending' => 'Chờ xử lý',
                                            'processing' => 'Đang xử lý',
                                            'shipped' => 'Đã giao hàng',
                                            'delivered' => 'Đã nhận hàng',
                                            'cancelled' => 'Đã hủy'
                                        ];
                                        echo $statuses[$order['order_status']] ?? $order['order_status'];
                                        ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <a href="order-detail.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">Xem</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="admin-section">
            <h2>Sản phẩm sắp hết hàng</h2>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Tên sản phẩm</th>
                            <th>Tồn kho</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($product = $low_stock->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><span class="low-stock"><?php echo $product['stock']; ?></span></td>
                                <td>
                                    <a href="product-edit.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">Cập nhật</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php closeDBConnection($conn); ?>
<?php include '../includes/footer.php'; ?>

