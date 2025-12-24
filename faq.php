<?php
require_once __DIR__ . '/config/config.php';

$pageTitle = 'Câu hỏi thường gặp';
include 'includes/header.php';
?>

<div class="container">
    <div class="faq-page">
        <h1 class="page-title">Câu hỏi thường gặp (FAQ)</h1>
        
        <div class="faq-content">
            <section class="faq-section">
                <h2><i class="fas fa-shopping-bag"></i> Về sản phẩm</h2>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Sản phẩm của Wearsy có đảm bảo chất lượng không?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Có, tất cả sản phẩm của Wearsy đều được kiểm tra chất lượng kỹ lưỡng trước khi đưa đến tay khách hàng. Chúng tôi cam kết chỉ cung cấp những sản phẩm chất lượng cao, đảm bảo sự hài lòng của khách hàng.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Làm thế nào để biết size phù hợp với tôi?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Mỗi sản phẩm đều có bảng size chi tiết. Bạn có thể tham khảo bảng size trong trang chi tiết sản phẩm hoặc liên hệ hotline 1900 1234 để được tư vấn chính xác nhất.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Sản phẩm có được bảo hành không?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Có, tất cả sản phẩm của Wearsy đều có chế độ bảo hành. Thời gian bảo hành tùy thuộc vào loại sản phẩm và được ghi rõ trong phiếu bảo hành kèm theo.</p>
                    </div>
                </div>
            </section>
            
            <section class="faq-section">
                <h2><i class="fas fa-cart-plus"></i> Về đặt hàng</h2>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Tôi có thể đặt hàng mà không cần đăng ký tài khoản không?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Không, để đảm bảo an toàn và thuận tiện trong việc theo dõi đơn hàng, bạn cần đăng ký tài khoản trước khi đặt hàng. Việc đăng ký rất đơn giản và nhanh chóng.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Làm thế nào để theo dõi đơn hàng của tôi?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Sau khi đặt hàng thành công, bạn sẽ nhận được mã đơn hàng qua email. Bạn có thể đăng nhập vào tài khoản, vào mục "Đơn hàng của tôi" để xem chi tiết và trạng thái đơn hàng.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Tôi có thể hủy đơn hàng sau khi đã đặt không?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Có, bạn có thể hủy đơn hàng trong vòng 24 giờ sau khi đặt hàng, nếu đơn hàng chưa được xử lý. Vui lòng liên hệ hotline 1900 1234 để được hỗ trợ hủy đơn hàng.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Đơn hàng của tôi sẽ được giao trong bao lâu?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Thời gian giao hàng phụ thuộc vào địa điểm nhận hàng:
                        <ul>
                            <li>TP.HCM và Hà Nội: 1-2 ngày</li>
                            <li>Các tỉnh thành khác: 3-5 ngày</li>
                            <li>Vùng sâu, vùng xa: 5-7 ngày</li>
                        </ul>
                        </p>
                    </div>
                </div>
            </section>
            
            <section class="faq-section">
                <h2><i class="fas fa-credit-card"></i> Về thanh toán</h2>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Wearsy hỗ trợ những phương thức thanh toán nào?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Chúng tôi hỗ trợ các phương thức thanh toán sau:
                        <ul>
                            <li>Thanh toán khi nhận hàng (COD)</li>
                            <li>Chuyển khoản ngân hàng</li>
                            <li>Ví MoMo</li>
                            <li>Ví ZaloPay</li>
                        </ul>
                        </p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Khi nào tôi phải thanh toán?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Nếu bạn chọn thanh toán COD, bạn sẽ thanh toán khi nhận hàng. Nếu chọn các phương thức khác, bạn cần thanh toán trước khi đơn hàng được xử lý.</p>
                    </div>
                </div>
            </section>
            
            <section class="faq-section">
                <h2><i class="fas fa-exchange-alt"></i> Về đổi trả</h2>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Tôi có thể đổi/trả sản phẩm không?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Có, bạn có thể đổi/trả sản phẩm trong vòng 7 ngày kể từ ngày nhận hàng, với điều kiện sản phẩm còn nguyên vẹn, chưa qua sử dụng. Xem thêm chi tiết tại <a href="return.php">Chính sách đổi trả</a>.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Tôi có phải trả phí đổi trả không?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Nếu sản phẩm bị lỗi hoặc không đúng mô tả, chúng tôi sẽ miễn phí đổi trả. Nếu đổi trả do lý do cá nhân, bạn sẽ chịu phí vận chuyển 30.000 đ.</p>
                    </div>
                </div>
            </section>
            
            <section class="faq-section">
                <h2><i class="fas fa-headset"></i> Hỗ trợ khách hàng</h2>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Làm thế nào để liên hệ với bộ phận hỗ trợ?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Bạn có thể liên hệ với chúng tôi qua:
                        <ul>
                            <li>Hotline: 1900 1234</li>
                            <li>Email: support@wearsy.com</li>
                            <li>Thời gian hỗ trợ: 8:00 - 22:00 (Tất cả các ngày)</li>
                        </ul>
                        Hoặc điền form liên hệ tại trang <a href="contact.php">Liên hệ</a>.
                        </p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Tôi có thể xem sản phẩm trước khi mua không?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Hiện tại chúng tôi chỉ bán hàng online. Tuy nhiên, bạn có thể xem hình ảnh chi tiết và đọc đánh giá từ khách hàng đã mua. Nếu không hài lòng, bạn có thể đổi trả trong vòng 7 ngày.</p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<style>
.faq-page {
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

.faq-section {
    margin-bottom: 40px;
}

.faq-section h2 {
    font-size: 28px;
    margin-bottom: 25px;
    color: var(--primary-color);
    display: flex;
    align-items: center;
    gap: 10px;
}

.faq-section h2 i {
    color: var(--secondary-color);
}

.faq-item {
    background-color: var(--bg-light);
    border-radius: 10px;
    margin-bottom: 15px;
    overflow: hidden;
    transition: var(--transition);
}

.faq-item.active .faq-question {
    background-color: var(--primary-color);
    color: white;
}

.faq-item.active .faq-question i {
    transform: rotate(180deg);
}

.faq-question {
    padding: 20px 25px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: var(--transition);
}

.faq-question:hover {
    background-color: rgba(44, 62, 80, 0.1);
}

.faq-item.active .faq-question:hover {
    background-color: var(--primary-color);
}

.faq-question h3 {
    font-size: 18px;
    margin: 0;
    flex: 1;
}

.faq-question i {
    margin-left: 15px;
    transition: var(--transition);
    color: var(--secondary-color);
}

.faq-item.active .faq-question i {
    color: white;
}

.faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.faq-item.active .faq-answer {
    max-height: 1000px;
}

.faq-answer {
    padding: 0 25px;
}

.faq-item.active .faq-answer {
    padding: 20px 25px;
}

.faq-answer p {
    font-size: 16px;
    line-height: 1.8;
    color: var(--text-light);
    margin-bottom: 10px;
}

.faq-answer ul {
    padding-left: 20px;
    margin-top: 10px;
}

.faq-answer li {
    margin-bottom: 8px;
    color: var(--text-light);
}

.faq-answer a {
    color: var(--accent-color);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const faqQuestions = document.querySelectorAll('.faq-question');
    
    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            const faqItem = this.parentElement;
            const isActive = faqItem.classList.contains('active');
            
            // Close all FAQ items
            document.querySelectorAll('.faq-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Toggle current item
            if (!isActive) {
                faqItem.classList.add('active');
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>

