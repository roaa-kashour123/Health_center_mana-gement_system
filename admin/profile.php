<?php
// استدعاء ملف التحقق من تسجيل الدخول للمشرف (المدير)
require_once 'includes/auth_check.php';

// تحديد اسم الصفحة الحالية
$current_page_name = 'profile';

// استدعاء إعدادات الاتصال بقاعدة البيانات
require_once '../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

// جلب معلومات المدير (المشرف) من قاعدة البيانات بناءً على معرفه الموجود في الجلسة
$stmt = $conn->prepare("SELECT id, username, email, full_name, phone FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// التحقق إذا كانت بيانات المدير غير موجودة (أي أن الجلسة تم التلاعب بها أو تم تسجيل الخروج)
if (!$admin) {
    // إذا لم يتم العثور على المدير، إعادة توجيه المستخدم إلى صفحة تسجيل الدخول
    header('Location: login.php');
    exit;
}

$message = '';  // لتخزين الرسائل (نجاح أو خطأ)
$message_type = ''; // لتخزين نوع الرسالة (نجاح أو خطأ)

// معالجة تعديل الملف الشخصي
if ($_POST && isset($_POST['update_profile'])) {
    // جمع البيانات المدخلة في النموذج
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    
    // التحقق من البريد الإلكتروني (عدم التكرار)
    // التحقق إذا كان هناك مدير آخر يستخدم نفس البريد الإلكتروني
    $check = $conn->prepare("SELECT id FROM admins WHERE email = ? AND id != ?");
    $check->execute([$email, $_SESSION['admin_id']]);
    if ($check->rowCount() > 0) {
        // إذا كان البريد الإلكتروني مستخدم من قبل مدير آخر، عرض رسالة خطأ
        $message = "البريد الإلكتروني مستخدم من قبل مدير آخر.";
        $message_type = "error";
    } else {
        // إذا لم يكن البريد الإلكتروني مستخدم، تحديث بيانات الملف الشخصي في قاعدة البيانات
        $stmt = $conn->prepare("UPDATE admins SET full_name = ?, email = ?, phone = ? WHERE id = ?");
        if ($stmt->execute([$full_name, $email, $phone, $_SESSION['admin_id']])) {
            // إذا تمت العملية بنجاح، تحديث الجلسة برسالة نجاح
            $_SESSION['admin_name'] = $full_name;
            $message = "تم تحديث الملف الشخصي بنجاح.";
            $message_type = "success";

            // إعادة تحميل البيانات المحدثة من قاعدة البيانات لتحديث الكائن $admin
            $stmt = $conn->prepare("SELECT id, username, email, full_name, phone FROM admins WHERE id = ?");
            $stmt->execute([$_SESSION['admin_id']]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            // في حالة حدوث خطأ أثناء التحديث
            $message = "حدث خطأ أثناء التحديث.";
            $message_type = "error";
        }
    }
}

// معالجة تغيير كلمة المرور
if ($_POST && isset($_POST['change_password'])) {
    // جمع البيانات المدخلة من قبل المستخدم لتغيير كلمة المرور
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // جلب كلمة المرور الحالية من قاعدة البيانات للمقارنة
    $stmt = $conn->prepare("SELECT password FROM admins WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $current_hash = $stmt->fetchColumn();
    
    // التحقق من صحة كلمة المرور الحالية
    if (!password_verify($current_password, $current_hash)) {
        // إذا كانت كلمة المرور الحالية غير صحيحة
        $message = "كلمة المرور الحالية غير صحيحة.";
        $message_type = "error";
    } elseif ($new_password !== $confirm_password) {
        // التحقق من تطابق كلمة المرور الجديدة مع تأكيد كلمة المرور
        $message = "كلمة المرور الجديدة وتأكيدها غير متطابقين.";
        $message_type = "error";
    } elseif (strlen($new_password) < 6) {
        // التحقق من طول كلمة المرور الجديدة (يجب أن تكون على الأقل 6 أحرف)
        $message = "كلمة المرور الجديدة يجب أن تحتوي على 6 أحرف على الأقل.";
        $message_type = "error";
    } else {
        // إذا كانت كلمة المرور الجديدة صحيحة، يتم تشفيرها باستخدام الدالة password_hash
        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        // تحديث كلمة المرور الجديدة في قاعدة البيانات
        $stmt = $conn->prepare("UPDATE admins SET password = ? WHERE id = ?");
        if ($stmt->execute([$new_hash, $_SESSION['admin_id']])) {
            // إذا تمت العملية بنجاح
            $message = "تم تغيير كلمة المرور بنجاح.";
            $message_type = "success";
        } else {
            // في حالة حدوث خطأ أثناء تغيير كلمة المرور
            $message = "حدث خطأ أثناء تغيير كلمة المرور.";
            $message_type = "error";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ملفي الشخصي - المركز الصحي المتقدم</title>
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
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-title .icon {
        font-size: 28px;
    }

    /* رسالة النجاح/الخطأ */
    .message {
        padding: 12px;
        border-radius: 10px;
        margin-bottom: 25px;
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

    /* أقسام الملف الشخصي */
    .profile-sections {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
        gap: 30px;
    }

    .profile-card {
        background: var(--card-bg);
        border-radius: 18px;
        padding: 30px;
        box-shadow: 0 6px 15px var(--card-shadow);
    }

    .section-title {
        font-size: 1.4rem;
        margin-bottom: 20px;
        color: var(--text-color);
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--border-color);
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--text-color);
    }

    .form-group input {
        width: 100%;
        padding: 12px;
        border: 2px solid var(--border-color);
        border-radius: 10px;
        font-family: 'Tajawal', sans-serif;
        font-size: 1rem;
        background: var(--card-bg);
        color: var(--text-color);
    }

    .form-group input:focus {
        outline: none;
        border-color: #0ea5e9;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 20px;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        font-size: 1rem;
        border: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        color: white;
    }

    .btn-secondary {
        background: var(--border-color);
        color: var(--muted-text);
    }

    /* معلومات الملف الشخصي للعرض */
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .info-item {
        margin-bottom: 15px;
    }

    .info-label {
        font-weight: 600;
        color: var(--text-color);
        margin-bottom: 5px;
    }

    .info-value {
        color: var(--text-color);
        padding: 8px 12px;
        background: var(--border-color);
        border-radius: 8px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .main-content {
            margin-right: 80px;
        }
        .profile-sections {
            grid-template-columns: 1fr;
        }
        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
    <?php include 'includes/theme_logic.php'; ?>
    <?php include 'includes/sidebar.php' ?>
</head>
<body>

    <!-- المحتوى الرئيسي للصفحة -->
    <div class="main-content">

        <!-- رأس الصفحة: العنوان -->
        <div class="page-header">
            <h1 class="page-title">
                <!-- أيقونة الملف الشخصي -->
                <span class="icon">👤</span>
                <!-- عنوان الصفحة: ملفي الشخصي -->
                ملفي الشخصي
            </h1>
        </div>

        <!-- عرض الرسالة (نجاح أو خطأ) إذا كانت موجودة -->
        <?php if ($message): ?>
            <div class="message <?= $message_type ?>">
                <!-- عرض محتوى الرسالة بعد تأمينه ضد XSS -->
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- قسم يحتوي على ثلاثة أجزاء: تعديل الملف الشخصي، تغيير كلمة المرور، وعرض المعلومات الحالية -->
        <div class="profile-sections">

            <!-- قسم تعديل الملف الشخصي -->
            <div class="profile-card">
                <h2 class="section-title">📋 تعديل الملف الشخصي</h2>
                <form method="POST">
                    <!-- حقل تعديل الاسم الكامل -->
                    <div class="form-group">
                        <label for="full_name">الاسم الكامل</label>
                        <!-- إدخال النص الخاص بالاسم الكامل مع قيمة مبدئية هي الاسم الموجود في قاعدة البيانات -->
                        <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($admin['full_name']) ?>" required>
                    </div>

                    <!-- حقل تعديل البريد الإلكتروني -->
                    <div class="form-group">
                        <label for="email">البريد الإلكتروني</label>
                        <!-- إدخال النص الخاص بالبريد الإلكتروني مع قيمة مبدئية هي البريد الإلكتروني الموجود في قاعدة البيانات -->
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required>
                    </div>

                    <!-- حقل تعديل رقم الجوال -->
                    <div class="form-group">
                        <label for="phone">رقم الجوال</label>
                        <!-- إدخال رقم الجوال مع قيمة مبدئية إذا كانت موجودة -->
                        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($admin['phone'] ?? '') ?>">
                    </div>

                    <!-- زر حفظ التغييرات -->
                    <div class="form-actions">
                        <button type="submit" name="update_profile" class="btn btn-primary">حفظ التغييرات</button>
                    </div>
                </form>
            </div>

            <!-- قسم تغيير كلمة المرور -->
            <div class="profile-card">
                <h2 class="section-title">🔒 تغيير كلمة المرور</h2>
                <form method="POST">
                    <!-- حقل كلمة المرور الحالية -->
                    <div class="form-group">
                        <label for="current_password">كلمة المرور الحالية</label>
                        <!-- إدخال كلمة المرور الحالية -->
                        <input type="password" id="current_password" name="current_password" required>
                    </div>

                    <!-- حقل كلمة المرور الجديدة -->
                    <div class="form-group">
                        <label for="new_password">كلمة المرور الجديدة</label>
                        <!-- إدخال كلمة المرور الجديدة مع تحديد الحد الأدنى للطول 6 أحرف -->
                        <input type="password" id="new_password" name="new_password" required minlength="6">
                    </div>

                    <!-- حقل تأكيد كلمة المرور الجديدة -->
                    <div class="form-group">
                        <label for="confirm_password">تأكيد كلمة المرور الجديدة</label>
                        <!-- إدخال تأكيد كلمة المرور الجديدة مع نفس شرط الطول -->
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                    </div>

                    <!-- زر تغيير كلمة المرور -->
                    <div class="form-actions">
                        <button type="submit" name="change_password" class="btn btn-primary">تغيير كلمة المرور</button>
                    </div>
                </form>
            </div>

            <!-- قسم عرض المعلومات الحالية للمستخدم -->
            <div class="profile-card">
                <h2 class="section-title">👁️ معلومات الحساب الحالية</h2>
                <div class="info-grid">
                    <!-- عرض اسم المستخدم -->
                    <div class="info-item">
                        <div class="info-label">اسم المستخدم</div>
                        <div class="info-value"><?= htmlspecialchars($admin['username']) ?></div>
                    </div>

                    <!-- عرض الاسم الكامل -->
                    <div class="info-item">
                        <div class="info-label">الاسم الكامل</div>
                        <div class="info-value"><?= htmlspecialchars($admin['full_name']) ?></div>
                    </div>

                    <!-- عرض البريد الإلكتروني -->
                    <div class="info-item">
                        <div class="info-label">البريد الإلكتروني</div>
                        <div class="info-value"><?= htmlspecialchars($admin['email']) ?></div>
                    </div>

                    <!-- عرض رقم الجوال -->
                    <div class="info-item">
                        <div class="info-label">رقم الجوال</div>
                        <div class="info-value"><?= htmlspecialchars($admin['phone'] ?? 'غير متوفر') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
