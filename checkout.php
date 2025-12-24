<?php
require_once __DIR__ . '/config/config.php';
requireLogin();

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

// Get cart items
$cart_query = "SELECT c.*, p.name, p.price, p.stock, p.image 
               FROM cart c 
               LEFT JOIN products p ON c.product_id = p.id 
               WHERE c.user_id = $user_id";
$cart_result = $conn->query($cart_query);

if ($cart_result->num_rows === 0) {
    header('Location: ' . SITE_URL . '/cart.php');
    exit();
}

$cart_items = [];
$total = 0;

while ($item = $cart_result->fetch_assoc()) {
    if ($item['stock'] < $item['quantity']) {
        $_SESSION['error'] = "Sản phẩm '{$item['name']}' không đủ số lượng!";
        header('Location: ' . SITE_URL . '/cart.php');
        exit();
    }
    $item_total = $item['quantity'] * $item['price'];
    $total += $item_total;
    $cart_items[] = $item;
}

// Get user info
$user_query = "SELECT * FROM users WHERE id = $user_id";
$user_result = $conn->query($user_query);
$user = $user_result->fetch_assoc();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipping_address = sanitizeInput($_POST['shipping_address'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $payment_method = sanitizeInput($_POST['payment_method'] ?? 'cod');
    $notes = sanitizeInput($_POST['notes'] ?? '');
    
    if (empty($shipping_address) || empty($phone)) {
        $error = 'Vui lòng nhập đầy đủ thông tin địa chỉ và số điện thoại!';
    } else {
        // Generate order number
        $order_number = 'ORD' . date('Ymd') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        // Create order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, order_number, total_amount, shipping_address, phone, payment_method, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isddsss", $user_id, $order_number, $total, $shipping_address, $phone, $payment_method, $notes);
        
        if ($stmt->execute()) {
            $order_id = $conn->insert_id;
            
            // Insert order items
            $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price, size, color) VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            foreach ($cart_items as $item) {
                $item_stmt->bind_param("iisidss", $order_id, $item['product_id'], $item['name'], $item['quantity'], $item['price'], $item['size'], $item['color']);
                $item_stmt->execute();
                
                // Update product stock
                $conn->query("UPDATE products SET stock = stock - {$item['quantity']} WHERE id = {$item['product_id']}");
            }
            
            $item_stmt->close();
            
            // Clear cart
            $conn->query("DELETE FROM cart WHERE user_id = $user_id");
            
            $stmt->close();
            closeDBConnection($conn);
            
            $_SESSION['order_success'] = $order_number;
            header('Location: ' . SITE_URL . '/order-success.php?order=' . $order_number);
            exit();
        } else {
            $error = 'Có lỗi xảy ra khi tạo đơn hàng! Vui lòng thử lại.';
        }
        
        $stmt->close();
    }
}

$pageTitle = 'Thanh toán';
include 'includes/header.php';
?>

<div class="container">
    <h1>Thanh toán</h1>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="checkout-page">
        <div class="checkout-form">
            <h2>Thông tin giao hàng</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="full_name">Họ và tên: <span class="required">*</span></label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required readonly>
                </div>
                
                <div class="form-group">
                    <label for="phone">Số điện thoại: <span class="required">*</span></label>
                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="shipping_address">Địa chỉ giao hàng: <span class="required">*</span></label>
                    <textarea id="shipping_address" name="shipping_address" rows="3" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="payment_method">Phương thức thanh toán: <span class="required">*</span></label>
                    <select id="payment_method" name="payment_method" required>
                        <option value="cod" selected>Thanh toán khi nhận hàng (COD)</option>
                        <option value="bank">Chuyển khoản ngân hàng</option>
                        <option value="momo">Ví MoMo</option>
                        <option value="zalopay">Ví ZaloPay</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="notes">Ghi chú (tùy chọn):</label>
                    <textarea id="notes" name="notes" rows="3"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary btn-large btn-block">Đặt hàng</button>
            </form>
        </div>
        
        <div class="checkout-summary">
            <h2>Đơn hàng của bạn</h2>
            <div class="order-items">
                <?php foreach ($cart_items as $item): ?>
                    <div class="order-item">
                        <div class="order-item-image">
                            <?php if ($item['image']): ?>
                                <img src="<?php echo SITE_URL; ?>/<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <?php else: ?>
                                <img src="<?php echo SITE_URL; ?>/assets/images/placeholder.jpg" alt="No image">
                            <?php endif; ?>
                        </div>
                        <div class="order-item-info">
                            <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                            <p>Số lượng: <?php echo $item['quantity']; ?> x <?php echo formatPrice($item['price']); ?></p>
                        </div>
                        <div class="order-item-total">
                            <?php echo formatPrice($item['quantity'] * $item['price']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="order-summary">
                <div class="summary-row">
                    <span>Tạm tính:</span>
                    <span><?php echo formatPrice($total); ?></span>
                </div>
                <div class="summary-row">
                    <span>Phí vận chuyển:</span>
                    <span>Miễn phí</span>
                </div>
                <div class="summary-row total">
                    <span><strong>Tổng cộng:</strong></span>
                    <span><strong><?php echo formatPrice($total); ?></strong></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php closeDBConnection($conn); ?>
<?php include 'includes/footer.php'; ?>

