<?php
// تعريف كلاس "Database" لإدارة الاتصال بقاعدة البيانات
class Database {
    // تعريف المتغيرات الخاصة بالاتصال بقاعدة البيانات
    private $host = 'localhost';       // المضيف (الكمبيوتر أو الخادم الذي يستضيف قاعدة البيانات)
    private $db_name = 'health_center_db'; // اسم قاعدة البيانات
    private $username = 'root';        // اسم المستخدم (هنا يستخدم الجذر "root" كمستخدم قاعدة البيانات)
    private $password = '';            // كلمة المرور (مفرغة هنا، قد تختلف حسب إعدادات الخادم)
    private $conn;                     // متغير لتخزين الاتصال بقاعدة البيانات

    // دالة لاسترجاع الاتصال بقاعدة البيانات
    public function getConnection() {
        // تعيين الاتصال كـ null في البداية
        $this->conn = null;

        // محاولة الاتصال بقاعدة البيانات باستخدام PDO (PHP Data Objects)
        try {
            // إنشاء الاتصال بقاعدة البيانات باستخدام تفاصيل الاتصال المعينة
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4", // الاتصال باستخدام معايير الـ UTF-8
                $this->username,    // اسم المستخدم
                $this->password     // كلمة المرور
            );
            
            // تعيين وضع الخطأ للتعامل مع الاستثناءات بشكل صحيح (عرض رسالة مفصلة في حالة حدوث خطأ)
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch(PDOException $e) {
            // في حالة حدوث استثناء، يتم إيقاف السكربت و عرض رسالة الخطأ
            die("خطأ في الاتصال: " . $e->getMessage());
        }

        // إرجاع الاتصال المفتوح مع قاعدة البيانات
        return $this->conn;
    }
}
?>
