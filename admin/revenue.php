<?php
require_once __DIR__ . '/../config/config.php';
requireAdmin();

$conn = getDBConnection();

// Get filter parameters
$period = isset($_GET['period']) ? sanitizeInput($_GET['period']) : 'month'; // day, week, month, year, all
$start_date = isset($_GET['start_date']) ? sanitizeInput($_GET['start_date']) : '';
$end_date = isset($_GET['end_date']) ? sanitizeInput($_GET['end_date']) : '';

// Build date filter (for simple queries without alias)
$date_filter = "";
// Build date filter with alias (for queries with JOIN)
$date_filter_aliased = "";

if (($period === 'custom' || $start_date || $end_date) && $start_date && $end_date) {
    $date_filter = "AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'";
    $date_filter_aliased = "AND DATE(o.created_at) BETWEEN '$start_date' AND '$end_date'";
} else {
    switch ($period) {
        case 'day':
            $date_filter = "AND DATE(created_at) = CURDATE()";
            $date_filter_aliased = "AND DATE(o.created_at) = CURDATE()";
            break;
        case 'week':
            $date_filter = "AND WEEK(created_at) = WEEK(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
            $date_filter_aliased = "AND WEEK(o.created_at) = WEEK(CURDATE()) AND YEAR(o.created_at) = YEAR(CURDATE())";
            break;
        case 'month':
            $date_filter = "AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
            $date_filter_aliased = "AND MONTH(o.created_at) = MONTH(CURDATE()) AND YEAR(o.created_at) = YEAR(CURDATE())";
            break;
        case 'year':
            $date_filter = "AND YEAR(created_at) = YEAR(CURDATE())";
            $date_filter_aliased = "AND YEAR(o.created_at) = YEAR(CURDATE())";
            break;
        case 'all':
        case 'custom':
        default:
            $date_filter = "";
            $date_filter_aliased = "";
            break;
    }
}

// Total revenue
$revenue_query = "SELECT SUM(total_amount) as total FROM orders WHERE payment_status = 'paid' $date_filter";
$revenue_result = $conn->query($revenue_query);
$total_revenue = $revenue_result->fetch_assoc()['total'] ?? 0;

// Total orders
$orders_query = "SELECT COUNT(*) as total FROM orders WHERE 1=1 $date_filter";
$orders_result = $conn->query($orders_query);
$total_orders = $orders_result->fetch_assoc()['total'] ?? 0;

// Completed orders
$completed_query = "SELECT COUNT(*) as total FROM orders WHERE order_status = 'delivered' $date_filter";
$completed_result = $conn->query($completed_query);
$completed_orders = $completed_result->fetch_assoc()['total'] ?? 0;

// Average order value
$avg_order = $total_orders > 0 ? $total_revenue / $total_orders : 0;

// Revenue by payment method
$payment_stats = $conn->query("
    SELECT payment_method, 
           COUNT(*) as count, 
           SUM(total_amount) as total 
    FROM orders 
    WHERE payment_status = 'paid' $date_filter
    GROUP BY payment_method
");

// Revenue by status
$status_stats = $conn->query("
    SELECT order_status, 
           COUNT(*) as count, 
           SUM(total_amount) as total 
    FROM orders 
    WHERE 1=1 $date_filter
    GROUP BY order_status
");

// Revenue by day (for chart)
$daily_revenue = $conn->query("
    SELECT DATE(created_at) as date, 
           SUM(total_amount) as total 
    FROM orders 
    WHERE payment_status = 'paid' 
    $date_filter
    GROUP BY DATE(created_at) 
    ORDER BY date DESC 
    LIMIT 30
");

$daily_labels = [];
$daily_values = [];
if ($daily_revenue) {
    while ($row = $daily_revenue->fetch_assoc()) {
        $daily_labels[] = date('d/m', strtotime($row['date']));
        $daily_values[] = (float)$row['total'];
    }
    // Đảo ngược để biểu đồ đi từ ngày cũ -> mới
    $daily_labels = array_reverse($daily_labels);
    $daily_values = array_reverse($daily_values);
}

// Top products by revenue
$top_products = $conn->query("
    SELECT p.name, 
           SUM(oi.quantity) as sold, 
           SUM(oi.price * oi.quantity) as revenue 
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    LEFT JOIN products p ON oi.product_id = p.id
    WHERE o.payment_status = 'paid' $date_filter_aliased
    GROUP BY p.id, p.name
    ORDER BY revenue DESC
    LIMIT 10
");

$pageTitle = 'Quản lý doanh thu';
include '../includes/header.php';
?>

<div class="admin-container">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="admin-main">
        <div class="admin-header">
            <h1><i class="fas fa-chart-line"></i> Quản lý doanh thu</h1>
        </div>
        
        <!-- Filter Section -->
        <div class="revenue-filters">
            <form method="GET" action="" class="filter-form">
                <div class="filter-group">
                    <label>Kỳ báo cáo:</label>
                    <select name="period" onchange="this.form.submit()">
                        <option value="day" <?php echo $period === 'day' ? 'selected' : ''; ?>>Hôm nay</option>
                        <option value="week" <?php echo $period === 'week' ? 'selected' : ''; ?>>Tuần này</option>
                        <option value="month" <?php echo $period === 'month' ? 'selected' : ''; ?>>Tháng này</option>
                        <option value="year" <?php echo $period === 'year' ? 'selected' : ''; ?>>Năm nay</option>
                        <option value="all" <?php echo $period === 'all' ? 'selected' : ''; ?>>Tất cả</option>
                        <option value="custom" <?php echo $period === 'custom' ? 'selected' : ''; ?>>Tùy chọn</option>
                    </select>
                </div>
                
                <?php if ($period === 'custom'): ?>
                <div class="filter-group">
                    <label>Từ ngày:</label>
                    <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>" required>
                </div>
                <div class="filter-group">
                    <label>Đến ngày:</label>
                    <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>" required>
                </div>
                <input type="hidden" name="period" value="custom">
                <button type="submit" class="btn btn-primary">Lọc</button>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card revenue">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo formatPrice($total_revenue); ?></h3>
                    <p>Tổng doanh thu</p>
                </div>
            </div>
            
            <div class="stat-card orders">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $total_orders; ?></h3>
                    <p>Tổng đơn hàng</p>
                </div>
            </div>
            
            <div class="stat-card completed">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $completed_orders; ?></h3>
                    <p>Đơn hoàn thành</p>
                </div>
            </div>
            
            <div class="stat-card average">
                <div class="stat-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo formatPrice($avg_order); ?></h3>
                    <p>Đơn hàng trung bình</p>
                </div>
            </div>
        </div>
        
        <!-- Revenue Line Chart -->
        <div class="chart-section" style="margin-bottom: 16px; max-width: 720px; margin-left: auto; margin-right: auto;">
            <h2>Biểu đồ doanh thu theo ngày</h2>
            <canvas id="dailyRevenueChart" height="60"></canvas>
            <p class="text-muted" style="margin-top: 6px; font-size: 12px;">
                Dữ liệu tối đa 30 ngày gần nhất (chỉ tính các đơn đã thanh toán).
            </p>
        </div>

        <!-- Revenue Charts -->
        <div class="revenue-charts">
            <div class="chart-section">
                <h2>Doanh thu theo phương thức thanh toán</h2>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Phương thức</th>
                                <th>Số lượng đơn</th>
                                <th>Doanh thu</th>
                                <th>Tỷ lệ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $payment_total = 0;
                            while ($row = $payment_stats->fetch_assoc()): 
                                $payment_total += $row['total'];
                            ?>
                                <tr>
                                    <td>
                                        <?php
                                        $methods = [
                                            'cod' => 'Thanh toán khi nhận hàng',
                                            'bank' => 'Chuyển khoản ngân hàng',
                                            'momo' => 'Ví MoMo',
                                            'zalopay' => 'Ví ZaloPay'
                                        ];
                                        echo $methods[$row['payment_method']] ?? $row['payment_method'];
                                        ?>
                                    </td>
                                    <td><?php echo $row['count']; ?></td>
                                    <td><?php echo formatPrice($row['total']); ?></td>
                                    <td>
                                        <?php 
                                        $percentage = $total_revenue > 0 ? ($row['total'] / $total_revenue) * 100 : 0;
                                        echo number_format($percentage, 1); ?>%
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="chart-section">
                <h2>Doanh thu theo trạng thái đơn hàng</h2>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Trạng thái</th>
                                <th>Số lượng</th>
                                <th>Doanh thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $status_stats->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <?php
                                        $statuses = [
                                            'pending' => 'Chờ xử lý',
                                            'processing' => 'Đang xử lý',
                                            'shipped' => 'Đã giao hàng',
                                            'delivered' => 'Đã nhận hàng',
                                            'cancelled' => 'Đã hủy'
                                        ];
                                        echo $statuses[$row['order_status']] ?? $row['order_status'];
                                        ?>
                                    </td>
                                    <td><?php echo $row['count']; ?></td>
                                    <td><?php echo formatPrice($row['total'] ?? 0); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Top Products -->
        <div class="top-products-section">
            <h2>Sản phẩm bán chạy</h2>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tên sản phẩm</th>
                            <th>Số lượng bán</th>
                            <th>Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $index = 1;
                        while ($product = $top_products->fetch_assoc()): 
                        ?>
                            <tr>
                                <td><?php echo $index++; ?></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo $product['sold']; ?></td>
                                <td><?php echo formatPrice($product['revenue']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function() {
        const canvas = document.getElementById('dailyRevenueChart');
        if (!canvas) return;

        // Cố định kích thước biểu đồ bất kể số lượng dữ liệu
        canvas.width = 720;
        canvas.height = 260;
        const ctx = canvas.getContext('2d');

        const labels = <?php echo json_encode($daily_labels, JSON_UNESCAPED_UNICODE); ?>;
        const data = <?php echo json_encode($daily_values, JSON_NUMERIC_CHECK); ?>;

        if (!labels.length) {
            ctx.parentElement.innerHTML += '<p style="margin-top:10px;font-size:13px;color:#718096;">Chưa có dữ liệu doanh thu để hiển thị biểu đồ.</p>';
            return;
        }

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Doanh thu (triệu VNĐ)',
                    data: data,
                    borderColor: 'rgba(49, 130, 206, 1)',
                    backgroundColor: 'rgba(129, 199, 212, 0.15)',
                    tension: 0.3,
                    fill: true,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: '#2c5282'
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const raw = context.parsed.y || 0;
                                const million = raw / 1000000;
                                return million.toLocaleString('vi-VN', { maximumFractionDigits: 2 }) + ' triệu VNĐ';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                const million = value / 1000000;
                                return million.toLocaleString('vi-VN', { maximumFractionDigits: 1 }) + ' tr';
                            }
                        }
                    }
                }
            }
        });
    })();
</script>

<?php closeDBConnection($conn); ?>
<?php include '../includes/footer.php'; ?>
