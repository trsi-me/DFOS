<?php
/**
 * ملف إعداد الاتصال بقاعدة البيانات
 * DFOS - Digital Food Ordering System
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'dfos');
define('DB_USER', 'root');
define('DB_PASS', '');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die('فشل الاتصال بقاعدة البيانات: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');
