<?php
/**
 * حذف صنف من السلة
 */
session_start();

$foodId = (int)($_GET['id'] ?? $_POST['food_id'] ?? 0);

if ($foodId > 0 && isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $foodId) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

header('Location: cart.php');
exit;
