<?php
/**
 * إدارة الطلبات - عرض وتغيير الحالة
 */
require_once 'includes/auth.php';
require_once '../config/database.php';

$pageTitle = 'إدارة الطلبات';
$message = '';
$messageType = '';

// تغيير حالة الطلب
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $orderId = (int)$_POST['order_id'];
    $status = trim($_POST['status']);
    $allowed = ['pending', 'preparing', 'ready', 'delivered', 'cancelled'];
    if ($orderId > 0 && in_array($status, $allowed)) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $orderId);
        if ($stmt->execute()) {
            $message = 'تم تحديث حالة الطلب';
            $messageType = 'success';
        }
    }
}

$orders = $conn->query("SELECT o.*, u.name as user_name, u.email FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC")->fetch_all(MYSQLI_ASSOC);

include 'includes/header.php';
?>

<section class="admin-section">
    <div class="admin-section-header">
        <h1 class="section-title">إدارة الطلبات</h1>
    </div>
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <table class="admin-table">
        <thead>
            <tr>
                <th>#</th>
                <th>العميل</th>
                <th>نوع الخدمة</th>
                <th>حجز الطاولة</th>
                <th>السعر</th>
                <th>الضريبة</th>
                <th>الإجمالي</th>
                <th>الحالة</th>
                <th>التاريخ</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo htmlspecialchars($order['user_name'] ?? 'ضيف'); ?></td>
                    <td><?php
                        $st = $order['service_type'] ?? 'takeout';
                        echo $st === 'table_reservation' ? 'حجز طاولة' : 'طلب عادي';
                    ?></td>
                    <td class="admin-reservation-cell"><?php
                        if (($order['service_type'] ?? '') === 'table_reservation') {
                            $parts = [];
                            if (!empty($order['reservation_date'])) {
                                $parts[] = htmlspecialchars($order['reservation_date']);
                            }
                            if (!empty($order['reservation_time'])) {
                                $parts[] = htmlspecialchars(substr($order['reservation_time'], 0, 5));
                            }
                            if (!empty($order['guest_count'])) {
                                $parts[] = (int)$order['guest_count'] . ' ضيف';
                            }
                            echo $parts ? implode(' — ', $parts) : '—';
                            if (!empty($order['reservation_notes'])) {
                                $rawNotes = $order['reservation_notes'];
                                $shortNotes = function_exists('mb_substr')
                                    ? (mb_strlen($rawNotes, 'UTF-8') > 40 ? mb_substr($rawNotes, 0, 40, 'UTF-8') . '…' : $rawNotes)
                                    : (strlen($rawNotes) > 40 ? substr($rawNotes, 0, 40) . '…' : $rawNotes);
                                echo '<br><small title="' . htmlspecialchars($rawNotes) . '">' . htmlspecialchars($shortNotes) . '</small>';
                            }
                        } else {
                            echo '—';
                        }
                    ?></td>
                    <td><?php echo number_format($order['total_price'], 2); ?> ر.س</td>
                    <td><?php echo number_format($order['tax'], 2); ?> ر.س</td>
                    <td><?php echo number_format($order['final_price'], 2); ?> ر.س</td>
                    <td><?php echo htmlspecialchars($order['status']); ?></td>
                    <td><?php echo date('Y-m-d H:i', strtotime($order['created_at'])); ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <select name="status" onchange="this.form.submit()">
                                <option value="pending" <?php echo $order['status']==='pending'?'selected':''; ?>>قيد الانتظار</option>
                                <option value="preparing" <?php echo $order['status']==='preparing'?'selected':''; ?>>قيد التجهيز</option>
                                <option value="ready" <?php echo $order['status']==='ready'?'selected':''; ?>>جاهز</option>
                                <option value="delivered" <?php echo $order['status']==='delivered'?'selected':''; ?>>تم التوصيل</option>
                                <option value="cancelled" <?php echo $order['status']==='cancelled'?'selected':''; ?>>ملغي</option>
                            </select>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php
$footerPath = __DIR__ . '/includes/footer.php';
if (file_exists($footerPath)) include $footerPath;
else echo '</main></body></html>';
?>
