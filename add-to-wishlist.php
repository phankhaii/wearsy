<?php
require_once __DIR__ . '/config/config.php';
requireLogin();

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$product_id) {
    header('Location: ' . SITE_URL . '/products.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$conn = getDBConnection();

// Check if already in wishlist
$check = $conn->query("SELECT id FROM wishlist WHERE user_id = $user_id AND product_id = $product_id");

if ($check->num_rows > 0) {
    // Remove from wishlist
    $conn->query("DELETE FROM wishlist WHERE user_id = $user_id AND product_id = $product_id");
    $_SESSION['success'] = 'Đã xóa khỏi danh sách yêu thích!';
} else {
    // Add to wishlist
    $conn->query("INSERT INTO wishlist (user_id, product_id) VALUES ($user_id, $product_id)");
    $_SESSION['success'] = 'Đã thêm vào danh sách yêu thích!';
}

closeDBConnection($conn);

$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : SITE_URL . '/products.php';
header('Location: ' . $redirect);
exit();
?>

