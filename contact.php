<?php
require_once __DIR__ . '/config/config.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Vui lòng điền đầy đủ thông tin!';
    } else {
        // In a real application, you would send an email here
        // For now, we'll just show a success message
        $success = 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi trong thời gian sớm nhất.';
    }
}

$pageTitle = 'Liên hệ';
include 'includes/header.php';
?>

<div class="container">
    <div class="contact-page">
        <h1 class="page-title">Liên hệ với chúng tôi</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="contact-content">
            <div class="contact-info">
                <h2>Thông tin liên hệ</h2>
                
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <h3>Địa chỉ</h3>
                        <p>123 Đường ABC, Quận XYZ, TP.HCM, Việt Nam</p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <h3>Điện thoại</h3>
                        <p>1900 1234</p>
                        <p>0123 456 789</p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h3>Email</h3>
                        <p>support@wearsy.com</p>
                        <p>info@wearsy.com</p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <h3>Giờ làm việc</h3>
                        <p>Thứ 2 - Chủ nhật: 8:00 - 22:00</p>
                    </div>
                </div>
                
                <div class="social-links-contact">
                    <h3>Theo dõi chúng tôi</h3>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="contact-form-wrapper">
                <h2>Gửi tin nhắn</h2>
                <form method="POST" action="" class="contact-form">
                    <div class="form-group">
                        <label for="name">Họ và tên: <span class="required">*</span></label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email: <span class="required">*</span></label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Chủ đề: <span class="required">*</span></label>
                        <input type="text" id="subject" name="subject" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Tin nhắn: <span class="required">*</span></label>
                        <textarea id="message" name="message" rows="6" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Gửi tin nhắn</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.contact-page {
    padding: 40px 0;
}

.page-title {
    text-align: center;
    font-size: 42px;
    margin-bottom: 40px;
    color: var(--primary-color);
}

.contact-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
}

.contact-info h2,
.contact-form-wrapper h2 {
    font-size: 28px;
    margin-bottom: 30px;
    color: var(--primary-color);
}

.contact-item {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
}

.contact-item i {
    font-size: 24px;
    color: var(--secondary-color);
    width: 30px;
}

.contact-item h3 {
    font-size: 18px;
    margin-bottom: 5px;
    color: var(--text-dark);
}

.contact-item p {
    color: var(--text-light);
    margin-bottom: 5px;
}

.social-links-contact {
    margin-top: 40px;
}

.social-links-contact h3 {
    margin-bottom: 15px;
}

.contact-form {
    background-color: var(--bg-light);
    padding: 30px;
    border-radius: 10px;
}

@media (max-width: 768px) {
    .contact-content {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>

