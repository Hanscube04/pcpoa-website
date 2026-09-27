-- =========================================================
-- PC POA COMPUTER SALES & REPAIR MANAGEMENT WEBSITE
-- Database: pcpoa_db
-- =========================================================

CREATE DATABASE IF NOT EXISTS pcpoa_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pcpoa_db;

-- ---------------------------------------------------------
-- 1. USERS TABLE (customers + admins)
-- ---------------------------------------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(30) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer','admin') NOT NULL DEFAULT 'customer',
    status ENUM('active','suspended') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 2. CATEGORIES TABLE
-- ---------------------------------------------------------
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 3. PRODUCTS TABLE
-- ---------------------------------------------------------
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(150) NOT NULL,
    brand VARCHAR(100),
    description TEXT,
    specifications TEXT,
    price DECIMAL(12,2) NOT NULL DEFAULT 0,
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255),
    status ENUM('active','hidden') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 4. SERVICES TABLE
-- ---------------------------------------------------------
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(150) NOT NULL,
    description TEXT,
    price VARCHAR(100),
    image VARCHAR(255),
    status ENUM('active','hidden') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 5. REPAIR REQUESTS TABLE
-- ---------------------------------------------------------
CREATE TABLE repair_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    customer_name VARCHAR(150),
    phone VARCHAR(30),
    email VARCHAR(150),
    device_type VARCHAR(100),
    brand VARCHAR(100),
    model VARCHAR(100),
    problem TEXT NOT NULL,
    preferred_date DATE,
    additional_info TEXT,
    status ENUM('Pending','Received','Diagnosing','Repairing','Ready for Collection','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
    repair_cost DECIMAL(12,2) DEFAULT NULL,
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_date TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 6. ORDERS TABLE
-- ---------------------------------------------------------
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    payment_status ENUM('Unpaid','Paid') NOT NULL DEFAULT 'Unpaid',
    order_status ENUM('Pending','Confirmed','Preparing','Out for Delivery','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
    delivery_address VARCHAR(255),
    phone VARCHAR(30),
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 7. ORDER ITEMS TABLE
-- ---------------------------------------------------------
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 8. MESSAGES TABLE (contact form)
-- ---------------------------------------------------------
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status ENUM('unread','read','replied') NOT NULL DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 9. VIDEOS TABLE (media gallery - admin uploads, customers view/play)
-- ---------------------------------------------------------
CREATE TABLE videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    video_file VARCHAR(255) NOT NULL,
    thumbnail VARCHAR(255),
    status ENUM('active','hidden') NOT NULL DEFAULT 'active',
    uploaded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 10. SETTINGS TABLE (site-wide info incl. logo, social, location)
--     Single-row table (id = 1) managed only from the admin panel.
-- ---------------------------------------------------------
CREATE TABLE settings (
    id INT PRIMARY KEY DEFAULT 1,
    site_name VARCHAR(150) NOT NULL DEFAULT 'PC Poa',
    logo VARCHAR(255) DEFAULT 'uploads/logo/logo.jpeg',
    slogan VARCHAR(255) DEFAULT 'Consult with us, buy with us, repair with us.',
    phone_primary VARCHAR(30) DEFAULT '0712 370 777',
    phone_secondary VARCHAR(30) DEFAULT '0792 294 949',
    whatsapp VARCHAR(30) DEFAULT '255712370777',
    email VARCHAR(150) DEFAULT 'info@pcpoa.co.tz',
    address VARCHAR(255) DEFAULT 'Agrey Street, Kariakoo, near Mkombozi Bank, Dar es Salaam, Tanzania',
    map_embed_url TEXT,
    instagram VARCHAR(150) DEFAULT 'pcpoa_official',
    tiktok VARCHAR(150) DEFAULT 'pcpoa',
    working_hours VARCHAR(150) DEFAULT 'Monday - Friday, 8:30 AM - 5:30 PM',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO settings (id) VALUES (1);

-- ---------------------------------------------------------
-- SAMPLE / SEED DATA
-- ---------------------------------------------------------

-- Default admin account (password: Admin@2026 -- CHANGE AFTER FIRST LOGIN)
-- Hash below corresponds to password_hash('Admin@2026', PASSWORD_DEFAULT)
INSERT INTO users (full_name, email, phone, password, role) VALUES
('PC Poa Admin', 'admin@pcpoa.co.tz', '0712370777', '$2y$10$92IXUNpkjO0rOQ5byMi.YeIvcqDsJoJkQ3wY0lJVkuvXLL/vRxL9C', 'admin');
-- NOTE: Run reset_admin_password.php once after import if this hash does not match your PHP/OpenSSL build.

INSERT INTO categories (category_name, description) VALUES
('Laptops', 'New and used laptops from trusted brands'),
('Desktops', 'Desktop computers and workstations'),
('Monitors', 'Computer monitors and displays'),
('Accessories', 'Keyboards, mice and other accessories'),
('Storage', 'SSDs and hard drives'),
('Networking', 'Routers, switches and networking equipment');

INSERT INTO products (category_id, name, brand, description, specifications, price, stock, image, status) VALUES
(1, 'HP EliteBook 840 G5', 'HP', 'Reliable business laptop, refurbished and tested.', 'Intel Core i5 | 8GB RAM | 256GB SSD | 14" Display | Windows 11', 850000, 5, 'uploads/products/sample-laptop-1.jpg', 'active'),
(1, 'Dell Latitude 3310', 'Dell', 'Compact and durable laptop, ideal for office use.', 'Intel Core i5 | 8GB RAM | 256GB SSD | 13.3" Display | Windows 11', 750000, 3, 'uploads/products/sample-laptop-2.jpg', 'active');

INSERT INTO services (service_name, description, price, image, status) VALUES
('Water Damaged PC Repair', 'We clean your laptop motherboard to remove corrosion and replace damaged chips and capacitors.', 'From TZS 30,000', 'uploads/services/service-motherboard.jpg', 'active'),
('Charging System Repair', 'We solve system charging issues such as battery not charging beyond 0%.', 'From TZS 25,000', 'uploads/services/service-motherboard.jpg', 'active'),
('Failed USB Port Repair', 'We fix faulty or dead USB ports on laptops and desktops.', 'From TZS 20,000', 'uploads/services/service-motherboard.jpg', 'active'),
('BIOS Programming', 'BIOS password removal and BIOS re-programming for slow booting PCs.', 'From TZS 20,000', 'uploads/services/service-motherboard.jpg', 'active'),
('LCD Replacement', 'We replace broken LCD screens and repair screens with no light or display.', 'From TZS 60,000', 'uploads/services/service-motherboard.jpg', 'active'),
('PC Keyboard Repair/Replacement', 'Laptop keyboard repair and replacement for all brands.', 'From TZS 25,000', 'uploads/services/service-motherboard.jpg', 'active'),
('Speaker Replacement', 'Laptop internal speaker diagnosis and replacement.', 'From TZS 20,000', 'uploads/services/service-motherboard.jpg', 'active'),
('Operating System Installation', 'Windows and software installation with genuine activation guidance.', 'From TZS 15,000', 'uploads/services/service-motherboard.jpg', 'active');
