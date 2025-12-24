<?php
require_once __DIR__ . '/config/config.php';

$pageTitle = 'Sản phẩm';
include 'includes/header.php';

$conn = getDBConnection();

// Get filter parameters
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$category_slug = isset($_GET['category']) ? sanitizeInput($_GET['category']) : '';
$sort = isset($_GET['sort']) ? sanitizeInput($_GET['sort']) : 'newest';
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 0;

// Build query
$where = "status = 'active'";
$join = "";

if ($category_slug) {
    $join = "LEFT JOIN categories ON products.category_id = categories.id";
    $where .= " AND categories.slug = '$category_slug'";
}

if ($search) {
    $where .= " AND (products.name LIKE '%$search%' OR products.description LIKE '%$search%')";
}

if ($min_price > 0) {
    $where .= " AND products.price >= $min_price";
}

if ($max_price > 0) {
    $where .= " AND products.price <= $max_price";
}

// Sort
$order_by = "ORDER BY products.created_at DESC";
switch ($sort) {
    case 'price_asc':
        $order_by = "ORDER BY products.price ASC";
        break;
    case 'price_desc':
        $order_by = "ORDER BY products.price DESC";
        break;
    case 'name':
        $order_by = "ORDER BY products.name ASC";
        break;
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Get total count
$count_query = "SELECT COUNT(*) as total FROM products $join WHERE $where";
$count_result = $conn->query($count_query);
$total_products = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_products / $per_page);

// Get products
$query = "SELECT products.* FROM products $join WHERE $where $order_by LIMIT $per_page OFFSET $offset";
$result = $conn->query($query);

// Get categories for filter
$categories_query = "SELECT * FROM categories ORDER BY name";
$categories_result = $conn->query($categories_query);

// Store categories in array
$categories = [];
while ($cat = $categories_result->fetch_assoc()) {
    $categories[] = $cat;
}

// Get category name if category_slug is set
$category_name = 'Sản phẩm';
if ($category_slug) {
    $cat_query = "SELECT name FROM categories WHERE slug = '$category_slug'";
    $cat_result = $conn->query($cat_query);
    if ($cat_result && $cat_result->num_rows > 0) {
        $category_name = $cat_result->fetch_assoc()['name'];
    }
}

closeDBConnection($conn);
?>

<div class="container">
    <div class="products-page">
        <aside class="products-sidebar">
            <h3>Lọc sản phẩm</h3>
            
            <div class="filter-section">
                <h4>Danh mục</h4>
                <ul class="filter-list">
                    <li><a href="<?php echo SITE_URL; ?>/products.php">Tất cả</a></li>
                    <?php foreach ($categories as $cat): ?>
                        <li>
                            <a href="<?php echo SITE_URL; ?>/products.php?category=<?php echo $cat['slug']; ?>" 
                               class="<?php echo $category_slug === $cat['slug'] ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="filter-section">
                <h4>Sắp xếp</h4>
                <form method="GET" action="">
                    <?php if ($search): ?>
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <?php endif; ?>
                    <?php if ($category_slug): ?>
                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_slug); ?>">
                    <?php endif; ?>
                    <select name="sort" onchange="this.form.submit()">
                        <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Mới nhất</option>
                        <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Giá tăng dần</option>
                        <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Giá giảm dần</option>
                        <option value="name" <?php echo $sort === 'name' ? 'selected' : ''; ?>>Tên A-Z</option>
                    </select>
                </form>
            </div>
        </aside>
        
        <main class="products-main">
            <div class="products-header">
                <h1>
                    <?php 
                    if ($category_slug) {
                        echo htmlspecialchars($category_name);
                    } elseif ($search) {
                        echo 'Kết quả tìm kiếm: ' . htmlspecialchars($search);
                    } else {
                        echo 'Tất cả sản phẩm';
                    }
                    ?>
                </h1>
                <p><?php echo $total_products; ?> sản phẩm</p>
            </div>
            
            <?php if ($result->num_rows > 0): ?>
                <div class="products-grid">
                    <?php while ($product = $result->fetch_assoc()): ?>
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
                                    <a href="<?php echo SITE_URL; ?>/product-detail.php?id=<?php echo $product['id']; ?>" class="action-icon"><i class="fas fa-eye"></i></a>
                                    <?php if (isLoggedIn()): ?>
                                        <a href="<?php echo SITE_URL; ?>/add-to-wishlist.php?id=<?php echo $product['id']; ?>" class="action-icon"><i class="far fa-heart"></i></a>
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
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?><?php echo $category_slug ? '&category=' . $category_slug : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="page-link">Trước</a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <?php if ($i == 1 || $i == $total_pages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                                <a href="?page=<?php echo $i; ?><?php echo $category_slug ? '&category=' . $category_slug : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                                   class="page-link <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                            <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                                <span class="page-link">...</span>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo $category_slug ? '&category=' . $category_slug : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="page-link">Sau</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="no-results">
                    <p>Không tìm thấy sản phẩm nào.</p>
                    <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-primary">Xem tất cả sản phẩm</a>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

