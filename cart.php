<?php
require_once __DIR__ . '/config/config.php';
requireLogin();

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

$success = '';
$error = '';

if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Update quantity
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $cart_id => $quantity) {
        $cart_id = (int)$cart_id;
        $quantity = (int)$quantity;
        
        if ($quantity <= 0) {
            // Remove item
            $conn->query("DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id");
        } else {
            // Update quantity
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("iii", $quantity, $cart_id, $user_id);
            $stmt->execute();
            $stmt->close();
        }
    }
    header('Location: ' . SITE_URL . '/cart.php');
    exit();
}

// Remove item
if (isset($_GET['remove'])) {
    $cart_id = (int)$_GET['remove'];
    $conn->query("DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id");
    header('Location: ' . SITE_URL . '/cart.php');
    exit();
}

// Get cart items
$cart_query = "SELECT c.*, p.name, p.price, p.image, p.stock, p.compare_price 
               FROM cart c 
               LEFT JOIN products p ON c.product_id = p.id 
               WHERE c.user_id = $user_id 
               ORDER BY c.created_at DESC";
$cart_result = $conn->query($cart_query);

$total = 0;
$cart_items = [];

while ($item = $cart_result->fetch_assoc()) {
    $item_total = $item['quantity'] * $item['price'];
    $total += $item_total;
    $cart_items[] = $item;
}

$pageTitle = 'Giỏ hàng';
include 'includes/header.php';
?>

<div class="container">
    <h1>Giỏ hàng của tôi</h1>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if (count($cart_items) > 0): ?>
        <form method="POST" action="">
            <div class="cart-table">
                <table>
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Tổng</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td class="product-cell">
                                    <div class="product-image-small">
                                        <?php if ($item['image']): ?>
                                            <img src="<?php echo SITE_URL; ?>/<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                        <?php else: ?>
                                            <img src="<?php echo SITE_URL; ?>/assets/images/placeholder.jpg" alt="No image">
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-details">
                                        <h3><a href="<?php echo SITE_URL; ?>/product-detail.php?id=<?php echo $item['product_id']; ?>"><?php echo htmlspecialchars($item['name']); ?></a></h3>
                                        <?php if ($item['size']): ?>
                                            <p>Size: <?php echo htmlspecialchars($item['size']); ?></p>
                                        <?php endif; ?>
                                        <?php if ($item['color']): ?>
                                            <p>Màu: <?php echo htmlspecialchars($item['color']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="price-cell">
                                    <?php if ($item['compare_price']): ?>
                                        <span class="price-old"><?php echo formatPrice($item['compare_price']); ?></span><br>
                                    <?php endif; ?>
                                    <span class="price-current"><?php echo formatPrice($item['price']); ?></span>
                                </td>
                                <td class="quantity-cell">
                                    <input type="number" name="quantities[<?php echo $item['id']; ?>]" 
                                           value="<?php echo $item['quantity']; ?>" 
                                           min="1" 
                                           max="<?php echo $item['stock']; ?>" 
                                           class="quantity-input">
                                </td>
                                <td class="total-cell">
                                    <strong><?php echo formatPrice($item['quantity'] * $item['price']); ?></strong>
                                </td>
                                <td class="actions-cell">
                                    <a href="<?php echo SITE_URL; ?>/cart.php?remove=<?php echo $item['id']; ?>" 
                                       class="btn-remove" 
                                       onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3"><strong>Tổng cộng:</strong></td>
                            <td colspan="2"><strong class="total-amount"><?php echo formatPrice($total); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="cart-actions">
                <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-outline">Tiếp tục mua sắm</a>
                <button type="submit" name="update_cart" class="btn btn-secondary">Cập nhật giỏ hàng</button>
                <a href="<?php echo SITE_URL; ?>/checkout.php" class="btn btn-primary">Thanh toán</a>
            </div>
        </form>
    <?php else: ?>
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <h2>Giỏ hàng của bạn đang trống</h2>
            <p>Hãy thêm sản phẩm vào giỏ hàng để tiếp tục mua sắm!</p>
            <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-primary">Mua sắm ngay</a>
        </div>
    <?php endif; ?>
</div>

<?php closeDBConnection($conn); ?>
<?php include 'includes/footer.php'; ?>

