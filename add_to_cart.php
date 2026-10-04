<?php
/**
 * إضافة صنف إلى السلة
 */
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$foodId = isset($_POST['food_id']) ? (int)$_POST['food_id'] : (int)($_GET['id'] ?? 0);
$quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;

if ($foodId <= 0) {
    header('Location: index.php');
    exit;
}

$stmt = $conn->prepare("SELECT id, name, price FROM foods WHERE id = ?");
$stmt->bind_param("i", $foodId);
$stmt->execute();
$food = $stmt->get_result()->fetch_assoc();

if ($food) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $foodId) {
            $item['quantity'] += $quantity;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $_SESSION['cart'][] = [
            'id' => $food['id'],
            'name' => $food['name'],
            'price' => (float)$food['price'],
            'quantity' => $quantity
        ];
    }
}

$redirect = $_GET['redirect'] ?? 'index.php';
header('Location: ' . $redirect);
exit;
