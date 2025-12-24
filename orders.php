<?php
require_once __DIR__ . '/config/config.php';
requireLogin();

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

// Get user orders
$orders_query = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC";
$orders_result = $conn->query($orders_query);

$pageTitle = 'Đơn hàng của tôi';
include 'includes/header.php';
?>

<div class="container">
    <h1>Đơn hàng của tôi</h1>
    
    <?php if ($orders_result->num_rows > 0): ?>
        <div class="orders-list">
            <?php while ($order = $orders_result->fetch_assoc()): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-info">
                            <h3>Đơn hàng #<?php echo htmlspecialchars($order['order_number']); ?></h3>
                            <p>Ngày đặt: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                        </div>
                        <div class="order-status">
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
                            <br>
                            <small>
                                <?php 
                                $payment_statuses = [
                                    'pending' => 'Chưa thanh toán',
                                    'paid' => 'Đã thanh toán',
                                    'failed' => 'Thanh toán thất bại'
                                ];
                                echo $payment_statuses[$order['payment_status']] ?? $order['payment_status'];
                                ?>
                            </small>
                        </div>
                    </div>
                    
                    <div class="order-details">
                        <p><strong>Tổng tiền:</strong> <?php echo formatPrice($order['total_amount']); ?></p>
                        <p><strong>Địa chỉ giao hàng:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?></p>
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
                    
                    <div class="order-actions">
                        <a href="<?php echo SITE_URL; ?>/order-detail.php?id=<?php echo $order['id']; ?>" class="btn btn-outline">Xem chi tiết</a>
                        <?php if ($order['order_status'] === 'pending'): ?>
                            <a href="<?php echo SITE_URL; ?>/cancel-order.php?id=<?php echo $order['id']; ?>" 
                               class="btn btn-danger" 
                               onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">Hủy đơn hàng</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-orders">
            <i class="fas fa-shopping-bag"></i>
            <h2>Bạn chưa có đơn hàng nào</h2>
            <p>Hãy mua sắm và đặt hàng ngay để trải nghiệm dịch vụ của chúng tôi!</p>
            <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-primary">Mua sắm ngay</a>
        </div>
    <?php endif; ?>
</div>

<?php closeDBConnection($conn); ?>
<?php include 'includes/footer.php'; ?>

