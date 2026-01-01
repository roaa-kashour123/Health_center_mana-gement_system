<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once '../models/Admin.php';

$message = '';
if ($_POST) {
    $admin = new Admin();
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($admin->loginByEmail($email, $password)) {
        $_SESSION['admin_id'] = $admin->id;
        $_SESSION['admin_name'] = $admin->full_name;
        header('Location: dashboard.php');
        exit;
    } else {
        $message = "البريد الإلكتروني أو كلمة المرور غير صحيحة.";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - لوحة تحكم المركز الصحي</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #e0f2fe 0%, #f0fdf4 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            max-width: 900px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .branding {
            background: linear-gradient(135deg, #0ea5e9 0%, #059669 100%);
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .branding h1 {
            font-size: 2.2rem;
            margin: 20px 0 10px;
            font-weight: 700;
        }

        .branding p {
            font-size: 1.1rem;
            opacity: 0.9;
            line-height: 1.7;
            max-width: 300px;
        }

        .medical-icons {
            display: flex;
            gap: 25px;
            margin-top: 30px;
        }

        .icon {
            background: rgba(255, 255, 255, 0.2);
            width: 70px;
            height: 70px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .form-section {
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-section h2 {
            color: #0f172a;
            margin-bottom: 10px;
            font-size: 1.8rem;
        }

        .form-section p {
            color: #64748b;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #334155;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px 14px 50px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: border-color 0.3s;
            direction: ltr;
            text-align: right;
        }

        .form-group input:focus {
            outline: none;
            border-color: #0ea5e9;
        }

        .form-icon {
            position: absolute;
            left: 16px;
            top: 42px;
            color: #94a3b8;
            font-size: 18px;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(14, 165, 233, 0.4);
        }

        .error {
            background: #fee2e2;
            color: #dc2626;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 25px;
            font-weight: 500;
            border: 1px solid #fecaca;
        }

        .back-home {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #0ea5e9;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.2s;
        }

        .back-home:hover {
            opacity: 0.8;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
                max-width: 500px;
            }

            .branding {
                padding: 30px 20px;
            }

            .form-section {
                padding: 40px 30px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="branding">
            <div style="font-size: 48px;">✚</div>
            <h1>المركز الصحي الشامل</h1>
            <p>نظام إدارة متكامل لتقديم أفضل الخدمات الطبية بمعايير عالمية</p>
            
            <div class="medical-icons">
                <div class="icon">⚕️</div>
                <div class="icon">💉</div>
                <div class="icon">🩺</div>
                <div class="icon">💊</div>
            </div>
        </div>

        <div class="form-section">
            <h2>مرحباً بك مرة أخرى</h2>
            <p>سجّل الدخول باستخدام بريدك الإلكتروني</p>
            
            <?php if ($message): ?>
                <div class="error"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="email">البريد الإلكتروني</label>
                    <span class="form-icon">📧</span>
                    <input type="email" id="email" name="email" placeholder="example@healthcenter.com" required>
                </div>
                
                <div class="form-group">
                    <label for="password">كلمة المرور</label>
                    <span class="form-icon">🔒</span>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>
                
                <button type="submit" class="btn-login">تسجيل الدخول</button>
            </form>
            
            <a href="../index.php" class="back-home">← العودة إلى الصفحة الرئيسية</a>
        </div>
    </div>
</body>
</html>