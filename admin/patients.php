<?php
require_once 'includes/auth_check.php';
$current_page_name = 'patients';

require_once '../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

// جلب قائمة المرضى
$stmt = $conn->query("SELECT id, full_name, email, phone, dob, gender, address FROM patients ORDER BY created_at DESC");
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قائمة المرضى - المركز الصحي المتقدم</title>
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

    /* جدول المرضى */
    .patients-table {
        width: 100%;
        border-collapse: collapse;
        background: var(--card-bg);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 15px var(--card-shadow);
    }

    .patients-table th {
        background: var(--border-color);
        padding: 16px;
        text-align: right;
        font-weight: 700;
        color: var(--text-color);
    }

    .patients-table td {
        padding: 16px;
        text-align: right;
        border-bottom: 1px solid var(--border-color);
    }

    .patients-table tr:last-child td {
        border-bottom: none;
    }

    .patient-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: white;
        font-weight: bold;
        font-size: 16px;
        margin-left: 10px;
    }

    .gender-male { color: #3b82f6; }
    .gender-female { color: #ec4899; }
    .gender-other { color: var(--muted-text); }

    .dob-age {
        display: flex;
        flex-direction: column;
    }

    .age {
        font-size: 0.85rem;
        color: var(--muted-text);
    }

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
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .empty-state .icon {
        font-size: 32px;
    }

    .empty-state p {
        color: var(--muted-text);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .main-content {
            margin-right: 80px;
        }
        .page-header {
            flex-direction: column;
            align-items: stretch;
        }
        .patients-table th,
        .patients-table td {
            padding: 12px 8px;
            font-size: 0.9rem;
        }
    }

    @media (max-width: 480px) {
        .patients-table thead {
            display: none;
        }
        .patients-table, .patients-table tbody, .patients-table tr, .patients-table td {
            display: block;
            width: 100%;
        }
        .patients-table tr {
            margin-bottom: 20px;
            padding: 15px;
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 2px 8px var(--card-shadow);
        }
        .patients-table td {
            text-align: right;
            padding: 8px 0;
            position: relative;
        }
        .patients-table td::before {
            content: attr(data-label) ": ";
            font-weight: 600;
            color: var(--text-color);
            display: inline-block;
            width: 120px;
        }
    }
</style>
    <?php include 'includes/theme_logic.php'; ?>
    <?php include 'includes/sidebar.php'?>
</head>
<body>
    
    <!-- المحتوى الرئيسي -->
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">
                <span class="icon">🏥</span>
                قائمة المرضى (<?= count($patients) ?>)
            </h1>
        </div>

        <!-- جدول المرضى -->
        <?php if (count($patients) > 0): ?>
        <table class="patients-table">
            <thead>
                <tr>
                    <th>المريض</th>
                    <th>البريد الإلكتروني</th>
                    <th>رقم الجوال</th>
                    <th>تاريخ الميلاد</th>
                    <th>الجنس</th>
                    <th>العنوان</th>
                </tr>
            </thead>
            <tbody>
    <!-- حلقة للتكرار عبر كل مريض في المصفوفة $patients -->
    <?php foreach ($patients as $patient): ?>
    <tr>
        <!-- عرض اسم المريض مع أيقونة شخصية -->
        <td>
            <span class="patient-icon">👤</span>
            <!-- استخدام htmlspecialchars لحماية البيانات من الثغرات الأمنية مثل XSS -->
            <?= htmlspecialchars($patient['full_name']) ?>
        </td>

        <!-- عرض البريد الإلكتروني للمريض، مع التحقق إذا كان غير موجود وعرض "غير متوفر" بدلاً منه -->
        <td><?= htmlspecialchars($patient['email'] ?? 'غير متوفر') ?></td>

        <!-- عرض رقم الجوال للمريض، مع التحقق إذا كان غير موجود وعرض "غير متوفر" بدلاً منه -->
        <td><?= htmlspecialchars($patient['phone'] ?? 'غير متوفر') ?></td>

        <!-- عرض تاريخ الميلاد مع حساب العمر بناءً عليه -->
        <td class="dob-age">
            <!-- عرض تاريخ الميلاد -->
            <?= htmlspecialchars($patient['dob']) ?>

            <!-- حساب العمر باستخدام دالة date_diff -->
            <span class="age">
                <?php 
                if ($patient['dob']) {
                    // حساب الفرق بين تاريخ الميلاد والتاريخ الحالي
                    $age = date_diff(date_create($patient['dob']), date_create('now'))->y;
                    // عرض العمر بالسنوات
                    echo "($age سنة)";
                }
                ?>
            </span>
        </td>

        <!-- عرض الجنس بناءً على القيمة المخزنة -->
        <td>
            <?php if ($patient['gender'] === 'male'): ?>
                <!-- إذا كان الجنس ذكر، عرض النص مع أيقونة الذكر -->
                <span class="gender-male">ذكر ♂</span>
            <?php elseif ($patient['gender'] === 'female'): ?>
                <!-- إذا كان الجنس أنثى، عرض النص مع أيقونة الأنثى -->
                <span class="gender-female">أنثى ♀</span>
            <?php else: ?>
                <!-- إذا كان الجنس غير محدد أو قيمة أخرى، عرض "أخرى" -->
                <span class="gender-other">أخرى</span>
            <?php endif; ?>
        </td>

        <!-- عرض عنوان المريض مع التحقق إذا كان غير موجود وعرض "غير متوفر" بدلاً منه -->
        <td><?= htmlspecialchars($patient['address'] ?? 'غير متوفر') ?></td>
    </tr>
    <?php endforeach; ?>
</tbody>

        </table>
        <?php else: ?>
        <div class="empty-state">
            <h3>
                <span class="icon">🏥</span>
                لا يوجد مرضى مسجلين حاليًا
            </h3>
            <p>يمكنك إضافة مرضى عبر قاعدة البيانات مباشرةً أو من خلال نظام الحجز.</p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>