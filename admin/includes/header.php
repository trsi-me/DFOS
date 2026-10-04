<?php
/**
 * رأس لوحة التحكم
 */
require_once __DIR__ . '/../../config/asset_version.php';
$adminBase = '';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>لوحة التحكم</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo rawurlencode(ASSET_VERSION); ?>">
    <link rel="stylesheet" href="admin.css?v=<?php echo rawurlencode(ASSET_VERSION); ?>">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
    <header class="admin-header">
        <a href="index.php" class="admin-logo">
            <img src="../assets/images/Logo.jpeg" alt="DFOS" class="admin-logo-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
            <span class="admin-logo-text" style="display:none;">DFOS</span>
        </a>
        <a href="../index.php" class="admin-site-link">الموقع</a>
    </header>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <nav class="admin-nav">
                <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='index.php'?'active':''; ?>">لوحة التحكم</a>
                <a href="categories.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='categories.php'?'active':''; ?>">الفئات</a>
                <a href="foods.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='foods.php'?'active':''; ?>">الطعام</a>
                <a href="orders.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='orders.php'?'active':''; ?>">الطلبات</a>
                <a href="users.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='users.php'?'active':''; ?>">المستخدمين</a>
                <a href="logout.php" class="nav-logout">خروج</a>
            </nav>
        </aside>
        <main class="admin-main">
