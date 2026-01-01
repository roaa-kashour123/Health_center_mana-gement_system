-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 06, 2025 at 11:47 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `health_center_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `created_at`) VALUES
(1, 'admin', 'admin@healthcenter.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير النظام', '0955987546', '2025-11-18 16:38:29');

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_id` int NOT NULL,
  `author_type` enum('doctor','specialist') COLLATE utf8mb4_unicode_ci NOT NULL,
  `published_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `title`, `content`, `author_id`, `author_type`, `published_at`) VALUES
(1, 'أهمية النشاط البدني للقلب', 'النشاط البدني المنتظم يقلل من مخاطر الإصابة بأمراض القلب بنسبة تصل إلى 30%. نوصي بممارسة الرياضة لمدة 30 دقيقة يوميًا.', 1, 'doctor', '2025-11-18 18:08:14'),
(2, 'استراتيجيات التعامل مع التوحد في المنزل', 'الآباء هم الشريك الأساسي في علاج طفل التوحد. إليكم أهم الاستراتيجيات التي يمكن تطبيقها في المنزل لدعم تطور الطفل.', 2, 'specialist', '2025-11-18 18:08:14'),
(3, 'فهم طيف التوحد: دليل شامل للآباء', 'طيف التوحد (Autism Spectrum Disorder - ASD) هو اضطراب في النمو العصبي يؤثر على التواصل الاجتماعي والسلوك. يظهر عادة في السنوات الأولى من الحياة ويستمر مدى الحياة. التشخيص المبكر والتدخل العلاجي يمكن أن يحسن بشكل كبير من جودة حياة الطفل المصاب.', 1, 'doctor', '2025-11-18 18:12:16');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`, `icon`, `status`, `created_at`, `updated_at`) VALUES
(1, 'قسم الجلدية', 'تشخيص وعلاج الأمراض الجلدية والشعر والأظافر', '🧴', 'active', '2025-11-19 10:25:26', '2025-11-19 10:25:26'),
(2, 'قسم الليزر', 'علاجات الليزر للبشرة ', '⚡', 'active', '2025-11-19 10:25:26', '2025-11-19 10:26:39'),
(3, 'قسم التحاليل الطبية', 'إجراء كافة أنواع التحاليل المخبرية', '🧪', 'active', '2025-11-19 10:25:26', '2025-11-19 10:25:26'),
(4, 'قسم طب الأسرة', 'رعاية صحية شاملة لجميع أفراد العائلة', '👨‍👩‍👧‍👦', 'active', '2025-11-19 10:25:26', '2025-11-19 10:25:26'),
(5, 'قسم التغذية', 'استشارات غذائية وتخطيط أنظمة غذائية', '🥗', 'active', '2025-11-19 10:25:26', '2025-11-19 10:25:26'),
(6, 'قسم الصحة النفسية', 'دعم نفسي وعلاج اضطرابات الصحة العقلية', '🧠', 'active', '2025-11-19 10:25:26', '2025-11-19 10:25:26'),
(7, 'قسم العلاج الطبيعي', 'جلسات علاج طبيعي لإعادة التأهيل', '💪', 'active', '2025-11-19 10:25:26', '2025-11-19 10:25:26'),
(8, 'قسم طب الأسنان', 'خدمات طب الأسنان والعلاجات التجميلية', '🦷', 'active', '2025-11-19 10:25:26', '2025-11-19 10:25:26');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `specialization` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','rejected','suspended') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `gender` enum('male','female') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `full_name`, `email`, `phone`, `specialization`, `license_number`, `status`, `gender`, `created_at`, `updated_at`) VALUES
(1, 'د. أحمد محمد علي', 'ahmed.doctor@healthcenter.com', '0501234567', 'أمراض القلب', 'LIC-2025-001', 'suspended', 'male', '2025-11-18 17:12:13', '2025-11-18 17:20:52'),
(6, 'د. فاطمة حسن', 'fatima.doctor@healthcenter.com', '0507654321', 'طب الأسرة', 'LIC-2025-002', 'approved', 'female', '2025-11-18 17:20:05', '2025-11-18 17:26:09'),
(7, 'د. خالد سعيد', 'khalid.doctor@healthcenter.com', '0501122334', 'الجراحة العامة', 'LIC-2025-003', 'approved', 'male', '2025-11-18 17:20:05', '2025-11-18 17:26:11'),
(8, 'د. نورة عبد الله', 'nora.doctor@healthcenter.com', '0504455667', 'النساء والتوليد', 'LIC-2025-004', 'approved', 'female', '2025-11-18 17:20:05', '2025-11-18 17:26:13'),
(9, 'أحمد الخطيب', 'ahmad@gmail.com', '0966521457', 'أمراض الكلى', '', 'pending', 'male', '2025-11-18 17:27:50', '2025-11-18 17:27:50');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `full_name`, `email`, `phone`, `dob`, `gender`, `address`, `created_at`) VALUES
(1, 'محمد أحمد علي', 'mohammed.patient@example.com', '0501122334', '1985-03-15', 'male', 'دمشق، حي المزة', '2025-11-18 17:56:56'),
(2, 'فاطمة سليمان', 'fatima.patient@example.com', '0502233445', '1990-07-22', 'female', 'دمشق العباسيين', '2025-11-18 17:56:56'),
(3, 'خالد ناصر', 'khalid.patient@example.com', '0503344556', '1978-11-05', 'male', 'دمشق العمارة', '2025-11-18 17:56:56'),
(4, 'نورة فهد', 'nora.patient@example.com', '0504455667', '2000-01-30', 'female', 'دمشق، حي القيمرية، شارع التخصصي', '2025-11-18 17:56:56'),
(5, 'عبدالله سعد', 'abdullah.patient@example.com', '0505566778', '1983-09-12', 'male', 'دمشق، البرامكة', '2025-11-18 17:56:56');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `entity_id` int NOT NULL,
  `entity_type` enum('doctor','specialist','center') COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` int NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `patient_id`, `entity_id`, `entity_type`, `rating`, `comment`, `created_at`) VALUES
(1, 1, 6, 'doctor', 5, 'تجربة ممتازة.', '2025-11-29 17:01:12'),
(2, 2, 3, 'specialist', 4, 'أخصائي نفسي جيد.', '2025-11-29 17:01:12');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `key_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`key_name`, `value`, `description`, `updated_at`) VALUES
('center_name_ar', 'المركز الصحي المتقدم', 'الاسم الرسمي للمركز المستخدم في الرأسية والفواتير', '2025-12-01 10:22:49'),
('default_theme', 'light', 'الوضع الافتراضي للواجهة للمستخدمين الجدد', '2025-12-01 11:02:26'),
('invoice_format', 'A4_Standard', 'تنسيق طباعة الفواتير (A4_Standard, Thermal_Small)', '2025-12-01 10:22:49');

-- --------------------------------------------------------

--
-- Table structure for table `specialists`
--

CREATE TABLE `specialists` (
  `id` int NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `field` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `license_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','rejected','suspended') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `gender` enum('male','female') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `specialists`
--

INSERT INTO `specialists` (`id`, `full_name`, `email`, `phone`, `field`, `license_number`, `status`, `gender`, `created_at`, `updated_at`) VALUES
(1, 'أ. ليلى أحمد', 'layla.specialist@healthcenter.com', '0501122334', 'أخصائية ليزر', 'LIC-S-2025-001', 'pending', 'female', '2025-11-18 17:33:10', '2025-11-18 17:33:10'),
(2, 'د. سارة محمد', 'sara.specialist@healthcenter.com', '0502233445', 'أخصائية بشرة', 'LIC-S-2025-002', 'approved', 'female', '2025-11-18 17:33:10', '2025-11-18 17:33:10'),
(3, 'أ. خالد علي', 'khalid.specialist@healthcenter.com', '0503344556', 'أخصائي نفسي', 'LIC-S-2025-003', 'approved', 'male', '2025-11-18 17:33:10', '2025-11-18 17:33:58'),
(4, 'د. نورة فهد', 'nora.specialist@healthcenter.com', '0504455667', 'أخصائية تغذية', 'LIC-S-2025-004', 'approved', 'female', '2025-11-18 17:33:10', '2025-11-18 17:33:10'),
(5, 'أ. فهد سليمان', 'fahd.specialist@healthcenter.com', '0505566778', 'أخصائي علاج طبيعي', 'LIC-S-2025-005', 'suspended', 'male', '2025-11-18 17:33:10', '2025-11-18 17:33:10');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','suspended') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `full_name`, `email`, `phone`, `position`, `status`, `created_at`, `updated_at`) VALUES
(1, 'خالد محمد', 'khalid.staff@healthcenter.com', '0501122334', 'موظف أمن', 'active', '2025-11-18 17:48:16', '2025-11-18 17:48:16'),
(2, 'سارة أحمد', 'sara.staff@healthcenter.com', '0502233445', 'موظفة استقبال', 'active', '2025-11-18 17:48:16', '2025-11-18 17:48:16'),
(3, 'فهد علي', 'fahd.staff@healthcenter.com', '0503344556', 'موظف IT', 'active', '2025-11-18 17:48:16', '2025-11-18 17:49:36'),
(4, 'منى سليمان', 'mona.staff@healthcenter.com', '0504455667', 'مساعد طبي', 'active', '2025-11-18 17:48:16', '2025-11-18 17:48:16'),
(5, 'ناصر عبد الله', 'nasser.staff@healthcenter.com', '0505566778', 'عامل نظافة', 'active', '2025-11-18 17:48:16', '2025-11-18 17:48:16'),
(6, 'سماهر سعيد', 'smaher@gmail.com', '0966325879', 'موظفة إدخال بيانات', 'suspended', '2025-11-18 17:49:15', '2025-11-18 17:49:39');

-- --------------------------------------------------------

--
-- Table structure for table `surveys`
--

CREATE TABLE `surveys` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_by` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `surveys`
--

INSERT INTO `surveys` (`id`, `title`, `description`, `created_by`, `created_at`) VALUES
(1, 'استبيان رضا المرضى', 'يساعدنا هذا الاستبيان في تحسين الخدمات', 1, '2025-11-18 18:30:27');

-- --------------------------------------------------------

--
-- Table structure for table `survey_responses`
--

CREATE TABLE `survey_responses` (
  `id` int NOT NULL,
  `survey_id` int NOT NULL,
  `patient_id` int DEFAULT NULL,
  `score` int DEFAULT NULL,
  `response_text` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `survey_responses`
--

INSERT INTO `survey_responses` (`id`, `survey_id`, `patient_id`, `score`, `response_text`, `created_at`) VALUES
(1, 1, 4, 9, 'الخدمة كانت سريعة جداً.', '2025-11-29 17:01:36'),
(2, 1, 5, 8, 'الموظفون ودودون.', '2025-11-29 17:01:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_articles_author` (`author_type`,`author_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `license_number` (`license_number`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`key_name`);

--
-- Indexes for table `specialists`
--
ALTER TABLE `specialists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `license_number` (`license_number`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `surveys`
--
ALTER TABLE `surveys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `survey_responses`
--
ALTER TABLE `survey_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `survey_id` (`survey_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `specialists`
--
ALTER TABLE `specialists`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `surveys`
--
ALTER TABLE `surveys`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `survey_responses`
--
ALTER TABLE `survey_responses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `surveys`
--
ALTER TABLE `surveys`
  ADD CONSTRAINT `surveys_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
