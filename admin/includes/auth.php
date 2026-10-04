<?php
/**
 * فحص صلاحية المدير - للوحة التحكم
 */
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
