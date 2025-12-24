<?php
// Site configuration
session_start();

define('SITE_NAME', 'Wearsy');
define('SITE_URL', 'http://localhost/Wearsy');
define('UPLOAD_PATH', 'uploads/');

// Cấu hình tài khoản nhận chuyển khoản (dùng tạo mã QR)
// TODO: Bạn thay lại thông tin ngân hàng thật của mình
define('BANK_NAME', 'Ngân hàng TMCP Ngoại thương Việt Nam');
define('BANK_ACCOUNT_NUMBER', '0123456789');
define('BANK_ACCOUNT_NAME', 'NGUYEN VAN A');
// Mã ngân hàng sử dụng cho VietQR (VD: 970436 cho Vietcombank)
define('BANK_BIN', '970436');

// QR tĩnh cho các ví điện tử (bạn thay URL ảnh QR thật của shop)
define('MOMO_QR_IMAGE', SITE_URL . '/assets/images/payments/momo-qr.png');
define('ZALOPAY_QR_IMAGE', SITE_URL . '/assets/images/payments/zalopay-qr.png');

// Include database configuration
require_once __DIR__ . '/database.php';

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/login.php');
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ' . SITE_URL . '/index.php');
        exit();
    }
}

function formatPrice($price) {
    return number_format($price, 0, ',', '.') . ' đ';
}

function generateSlug($string) {
    $string = trim($string);
    $string = mb_strtolower($string, 'UTF-8');
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    $string = trim($string, '-');
    return $string;
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function getCartCount() {
    if (!isset($_SESSION['user_id'])) {
        return 0;
    }
    
    $conn = getDBConnection();
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = $row['total'] ?? 0;
    $stmt->close();
    closeDBConnection($conn);
    
    return $count;
}
?>

