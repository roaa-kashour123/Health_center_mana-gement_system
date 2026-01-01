<?php
// استدعاء ملف التحقق من تسجيل الدخول لضمان أن المستخدم مسجل دخوله ولديه صلاحيات للوصول إلى الصفحة
require_once 'includes/auth_check.php';

// تعيين اسم الصفحة الحالية لاستخدامه في العنوان أو التنقل
$current_page_name = 'surveys';

// استدعاء إعدادات الاتصال بقاعدة البيانات
require_once '../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

// جلب الاستبيانات مع معلومات الموظف الذي أنشأها
$stmt = $conn->query("
    SELECT s.id, s.title, s.description, s.created_at, st.full_name as creator_name
    FROM surveys s
    JOIN staff st ON s.created_by = st.id  -- ربط الاستبيانات مع جدول الموظفين عبر معرف الموظف
    ORDER BY s.created_at DESC  -- ترتيب الاستبيانات بناءً على تاريخ الإنشاء من الأحدث إلى الأقدم
");

// جلب جميع البيانات من الاستعلام وتخزينها في مصفوفة
$surveys = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الاستبيانات - المركز الصحي المتقدم</title>
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

    /* بطاقة الاستبيان */
    .survey-card {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 4px 15px var(--card-shadow);
    }

    .survey-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .survey-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-color);
        margin-bottom: 10px;
    }

    .survey-meta {
        display: flex;
        align-items: center;
        gap: 20px;
        color: var(--muted-text);
        font-size: 0.95rem;
    }

    .creator {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .creator-icon {
        color: #0ea5e9;
        font-size: 18px;
    }

    .date {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .date-icon {
        color: var(--muted-text);
    }

    .survey-description {
        line-height: 1.7;
        color: var(--text-color);
        font-size: 1.05rem;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--border-color);
    }

    /* زر عرض الردود فقط */
    .view-responses {
        padding: 8px 16px;
        border-radius: 8px;
        color: white;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
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
        .survey-header {
            flex-direction: column;
            align-items: flex-start;
        }
        .survey-title {
            font-size: 1.3rem;
        }
    }
</style>
    <?php include 'includes/theme_logic.php'; ?>
    <?php include 'includes/sidebar.php' ?>
</head>
<body>

    <!-- المحتوى الرئيسي -->
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">
                <span class="icon">📋</span>
                الاستبيانات (<?= count($surveys) ?>)
            </h1>
        </div>

        <!-- عرض الاستبيانات -->
        <?php if (count($surveys) > 0): ?>
            <?php foreach ($surveys as $survey): ?>
            <div class="survey-card">
                <div class="survey-header">
                    <h2 class="survey-title"><?= htmlspecialchars($survey['title']) ?></h2>
                    <div class="survey-meta">
                        <div class="creator">
                            <span class="creator-icon">👤</span>
                            <?= htmlspecialchars($survey['creator_name']) ?>
                        </div>
                        <div class="date">
                            <span class="date-icon">📅</span>
                            <?= date('d/m/Y', strtotime($survey['created_at'])) ?>
                        </div>
                    </div>
                </div>
                <div class="survey-description">
                    <?= nl2br(htmlspecialchars($survey['description'])) ?>
                </div>
                <!-- زر عرض الردود فقط (لا يمكن تغيير الحالة أو أي شيء آخر) -->
                <a href="view_responses.php?survey_id=<?= $survey['id'] ?>" class="view-responses">عرض الردود</a>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
        <div class="empty-state">
            <h3>
                <span class="icon">📋</span>
                لا توجد استبيانات حالياً
            </h3>
            <p>الموظفون يمكنهم إنشاء استبيانات جديدة عبر نظامهم الخاص.</p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
