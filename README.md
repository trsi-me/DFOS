# DFOS

الاسم في التعليقات: Digital Food Ordering System. الواجهة تستخدم DFOS. مجلد المشروع: `DFOS`.

تناقض اسم القاعدة:

| المصدر | الاسم |
| --- | --- |
| `database.sql` | `CREATE DATABASE` ثم `USE dfos` |
| `config/database.php` الثابت `DB_NAME` | `dfos` |
| README السابق | إنشاء قاعدة باسم `dfos_db` |

الاتصال في الكود يفتح `dfos`.

## 1. ما هو المشروع

موقع PHP لعرض أصناف طعام من جداول `categories` و`foods`، وسلة في `$_SESSION['cart']`، وإتمام طلب مع ضريبة، مع خيار استلام أو حجز طاولة، وصفحة حجز مستقلة `reserve_table.php`. الطلب يعمل بحساب أو كضيف (`user_id` يقبل NULL). لوحة `admin/` تدير الفئات والأصناف والطلبات والمستخدمين.

## 2. لماذا يوجد هذا المشروع

README السابق: طلب الطعام من قائمة رقمية مع حساب أو بدونه. الكود يطابق ذلك: `checkout.php` يأخذ `user_id` من الجلسة أو `null`، و`includes/auth_check.php` يعلّق أن الطلب بدون حساب مسموح.

## 3. من يستخدمه

| الطرف | الشرط |
| --- | --- |
| زائر أو ضيف | تصفح `index.php` والسلة والدفع وحجز الطاولة بلا `user_id` |
| مستخدم | `users.is_admin = 0` بعد `register.php` / `login.php`. يصل إلى `profile.php` |
| مدير | `users.is_admin = 1`. الدخول من `admin/login.php` الذي يشترط `is_admin = 1` |

## 4. ماذا يستطيع النظام أن يفعل

- عرض الأطعمة في `index.php` مع تصفية `?category=`.
- تفاصيل `product_detail.php`.
- إضافة `add_to_cart.php` وتحديث `update_cart.php` وحذف `remove_from_cart.php`.
- سلة `cart.php` ثم `checkout.php`.
- الضريبة في `checkout.php`: `$TAX_RATE = 0.15` والنص في الصفحة «الضريبة (15%)».
- `service_type`: `takeout` أو `table_reservation`.
- حجز طاولة بلا أصناف من `reserve_table.php` (ضريبة `0.0` في المقطع المقروء).
- صفحة `order_success.php` تعرض تفاصيل الحجز عندما `service_type` يساوي `table_reservation`.
- تسجيل ودخول وخروج وملف شخصي و`about.php`.
- إدارة: `admin/index.php`، `admin/categories.php`، `admin/foods.php` (إضافة وتعديل وحذف ورفع صورة)، `admin/orders.php` (حالات الطلب)، `admin/users.php`.

## 5. كيف يعمل النظام

```
المتصفح
   |
   v
صفحات الجذر (جلسة PHP)
   |
   v
config/database.php   mysqli  ->  قاعدة dfos
   |
   +-- foods / categories
   +-- $_SESSION['cart']
   +-- orders / order_items
```

الإدارة تستخدم `admin/includes/auth.php` ثم نفس `config/database.php`.

## 6. أمثلة واقعية

1. زائر يضيف برجر من القائمة. `add_to_cart.php` يقرأ السعر من `foods` ويضع العنصر في `$_SESSION['cart']`. إن تكرر `id` تُزاد `quantity`.
2. في `checkout.php` السلة غير فارغة. المجموع الفرعي من أسعار الجلسة. الضريبة 15 بالمئة. اختيار `takeout` يدرج طلباً بحالة `pending` ثم صفوف `order_items`.
3. اختيار `table_reservation` يتطلب `reservation_date` و`reservation_time`. التاريخ الأقدم من اليوم يُرفض. تُحفظ `guest_count` و`reservation_notes`.
4. `reserve_table.php` ينشئ طلباً من نوع `table_reservation` حتى بدون سلة أصناف، ويضيف سطر تواصل إلى الملاحظات عند الحاجة.
5. المدير في `admin/orders.php` يغيّر `status` إلى واحدة من: `pending`, `preparing`, `ready`, `delivered`, `cancelled`.

## 7. رحلة المستخدم

**ضيف**

1. `index.php` ثم إضافة للسلة.
2. `cart.php` ثم `checkout.php`.
3. اختيار نوع الخدمة وتأكيد الطلب.
4. `order_success.php`.
5. أو `reserve_table.php` لحجز طاولة.

**صاحب حساب**

1. `register.php` ثم `login.php`.
2. نفس مسار الطلب، ويُحفظ `orders.user_id`.
3. `profile.php` لتعديل الاسم والبريد والجوال وكلمة المرور.
4. `logout.php`.

**مدير**

1. `admin/login.php`.
2. الأصناف والفئات والطلبات والمستخدمين.
3. `admin/logout.php`.

## 8. الوحدات والأقسام

| الوحدة | الملفات |
| --- | --- |
| قائمة وطلب | `index.php`, `product_detail.php`, `cart.php`, `add_to_cart.php`, `update_cart.php`, `remove_from_cart.php`, `checkout.php`, `order_success.php`, `reserve_table.php` |
| حساب | `login.php`, `register.php`, `logout.php`, `profile.php`, `about.php` |
| تخطيط | `includes/header.php`, `includes/footer.php`, `includes/auth_check.php` |
| إدارة | `admin/index.php`, `login.php`, `logout.php`, `categories.php`, `foods.php`, `orders.php`, `users.php`, `admin.css`, `includes/auth.php`, `includes/header.php`, `includes/footer.php` |
| إعداد | `config/database.php`, `config/asset_version.php` |
| بيانات | `database.sql` |
| أصول | `assets/css/style.css`, `assets/js/main.js` |

## 9. الشركات والكيانات

اسم مطعم قانوني في الملفات: غير موثق. العملة في واجهة الدفع تُعرض «ر.س» في `checkout.php`. أصناف البذرة عربية (عصائر، قهوة، برجر، بيتزا، كنافة، بسبوسة).

## 10. الصلاحيات

| الإجراء | الشرط |
| --- | --- |
| القائمة والسلة والدفع والحجز | بلا دخول. `auth_check.php` لا يمنع الضيف |
| `profile.php` | `$_SESSION['user_id']` وإلا `login.php` |
| `admin/login.php` | بريد موجود و`is_admin = 1` و`password_verify` |
| صفحات `admin/` التي تضم `includes/auth.php` | جلسة مدير |

قيم `is_admin`: `0` مستخدم، `1` مدير، حسب `database.sql`.

## 11. الأتمتة وسير العمل

مجدول: غير موجود.

حالات الطلب يغيرها المدير يدوياً من `admin/orders.php`. انتقال تلقائي بين الحالات: غير موجود.

سير الدفع:

1. سلة جلسة غير فارغة وإلا تحويل إلى `cart.php`.
2. حساب `tax` و`final_price`.
3. التحقق من نوع الخدمة وتاريخ الحجز.
4. `INSERT` في `orders` بحالة `pending`.
5. `INSERT` في `order_items` لكل عنصر.

سير الحجز المستقل في `reserve_table.php` يدرج `orders` بضريبة صفر وقد يدمج بيانات التواصل في `reservation_notes`.

## 12. التكامل بين الوحدات

`index.php` يجلب `categories` و`foods`. صورة بعض الأصناف تُستبدل في مصفوفة `$specificFoodImages` داخل `index.php` (تعليق: صور محددة للأصناف). `product_detail.php` يعرض الصنف. السلة تخزن `id` و`name` و`price` و`quantity` في الجلسة بعد قراءة السعر من القاعدة عند الإضافة.

`admin/orders.php` يعرض `service_type` وبيانات الحجز واسم المستخدم أو النص «ضيف» عندما `user_name` فارغ بسبب `LEFT JOIN` و`user_id` الفارغ.

حذف صنف من `admin/foods.php` يحذف من `foods`، و`order_items.food_id` له `ON DELETE CASCADE` في SQL.

## 13. المصطلحات

| المصطلح | المعنى |
| --- | --- |
| `takeout` | طلب عادي في واجهة الإدارة «طلب عادي» |
| `table_reservation` | حجز طاولة |
| `pending` | الحالة الابتدائية عند الإدراج |
| `preparing`, `ready`, `delivered`, `cancelled` | الحالات المسموحة في `admin/orders.php` |
| `$TAX_RATE` | `0.15` في `checkout.php` |
| ضيف | طلب بلا `user_id` |
| `is_admin` | `1` مدير |

## 14. الأسئلة الشائعة

**هل يلزم حساب لإتمام الطلب؟** الكود يسمح بـ `user_id` فارغ. README السابق يذكر الطلب كضيف.

**أين السلة؟** في جلسة PHP `$_SESSION['cart']` وليست جدولاً.

**كم الضريبة؟** 15 بالمئة من المجموع في `checkout.php`. في `reserve_table.php` المتغير `$tax` يُضبط إلى `0.0` في المسار المقروء.

**ما حالات الطلب؟** الخمس المذكورة في القسم 13. قيم أخرى يرفضها الفحص `in_array`.

**ما اسم القاعدة؟** الملفات التنفيذية تقول `dfos`. README السابق قال `dfos_db`.

## 15. المعمارية

```
+------------------+     +---------------------------+     +------------------+
| المتصفح          |     | PHP + Session cart        |     | MySQL            |
| style.css main.js| --> | index cart checkout       | --> | dfos             |
|                  | <-- | reserve_table order_success |   | users categories |
| admin.css        | --> | admin/* + auth.php        | --> | foods orders     |
+------------------+     | config/database.php mysqli|     | order_items      |
                         +---------------------------+     +------------------+
```

## 16. التقنيات المستخدمة

| التقنية | أين |
| --- | --- |
| PHP | الصفحات |
| MySQLi (`new mysqli`) | `config/database.php` |
| `bind_param` في الصفحات التي أُعدت فيها العبارات | مثل `add_to_cart.php` و`checkout.php` و`admin/orders.php` |
| HTML و CSS | `assets/css/style.css` و`admin/admin.css` |
| JavaScript | `assets/js/main.js` وسكربت داخل `checkout.php` لإظهار حقول الحجز |
| `password_hash` / `password_verify` | التسجيل والدخول |

PDO: غير مستخدم في `config/database.php`. Composer: غير موجود.

## 17. هيكل المشروع

```
DFOS/
├── index.php
├── product_detail.php
├── about.php
├── login.php
├── register.php
├── logout.php
├── profile.php
├── cart.php
├── add_to_cart.php
├── update_cart.php
├── remove_from_cart.php
├── checkout.php
├── order_success.php
├── reserve_table.php
├── database.sql
├── config/database.php
├── config/asset_version.php
├── includes/
├── admin/
└── assets/css/style.css
    assets/js/main.js
    assets/fonts/OFL.txt
```

README السابق يذكر وضع الشعار في `assets/images/logo.jpg` وإن غاب يظهر نص DFOS. صور أصناف في البذرة مثل `arabic_coffee.jpg` و`kunafa.jpg` و`basbousa.jpg` و`green_tea.jpg`. ظهور هذه الملفات في قائمة الملفات الحالية: غير ظاهر.

## 18. واجهة المستخدم

صفحات عربية. الهيدر من `includes/header.php`. لوحة الإدارة من `admin/includes/header.php` و`admin.css`. حقول الحجز `.reservation-fields` تُخفى عندما تكون الخدمة `takeout` عبر سكربت في `checkout.php`. favicon: غير موجود في الملفات التي فُحصت. `config/asset_version.php` موجود لمعامل أصول (القيمة تُقرأ من ذلك الملف عند الربط في الهيدر).

## 19. الخادم

README السابق: XAMPP، Apache وMySQL، والمسار `http://localhost/DFOS/` إذا كان المجلد داخل `htdocs`، ولوحة `http://localhost/DFOS/admin/`. رابط XAMPP المذكور سابقاً: https://www.apachefriends.org/download.html. منفذ في الكود: غير موثق.

## 20. مسار الطلب

1. `session_start()` في صفحات الواجهة.
2. `require config/database.php` فينشأ `$conn`.
3. عند فشل الاتصال يتوقف التنفيذ بنص «فشل الاتصال بقاعدة البيانات» و`$conn->connect_error`.
4. `add_to_cart.php` يقبل POST بحقل `food_id` أو GET بمعامل `id`.
5. `checkout.php` عند POST يكتب الطلب.
6. الإدارة تتحقق من الجلسة ثم تنفذ استعلامات `mysqli`.

## 21. قاعدة البيانات

### users

PK `id`. `name`, `email` UNIQUE, `password`, `phone`, `is_admin` TINYINT افتراضي 0, `created_at`.

### categories

PK `id`. `name`, `created_at`.

### foods

PK `id`. `name`, `description`, `price` DECIMAL(10,2), `category_id` FK إلى `categories(id)` ON DELETE CASCADE, `image`, `calories`, `ingredients`, `allergens`, `created_at`.

### orders

PK `id`. `user_id` NULL وFK إلى `users(id)` ON DELETE SET NULL. `total_price`, `tax` افتراضي 0, `final_price`, `status` افتراضي `pending`, `service_type` افتراضي `takeout`, `reservation_date`, `reservation_time`, `guest_count`, `reservation_notes`, `created_at`.

### order_items

PK `id`. `order_id` FK CASCADE إلى `orders`. `food_id` FK CASCADE إلى `foods`. `quantity` افتراضي 1. `price`.

بذرة الفئات: مشروبات، وجبات رئيسية، مقبلات، حلويات. بذرة أطعمة بأسعار وسعرات ومكوّنات وحساسيات (مثل جلوتين وحليب). مدير البذرة: الاسم «مدير النظام»، البريد `admin@dfos.com`، `is_admin = 1`، وكلمة المرور تجزئة. النص مذكور في تعليق SQL وREADME السابق وغير مُعاد هنا.

## 22. واجهات البرمجة

REST مستقل: غير موجود.

| الطريقة | المسار | الغرض | مدخلات | صلاحية | استجابة |
| --- | --- | --- | --- | --- | --- |
| GET | `index.php` | قائمة | `category` اختياري | عامة | HTML |
| GET | `product_detail.php` | صنف | معرف الصنف | عامة | HTML |
| GET | `about.php` | عن النظام | | عامة | HTML |
| POST أو GET | `add_to_cart.php` | إضافة | `food_id` أو `id`, `quantity` | عامة | تحويل |
| طلب التحديث | `update_cart.php` | كمية | حسب النموذج | جلسة السلة | تحويل |
| طلب الحذف | `remove_from_cart.php` | إزالة صنف | معرف | جلسة السلة | تحويل |
| GET/POST | `cart.php` | عرض السلة | | عامة | HTML |
| POST | `checkout.php` | إنشاء طلب | `service_type`, حقول الحجز | سلة غير فارغة، الحساب اختياري | HTML أو تحويل نجاح |
| GET | `order_success.php` | نجاح | معرف الطلب حسب الصفحة | | HTML |
| POST | `reserve_table.php` | حجز | `reservation_date`, `reservation_time`, الضيوف، الملاحظات، تواصل | عامة | HTML |
| POST | `login.php` | دخول مستخدم | بريد وكلمة مرور | | HTML أو تحويل |
| POST | `register.php` | حساب | الاسم والبريد وكلمة المرور والجوال | | HTML |
| GET/POST | `profile.php` | تعديل | | `user_id` | HTML |
| GET | `logout.php` | خروج | | | تحويل |
| POST | `admin/login.php` | دخول مدير | بريد وكلمة مرور | `is_admin = 1` | تحويل |
| GET | `admin/logout.php` | خروج مدير | | | تحويل |
| GET | `admin/index.php` | لوحة | | مدير | HTML |
| GET/POST | `admin/categories.php` | فئات | | مدير | HTML |
| GET/POST | `admin/foods.php` | أصناف | حقول الصنف و`image_file` و`delete` | مدير | HTML |
| POST | `admin/orders.php` | حالة الطلب | `order_id`, `status` | مدير | HTML |
| GET/POST | `admin/users.php` | مستخدمون | | مدير | HTML |

## 23. تسجيل الدخول والصلاحيات

دخول المستخدم من `login.php` (جلسة واجهة المتجر). دخول المدير استعلام `WHERE email = ? AND is_admin = 1` في `admin/login.php`.

`profile.php` يرفض بريداً مستخدماً لحساب آخر. CSRF: غير موجود في الملفات المقروءة. خصائص كعكة الجلسة: غير موثقة.

`includes/auth_check.php` يعلّق أن الملف للصفحات التي تتطلب دخولاً، ومحتواه الحالي يفحص وجود الجلسة فقط ولا يحوّل إلى `login.php`. الصفحات التي تمنع الضيف، مثل `profile.php`، تنفذ الفحص بنفسها.

## 24. الحماية

- كثير من الكتابات تستخدم `prepare` و`bind_param` (الطلب، السلة، حالة الطلب، دخول المدير).
- كلمات المرور عبر `password_hash`.
- `htmlspecialchars` في عرض الأسماء والملاحظات في `admin/orders.php` و`order_success.php`.
- أنواع الخدمة والحالات مقيدة بـ `in_array`.
- تاريخ الحجز لا يقبل ما قبل اليوم.
- `admin/foods.php` الحذف: `DELETE FROM foods WHERE id = $id` بعد تحويل المعرّف إلى عدد صحيح.
- فشل الاتصال يطبع `$conn->connect_error`.
- `DB_USER` هو `root` و`DB_PASS` فارغ في `config/database.php`. ملف `.env`: غير موجود.
- رفع صورة الصنف يأخذ الامتداد من اسم الملف ويحفظ في `assets/images/` باسم `food_` مع `time()`. فحص MIME: غير موجود في المقطع المقروء.

## 25. الإعدادات

| الرمز | الملف | القيمة الحالية |
| --- | --- | --- |
| `DB_HOST` | `config/database.php` | `localhost` |
| `DB_NAME` | `config/database.php` | `dfos` |
| `DB_USER` | `config/database.php` | `root` |
| `DB_PASS` | `config/database.php` | فارغ في الملف |
| `$TAX_RATE` | `checkout.php` | `0.15` |
| الترميز | `set_charset('utf8mb4')` | `utf8mb4` |

## 26. التكاملات الخارجية

بوابة دفع أو خرائط أو بريد: غير موجودة في الملفات الحالية. README السابق يذكر خط IBM Plex Sans Arabic من Google Fonts في تعليق الهيكل. إن كان الرابط داخل `includes/header.php` فهو تحميل خط خارجي عند توفر الشبكة.

## 27. المهام المجدولة

Cron: غير موجود. تغيير حالة الطلب يدوي من المدير.

## 28. تخزين الملفات

صور الأصناف اسم ملف في `foods.image` تحت `assets/images/`. الرفع من `admin/foods.php` عبر `move_uploaded_file` إلى `../assets/images/`. الشعار المتوقع `assets/images/logo.jpg` حسب README السابق. السلة في الجلسة على الخادم وليست ملفاً.

## 29. السجلات والمتابعة

ملفات log: غير موجودة. رسائل الإدارة مثل «تم تحديث حالة الطلب» و«تم إضافة الصنف» و«تم حذف الصنف» تُعرض في الصفحة. خطأ الاتصال يُطبع مباشرة.

## 30. التثبيت

من README السابق مع تصحيح اسم القاعدة حسب SQL والكود:

1. تثبيت XAMPP وتشغيل Apache وMySQL.
2. نسخ المشروع إلى `C:\xampp\htdocs\DFOS` أو الإبقاء على `D:\VSCode\Projects\DFOS` إذا كان الخادم يشير إليه.
3. استيراد `database.sql`. الملف ينشئ قاعدة `dfos` ويختارها. README السابق طلب إنشاء `dfos_db` يدوياً. لمطابقة `DB_NAME` استخدم الاسم `dfos`.
4. راجع `config/database.php`.
5. افتح `http://localhost/DFOS/` والإدارة `http://localhost/DFOS/admin/`.
6. حساب المدير في البذرة بريده `admin@dfos.com`. كلمة المرور في تعليق SQL وREADME السابق وغير مكررة هنا.
7. ضع الشعار في `assets/images/logo.jpg` إن رغبت بظهوره كما ذكر README السابق.

أمر بديل مذكور سابقاً:

```
mysql -u root -p < database.sql
```

## 31. دليل التطوير

- الاتصال متغير `$conn` من نوع `mysqli` وليس PDO.
- السلة مصفوفة جلسة. تعديل السعر في الإدارة لا يغيّر عناصر السلة المفتوحة مسبقاً لأن السعر نُسخ عند `add_to_cart.php`.
- حالة جديدة للطلب تحتاج إضافتها إلى المصفوفة `$allowed` في `admin/orders.php` وإلا يرفضها الفحص.
- `includes/auth_check.php` لا يفرض تحويلاً بنفسه.
- اختبارات آلية: غير موجودة.

## 32. النشر

ملف Docker أو منصة نشر: غير موجود. الخطوات العملية هي خادم PHP مع امتداد `mysqli` وMySQL واستيراد `database.sql` وضبط `config/database.php`.

## 33. النسخ الاحتياطي والاستعادة

سكربت نسخ: غير موجود. المرجع `database.sql`. الجداول `CREATE TABLE IF NOT EXISTS`. إدراج الفئات والأطعمة والمدير ليس `INSERT IGNORE`، فتكرار الاستيراد على قاعدة ممتلئة قد يكرر الأصناف أو يصطدم بفريد البريد `admin@dfos.com`.

## 34. تشخيص المشكلات

| العرض | المطابق |
| --- | --- |
| «فشل الاتصال بقاعدة البيانات» | MySQL متوقف، أو القاعدة اسمها `dfos_db` بينما `DB_NAME` هو `dfos` |
| السلة ترجع من الدفع | `$_SESSION['cart']` فارغة |
| رفض الحجز | تاريخ قبل اليوم أو وقت أو تاريخ فارغ |
| المدير لا يدخل من صفحة المستخدم أو العكس | `admin/login.php` يشترط `is_admin = 1` |
| صورة صنف لا تظهر | الملف غير موجود في `assets/images/` رغم الاسم في العمود |
| ضيف في جدول الطلبات | `user_id` فارغ وهذا سلوك مقصود في المخطط |

## 35. الاعتماديات

`composer.json`: غير موجود. PHP مع امتداد MySQLi، وMySQL، وخادم ويب. رفع الصور يحتاج صلاحية كتابة على `assets/images/`.

## 36. القيود المعروفة

- تناقض التوثيق القديم `dfos_db` مع الاسم الفعلي `dfos`.
- CSRF غير موجود.
- رسالة الاتصال تكشف `connect_error`.
- فحص نوع الملف المرفوع بالمحتوى: غير موجود.
- الضريبة ثابتة `0.15` في ملف الدفع وليست جدولاً.
- دفع إلكتروني: غير موجود.
- favicon: غير موجود.
- `auth_check.php` لا يحوّل غير المسجل بنفسه.

## 37. حالة النظام الحالية

صفحات الطلب والإدارة و`database.sql` موجودة. README السابق موجود ووصف الهيكل. اسم القاعدة في ذلك الوصف يختلف عن `config/database.php`. رقم إصدار منتج: غير موجود. بيانات تجريبية للفئات والأطعمة ومدير واحد موجودة في SQL.

## 38. قرارات المعمارية

| القرار | الأثر |
| --- | --- |
| سلة في الجلسة | ترتبط بجلسة الخادم وتُفقد بانتهائها |
| `user_id` قابل للإفراغ | الضيف يطلب دون حساب |
| `service_type` على نفس جدول `orders` | الحجز والطلب العادي في جدول واحد |
| MySQLi مباشرة | نمط مختلف عن مشاريع PDO الأخرى في المجلد الأب، وهذا وصف لهذا المشروع فقط |
| حالات نصية بلا جدول مرجعي | القيم مضبوطة في PHP عند التحديث |

## 39. سجل التغييرات

سجل إصدارات: غير موجود. README السابق وثق الهيكل وخطوات XAMPP واسم `dfos_db` وحساب المدير. هذا الملف يثبت اسم القاعدة `dfos` من SQL و`config/database.php` ويوثق `reserve_table.php` والضريبة وحالات الطلب.

## System Overview

DFOS is a PHP food ordering site using MySQLi and database `dfos`. Guests and users add foods to a session cart, check out with 15 percent tax, and can reserve a table. Admins manage categories, foods, order status, and users. The older README name `dfos_db` does not match `DB_NAME`.

## Quick Reference

| البند | القيمة |
| --- | --- |
| القاعدة في الكود وSQL | `dfos` |
| الاسم في README السابق | `dfos_db` |
| الاتصال | `config/database.php` |
| مدير البذرة | `admin@dfos.com` و`is_admin = 1` |
| سلة | `$_SESSION['cart']` |
| ضريبة الدفع | `0.15` |
| أنواع الخدمة | `takeout`, `table_reservation` |
| حالات الإدارة | `pending`, `preparing`, `ready`, `delivered`, `cancelled` |
| صور | `assets/images/` |

## Quick Start

1. شغّل Apache وMySQL.
2. استورد `database.sql` حتى تُنشأ القاعدة `dfos`.
3. تأكد أن `DB_NAME` يبقى `dfos`.
4. افتح `/DFOS/` ولوحة `/DFOS/admin/`.
5. ادخل للمدير بالبريد `admin@dfos.com`. كلمة المرور في تعليق SQL وغير مكررة هنا.

## For Non-Technical Users

تصفح الأصناف وأضفها إلى السلة ثم أكد الطلب. يمكنك الطلب بدون حساب. يمكن اختيار استلام عادي أو حجز طاولة بتاريخ ووقت. صفحة مستقلة تحجز طاولة دون المرور بالسلة. المدير يغيّر حالة الطلب من لوحة التحكم. الدفع الإلكتروني داخل الموقع: غير موجود، والفاتورة تحسب ضريبة 15 بالمئة على طلب السلة.

## For Developers

استخدم `$conn` من `config/database.php`. لا تنشئ قاعدة باسم `dfos_db` إلا إذا غيّرت `DB_NAME` و`USE` معاً. أبقِ حالات الطلب ضمن `$allowed`. سعر السلة نسخة وقت الإضافة. رفع الصور يكتب في `assets/images/` باسم يعتمد على الوقت وامتداد الاسم الأصلي.
