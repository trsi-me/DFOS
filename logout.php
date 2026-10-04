<?php
/**
 * تسجيل الخروج - إنهاء الجلسة
 */
session_start();
session_destroy();
header('Location: index.php');
exit;
