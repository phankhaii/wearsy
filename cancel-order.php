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

// Check if order belongs to user and is cancellable
$order_query = "SELECT * FROM orders WHERE id = ? AND user_id = ? AND order_status = 'pending'";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows === 0) {
    $_SESSION['error'] = 'Không thể hủy đơn hàng này!';
    $stmt->close();
    closeDBConnection($conn);
    header('Location: ' . SITE_URL . '/orders.php');
    exit();
}

$order = $order_result->fetch_assoc();
$stmt->close();

// Update order status
$conn->query("UPDATE orders SET order_status = 'cancelled' WHERE id = $order_id");

// Restore product stock
$items_query = "SELECT * FROM order_items WHERE order_id = $order_id";
$items_result = $conn->query($items_query);
while ($item = $items_result->fetch_assoc()) {
    $conn->query("UPDATE products SET stock = stock + {$item['quantity']} WHERE id = {$item['product_id']}");
}

closeDBConnection($conn);

$_SESSION['success'] = 'Đã hủy đơn hàng thành công!';
header('Location: ' . SITE_URL . '/orders.php');
exit();
?>

