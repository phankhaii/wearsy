<?php
require_once __DIR__ . '/../config/config.php';
requireAdmin();

$conn = getDBConnection();

// Delete product
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM products WHERE id = $id");
    header('Location: ' . SITE_URL . '/admin/products.php');
    exit();
}

// Get all products
$query = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC";
$result = $conn->query($query);

$pageTitle = 'Quản lý sản phẩm';
include '../includes/header.php';
?>

<div class="admin-container">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="admin-main">
        <div class="admin-header">
            <h1><i class="fas fa-box"></i> Quản lý sản phẩm</h1>
            <a href="<?php echo SITE_URL; ?>/admin/product-add.php" class="btn btn-primary">Thêm sản phẩm mới</a>
        </div>
    
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Hình ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Giá</th>
                    <th>Tồn kho</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td>
                            <?php if ($product['image']): ?>
                                <img src="<?php echo SITE_URL; ?>/<?php echo $product['image']; ?>" alt="" class="thumb-image">
                            <?php else: ?>
                                <span class="no-image">No image</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
                        <td><?php echo formatPrice($product['price']); ?></td>
                        <td><?php echo $product['stock']; ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $product['status']; ?>">
                                <?php 
                                $statuses = [
                                    'active' => 'Hoạt động',
                                    'inactive' => 'Ngừng bán',
                                    'out_of_stock' => 'Hết hàng'
                                ];
                                echo $statuses[$product['status']] ?? $product['status'];
                                ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo SITE_URL; ?>/admin/product-edit.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">Sửa</a>
                            <a href="<?php echo SITE_URL; ?>/admin/products.php?delete=<?php echo $product['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">Xóa</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php closeDBConnection($conn); ?>
<?php include '../includes/footer.php'; ?>

