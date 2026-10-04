<?php
/**
 * تحديث كمية صنف في السلة
 */
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php');
    exit;
}

$foodId = (int)($_POST['food_id'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? 0);

if ($foodId > 0 && isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $foodId) {
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$key]);
            } else {
                $_SESSION['cart'][$key]['quantity'] = $quantity;
            }
            break;
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

header('Location: cart.php');
exit;
