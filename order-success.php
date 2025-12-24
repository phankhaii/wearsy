<?php
require_once __DIR__ . '/config/config.php';
requireLogin();

$order_number = isset($_GET['order']) ? sanitizeInput($_GET['order']) : '';

if (empty($order_number)) {
    header('Location: ' . SITE_URL . '/index.php');
    exit();
}

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

$order_query = "SELECT * FROM orders WHERE order_number = ? AND user_id = ?";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("si", $order_number, $user_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows === 0) {
    header('Location: ' . SITE_URL . '/index.php');
    exit();
}

$order = $order_result->fetch_assoc();
$stmt->close();

// Get order items
$items_query = "SELECT * FROM order_items WHERE order_id = ?";
$stmt = $conn->prepare($items_query);
$stmt->bind_param("i", $order['id']);
$stmt->execute();
$items_result = $stmt->get_result();

$pageTitle = 'Đặt hàng thành công';
include 'includes/header.php';
?>

<div class="container">
    <div class="order-success">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1>Đặt hàng thành công!</h1>
        <p class="order-number">Mã đơn hàng: <strong><?php echo htmlspecialchars($order['order_number']); ?></strong></p>
        <p>Cảm ơn bạn đã đặt hàng tại Wearsy.</p>

        <?php if (in_array($order['payment_method'], ['bank', 'momo', 'zalopay'], true) && $order['payment_status'] === 'pending'): ?>
            <?php
            $amount = (int)$order['total_amount'];
            $qrInfo = urlencode('Thanh toan don ' . $order['order_number']);
            $bankQrUrl = 'https://img.vietqr.io/image/' . BANK_BIN . '-' . BANK_ACCOUNT_NUMBER . '-compact2.png'
                . '?amount=' . $amount
                . '&addInfo=' . $qrInfo
                . '&accountName=' . urlencode(BANK_ACCOUNT_NAME);
            ?>
            <div class="payment-qr-wrapper">
                <h2 style="text-align: center; margin-bottom: 8px;">Thanh toán đơn hàng</h2>
                <p style="text-align: center; font-size: 14px; color: #4a5568;">
                    Vui lòng thanh toán trước khi đơn hàng được xác nhận. Chọn phương thức bên dưới:
                </p>

                <div class="payment-qr-tabs" id="payment-qr-tabs">
                    <button type="button" class="payment-qr-tab active" data-target="qr-bank">
                        <i class="fas fa-university"></i> Ngân hàng
                    </button>
                    <button type="button" class="payment-qr-tab" data-target="qr-momo">
                        <i class="fas fa-mobile-alt"></i> MoMo
                    </button>
                    <button type="button" class="payment-qr-tab" data-target="qr-zalopay">
                        <i class="fas fa-wallet"></i> ZaloPay
                    </button>
                </div>

                <!-- Bank QR -->
                <div class="payment-qr-grid payment-qr-panel" id="qr-bank">
                    <div class="payment-qr-code">
                        <img src="<?php echo $bankQrUrl; ?>" alt="QR ngân hàng">
                    </div>
                    <div class="payment-qr-info">
                        <p><strong>Ngân hàng:</strong> <?php echo BANK_NAME; ?></p>
                        <p><strong>Số tài khoản:</strong> <?php echo BANK_ACCOUNT_NUMBER; ?></p>
                        <p><strong>Chủ tài khoản:</strong> <?php echo BANK_ACCOUNT_NAME; ?></p>
                        <p><strong>Số tiền:</strong> <?php echo formatPrice($order['total_amount']); ?></p>
                        <p><strong>Nội dung chuyển khoản:</strong> <code><?php echo htmlspecialchars($order['order_number']); ?></code></p>
                        <div class="payment-qr-note">
                            Vui lòng giữ nguyên <strong>nội dung chuyển khoản</strong> là mã đơn hàng để chúng tôi dễ kiểm tra.
                        </div>
                    </div>
                </div>

                <!-- MoMo QR -->
                <div class="payment-qr-grid payment-qr-panel" id="qr-momo" style="display:none;">
                    <div class="payment-qr-code">
                        <img src="<?php echo MOMO_QR_IMAGE; ?>" alt="QR MoMo">
                    </div>
                    <div class="payment-qr-info">
                        <p><strong>Ví MoMo</strong></p>
                        <p><strong>Số tiền:</strong> <?php echo formatPrice($order['total_amount']); ?></p>
                        <p><strong>Nội dung chuyển khoản:</strong> <code><?php echo htmlspecialchars($order['order_number']); ?></code></p>
                        <div class="payment-qr-note">
                            Đây là mã QR tĩnh MoMo của shop. Sau khi quét, vui lòng <strong>nhập đúng số tiền</strong> và
                            <strong>ghi mã đơn hàng</strong> ở phần ghi chú.
                        </div>
                    </div>
                </div>

                <!-- ZaloPay QR -->
                <div class="payment-qr-grid payment-qr-panel" id="qr-zalopay" style="display:none;">
                    <div class="payment-qr-code">
                        <img src="<?php echo ZALOPAY_QR_IMAGE; ?>" alt="QR ZaloPay">
                    </div>
                    <div class="payment-qr-info">
                        <p><strong>Ví ZaloPay</strong></p>
                        <p><strong>Số tiền:</strong> <?php echo formatPrice($order['total_amount']); ?></p>
                        <p><strong>Nội dung chuyển khoản:</strong> <code><?php echo htmlspecialchars($order['order_number']); ?></code></p>
                        <div class="payment-qr-note">
                            Đây là mã QR tĩnh ZaloPay của shop. Sau khi quét, vui lòng <strong>nhập đúng số tiền</strong> và
                            <strong>ghi mã đơn hàng</strong> ở phần ghi chú.
                        </div>
                    </div>
                </div>

                <p style="text-align:center; margin-top: 14px; font-size: 13px; color: #718096;">
                    Sau khi thanh toán thành công, admin sẽ kiểm tra và chuyển trạng thái đơn sang
                    <strong>Đã xác nhận / Đang xử lý</strong>.
                </p>
            </div>

            <script>
                (function() {
                    const tabs = document.querySelectorAll('.payment-qr-tab');
                    const panels = document.querySelectorAll('.payment-qr-panel');
                    tabs.forEach(tab => {
                        tab.addEventListener('click', function() {
                            const targetId = this.getAttribute('data-target');
                            tabs.forEach(t => t.classList.remove('active'));
                            this.classList.add('active');
                            panels.forEach(panel => {
                                panel.style.display = panel.id === targetId ? 'grid' : 'none';
                            });
                        });
                    });
                })();
            </script>
        <?php else: ?>
            <p>Chúng tôi sẽ xử lý đơn hàng của bạn trong thời gian sớm nhất.</p>
        <?php endif; ?>
        
        <div class="order-details-box">
            <h2>Chi tiết đơn hàng</h2>
            <div class="order-info">
                <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                <p><strong>Địa chỉ giao hàng:</strong> <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
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
            </div>
            
            <h3>Sản phẩm đã đặt</h3>
            <div class="order-items-list">
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
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
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
        
        <div class="success-actions">
            <a href="<?php echo SITE_URL; ?>/orders.php" class="btn btn-primary">Xem đơn hàng của tôi</a>
            <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-outline">Tiếp tục mua sắm</a>
        </div>
    </div>
</div>

<?php $stmt->close(); closeDBConnection($conn); ?>
<?php include 'includes/footer.php'; ?>

