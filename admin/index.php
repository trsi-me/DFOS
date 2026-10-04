<?php
/**
 * الصفحة الرئيسية للوحة التحكم
 */
require_once 'includes/auth.php';
require_once '../config/database.php';

$pageTitle = 'لوحة التحكم';

// إحصائيات سريعة
$stats = [];
$stats['categories'] = $conn->query("SELECT COUNT(*) as c FROM categories")->fetch_assoc()['c'];
$stats['foods'] = $conn->query("SELECT COUNT(*) as c FROM foods")->fetch_assoc()['c'];
$stats['orders'] = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];
$stats['users'] = $conn->query("SELECT COUNT(*) as c FROM users WHERE is_admin = 0")->fetch_assoc()['c'];

include 'includes/header.php';
?>

<section class="admin-dashboard">
    <h1 class="section-title">لوحة التحكم</h1>
    <div class="admin-stats">
        <div class="stat-box">
            <h3><?php echo $stats['categories']; ?></h3>
            <p>الفئات</p>
            <a href="categories.php">إدارة</a>
        </div>
        <div class="stat-box">
            <h3><?php echo $stats['foods']; ?></h3>
            <p>أصناف الطعام</p>
            <a href="foods.php">إدارة</a>
        </div>
        <div class="stat-box">
            <h3><?php echo $stats['orders']; ?></h3>
            <p>الطلبات</p>
            <a href="orders.php">إدارة</a>
        </div>
        <div class="stat-box">
            <h3><?php echo $stats['users']; ?></h3>
            <p>المستخدمين</p>
            <a href="users.php">إدارة</a>
        </div>
    </div>
</section>

<?php
$footerPath = __DIR__ . '/includes/footer.php';
if (file_exists($footerPath)) include $footerPath;
else echo '</main></body></html>';
?>
