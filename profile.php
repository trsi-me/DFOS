<?php
/**
 * صفحة تعديل بيانات المستخدم
 */
session_start();
require_once 'config/database.php';

$pageTitle = 'حسابي';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$error = '';
$success = '';

$stmt = $conn->prepare("SELECT name, email, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($email)) {
        $error = 'الرجاء ملء الاسم والبريد الإلكتروني';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'البريد الإلكتروني غير صحيح';
    } else {
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $checkStmt->bind_param("si", $email, $userId);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows > 0) {
            $error = 'البريد الإلكتروني مسجل لمستخدم آخر';
        } else {
            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, password=? WHERE id=?");
                $stmt->bind_param("ssssi", $name, $email, $phone, $hashedPassword, $userId);
            } else {
                $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=? WHERE id=?");
                $stmt->bind_param("sssi", $name, $email, $phone, $userId);
            }
            if ($stmt->execute()) {
                $_SESSION['user_name'] = $name;
                $user = ['name' => $name, 'email' => $email, 'phone' => $phone];
                $success = 'تم تحديث البيانات بنجاح';
            } else {
                $error = 'حدث خطأ أثناء التحديث';
            }
        }
    }
}

include 'includes/header.php';
?>

<section class="form-section">
    <h1 class="section-title">تعديل بيانات الحساب</h1>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" class="auth-form">
        <div class="form-group">
            <label for="name">الاسم</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">البريد الإلكتروني</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="phone">رقم الهاتف</label>
            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="password">كلمة المرور الجديدة (اتركها فارغة للإبقاء على الحالية)</label>
            <input type="password" id="password" name="password">
        </div>
        <button type="submit" class="submit-btn">حفظ التغييرات</button>
    </form>
</section>

<?php include 'includes/footer.php'; ?>
