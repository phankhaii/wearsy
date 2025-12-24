<?php
require_once __DIR__ . '/../config/config.php';
requireAdmin();

$conn = getDBConnection();
$error = '';
$success = '';

// Get categories
$categories = $conn->query("SELECT * FROM categories ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $compare_price = !empty($_POST['compare_price']) ? (float)$_POST['compare_price'] : null;
    $category_id = (int)($_POST['category_id'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $size = sanitizeInput($_POST['size'] ?? '');
    $color = sanitizeInput($_POST['color'] ?? '');
    $brand = sanitizeInput($_POST['brand'] ?? '');
    $material = sanitizeInput($_POST['material'] ?? '');
    $status = sanitizeInput($_POST['status'] ?? 'active');
    $featured = isset($_POST['featured']) ? 1 : 0;
    $image_path = null;
    
    if (empty($name) || $price <= 0) {
        $error = 'Vui lòng nhập đầy đủ thông tin bắt buộc!';
    } else {
        $slug = generateSlug($name);
        // Make slug unique
        $slug_check = $conn->query("SELECT id FROM products WHERE slug = '$slug'");
        if ($slug_check->num_rows > 0) {
            $slug = $slug . '-' . time();
        }

        // Handle image upload (optional)
        if (!empty($_FILES['image']['name'])) {
            $uploadDir = __DIR__ . '/../uploads/products';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($ext, $allowed)) {
                $error = 'Định dạng ảnh không hợp lệ. Vui lòng chọn JPG, JPEG, PNG hoặc WEBP.';
            } else {
                $fileName = $slug . '-' . time() . '.' . $ext;
                $targetPath = $uploadDir . '/' . $fileName;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    // Lưu đường dẫn tương đối để dùng trên website
                    $image_path = 'uploads/products/' . $fileName;
                } else {
                    $error = 'Không thể tải lên ảnh sản phẩm. Vui lòng thử lại.';
                }
            }
        }
    }

    if (empty($error)) {
        $stmt = $conn->prepare("INSERT INTO products (name, slug, description, price, compare_price, category_id, image, stock, size, color, brand, material, status, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssddisisssssi", $name, $slug, $description, $price, $compare_price, $category_id, $image_path, $stock, $size, $color, $brand, $material, $status, $featured);
        
        if ($stmt->execute()) {
            $success = 'Thêm sản phẩm thành công!';
            header('refresh:2;url=' . SITE_URL . '/admin/products.php');
        } else {
            $error = 'Có lỗi xảy ra khi thêm sản phẩm!';
        }
        
        $stmt->close();
    }
}

$pageTitle = 'Thêm sản phẩm';
include '../includes/header.php';
?>

<div class="container">
    <h1>Thêm sản phẩm mới</h1>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="" class="admin-form" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Tên sản phẩm: <span class="required">*</span></label>
            <input type="text" id="name" name="name" required>
        </div>
        
        <div class="form-group">
            <label for="description">Mô tả:</label>
            <textarea id="description" name="description" rows="5"></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="price">Giá bán: <span class="required">*</span></label>
                <input type="number" id="price" name="price" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="compare_price">Giá so sánh:</label>
                <input type="number" id="compare_price" name="compare_price" step="0.01" min="0">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="category_id">Danh mục:</label>
                <select id="category_id" name="category_id">
                    <option value="0">Chọn danh mục</option>
                    <?php while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="stock">Tồn kho:</label>
                <input type="number" id="stock" name="stock" min="0" value="0">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="size">Size:</label>
                <input type="text" id="size" name="size" placeholder="S,M,L,XL">
            </div>
            
            <div class="form-group">
                <label for="color">Màu sắc:</label>
                <input type="text" id="color" name="color" placeholder="Đen,Trắng,Xanh">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="brand">Thương hiệu:</label>
                <input type="text" id="brand" name="brand">
            </div>
            
            <div class="form-group">
                <label for="material">Chất liệu:</label>
                <input type="text" id="material" name="material">
            </div>
        </div>

        <div class="form-group">
            <label for="image">Ảnh sản phẩm (JPG, PNG, WEBP):</label>
            <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">
            <small>Ảnh sẽ hiển thị ở danh sách sản phẩm và trang chi tiết.</small>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="status">Trạng thái:</label>
                <select id="status" name="status">
                    <option value="active">Hoạt động</option>
                    <option value="inactive">Ngừng bán</option>
                    <option value="out_of_stock">Hết hàng</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="featured" value="1"> Sản phẩm nổi bật
                </label>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Thêm sản phẩm</button>
            <a href="<?php echo SITE_URL; ?>/admin/products.php" class="btn btn-outline">Hủy</a>
        </div>
    </form>
</div>

<?php closeDBConnection($conn); ?>
<?php include '../includes/footer.php'; ?>

