<?php
require_once __DIR__ . '/config/config.php';

$pageTitle = 'Chính sách vận chuyển';
include 'includes/header.php';
?>

<div class="container">
    <div class="policy-page">
        <h1 class="page-title">Chính sách vận chuyển</h1>
        
        <div class="policy-content">
            <section class="policy-section">
                <h2><i class="fas fa-truck"></i> Phạm vi giao hàng</h2>
                <p>Wearsy hiện tại giao hàng toàn quốc, bao gồm tất cả 63 tỉnh thành trên cả nước.</p>
            </section>
            
            <section class="policy-section">
                <h2><i class="fas fa-money-bill-wave"></i> Phí vận chuyển</h2>
                <div class="shipping-fees">
                    <div class="fee-item">
                        <h3>Miễn phí vận chuyển</h3>
                        <p>Áp dụng cho tất cả đơn hàng có giá trị từ 500.000 đ trở lên</p>
                    </div>
                    <div class="fee-item">
                        <h3>Phí vận chuyển tiêu chuẩn</h3>
                        <p>Đơn hàng dưới 500.000 đ: 30.000 đ (khu vực nội thành) hoặc 50.000 đ (khu vực ngoại thành)</p>
                    </div>
                </div>
            </section>
            
            <section class="policy-section">
                <h2><i class="fas fa-clock"></i> Thời gian giao hàng</h2>
                <ul>
                    <li><strong>Khu vực TP.HCM và Hà Nội:</strong> 1-2 ngày làm việc</li>
                    <li><strong>Khu vực các tỉnh thành khác:</strong> 3-5 ngày làm việc</li>
                    <li><strong>Khu vực vùng sâu, vùng xa:</strong> 5-7 ngày làm việc</li>
                </ul>
                <p><em>Lưu ý: Thời gian giao hàng được tính từ khi đơn hàng được xác nhận và thanh toán thành công.</em></p>
            </section>
            
            <section class="policy-section">
                <h2><i class="fas fa-check-circle"></i> Quy trình giao hàng</h2>
                <ol>
                    <li>Đơn hàng được xác nhận và xử lý trong vòng 24 giờ</li>
                    <li>Nhân viên giao hàng sẽ liên hệ với khách hàng trước khi giao hàng</li>
                    <li>Khách hàng kiểm tra hàng hóa trước khi thanh toán</li>
                    <li>Ký xác nhận nhận hàng và thanh toán (nếu chọn COD)</li>
                </ol>
            </section>
            
            <section class="policy-section">
                <h2><i class="fas fa-box"></i> Đóng gói sản phẩm</h2>
                <p>Tất cả sản phẩm của Wearsy đều được đóng gói cẩn thận:</p>
                <ul>
                    <li>Sử dụng túi/hộp đóng gói chuyên dụng</li>
                    <li>Bảo vệ sản phẩm khỏi va đập, ẩm ướt</li>
                    <li>Có tem niêm phong của Wearsy</li>
                    <li>Kèm theo hóa đơn và phiếu bảo hành (nếu có)</li>
                </ul>
            </section>
            
            <section class="policy-section">
                <h2><i class="fas fa-exclamation-triangle"></i> Lưu ý quan trọng</h2>
                <ul>
                    <li>Vui lòng kiểm tra kỹ sản phẩm trước khi ký nhận</li>
                    <li>Nếu phát hiện hàng hóa bị hư hỏng, vui lòng từ chối nhận hàng và liên hệ ngay với chúng tôi</li>
                    <li>Nếu không có người nhận, đơn hàng sẽ được giao lại vào lần sau</li>
                    <li>Sau 3 lần giao hàng không thành công, đơn hàng sẽ bị hủy</li>
                </ul>
            </section>
            
            <section class="policy-section">
                <h2><i class="fas fa-headset"></i> Hỗ trợ</h2>
                <p>Nếu bạn có bất kỳ thắc mắc nào về việc vận chuyển, vui lòng liên hệ:</p>
                <ul>
                    <li>Hotline: 1900 1234</li>
                    <li>Email: support@wearsy.com</li>
                    <li>Thời gian hỗ trợ: 8:00 - 22:00 (Tất cả các ngày)</li>
                </ul>
            </section>
        </div>
    </div>
</div>

<style>
.policy-page {
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

.policy-section {
    margin-bottom: 40px;
    padding: 30px;
    background-color: var(--bg-light);
    border-radius: 10px;
}

.policy-section h2 {
    font-size: 24px;
    margin-bottom: 20px;
    color: var(--primary-color);
    display: flex;
    align-items: center;
    gap: 10px;
}

.policy-section h2 i {
    color: var(--secondary-color);
}

.policy-section p {
    font-size: 16px;
    line-height: 1.8;
    color: var(--text-light);
    margin-bottom: 15px;
}

.policy-section ul,
.policy-section ol {
    padding-left: 20px;
    margin-bottom: 15px;
}

.policy-section li {
    margin-bottom: 10px;
    line-height: 1.8;
    color: var(--text-light);
}

.policy-section li strong {
    color: var(--text-dark);
}

.policy-section em {
    color: var(--text-light);
    font-size: 14px;
}

.shipping-fees {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.fee-item {
    background-color: var(--bg-white);
    padding: 20px;
    border-radius: 10px;
    border: 2px solid var(--border-color);
}

.fee-item h3 {
    font-size: 18px;
    margin-bottom: 10px;
    color: var(--secondary-color);
}

.fee-item p {
    font-size: 14px;
    margin-bottom: 0;
}
</style>

<?php include 'includes/footer.php'; ?>

