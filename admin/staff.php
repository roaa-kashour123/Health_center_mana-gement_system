<?php
// استدعاء ملف التحقق من تسجيل الدخول (لضمان أن المستخدم مسجل دخوله ولديه صلاحيات)
require_once 'includes/auth_check.php';

// تعيين اسم الصفحة الحالية لاستخدامه في العنوان أو التنقل
$current_page_name = 'staff';

// استدعاء إعدادات الاتصال بقاعدة البيانات
require_once '../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

// متغير لتخزين الرسائل التي سيتم عرضها للمستخدم (نجاح أو خطأ)
$message = '';

// معالجة إضافة موظف جديد
if ($_POST && isset($_POST['add_staff'])) {
    try {
        // جمع البيانات المدخلة من المستخدم (اسم الموظف، البريد الإلكتروني، الهاتف، المسمى الوظيفي)
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $position = trim($_POST['position']);
        $status = 'active'; // الوضع الافتراضي للموظف الجديد هو "نشط"

        // التحقق من وجود البريد الإلكتروني في قاعدة البيانات
        $check = $conn->prepare("SELECT id FROM staff WHERE email = ?");
        $check->execute([$email]);
        if ($check->rowCount() > 0) {
            // إذا كان البريد الإلكتروني موجودًا بالفعل في قاعدة البيانات
            $message = "البريد الإلكتروني مستخدم بالفعل.";
        } else {
            // إذا لم يكن البريد الإلكتروني موجودًا، يتم إدخال الموظف الجديد في قاعدة البيانات
            $stmt = $conn->prepare("INSERT INTO staff (full_name, email, phone, position, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$full_name, $email, $phone, $position, $status]);
            $message = "تم إضافة الموظف بنجاح.";
        }
    } catch (Exception $e) {
        // في حالة حدوث خطأ أثناء تنفيذ العملية، يتم عرض رسالة الخطأ
        $message = "حدث خطأ: " . $e->getMessage();
    }
}

// معالجة تغيير حالة الموظف (تفعيل أو تعليق)
if (isset($_GET['action']) && isset($_GET['id'])) {
    // جمع المعرف (ID) من الرابط
    $id = (int)$_GET['id']; // التأكد من أن المعرف هو عدد صحيح
    $action = $_GET['action']; // جمع نوع الإجراء (تفعيل أو تعليق)

    // تحديد الحالة الجديدة بناءً على نوع الإجراء
    $status = ($action === 'suspend') ? 'suspended' : 'active'; // إذا كان الإجراء "تعليق" يتم وضع الحالة "معلق"، وإلا "نشط"

    // تحديث حالة الموظف في قاعدة البيانات
    $stmt = $conn->prepare("UPDATE staff SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    // إعادة توجيه المستخدم إلى صفحة الموظفين بعد التحديث
    header('Location: staff.php');
    exit;
}

// جلب قائمة الموظفين من قاعدة البيانات
$stmt = $conn->query("SELECT id, full_name, email, phone, position, status FROM staff ORDER BY created_at DESC");
// تحويل البيانات المسترجعة إلى مصفوفة مرتبطة
$staff = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الموظفين - المركز الصحي المتقدم</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* المحتوى الرئيسي */
    .main-content {
        margin-right: 260px;
        padding: 30px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .page-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--text-color);
    }

    .btn-add {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: transform 0.2s;
    }

    .btn-add:hover {
        transform: translateY(-2px);
    }

    /* نموذج إضافة موظف */
    .add-staff-form {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px var(--card-shadow);
        display: none;
    }

    .add-staff-form.active {
        display: block;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--text-color);
    }

    .form-group input, .form-group select {
        width: 100%;
        padding: 12px;
        border: 2px solid var(--border-color);
        border-radius: 10px;
        font-family: 'Tajawal', sans-serif;
        font-size: 1rem;
        background: var(--card-bg);
        color: var(--text-color);
    }

    .form-group input:focus, .form-group select:focus {
        outline: none;
        border-color: #0ea5e9;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .btn-submit {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-cancel {
        background: var(--border-color);
        color: var(--muted-text);
        border: none;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
    }

    /* رسالة النجاح/الخطأ */
    .message {
        padding: 12px;
        border-radius: 10px;
        margin-bottom: 20px;
        text-align: center;
        font-weight: 500;
    }

    .message.success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .message.error {
        background: #fee2e2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    /* جدول الموظفين */
    .staff-table {
        width: 100%;
        border-collapse: collapse;
        background: var(--card-bg);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 15px var(--card-shadow);
    }

    .staff-table th {
        background: var(--border-color);
        padding: 16px;
        text-align: right;
        font-weight: 700;
        color: var(--text-color);
    }

    .staff-table td {
        padding: 16px;
        text-align: right;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-color);
    }

    .staff-table tr:last-child td {
        border-bottom: none;
    }

    .status {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .status-active { background: #dcfce7; color: #166534; }
    .status-suspended { background: #fee2e2; color: #dc2626; }

    .actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-action {
        padding: 8px 14px;
        border-radius: 8px;
        color: white;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .activate { background: linear-gradient(135deg, #10b981, #047857); }
    .suspend { background: linear-gradient(135deg, #f59e0b, #b45309); }

    /* رسالة فارغ */
    .empty-state {
        background: var(--card-bg);
        padding: 40px;
        border-radius: 16px;
        text-align: center;
        box-shadow: 0 4px 15px var(--card-shadow);
    }

    .empty-state h3 {
        color: var(--text-color);
        margin-bottom: 10px;
    }

    .empty-state p {
        color: var(--muted-text);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .main-content {
            margin-right: 80px;
        }
        .form-grid {
            grid-template-columns: 1fr;
        }
        .page-header {
            flex-direction: column;
            align-items: stretch;
        }
        .actions {
            justify-content: center;
        }
    }
</style>
    <?php include 'includes/theme_logic.php'; ?>
    <?php include 'includes/sidebar.php' ?>
</head>
<body>
    <!-- المحتوى الرئيسي للصفحة -->
    <div class="main-content">

        <!-- رأس الصفحة: العنوان وزر إضافة موظف -->
        <div class="page-header">
            <!-- عنوان الصفحة مع عرض عدد الموظفين -->
            <h1 class="page-title">
                إدارة الموظفين (<?= count($staff) ?>)
            </h1>

            <!-- زر إظهار/إخفاء نموذج إضافة موظف جديد -->
            <button class="btn-add" id="toggleForm">
                <span>+</span> إضافة موظف جديد
            </button>
        </div>

        <!-- عرض رسالة نجاح أو خطأ (إن وُجدت) -->
        <?php if ($message): ?>
            <div class="message <?= strpos($message, 'خطأ') !== false ? 'error' : 'success' ?>">
                <!-- حماية النص من هجمات XSS -->
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- نموذج إضافة موظف جديد -->
        <form class="add-staff-form" id="staffForm" method="POST">

            <!-- تقسيم النموذج إلى شبكة (Grid) -->
            <div class="form-grid">

                <!-- حقل الاسم الكامل -->
                <div class="form-group">
                    <label for="full_name">الاسم الكامل</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>

                <!-- حقل البريد الإلكتروني -->
                <div class="form-group">
                    <label for="email">البريد الإلكتروني</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <!-- حقل رقم الجوال -->
                <div class="form-group">
                    <label for="phone">رقم الجوال</label>
                    <input type="tel" id="phone" name="phone">
                </div>

                <!-- حقل المسمى الوظيفي -->
                <div class="form-group">
                    <label for="position">المسمى الوظيفي</label>
                    <input type="text" id="position" name="position"
                           placeholder="مثل: موظف أمن، موظف IT، إلخ" required>
                </div>
            </div>

            <!-- حقل مخفي لتحديد أن الطلب هو إضافة موظف -->
            <input type="hidden" name="add_staff" value="1">

            <!-- أزرار التحكم في النموذج -->
            <div class="form-actions">
                <!-- زر إلغاء وإخفاء النموذج -->
                <button type="button" class="btn-cancel" id="cancelForm">إلغاء</button>

                <!-- زر إرسال النموذج -->
                <button type="submit" class="btn-submit">إضافة الموظف</button>
            </div>
        </form>

        <!-- جدول عرض الموظفين -->
        <?php if (count($staff) > 0): ?>
        <table class="staff-table">
            <thead>
                <tr>
                    <th>الاسم الكامل</th>
                    <th>المسمى الوظيفي</th>
                    <th>رقم الجوال</th>
                    <th>البريد الإلكتروني</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <!-- حلقة لعرض بيانات كل موظف -->
                <?php foreach ($staff as $employee): ?>
                <tr>
                    <!-- عرض اسم الموظف -->
                    <td><?= htmlspecialchars($employee['full_name']) ?></td>

                    <!-- عرض المسمى الوظيفي -->
                    <td><?= htmlspecialchars($employee['position']) ?></td>

                    <!-- عرض رقم الجوال أو نص افتراضي -->
                    <td><?= htmlspecialchars($employee['phone'] ?? 'غير متوفر') ?></td>

                    <!-- عرض البريد الإلكتروني -->
                    <td><?= htmlspecialchars($employee['email']) ?></td>

                    <!-- عرض حالة الموظف (فعال / معلق) -->
                    <td>
                        <span class="status status-<?= $employee['status'] ?>">
                            <?= $employee['status'] === 'active' ? 'فعال' : 'معلق' ?>
                        </span>
                    </td>

                    <!-- أزرار التحكم بالحالة -->
                    <td class="actions">
                        <?php if ($employee['status'] === 'active'): ?>
                            <!-- زر تعليق الموظف -->
                            <a href="?action=suspend&id=<?= $employee['id'] ?>"
                               class="btn-action suspend">تعليق</a>
                        <?php else: ?>
                            <!-- زر تفعيل الموظف -->
                            <a href="?action=activate&id=<?= $employee['id'] ?>"
                               class="btn-action activate">تفعيل</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- رسالة تظهر في حال عدم وجود موظفين -->
        <?php else: ?>
        <div class="empty-state">
            <h3>لا يوجد موظفين مسجلين حاليًا</h3>
            <p>يمكنك إضافة موظفين جدد باستخدام زر "إضافة موظف جديد".</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- جافاسكربت للتحكم في إظهار وإخفاء نموذج الإضافة -->
    <script>
        // عند الضغط على زر "إضافة موظف جديد"
        document.getElementById('toggleForm').addEventListener('click', function() {
            // تبديل حالة إظهار/إخفاء النموذج
            document.getElementById('staffForm').classList.toggle('active');
        });

        // عند الضغط على زر "إلغاء"
        document.getElementById('cancelForm').addEventListener('click', function() {
            // إخفاء النموذج
            document.getElementById('staffForm').classList.remove('active');
        });
    </script>
</body>

</html>