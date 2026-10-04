<?php
/**
 * صفحة سلة المشتريات - عرض وتعديل وحذف الأصناف
 */
session_start();
$pageTitle = 'السلة';
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
include 'includes/header.php';
?>

<section class="cart-section">
    <h1 class="section-title">سلة المشتريات</h1>
    
    <?php if (empty($cart)): ?>
        <div class="cart-empty">
            <p>السلة فارغة. <a href="index.php">تصفح القائمة</a></p>
        </div>
    <?php else: ?>
        <div class="cart-items">
            <?php foreach ($cart as $item): ?>
                <div class="cart-item">
                    <div class="cart-item-info">
                        <h3 class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p class="cart-item-price"><?php echo number_format($item['price'], 2); ?> ر.س</p>
                    </div>
                    <form method="POST" action="update_cart.php" class="cart-item-form">
                        <input type="hidden" name="food_id" value="<?php echo $item['id']; ?>">
                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input">
                        <button type="submit" class="update-btn">تحديث</button>
                    </form>
                    <form method="POST" action="remove_from_cart.php" class="remove-form">
                        <input type="hidden" name="food_id" value="<?php echo $item['id']; ?>">
                        <button type="submit" class="remove-btn">حذف</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="cart-reserve-hint">
            <strong>حجز طاولة؟</strong> من صفحة <a href="reserve_table.php">حجز طاولة طعام</a> مباشرة، أو أضف أصنافاً ثم في «إتمام الطلب» اختر «حجز طاولة مع الطلب».
        </div>
        <div class="cart-summary">
            <p class="cart-total">المجموع: <?php echo number_format($subtotal, 2); ?> ر.س</p>
            <a href="checkout.php" class="checkout-btn">إتمام الطلب</a>
        </div>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
