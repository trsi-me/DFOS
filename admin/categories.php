<?php
/**
 * إدارة الفئات - إضافة، تعديل، حذف
 */
require_once 'includes/auth.php';
require_once '../config/database.php';

$pageTitle = 'إدارة الفئات';
$message = '';
$messageType = '';

// حذف فئة
if (isset($_GET['delete']) && (int)$_GET['delete'] > 0) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM categories WHERE id = $id");
    $message = 'تم حذف الفئة';
    $messageType = 'success';
}

// إضافة فئة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = trim($_POST['name'] ?? '');
    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            $message = 'تم إضافة الفئة';
            $messageType = 'success';
        }
    }
}

// تعديل فئة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    if ($id > 0 && !empty($name)) {
        $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $id);
        if ($stmt->execute()) {
            $message = 'تم تحديث الفئة';
            $messageType = 'success';
        }
    }
}

$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);

include 'includes/header.php';
?>

<section class="admin-section">
    <div class="admin-section-header">
        <h1 class="section-title">إدارة الفئات</h1>
        <a href="#" class="btn-add" onclick="document.getElementById('addModal').classList.add('active'); return false;">إضافة فئة</a>
    </div>
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <table class="admin-table">
        <thead>
            <tr>
                <th>#</th>
                <th>الاسم</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?php echo $cat['id']; ?></td>
                    <td><?php echo htmlspecialchars($cat['name']); ?></td>
                    <td>
                        <button class="btn-small btn-edit" onclick="editCategory(<?php echo $cat['id']; ?>, '<?php echo htmlspecialchars(addslashes($cat['name'])); ?>')">تعديل</button>
                        <a href="categories.php?delete=<?php echo $cat['id']; ?>" class="btn-small btn-delete" onclick="return confirm('حذف هذه الفئة؟')">حذف</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<div id="addModal" class="modal-overlay">
    <div class="modal-content">
        <h2>إضافة فئة</h2>
        <form method="POST" class="modal-form">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label>اسم الفئة</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="submit-btn">إضافة</button>
                <button type="button" class="submit-btn btn-cancel" onclick="document.getElementById('addModal').classList.remove('active')">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<div id="editModal" class="modal-overlay">
    <div class="modal-content">
        <h2>تعديل فئة</h2>
        <form method="POST" class="modal-form">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="editId">
            <div class="form-group">
                <label>اسم الفئة</label>
                <input type="text" name="name" id="editName" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="submit-btn">حفظ</button>
                <button type="button" class="submit-btn btn-cancel" onclick="document.getElementById('editModal').classList.remove('active')">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<script>
function editCategory(id, name) {
    document.getElementById('editId').value = id;
    document.getElementById('editName').value = name;
    document.getElementById('editModal').classList.add('active');
}
</script>

<?php
$footerPath = __DIR__ . '/includes/footer.php';
if (file_exists($footerPath)) include $footerPath;
else echo '</main></body></html>';
?>
