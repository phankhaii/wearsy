<?php
require_once __DIR__ . '/config/config.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$product_id) {
    header('Location: ' . SITE_URL . '/products.php');
    exit();
}

$conn = getDBConnection();

// Get product details
$stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ? AND p.status = 'active'");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: ' . SITE_URL . '/products.php');
    exit();
}

$product = $result->fetch_assoc();
$stmt->close();

// Update views
$conn->query("UPDATE products SET views = views + 1 WHERE id = $product_id");

// Check if in wishlist
$in_wishlist = false;
if (isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    $wishlist_check = $conn->query("SELECT id FROM wishlist WHERE user_id = $user_id AND product_id = $product_id");
    $in_wishlist = $wishlist_check->num_rows > 0;
}

// Get related products
$related_query = "SELECT * FROM products WHERE category_id = {$product['category_id']} AND id != $product_id AND status = 'active' LIMIT 4";
$related_result = $conn->query($related_query);

// Get reviews
$reviews_query = "SELECT r.*, u.full_name, u.username FROM reviews r LEFT JOIN users u ON r.user_id = u.id WHERE r.product_id = $product_id ORDER BY r.created_at DESC";
$reviews_result = $conn->query($reviews_query);

// Calculate average rating
$rating_query = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM reviews WHERE product_id = $product_id";
$rating_result = $conn->query($rating_query);
$rating_data = $rating_result->fetch_assoc();
$avg_rating = round($rating_data['avg_rating'] ?? 0, 1);
$total_reviews = $rating_data['total_reviews'] ?? 0;

$pageTitle = $product['name'];
include 'includes/header.php';
?>

<div class="container">
    <div class="product-detail">
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>/index.php">Trang chủ</a> / 
            <a href="<?php echo SITE_URL; ?>/products.php">Sản phẩm</a> / 
            <?php if ($product['category_name']): ?>
                <a href="<?php echo SITE_URL; ?>/products.php?category=<?php echo urlencode($product['category_name']); ?>"><?php echo htmlspecialchars($product['category_name']); ?></a> / 
            <?php endif; ?>
            <span><?php echo htmlspecialchars($product['name']); ?></span>
        </div>
        
        <div class="product-detail-content">
            <div class="product-images">
                <div class="main-image">
                    <?php if ($product['image']): ?>
                        <img src="<?php echo SITE_URL; ?>/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php else: ?>
                        <img src="<?php echo SITE_URL; ?>/assets/images/placeholder.jpg" alt="No image">
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="product-info-detail">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                
                <?php if ($total_reviews > 0): ?>
                    <div class="product-rating">
                        <div class="stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?php echo $i <= $avg_rating ? 'active' : ''; ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <span>(<?php echo $avg_rating; ?>/5 - <?php echo $total_reviews; ?> đánh giá)</span>
                    </div>
                <?php endif; ?>
                
                <div class="product-price-detail">
                    <?php if ($product['compare_price']): ?>
                        <span class="price-old"><?php echo formatPrice($product['compare_price']); ?></span>
                        <span class="discount">-<?php echo round((($product['compare_price'] - $product['price']) / $product['compare_price']) * 100); ?>%</span>
                    <?php endif; ?>
                    <span class="price-current"><?php echo formatPrice($product['price']); ?></span>
                </div>
                
                <div class="product-description">
                    <h3>Mô tả sản phẩm</h3>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>
                
                <div class="product-specs">
                    <?php if ($product['brand']): ?>
                        <p><strong>Thương hiệu:</strong> <?php echo htmlspecialchars($product['brand']); ?></p>
                    <?php endif; ?>
                    <?php if ($product['material']): ?>
                        <p><strong>Chất liệu:</strong> <?php echo htmlspecialchars($product['material']); ?></p>
                    <?php endif; ?>
                    <?php if ($product['size']): ?>
                        <p><strong>Size:</strong> <?php echo htmlspecialchars($product['size']); ?></p>
                    <?php endif; ?>
                    <?php if ($product['color']): ?>
                        <p><strong>Màu sắc:</strong> <?php echo htmlspecialchars($product['color']); ?></p>
                    <?php endif; ?>
                    <p><strong>Tình trạng:</strong> 
                        <?php if ($product['stock'] > 0): ?>
                            <span class="in-stock">Còn hàng (<?php echo $product['stock']; ?> sản phẩm)</span>
                        <?php else: ?>
                            <span class="out-of-stock">Hết hàng</span>
                        <?php endif; ?>
                    </p>
                </div>
                
                <div class="product-actions-detail">
                    <form action="<?php echo SITE_URL; ?>/add-to-cart.php" method="POST" class="add-to-cart-form">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <div class="quantity-selector">
                            <label>Số lượng:</label>
                            <button type="button" class="qty-btn minus" onclick="changeQuantity(-1)">-</button>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                            <button type="button" class="qty-btn plus" onclick="changeQuantity(1)">+</button>
                        </div>
                        
                        <?php if (isLoggedIn()): ?>
                            <?php if ($product['stock'] > 0): ?>
                                <button type="submit" class="btn btn-primary btn-large">Thêm vào giỏ hàng</button>
                            <?php else: ?>
                                <button type="button" class="btn btn-disabled" disabled>Hết hàng</button>
                            <?php endif; ?>
                            <a href="<?php echo SITE_URL; ?>/add-to-wishlist.php?id=<?php echo $product['id']; ?>" class="btn btn-outline btn-large">
                                <i class="far fa-heart"></i> <?php echo $in_wishlist ? 'Đã yêu thích' : 'Yêu thích'; ?>
                            </a>
                        <?php else: ?>
                            <a href="<?php echo SITE_URL; ?>/login.php" class="btn btn-primary btn-large">Đăng nhập để mua hàng</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Reviews Section -->
        <div class="product-reviews">
            <h2>Đánh giá sản phẩm (<?php echo $total_reviews; ?>)</h2>
            
            <?php if (isLoggedIn()): ?>
                <div class="review-form">
                    <h3>Viết đánh giá</h3>
                    <form action="<?php echo SITE_URL; ?>/add-review.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <div class="form-group">
                            <label>Đánh giá:</label>
                            <div class="rating-input">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" name="rating" value="<?php echo $i; ?>" id="rating<?php echo $i; ?>" required>
                                    <label for="rating<?php echo $i; ?>" class="star-label"><i class="fas fa-star"></i></label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="comment">Nhận xét:</label>
                            <textarea id="comment" name="comment" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                    </form>
                </div>
            <?php endif; ?>
            
            <div class="reviews-list">
                <?php if ($reviews_result->num_rows > 0): ?>
                    <?php while ($review = $reviews_result->fetch_assoc()): ?>
                        <div class="review-item">
                            <div class="review-header">
                                <strong><?php echo htmlspecialchars($review['full_name'] ?: $review['username']); ?></strong>
                                <div class="review-rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'active' : ''; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <span class="review-date"><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></span>
                            </div>
                            <p><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá sản phẩm này!</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Related Products -->
        <?php if ($related_result->num_rows > 0): ?>
            <div class="related-products">
                <h2>Sản phẩm liên quan</h2>
                <div class="products-grid">
                    <?php while ($related = $related_result->fetch_assoc()): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <a href="<?php echo SITE_URL; ?>/product-detail.php?id=<?php echo $related['id']; ?>">
                                    <?php if ($related['image']): ?>
                                        <img src="<?php echo SITE_URL; ?>/<?php echo $related['image']; ?>" alt="<?php echo htmlspecialchars($related['name']); ?>">
                                    <?php else: ?>
                                        <img src="<?php echo SITE_URL; ?>/assets/images/placeholder.jpg" alt="No image">
                                    <?php endif; ?>
                                </a>
                            </div>
                            <div class="product-info">
                                <h3><a href="<?php echo SITE_URL; ?>/product-detail.php?id=<?php echo $related['id']; ?>"><?php echo htmlspecialchars($related['name']); ?></a></h3>
                                <div class="product-price">
                                    <span class="price-current"><?php echo formatPrice($related['price']); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function changeQuantity(change) {
    const input = document.getElementById('quantity');
    const currentValue = parseInt(input.value);
    const maxValue = parseInt(input.max);
    const newValue = currentValue + change;
    
    if (newValue >= 1 && newValue <= maxValue) {
        input.value = newValue;
    }
}
</script>

<?php closeDBConnection($conn); ?>
<?php include 'includes/footer.php'; ?>

