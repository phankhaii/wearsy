<?php
// Database configuration
// Thay đổi mật khẩu tại đây nếu MySQL của bạn có mật khẩu
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '123456'); // Để trống nếu không có mật khẩu, hoặc nhập mật khẩu MySQL của bạn
define('DB_NAME', 'wearsy_db');

// Create database connection
function getDBConnection() {
    try {
        // First, try to connect without database to create it if needed
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
        
        if ($conn->connect_error) {
            die("Database connection error: " . $conn->connect_error . "<br>" . 
                "Vui lòng kiểm tra lại cấu hình trong file config/database.php<br>" .
                "Nếu MySQL có mật khẩu, hãy cập nhật DB_PASS trong file config/database.php");
        }
        
        // Create database if it doesn't exist
        $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        if (!$conn->query($sql)) {
            die("Error creating database: " . $conn->error);
        }
        
        // Close connection and reconnect with database
        $conn->close();
        
        // Now connect to the specific database
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Exception $e) {
        die("Database connection error: " . $e->getMessage() . "<br>" . 
            "Vui lòng kiểm tra lại cấu hình trong file config/database.php");
    }
}

// Close database connection
function closeDBConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}
?>

