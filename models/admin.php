<?php
// استدعاء ملف الاتصال بقاعدة البيانات
require_once '../config/Database.php';

// تعريف كلاس "Admin" الذي يتعامل مع العمليات الخاصة بالمديرين في النظام
class Admin {
    // تعريف المتغيرات الخاصة بالاتصال بقاعدة البيانات والطاولة
    private $conn;    // المتغير الذي يخزن الاتصال بقاعدة البيانات
    private $table = 'admins'; // اسم جدول المديرين في قاعدة البيانات

    // خصائص الكلاس التي سيتم تخزين بيانات المدير فيها
    public $id;       // معرف المدير
    public $username; // اسم المستخدم
    public $email;    // البريد الإلكتروني
    public $password; // كلمة المرور
    public $full_name; // الاسم الكامل
    public $phone;    // رقم الهاتف

    // دالة البناء (Constructor) التي تنشئ الاتصال بقاعدة البيانات عند إنشاء كائن من الكلاس
    public function __construct() {
        // إنشاء كائن من كلاس Database والحصول على الاتصال
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // ✅ دالة لتسجيل الدخول باستخدام البريد الإلكتروني وكلمة المرور
    public function loginByEmail($email, $password) {
        // الاستعلام لاختيار بيانات المدير بناءً على البريد الإلكتروني
        $query = "SELECT id, username, email, password, full_name FROM " . $this->table . " WHERE email = :email LIMIT 1";

        // تحضير الاستعلام لتنفيذه
        $stmt = $this->conn->prepare($query);

        // ربط المتغيرات للاستعلام (البريد الإلكتروني)
        $stmt->bindParam(':email', $email);

        // تنفيذ الاستعلام
        $stmt->execute();

        // التحقق إذا كان يوجد نتائج (أي مدير بهذا البريد الإلكتروني)
        if ($stmt->rowCount() > 0) {
            // جلب السطر الذي يحتوي على البيانات
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // التحقق من تطابق كلمة المرور المدخلة مع المخزنة في قاعدة البيانات
            if (password_verify($password, $row['password'])) {
                // إذا كانت كلمة المرور صحيحة، تعيين القيم إلى خصائص الكلاس
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->email = $row['email'];
                $this->full_name = $row['full_name'];

                // إرجاع true للدلالة على أن عملية تسجيل الدخول كانت ناجحة
                return true;
            }
        }
        // إرجاع false إذا لم تكن كلمة المرور صحيحة أو البريد الإلكتروني غير موجود
        return false;
    }

    // دالة لتحديث بيانات الملف الشخصي للمدير
    public function updateProfile($data) {
        // الاستعلام لتحديث بيانات المدير
        $query = "UPDATE " . $this->table . " 
                  SET full_name = :full_name, email = :email, phone = :phone 
                  WHERE id = :id";

        // تحضير الاستعلام لتنفيذه
        $stmt = $this->conn->prepare($query);

        // ربط المتغيرات للاستعلام (الاسم الكامل، البريد الإلكتروني، رقم الهاتف، والمعرف)
        $stmt->bindParam(':full_name', $data['full_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':id', $data['id']);

        // تنفيذ الاستعلام وتحديث البيانات في قاعدة البيانات
        return $stmt->execute();
    }
}
?>
