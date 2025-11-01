-- ============================================
-- محتوى تجريبي لمنصة FiqhLearning
-- Demo Content for FiqhLearning Platform
-- ============================================
-- الإصدار: 1.0.0
-- التاريخ: 2024-11-01
-- ============================================

-- تعطيل فحص المفاتيح الأجنبية مؤقتاً
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- ============================================
-- 1. التصنيفات (Taxonomies)
-- ============================================

-- المواد (Subjects)
INSERT IGNORE INTO `wp_terms` (`term_id`, `name`, `slug`, `term_group`) VALUES
(100, 'الفقه', 'fiqh', 0),
(101, 'الحديث', 'hadith', 0),
(102, 'التفسير', 'tafsir', 0),
(103, 'العقيدة', 'aqeedah', 0),
(104, 'اللغة العربية', 'arabic-language', 0);

INSERT IGNORE INTO `wp_term_taxonomy` (`term_taxonomy_id`, `term_id`, `taxonomy`, `description`, `parent`, `count`) VALUES
(100, 100, 'fiqh_subject', '', 0, 1),
(101, 101, 'fiqh_subject', '', 0, 0),
(102, 102, 'fiqh_subject', '', 0, 0),
(103, 103, 'fiqh_subject', '', 0, 0),
(104, 104, 'fiqh_subject', '', 0, 0);

-- السنوات (Years)
INSERT IGNORE INTO `wp_terms` (`term_id`, `name`, `slug`, `term_group`) VALUES
(200, 'السنة الأولى', 'year-1', 0),
(201, 'السنة الثانية', 'year-2', 0),
(202, 'السنة الثالثة', 'year-3', 0),
(203, 'السنة الرابعة', 'year-4', 0);

INSERT IGNORE INTO `wp_term_taxonomy` (`term_taxonomy_id`, `term_id`, `taxonomy`, `description`, `parent`, `count`) VALUES
(200, 200, 'fiqh_year', '', 0, 1),
(201, 201, 'fiqh_year', '', 0, 0),
(202, 202, 'fiqh_year', '', 0, 0),
(203, 203, 'fiqh_year', '', 0, 0);

-- الفصول (Semesters)
INSERT IGNORE INTO `wp_terms` (`term_id`, `name`, `slug`, `term_group`) VALUES
(300, 'الفصل الأول', 'semester-1', 0),
(301, 'الفصل الثاني', 'semester-2', 0);

INSERT IGNORE INTO `wp_term_taxonomy` (`term_taxonomy_id`, `term_id`, `taxonomy`, `description`, `parent`, `count`) VALUES
(300, 300, 'fiqh_semester', '', 0, 1),
(301, 301, 'fiqh_semester', '', 0, 0);

-- أنواع المقررات (Course Types)
INSERT IGNORE INTO `wp_terms` (`term_id`, `name`, `slug`, `term_group`) VALUES
(400, 'أساسي', 'core', 0),
(401, 'تكميلي', 'supplementary', 0),
(402, 'إثرائي', 'enrichment', 0);

INSERT IGNORE INTO `wp_term_taxonomy` (`term_taxonomy_id`, `term_id`, `taxonomy`, `description`, `parent`, `count`) VALUES
(400, 400, 'fiqh_course_type', '', 0, 1),
(401, 401, 'fiqh_course_type', '', 0, 0),
(402, 402, 'fiqh_course_type', '', 0, 0);

-- ============================================
-- 2. الدفعات (Batches)
-- ============================================

INSERT INTO `wp_fiqh_batches` (`id`, `name`, `description`, `start_date`, `end_date`, `status`, `created_at`) VALUES
(1, 'دفعة 2024-2025', 'دفعة تجريبية للعام الدراسي 2024-2025', '2024-09-01', '2025-06-30', 'active', NOW());

-- ============================================
-- 3. المقرر التجريبي (Course)
-- ============================================

INSERT INTO `wp_posts` (`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `post_name`, `post_type`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`) VALUES
(1000, 1, NOW(), NOW(),
'<p>مقرر شامل في فقه العبادات على المذهب المالكي، يشمل دراسة الطهارة والصلاة والزكاة والصيام والحج.</p>
<h3>أهداف المقرر:</h3>
<ul>
<li>فهم أحكام الطهارة والصلاة بالتفصيل</li>
<li>معرفة أحكام الزكاة والصوم</li>
<li>دراسة مناسك الحج والعمرة</li>
<li>التطبيق العملي للأحكام الفقهية</li>
</ul>
<h3>محتوى المقرر:</h3>
<ol>
<li>باب الطهارة</li>
<li>باب الصلاة</li>
<li>باب الزكاة</li>
<li>باب الصيام</li>
<li>باب الحج</li>
</ol>',
'فقه العبادات - المذهب المالكي',
'دراسة شاملة لفقه العبادات على المذهب المالكي من الطهارة إلى الحج',
'publish', 'fiqh-ibadat', 'fiqh_course', '', '', NOW(), NOW());

-- Meta Data للمقرر
INSERT INTO `wp_postmeta` (`post_id`, `meta_key`, `meta_value`) VALUES
(1000, '_fiqh_course_book_url', 'https://archive.org/download/FP22640/22640.pdf'),
(1000, '_fiqh_course_duration', '12 أسبوع'),
(1000, '_fiqh_course_teacher_id', '1'),
(1000, '_fiqh_course_level', 'مبتدئ'),
(1000, '_fiqh_course_credits', '3');

-- ربط المقرر بالتصنيفات
INSERT INTO `wp_term_relationships` (`object_id`, `term_taxonomy_id`) VALUES
(1000, 100), -- الفقه
(1000, 200), -- السنة الأولى
(1000, 300), -- الفصل الأول
(1000, 400); -- أساسي

-- ============================================
-- 4. الدروس (Lessons)
-- ============================================

-- الدرس 1
INSERT INTO `wp_posts` (`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `post_name`, `post_type`) VALUES
(1001, 1, NOW(), NOW(),
'<h2>مقدمة في الطهارة</h2>
<p>الطهارة في اللغة: النظافة والنزاهة من الأدناس، حسية كانت أو معنوية.</p>
<p>وفي الاصطلاح الشرعي: رفع الحدث وإزالة النجس.</p>
<h3>أقسام المياه</h3>
<p>المياه في الفقه المالكي تنقسم إلى:</p>
<ol>
<li><strong>الماء المطلق:</strong> وهو الطهور الذي يرفع الحدث ويزيل النجس</li>
<li><strong>الماء المستعمل:</strong> وهو الذي انفصل عن الأعضاء بعد استعماله في الطهارة</li>
<li><strong>الماء المتغير:</strong> وهو الذي تغير أحد أوصافه الثلاثة (اللون، الطعم، الرائحة)</li>
</ol>
<h3>النجاسات</h3>
<p>النجاسة: هي كل عين يجب التطهر منها. وتنقسم إلى:</p>
<ul>
<li>نجاسة مغلظة: كالكلب والخنزير</li>
<li>نجاسة مخففة: كبول الصبي الذي لم يأكل الطعام</li>
<li>نجاسة متوسطة: كبقية النجاسات</li>
</ul>',
'الدرس 1: مقدمة في الطهارة',
'تعريف الطهارة وأقسام المياه وأحكام النجاسات',
'publish', 'lesson-01-tahara', 'fiqh_lesson');

INSERT INTO `wp_postmeta` (`post_id`, `meta_key`, `meta_value`) VALUES
(1001, '_fiqh_lesson_course_id', '1000'),
(1001, '_fiqh_lesson_video_url', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'),
(1001, '_fiqh_lesson_audio_url', 'https://archive.org/download/sample-audio/sample-audio.mp3'),
(1001, '_fiqh_lesson_pdf_url', 'https://archive.org/download/FP22640/22640.pdf'),
(1001, '_fiqh_lesson_duration', '45 دقيقة'),
(1001, '_fiqh_lesson_order', '1'),
(1001, '_fiqh_lesson_exercises', '<p><strong>تمرين 1:</strong> اذكر تعريف الطهارة لغة واصطلاحاً.</p><p><strong>تمرين 2:</strong> ما هي أقسام المياه في الفقه المالكي؟</p><p><strong>تمرين 3:</strong> قارن بين النجاسة المغلظة والمخففة.</p>');

-- الدرس 2
INSERT INTO `wp_posts` (`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `post_name`, `post_type`) VALUES
(1002, 1, NOW(), NOW(),
'<h2>الوضوء وأحكامه</h2>
<p>الوضوء: لغة من الوضاءة وهي الحسن والنظافة، وشرعاً: استعمال الماء في أعضاء مخصوصة بنية رفع الحدث.</p>
<h3>فرائض الوضوء</h3>
<p>فرائض الوضوء السبعة في المذهب المالكي:</p>
<ol>
<li><strong>النية:</strong> محلها القلب عند غسل أول جزء من الوجه</li>
<li><strong>غسل الوجه:</strong> من منابت شعر الرأس إلى الذقن، ومن الأذن إلى الأذن</li>
<li><strong>غسل اليدين:</strong> مع المرفقين</li>
<li><strong>مسح الرأس:</strong> كله أو بعضه</li>
<li><strong>غسل الرجلين:</strong> مع الكعبين</li>
<li><strong>الدلك:</strong> إمرار اليد على العضو</li>
<li><strong>الموالاة:</strong> عدم تأخير غسل عضو حتى ينشف الذي قبله</li>
</ol>
<h3>سنن الوضوء</h3>
<ul>
<li>السواك</li>
<li>التسمية في أوله</li>
<li>غسل الكفين ثلاثاً</li>
<li>المضمضة والاستنشاق</li>
<li>تجديد الماء للرأس</li>
<li>مسح الأذنين</li>
</ul>',
'الدرس 2: الوضوء وأحكامه',
'دراسة تفصيلية لفرائض الوضوء وسننه',
'publish', 'lesson-02-wudu', 'fiqh_lesson');

INSERT INTO `wp_postmeta` (`post_id`, `meta_key`, `meta_value`) VALUES
(1002, '_fiqh_lesson_course_id', '1000'),
(1002, '_fiqh_lesson_video_url', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'),
(1002, '_fiqh_lesson_audio_url', 'https://archive.org/download/sample-audio/sample-audio.mp3'),
(1002, '_fiqh_lesson_pdf_url', 'https://archive.org/download/FP22640/22640.pdf'),
(1002, '_fiqh_lesson_duration', '50 دقيقة'),
(1002, '_fiqh_lesson_order', '2'),
(1002, '_fiqh_lesson_exercises', '<p><strong>تمرين 1:</strong> اذكر فرائض الوضوء السبعة.</p><p><strong>تمرين 2:</strong> ما الفرق بين الفرض والسنة في الوضوء؟</p><p><strong>تمرين 3:</strong> اشرح معنى الموالاة والدلك.</p>');

-- الدرس 3
INSERT INTO `wp_posts` (`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `post_name`, `post_type`) VALUES
(1003, 1, NOW(), NOW(),
'<h2>الغسل والتيمم</h2>
<h3>أحكام الغسل</h3>
<p>الغسل: هو إفاضة الماء على جميع البدن بنية رفع الحدث الأكبر.</p>
<p><strong>موجبات الغسل:</strong></p>
<ol>
<li>الجنابة (الجماع وخروج المني)</li>
<li>الحيض</li>
<li>النفاس</li>
</ol>
<p><strong>فرائض الغسل:</strong></p>
<ol>
<li>النية</li>
<li>إفاضة الماء على جميع البدن</li>
<li>الدلك</li>
<li>الموالاة</li>
</ol>
<h3>أحكام التيمم</h3>
<p>التيمم: هو القصد إلى الصعيد الطاهر لمسح الوجه واليدين بنية استباحة الصلاة.</p>
<p><strong>مسوغات التيمم:</strong></p>
<ul>
<li>عدم وجود الماء</li>
<li>المرض الذي يضر معه استعمال الماء</li>
<li>شدة البرد مع عدم إمكان تسخين الماء</li>
<li>الخوف من استعمال الماء</li>
</ul>',
'الدرس 3: الغسل والتيمم',
'أحكام الغسل من الجنابة والحيض، وأحكام التيمم',
'publish', 'lesson-03-ghusl-tayammum', 'fiqh_lesson');

INSERT INTO `wp_postmeta` (`post_id`, `meta_key`, `meta_value`) VALUES
(1003, '_fiqh_lesson_course_id', '1000'),
(1003, '_fiqh_lesson_video_url', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'),
(1003, '_fiqh_lesson_audio_url', 'https://archive.org/download/sample-audio/sample-audio.mp3'),
(1003, '_fiqh_lesson_pdf_url', 'https://archive.org/download/FP22640/22640.pdf'),
(1003, '_fiqh_lesson_duration', '55 دقيقة'),
(1003, '_fiqh_lesson_order', '3'),
(1003, '_fiqh_lesson_exercises', '<p><strong>تمرين 1:</strong> ما هي موجبات الغسل؟</p><p><strong>تمرين 2:</strong> اذكر فرائض الغسل.</p><p><strong>تمرين 3:</strong> متى يجوز التيمم؟</p>');

-- الدرس 4
INSERT INTO `wp_posts` (`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `post_name`, `post_type`) VALUES
(1004, 1, NOW(), NOW(),
'<h2>شروط الصلاة</h2>
<p>الشروط: جمع شرط، وهو ما يلزم من عدمه العدم ولا يلزم من وجوده الوجود.</p>
<h3>شروط وجوب الصلاة</h3>
<ol>
<li><strong>الإسلام:</strong> فلا تجب على الكافر</li>
<li><strong>العقل:</strong> فلا تجب على المجنون</li>
<li><strong>البلوغ:</strong> فلا تجب على الصبي</li>
<li><strong>الطهارة من الحيض والنفاس:</strong> للمرأة</li>
</ol>
<h3>شروط صحة الصلاة</h3>
<ol>
<li><strong>الطهارة من الحدث:</strong> بالوضوء أو الغسل أو التيمم</li>
<li><strong>الطهارة من النجس:</strong> في البدن والثوب والمكان</li>
<li><strong>ستر العورة:</strong> بما يمنع وصف البشرة</li>
<li><strong>استقبال القبلة:</strong> لمن يقدر عليه</li>
<li><strong>دخول الوقت:</strong> لكل صلاة</li>
<li><strong>العلم بفرضيتها:</strong> للمكلف</li>
</ol>
<h3>حدود العورة</h3>
<ul>
<li><strong>عورة الرجل:</strong> من السرة إلى الركبة</li>
<li><strong>عورة المرأة:</strong> جميع البدن ما عدا الوجه والكفين</li>
</ul>',
'الدرس 4: شروط الصلاة',
'شروط وجوب وصحة الصلاة، وأحكام ستر العورة',
'publish', 'lesson-04-prayer-conditions', 'fiqh_lesson');

INSERT INTO `wp_postmeta` (`post_id`, `meta_key`, `meta_value`) VALUES
(1004, '_fiqh_lesson_course_id', '1000'),
(1004, '_fiqh_lesson_video_url', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'),
(1004, '_fiqh_lesson_audio_url', 'https://archive.org/download/sample-audio/sample-audio.mp3'),
(1004, '_fiqh_lesson_pdf_url', 'https://archive.org/download/FP22640/22640.pdf'),
(1004, '_fiqh_lesson_duration', '60 دقيقة'),
(1004, '_fiqh_lesson_order', '4'),
(1004, '_fiqh_lesson_exercises', '<p><strong>تمرين 1:</strong> ما الفرق بين شروط الوجوب وشروط الصحة؟</p><p><strong>تمرين 2:</strong> اذكر شروط صحة الصلاة.</p><p><strong>تمرين 3:</strong> ما حدود عورة الرجل والمرأة؟</p>');

-- الدرس 5
INSERT INTO `wp_posts` (`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `post_name`, `post_type`) VALUES
(1005, 1, NOW(), NOW(),
'<h2>أركان الصلاة وواجباتها</h2>
<h3>أركان الصلاة</h3>
<p>الأركان الأربعة عشر للصلاة في المذهب المالكي:</p>
<ol>
<li><strong>النية:</strong> محلها القلب</li>
<li><strong>تكبيرة الإحرام:</strong> الله أكبر</li>
<li><strong>القيام لها:</strong> لمن قدر عليه</li>
<li><strong>قراءة الفاتحة:</strong> في كل ركعة</li>
<li><strong>القيام للفاتحة:</strong> لمن قدر</li>
<li><strong>الركوع:</strong> بحيث تصل اليدان للركبتين</li>
<li><strong>الرفع من الركوع:</strong> والاعتدال قائماً</li>
<li><strong>السجود:</strong> على سبعة أعظم</li>
<li><strong>الرفع من السجود:</strong> والجلوس بين السجدتين</li>
<li><strong>الجلوس للسلام:</strong> بقدر التشهد</li>
<li><strong>السلام:</strong> بلفظه</li>
<li><strong>النية لإنهاء الصلاة:</strong> بالسلام</li>
<li><strong>الطمأنينة:</strong> في جميع الأركان</li>
<li><strong>الترتيب:</strong> بين الأركان</li>
</ol>
<h3>واجبات الصلاة</h3>
<ul>
<li>رفع اليدين مع تكبيرة الإحرام</li>
<li>التكبير لكل خفض ورفع</li>
<li>التسميع (سمع الله لمن حمده)</li>
<li>التحميد (ربنا ولك الحمد)</li>
<li>التشهد الأول</li>
<li>الجلوس له</li>
</ul>',
'الدرس 5: أركان الصلاة وواجباتها',
'تفصيل الأركان الأربعة عشر للصلاة وواجباتها',
'publish', 'lesson-05-prayer-pillars', 'fiqh_lesson');

INSERT INTO `wp_postmeta` (`post_id`, `meta_key`, `meta_value`) VALUES
(1005, '_fiqh_lesson_course_id', '1000'),
(1005, '_fiqh_lesson_video_url', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'),
(1005, '_fiqh_lesson_audio_url', 'https://archive.org/download/sample-audio/sample-audio.mp3'),
(1005, '_fiqh_lesson_pdf_url', 'https://archive.org/download/FP22640/22640.pdf'),
(1005, '_fiqh_lesson_duration', '65 دقيقة'),
(1005, '_fiqh_lesson_order', '5'),
(1005, '_fiqh_lesson_exercises', '<p><strong>تمرين 1:</strong> اذكر أركان الصلاة الأربعة عشر.</p><p><strong>تمرين 2:</strong> ما الفرق بين الركن والواجب؟</p><p><strong>تمرين 3:</strong> اشرح معنى الطمأنينة والترتيب.</p>');

-- ============================================
-- 5. طالب تجريبي (Demo Student)
-- ============================================

INSERT INTO `wp_users` (`ID`, `user_login`, `user_pass`, `user_nicename`, `user_email`, `user_registered`, `user_status`, `display_name`) VALUES
(100, 'student_demo', MD5('demo123'), 'student_demo', 'student@demo.local', NOW(), 0, 'طالب تجريبي');

INSERT INTO `wp_usermeta` (`user_id`, `meta_key`, `meta_value`) VALUES
(100, 'wp_capabilities', 'a:1:{s:7:"student";b:1;}'),
(100, 'wp_user_level', '0'),
(100, 'first_name', 'طالب'),
(100, 'last_name', 'تجريبي'),
(100, 'nickname', 'طالب تجريبي');

-- تسجيل الطالب في المقرر
INSERT INTO `wp_fiqh_enrollments` (`user_id`, `course_id`, `batch_id`, `status`, `enrolled_at`) VALUES
(100, 1000, 1, 'active', NOW());

-- إضافة تقدم للطالب (أول درسين مكتملين)
INSERT INTO `wp_fiqh_progress` (`user_id`, `lesson_id`, `course_id`, `status`, `progress_percentage`, `completed_at`, `started_at`) VALUES
(100, 1001, 1000, 'completed', 100, NOW(), DATE_SUB(NOW(), INTERVAL 3 DAY)),
(100, 1002, 1000, 'completed', 100, NOW(), DATE_SUB(NOW(), INTERVAL 2 DAY)),
(100, 1003, 1000, 'in_progress', 45, NULL, DATE_SUB(NOW(), INTERVAL 1 DAY));

-- ============================================
-- 6. الأسئلة والأجوبة (Q&A)
-- ============================================

INSERT INTO `wp_fiqh_questions` (`user_id`, `course_id`, `lesson_id`, `question`, `answer`, `answered_by`, `answered_at`, `is_anonymous`, `status`, `is_featured`, `views_count`, `created_at`) VALUES
(100, 1000, 1001, 'ما هي شروط صحة الوضوء في المذهب المالكي؟',
'شروط صحة الوضوء في المذهب المالكي سبعة: النية، وغسل الوجه، وغسل اليدين إلى المرفقين، ومسح الرأس، وغسل الرجلين إلى الكعبين، والدلك، والموالاة. وهذه هي الفرائض التي لا يصح الوضوء إلا بها.',
1, NOW(), 0, 'answered', 1, 15, DATE_SUB(NOW(), INTERVAL 2 DAY)),

(100, 1000, 1002, 'هل يجب الدلك في الوضوء؟',
'نعم، الدلك فرض في المذهب المالكي، وهو إمرار اليد على العضو المغسول. وخالف في ذلك الجمهور حيث جعلوه سنة.',
1, NOW(), 0, 'answered', 0, 8, DATE_SUB(NOW(), INTERVAL 1 DAY)),

(100, 1000, NULL, 'ما الفرق بين الحدث الأصغر والأكبر؟',
NULL, NULL, NULL, 0, 'pending', 0, 3, NOW());

-- ============================================
-- 7. الإشعارات (Notifications)
-- ============================================

INSERT INTO `wp_fiqh_notifications` (`user_id`, `title`, `message`, `type`, `is_read`, `action_url`, `created_at`) VALUES
(100, 'مرحباً بك في المنصة', 'تم تسجيلك بنجاح في مقرر فقه العبادات. ابدأ رحلتك العلمية الآن!', 'success', 0, '/courses/fiqh-ibadat', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(100, 'درس جديد متاح', 'تم إضافة درس جديد: أركان الصلاة وواجباتها', 'info', 1, '/lessons/lesson-05-prayer-pillars', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(100, 'إجابة على سؤالك', 'تم الرد على سؤالك حول شروط صحة الوضوء', 'answer', 0, '/questions', NOW());

-- ============================================
-- 8. مقالة تجريبية (Blog Post)
-- ============================================

INSERT INTO `wp_posts` (`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `post_name`, `post_type`, `comment_status`, `ping_status`) VALUES
(2000, 1, NOW(), NOW(),
'<p>الفقه المالكي هو أحد المذاهب الفقهية الأربعة المعتمدة في العالم الإسلامي، نسبة إلى الإمام مالك بن أنس رحمه الله، إمام دار الهجرة.</p>

<h2>نشأة المذهب المالكي</h2>
<p>نشأ المذهب المالكي في المدينة المنورة على يد الإمام مالك بن أنس (93-179 هـ)، الذي أخذ العلم عن كبار التابعين وتابعيهم، وصنّف كتابه العظيم "الموطأ" الذي يعد من أوائل كتب الحديث المصنفة.</p>

<h2>خصائص المذهب المالكي</h2>
<p>يتميز المذهب المالكي بخصائص عديدة أهمها:</p>
<ul>
<li><strong>الاعتماد على عمل أهل المدينة:</strong> حيث اعتبر الإمام مالك عمل أهل المدينة حجة، لأنهم ورثوا العلم عن الصحابة الذين عاشوا فيها</li>
<li><strong>التوسط والاعتدال:</strong> يسلك المذهب منهجاً وسطاً بين التشدد والتساهل</li>
<li><strong>الاهتمام بالمقاصد الشرعية:</strong> يُعنى المالكية بمقاصد الشريعة ومراعاة المصالح</li>
<li><strong>الأخذ بالمصالح المرسلة:</strong> وهي المصالح التي لم يرد نص بإلغائها أو اعتبارها</li>
<li><strong>سد الذرائع:</strong> منع الوسائل المؤدية إلى المفاسد</li>
</ul>

<h2>انتشار المذهب</h2>
<p>انتشر المذهب المالكي في مناطق واسعة من العالم الإسلامي، خاصة في:</p>
<ul>
<li>شمال أفريقيا (المغرب، الجزائر، تونس، ليبيا، موريتانيا)</li>
<li>غرب أفريقيا</li>
<li>مصر والسودان</li>
<li>الأندلس تاريخياً</li>
<li>بعض مناطق الخليج العربي</li>
</ul>

<h2>أهمية دراسة المذهب المالكي</h2>
<p>دراسة المذهب المالكي ضرورية لعدة أسباب:</p>
<ol>
<li>انتشاره الواسع في العالم الإسلامي</li>
<li>تميزه بأصول فقهية فريدة</li>
<li>اعتماده على الأحاديث الصحيحة</li>
<li>مراعاته للمصالح ومقاصد الشريعة</li>
<li>تيسيره على الناس في أحكام العبادات والمعاملات</li>
</ol>

<h2>مصادر المذهب المالكي</h2>
<p>من أهم المصادر في المذهب المالكي:</p>
<ul>
<li><strong>الموطأ:</strong> للإمام مالك، وهو أصل المذهب</li>
<li><strong>المدونة الكبرى:</strong> للإمام سحنون</li>
<li><strong>الرسالة:</strong> لابن أبي زيد القيرواني</li>
<li><strong>مختصر خليل:</strong> من أهم المتون الفقهية</li>
<li><strong>شرح الزرقاني على الموطأ</strong></li>
<li><strong>حاشية الدسوقي على الشرح الكبير</strong></li>
</ul>

<h2>الخاتمة</h2>
<p>إن دراسة الفقه المالكي تفتح للطالب أفقاً واسعاً في فهم الأحكام الشرعية، وتمكنه من معرفة أقوال العلماء وأدلتهم، وتدربه على استنباط الأحكام من النصوص الشرعية.</p>

<p>ندعو الله أن يوفقنا لتعلم العلم النافع والعمل الصالح، وأن يجعلنا من أهل العلم العاملين.</p>',
'أهمية دراسة الفقه المالكي وخصائصه',
'نظرة شاملة على المذهب المالكي: نشأته، خصائصه، انتشاره، وأهم مصادره',
'publish', 'importance-of-maliki-fiqh', 'post', 'open', 'open');

-- ربط المقالة بالتصنيف
INSERT INTO `wp_term_relationships` (`object_id`, `term_taxonomy_id`) VALUES
(2000, 1); -- تصنيف "غير مصنف"

-- ============================================
-- النهاية - COMMIT
-- ============================================

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

-- ============================================
-- ملاحظات الاستيراد
-- ============================================
-- 1. تأكد من تغيير بادئة الجداول (wp_) حسب تثبيت WordPress الخاص بك
-- 2. تأكد من وجود الجداول المخصصة قبل الاستيراد
-- 3. قد تحتاج لتعديل IDs للمستخدمين والمنشورات حسب قاعدة البيانات الموجودة
-- 4. كلمة المرور للطالب التجريبي: demo123
-- 5. اسم المستخدم: student_demo
-- ============================================
