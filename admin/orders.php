<?php
require_once __DIR__ . '/../config/config.php';
requireAdmin();

$conn = getDBConnection();
$success = '';
$error = '';

// Update order status
if (isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = sanitizeInput($_POST['order_status']);

    // Lấy trạng thái thanh toán hiện tại
    $check = $conn->query("SELECT payment_status FROM orders WHERE id = $order_id");
    $row = $check->fetch_assoc();
    $payment_status = $row['payment_status'] ?? 'pending';

    // Chỉ cho phép chuyển sang processing/shipped/delivered nếu đã thanh toán
    $needPaid = in_array($new_status, ['processing', 'shipped', 'delivered'], true);

    if ($needPaid && $payment_status !== 'paid') {
        $error = 'Không thể xác nhận đơn hàng khi chưa thanh toán. Vui lòng đánh dấu "Đã thanh toán" trước.';
    } else {
        $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $order_id);
        if ($stmt->execute()) {
            $success = 'Đã cập nhật trạng thái đơn hàng.';
        } else {
            $error = 'Có lỗi xảy ra khi cập nhật trạng thái đơn hàng.';
        }
        $stmt->close();
    }
}

// Update payment status
if (isset($_POST['update_payment'])) {
    $order_id = (int)$_POST['order_id'];
    $new_payment_status = sanitizeInput($_POST['payment_status']);

    $stmt = $conn->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_payment_status, $order_id);
    if ($stmt->execute()) {
        // Nếu vừa đánh dấu đã thanh toán và đơn đang chờ xử lý thì tự chuyển sang đang xử lý
        $conn->query("UPDATE orders SET order_status = 'processing' WHERE id = $order_id AND order_status = 'pending' AND payment_status = 'paid'");
        $success = 'Đã cập nhật trạng thái thanh toán.';
    } else {
        $error = 'Có lỗi xảy ra khi cập nhật thanh toán.';
    }
    $stmt->close();
}

// Get all orders
$query = "SELECT o.*, u.full_name, u.email FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC";
$result = $conn->query($query);

$pageTitle = 'Quản lý đơn hàng';
include '../includes/header.php';
?>

<div class="admin-container">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="admin-main">
        <div class="admin-header">
            <h1><i class="fas fa-shopping-cart"></i> Quản lý đơn hàng</h1>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
    
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Thanh toán</th>
                    <th>Ngày đặt</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                        <td>
                            <?php echo htmlspecialchars($order['full_name']); ?><br>
                            <small><?php echo htmlspecialchars($order['email']); ?></small>
                        </td>
                        <td><?php echo formatPrice($order['total_amount']); ?></td>
                        <td>
                            <form method="POST" action="" style="display: inline-block;">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="order_status" onchange="this.form.submit()" class="status-select">
                                    <option value="pending" <?php echo $order['order_status'] === 'pending' ? 'selected' : ''; ?>>Chờ xử lý</option>
                                    <option value="processing" <?php echo $order['order_status'] === 'processing' ? 'selected' : ''; ?>>Đang xử lý</option>
                                    <option value="shipped" <?php echo $order['order_status'] === 'shipped' ? 'selected' : ''; ?>>Đã giao hàng</option>
                                    <option value="delivered" <?php echo $order['order_status'] === 'delivered' ? 'selected' : ''; ?>>Đã nhận hàng</option>
                                    <option value="cancelled" <?php echo $order['order_status'] === 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                                </select>
                                <input type="hidden" name="update_status" value="1">
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="" style="display: inline-block;">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="payment_status" onchange="this.form.submit()" class="status-select">
                                    <option value="pending" <?php echo $order['payment_status'] === 'pending' ? 'selected' : ''; ?>>Chờ thanh toán</option>
                                    <option value="paid" <?php echo $order['payment_status'] === 'paid' ? 'selected' : ''; ?>>Đã thanh toán</option>
                                    <option value="failed" <?php echo $order['payment_status'] === 'failed' ? 'selected' : ''; ?>>Thất bại</option>
                                </select>
                                <input type="hidden" name="update_payment" value="1">
                            </form>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                        <td>
                            <a href="order-detail.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">Xem chi tiết</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php closeDBConnection($conn); ?>
<?php include '../includes/footer.php'; ?>

