<?php
// Script để reset mật khẩu admin
require_once __DIR__ . '/config/config.php';

$password = 'admin123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

echo "Mật khẩu: " . $password . "\n";
echo "Hash: " . $hashed_password . "\n\n";

$conn = getDBConnection();

// Cập nhật mật khẩu admin
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
$stmt->bind_param("s", $hashed_password);

if ($stmt->execute()) {
    echo "✅ Đã reset mật khẩu admin thành công!\n";
    echo "Username: admin\n";
    echo "Password: admin123\n";
} else {
    echo "❌ Lỗi: " . $stmt->error . "\n";
}

$stmt->close();

// Nếu chưa có admin, tạo mới
$check_stmt = $conn->prepare("SELECT id FROM users WHERE username = 'admin'");
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    echo "\n⚠️ Tài khoản admin chưa tồn tại. Đang tạo mới...\n";
    $insert_stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, role) VALUES (?, ?, ?, ?, ?)");
    $username = 'admin';
    $email = 'admin@wearsy.com';
    $full_name = 'Admin Wearsy';
    $role = 'admin';
    
    $insert_stmt->bind_param("sssss", $username, $email, $hashed_password, $full_name, $role);
    
    if ($insert_stmt->execute()) {
        echo "✅ Đã tạo tài khoản admin thành công!\n";
    } else {
        echo "❌ Lỗi tạo tài khoản: " . $insert_stmt->error . "\n";
    }
    
    $insert_stmt->close();
}

$check_stmt->close();
closeDBConnection($conn);

echo "\nHoàn tất! Bạn có thể xóa file này sau khi reset xong.\n";
?>

