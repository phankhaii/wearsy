<?php
require_once __DIR__ . '/config/config.php';
requireLogin();

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

$error = '';
$success = '';

// Get user info
$user_query = "SELECT * FROM users WHERE id = $user_id";
$user_result = $conn->query($user_query);
$user = $user_result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitizeInput($_POST['full_name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $address = sanitizeInput($_POST['address'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($full_name) || empty($email)) {
        $error = 'Vui lòng nhập đầy đủ thông tin bắt buộc!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email không hợp lệ!';
    } else {
        // Check if email is already used by another user
        $email_check = $conn->query("SELECT id FROM users WHERE email = '$email' AND id != $user_id");
        if ($email_check->num_rows > 0) {
            $error = 'Email đã được sử dụng bởi tài khoản khác!';
        } else {
            // Update user info
            $update_query = "UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ssssi", $full_name, $email, $phone, $address, $user_id);
            
            if ($stmt->execute()) {
                $_SESSION['user_name'] = $full_name;
                $_SESSION['user_email'] = $email;
                
                // Update password if provided
                if (!empty($new_password)) {
                    if (empty($current_password)) {
                        $error = 'Vui lòng nhập mật khẩu hiện tại!';
                    } elseif (!password_verify($current_password, $user['password'])) {
                        $error = 'Mật khẩu hiện tại không chính xác!';
                    } elseif ($new_password !== $confirm_password) {
                        $error = 'Mật khẩu mới không khớp!';
                    } elseif (strlen($new_password) < 6) {
                        $error = 'Mật khẩu mới phải có ít nhất 6 ký tự!';
                    } else {
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $conn->query("UPDATE users SET password = '$hashed_password' WHERE id = $user_id");
                        $success = 'Đã cập nhật thông tin và mật khẩu thành công!';
                    }
                } else {
                    $success = 'Đã cập nhật thông tin thành công!';
                }
            } else {
                $error = 'Có lỗi xảy ra khi cập nhật thông tin!';
            }
            
            $stmt->close();
        }
    }
}

// Get updated user info
$user_query = "SELECT * FROM users WHERE id = $user_id";
$user_result = $conn->query($user_query);
$user = $user_result->fetch_assoc();

$pageTitle = 'Thông tin cá nhân';
include 'includes/header.php';
?>

<div class="container">
    <div class="profile-page">
        <div class="profile-sidebar">
            <h3>Tài khoản</h3>
            <ul class="profile-menu">
                <li><a href="<?php echo SITE_URL; ?>/profile.php" class="active">Thông tin cá nhân</a></li>
                <li><a href="<?php echo SITE_URL; ?>/orders.php">Đơn hàng của tôi</a></li>
                <li><a href="<?php echo SITE_URL; ?>/wishlist.php">Danh sách yêu thích</a></li>
            </ul>
        </div>
        
        <div class="profile-content">
            <h1>Thông tin cá nhân</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" class="profile-form">
                <div class="form-section">
                    <h2>Thông tin cơ bản</h2>
                    
                    <div class="form-group">
                        <label for="username">Tên đăng nhập:</label>
                        <input type="text" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                        <small>Không thể thay đổi tên đăng nhập</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="full_name">Họ và tên: <span class="required">*</span></label>
                        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email: <span class="required">*</span></label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Số điện thoại:</label>
                        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Địa chỉ:</label>
                        <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    </div>
                </div>
                
                <div class="form-section">
                    <h2>Đổi mật khẩu</h2>
                    <p class="form-note">Để trống nếu không muốn đổi mật khẩu</p>
                    
                    <div class="form-group">
                        <label for="current_password">Mật khẩu hiện tại:</label>
                        <input type="password" id="current_password" name="current_password">
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">Mật khẩu mới:</label>
                        <input type="password" id="new_password" name="new_password" minlength="6">
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Xác nhận mật khẩu mới:</label>
                        <input type="password" id="confirm_password" name="confirm_password" minlength="6">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-large">Cập nhật thông tin</button>
            </form>
        </div>
    </div>
</div>

<?php closeDBConnection($conn); ?>
<?php include 'includes/footer.php'; ?>

