<?php
/**
 * صفحة تفاصيل المنتج - السعرات، المكونات، الحساسية
 */
session_start();
require_once 'config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$stmt = $conn->prepare("SELECT f.*, c.name as category_name FROM foods f 
    LEFT JOIN categories c ON f.category_id = c.id WHERE f.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$food = $stmt->get_result()->fetch_assoc();

if (!$food) {
    header('Location: index.php');
    exit;
}

$specificFoodImages = [
    'بسبوسة' => 'https://i.pinimg.com/736x/d2/76/b2/d276b297f4e90cd5b28b084f5dc1d276.jpg',
    'كنافة' => 'https://www.atyabtabkha.com/tachyon/sites/2/2022/09/kunafa-1.jpg',
    'قهوة عربية' => 'https://kitchen.sayidaty.net/uploads/small/c0/c02473ef8ea1509755391a8bbd6de8c1_w750_h500.jpg',
    'شاي أخضر' => 'https://daiohs-s.com/theme/tmr_simple/img/top/service_02.jpg',
];
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
$imgUrl = isset($specificFoodImages[$food['name']]) ? $specificFoodImages[$food['name']] : (!empty($food['image']) && file_exists('assets/images/' . $food['image']) ? 'assets/images/' . $food['image'] : ($foodImages[$food['id']] ?? 'https://placehold.co/600x400/e8e8e8/999?text=+'));

// محتوى تفصيلي إضافي لكل منتج
$productExtras = [
    'عصير برتقال' => [
        'full_desc' => 'عصير برتقال طازج يُعصر يومياً من أجود أنواع البرتقال، غني بفيتامين ج ومضادات الأكسدة. يُقدم بارداً ومنعشاً، مثالي لبداية اليوم أو كمرافق للوجبات.',
        'prep' => 'يُعصر البرتقال طازجاً عند الطلب، يُضاف القليل من السكر حسب الرغبة، ويُقدم مع الثلج.',
        'tips' => 'يُفضل تناوله فوراً للحصول على أقصى استفادة من الفيتامينات.',
    ],
    'عصير مانجو' => [
        'full_desc' => 'عصير مانجو طبيعي 100% من فاكهة المانجو الاستوائية الناضجة. يتميز بقوامه الكريمي ونكهته الحلوة المنعشة، غني بفيتامين أ والألياف.',
        'prep' => 'يُخلط المانجو الطازج مع القليل من الماء والسكر، ويُقدم بارداً.',
        'tips' => 'مثالي كوجبة خفيفة صحية أو مع وجبة الإفطار.',
    ],
    'قهوة عربية' => [
        'full_desc' => 'قهوة عربية أصيلة تُحضر وفق التقاليد الخليجية، مع الهيل والزعفران. تُقدم في الدلة التقليدية، رمز الكرم والضيافة العربية.',
        'prep' => 'تُحمص حبوب البن العربي، تُطحن وتُغلى مع الهيل والزعفران، تُقدم في فناجين صغيرة.',
        'tips' => 'تُقدم عادة مع التمر أو الحلويات العربية، وتُشرب بدون سكر.',
    ],
    'شاي بالنعناع' => [
        'full_desc' => 'شاي أخضر منعش مع أوراق النعناع الطازج، مزيج تقليدي مغاربي. منعش بعد الوجبات وخفيف على المعدة.',
        'prep' => 'يُنقع الشاي الأخضر مع النعناع الطازج في الماء الساخن، يُحلى حسب الرغبة.',
        'tips' => 'يُقدم ساخناً في الشتاء وبارداً في الصيف.',
    ],
    'برجر لحم' => [
        'full_desc' => 'برجر لحم بقري طازج 180 جرام مع خبز طري، خس طازج، شرائح طماطم، جبن شيدر، وصلصة خاصة. وجبة مشبعة ولذيذة.',
        'prep' => 'تُشوى قطعة اللحم على الشواية، تُوضع في الخبز مع الخضار والجبن والصلصة.',
        'tips' => 'يُقدم مع البطاطس المقلية والمشروبات الغازية.',
    ],
    'دجاج مشوي' => [
        'full_desc' => 'نصف دجاجة مشوية على الفحم مع بهارات خاصة وزيوت عطرية. جلد مقرمش من الخارج ولحم طري من الداخل.',
        'prep' => 'يُتبل الدجاج بالبهارات والزيت، يُشوى على الفحم حتى النضج الكامل.',
        'tips' => 'يُقدم مع الأرز أو الخبز والسلطة والحمص.',
    ],
    'بيتزا مارجريتا' => [
        'full_desc' => 'بيتزا إيطالية كلاسيكية بعجينة رقيقة، صلصة طماطم طازجة، جبن موزاريلا، وريحان طازج. بسيطة وأنيقة.',
        'prep' => 'تُفرد العجينة، تُغطى بالصلصة والجبن، تُخبز في فرن حجري حتى الذوبان.',
        'tips' => 'تُقدم ساخنة مع زيت الزيتون والريحان الطازج.',
    ],
    'سلطة سيزر' => [
        'full_desc' => 'سلطة سيزر كلاسيكية بخس رومانو طازج، جبن بارميزان، خبز محمص، وصلصة سيزر الكريمية. وجبة خفيفة أو مقبلات.',
        'prep' => 'يُقطع الخس، يُضاف الجبن والخبز المحمص، وتُسكب صلصة سيزر.',
        'tips' => 'تُقدم كطبق رئيسي خفيف أو مقبلات قبل الوجبة.',
    ],
    'بطاطس مقلية' => [
        'full_desc' => 'بطاطس مقلية مقرمشة ذهبية، مقطعة يدوياً ومقلية في زيت نباتي نظيف. تُقدم مع الكاتشب أو المايونيز.',
        'prep' => 'تُقطع البطاطس وتُقلى حتى تصبح ذهبية مقرمشة، تُملح وتُقدم ساخنة.',
        'tips' => 'مثالية كطبق جانبي مع البرجر أو الدجاج.',
    ],
    'كنافة' => [
        'full_desc' => 'كنافة نابلسية تقليدية بعجينة الكنافة الطرية والجبن الحلو، تُسقى بالق syrup وماء الورد، وتُزين بالفستق الحلبي.',
        'prep' => 'تُفرد عجينة الكنافة، تُضاف الجبن، تُخبز حتى الذهبية، تُسقى بالقطر.',
        'tips' => 'تُقدم ساخنة مع القهوة العربية، وتُزين بالفستق المطحون.',
    ],
    'بسبوسة' => [
        'full_desc' => 'بسبوسة شرقية بالسميد والعسل، طرية من الداخل ومقرمشة من الخارج. تُزين بجوز الهند وماء الورد.',
        'prep' => 'يُخلط السميد مع الزبدة والسكر، يُخبز، ثم يُسقى بشراب السكر والعسل.',
        'tips' => 'تُقدم باردة أو بدرجة حرارة الغرفة مع الشاي أو القهوة.',
    ],
    'شاي أخضر' => [
        'full_desc' => 'شاي أخضر ياباني طبيعي، غني بمضادات الأكسدة ومنخفض الكافيين. يُقدم ساخناً أو مثلجاً حسب الموسم.',
        'prep' => 'يُنقع الشاي الأخضر في ماء ساخن (80 مئوية) لمدة 2-3 دقائق.',
        'tips' => 'مثالي للاسترخاء أو كبديل صحي للقهوة.',
    ],
];
$extra = $productExtras[$food['name']] ?? [];

// بيانات احتياطية للسعرات والمكونات والحساسية (إن لم تكن في قاعدة البيانات)
$fallbackNutrition = [
    'عصير برتقال' => ['calories' => 112, 'ingredients' => 'برتقال طازج، ماء، سكر', 'allergens' => null],
    'عصير مانجو' => ['calories' => 130, 'ingredients' => 'مانجو، ماء، سكر', 'allergens' => null],
    'قهوة عربية' => ['calories' => 5, 'ingredients' => 'بن عربي، ماء، هيل، زعفران', 'allergens' => null],
    'شاي بالنعناع' => ['calories' => 2, 'ingredients' => 'شاي أخضر، نعناع طازج، ماء', 'allergens' => null],
    'برجر لحم' => ['calories' => 650, 'ingredients' => 'لحم بقري، خبز، خس، طماطم، جبن، صلصة', 'allergens' => 'جلوتين، حليب'],
    'دجاج مشوي' => ['calories' => 450, 'ingredients' => 'دجاج، بهارات، زيت زيتون', 'allergens' => null],
    'بيتزا مارجريتا' => ['calories' => 680, 'ingredients' => 'عجينة، جبن موزاريلا، صلصة طماطم، ريحان', 'allergens' => 'جلوتين، حليب'],
    'سلطة سيزر' => ['calories' => 350, 'ingredients' => 'خس، جبن بارميزان، خبز محمص، صلصة سيزر', 'allergens' => 'جلوتين، حليب، بيض'],
    'بطاطس مقلية' => ['calories' => 365, 'ingredients' => 'بطاطس، زيت نباتي، ملح', 'allergens' => null],
    'كنافة' => ['calories' => 420, 'ingredients' => 'عجينة كنافة، جبن، سمن، سكر، ماء الورد، فستق', 'allergens' => 'حليب، جلوتين'],
    'بسبوسة' => ['calories' => 380, 'ingredients' => 'سميد، سكر، عسل، زبدة، جوز هند، ماء الورد', 'allergens' => 'جوز، جلوتين'],
    'شاي أخضر' => ['calories' => 2, 'ingredients' => 'أوراق شاي أخضر، ماء', 'allergens' => null],
];
$fb = $fallbackNutrition[$food['name']] ?? [];
$food['calories'] = $food['calories'] ?? $fb['calories'] ?? null;
$food['ingredients'] = $food['ingredients'] ?? $fb['ingredients'] ?? null;
$food['allergens'] = isset($food['allergens']) && $food['allergens'] !== '' ? $food['allergens'] : ($fb['allergens'] ?? null);

$pageTitle = $food['name'];
include 'includes/header.php';
?>

<section class="product-detail-section">
    <a href="index.php" class="back-to-menu">العودة للقائمة</a>

    <div class="product-detail-layout">
        <div class="product-detail-image">
            <img src="<?php echo htmlspecialchars($imgUrl); ?>" alt="<?php echo htmlspecialchars($food['name']); ?>" onerror="this.src='https://placehold.co/600x400/e8e8e8/999?text=+'">
        </div>

        <div class="product-detail-info">
            <span class="product-category"><?php echo htmlspecialchars($food['category_name'] ?? ''); ?></span>
            <h1 class="product-title"><?php echo htmlspecialchars($food['name']); ?></h1>
            <p class="product-description"><?php echo htmlspecialchars($food['description'] ?? ''); ?></p>
            <?php if (!empty($extra['full_desc'])): ?>
                <p class="product-full-desc"><?php echo htmlspecialchars($extra['full_desc']); ?></p>
            <?php endif; ?>
            <p class="product-price"><?php echo number_format($food['price'], 2); ?> ر.س</p>

            <form method="POST" action="add_to_cart.php" class="product-add-form">
                <input type="hidden" name="food_id" value="<?php echo $food['id']; ?>">
                <input type="number" name="quantity" value="1" min="1" class="product-qty">
                <button type="submit" class="add-to-cart-btn">أضف للسلة</button>
            </form>

            <div class="product-info-cards">
                <div class="info-card">
                    <span class="info-card-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 23c-4.97 0-9-4.03-9-9 0-2.5 1.05-4.74 2.74-6.32.37-.34.95-.1.95.44v.18c0 2.08 1.68 3.77 3.76 3.77 1.04 0 2.02-.42 2.72-1.15.7-.73 1.09-1.7 1.09-2.73 0-.34-.04-.68-.1-1.01-.06-.33-.14-.66-.24-.98-.1-.32-.22-.63-.36-.93-.14-.3-.3-.58-.48-.85-.18-.27-.38-.52-.6-.76-.22-.24-.46-.46-.72-.66-.26-.2-.54-.38-.84-.53-.3-.15-.62-.27-.95-.37-.33-.1-.67-.17-1.02-.21-.35-.04-.7-.05-1.05-.02-.35.03-.69.09-1.02.17-.33.08-.65.19-.96.32-.31.13-.6.29-.88.48-.28.19-.54.4-.78.64-.24.24-.45.5-.64.78-.19.28-.35.57-.48.88-.13.31-.24.63-.32.96-.08.33-.14.67-.17 1.02-.03.35-.02.7.02 1.05.04.35.11.69.21 1.02.1.33.22.65.37.95.15.3.33.58.53.84.2.26.42.5.66.72.24.22.49.42.76.6.27.18.55.34.85.48.3.14.61.26.93.36.32.1.65.18.98.24.33.06.67.1 1.01.1 1.03 0 2-.39 2.73-1.09.73-.7 1.15-1.68 1.15-2.72 0-2.08-1.69-3.76-3.77-3.76h-.18c-.54 0-.78.58-.44.95 1.58 1.69 2.32 3.82 2.32 6.09 0 4.97-4.03 9-9 9z"/></svg></span>
                    <h3>السعرات الحرارية</h3>
                    <p><?php echo $food['calories'] ? number_format((int)$food['calories']) . ' سعرة حرارية' : 'غير متوفر'; ?></p>
                </div>
                <div class="info-card">
                    <span class="info-card-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M3 5h2v2H3V5zm0 6h2v2H3v-2zm0 6h2v2H3v-2zm4-12h14v2H7V5zm0 6h14v2H7v-2zm0 6h14v2H7v-2z"/></svg></span>
                    <h3>المكونات</h3>
                    <p><?php echo !empty($food['ingredients']) ? htmlspecialchars($food['ingredients']) : 'غير متوفر'; ?></p>
                </div>
                <div class="info-card <?php echo !empty($food['allergens']) ? 'has-allergens' : ''; ?>">
                    <span class="info-card-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99zM11 10v4h2v-4h-2zm0 6v2h2v-2h-2z"/></svg></span>
                    <h3>الحساسية</h3>
                    <p><?php echo !empty($food['allergens']) ? htmlspecialchars($food['allergens']) : 'لا توجد مسببات حساسية معروفة'; ?></p>
                </div>
            </div>

            <?php if (!empty($extra['prep']) || !empty($extra['tips'])): ?>
            <div class="product-extra-content">
                <?php if (!empty($extra['prep'])): ?>
                <div class="extra-block">
                    <h4>طريقة التحضير</h4>
                    <p><?php echo htmlspecialchars($extra['prep']); ?></p>
                </div>
                <?php endif; ?>
                <?php if (!empty($extra['tips'])): ?>
                <div class="extra-block">
                    <h4>نصائح التقديم</h4>
                    <p><?php echo htmlspecialchars($extra['tips']); ?></p>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
