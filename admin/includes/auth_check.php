<?php
session_start();

// التحقق من أن المشرف مسجل الدخول
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
// يمكن هنا أيضاً إضافة ملف Database.php لتجنب تكراره
// require_once '../config/Database.php';
?>