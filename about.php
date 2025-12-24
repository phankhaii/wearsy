<?php
require_once __DIR__ . '/config/config.php';

$pageTitle = 'Giới thiệu';
include 'includes/header.php';
?>

<div class="container">
    <div class="page-content">
        <h1 class="page-title">Về Wearsy</h1>
        
        <section class="about-section">
            <h2>Chào mừng đến với Wearsy</h2>
            <p>Wearsy là thương hiệu thời trang hàng đầu Việt Nam, được thành lập với sứ mệnh mang đến những sản phẩm thời trang chất lượng cao, hiện đại và phù hợp với mọi đối tượng khách hàng. Chúng tôi tự hào là địa chỉ tin cậy cho những ai yêu thích thời trang và luôn muốn thể hiện phong cách cá nhân độc đáo.</p>
        </section>
        
        <section class="about-section">
            <h2>Tầm nhìn</h2>
            <p>Trở thành thương hiệu thời trang số 1 tại Việt Nam, được khách hàng tin tưởng và yêu mến. Chúng tôi hướng tới việc tạo ra những bộ sưu tập thời trang đa dạng, phong phú, đáp ứng mọi nhu cầu và sở thích của khách hàng.</p>
        </section>
        
        <section class="about-section">
            <h2>Sứ mệnh</h2>
            <ul>
                <li>Mang đến những sản phẩm thời trang chất lượng cao với giá cả hợp lý</li>
                <li>Cung cấp dịch vụ khách hàng tuyệt vời, tận tâm và chuyên nghiệp</li>
                <li>Luôn cập nhật xu hướng thời trang mới nhất trên thế giới</li>
                <li>Đảm bảo sự hài lòng tối đa cho mọi khách hàng</li>
            </ul>
        </section>
        
        <section class="about-section">
            <h2>Giá trị cốt lõi</h2>
            <div class="values-grid">
                <div class="value-item">
                    <i class="fas fa-gem"></i>
                    <h3>Chất lượng</h3>
                    <p>Chúng tôi luôn đặt chất lượng sản phẩm lên hàng đầu, sử dụng nguyên liệu tốt nhất và quy trình sản xuất chặt chẽ.</p>
                </div>
                <div class="value-item">
                    <i class="fas fa-heart"></i>
                    <h3>Tận tâm</h3>
                    <p>Đội ngũ nhân viên của chúng tôi luôn tận tâm phục vụ khách hàng, đảm bảo mọi trải nghiệm mua sắm đều tuyệt vời nhất.</p>
                </div>
                <div class="value-item">
                    <i class="fas fa-lightbulb"></i>
                    <h3>Sáng tạo</h3>
                    <p>Chúng tôi không ngừng sáng tạo, cập nhật những xu hướng thời trang mới nhất để mang đến những sản phẩm độc đáo.</p>
                </div>
                <div class="value-item">
                    <i class="fas fa-handshake"></i>
                    <h3>Uy tín</h3>
                    <p>Uy tín và sự tin cậy là nền tảng của Wearsy, chúng tôi cam kết thực hiện đúng những gì đã hứa với khách hàng.</p>
                </div>
            </div>
        </section>
        
        <section class="about-section">
            <h2>Liên hệ với chúng tôi</h2>
            <p>Nếu bạn có bất kỳ câu hỏi hoặc ý kiến đóng góp nào, đừng ngần ngại liên hệ với chúng tôi:</p>
            <ul>
                <li><i class="fas fa-map-marker-alt"></i> Địa chỉ: 123 Đường ABC, Quận XYZ, TP.HCM</li>
                <li><i class="fas fa-phone"></i> Hotline: 1900 1234</li>
                <li><i class="fas fa-envelope"></i> Email: support@wearsy.com</li>
                <li><i class="fas fa-clock"></i> Giờ làm việc: 8:00 - 22:00 (Tất cả các ngày trong tuần)</li>
            </ul>
        </section>
    </div>
</div>

<style>
.page-content {
    padding: 40px 0;
    max-width: 900px;
    margin: 0 auto;
}

.page-title {
    font-size: 42px;
    text-align: center;
    margin-bottom: 40px;
    color: var(--primary-color);
}

.about-section {
    margin-bottom: 50px;
}

.about-section h2 {
    font-size: 28px;
    margin-bottom: 20px;
    color: var(--primary-color);
}

.about-section p {
    font-size: 16px;
    line-height: 1.8;
    color: var(--text-light);
    margin-bottom: 15px;
}

.about-section ul {
    list-style: none;
    padding-left: 0;
}

.about-section ul li {
    padding: 10px 0;
    padding-left: 30px;
    position: relative;
    font-size: 16px;
    line-height: 1.8;
    color: var(--text-light);
}

.about-section ul li::before {
    content: '\f00c';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    position: absolute;
    left: 0;
    color: var(--success-color);
}

.about-section ul li i {
    margin-right: 10px;
    color: var(--secondary-color);
}

.values-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 30px;
    margin-top: 30px;
}

.value-item {
    text-align: center;
    padding: 30px 20px;
    background-color: var(--bg-light);
    border-radius: 10px;
    transition: var(--transition);
}

.value-item:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-hover);
}

.value-item i {
    font-size: 48px;
    color: var(--secondary-color);
    margin-bottom: 20px;
}

.value-item h3 {
    font-size: 20px;
    margin-bottom: 15px;
    color: var(--primary-color);
}

.value-item p {
    font-size: 14px;
    line-height: 1.6;
}
</style>

<?php include 'includes/footer.php'; ?>

