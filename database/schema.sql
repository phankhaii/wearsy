-- Wearsy Fashion Store Database Schema
-- Created for XAMPP phpMyAdmin

CREATE DATABASE IF NOT EXISTS wearsy_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE wearsy_db;

-- Users table (for customers and admins)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    compare_price DECIMAL(10, 2),
    sku VARCHAR(50) UNIQUE,
    category_id INT,
    image VARCHAR(255),
    gallery TEXT,
    stock INT DEFAULT 0,
    size VARCHAR(100),
    color VARCHAR(100),
    brand VARCHAR(100),
    material VARCHAR(100),
    status ENUM('active', 'inactive', 'out_of_stock') DEFAULT 'active',
    featured BOOLEAN DEFAULT 0,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cart table
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    size VARCHAR(20),
    color VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (user_id, product_id, size, color)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Wishlist table
CREATE TABLE IF NOT EXISTS wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist (user_id, product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    shipping_address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    payment_method VARCHAR(50) DEFAULT 'cod',
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    order_status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT,
    product_name VARCHAR(200) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    size VARCHAR(20),
    color VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    order_id INT,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    UNIQUE KEY unique_review (user_id, product_id, order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: admin123)
-- Hash password được tạo bằng: password_hash('admin123', PASSWORD_DEFAULT)
INSERT IGNORE INTO users (username, email, password, full_name, role) VALUES 
('admin', 'admin@wearsy.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', 'Admin Wearsy', 'admin');

-- Insert sample categories
INSERT IGNORE INTO categories (name, slug, description) VALUES
('Áo', 'ao', 'Áo thời trang cho mọi lứa tuổi'),
('Quần', 'quan', 'Quần áo thời trang'),
('Váy', 'vay', 'Váy đẹp cho phụ nữ'),
('Giày dép', 'giay-dep', 'Giày dép thời trang'),
('Phụ kiện', 'phu-kien', 'Phụ kiện thời trang'),
('Đồng hồ', 'dong-ho', 'Đồng hồ thời trang');

-- Insert sample products
INSERT IGNORE INTO products (name, slug, description, price, compare_price, category_id, stock, size, color, brand, material, featured) VALUES
('Áo Thun Nam Cổ Tròn', 'ao-thun-nam-co-tron', 'Áo thun nam cổ tròn chất liệu cotton cao cấp, mát mẻ, thấm hút mồ hôi tốt', 299000, 399000, 1, 50, 'S,M,L,XL', 'Đen,Trắng,Xanh', 'Wearsy', 'Cotton', 1),
('Quần Jeans Nam', 'quan-jeans-nam', 'Quần jeans nam form slim fit, chất liệu denim cao cấp', 899000, 1199000, 2, 30, '28,29,30,31,32', 'Xanh đậm,Xanh nhạt', 'Wearsy', 'Denim', 1),
('Váy Đầm Nữ Dài Tay', 'vay-dam-nu-dai-tay', 'Váy đầm nữ dài tay thanh lịch, phù hợp mọi hoàn cảnh', 1299000, 1699000, 3, 25, 'S,M,L', 'Đen,Trắng,Hồng', 'Wearsy', 'Polyester', 1),
('Giày Sneaker Nữ', 'giay-sneaker-nu', 'Giày sneaker nữ đế cao su, êm ái, thời trang', 1599000, 1999000, 4, 40, '36,37,38,39,40', 'Trắng,Đen,Hồng', 'Wearsy', 'Da tổng hợp', 1),
('Túi Xách Nữ Da Thật', 'tui-xach-nu-da-that', 'Túi xách nữ da thật, thiết kế sang trọng', 2499000, 2999000, 5, 15, 'One Size', 'Đen,Nâu,Đỏ', 'Wearsy', 'Da thật', 1),
('Đồng Hồ Nam Dây Da', 'dong-ho-nam-day-da', 'Đồng hồ nam dây da, mặt số lớn, sang trọng', 3499000, 4499000, 6, 20, 'One Size', 'Đen,Nâu', 'Wearsy', 'Da + Kim loại', 1);

