<?php
require_once __DIR__ . '/../config/config.php';
requireAdmin();

$conn = getDBConnection();

$success = '';
$error = '';

// Update user role or delete user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_role'])) {
        $user_id = (int)$_POST['user_id'];
        $new_role = sanitizeInput($_POST['role']);
        
        // Prevent changing own role
        if ($user_id == $_SESSION['user_id']) {
            $error = 'Bạn không thể thay đổi quyền của chính mình!';
        } else {
            $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->bind_param("si", $new_role, $user_id);
            if ($stmt->execute()) {
                $success = 'Đã cập nhật quyền người dùng thành công!';
            } else {
                $error = 'Có lỗi xảy ra khi cập nhật!';
            }
            $stmt->close();
        }
    }
    
    if (isset($_POST['delete_user'])) {
        $user_id = (int)$_POST['user_id'];
        
        // Prevent deleting own account
        if ($user_id == $_SESSION['user_id']) {
            $error = 'Bạn không thể xóa tài khoản của chính mình!';
        } else {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            if ($stmt->execute()) {
                $success = 'Đã xóa người dùng thành công!';
            } else {
                $error = 'Có lỗi xảy ra khi xóa!';
            }
            $stmt->close();
        }
    }
}

// Get filter parameters
$role_filter = isset($_GET['role']) ? sanitizeInput($_GET['role']) : '';
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';

// Build query
$where = "1=1";
if ($role_filter) {
    $where .= " AND role = '$role_filter'";
}
if ($search) {
    $where .= " AND (username LIKE '%$search%' OR email LIKE '%$search%' OR full_name LIKE '%$search%')";
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Get total count
$count_query = "SELECT COUNT(*) as total FROM users WHERE $where";
$count_result = $conn->query($count_query);
$total_users = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_users / $per_page);

// Get users
$query = "SELECT * FROM users WHERE $where ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
$result = $conn->query($query);

// Get statistics
$total_admins = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'admin'")->fetch_assoc()['total'];
$total_customers = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'")->fetch_assoc()['total'];

$pageTitle = 'Quản lý tài khoản';
include '../includes/header.php';
?>

<div class="admin-container">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="admin-main">
        <div class="admin-header">
            <h1><i class="fas fa-users"></i> Quản lý tài khoản</h1>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon users">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $total_users; ?></h3>
                    <p>Tổng tài khoản</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $total_admins; ?></h3>
                    <p>Quản trị viên</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $total_customers; ?></h3>
                    <p>Khách hàng</p>
                </div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filters-section">
            <form method="GET" action="" class="filter-form">
                <div class="filter-group">
                    <label>Lọc theo vai trò:</label>
                    <select name="role" onchange="this.form.submit()">
                        <option value="">Tất cả</option>
                        <option value="admin" <?php echo $role_filter === 'admin' ? 'selected' : ''; ?>>Quản trị viên</option>
                        <option value="user" <?php echo $role_filter === 'user' ? 'selected' : ''; ?>>Khách hàng</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Tìm kiếm:</label>
                    <input type="text" name="search" placeholder="Tên, email, username..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                
                <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                <a href="users.php" class="btn btn-outline">Reset</a>
            </form>
        </div>
        
        <!-- Users Table -->
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên đăng nhập</th>
                        <th>Email</th>
                        <th>Họ tên</th>
                        <th>Điện thoại</th>
                        <th>Vai trò</th>
                        <th>Ngày đăng ký</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                            <td>
                                <form method="POST" action="" style="display: inline-block;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <select name="role" onchange="this.form.submit()" class="status-select" <?php echo $user['id'] == $_SESSION['user_id'] ? 'disabled' : ''; ?>>
                                        <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>Khách hàng</option>
                                        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Quản trị viên</option>
                                    </select>
                                    <input type="hidden" name="update_role" value="1">
                                </form>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <form method="POST" action="" style="display: inline-block;" onsubmit="return confirm('Bạn có chắc muốn xóa tài khoản này?');">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" name="delete_user" value="1" class="btn btn-sm btn-danger">Xóa</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">Tài khoản của bạn</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?><?php echo $role_filter ? '&role=' . $role_filter : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="page-link">Trước</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <?php if ($i == 1 || $i == $total_pages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                        <a href="?page=<?php echo $i; ?><?php echo $role_filter ? '&role=' . $role_filter : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                           class="page-link <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                        <span class="page-link">...</span>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?><?php echo $role_filter ? '&role=' . $role_filter : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="page-link">Sau</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php closeDBConnection($conn); ?>
<?php include '../includes/footer.php'; ?>

