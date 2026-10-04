<?php
/**
 * الصفحة الرئيسية - عرض قائمة الطعام حسب الفئات
 */
session_start();
require_once 'config/database.php';

$pageTitle = 'الرئيسية';

// جلب الفئات
$categoriesQuery = $conn->query("SELECT * FROM categories ORDER BY name");
$categories = $categoriesQuery ? $categoriesQuery->fetch_all(MYSQLI_ASSOC) : [];

// جلب الطعام حسب الفئة المختارة
$selectedCategory = isset($_GET['category']) ? (int)$_GET['category'] : null;
if ($selectedCategory) {
    $stmt = $conn->prepare("SELECT f.*, c.name as category_name FROM foods f 
        JOIN categories c ON f.category_id = c.id 
        WHERE f.category_id = ? ORDER BY f.name");
    $stmt->bind_param("i", $selectedCategory);
    $stmt->execute();
    $foods = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $foodsQuery = $conn->query("SELECT f.*, c.name as category_name FROM foods f 
        JOIN categories c ON f.category_id = c.id ORDER BY c.name, f.name");
    $foods = $foodsQuery ? $foodsQuery->fetch_all(MYSQLI_ASSOC) : [];
}

// صور محددة للأصناف (حسب طلب المستخدم)
$specificFoodImages = [
    'بسبوسة' => 'https://i.pinimg.com/736x/d2/76/b2/d276b297f4e90cd5b28b084f5dc1d276.jpg',
    'كنافة' => 'https://www.atyabtabkha.com/tachyon/sites/2/2022/09/kunafa-1.jpg',
    'قهوة عربية' => 'https://kitchen.sayidaty.net/uploads/small/c0/c02473ef8ea1509755391a8bbd6de8c1_w750_h500.jpg',
    'شاي أخضر' => 'https://daiohs-s.com/theme/tmr_simple/img/top/service_02.jpg',
];
// صور احتياطية للأصناف الأخرى
$foodImages = [
    1 => 'https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?w=600&q=80',
    2 => 'https://images.unsplash.com/photo-1600271886742-f049cd451bba?w=600&q=80',
    3 => $specificFoodImages['قهوة عربية'],
    4 => 'https://images.unsplash.com/photo-1564890369478-c89ca6d9cde9?w=600&q=80',
    5 => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600&q=80',
    6 => 'https://images.unsplash.com/photo-1598103442097-8b74394b95c6?w=600&q=80',
    7 => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=600&q=80',
    8 => 'https://images.unsplash.com/photo-1546793665-c74683f339c1?w=600&q=80',
    9 => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=600&q=80',
    10 => $specificFoodImages['كنافة'],
    11 => $specificFoodImages['بسبوسة'],
    12 => $specificFoodImages['شاي أخضر'],
];

include 'includes/header.php';
?>

<div class="hero-banner">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>مرحباً بك في DFOS</h1>
        <p>اطلب طعامك المفضل بسهولة وسرعة — نظام طلب عالمي احترافي</p>
        <div class="hero-cta-row">
            <a href="reserve_table.php" class="hero-btn hero-btn-reserve">حجز طاولة طعام</a>
            <a href="#menu-anchor" class="hero-btn hero-btn-menu">تصفح القائمة</a>
        </div>
    </div>
</div>

<section class="food-menu-section" id="menu-anchor">
    <div class="section-header">
        <h1 class="section-title">قائمتنا</h1>
        <p class="section-subtitle">اختر من بين تشكيلتنا الواسعة من الأصناف المميزة</p>
    </div>
    
    <div class="category-filter">
        <a href="index.php" class="category-btn <?php echo !$selectedCategory ? 'active' : ''; ?>">الكل</a>
        <?php foreach ($categories as $cat): ?>
            <a href="index.php?category=<?php echo $cat['id']; ?>" 
               class="category-btn <?php echo $selectedCategory == $cat['id'] ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($cat['name']); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="foods-grid">
        <?php if (empty($foods)): ?>
            <p class="empty-message">لا توجد أصناف في هذه الفئة</p>
        <?php else: ?>
            <?php foreach ($foods as $food): 
                $imgUrl = isset($specificFoodImages[$food['name']]) ? $specificFoodImages[$food['name']] : (!empty($food['image']) && file_exists('assets/images/' . $food['image']) ? 'assets/images/' . $food['image'] : ($foodImages[$food['id']] ?? 'https://placehold.co/500x350/e8e8e8/999?text=+'));
            ?>
                <article class="food-card">
                    <a href="product_detail.php?id=<?php echo $food['id']; ?>" class="food-card-link">
                        <div class="food-card-image">
                            <img src="<?php echo htmlspecialchars($imgUrl); ?>" alt="<?php echo htmlspecialchars($food['name']); ?>" loading="lazy" onerror="this.src='https://placehold.co/500x350/e8e8e8/999?text=+'">
                            <div class="food-card-overlay"></div>
                        </div>
                        <div class="food-card-body">
                            <h3 class="food-name"><?php echo htmlspecialchars($food['name']); ?></h3>
                            <p class="food-description"><?php echo htmlspecialchars($food['description'] ?? ''); ?></p>
                        </div>
                    </a>
                    <div class="food-card-footer">
                        <span class="food-price"><?php echo number_format($food['price'], 2); ?> ر.س</span>
                        <a href="product_detail.php?id=<?php echo $food['id']; ?>" class="detail-link">تفاصيل</a>
                        <form method="POST" action="add_to_cart.php" class="add-cart-form">
                            <input type="hidden" name="food_id" value="<?php echo $food['id']; ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="add-to-cart-btn">أضف</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
