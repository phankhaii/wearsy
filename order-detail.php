<?php
require_once __DIR__ . '/config/config.php';
requireLogin();

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$order_id) {
    header('Location: ' . SITE_URL . '/orders.php');
    exit();
}

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

// Get order
$order_query = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows === 0) {
    header('Location: ' . SITE_URL . '/orders.php');
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
include 'includes/header.php';
?>

<div class="container">
    <div class="order-detail-page">
        <h1>Chi tiết đơn hàng #<?php echo htmlspecialchars($order['order_number']); ?></h1>
        
        <div class="order-detail-box">
            <div class="detail-section">
                <h2>Thông tin đơn hàng</h2>
                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Mã đơn hàng:</label>
                        <span><?php echo htmlspecialchars($order['order_number']); ?></span>
                    </div>
                    <div class="detail-item">
                        <label>Ngày đặt:</label>
                        <span><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></span>
                    </div>
                    <div class="detail-item">
                        <label>Trạng thái:</label>
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
                    </div>
                    <div class="detail-item">
                        <label>Tổng tiền:</label>
                        <span class="price-large"><?php echo formatPrice($order['total_amount']); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="detail-section">
                <h2>Thông tin giao hàng</h2>
                <p><strong>Địa chỉ:</strong> <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
            </div>
            
            <div class="detail-section">
                <h2>Phương thức thanh toán</h2>
                <p>
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
                <p><strong>Trạng thái thanh toán:</strong> 
                    <?php 
                    $payment_statuses = [
                        'pending' => 'Chờ thanh toán',
                        'paid' => 'Đã thanh toán',
                        'failed' => 'Thanh toán thất bại'
                    ];
                    echo $payment_statuses[$order['payment_status']] ?? $order['payment_status'];
                    ?>
                </p>
            </div>
            
            <?php if ($order['notes']): ?>
                <div class="detail-section">
                    <h2>Ghi chú</h2>
                    <p><?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
                </div>
            <?php endif; ?>
            
            <div class="detail-section">
                <h2>Sản phẩm</h2>
                <div class="order-items-table">
                    <table>
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
                                    <td class="product-cell">
                                        <?php if ($item['image']): ?>
                                            <img src="<?php echo SITE_URL; ?>/<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="item-image">
                                        <?php endif; ?>
                                        <div>
                                            <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                                            <?php if ($item['size'] || $item['color']): ?>
                                                <p class="item-attributes">
                                                    <?php if ($item['size']): ?>Size: <?php echo htmlspecialchars($item['size']); ?><?php endif; ?>
                                                    <?php if ($item['color']): ?><?php echo $item['size'] ? ', ' : ''; ?>Màu: <?php echo htmlspecialchars($item['color']); ?><?php endif; ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td><?php echo formatPrice($item['price']); ?></td>
                                    <td><strong><?php echo formatPrice($item['quantity'] * $item['price']); ?></strong></td>
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
            <a href="<?php echo SITE_URL; ?>/orders.php" class="btn btn-outline">Quay lại danh sách đơn hàng</a>
            <?php if ($order['order_status'] === 'pending'): ?>
                <a href="<?php echo SITE_URL; ?>/cancel-order.php?id=<?php echo $order['id']; ?>" 
                   class="btn btn-danger" 
                   onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">Hủy đơn hàng</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $stmt->close(); closeDBConnection($conn); ?>
<?php include 'includes/footer.php'; ?>

