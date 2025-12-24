# Wearsy - Trang Web Bán Hàng Thời Trang

Wearsy là một trang web bán hàng thời trang hoàn chỉnh được xây dựng bằng PHP và MySQL, chạy trên môi trường XAMPP localhost.

## Tính năng chính

### Người dùng (User)
- ✅ Đăng ký và đăng nhập tài khoản
- ✅ Xem danh sách sản phẩm với bộ lọc và tìm kiếm
- ✅ Xem chi tiết sản phẩm với hình ảnh, mô tả, đánh giá
- ✅ Thêm sản phẩm vào giỏ hàng
- ✅ Quản lý giỏ hàng (thêm, sửa, xóa)
- ✅ Danh sách yêu thích (Wishlist)
- ✅ Đặt hàng và thanh toán
- ✅ Xem lịch sử đơn hàng
- ✅ Đánh giá sản phẩm
- ✅ Quản lý thông tin cá nhân

### Quản trị viên (Admin)
- ✅ Dashboard quản trị
- ✅ Quản lý sản phẩm (thêm, sửa, xóa)
- ✅ Quản lý đơn hàng
- ✅ Xem chi tiết đơn hàng
- ✅ Cập nhật trạng thái đơn hàng

## Công nghệ sử dụng

- **Backend**: PHP 7.4+
- **Database**: MySQL (phpMyAdmin)
- **Frontend**: HTML5, CSS3, JavaScript
- **Icons**: Font Awesome 6.4.0
- **Server**: XAMPP (Apache + MySQL)

## Cài đặt

### Yêu cầu hệ thống
- XAMPP (PHP 7.4+ và MySQL)
- Trình duyệt web hiện đại (Chrome, Firefox, Edge...)

### Các bước cài đặt

1. **Copy dự án vào thư mục XAMPP**
   ```
   Copy thư mục Wearsy vào C:\xampp\htdocs\
   ```

2. **Tạo database**
   - Mở phpMyAdmin (http://localhost/phpmyadmin)
   - Import file `database/schema.sql`
   - Database sẽ được tạo tự động với tên `wearsy_db`

3. **Cấu hình database**
   - Mở file `config/database.php`
   - Kiểm tra các thông tin kết nối:
     ```php
     DB_HOST: localhost
     DB_USER: root
     DB_PASS: (để trống nếu không có mật khẩu)
     DB_NAME: wearsy_db
     ```

4. **Tạo thư mục uploads**
   - Đảm bảo thư mục `uploads/` tồn tại và có quyền ghi

5. **Truy cập website**
   - Mở trình duyệt và truy cập: `http://localhost/Wearsy`

## Tài khoản mặc định

### Admin
- **Username**: admin
- **Email**: admin@wearsy.com
- **Password**: admin123

### User
- Đăng ký tài khoản mới tại trang đăng ký

## Cấu trúc thư mục

```
Wearsy/
├── admin/              # Trang quản trị
│   ├── index.php      # Dashboard
│   ├── products.php   # Quản lý sản phẩm
│   ├── orders.php     # Quản lý đơn hàng
│   └── ...
├── assets/            # Tài nguyên tĩnh
│   ├── css/
│   │   └── style.css  # Stylesheet chính
│   ├── js/
│   │   └── main.js    # JavaScript chính
│   └── images/        # Hình ảnh
├── config/            # Cấu hình
│   ├── config.php     # Cấu hình chung
│   └── database.php   # Cấu hình database
├── database/          # Database
│   └── schema.sql     # File SQL tạo database
├── includes/          # Các file include
│   ├── header.php     # Header
│   └── footer.php     # Footer
├── uploads/           # Thư mục upload hình ảnh
├── index.php          # Trang chủ
├── products.php       # Trang sản phẩm
├── product-detail.php # Chi tiết sản phẩm
├── cart.php           # Giỏ hàng
├── checkout.php       # Thanh toán
├── login.php          # Đăng nhập
├── register.php       # Đăng ký
├── profile.php        # Thông tin cá nhân
├── orders.php         # Đơn hàng
├── wishlist.php       # Danh sách yêu thích
├── about.php          # Giới thiệu
├── contact.php        # Liên hệ
├── help.php           # Hướng dẫn
├── shipping.php       # Chính sách vận chuyển
├── return.php         # Chính sách đổi trả
└── faq.php            # Câu hỏi thường gặp
```

## Tính năng nổi bật

### 1. Hệ thống sản phẩm
- Hiển thị sản phẩm nổi bật
- Lọc theo danh mục
- Tìm kiếm sản phẩm
- Sắp xếp theo giá, tên
- Phân trang

### 2. Giỏ hàng
- Lưu trữ trong database
- Đồng bộ với tài khoản
- Cập nhật số lượng
- Xóa sản phẩm

### 3. Đặt hàng
- Nhiều phương thức thanh toán
- Quản lý địa chỉ giao hàng
- Theo dõi trạng thái đơn hàng
- Hủy đơn hàng

### 4. Đánh giá sản phẩm
- Đánh giá từ 1-5 sao
- Viết nhận xét
- Hiển thị đánh giá trung bình

### 5. Quản trị
- Dashboard tổng quan
- Quản lý sản phẩm đầy đủ
- Quản lý đơn hàng
- Cập nhật trạng thái

## Bảo mật

- Mật khẩu được mã hóa bằng `password_hash()`
- Sử dụng prepared statements để chống SQL injection
- Xử lý input với `htmlspecialchars()` để chống XSS
- Kiểm tra quyền truy cập cho từng trang

## Hỗ trợ

Nếu gặp vấn đề, vui lòng:
1. Kiểm tra lại cấu hình database
2. Đảm bảo XAMPP đang chạy
3. Kiểm tra quyền truy cập thư mục uploads
4. Xem log lỗi trong php.ini

## Giấy phép

Dự án này được phát triển cho mục đích học tập và thương mại.

## Tác giả

Wearsy Team

---

**Lưu ý**: Đây là phiên bản demo chạy trên localhost. Để triển khai lên server thực tế, bạn cần:
- Cấu hình lại database connection
- Cấu hình lại SITE_URL trong config.php
- Đảm bảo server hỗ trợ PHP 7.4+
- Cấu hình SSL/HTTPS cho bảo mật

