<?php
/**
 * صفحة تسجيل الدخول
 */
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
require_once 'config/database.php';

$pageTitle = 'تسجيل الدخول';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'الرجاء إدخال البريد وكلمة المرور';
    } else {
        $stmt = $conn->prepare("SELECT id, name, password, is_admin FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $error = 'البريد أو كلمة المرور غير صحيحة';
        } else {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                if (!empty($user['is_admin'])) {
                    $_SESSION['admin_id'] = $user['id'];
                }
                header('Location: index.php');
                exit;
            } else {
                $error = 'البريد أو كلمة المرور غير صحيحة';
            }
        }
    }
}

include 'includes/header.php';
?>

<section class="form-section">
    <h1 class="section-title">تسجيل الدخول</h1>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" class="auth-form">
        <div class="form-group">
            <label for="email">البريد الإلكتروني</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="password">كلمة المرور</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="submit-btn">دخول</button>
    </form>
    <p class="form-link">ليس لديك حساب؟ <a href="register.php">إنشاء حساب</a></p>
</section>

<?php include 'includes/footer.php'; ?>
