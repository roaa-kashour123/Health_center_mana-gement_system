<?php
// استدعاء ملف التحقق من تسجيل الدخول لضمان أن المستخدم مسجل دخوله ولديه صلاحيات للوصول إلى الصفحة
require_once 'includes/auth_check.php';

// تعيين اسم الصفحة الحالية لاستخدامه في العنوان أو التنقل
$current_page_name = 'view_responses';

// استدعاء إعدادات الاتصال بقاعدة البيانات
require_once '../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

// التحقق من وجود معرّف الاستبيان في الرابط وتأكد من أنه عدد صحيح
if (!isset($_GET['survey_id']) || !is_numeric($_GET['survey_id'])) {
    // إذا لم يكن معرّف الاستبيان موجودًا أو لم يكن عددًا صحيحًا، يتم إعادة التوجيه إلى صفحة الاستبيانات
    header('Location: surveys.php');
    exit;
}

// تعيين معرّف الاستبيان الذي تم تمريره عبر الرابط إلى متغير
$survey_id = (int)$_GET['survey_id'];

// جلب تفاصيل الاستبيان باستخدام معرّف الاستبيان
$stmt_survey = $conn->prepare("SELECT title, description FROM surveys WHERE id = ?");
$stmt_survey->execute([$survey_id]);
$survey = $stmt_survey->fetch(PDO::FETCH_ASSOC);

// التحقق مما إذا كان الاستبيان موجودًا في قاعدة البيانات
if (!$survey) {
    echo "لم يتم العثور على الاستبيان.";
    exit;  // إذا لم يتم العثور على الاستبيان، يتم إيقاف التنفيذ وعرض رسالة للمستخدم
}

// جلب ردود الاستبيان باستخدام معرّف الاستبيان
$query = "
    SELECT 
        sr.score,                    -- التقييم الذي قدمه المريض
        sr.response_text,            -- نص الرد الذي قدمه المريض
        sr.created_at,               -- تاريخ ووقت تقديم الرد
        p.full_name as patient_name  -- اسم المريض الذي قدم الرد
    FROM survey_responses sr
    LEFT JOIN patients p ON sr.patient_id = p.id  -- الانضمام إلى جدول المرضى للحصول على اسم المريض
    WHERE sr.survey_id = ?  -- تحديد الاستبيان الذي تمت عليه الردود باستخدام معرّف الاستبيان
    ORDER BY sr.created_at DESC  -- ترتيب الردود حسب تاريخ تقديمها من الأحدث إلى الأقدم
";
$stmt = $conn->prepare($query);
$stmt->execute([$survey_id]);

// جلب جميع الردود وتخزينها في مصفوفة
$responses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// حساب عدد الردود
$response_count = count($responses);
?>


<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ردود الاستبيان: <?= htmlspecialchars($survey['title']) ?> - المركز الصحي</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    .main-content { margin-right: 260px; padding: 30px; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px; }
    
    .page-title { 
        font-size: 1.8rem; 
        font-weight: 700; 
        color: var(--text-color);
        display: flex; 
        align-items: center; 
        gap: 12px; 
    }
    
    .survey-info-card { 
        background: var(--border-color);
        border-radius: 12px; 
        padding: 20px; 
        margin-bottom: 30px; 
        border: 1px solid var(--border-color);
    }
    
    .survey-info-card h2 { 
        margin-top: 0; 
        font-size: 1.5rem; 
        color: var(--text-color);
    }
    
    .survey-info-card p { 
        color: var(--text-color);
        line-height: 1.6; 
    }
    
    .response-card { 
        background: var(--card-bg);
        border-radius: 16px; 
        padding: 25px; 
        margin-bottom: 20px; 
        box-shadow: 0 2px 8px var(--card-shadow);
    }
    
    .response-header { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        margin-bottom: 15px; 
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 10px; 
    }
    
    .score-badge { 
        padding: 6px 12px; 
        border-radius: 8px; 
        font-weight: 700; 
        background: #dcfce7; 
        color: #166534; 
    }
    
    .response-meta { 
        font-size: 0.9rem; 
        color: var(--muted-text);
    }
    
    .response-comment { 
        line-height: 1.7; 
        color: var(--text-color);
    }
    
    .back-link { 
        margin-bottom: 20px; 
        display: inline-block; 
        color: #0ea5e9; 
        text-decoration: none; 
        font-weight: 600; 
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
    }

    .empty-state p {
        color: var(--muted-text);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .main-content { margin-right: 80px; }
    }
</style>
    <?php include 'includes/theme_logic.php'; ?>
    <?php include 'includes/sidebar.php' ?>
</head>
<body>

    <div class="main-content">
        <a href="surveys.php" class="back-link">← العودة إلى الاستبيانات</a>
        <div class="page-header">
            <h1 class="page-title">
                <span class="icon">📋</span>
                ردود استبيان: <?= htmlspecialchars($survey['title']) ?> (<?= $response_count ?> رد)
            </h1>
        </div>
        
        <div class="survey-info-card">
            <h2>وصف الاستبيان</h2>
            <p><?= nl2br(htmlspecialchars($survey['description'])) ?></p>
        </div>

        <?php if ($response_count > 0): ?>
            <?php foreach ($responses as $response): ?>
            <div class="response-card">
                <div class="response-header">
                    <div class="score-badge">
                        التقييم: <?= $response['score'] ?? 'غير متوفر' ?>
                    </div>
                    <div class="response-meta">
                        الناشر: <?= htmlspecialchars($response['patient_name'] ?? 'مستخدم مجهول') ?> | 
                        التاريخ: <?= date('Y-m-d H:i', strtotime($response['created_at'])) ?>
                    </div>
                </div>
                <p class="response-comment">
                    **التعليق/الرد:**<br>
                    <?= nl2br(htmlspecialchars($response['response_text'] ?? 'لا يوجد رد نصي.')) ?>
                </p>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
        <div class="empty-state">
            <h3>
                <span class="icon">❌</span>
                لا توجد ردود حالياً لهذا الاستبيان
            </h3>
            <p>الانتظار حتى يتم إرسال ردود من قبل المستخدمين.</p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>