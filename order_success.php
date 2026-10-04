<?php
/**
 * صفحة نجاح الطلب - عرض بعد إتمام الطلب
 */
session_start();
require_once 'config/database.php';

$pageTitle = 'تم الطلب بنجاح';

if (!isset($_SESSION['last_order_id'])) {
    header('Location: index.php');
    exit;
}

$orderId = (int)$_SESSION['last_order_id'];
unset($_SESSION['last_order_id']);

$orderExtra = null;
$stmt = $conn->prepare(
    "SELECT service_type, reservation_date, reservation_time, guest_count, reservation_notes, final_price FROM orders WHERE id = ?"
);
$stmt->bind_param('i', $orderId);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
if ($row) {
    $orderExtra = $row;
}

include 'includes/header.php';
?>

<section class="success-section">
    <h1 class="section-title">تم إرسال طلبك بنجاح</h1>
    <p class="success-message">رقم الطلب: <strong><?php echo $orderId; ?></strong></p>
    <?php if ($orderExtra && ($orderExtra['service_type'] ?? '') === 'table_reservation'): ?>
        <?php
        $fp = isset($orderExtra['final_price']) ? (float)$orderExtra['final_price'] : 0;
        $tableOnly = $fp <= 0.00001;
        ?>
        <div class="success-reservation-box">
            <p class="success-info"><strong>تم تسجيل حجز الطاولة</strong></p>
            <ul class="success-reservation-details">
                <li>التاريخ: <?php echo htmlspecialchars($orderExtra['reservation_date'] ?? ''); ?></li>
                <li>الوقت: <?php echo htmlspecialchars(substr($orderExtra['reservation_time'] ?? '', 0, 5)); ?></li>
                <li>عدد الضيوف: <?php echo (int)($orderExtra['guest_count'] ?? 0); ?></li>
                <?php if (!empty($orderExtra['reservation_notes'])): ?>
                    <li>ملاحظات: <?php echo nl2br(htmlspecialchars($orderExtra['reservation_notes'])); ?></li>
                <?php endif; ?>
            </ul>
        </div>
        <?php if ($tableOnly): ?>
            <p class="success-info">سنؤكد الحجز حسب التوفر. يمكنك تصفح القائمة والطلب لاحقاً أو عند الحضور.</p>
        <?php else: ?>
            <p class="success-info">سنؤكد الحجز حسب التوفر. سيتم تجهيز طلبك في الموعد المحدد قدر الإمكان.</p>
        <?php endif; ?>
    <?php else: ?>
        <p class="success-info">سيتم تجهيز طلبك قريباً.</p>
    <?php endif; ?>
    <a href="index.php" class="submit-btn">العودة للرئيسية</a>
</section>

<?php include 'includes/footer.php'; ?>
