<?php
require_once __DIR__ . '/config/config.php';
requireLogin();

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

// Add to wishlist
if (isset($_GET['add'])) {
    $product_id = (int)$_GET['add'];
    $check = $conn->query("SELECT id FROM wishlist WHERE user_id = $user_id AND product_id = $product_id");
    if ($check->num_rows === 0) {
        $conn->query("INSERT INTO wishlist (user_id, product_id) VALUES ($user_id, $product_id)");
    }
    header('Location: ' . SITE_URL . '/wishlist.php');
    exit();
}

// Remove from wishlist
if (isset($_GET['remove'])) {
    $product_id = (int)$_GET['remove'];
    $conn->query("DELETE FROM wishlist WHERE user_id = $user_id AND product_id = $product_id");
    header('Location: ' . SITE_URL . '/wishlist.php');
    exit();
}

// Get wishlist items
$wishlist_query = "SELECT w.*, p.* FROM wishlist w 
                   LEFT JOIN products p ON w.product_id = p.id 
                   WHERE w.user_id = $user_id AND p.status = 'active'
                   ORDER BY w.created_at DESC";
$wishlist_result = $conn->query($wishlist_query);

$pageTitle = 'Yêu thích';
include 'includes/header.php';
?>

<div class="container">
    <h1>Danh sách yêu thích</h1>
    
    <?php if ($wishlist_result->num_rows > 0): ?>
        <div class="products-grid">
            <?php while ($product = $wishlist_result->fetch_assoc()): ?>
                <div class="product-card">
                    <div class="product-image">
                        <a href="<?php echo SITE_URL; ?>/product-detail.php?id=<?php echo $product['id']; ?>">
                            <?php if ($product['image']): ?>
                                <img src="<?php echo SITE_URL; ?>/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php else: ?>
                                <img src="<?php echo SITE_URL; ?>/assets/images/placeholder.jpg" alt="No image">
                            <?php endif; ?>
                        </a>
                        <div class="product-actions">
                            <a href="<?php echo SITE_URL; ?>/product-detail.php?id=<?php echo $product['id']; ?>" class="action-icon"><i class="fas fa-eye"></i></a>
                            <a href="<?php echo SITE_URL; ?>/wishlist.php?remove=<?php echo $product['id']; ?>" class="action-icon"><i class="fas fa-heart" style="color: red;"></i></a>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name">
                            <a href="<?php echo SITE_URL; ?>/product-detail.php?id=<?php echo $product['id']; ?>">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </a>
                        </h3>
                        <div class="product-price">
                            <?php if ($product['compare_price']): ?>
                                <span class="price-old"><?php echo formatPrice($product['compare_price']); ?></span>
                            <?php endif; ?>
                            <span class="price-current"><?php echo formatPrice($product['price']); ?></span>
                        </div>
                        <a href="<?php echo SITE_URL; ?>/add-to-cart.php?id=<?php echo $product['id']; ?>" class="btn btn-secondary btn-block">Thêm vào giỏ</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-wishlist">
            <i class="far fa-heart"></i>
            <h2>Danh sách yêu thích của bạn đang trống</h2>
            <p>Hãy thêm sản phẩm vào danh sách yêu thích để dễ dàng mua sau!</p>
            <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-primary">Mua sắm ngay</a>
        </div>
    <?php endif; ?>
</div>

<?php closeDBConnection($conn); ?>
<?php include 'includes/footer.php'; ?>

