<?php
require_once __DIR__ . '/../config/config.php';
requireAdmin();

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$order_id) {
    header('Location: ' . SITE_URL . '/admin/orders.php');
    exit();
}

$conn = getDBConnection();

// Get order
$order_query = "SELECT o.*, u.full_name, u.email FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ?";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows === 0) {
    header('Location: ' . SITE_URL . '/admin/orders.php');
    exit();
}

$order = $order_result->fetch_assoc();
$stmt->close();

// Get order items
$items_query = "SELECT oi.*, p.image FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?";
$stmt = $conn->prepare($items_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();

$pageTitle = 'Chi tiết đơn hàng';
include '../includes/header.php';
?>

<div class="admin-container">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="admin-main">
        <div class="admin-header">
            <h1><i class="fas fa-file-invoice"></i> Chi tiết đơn hàng #<?php echo htmlspecialchars($order['order_number']); ?></h1>
        </div>
    
    <div class="order-detail-box">
        <div class="detail-section">
            <h2>Thông tin khách hàng</h2>
            <p><strong>Họ tên:</strong> <?php echo htmlspecialchars($order['full_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
            <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
            <p><strong>Địa chỉ giao hàng:</strong> <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
        </div>
        
        <div class="detail-section">
            <h2>Thông tin đơn hàng</h2>
            <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
            <p><strong>Trạng thái:</strong> 
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
            </p>
            <p><strong>Phương thức thanh toán:</strong> 
                <?php 
                $payment_methods = [
                    'cod' => 'Thanh toán khi nhận hàng',
                    'bank' => 'Chuyển khoản ngân hàng',
                    'momo' => 'Ví MoMo',
                    'zalopay' => 'Ví ZaloPay'
                ];
                echo $payment_methods[$order['payment_method']] ?? $order['payment_method'];
                ?>
            </p>
        </div>
        
        <div class="detail-section">
            <h2>Sản phẩm</h2>
            <div class="order-items-table">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Giá</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = $items_result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($item['product_name']); ?>
                                    <?php if ($item['size'] || $item['color']): ?>
                                        <br><small>
                                            <?php if ($item['size']): ?>Size: <?php echo htmlspecialchars($item['size']); ?><?php endif; ?>
                                            <?php if ($item['color']): ?><?php echo $item['size'] ? ', ' : ''; ?>Màu: <?php echo htmlspecialchars($item['color']); ?><?php endif; ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td><?php echo formatPrice($item['price']); ?></td>
                                <td><?php echo formatPrice($item['quantity'] * $item['price']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3"><strong>Tổng cộng:</strong></td>
                            <td><strong><?php echo formatPrice($order['total_amount']); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    
        <div class="order-actions">
            <a href="<?php echo SITE_URL; ?>/admin/orders.php" class="btn btn-outline">Quay lại</a>
        </div>
    </div>
</div>

<?php $stmt->close(); closeDBConnection($conn); ?>
<?php include '../includes/footer.php'; ?>

