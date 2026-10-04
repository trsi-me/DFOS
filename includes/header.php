<?php
/**
 * رأس الصفحة المشترك - Header
 * يحتوي على الشعار والقائمة الرئيسية
 */
require_once __DIR__ . '/../config/asset_version.php';
$basePath = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../' : '';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>DFOS</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/style.css?v=<?php echo rawurlencode(ASSET_VERSION); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="main-header">
        <div class="header-content">
            <a href="<?php echo $basePath; ?>index.php" class="logo-link">
                <img src="<?php echo $basePath; ?>assets/images/Logo.jpeg" alt="DFOS Logo" class="site-logo" onerror="this.style.display='none'; var s=this.nextElementSibling; if(s) s.style.display='inline';">
            </a>
            <nav class="main-nav">
                <a href="<?php echo $basePath; ?>index.php">الرئيسية</a>
                <a href="<?php echo $basePath; ?>reserve_table.php" class="nav-link-reserve">حجز طاولة</a>
                <a href="<?php echo $basePath; ?>cart.php">السلة</a>
                <a href="<?php echo $basePath; ?>about.php">عن النظام</a>
                <?php if (isset($_SESSION['admin_id'])): ?>
                    <a href="<?php echo $basePath; ?>admin/index.php" class="nav-admin-btn">لوحة التحكم</a>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo $basePath; ?>profile.php">حسابي</a>
                    <a href="<?php echo $basePath; ?>logout.php">تسجيل الخروج</a>
                <?php else: ?>
                    <a href="<?php echo $basePath; ?>login.php">تسجيل الدخول</a>
                    <a href="<?php echo $basePath; ?>register.php">إنشاء حساب</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main class="main-content">
