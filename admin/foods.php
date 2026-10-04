<?php
/**
 * إدارة الطعام - إضافة، تعديل، حذف
 */
require_once 'includes/auth.php';
require_once '../config/database.php';

$pageTitle = 'إدارة الطعام';
$message = '';
$messageType = '';

// حذف صنف
if (isset($_GET['delete']) && (int)$_GET['delete'] > 0) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM foods WHERE id = $id");
    $message = 'تم حذف الصنف';
    $messageType = 'success';
}

// إضافة صنف
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $image = trim($_POST['image'] ?? '');
    $calories = !empty($_POST['calories']) ? (int)$_POST['calories'] : null;
    $ingredients = trim($_POST['ingredients'] ?? '') ?: null;
    $allergens = trim($_POST['allergens'] ?? '') ?: null;
    if (!empty($name) && $price > 0 && $category_id > 0) {
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === 0) {
            $ext = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
            $image = 'food_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image_file']['tmp_name'], '../assets/images/' . $image);
        }
        $stmt = $conn->prepare("INSERT INTO foods (name, description, price, category_id, image, calories, ingredients, allergens) VALUES (?, ?, ?, ?, ?, NULLIF(?, ''), ?, ?)");
        $calStr = $calories !== null ? (string)$calories : '';
        $stmt->bind_param("ssdissss", $name, $description, $price, $category_id, $image, $calStr, $ingredients, $allergens);
        if ($stmt->execute()) {
            $message = 'تم إضافة الصنف';
            $messageType = 'success';
        }
    }
}

// تعديل صنف
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $image = trim($_POST['image'] ?? '');
    $calories = !empty($_POST['calories']) ? (int)$_POST['calories'] : null;
    $ingredients = trim($_POST['ingredients'] ?? '') ?: null;
    $allergens = trim($_POST['allergens'] ?? '') ?: null;
    if ($id > 0 && !empty($name) && $price > 0 && $category_id > 0) {
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === 0) {
            $ext = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
            $image = 'food_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image_file']['tmp_name'], '../assets/images/' . $image);
        }
        $stmt = $conn->prepare("UPDATE foods SET name=?, description=?, price=?, category_id=?, image=?, calories=NULLIF(?, ''), ingredients=?, allergens=? WHERE id=?");
        $calStr = $calories !== null ? (string)$calories : '';
        $stmt->bind_param("ssdissssi", $name, $description, $price, $category_id, $image, $calStr, $ingredients, $allergens, $id);
        if ($stmt->execute()) {
            $message = 'تم تحديث الصنف';
            $messageType = 'success';
        }
    }
}

$foods = $conn->query("SELECT f.*, c.name as category_name FROM foods f LEFT JOIN categories c ON f.category_id = c.id ORDER BY f.name")->fetch_all(MYSQLI_ASSOC);
$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);

include 'includes/header.php';
?>

<section class="admin-section">
    <div class="admin-section-header">
        <h1 class="section-title">إدارة الطعام</h1>
        <a href="#" class="btn-add" onclick="document.getElementById('addModal').classList.add('active'); return false;">إضافة صنف</a>
    </div>
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <table class="admin-table">
        <thead>
            <tr>
                <th>#</th>
                <th>الاسم</th>
                <th>الفئة</th>
                <th>السعر</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($foods as $food): ?>
                <tr>
                    <td><?php echo $food['id']; ?></td>
                    <td><?php echo htmlspecialchars($food['name']); ?></td>
                    <td><?php echo htmlspecialchars($food['category_name'] ?? '-'); ?></td>
                    <td><?php echo number_format($food['price'], 2); ?> ر.س</td>
                    <td>
                        <button class="btn-small btn-edit" onclick='editFood(<?php echo json_encode($food); ?>)'>تعديل</button>
                        <a href="foods.php?delete=<?php echo $food['id']; ?>" class="btn-small btn-delete" onclick="return confirm('حذف هذا الصنف؟')">حذف</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<div id="addModal" class="modal-overlay">
    <div class="modal-content modal-wide">
        <h2>إضافة صنف</h2>
        <form method="POST" enctype="multipart/form-data" class="modal-form">
            <input type="hidden" name="action" value="add">
            <div class="form-row">
                <div class="form-group">
                    <label>الاسم</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>الفئة</label>
                    <select name="category_id" required>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>السعر (ر.س)</label>
                    <input type="number" name="price" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label>السعرات الحرارية</label>
                    <input type="number" name="calories" min="0" placeholder="اختياري">
                </div>
            </div>
            <div class="form-group">
                <label>الوصف</label>
                <textarea name="description" rows="2"></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>صورة</label>
                    <input type="file" name="image_file" accept="image/*">
                </div>
                <div class="form-group">
                    <label>مسببات الحساسية</label>
                    <input type="text" name="allergens" placeholder="جلوتين، حليب">
                </div>
            </div>
            <div class="form-group">
                <label>المكونات</label>
                <textarea name="ingredients" rows="2" placeholder="سميد، سكر، عسل"></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="submit-btn">إضافة</button>
                <button type="button" class="submit-btn btn-cancel" onclick="document.getElementById('addModal').classList.remove('active')">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<div id="editModal" class="modal-overlay">
    <div class="modal-content modal-wide">
        <h2>تعديل صنف</h2>
        <form method="POST" enctype="multipart/form-data" class="modal-form">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="editId">
            <input type="hidden" name="image" id="editImage">
            <div class="form-row">
                <div class="form-group">
                    <label>الاسم</label>
                    <input type="text" name="name" id="editName" required>
                </div>
                <div class="form-group">
                    <label>الفئة</label>
                    <select name="category_id" id="editCategory" required>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>السعر (ر.س)</label>
                    <input type="number" name="price" id="editPrice" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label>السعرات الحرارية</label>
                    <input type="number" name="calories" id="editCalories" min="0" placeholder="اختياري">
                </div>
            </div>
            <div class="form-group">
                <label>الوصف</label>
                <textarea name="description" id="editDesc" rows="2"></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>صورة</label>
                    <input type="file" name="image_file" accept="image/*">
                </div>
                <div class="form-group">
                    <label>مسببات الحساسية</label>
                    <input type="text" name="allergens" id="editAllergens" placeholder="جلوتين، حليب">
                </div>
            </div>
            <div class="form-group">
                <label>المكونات</label>
                <textarea name="ingredients" id="editIngredients" rows="2" placeholder="سميد، سكر، عسل"></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="submit-btn">حفظ</button>
                <button type="button" class="submit-btn btn-cancel" onclick="document.getElementById('editModal').classList.remove('active')">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<script>
function editFood(food) {
    document.getElementById('editId').value = food.id;
    document.getElementById('editName').value = food.name;
    document.getElementById('editDesc').value = food.description || '';
    document.getElementById('editPrice').value = food.price;
    document.getElementById('editCategory').value = food.category_id;
    document.getElementById('editImage').value = food.image || '';
    document.getElementById('editCalories').value = food.calories || '';
    document.getElementById('editIngredients').value = food.ingredients || '';
    document.getElementById('editAllergens').value = food.allergens || '';
    document.getElementById('editModal').classList.add('active');
}
</script>

<?php
$footerPath = __DIR__ . '/includes/footer.php';
if (file_exists($footerPath)) include $footerPath;
else echo '</main></body></html>';
?>
