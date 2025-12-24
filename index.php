<?php
require_once __DIR__ . '/config/config.php';

$pageTitle = 'Trang chủ';
include 'includes/header.php';

$conn = getDBConnection();

// Get featured products
$featured_query = "SELECT * FROM products WHERE featured = 1 AND status = 'active' ORDER BY created_at DESC LIMIT 8";
$featured_result = $conn->query($featured_query);

// Get latest products
$latest_query = "SELECT * FROM products WHERE status = 'active' ORDER BY created_at DESC LIMIT 8";
$latest_result = $conn->query($latest_query);

// Get categories
$categories_query = "SELECT * FROM categories ORDER BY name";
$categories_result = $conn->query($categories_query);

closeDBConnection($conn);
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-slider">
        <!-- Slide 1 -->
        <div class="hero-slide active">
            <div class="hero-media hero-media-1"></div>
            <div class="hero-content">
                <span class="hero-label">Bộ sưu tập mới</span>
                <h2>Thời trang hiện đại</h2>
                <p>Khám phá những thiết kế mới nhất, phù hợp mọi phong cách hàng ngày.</p>
                <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-primary btn-large">Mua ngay</a>
            </div>
        </div>

        <!-- Slide 2 -->
        <div class="hero-slide">
            <div class="hero-media hero-media-2"></div>
            <div class="hero-content">
                <span class="hero-label">Ưu đãi đặc biệt</span>
                <h2>Giảm đến 50%</h2>
                <p>Sale lớn các mẫu áo thun, sơ mi, hoodie hot trend mùa này.</p>
                <a href="<?php echo SITE_URL; ?>/products.php?category=ao-nam" class="btn btn-primary btn-large">Xem áo nam</a>
            </div>
        </div>

        <!-- Slide 3 -->
        <div class="hero-slide">
            <div class="hero-media hero-media-3"></div>
            <div class="hero-content">
                <span class="hero-label">Trang phục nữ</span>
                <h2>Thanh lịch & Cá tính</h2>
                <p>Váy, đầm, sơ mi, quần jean được phối sẵn set cực đẹp cho bạn.</p>
                <a href="<?php echo SITE_URL; ?>/products.php?category=thoi-trang-nu" class="btn btn-primary btn-large">Xem thời trang nữ</a>
            </div>
        </div>

        <!-- Slider controls -->
        <button class="hero-arrow hero-arrow-prev" aria-label="Trước">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="hero-arrow hero-arrow-next" aria-label="Sau">
            <i class="fas fa-chevron-right"></i>
        </button>

        <div class="hero-indicators">
            <button class="hero-dot active" data-slide="0" aria-label="Slide 1"></button>
            <button class="hero-dot" data-slide="1" aria-label="Slide 2"></button>
            <button class="hero-dot" data-slide="2" aria-label="Slide 3"></button>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section">
    <div class="container">
        <h2 class="section-title">Danh mục sản phẩm</h2>
        <div class="categories-grid">
            <?php while ($category = $categories_result->fetch_assoc()): ?>
                <?php
                // Map slug danh mục -> ảnh tương ứng
                $slug = $category['slug'];
                $categoryImages = [
                    'ao'           => 'assets/images/categories/ao.svg',
                    'dong-ho'      => 'assets/images/categories/dong-ho.svg',
                    'giay-dep'     => 'assets/images/categories/giay-dep.svg',
                    'phu-kien'     => 'assets/images/categories/phu-kien.svg',
                    'quan'         => 'assets/images/categories/quan.svg',
                    'vay'          => 'assets/images/categories/vay.svg',
                ];

                $imagePath = $categoryImages[$slug] ?? null;
                ?>

                <div class="category-card">
                    <a href="<?php echo SITE_URL; ?>/products.php?category=<?php echo $slug; ?>">
                        <div class="category-icon">
                            <?php if ($imagePath && file_exists(__DIR__ . '/' . $imagePath)): ?>
                                <img src="<?php echo SITE_URL . '/' . $imagePath; ?>"
                                     alt="<?php echo htmlspecialchars($category['name']); ?>">
                            <?php else: ?>
                                <i class="fas fa-tshirt"></i>
                            <?php endif; ?>
                        </div>
                        <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="products-section">
    <div class="container">
        <h2 class="section-title">Sản phẩm nổi bật</h2>
        <div class="products-grid">
            <?php while ($product = $featured_result->fetch_assoc()): ?>
                <div class="product-card">
                    <div class="product-image">
                        <a href="<?php echo SITE_URL; ?>/product-detail.php?id=<?php echo $product['id']; ?>">
                            <?php if ($product['image']): ?>
                                <img src="<?php echo SITE_URL; ?>/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php else: ?>
                                <img src="<?php echo SITE_URL; ?>/
                                
                                
                                assets/images/placeholder.jpg" alt="No image">
                            <?php endif; ?>
                        </a>
                        <?php if ($product['compare_price']): ?>
                            <span class="product-badge">Sale</span>
                        <?php endif; ?>
                        <div class="product-actions">
                            <a href="<?php echo SITE_URL; ?>/product-detail.php?id=<?php echo $product['id']; ?>" class="action-icon" title="Xem chi tiết"><i class="fas fa-eye"></i></a>
                            <?php if (isLoggedIn()): ?>
                                <a href="<?php echo SITE_URL; ?>/add-to-wishlist.php?id=<?php echo $product['id']; ?>" class="action-icon" title="Yêu thích"><i class="far fa-heart"></i></a>
                            <?php endif; ?>
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
        <div class="section-footer">
            <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-outline">Xem tất cả sản phẩm</a>
        </div>
    </div>
</section>

<!-- Latest Products Section -->
<section class="products-section bg-light">
    <div class="container">
        <h2 class="section-title">Sản phẩm mới nhất</h2>
        <div class="products-grid">
            <?php while ($product = $latest_result->fetch_assoc()): ?>
                <div class="product-card">
                    <div class="product-image">
                        <a href="<?php echo SITE_URL; ?>/product-detail.php?id=<?php echo $product['id']; ?>">
                            <?php if ($product['image']): ?>
                                <img src="<?php echo SITE_URL; ?>/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php else: ?>
                                <img src="<?php echo SITE_URL; ?>/assets/images/placeholder.jpg" alt="No image">
                            <?php endif; ?>
                        </a>
                        <?php if ($product['compare_price']): ?>
                            <span class="product-badge">Sale</span>
                        <?php endif; ?>
                        <div class="product-actions">
                            <a href="<?php echo SITE_URL; ?>/product-detail.php?id=<?php echo $product['id']; ?>" class="action-icon" title="Xem chi tiết"><i class="fas fa-eye"></i></a>
                            <?php if (isLoggedIn()): ?>
                                <a href="<?php echo SITE_URL; ?>/add-to-wishlist.php?id=<?php echo $product['id']; ?>" class="action-icon" title="Yêu thích"><i class="far fa-heart"></i></a>
                            <?php endif; ?>
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
    </div>
</section>

<?php include 'includes/footer.php'; ?>

