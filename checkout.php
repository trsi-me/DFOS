<?php
/**
 * صفحة إتمام الطلب - تفاصيل الطلب مع السعر والضريبة والإجمالي
 * يتضمن خيار حجز طاولة طعام في المطعم
 */
session_start();
require_once 'config/database.php';

$pageTitle = 'إتمام الطلب';
$TAX_RATE = 0.15;

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
if (empty($cart)) {
    header('Location: cart.php');
    exit;
}

$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$tax = $subtotal * $TAX_RATE;
$finalPrice = $subtotal + $tax;

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    $serviceType = isset($_POST['service_type']) ? trim($_POST['service_type']) : 'takeout';
    if (!in_array($serviceType, ['takeout', 'table_reservation'], true)) {
        $serviceType = 'takeout';
    }

    $reservationDate = null;
    $reservationTime = null;
    $guestCount = null;
    $reservationNotes = null;

    if ($serviceType === 'table_reservation') {
        $reservationDate = trim($_POST['reservation_date'] ?? '');
        $reservationTime = trim($_POST['reservation_time'] ?? '');
        $guestCount = isset($_POST['guest_count']) ? (int)$_POST['guest_count'] : 0;
        $reservationNotes = trim($_POST['reservation_notes'] ?? '');
        if ($reservationNotes === '') {
            $reservationNotes = null;
        }

        if ($reservationDate === '' || $reservationTime === '') {
            $error = 'يرجى تحديد تاريخ ووقت الحجز عند اختيار حجز طاولة.';
        } elseif ($guestCount < 1 || $guestCount > 50) {
            $error = 'عدد الضيوف يجب أن يكون بين 1 و 50.';
        } else {
            $today = date('Y-m-d');
            if ($reservationDate < $today) {
                $error = 'تاريخ الحجز لا يمكن أن يكون في الماضي.';
            }
        }
    }

    if ($error === '') {
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

            $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, food_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($cart as $item) {
                $itemStmt->bind_param('iiid', $orderId, $item['id'], $item['quantity'], $item['price']);
                $itemStmt->execute();
            }

            $conn->commit();
            unset($_SESSION['cart']);
            $_SESSION['last_order_id'] = $orderId;
            header('Location: order_success.php');
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            $error = 'حدث خطأ أثناء إتمام الطلب. حاول مرة أخرى.';
        }
    }
}

$formService = ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['service_type']))
    ? $_POST['service_type'] : 'takeout';
if (!in_array($formService, ['takeout', 'table_reservation'], true)) {
    $formService = 'takeout';
}
$formDate = htmlspecialchars($_POST['reservation_date'] ?? '');
$formTime = htmlspecialchars($_POST['reservation_time'] ?? '');
$formGuests = isset($_POST['guest_count']) ? (int)$_POST['guest_count'] : 2;
if ($formGuests < 1) {
    $formGuests = 2;
}
$formNotes = htmlspecialchars($_POST['reservation_notes'] ?? '');

include 'includes/header.php';
?>

<section class="checkout-section">
    <h1 class="section-title">إتمام الطلب</h1>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="checkout-details">
        <h2>تفاصيل الطلب</h2>
        <ul class="order-items-list">
            <?php foreach ($cart as $item): ?>
                <li>
                    <span><?php echo htmlspecialchars($item['name']); ?></span>
                    <span><?php echo $item['quantity']; ?> x <?php echo number_format($item['price'], 2); ?> ر.س</span>
                    <span><?php echo number_format($item['price'] * $item['quantity'], 2); ?> ر.س</span>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="order-summary">
            <p><strong>السعر:</strong> <?php echo number_format($subtotal, 2); ?> ر.س</p>
            <p><strong>الضريبة (15%):</strong> <?php echo number_format($tax, 2); ?> ر.س</p>
            <p class="final-price"><strong>الإجمالي:</strong> <?php echo number_format($finalPrice, 2); ?> ر.س</p>
        </div>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <p class="guest-notice">أنت تطلب كضيف. يمكنك <a href="register.php">إنشاء حساب</a> لتتبع طلباتك.</p>
        <?php endif; ?>

        <p class="checkout-reserve-hint">تريد حجز طاولة فقط بدون طلب أطعمة الآن؟ استخدم صفحة <a href="reserve_table.php"><strong>حجز طاولة طعام</strong></a>.</p>

        <form method="POST" class="checkout-form" id="checkout-form">
            <fieldset class="service-type-fieldset">
                <legend class="service-type-legend">نوع الخدمة — اختر أحد الخيارين</legend>
                <label class="service-option">
                    <input type="radio" name="service_type" value="takeout" <?php echo $formService === 'takeout' ? 'checked' : ''; ?>>
                    <span>طلب عادي (استلام من المطعم / بدون حجز طاولة)</span>
                </label>
                <label class="service-option">
                    <input type="radio" name="service_type" value="table_reservation" id="service-table" <?php echo $formService === 'table_reservation' ? 'checked' : ''; ?>>
                    <span>حجز طاولة طعام في المطعم مع هذا الطلب</span>
                </label>
            </fieldset>

            <div class="reservation-fields" id="reservation-fields" <?php echo $formService === 'takeout' ? 'hidden' : ''; ?>>
                <div class="form-group">
                    <label for="reservation_date">تاريخ الحجز</label>
                    <input type="date" id="reservation_date" name="reservation_date" min="<?php echo date('Y-m-d'); ?>" value="<?php echo $formDate; ?>">
                </div>
                <div class="form-group">
                    <label for="reservation_time">وقت الحجز</label>
                    <input type="time" id="reservation_time" name="reservation_time" value="<?php echo $formTime; ?>">
                </div>
                <div class="form-group">
                    <label for="guest_count">عدد الضيوف</label>
                    <input type="number" id="guest_count" name="guest_count" min="1" max="50" value="<?php echo $formGuests; ?>">
                </div>
                <div class="form-group">
                    <label for="reservation_notes">ملاحظات (اختياري)</label>
                    <textarea id="reservation_notes" name="reservation_notes" rows="2" placeholder="مثال: مناسبة عيد ميلاد، طاولة بجانب النافذة"><?php echo $formNotes; ?></textarea>
                </div>
            </div>

            <button type="submit" class="submit-btn">تأكيد الطلب</button>
        </form>
        <a href="cart.php" class="back-link">العودة للسلة</a>
    </div>
</section>

<script>
(function () {
    var tableRadio = document.getElementById('service-table');
    var fields = document.getElementById('reservation-fields');
    var form = document.getElementById('checkout-form');
    if (!tableRadio || !fields || !form) return;
    function sync() {
        var show = tableRadio.checked;
        fields.hidden = !show;
        fields.querySelectorAll('input, textarea').forEach(function (el) {
            if (el.name === 'reservation_notes') return;
            el.required = show;
        });
        if (show && !fields.querySelector('#reservation_date').value) {
            fields.querySelector('#reservation_date').value = new Date().toISOString().slice(0, 10);
        }
    }
    form.querySelectorAll('input[name="service_type"]').forEach(function (r) {
        r.addEventListener('change', sync);
    });
    sync();
})();
</script>

<?php include 'includes/footer.php'; ?>
