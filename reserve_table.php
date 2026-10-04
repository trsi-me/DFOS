<?php
/**
 * حجز طاولة طعام — صفحة مستقلة (بدون شرط إضافة أصناف للسلة)
 */
session_start();
require_once 'config/database.php';

$pageTitle = 'حجز طاولة طعام';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    $reservationDate = trim($_POST['reservation_date'] ?? '');
    $reservationTime = trim($_POST['reservation_time'] ?? '');
    $guestCount = isset($_POST['guest_count']) ? (int)$_POST['guest_count'] : 0;
    $reservationNotes = trim($_POST['reservation_notes'] ?? '');
    $contactName = trim($_POST['contact_name'] ?? '');
    $contactPhone = trim($_POST['contact_phone'] ?? '');

    if ($reservationDate === '' || $reservationTime === '') {
        $error = 'يرجى تحديد تاريخ ووقت الحجز.';
    } elseif ($guestCount < 1 || $guestCount > 50) {
        $error = 'عدد الضيوف يجب أن يكون بين 1 و 50.';
    } elseif ($contactPhone === '') {
        $error = 'يرجى إدخال رقم جوال للتواصل.';
    } else {
        $today = date('Y-m-d');
        if ($reservationDate < $today) {
            $error = 'تاريخ الحجز لا يمكن أن يكون في الماضي.';
        }
    }

    if ($error === '') {
        if ($contactName !== '' || $contactPhone !== '') {
            $contactLine = [];
            if ($contactName !== '') {
                $contactLine[] = 'الاسم: ' . $contactName;
            }
            if ($contactPhone !== '') {
                $contactLine[] = 'الجوال: ' . $contactPhone;
            }
            $reservationNotes = ($reservationNotes !== '' ? $reservationNotes . "\n" : '') . implode(' — ', $contactLine);
        }
        if ($reservationNotes === '') {
            $reservationNotes = null;
        }

        $subtotal = 0.0;
        $tax = 0.0;
        $finalPrice = 0.0;
        $serviceType = 'table_reservation';
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare(
                "INSERT INTO orders (user_id, total_price, tax, final_price, status, service_type, reservation_date, reservation_time, guest_count, reservation_notes) VALUES (?, ?, ?, ?, 'pending', ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param(
                'idddsssis',
                $userId,
                $subtotal,
                $tax,
                $finalPrice,
                $serviceType,
                $reservationDate,
                $reservationTime,
                $guestCount,
                $reservationNotes
            );
            $stmt->execute();
            $orderId = $conn->insert_id;
            $conn->commit();
            $_SESSION['last_order_id'] = $orderId;
            header('Location: order_success.php');
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            $error = 'تعذر حفظ الحجز. تأكد من تشغيل ترحيل قاعدة البيانات (ملف database_migration.sql) أو تواصل مع الإدارة.';
        }
    }
}

$formDate = htmlspecialchars($_POST['reservation_date'] ?? '');
$formTime = htmlspecialchars($_POST['reservation_time'] ?? '');
$formGuests = isset($_POST['guest_count']) ? (int)$_POST['guest_count'] : 4;
if ($formGuests < 1) {
    $formGuests = 4;
}
$formNotes = htmlspecialchars($_POST['reservation_notes'] ?? '');
$formName = htmlspecialchars($_POST['contact_name'] ?? '');
$formPhone = htmlspecialchars($_POST['contact_phone'] ?? '');

include 'includes/header.php';
?>

<section class="checkout-section reserve-table-page">
    <h1 class="section-title">حجز طاولة طعام</h1>
    <p class="reserve-intro">احجز موعدك في المطعم. لا تحتاج لإضافة أصناف للسلة — يمكنك الطلب لاحقاً أو من القائمة عند الحضور.</p>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="checkout-details">
        <form method="POST" class="checkout-form" id="reserve-form">
            <div class="reservation-fields reservation-fields-visible">
                <div class="form-group">
                    <label for="reservation_date">تاريخ الحجز <span class="req">*</span></label>
                    <input type="date" id="reservation_date" name="reservation_date" required min="<?php echo date('Y-m-d'); ?>" value="<?php echo $formDate; ?>">
                </div>
                <div class="form-group">
                    <label for="reservation_time">وقت الحجز <span class="req">*</span></label>
                    <input type="time" id="reservation_time" name="reservation_time" required value="<?php echo $formTime; ?>">
                </div>
                <div class="form-group">
                    <label for="guest_count">عدد الضيوف <span class="req">*</span></label>
                    <input type="number" id="guest_count" name="guest_count" min="1" max="50" required value="<?php echo $formGuests; ?>">
                </div>
                <div class="form-group">
                    <label for="contact_name">الاسم</label>
                    <input type="text" id="contact_name" name="contact_name" autocomplete="name" value="<?php echo $formName; ?>" placeholder="اسمك">
                </div>
                <div class="form-group">
                    <label for="contact_phone">رقم الجوال للتواصل <span class="req">*</span></label>
                    <input type="tel" id="contact_phone" name="contact_phone" required autocomplete="tel" value="<?php echo $formPhone; ?>" placeholder="05xxxxxxxx">
                </div>
                <div class="form-group">
                    <label for="reservation_notes">ملاحظات (اختياري)</label>
                    <textarea id="reservation_notes" name="reservation_notes" rows="3" placeholder="مناسبة، طلبات خاصة، مكان مفضل للجلوس"><?php echo $formNotes; ?></textarea>
                </div>
            </div>

            <button type="submit" class="submit-btn submit-btn-reserve">تأكيد حجز الطاولة</button>
        </form>
        <p class="reserve-alt"><a href="index.php">العودة للقائمة</a> · <a href="cart.php">السلة وإتمام طلب مع حجز</a></p>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
