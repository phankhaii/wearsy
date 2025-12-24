<?php
require_once __DIR__ . '/config/config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . SITE_URL . '/products.php');
    exit();
}

$product_id = (int)($_POST['product_id'] ?? 0);
$rating = (int)($_POST['rating'] ?? 0);
$comment = sanitizeInput($_POST['comment'] ?? '');

if (!$product_id || $rating < 1 || $rating > 5 || empty($comment)) {
    $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin đánh giá!';
    header('Location: ' . SITE_URL . '/product-detail.php?id=' . $product_id);
    exit();
}

$user_id = $_SESSION['user_id'];
$conn = getDBConnection();

// Check if user has purchased this product (optional check)
// Check if review already exists
$check = $conn->query("SELECT id FROM reviews WHERE user_id = $user_id AND product_id = $product_id");

if ($check->num_rows > 0) {
    $_SESSION['error'] = 'Bạn đã đánh giá sản phẩm này rồi!';
    closeDBConnection($conn);
    header('Location: ' . SITE_URL . '/product-detail.php?id=' . $product_id);
    exit();
}

// Insert review
$stmt = $conn->prepare("INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiis", $user_id, $product_id, $rating, $comment);

if ($stmt->execute()) {
    $_SESSION['success'] = 'Cảm ơn bạn đã đánh giá sản phẩm!';
} else {
    $_SESSION['error'] = 'Có lỗi xảy ra khi gửi đánh giá!';
}

$stmt->close();
closeDBConnection($conn);

header('Location: ' . SITE_URL . '/product-detail.php?id=' . $product_id);
exit();
?>

