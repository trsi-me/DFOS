<?php
/**
 * تسجيل خروج المدير
 */
session_start();
unset($_SESSION['admin_id']);
unset($_SESSION['admin_name']);
header('Location: login.php');
exit;
