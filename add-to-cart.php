<?php
require_once __DIR__ . '/config/config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    $size = isset($_POST['size']) ? sanitizeInput($_POST['size']) : '';
    $color = isset($_POST['color']) ? sanitizeInput($_POST['color']) : '';
} else {
    $product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $quantity = 1;
    $size = '';
    $color = '';
}

if (!$product_id) {
    header('Location: ' . SITE_URL . '/products.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$conn = getDBConnection();

// Check if product exists and is available
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND status = 'active'");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = 'Sản phẩm không tồn tại hoặc đã ngừng bán.';
    header('Location: ' . SITE_URL . '/products.php');
    exit();
}

$product = $result->fetch_assoc();
$stmt->close();

// Check stock
if ($product['stock'] < $quantity) {
    $_SESSION['error'] = 'Số lượng sản phẩm không đủ!';
    header('Location: ' . SITE_URL . '/product-detail.php?id=' . $product_id);
    exit();
}

// Check if item already in cart
$stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ? AND (size = ? OR size IS NULL) AND (color = ? OR color IS NULL)");
$stmt->bind_param("iiss", $user_id, $product_id, $size, $color);
$stmt->execute();
$existing = $stmt->get_result();

if ($existing->num_rows > 0) {
    // Update quantity
    $cart_item = $existing->fetch_assoc();
    $new_quantity = $cart_item['quantity'] + $quantity;
    
    if ($new_quantity > $product['stock']) {
        $_SESSION['error'] = 'Số lượng sản phẩm trong giỏ hàng vượt quá số lượng có sẵn!';
        header('Location: ' . SITE_URL . '/product-detail.php?id=' . $product_id);
        exit();
    }
    
    $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $update_stmt->bind_param("ii", $new_quantity, $cart_item['id']);
    $update_stmt->execute();
    $update_stmt->close();
    
    $_SESSION['success'] = 'Đã cập nhật số lượng sản phẩm trong giỏ hàng!';
} else {
    // Insert new item
    $insert_stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity, size, color) VALUES (?, ?, ?, ?, ?)");
    $insert_stmt->bind_param("iiiss", $user_id, $product_id, $quantity, $size, $color);
    
    if ($insert_stmt->execute()) {
        $_SESSION['success'] = 'Đã thêm sản phẩm vào giỏ hàng!';
    } else {
        $_SESSION['error'] = 'Có lỗi xảy ra khi thêm vào giỏ hàng!';
    }
    
    $insert_stmt->close();
}

$stmt->close();
closeDBConnection($conn);

header('Location: ' . SITE_URL . '/cart.php');
exit();
?>

