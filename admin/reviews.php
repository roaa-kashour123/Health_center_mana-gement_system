<?php
// استدعاء ملف التحقق من تسجيل الدخول
require_once 'includes/auth_check.php';

// تعيين اسم الصفحة الحالية لاستخدامه في العنوان أو التنقل
$current_page_name = 'reviews';

// استدعاء إعدادات الاتصال بقاعدة البيانات
require_once '../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

// استعلام SQL لجلب قائمة التقييمات
$query = "
    SELECT 
        r.id,                     -- جلب معرف التقييم
        r.rating,                 -- جلب التقييم نفسه (مثل 1-5)
        r.comment,                -- جلب تعليق التقييم
        r.entity_type,            -- نوع الكيان الذي تم تقييمه (مثل: طبيب، اختصاصي، مركز)
        r.created_at,             -- تاريخ ووقت إنشاء التقييم
        p.full_name as patient_name,  -- اسم المريض الذي قام بالتقييم
        -- تحديد اسم الكيان الذي تم تقييمه بناءً على نوع الكيان (طبيب أو اختصاصي أو مركز صحي)
        CASE r.entity_type
            WHEN 'doctor' THEN d.full_name         -- إذا كان الكيان طبيبًا، إرجاع اسم الطبيب
            WHEN 'specialist' THEN s.full_name    -- إذا كان الكيان اختصاصيًا، إرجاع اسم الاختصاصي
            WHEN 'center' THEN 'المركز الصحي العام'  -- إذا كان الكيان مركزًا صحيًا، إرجاع اسم المركز
            ELSE 'غير معروف'                      -- إذا لم يكن الكيان معروفًا، إرجاع 'غير معروف'
        END as rated_entity_name,
        r.entity_id                -- جلب معرف الكيان (مثل معرف الطبيب أو الاختصاصي أو المركز الصحي)
    FROM ratings r
    JOIN patients p ON r.patient_id = p.id  -- ربط التقييم مع المرضى عبر معرف المريض
    LEFT JOIN doctors d ON r.entity_type = 'doctor' AND r.entity_id = d.id  -- ربط التقييم مع الأطباء (إذا كان الكيان هو طبيب)
    LEFT JOIN specialists s ON r.entity_type = 'specialist' AND r.entity_id = s.id  -- ربط التقييم مع الاختصاصيين (إذا كان الكيان هو اختصاصي)
    ORDER BY r.created_at DESC  -- ترتيب التقييمات من الأحدث إلى الأقدم
";

// تنفيذ الاستعلام
$stmt = $conn->query($query);

// جلب كل التقييمات الناتجة عن الاستعلام وتحويلها إلى مصفوفة
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// حساب عدد التقييمات
$review_count = count($reviews);
?>


<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقييمات المرضى - المركز الصحي المتقدم</title>
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
    
    .reviews-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
    
    .review-card { 
        background: var(--card-bg);
        border-radius: 16px; 
        padding: 25px; 
        box-shadow: 0 4px 15px var(--card-shadow);
        display: flex; 
        flex-direction: column; 
        justify-content: space-between; 
    }
    
    .review-header { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        margin-bottom: 15px; 
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 10px; 
    }
    
    .rating-stars { color: #facc15; font-size: 1.2rem; }
    
    .rated-entity { 
        font-weight: 600; 
        color: var(--text-color);
        display: flex; 
        align-items: center; 
        gap: 8px; 
    }
    
    .review-comment { 
        line-height: 1.7; 
        color: var(--text-color);
        margin-bottom: 15px; 
        flex-grow: 1; 
    }
    
    .reviewer-info { 
        display: flex; 
        justify-content: space-between; 
        font-size: 0.9rem; 
        color: var(--muted-text);
    }
    
    .entity-type-badge { padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; }
    .badge-doctor { background: #dcfce7; color: #166534; }
    .badge-specialist { background: #fef3c7; color: #d97706; }
    .badge-center { background: #e0f2fe; color: #0ea5e9; }
    
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
        .reviews-grid { grid-template-columns: 1fr; }
    }
</style>
    <?php include 'includes/theme_logic.php'; ?>
    <?php include 'includes/sidebar.php' ?>
</head>
<body>
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">
                <span class="icon">⭐</span>
                تقييمات المرضى (<?= $review_count ?>)
            </h1>
        </div>

        <?php if ($review_count > 0): ?>
        <div class="reviews-grid">
            <?php foreach ($reviews as $review): ?>
            <div class="review-card">
                <div>
                    <div class="review-header">
                        <div class="rated-entity">
                            <span class="entity-type-badge badge-<?= $review['entity_type'] ?>">
                                <?= match($review['entity_type']) { 'doctor' => 'طبيب', 'specialist' => 'أخصائي', 'center' => 'المركز العام' } ?>
                            </span>
                             <?= htmlspecialchars($review['rated_entity_name']) ?>
                        </div>
                        <div class="rating-stars">
                            <?= str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']) ?>
                        </div>
                    </div>
                    <p class="review-comment">"<?= nl2br(htmlspecialchars($review['comment'] ?? 'لا يوجد تعليق مكتوب.')) ?>"</p>
                </div>
                <div class="reviewer-info">
                    <span>الناشر: <?= htmlspecialchars($review['patient_name']) ?></span>
                    <span>التاريخ: <?= date('Y-m-d', strtotime($review['created_at'])) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <h3>لا توجد تقييمات مرضى حاليًا</h3>
            <p>عندما يضيف المرضى تقييمات، ستظهر هنا.</p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>