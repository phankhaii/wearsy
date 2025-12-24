<?php
require_once __DIR__ . '/../config/config.php';
requireAdmin();

$conn = getDBConnection();

$success = '';
$error = '';

// Update shipping status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_shipping'])) {
        $order_id = (int)$_POST['order_id'];
        $order_status = sanitizeInput($_POST['order_status']);
        $tracking_number = sanitizeInput($_POST['tracking_number'] ?? '');
        
        // Chỉ cho phép chuyển sang shipped/delivered nếu đã thanh toán
        $needPaid = in_array($order_status, ['shipped', 'delivered'], true);
        $paymentStatusRow = $conn->query("SELECT payment_status FROM orders WHERE id = $order_id")->fetch_assoc();
        $payment_status = $paymentStatusRow['payment_status'] ?? 'pending';

        if ($needPaid && $payment_status !== 'paid') {
            $error = 'Không thể cập nhật giao hàng khi đơn chưa thanh toán.';
        } else {
            // Update order status
            $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
            $stmt->bind_param("si", $order_status, $order_id);
        
            if ($stmt->execute()) {
            // If status is shipped, you can add tracking number to notes
            if ($order_status === 'shipped' && $tracking_number) {
                $update_notes = $conn->prepare("UPDATE orders SET notes = CONCAT(COALESCE(notes, ''), '\nMã vận đơn: ', ?) WHERE id = ?");
                $update_notes->bind_param("si", $tracking_number, $order_id);
                $update_notes->execute();
                $update_notes->close();
            }
            
                $success = 'Đã cập nhật trạng thái giao hàng thành công!';
            } else {
                $error = 'Có lỗi xảy ra khi cập nhật!';
            }
        
            $stmt->close();
        }
    }
}

// Get filter parameters
$status_filter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';
$date_from = isset($_GET['date_from']) ? sanitizeInput($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? sanitizeInput($_GET['date_to']) : '';

// Build query
$where = "1=1";
if ($status_filter) {
    $where .= " AND order_status = '$status_filter'";
}
if ($date_from) {
    $where .= " AND DATE(created_at) >= '$date_from'";
}
if ($date_to) {
    $where .= " AND DATE(created_at) <= '$date_to'";
}

// Get orders for shipping
$query = "SELECT o.*, u.full_name, u.phone, u.email 
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.id 
          WHERE $where 
          ORDER BY 
            CASE order_status 
                WHEN 'pending' THEN 1
                WHEN 'processing' THEN 2
                WHEN 'shipped' THEN 3
                WHEN 'delivered' THEN 4
                WHEN 'cancelled' THEN 5
            END,
            o.created_at DESC";
$result = $conn->query($query);

// Statistics
$pending_shipping = $conn->query("SELECT COUNT(*) as total FROM orders WHERE order_status IN ('pending', 'processing')")->fetch_assoc()['total'];
$shipped_today = $conn->query("SELECT COUNT(*) as total FROM orders WHERE order_status = 'shipped' AND DATE(updated_at) = CURDATE()")->fetch_assoc()['total'];
$delivered_today = $conn->query("SELECT COUNT(*) as total FROM orders WHERE order_status = 'delivered' AND DATE(updated_at) = CURDATE()")->fetch_assoc()['total'];

$pageTitle = 'Quản lý giao hàng';
include '../includes/header.php';
?>

<div class="admin-container">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="admin-main">
        <div class="admin-header">
            <h1><i class="fas fa-truck"></i> Quản lý giao hàng</h1>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $pending_shipping; ?></h3>
                    <p>Đang chờ giao</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $shipped_today; ?></h3>
                    <p>Đã giao hôm nay</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $delivered_today; ?></h3>
                    <p>Hoàn thành hôm nay</p>
                </div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filters-section">
            <form method="GET" action="" class="filter-form">
                <div class="filter-group">
                    <label>Lọc theo trạng thái:</label>
                    <select name="status" onchange="this.form.submit()">
                        <option value="">Tất cả</option>
                        <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Chờ xử lý</option>
                        <option value="processing" <?php echo $status_filter === 'processing' ? 'selected' : ''; ?>>Đang xử lý</option>
                        <option value="shipped" <?php echo $status_filter === 'shipped' ? 'selected' : ''; ?>>Đã giao hàng</option>
                        <option value="delivered" <?php echo $status_filter === 'delivered' ? 'selected' : ''; ?>>Đã nhận hàng</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Từ ngày:</label>
                    <input type="date" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>">
                </div>
                
                <div class="filter-group">
                    <label>Đến ngày:</label>
                    <input type="date" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>">
                </div>
                
                <button type="submit" class="btn btn-primary">Lọc</button>
                <a href="shipping.php" class="btn btn-outline">Reset</a>
            </form>
        </div>
        
        <!-- Shipping Table -->
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Địa chỉ giao hàng</th>
                        <th>Điện thoại</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày đặt</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                            <td>
                                <?php echo htmlspecialchars($order['full_name']); ?><br>
                                <small><?php echo htmlspecialchars($order['email']); ?></small>
                            </td>
                            <td><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></td>
                            <td><?php echo htmlspecialchars($order['phone']); ?></td>
                            <td><?php echo formatPrice($order['total_amount']); ?></td>
                            <td>
                                <form method="POST" action="" class="shipping-status-form">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="order_status" class="status-select" onchange="this.form.submit()">
                                        <option value="pending" <?php echo $order['order_status'] === 'pending' ? 'selected' : ''; ?>>Chờ xử lý</option>
                                        <option value="processing" <?php echo $order['order_status'] === 'processing' ? 'selected' : ''; ?>>Đang xử lý</option>
                                        <option value="shipped" <?php echo $order['order_status'] === 'shipped' ? 'selected' : ''; ?>>Đã giao hàng</option>
                                        <option value="delivered" <?php echo $order['order_status'] === 'delivered' ? 'selected' : ''; ?>>Đã nhận hàng</option>
                                        <option value="cancelled" <?php echo $order['order_status'] === 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                                    </select>
                                    <input type="hidden" name="update_shipping" value="1">
                                    
                                    <?php if ($order['order_status'] === 'shipped' || $order['order_status'] === 'processing'): ?>
                                        <input type="text" name="tracking_number" placeholder="Mã vận đơn" class="tracking-input" style="margin-top: 5px; width: 100%; padding: 5px;">
                                    <?php endif; ?>
                                </form>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            <td>
                                <a href="order-detail.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">Chi tiết</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php closeDBConnection($conn); ?>
<?php include '../includes/footer.php'; ?>

