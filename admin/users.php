<?php
/**
 * إدارة المستخدمين - عرض المستخدمين
 */
require_once 'includes/auth.php';
require_once '../config/database.php';

$pageTitle = 'إدارة المستخدمين';

$users = $conn->query("SELECT id, name, email, phone, created_at FROM users WHERE is_admin = 0 ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

include 'includes/header.php';
?>

<section class="admin-section">
    <div class="admin-section-header">
        <h1 class="section-title">إدارة المستخدمين</h1>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>#</th>
                <th>الاسم</th>
                <th>البريد</th>
                <th>الهاتف</th>
                <th>تاريخ التسجيل</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
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
