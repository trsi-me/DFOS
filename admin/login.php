<?php
/**
 * تسجيل دخول المدير
 */
session_start();
if (isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}
require_once '../config/database.php';
require_once '../config/asset_version.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'الرجاء إدخال البريد وكلمة المرور';
    } else {
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ? AND is_admin = 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $error = 'بيانات الدخول غير صحيحة';
        } else {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_name'] = $user['name'];
                header('Location: index.php');
                exit;
            } else {
                $error = 'بيانات الدخول غير صحيحة';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل دخول المدير</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo rawurlencode(ASSET_VERSION); ?>">
    <link rel="stylesheet" href="admin.css?v=<?php echo rawurlencode(ASSET_VERSION); ?>">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
    <div class="form-section admin-login-form">
        <h1 class="section-title">تسجيل دخول المدير</h1>
        <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <form method="POST" class="auth-form">
            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">كلمة المرور</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="submit-btn">دخول</button>
        </form>
        <p class="form-link"><a href="../index.php">العودة للموقع</a></p>
    </div>
</body>
</html>
