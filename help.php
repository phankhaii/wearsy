<?php
require_once __DIR__ . '/config/config.php';

$pageTitle = 'Hướng dẫn mua hàng';
include 'includes/header.php';
?>

<div class="container">
    <div class="help-page">
        <h1 class="page-title">Hướng dẫn mua hàng</h1>
        
        <div class="help-content">
            <section class="help-section">
                <h2><i class="fas fa-shopping-cart"></i> Các bước đặt hàng</h2>
                <div class="steps">
                    <div class="step">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Tìm kiếm sản phẩm</h3>
                            <p>Sử dụng thanh tìm kiếm hoặc duyệt qua các danh mục sản phẩm để tìm sản phẩm bạn muốn mua.</p>
                        </div>
                    </div>
                    
                    <div class="step">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Xem chi tiết sản phẩm</h3>
                            <p>Click vào sản phẩm để xem thông tin chi tiết, hình ảnh, giá cả và các đánh giá từ khách hàng.</p>
                        </div>
                    </div>
                    
                    <div class="step">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Thêm vào giỏ hàng</h3>
                            <p>Chọn size, màu sắc và số lượng, sau đó click "Thêm vào giỏ hàng".</p>
                        </div>
                    </div>
                    
                    <div class="step">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h3>Kiểm tra giỏ hàng</h3>
                            <p>Vào giỏ hàng để kiểm tra lại các sản phẩm đã chọn, điều chỉnh số lượng nếu cần.</p>
                        </div>
                    </div>
                    
                    <div class="step">
                        <div class="step-number">5</div>
                        <div class="step-content">
                            <h3>Thanh toán</h3>
                            <p>Click "Thanh toán", điền đầy đủ thông tin giao hàng và chọn phương thức thanh toán.</p>
                        </div>
                    </div>
                    
                    <div class="step">
                        <div class="step-number">6</div>
                        <div class="step-content">
                            <h3>Hoàn tất đặt hàng</h3>
                            <p>Xác nhận thông tin và click "Đặt hàng". Bạn sẽ nhận được email xác nhận đơn hàng.</p>
                        </div>
                    </div>
                </div>
            </section>
            
            <section class="help-section">
                <h2><i class="fas fa-user-plus"></i> Đăng ký tài khoản</h2>
                <p>Để mua hàng tại Wearsy, bạn cần đăng ký tài khoản:</p>
                <ol>
                    <li>Click vào nút "Đăng ký" ở góc trên cùng bên phải</li>
                    <li>Điền đầy đủ thông tin: Tên đăng nhập, Email, Mật khẩu, Họ tên</li>
                    <li>Click "Đăng ký" để hoàn tất</li>
                    <li>Bạn có thể đăng nhập ngay sau khi đăng ký thành công</li>
                </ol>
            </section>
            
            <section class="help-section">
                <h2><i class="fas fa-credit-card"></i> Phương thức thanh toán</h2>
                <p>Wearsy hỗ trợ các phương thức thanh toán sau:</p>
                <ul>
                    <li><strong>Thanh toán khi nhận hàng (COD):</strong> Bạn sẽ thanh toán khi nhận được hàng</li>
                    <li><strong>Chuyển khoản ngân hàng:</strong> Chuyển khoản trực tiếp vào tài khoản ngân hàng của chúng tôi</li>
                    <li><strong>Ví MoMo:</strong> Thanh toán qua ứng dụng MoMo</li>
                    <li><strong>Ví ZaloPay:</strong> Thanh toán qua ứng dụng ZaloPay</li>
                </ul>
            </section>
            
            <section class="help-section">
                <h2><i class="fas fa-question-circle"></i> Câu hỏi thường gặp</h2>
                <div class="faq-list">
                    <div class="faq-item">
                        <h3>Làm thế nào để theo dõi đơn hàng?</h3>
                        <p>Sau khi đặt hàng thành công, bạn sẽ nhận được mã đơn hàng. Đăng nhập vào tài khoản và vào mục "Đơn hàng của tôi" để xem chi tiết và trạng thái đơn hàng.</p>
                    </div>
                    
                    <div class="faq-item">
                        <h3>Tôi có thể hủy đơn hàng không?</h3>
                        <p>Có, bạn có thể hủy đơn hàng trong vòng 24 giờ sau khi đặt hàng, nếu đơn hàng chưa được xử lý.</p>
                    </div>
                    
                    <div class="faq-item">
                        <h3>Sản phẩm có được đổi trả không?</h3>
                        <p>Có, bạn có thể đổi trả sản phẩm trong vòng 7 ngày kể từ ngày nhận hàng nếu sản phẩm còn nguyên vẹn, chưa sử dụng. Xem thêm tại <a href="return.php">Chính sách đổi trả</a>.</p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<style>
.help-page {
    padding: 40px 0;
    max-width: 900px;
    margin: 0 auto;
}

.page-title {
    text-align: center;
    font-size: 42px;
    margin-bottom: 40px;
    color: var(--primary-color);
}

.help-section {
    margin-bottom: 50px;
}

.help-section h2 {
    font-size: 28px;
    margin-bottom: 25px;
    color: var(--primary-color);
    display: flex;
    align-items: center;
    gap: 10px;
}

.help-section h2 i {
    color: var(--secondary-color);
}

.help-section p {
    font-size: 16px;
    line-height: 1.8;
    color: var(--text-light);
    margin-bottom: 15px;
}

.steps {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.step {
    display: flex;
    gap: 20px;
    align-items: flex-start;
}

.step-number {
    width: 50px;
    height: 50px;
    background-color: var(--secondary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: bold;
    flex-shrink: 0;
}

.step-content h3 {
    font-size: 20px;
    margin-bottom: 8px;
    color: var(--text-dark);
}

.step-content p {
    color: var(--text-light);
}

.help-section ol,
.help-section ul {
    padding-left: 20px;
    margin-bottom: 15px;
}

.help-section li {
    margin-bottom: 10px;
    line-height: 1.8;
    color: var(--text-light);
}

.help-section li strong {
    color: var(--text-dark);
}

.faq-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.faq-item {
    background-color: var(--bg-light);
    padding: 25px;
    border-radius: 10px;
}

.faq-item h3 {
    font-size: 18px;
    margin-bottom: 10px;
    color: var(--primary-color);
}

.faq-item p {
    color: var(--text-light);
    line-height: 1.8;
}

.faq-item a {
    color: var(--accent-color);
}
</style>

<?php include 'includes/footer.php'; ?>

