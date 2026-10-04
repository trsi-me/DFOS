-- DFOS - Digital Food Ordering System
-- إنشاء قاعدة البيانات والجداول

CREATE DATABASE IF NOT EXISTS dfos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dfos;

-- جدول المستخدمين
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- جدول الفئات
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- جدول الأطعمة
CREATE TABLE IF NOT EXISTS foods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category_id INT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    calories INT DEFAULT NULL,
    ingredients TEXT DEFAULT NULL,
    allergens TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- جدول الطلبات
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    tax DECIMAL(10,2) NOT NULL DEFAULT 0,
    final_price DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    service_type VARCHAR(30) NOT NULL DEFAULT 'takeout',
    reservation_date DATE DEFAULT NULL,
    reservation_time TIME DEFAULT NULL,
    guest_count INT DEFAULT NULL,
    reservation_notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- جدول عناصر الطلب
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    food_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (food_id) REFERENCES foods(id) ON DELETE CASCADE
);

-- إدراج بيانات تجريبية
INSERT INTO categories (name) VALUES 
('مشروبات'),
('وجبات رئيسية'),
('مقبلات'),
('حلويات');

INSERT INTO foods (name, description, price, category_id, image, calories, ingredients, allergens) VALUES 
('عصير برتقال', 'عصير برتقال طازج', 15.00, 1, NULL, 112, 'برتقال طازج، ماء، سكر', NULL),
('عصير مانجو', 'عصير مانجو طبيعي', 18.00, 1, NULL, 130, 'مانجو، ماء، سكر', NULL),
('قهوة عربية', 'قهوة عربية أصيلة', 12.00, 1, 'arabic_coffee.jpg', 5, 'بن عربي، ماء، هيل، زعفران', NULL),
('شاي بالنعناع', 'شاي أخضر مع النعناع', 8.00, 1, NULL, 2, 'شاي أخضر، نعناع طازج، ماء', NULL),
('برجر لحم', 'برجر لحم مع الخضار', 35.00, 2, NULL, 650, 'لحم بقري، خبز، خس، طماطم، جبن، صلصة', 'جلوتين، حليب'),
('دجاج مشوي', 'نصف دجاجة مشوية', 45.00, 2, NULL, 450, 'دجاج، بهارات، زيت زيتون', NULL),
('بيتزا مارجريتا', 'بيتزا بالجبن والطماطم', 40.00, 2, NULL, 680, 'عجينة، جبن موزاريلا، صلصة طماطم، ريحان', 'جلوتين، حليب'),
('سلطة سيزر', 'سلطة سيزر كلاسيكية', 25.00, 3, NULL, 350, 'خس، جبن بارميزان، خبز محمص، صلصة سيزر', 'جلوتين، حليب، بيض'),
('بطاطس مقلية', 'بطاطس مقلية مقرمشة', 15.00, 3, NULL, 365, 'بطاطس، زيت نباتي، ملح', NULL),
('كنافة', 'كنافة بالجبن', 20.00, 4, 'kunafa.jpg', 420, 'عجينة كنافة، جبن، سمن، سكر، ماء الورد، فستق', 'حليب، جلوتين'),
('بسبوسة', 'بسبوسة بالعسل', 18.00, 4, 'basbousa.jpg', 380, 'سميد، سكر، عسل، زبدة، جوز هند، ماء الورد', 'جوز، جلوتين'),
('شاي أخضر', 'شاي أخضر طبيعي منعش', 10.00, 1, 'green_tea.jpg', 2, 'أوراق شاي أخضر، ماء', NULL);

-- إنشاء حساب مدير (كلمة المرور: admin123)
INSERT INTO users (name, email, password, is_admin) VALUES 
('مدير النظام', 'admin@dfos.com', '$2y$10$yeyfFjDlSB2BXO6yUNfh6.f4WTwUqONgU2lcvTbbQh1wCBd/ogLi2', 1);
