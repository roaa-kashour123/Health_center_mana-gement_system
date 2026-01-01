<?php
function is_active($target, $current) {
    return (isset($current) && $target === $current) ? 'active' : '';
}
?>

<style>
    /* CSS الشريط الجانبي والميزات المتعلقة به (تم استخلاصه من ملفات Admin) */
    .sidebar {
        width: 260px;
        /* استخدام متغيرات CSS Theme */
        background: var(--sidebar-bg); 
        color: var(--sidebar-text);
        height: 100vh;
        position: fixed;
        overflow-y: auto;
        box-shadow: 3px 0 15px rgba(0,0,0,0.1);
        transition: background 0.3s ease;
    }

    .logo {
        padding: 25px 20px;
        font-size: 1.6rem;
        font-weight: 700;
        border-bottom: 1px solid rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .logo-icon {
        font-size: 28px;
    }

    .sidebar ul {
        list-style: none;
        padding: 20px 0;
    }

    .sidebar ul li {
        padding: 0 15px;
    }

    .sidebar ul li a {
        color: var(--sidebar-text);
        text-decoration: none;
        display: flex;
        align-items: center;
        padding: 14px 18px;
        border-radius: 12px;
        margin: 6px 0;
        transition: all 0.3s ease;
        font-weight: 500;
        gap: 14px;
    }

    .sidebar ul li a:hover, .sidebar ul li a.active {
        background: rgba(255,255,255,0.15);
        transform: translateX(4px);
    }

    .sidebar ul li a.logout {
        margin-top: 30px;
        background: rgba(239, 68, 68, 0.3);
        color: #fee2e2;
    }
    
    /* Responsive for small screens */
    @media (max-width: 992px) {
        .sidebar {
            width: 80px;
        }
        .sidebar .logo span, .sidebar ul li a span:nth-child(2) {
            display: none;
        }
        .sidebar .logo {
            justify-content: center;
            padding: 25px 0;
        }
        .sidebar ul li a {
            justify-content: center;
            padding: 16px;
        }
        .main-content {
            margin-right: 80px;
        }
    }
</style>

<div class="sidebar">
    <div class="logo">
        <div class="logo-icon">✚</div>
        <span>المركز الصحي</span>
    </div>
    <ul>
        <li><a href="dashboard.php" class="<?= is_active('dashboard', $current_page_name) ?>"><span>🏠</span> <span>الرئيسية</span></a></li>
        <li><a href="doctors.php" class="<?= is_active('doctors', $current_page_name) ?>"><span>👨‍⚕️</span> <span>إدارة الأطباء</span></a></li>
        <li><a href="specialists.php" class="<?= is_active('specialists', $current_page_name) ?>"><span>👩‍⚕️</span> <span>إدارة الأخصائيين</span></a></li>
        <li><a href="staff.php" class="<?= is_active('staff', $current_page_name) ?>"><span>👥</span> <span>إدارة الموظفين</span></a></li>
        <li><a href="patients.php" class="<?= is_active('patients', $current_page_name) ?>"><span>🏥</span> <span>قائمة المرضى</span></a></li>
        <li><a href="articles.php" class="<?= is_active('articles', $current_page_name) ?>"><span>📝</span> <span>المقالات</span></a></li>
        <li><a href="surveys.php" class="<?= is_active('surveys', $current_page_name) ?>"><span>📋</span> <span>الاستبيانات</span></a></li>
        <li><a href="reports.php" class="<?= is_active('reports', $current_page_name) ?>"><span>📊</span> <span>التقارير</span></a></li>
        <li><a href="reviews.php" class="<?= is_active('reviews', $current_page_name) ?>"><span>⭐</span> <span>تقييمات المرضى</span></a></li>
        <li><a href="settings.php" class="<?= is_active('settings', $current_page_name) ?>"><span>⚙️</span> <span>إعدادات النظام</span></a></li>
        <li><a href="profile.php" class="<?= is_active('profile', $current_page_name) ?>"><span>👤</span> <span>ملفي الشخصي</span></a></li>
        <li><a href="logout.php" class="logout"><span>🚪</span> <span>تسجيل الخروج</span></a></li>
    </ul>
</div>