<?php
require_once __DIR__ . '/../config/config.php';
requireAdmin();

$conn = getDBConnection();

// Lấy danh sách category theo slug để không phụ thuộc ID cố định
$categories = [];
$result = $conn->query("SELECT id, slug FROM categories");
while ($row = $result->fetch_assoc()) {
    $categories[$row['slug']] = (int)$row['id'];
}

// Danh sách sản phẩm mẫu để seed (không trùng slug sẽ tránh insert trùng)
$products = [
    // Áo
    [
        'name' => 'Áo Polo Nam Basic',
        'slug' => 'ao-polo-nam-basic',
        'description' => 'Áo polo nam form regular, chất liệu cotton thoáng mát, dễ phối đồ hằng ngày.',
        'price' => 349000,
        'compare_price' => 449000,
        'category_slug' => 'ao',
        'stock' => 80,
        'size' => 'S,M,L,XL',
        'color' => 'Trắng,Đen,Xanh dương',
        'brand' => 'Wearsy',
        'material' => 'Cotton',
        'featured' => 1,
    ],
    [
        'name' => 'Áo Sơ Mi Nam Trơn Dài Tay',
        'slug' => 'ao-so-mi-nam-tron-dai-tay',
        'description' => 'Áo sơ mi nam trơn, dáng slim fit, phù hợp đi làm và đi chơi.',
        'price' => 399000,
        'compare_price' => 529000,
        'category_slug' => 'ao',
        'stock' => 60,
        'size' => 'S,M,L,XL',
        'color' => 'Trắng,Xanh nhạt,Be',
        'brand' => 'Wearsy',
        'material' => 'Cotton',
        'featured' => 0,
    ],
    [
        'name' => 'Áo Thun Nữ Oversize',
        'slug' => 'ao-thun-nu-oversize',
        'description' => 'Áo thun nữ form rộng, in chữ trước ngực, phong cách streetwear.',
        'price' => 259000,
        'compare_price' => 329000,
        'category_slug' => 'ao',
        'stock' => 70,
        'size' => 'S,M,L',
        'color' => 'Trắng,Đen,Hồng',
        'brand' => 'Wearsy',
        'material' => 'Cotton',
        'featured' => 1,
    ],
    [
        'name' => 'Áo Khoác Hoodie Unisex',
        'slug' => 'ao-khoac-hoodie-unisex',
        'description' => 'Hoodie nỉ bông ấm, có túi kengroo, thích hợp thu đông.',
        'price' => 499000,
        'compare_price' => 659000,
        'category_slug' => 'ao',
        'stock' => 45,
        'size' => 'M,L,XL,XXL',
        'color' => 'Đen,Xám,Be',
        'brand' => 'Wearsy',
        'material' => 'Nỉ bông',
        'featured' => 1,
    ],
    [
        'name' => 'Áo Cardigan Len Nữ',
        'slug' => 'ao-cardigan-len-nu',
        'description' => 'Cardigan len mỏng, dáng rộng, phối được nhiều kiểu váy và quần.',
        'price' => 459000,
        'compare_price' => 599000,
        'category_slug' => 'ao',
        'stock' => 35,
        'size' => 'S,M,L',
        'color' => 'Nâu,Be,Xanh rêu',
        'brand' => 'Wearsy',
        'material' => 'Len acrylic',
        'featured' => 0,
    ],
    [
        'name' => 'Áo Thun Nam In Hình Graphic',
        'slug' => 'ao-thun-nam-in-graphic',
        'description' => 'Áo thun in hình graphic trước ngực, phong cách trẻ trung cá tính.',
        'price' => 319000,
        'compare_price' => 419000,
        'category_slug' => 'ao',
        'stock' => 55,
        'size' => 'S,M,L,XL',
        'color' => 'Trắng,Đen,Xanh lá',
        'brand' => 'Wearsy',
        'material' => 'Cotton',
        'featured' => 0,
    ],

    // Quần
    [
        'name' => 'Quần Tây Nam Slim Fit',
        'slug' => 'quan-tay-nam-slim-fit',
        'description' => 'Quần tây nam dáng ôm nhẹ, phù hợp công sở và sự kiện.',
        'price' => 599000,
        'compare_price' => 799000,
        'category_slug' => 'quan',
        'stock' => 40,
        'size' => '28,29,30,31,32,33',
        'color' => 'Đen,Xám,Than',
        'brand' => 'Wearsy',
        'material' => 'Polyester + Rayon',
        'featured' => 1,
    ],
    [
        'name' => 'Quần Jogger Unisex',
        'slug' => 'quan-jogger-unisex',
        'description' => 'Quần jogger thể thao bo gấu, vải thun co giãn thoải mái.',
        'price' => 379000,
        'compare_price' => 479000,
        'category_slug' => 'quan',
        'stock' => 70,
        'size' => 'S,M,L,XL',
        'color' => 'Đen,Xám,Olive',
        'brand' => 'Wearsy',
        'material' => 'Thun Poly',
        'featured' => 0,
    ],
    [
        'name' => 'Quần Short Jean Nữ Lưng Cao',
        'slug' => 'quan-short-jean-nu-lung-cao',
        'description' => 'Quần short jean nữ lưng cao tôn dáng, dễ phối áo croptop.',
        'price' => 329000,
        'compare_price' => 429000,
        'category_slug' => 'quan',
        'stock' => 65,
        'size' => 'S,M,L',
        'color' => 'Xanh nhạt,Xanh đậm',
        'brand' => 'Wearsy',
        'material' => 'Denim',
        'featured' => 1,
    ],
    [
        'name' => 'Quần Kaki Nam Ống Đứng',
        'slug' => 'quan-kaki-nam-ong-dung',
        'description' => 'Quần kaki nam ống đứng, dễ mặc, không nhăn.',
        'price' => 449000,
        'compare_price' => 579000,
        'category_slug' => 'quan',
        'stock' => 50,
        'size' => '29,30,31,32,33,34',
        'color' => 'Be,Nâu,Xanh rêu',
        'brand' => 'Wearsy',
        'material' => 'Kaki',
        'featured' => 0,
    ],
    [
        'name' => 'Quần Legging Thể Thao Nữ',
        'slug' => 'quan-legging-the-thao-nu',
        'description' => 'Quần legging co giãn 4 chiều, thích hợp tập gym, yoga.',
        'price' => 299000,
        'compare_price' => 399000,
        'category_slug' => 'quan',
        'stock' => 80,
        'size' => 'S,M,L',
        'color' => 'Đen,Xám',
        'brand' => 'Wearsy',
        'material' => 'Poly + Spandex',
        'featured' => 1,
    ],
    [
        'name' => 'Quần Jean Nữ Rách Gối',
        'slug' => 'quan-jean-nu-rach-goi',
        'description' => 'Quần jean nữ rách nhẹ ở gối, phong cách trẻ trung.',
        'price' => 559000,
        'compare_price' => 729000,
        'category_slug' => 'quan',
        'stock' => 45,
        'size' => '26,27,28,29,30',
        'color' => 'Xanh nhạt,Đen',
        'brand' => 'Wearsy',
        'material' => 'Denim',
        'featured' => 0,
    ],

    // Tương tự: Váy (vay), Giày dép (giay-dep), Phụ kiện (phu-kien), Đồng hồ (dong-ho)
    // (để ngắn gọn, bạn đã có danh sách đầy đủ trong sample_products.sql, có thể copy thêm vào đây nếu cần nhiều hơn)
];

$inserted = 0;
$skipped = 0;

$stmt = $conn->prepare(
    "INSERT INTO products (name, slug, description, price, compare_price, category_id, stock, size, color, brand, material, status, featured)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?)"
);

foreach ($products as $p) {
    $slug = $conn->real_escape_string($p['slug']);

    // Nếu slug đã tồn tại thì bỏ qua, tránh trùng sản phẩm
    $check = $conn->query("SELECT id FROM products WHERE slug = '$slug' LIMIT 1");
    if ($check && $check->num_rows > 0) {
        $skipped++;
        continue;
    }

    $category_slug = $p['category_slug'];
    if (!isset($categories[$category_slug])) {
        $skipped++;
        continue;
    }
    $category_id = $categories[$category_slug];

    $name = $p['name'];
    $description = $p['description'];
    $price = (float)$p['price'];
    $compare_price = (float)$p['compare_price'];
    $stock = (int)$p['stock'];
    $size = $p['size'];
    $color = $p['color'];
    $brand = $p['brand'];
    $material = $p['material'];
    $featured = (int)$p['featured'];

    $stmt->bind_param(
        "sssddiissssi",
        $name,
        $slug,
        $description,
        $price,
        $compare_price,
        $category_id,
        $stock,
        $size,
        $color,
        $brand,
        $material,
        $featured
    );

    if ($stmt->execute()) {
        $inserted++;
    } else {
        $skipped++;
    }
}

$stmt->close();
closeDBConnection($conn);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Seed sản phẩm Wearsy</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>
<body>
<div class="container" style="padding: 40px 0;">
    <h1>Seed sản phẩm mẫu</h1>
    <p>Đã thêm <strong><?php echo $inserted; ?></strong> sản phẩm mới.</p>
    <p>Bỏ qua <strong><?php echo $skipped; ?></strong> sản phẩm (do đã tồn tại hoặc thiếu danh mục).</p>
    <p>Bạn có thể xóa file <code>admin/seed-products.php</code> sau khi đã seed xong để an toàn hơn.</p>
    <a href="<?php echo SITE_URL; ?>/admin/products.php" class="btn btn-primary" style="margin-top: 20px;">Về danh sách sản phẩm</a>
</div>
</body>
</html>


