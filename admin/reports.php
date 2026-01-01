<?php
require_once 'includes/auth_check.php';
$current_page_name = 'reports';

require_once '../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

// معالجة طلب التصدير إلى CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    // جلب جميع الإحصائيات
    $reports = [
        'ملخص عام' => [
            'إجمالي الأطباء' => $conn->query("SELECT COUNT(*) FROM doctors")->fetchColumn(),
            'الأطباء المعتمدون' => $conn->query("SELECT COUNT(*) FROM doctors WHERE status = 'approved'")->fetchColumn(),
            'الأطباء بانتظار الموافقة' => $conn->query("SELECT COUNT(*) FROM doctors WHERE status = 'pending'")->fetchColumn(),
            'إجمالي الأخصائيين' => $conn->query("SELECT COUNT(*) FROM specialists")->fetchColumn(),
            'الأخصائيون المعتمدون' => $conn->query("SELECT COUNT(*) FROM specialists WHERE status = 'approved'")->fetchColumn(),
            'الأخصائيون بانتظار الموافقة' => $conn->query("SELECT COUNT(*) FROM specialists WHERE status = 'pending'")->fetchColumn(),
            'إجمالي الموظفين' => $conn->query("SELECT COUNT(*) FROM staff")->fetchColumn(),
            'الموظفون النشطون' => $conn->query("SELECT COUNT(*) FROM staff WHERE status = 'active'")->fetchColumn(),
            'الموظفون المعلقون' => $conn->query("SELECT COUNT(*) FROM staff WHERE status = 'suspended'")->fetchColumn(),
            'إجمالي المرضى' => $conn->query("SELECT COUNT(*) FROM patients")->fetchColumn(),
            'إجمالي المقالات' => $conn->query("SELECT COUNT(*) FROM articles")->fetchColumn(),
            'إجمالي الاستبيانات' => $conn->query("SELECT COUNT(*) FROM surveys")->fetchColumn()
        ],
        'تفاصيل الموظفين' => [],
        'تفاصيل المرضى' => []
    ];
    
    // إضافة تفاصيل الموظفين
    $staff_stmt = $conn->query("SELECT full_name, position, status FROM staff ORDER BY created_at DESC LIMIT 50");
    while ($row = $staff_stmt->fetch(PDO::FETCH_ASSOC)) {
        $reports['تفاصيل الموظفين'][] = $row;
    }
    
    // إضافة تفاصيل المرضى
    $patients_stmt = $conn->query("SELECT full_name, gender, DATE(dob) as dob, phone FROM patients ORDER BY created_at DESC LIMIT 50");
    while ($row = $patients_stmt->fetch(PDO::FETCH_ASSOC)) {
        $reports['تفاصيل المرضى'][] = $row;
    }
    
    // إعدادات التصدير
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="takrir_nizam_al_markaz_al_sihy_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // BOM للترميز العربي
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // تصدير الملخص العام
    fputcsv($output, ['=== تقرير نظام المركز الصحي ==='], ',', '"');
    fputcsv($output, ['تاريخ التوليد: ' . date('Y-m-d H:i:s')], ',', '"');
    fputcsv($output, [''], ',', '"');
    fputcsv($output, ['ملخص عام'], ',', '"');
    fputcsv($output, [''], ',', '"');
    fputcsv($output, ['الفئة', 'القيمة'], ',', '"');
    
    foreach ($reports['ملخص عام'] as $category => $value) {
        fputcsv($output, [$category, $value], ',', '"');
    }
    
    // تصدير تفاصيل الموظفين
    fputcsv($output, [''], ',', '"');
    fputcsv($output, ['تفاصيل الموظفين (آخر 50 موظف)'], ',', '"');
    fputcsv($output, [''], ',', '"');
    
    if (!empty($reports['تفاصيل الموظفين'])) {
        fputcsv($output, ['الاسم الكامل', 'المسمى الوظيفي', 'الحالة'], ',', '"');
        foreach ($reports['تفاصيل الموظفين'] as $staff) {
            fputcsv($output, [$staff['full_name'], $staff['position'], $staff['status']], ',', '"');
        }
    } else {
        fputcsv($output, ['لا توجد بيانات موظفين'], ',', '"');
    }
    
    // تصدير تفاصيل المرضى
    fputcsv($output, [''], ',', '"');
    fputcsv($output, ['تفاصيل المرضى (آخر 50 مريض)'], ',', '"');
    fputcsv($output, [''], ',', '"');
    
    if (!empty($reports['تفاصيل المرضى'])) {
        fputcsv($output, ['الاسم الكامل', 'الجنس', 'تاريخ الميلاد', 'رقم الجوال'], ',', '"');
        foreach ($reports['تفاصيل المرضى'] as $patient) {
            fputcsv($output, [
                $patient['full_name'], 
                $patient['gender'], 
                $patient['dob'], 
                $patient['phone'] ?? 'غير متوفر'
            ], ',', '"');
        }
    } else {
        fputcsv($output, ['لا توجد بيانات مرضى'], ',', '"');
    }
    
    fclose($output);
    exit;
}

// جلب الإحصائيات للعرض في الواجهة
$stats = [
    'doctors_total' => $conn->query("SELECT COUNT(*) FROM doctors")->fetchColumn(),
    'doctors_approved' => $conn->query("SELECT COUNT(*) FROM doctors WHERE status = 'approved'")->fetchColumn(),
    'doctors_pending' => $conn->query("SELECT COUNT(*) FROM doctors WHERE status = 'pending'")->fetchColumn(),
    'specialists_total' => $conn->query("SELECT COUNT(*) FROM specialists")->fetchColumn(),
    'specialists_approved' => $conn->query("SELECT COUNT(*) FROM specialists WHERE status = 'approved'")->fetchColumn(),
    'specialists_pending' => $conn->query("SELECT COUNT(*) FROM specialists WHERE status = 'pending'")->fetchColumn(),
    'staff_total' => $conn->query("SELECT COUNT(*) FROM staff")->fetchColumn(),
    'staff_active' => $conn->query("SELECT COUNT(*) FROM staff WHERE status = 'active'")->fetchColumn(),
    'staff_suspended' => $conn->query("SELECT COUNT(*) FROM staff WHERE status = 'suspended'")->fetchColumn(),
    'patients_total' => $conn->query("SELECT COUNT(*) FROM patients")->fetchColumn(),
    'articles_total' => $conn->query("SELECT COUNT(*) FROM articles")->fetchColumn(),
    'surveys_total' => $conn->query("SELECT COUNT(*) FROM surveys")->fetchColumn()
];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقارير النظام - المركز الصحي المتقدم</title>
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

    .export-btn {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: transform 0.2s;
        font-size: 1rem;
    }

    .export-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
    }

    /* بطاقات الإحصائيات */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 12px var(--card-shadow);
        text-align: center;
        transition: transform 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
    }

    .stat-icon {
        font-size: 28px;
        margin-bottom: 12px;
        color: #0ea5e9;
    }

    .stat-title {
        font-size: 1rem;
        color: var(--muted-text);
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--text-color);
    }

    /* أقسام التقارير */
    .report-sections {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
    }

    .report-card {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 12px var(--card-shadow);
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--border-color);
    }

    .section-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--text-color);
    }

    .section-icon {
        font-size: 24px;
        color: #0ea5e9;
    }

    .stats-table {
        width: 100%;
        border-collapse: collapse;
    }

    .stats-table th {
        text-align: right;
        padding: 10px;
        background: var(--border-color);
        font-weight: 600;
        color: var(--text-color);
        font-size: 0.95rem;
    }

    .stats-table td {
        padding: 10px;
        text-align: right;
        border-bottom: 1px solid var(--border-color);
        font-size: 0.95rem;
        color: var(--text-color);
    }

    .stats-table tr:last-child td {
        border-bottom: none;
    }

    .stat-number {
        font-weight: 700;
        color: #0ea5e9;
    }

    /* قسم المعلومات */
    .info-section {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 25px;
        margin-top: 25px;
        box-shadow: 0 4px 12px var(--card-shadow);
    }

    .info-title {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 15px;
        color: var(--text-color);
    }

    .info-content {
        color: var(--muted-text);
        line-height: 1.6;
    }

    .info-list {
        padding-right: 20px;
    }

    .info-list li {
        margin-bottom: 10px;
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
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .report-sections {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
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
            <div class="page-title
">
                <span class="icon">📊</span>
                <span>تقارير نظام المركز الصحي</span>
            </div>
            <a href="reports.php?export=csv" class="export-btn
">
                <span>⬇️</span>
                <span>تصدير إلى CSV</span>
            </a>
        </div>
        <!-- بطاقات الإحصائيات -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👨‍⚕️</div>
                <div class="stat-title">إجمالي الأطباء</div>
                <div class="stat-value"><?php echo $stats['doctors_total']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-title">الأطباء المعتمدون</div>
                <div class="stat-value"><?php echo $stats['doctors_approved']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⏳</div>
                <div class="stat-title">الأطباء بانتظار الموافقة</div>
                <div class="stat-value"><?php echo $stats['doctors_pending']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">👩‍⚕️</div>
                <div class="stat-title">إجمالي الأخصائيين</div>
                <div class="stat-value"><?php echo $stats['specialists_total']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-title">الأخصائيون المعتمدون</div>
                <div class="stat-value"><?php echo $stats['specialists_approved']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⏳</div>
                <div class="stat-title
">الأخصائيون بانتظار الموافقة</div>
                <div class="stat-value"><?php echo $stats['specialists_pending']; ?></div>
            </div>  
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-title">إجمالي الموظفين</div>
                <div class="stat-value"><?php echo $stats['staff_total']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🟢</div>
                <div class="stat-title">الموظفون النشطون</div>
                <div class="stat-value"><?php echo $stats['staff_active']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🔴</div>
                <div class="stat-title">الموظفون المعلقون</div>
                <div class="stat-value"><?php echo $stats['staff_suspended']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🏥</div>
                <div class="stat-title">إجمالي المرضى</div>
                <div class="stat-value"><?php echo $stats['patients_total']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📝</div>
                <div class="stat-title">إجمالي المقالات</div>
                <div class="stat-value"><?php echo $stats['articles_total']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📋</div>
                <div class="stat-title">إجمالي الاستبيانات</div>
                <div class="stat-value"><?php echo $stats['surveys_total']; ?></div>
            </div>
        </div>
        <!-- أقسام التقارير -->
        <div class="report-sections">
            <!-- ملخص عام -->
            <div class="report-card">
                <div class="section-header">
                    <span class="section-icon">📈</span>
                    <span class="section-title">ملخص عام</span>
                </div>
                <table class="stats-table">
                    <tr>
                        <th>الفئة</th>
                        <th>القيمة</th>
                    </tr>
                    <?php foreach ($stats as $key => $value): ?>
                    <tr>
                        <td><?php echo str_replace('_', ' ', ucfirst($key)); ?></td>
                        <td class="stat-number"><?php echo $value; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <!-- تفاصيل الموظفين -->
            <div class="report-card">
                <div class="section-header">
                    <span class="section-icon">👥</span>
                    <span class="section-title">تفاصيل الموظفين (الموظفين الجدد)</span>
                </div>
                <table class="stats-table">
                    <tr>
                        <th>الاسم الكامل</th>
                        <th>المسمى الوظيفي</th>
                        <th>الحالة</th>
                    </tr>
                    <?php
                    $staff_stmt = $conn->query("SELECT full_name, position, status FROM staff ORDER BY created_at DESC LIMIT 50");
                    while ($staff = $staff_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($staff['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($staff['position']); ?></td>
                        <td><?php echo htmlspecialchars($staff['status']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
            <!-- تفاصيل المرضى -->
            <div class="report-card">
                <div class="section-header">
                    <span class="section-icon">🏥</span>
                    <span class="section-title">تفاصيل المرضى (المرضى الجدد)</span>
                </div>
                <table class="stats-table">
                    <tr>
                        <th>الاسم الكامل</th>
                        <th>الجنس</th>
                        <th>تاريخ الميلاد</th>
                        <th>رقم الجوال</th>
                    </tr>
                    <?php
                    $patients_stmt = $conn->query("SELECT full_name, gender, DATE(dob) as dob, phone FROM patients ORDER BY created_at DESC LIMIT 50");
                    while ($patient = $patients_stmt->fetch(PDO::FETCH_ASSOC)): ?> 
                    <tr>
                        <td><?php echo htmlspecialchars($patient['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($patient['gender']); ?></td>
                        <td><?php echo htmlspecialchars($patient['dob']); ?></td>
                        <td><?php echo htmlspecialchars($patient['phone'] ?? 'غير متوفر'); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>
</body>
</html>

