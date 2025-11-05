<?php
/**
 * محتوى تجريبي Demo Content - محدّث بالبيانات الحقيقية
 *
 * ملاحظة: هذا الملف يُشغّل مرة واحدة فقط لإنشاء محتوى تجريبي كامل
 * للتشغيل: قم بزيارة: wp-admin/admin.php?page=fiqh-demo-content
 *
 * @package FiqhLearning_LMS
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * إضافة صفحة في لوحة التحكم لتشغيل المحتوى التجريبي
 */
function fiqh_add_demo_content_menu() {
    add_submenu_page(
        'edit.php?post_type=fiqh_course',
        __('محتوى تجريبي', 'fiqh-lms'),
        __('محتوى تجريبي', 'fiqh-lms'),
        'manage_options',
        'fiqh-demo-content',
        'fiqh_create_demo_content_page'
    );
}
add_action('admin_menu', 'fiqh_add_demo_content_menu');

/**
 * صفحة إنشاء المحتوى التجريبي
 */
function fiqh_create_demo_content_page() {
    // التحقق من الصلاحيات أولاً
    if (!current_user_can('manage_options')) {
        wp_die(__('عذراً، ليس لديك صلاحية للوصول إلى هذه الصفحة.', 'fiqh-lms'));
    }

    ?>
    <div class="wrap">
        <h1><?php _e('إنشاء محتوى تجريبي كامل', 'fiqh-lms'); ?></h1>
        <div class="notice notice-warning">
            <p><strong><?php _e('تحذير:', 'fiqh-lms'); ?></strong> <?php _e('سيتم إنشاء محتوى تجريبي كامل للمنصة مع البيانات الحقيقية. هذه العملية قد تستغرق بعض الوقت.', 'fiqh-lms'); ?></p>
        </div>

        <?php
        // التحقق من POST بدلاً من GET
        if (isset($_POST['generate_demo']) && check_admin_referer('fiqh_demo_content_action', 'fiqh_demo_content_nonce')) {
            fiqh_generate_demo_content();
        } else {
            ?>
            <form method="post" action="">
                <?php wp_nonce_field('fiqh_demo_content_action', 'fiqh_demo_content_nonce'); ?>
                <input type="hidden" name="generate_demo" value="1">
                <p>
                    <input type="submit" class="button button-primary button-large" value="<?php _e('إنشاء المحتوى التجريبي الآن', 'fiqh-lms'); ?>">
                </p>
            </form>
            <?php
        }
        ?>
    </div>
    <?php
}

/**
 * إنشاء المحتوى التجريبي الكامل بالبيانات الحقيقية
 */
function fiqh_generate_demo_content() {
    global $wpdb;

    set_time_limit(300); // 5 دقائق

    echo '<div class="updated"><p><strong>' . __('بدأ إنشاء المحتوى التجريبي...', 'fiqh-lms') . '</strong></p></div>';

    // 0. التأكد من وجود دور الطالب
    echo '<h2>0️⃣ التحقق من أدوار المستخدمين</h2>';

    // حذف وإعادة إنشاء دور الطالب لضمان الصلاحيات الصحيحة
    remove_role('student');
    add_role(
        'student',
        __('طالب', 'fiqh-lms'),
        array(
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
            'publish_posts' => false,
            'upload_files' => false,
            'view_courses' => true,
            'view_lessons' => true,
            'submit_questions' => true,
            'take_notes' => true,
            'track_progress' => true,
        )
    );
    echo '<p style="color: green;">✅ تم إنشاء/تحديث دور الطالب مع الصلاحيات المناسبة</p>';

    // 1. إنشاء التصنيفات والعلوم الشرعية
    echo '<h2>1️⃣ إنشاء العلوم الشرعية</h2>';
    $sciences_data = array(
        array('name' => '📘 علم التفسير', 'desc' => 'هو علمٌ يبحث في بيان كيفيّة فَهْم ألفاظ القرآن الكريم.'),
        array('name' => '🧩 علم المنطق', 'desc' => 'علم تمنع مراعاته من الوقوع في الخطأ في الفكر.'),
        array('name' => '📜 علم مصطلح الحديث', 'desc' => 'هو العلم بقواعدٍ يُعرف بها أحوال السند والمتن من حيث القبول أو الرد.'),
        array('name' => '📚 مطالعة (كتاب مختار)', 'desc' => 'المطالعة الموجهة توسع عقل الطالب وتزيد فِطنته ونباهته.'),
        array('name' => '⚖️ أصول الفقه', 'desc' => 'علم يبحث في أدلة الفقه الإجمالية وكيفية الاستفادة منها، وحال المستفيد.'),
        array('name' => '🕋 علم العقيدة (علم الكلام)', 'desc' => 'يقوم علم الكلام على بحث ودراسة مسائل العقيدة الإسلامية بإيراد الأدلة وعرض الحجج على إثباتها.'),
        array('name' => '🌿 علم التزكية', 'desc' => 'يسمى كذلك بعلم إصلاح النفس أو علم التصوف، وهو علمٌ يبحث فيه عن الطرق التي تخلص النفس من أسوائها.'),
        array('name' => '⚖️ علم الفقه', 'desc' => 'العلم بالأحكام الشرعية المكتسبة من أدلتها التفصيلية.'),
        array('name' => '🧮 علم الميراث', 'desc' => 'علم الفرائض أو علم المواريث هو العلم الذي يعنى بأحوال تَرِكة الميت وميراثه من حيث تقسيمها على مستحقيها.'),
        array('name' => '✍️ علم النحو', 'desc' => 'هو علم يعصم اللسان من اللحن.'),
        array('name' => '🔠 علم الصرف', 'desc' => 'علم بأصول يُعرف بها أحوال بِنْيَة الكلمة التي ليست بإعراب ولا بناء.'),
        array('name' => '🎓 مسار تعليمي', 'desc' => 'محاضرات تحفيزية وإرشادية لطلبة العلم الشرعي.'),
    );

    foreach ($sciences_data as $science) {
        $term = term_exists($science['name'], 'fiqh_course_science');
        if (!$term) {
            wp_insert_term($science['name'], 'fiqh_course_science', array(
                'description' => $science['desc']
            ));
            echo '<p style="color: green;">✅ تم إنشاء علم: <strong>' . esc_html($science['name']) . '</strong></p>';
        } else {
            echo '<p style="color: orange;">⚠️ العلم موجود مسبقاً: ' . esc_html($science['name']) . '</p>';
        }
    }

    // إنشاء تصنيفات المقالات
    echo '<h3>إنشاء تصنيفات المقالات</h3>';
    $categories = array('أحداث', 'أخبار', 'مقالات علمية', 'إعلانات');
    foreach ($categories as $cat_name) {
        if (!get_category_by_slug(sanitize_title($cat_name))) {
            wp_insert_category(array(
                'cat_name' => $cat_name,
                'category_nicename' => sanitize_title($cat_name)
            ));
            echo '<p style="color: green;">✅ تم إنشاء تصنيف: <strong>' . $cat_name . '</strong></p>';
        }
    }

    // 1.5 إنشاء الصفحات الأساسية
    echo '<h2>📄 إنشاء الصفحات الأساسية</h2>';
    $pages = array(
        array(
            'title' => 'تسجيل الدخول',
            'slug' => 'login',
            'template' => 'page-login.php',
            'content' => '<!-- صفحة تسجيل الدخول -->'
        ),
        array(
            'title' => 'استعادة كلمة المرور',
            'slug' => 'forgot-password',
            'template' => 'page-forgot-password.php',
            'content' => '<!-- صفحة استعادة كلمة المرور -->'
        ),
        array(
            'title' => 'لوحة التحكم',
            'slug' => 'dashboard',
            'template' => 'page-dashboard.php',
            'content' => '<!-- لوحة تحكم الطالب -->'
        ),
        array(
            'title' => 'من نحن',
            'slug' => 'about',
            'template' => 'page-about.php',
            'content' => '<p>صفحة تعريفية عن المنصة</p>'
        ),
        array(
            'title' => 'اتصل بنا',
            'slug' => 'contact',
            'template' => 'page-contact.php',
            'content' => '<!-- صفحة الاتصال -->'
        ),
        array(
            'title' => 'الأسئلة',
            'slug' => 'questions',
            'template' => 'page-questions.php',
            'content' => '<!-- صفحة الأسئلة والأجوبة -->'
        ),
        array(
            'title' => 'خطة الدراسة',
            'slug' => 'study-plan',
            'template' => 'page-study-plan.php',
            'content' => '<!-- خطة الدراسة -->'
        ),
    );

    foreach ($pages as $page_data) {
        // التحقق من عدم وجود الصفحة
        $existing_page = get_page_by_path($page_data['slug']);

        if (!$existing_page) {
            $page_id = wp_insert_post(array(
                'post_title' => $page_data['title'],
                'post_name' => $page_data['slug'],
                'post_content' => $page_data['content'],
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_author' => 1,
            ));

            if ($page_id && !is_wp_error($page_id)) {
                // تعيين template الصفحة
                update_post_meta($page_id, '_wp_page_template', $page_data['template']);
                echo '<p style="color: green;">✅ تم إنشاء صفحة: <strong>' . $page_data['title'] . '</strong> (/' . $page_data['slug'] . ')</p>';
            }
        } else {
            // تحديث template إذا كانت الصفحة موجودة
            update_post_meta($existing_page->ID, '_wp_page_template', $page_data['template']);
            echo '<p style="color: orange;">⚠️ الصفحة موجودة مسبقاً: ' . $page_data['title'] . ' (تم تحديث القالب)</p>';
        }
    }

    // 2. إنشاء المستويات الدراسية (Batches)
    echo '<h2>2️⃣ إنشاء المستويات الدراسية</h2>';
    $batches = array(
        array(
            'name' => 'المستوى الأول',
            'description' => 'المستوى الأول للطلبة المبتدئين',
            'level_order' => 1,
            'start_date' => '2024-09-01',
            'end_date' => '2025-06-30',
            'status' => 'active'
        ),
        array(
            'name' => 'المستوى الثاني',
            'description' => 'المستوى الثاني للطلبة المتقدمين',
            'level_order' => 2,
            'start_date' => '2024-09-01',
            'end_date' => '2025-06-30',
            'status' => 'active'
        ),
    );

    $batch_ids = array();
    foreach ($batches as $batch) {
        // التحقق من عدم التكرار
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}fiqh_batches WHERE name = %s",
            $batch['name']
        ));

        if (!$exists) {
            $wpdb->insert($wpdb->prefix . 'fiqh_batches', $batch);
            $batch_ids[$batch['name']] = $wpdb->insert_id;
            echo '<p style="color: green;">✅ تم إنشاء مستوى: <strong>' . $batch['name'] . '</strong> (ترتيب: ' . $batch['level_order'] . ')</p>';
        } else {
            $batch_ids[$batch['name']] = $exists;
            echo '<p style="color: orange;">⚠️ المستوى موجود مسبقاً: ' . $batch['name'] . '</p>';
        }
    }

    // 3. إنشاء حسابات الإدارة
    echo '<h2>3️⃣ إنشاء حسابات الإدارة</h2>';
    $admin_users = array(
        array(
            'username' => 'mohamed',
            'email' => 'mohamed@gmail.com',
            'password' => 'HUNTERpeace2014',
            'display_name' => 'mohamed',
            'level' => 'المستوى الثاني'
        ),
        array(
            'username' => 'admin',
            'email' => 'rayhaneschool@gmail.com',
            'password' => '23062022',
            'display_name' => 'admin',
            'level' => 'المستوى الثاني'
        ),
        array(
            'username' => 'abdelhamid_kerroumiعبد_الحميد_كرومي',
            'email' => 'Kerroumiabdelhamid272@gmail.com',
            'password' => 'Kerroumiabdelhamid272@gmail.com',
            'display_name' => 'عبد الحميد كرومي',
            'level' => 'المستوى الثاني'
        ),
    );

    foreach ($admin_users as $admin) {
        $user_id = username_exists($admin['username']);
        if (!$user_id && !email_exists($admin['email'])) {
            $user_id = wp_create_user($admin['username'], $admin['password'], $admin['email']);
            wp_update_user(array(
                'ID' => $user_id,
                'display_name' => $admin['display_name'],
                'role' => 'administrator'
            ));

            // تعيين المستوى
            if (isset($batch_ids[$admin['level']])) {
                update_user_meta($user_id, '_fiqh_student_level', $batch_ids[$admin['level']]);
            }

            echo '<p style="color: green;">✅ تم إنشاء مدير: <strong>' . $admin['display_name'] . '</strong> (البريد: ' . $admin['email'] . ')</p>';
        } else {
            echo '<p style="color: orange;">⚠️ المستخدم موجود مسبقاً: ' . $admin['display_name'] . '</p>';
        }
    }

    // 4. إنشاء طلاب المستوى الثاني
    echo '<h2>4️⃣ إنشاء طلاب المستوى الثاني</h2>';
    $level2_students = array(
        array('email' => 'noussaybabrmk@gmail.com', 'password' => '0665153437', 'display_name' => 'نسيبة برمكي'),
        array('email' => 'bouchra.meryemg1@yahoo.com', 'password' => '0669436242', 'display_name' => 'مريم بوشرى'),
        array('email' => 'mahboubabourafa@gmail.com', 'password' => '0672030813', 'display_name' => 'محبوبة برافة'),
        array('email' => 'asmasamo2003@gmail.com', 'password' => '0673634075', 'display_name' => 'عائشة صديقي'),
        array('email' => 'mohibatoalqoran1994@gmail.com', 'password' => '0666273737', 'display_name' => 'صفية موساوي'),
        array('email' => 'Fatimazohrazohra992@gmail.com', 'password' => '0673598421', 'display_name' => 'محفوض فاطمة'),
        array('email' => 'meryem98meryem98@gmail.com', 'password' => '0659748993', 'display_name' => 'بحاج مريم'),
        array('email' => 'lalabelouafi94@gmail.com', 'password' => '0669876787', 'display_name' => 'لالة بلوافي'),
        array('email' => 'aichahila74@gmail.com', 'password' => '0697491838', 'display_name' => 'عائشة حيلة'),
        array('email' => 'thrgtf32@gmail.com', 'password' => '0699303922', 'display_name' => 'قطاف أحمد الطاهر'),
        array('email' => 'Meryem.sgr00@gmail.com', 'password' => '0658540656', 'display_name' => 'رباب مريم صغير'),
        array('email' => 'hadjerhammaoui2001@gmail.com', 'password' => '0673431208', 'display_name' => 'هاجر حماوي'),
    );

    $student_ids_level2 = array();
    foreach ($level2_students as $student) {
        $username = sanitize_user(str_replace(['@', '.'], '_', explode('@', $student['email'])[0]));
        $user_id = email_exists($student['email']);

        if (!$user_id) {
            $user_id = wp_create_user($username, $student['password'], $student['email']);
            wp_update_user(array(
                'ID' => $user_id,
                'display_name' => $student['display_name'],
                'role' => 'student'
            ));

            // تعيين المستوى الثاني
            update_user_meta($user_id, '_fiqh_student_level', $batch_ids['المستوى الثاني']);

            $student_ids_level2[] = $user_id;
            echo '<p style="color: green;">✅ تم إنشاء طالبة: <strong>' . $student['display_name'] . '</strong> (' . $student['email'] . ')</p>';
        } else {
            $student_ids_level2[] = $user_id;
            echo '<p style="color: orange;">⚠️ الطالبة موجودة مسبقاً: ' . $student['display_name'] . '</p>';
        }
    }

    // 5. إنشاء طلاب المستوى الأول
    echo '<h2>5️⃣ إنشاء طلاب المستوى الأول</h2>';
    $level1_students = array(
        array('email' => 'rabhikarima1994@gmail.com', 'password' => '0662010098', 'display_name' => 'رابحي كريمة'),
        array('email' => 'benchikhbakhta01@gmail.com', 'password' => '0662676839', 'display_name' => 'بختة بن الشيخ'),
        array('email' => 'isambahaa62@gmail.com', 'password' => '0660937757', 'display_name' => 'بوصالح نورة'),
        array('email' => 'Saba31526@gmail.com', 'password' => '0667983106', 'display_name' => 'صبا'),
        array('email' => 'latifadjeballah.1988@gmail.com', 'password' => '0669992642', 'display_name' => 'جاب الله لطيفة'),
        array('email' => 'hlleme01@gmail.com', 'password' => '0664089031', 'display_name' => 'بلخير عبد الحليم'),
        array('email' => 'aloumer2001saad@gmail.com', 'password' => '0799179814', 'display_name' => 'سعد نفاد'),
        array('email' => 'slimanideja76@gmail.com', 'password' => '0662654669', 'display_name' => 'سليماني جمعة'),
        array('email' => 'khaledbouchra45@gmail.com', 'password' => '0662250419', 'display_name' => 'خارف مباركة'),
        array('email' => 'omalhkir93@gmail.com', 'password' => '0669008578', 'display_name' => 'بن عزاوي أم الخير'),
        array('email' => 'nounaalgariba@gmail.com', 'password' => '0655745375', 'display_name' => 'بن دحو إيمان'),
        array('email' => 'yaganora@gmail.com', 'password' => '0673602675', 'display_name' => 'ياقة نورية'),
        array('email' => 'Mohamedbahida13@gmail.com', 'password' => '0663208072', 'display_name' => 'باحيدة محمد'),
        array('email' => 'djelloulabencheikh@gmail.com', 'password' => '0674135949', 'display_name' => 'بن الشيخ جلولة'),
        array('email' => 'omarionline@gmail.com', 'password' => '0696465289', 'display_name' => 'عوماري محمد'),
    );

    $student_ids_level1 = array();
    foreach ($level1_students as $student) {
        $username = sanitize_user(str_replace(['@', '.'], '_', explode('@', $student['email'])[0]));
        $user_id = email_exists($student['email']);

        if (!$user_id) {
            $user_id = wp_create_user($username, $student['password'], $student['email']);
            wp_update_user(array(
                'ID' => $user_id,
                'display_name' => $student['display_name'],
                'role' => 'student'
            ));

            // تعيين المستوى الأول
            update_user_meta($user_id, '_fiqh_student_level', $batch_ids['المستوى الأول']);

            $student_ids_level1[] = $user_id;
            echo '<p style="color: green;">✅ تم إنشاء طالب/ة: <strong>' . $student['display_name'] . '</strong> (' . $student['email'] . ')</p>';
        } else {
            $student_ids_level1[] = $user_id;
            echo '<p style="color: orange;">⚠️ الطالب/ة موجود/ة مسبقاً: ' . $student['display_name'] . '</p>';
        }
    }

    // جمع كل الطلاب
    $all_student_ids = array_merge($student_ids_level1, $student_ids_level2);

    // 6. إنشاء مقررات تجريبية مفصلة مع المحاضرات
    echo '<h2>6️⃣ إنشاء المقررات الدراسية المفصلة</h2>';

    // بيانات المقررات مع محاضراتها
    $detailed_courses = array(
        // علم الفقه
        array(
            'title' => 'متن المرشد المعين',
            'science' => '⚖️ علم الفقه',
            'content' => '<p>شرح متن المرشد المعين على الضروري من علوم الدين</p>',
            'excerpt' => 'متن المرشد المعين على الضروري من علوم الدين',
            'duration' => '18 محاضرة',
            'level' => 'المستوى الثاني',
            'lessons' => array(
                array('title' => 'المحاضرة الأولى - الجزء الأول', 'url' => 'https://youtu.be/jprs2rJKfVc'),
                array('title' => 'المحاضرة الأولى - الجزء الثاني', 'url' => 'https://youtu.be/ApCXniVww4s'),
                array('title' => 'المحاضرة الأولى - الجزء الثالث', 'url' => 'https://youtu.be/uN-7N8B4pxY'),
                array('title' => 'المحاضرة الثانية', 'url' => 'https://youtu.be/GBqx93bA44E'),
                array('title' => 'المحاضرة الثالثة', 'url' => 'https://youtu.be/hGC64jID_ow'),
                array('title' => 'المحاضرة الرابعة', 'url' => 'https://youtu.be/Vvloak0LtU8'),
                array('title' => 'المحاضرة الخامسة', 'url' => 'https://youtu.be/en01cDv4cmo'),
                array('title' => 'المحاضرة السادسة', 'url' => 'https://youtu.be/pz00tc7GyNs'),
                array('title' => 'الحصة السابعة', 'url' => 'https://youtu.be/h8WHkdnx1b8'),
                array('title' => 'الحصة الثامنة', 'url' => 'https://youtu.be/1Bpy-MeEkmg'),
                array('title' => 'الحصة التاسعة', 'url' => 'https://youtu.be/UlnN0eIUj5s'),
                array('title' => 'المحاضرة العاشرة', 'url' => 'https://youtu.be/f8moQbl5Dps'),
                array('title' => 'المحاضرة الحادية عشرة', 'url' => 'https://youtu.be/HVfcl2mt6o0'),
                array('title' => 'المحاضرة الثانية عشرة', 'url' => 'https://youtu.be/f6D9TTgC4wE'),
                array('title' => 'المحاضرة الثالثة عشرة', 'url' => 'https://youtu.be/PIgbhWwxATw'),
                array('title' => 'المحاضرة الرابعة عشرة', 'url' => 'https://youtu.be/E8e63cnjQ30'),
                array('title' => 'المحاضرة الرابعة عشرة تابع', 'url' => 'https://youtu.be/-iYJzKHfnmA'),
                array('title' => 'المحاضرة الخامسة عشرة و الأخيرة', 'url' => 'https://youtu.be/jqdAH-DHEWY'),
            )
        ),
        array(
            'title' => 'متن العبقري',
            'science' => '⚖️ علم الفقه',
            'content' => '<p>شرح متن العبقري في الفقه المالكي</p>',
            'excerpt' => 'متن العبقري في الفقه المالكي',
            'duration' => '21 محاضرة',
            'level' => 'المستوى الثاني',
            'lessons' => array(
                array('title' => 'المحاضرة الأولى', 'url' => 'https://youtu.be/yMNNEziMXGI'),
                array('title' => 'المحاضرة الثانية', 'url' => 'https://youtu.be/Y2FfbRjJAJI'),
                array('title' => 'المحاضرة الثالثة', 'url' => 'https://youtu.be/XlfEMF1r5T4'),
                array('title' => 'المحاضرة الرابعة', 'url' => 'https://youtu.be/-l_UDTopkUc'),
                array('title' => 'المحاضرة الخامسة', 'url' => 'https://youtu.be/s5zwHfCDG0I'),
                array('title' => 'المحاضرة السادسة', 'url' => 'https://youtu.be/uZ4vaQnAyKk'),
                array('title' => 'المحاضرة السابعة', 'url' => 'https://youtu.be/22bZMlZg6N4'),
                array('title' => 'المحاضرة الثامنة', 'url' => 'https://youtu.be/o1vMAEZlgPs'),
                array('title' => 'المحاضرة التاسعة', 'url' => 'https://youtu.be/19J1XPnToFk'),
                array('title' => 'المحاضرة العاشرة', 'url' => 'https://youtu.be/yvj3AkcdZQI'),
                array('title' => 'المحاضرة الحادية عشر', 'url' => 'https://youtu.be/NRZ02waMOqY'),
                array('title' => 'المحاضرة الثانية عشر', 'url' => 'https://youtu.be/gsyYGSgr-GM'),
                array('title' => 'المحاضرة الثالثة عشر', 'url' => 'https://youtu.be/_zizOA2xo8w'),
                array('title' => 'المحاضرة الرابعة عشر', 'url' => 'https://youtu.be/-Qjf19gTwc4'),
                array('title' => 'المحاضرة الخامسة عشر', 'url' => 'https://youtu.be/GVRFK6xdxr8'),
                array('title' => 'المحاضرة السادسة عشر', 'url' => 'https://youtu.be/WWRx0IdEPs8'),
                array('title' => 'المحاضرة السابعة عشر', 'url' => 'https://youtu.be/b5PrHyF19LI'),
                array('title' => 'المحاضرة الثامنة عشر', 'url' => 'https://youtu.be/QTYjfHB93LA'),
                array('title' => 'المحاضرة التاسعة عشر', 'url' => 'https://youtu.be/z-xyz4qLYpQ'),
                array('title' => 'المحاضرة العشرون', 'url' => 'https://youtu.be/-TAo50P3EtQ'),
                array('title' => 'المحاضرة الحادية و العشرون', 'url' => 'https://youtu.be/7HeRQLVoiws'),
            )
        ),
        array(
            'title' => 'شرح متن المرشد المعين على الضروري من علوم الدين',
            'science' => '⚖️ علم الفقه',
            'content' => '<p>شرح مفصل لمتن المرشد المعين للمستوى الأول</p>',
            'excerpt' => 'شرح متن المرشد المعين',
            'duration' => '15 حصة',
            'level' => 'المستوى الأول',
            'lessons' => array(
                array('title' => 'الحصة الأولى', 'url' => 'https://youtu.be/5GeHTCxUVs8'),
                array('title' => 'الحصة الثانية', 'url' => 'https://youtu.be/a5394Id-ljU'),
                array('title' => 'الحصة الثالثة', 'url' => 'https://youtu.be/XqYml4zRfzE'),
                array('title' => 'الحصة الرابعة', 'url' => 'https://youtu.be/dpniyIgkR_M'),
                array('title' => 'الحصة الخامسة', 'url' => 'https://youtu.be/PjPKfZe7IRQ'),
                array('title' => 'الحصة السادسة', 'url' => 'https://youtu.be/s7AAgMUqpGs'),
                array('title' => 'الحصة السابعة', 'url' => 'https://youtu.be/sKwuwZa0qTU'),
                array('title' => 'الحصة الثامنة', 'url' => 'https://youtu.be/W5_WNYHt5Eo'),
                array('title' => 'الحصة التاسعة', 'url' => 'https://youtu.be/CsD1NXer6Ms'),
                array('title' => 'الحصة العاشرة', 'url' => 'https://youtu.be/yFIw9r-U1yk'),
                array('title' => 'الحصة الحادية عشرة', 'url' => 'https://youtu.be/C1aaKTO4J9c'),
                array('title' => 'الحصة الثانية عشرة', 'url' => 'https://youtu.be/JjiPH36y91o'),
                array('title' => 'الحصة الثالثة عشرة', 'url' => 'https://youtu.be/tE6bctkGAd4'),
                array('title' => 'الحصة الرابعة عشر', 'url' => 'https://youtu.be/YNWqqQTHcJk'),
                array('title' => 'الخامسة عشر', 'url' => 'https://youtu.be/VxnULGfV5_s'),
            )
        ),
        array(
            'title' => 'شرح متن العبقري',
            'science' => '⚖️ علم الفقه',
            'content' => '<p>شرح مبسط لمتن العبقري للمبتدئين</p>',
            'excerpt' => 'شرح متن العبقري للمبتدئين',
            'duration' => '7 حصص',
            'level' => 'المستوى الأول',
            'lessons' => array(
                array('title' => 'الحصة الأولى', 'url' => 'https://youtu.be/zt1CRHr5Axw'),
                array('title' => 'الحصة الثانية', 'url' => 'https://youtu.be/2ZMoiaQwENI'),
                array('title' => 'الحصة الثالثة', 'url' => 'https://youtu.be/89gP7TjHpMs'),
                array('title' => 'الحصة الرابعة', 'url' => 'https://youtu.be/E0Svl15YVwQ'),
                array('title' => 'الحصة الخامسة', 'url' => 'https://youtu.be/ThBZy0FwdZc'),
                array('title' => 'الحصة السادسة', 'url' => 'https://youtu.be/WE-NkpfB5fI'),
                array('title' => 'الحصة السابعة والأخيرة', 'url' => 'https://youtu.be/5_m-r958Tdw'),
            )
        ),
        // علم الميراث
        array(
            'title' => 'شرح الرحبية',
            'science' => '🧮 علم الميراث',
            'content' => '<p>شرح متن الرحبية في علم الفرائض</p>',
            'excerpt' => 'شرح الرحبية في الفرائض',
            'duration' => '10 حصص',
            'level' => 'المستوى الثاني',
            'lessons' => array(
                array('title' => 'الحصة الأولى', 'url' => 'https://youtu.be/1aG7Fpjn_cE'),
                array('title' => 'الحصة الثانية', 'url' => 'https://youtu.be/Dhysp7ubCUE'),
                array('title' => 'الحصة الثالثة', 'url' => 'https://youtu.be/W-3vwu9xu54'),
                array('title' => 'الحصة الرابعة', 'url' => 'https://youtu.be/E86NXxnTvc-'),
                array('title' => 'الحصة الخامسة', 'url' => 'https://youtu.be/h02xpW_ZSko'),
                array('title' => 'الحصة السادسة', 'url' => 'https://youtu.be/WoX-lv0CESY'),
                array('title' => 'الحصة السابعة', 'url' => 'https://youtu.be/jOEVjRm4uu4'),
                array('title' => 'الحصة الثامنة', 'url' => 'https://youtu.be/PDFlumrtG54'),
                array('title' => 'الحصة التاسعة', 'url' => 'https://youtu.be/l-ekXpyEGas'),
                array('title' => 'الحصة الأخيرة', 'url' => 'https://youtu.be/28e9freLY5k'),
            )
        ),
        // علم النحو
        array(
            'title' => 'متن الأجرومية',
            'science' => '✍️ علم النحو',
            'content' => '<p>شرح متن الأجرومية في النحو</p>',
            'excerpt' => 'شرح الأجرومية في النحو',
            'duration' => '12 جزء',
            'level' => 'المستوى الثاني',
            'lessons' => array(
                array('title' => 'المحاضرة الأولى - الجزء الأول', 'url' => 'https://youtu.be/0H5ZUVU66ms'),
                array('title' => 'المحاضرة الأولى - الجزء الثاني', 'url' => 'https://youtu.be/aCw0n9OrPdQ'),
                array('title' => 'المحاضرة الأولى - الجزء الثالث', 'url' => 'https://youtu.be/E43t5_ucjcc'),
                array('title' => 'المحاضرة الأولى - الجزء الرابع', 'url' => 'https://youtu.be/0hpw6ws0R60'),
                array('title' => 'المحاضرة الأولى - الجزء الخامس', 'url' => 'https://youtu.be/IKuFdoQBYvk'),
                array('title' => 'المحاضرة الأولى - الجزء السادس', 'url' => 'https://youtu.be/NvbLgHmohCU'),
                array('title' => 'المحاضرة الثانية - الجزء الأول', 'url' => 'https://youtu.be/JB3rG10QPvc'),
                array('title' => 'المحاضرة الثانية - الجزء الثاني', 'url' => 'https://youtu.be/0dklo9b6J48'),
                array('title' => 'المحاضرة الثانية - الجزء الثالث', 'url' => 'https://youtu.be/4GcRcBMu2gU'),
                array('title' => 'المحاضرة الثانية - الجزء الرابع', 'url' => 'https://youtu.be/TZlNS9nSjmk'),
                array('title' => 'المحاضرة الثانية - الجزء الخامس', 'url' => 'https://youtu.be/XBugwNKI-E0'),
                array('title' => 'المحاضرة الثانية - الجزء السادس', 'url' => 'https://youtu.be/C63EsVSuepY'),
            )
        ),
        array(
            'title' => 'دروس شرح الآجرومية للدفعة الجديدة',
            'science' => '✍️ علم النحو',
            'content' => '<p>دروس مبسطة في شرح الآجرومية</p>',
            'excerpt' => 'شرح الآجرومية للمبتدئين',
            'duration' => '13 حصة',
            'level' => 'المستوى الأول',
            'lessons' => array(
                array('title' => 'الحصة الأولى (مقدمات)', 'url' => 'https://youtu.be/fwMvNius2-s'),
                array('title' => 'الحصة الثانية', 'url' => 'https://youtu.be/LnWdT6NF18Y'),
                array('title' => 'الحصة الثالثة', 'url' => 'https://youtu.be/0c8AGNkpVqk'),
                array('title' => 'الحصة الرابعة', 'url' => 'https://youtu.be/RhYhYhQGNWg'),
                array('title' => 'الحصة الخامسة', 'url' => 'https://youtu.be/5-MyUF6WA3E'),
                array('title' => 'الحصة السادسة', 'url' => 'https://youtu.be/hmO8d2tkoaI'),
                array('title' => 'الحصة السابعة', 'url' => 'https://youtu.be/bSyRohpb3IY'),
                array('title' => 'الحصة الثامنة', 'url' => 'https://youtu.be/BosSglLUmWA'),
                array('title' => 'الحصة التاسعة', 'url' => 'https://youtu.be/IhSfdefKr2M'),
                array('title' => 'الحصة العاشرة', 'url' => 'https://youtu.be/x6ZKzOUne9Y'),
                array('title' => 'الحصة الحادية عشر', 'url' => 'https://youtu.be/GKkdJ7E_qxc'),
                array('title' => 'الحصة الثانية عشر', 'url' => 'https://youtu.be/5OTURyJghpY'),
                array('title' => 'حصة إعرابية عامة', 'url' => 'https://youtu.be/5APRciJrAkA'),
            )
        ),
        // علم الصرف
        array(
            'title' => 'متن الصرف الصغير',
            'science' => '🔠 علم الصرف',
            'content' => '<p>شرح متن الصرف الصغير</p>',
            'excerpt' => 'شرح الصرف الصغير',
            'duration' => '11 جزء',
            'level' => 'المستوى الثاني',
            'lessons' => array(
                array('title' => 'المحاضرة الأولى - الجزء الأول', 'url' => 'https://youtu.be/9Z10li_AS5E'),
                array('title' => 'المحاضرة الأولى - الجزء الثاني', 'url' => 'https://youtu.be/OS2JpKuacIY'),
                array('title' => 'المحاضرة الأولى - الجزء الثالث', 'url' => 'https://youtu.be/bCMnzg7Du30'),
                array('title' => 'المحاضرة الأولى - الجزء الرابع', 'url' => 'https://youtu.be/cz9xyUyoJow'),
                array('title' => 'المحاضرة الثانية - الجزء الأول', 'url' => 'https://youtu.be/sKcx4kBsPqM'),
                array('title' => 'المحاضرة الثانية - الجزء الثاني', 'url' => 'https://youtu.be/qlNCuJsSM7E'),
                array('title' => 'المحاضرة الثانية - الجزء الثالث', 'url' => 'https://youtu.be/na-XqYJ_nfA'),
                array('title' => 'المحاضرة الثالثة - الجزء الأول', 'url' => 'https://youtu.be/W6p9NYtVeTg'),
                array('title' => 'المحاضرة الثالثة - الجزء الثاني', 'url' => 'https://youtu.be/EzZcELYkW0A'),
                array('title' => 'المحاضرة الرابعة - الجزء الأول', 'url' => 'https://youtu.be/h8IJrB3u0P0'),
                array('title' => 'المحاضرة الرابعة - الجزء الثاني', 'url' => 'https://youtu.be/A3SRrEj1ceA'),
            )
        ),
        array(
            'title' => 'شرح كتاب الصرف الصغير',
            'science' => '🔠 علم الصرف',
            'content' => '<p>شرح مبسط لكتاب الصرف الصغير</p>',
            'excerpt' => 'شرح الصرف للمبتدئين',
            'duration' => '3 حصص',
            'level' => 'المستوى الأول',
            'lessons' => array(
                array('title' => 'الحصة الأولى', 'url' => 'https://youtu.be/2FLP6vY2mbA'),
                array('title' => 'الحصة الثانية', 'url' => 'https://youtu.be/YXt4PlURwr0'),
                array('title' => 'الحصة الثالثة', 'url' => 'https://youtu.be/NcpsQ6fGrg8'),
            )
        ),
        // علم التفسير
        array(
            'title' => 'شرح مقدمة في أصول التفسير لابن تيمية',
            'science' => '📘 علم التفسير',
            'content' => '<p>شرح مقدمة ابن تيمية في أصول التفسير</p>',
            'excerpt' => 'مقدمة في أصول التفسير',
            'duration' => '6 حصص',
            'level' => 'المستوى الثاني',
            'lessons' => array(
                array('title' => 'الحصة الأولى', 'url' => 'https://youtu.be/FVTnHhabavA'),
                array('title' => 'الحصة الثانية', 'url' => 'https://youtu.be/a483N_XMmw8'),
                array('title' => 'الحصة الثالثة - الجزء الأول', 'url' => 'https://youtu.be/knjZgZGSwOQ'),
                array('title' => 'الحصة الثالثة - الجزء الثاني', 'url' => 'https://youtu.be/-rCm3xWOjc'),
                array('title' => 'الحصة الرابعة', 'url' => 'https://youtu.be/XSFA3VOgsnE'),
                array('title' => 'الحصة الخامسة', 'url' => 'https://youtu.be/Uupk3WTOnF4'),
                array('title' => 'الحصة السادسة والأخيرة', 'url' => 'https://youtu.be/gjboPBCoInQ'),
            )
        ),
        array(
            'title' => 'فسحة تاريخية في علم التفسير',
            'science' => '📘 علم التفسير',
            'content' => '<p>رحلة تاريخية في علم التفسير وتطوره</p>',
            'excerpt' => 'فسحة تاريخية في علم التفسير',
            'duration' => 'محاضرة واحدة',
            'level' => 'المستوى الثاني',
            'lessons' => array(
                array('title' => 'فسحة تاريخية علم التفسير - الجزء 1', 'url' => 'https://youtu.be/Zu_PCWsQ0E8'),
            )
        ),
        // علم المنطق
        array(
            'title' => 'مقدمات في علم المنطق - مستوى متقدم',
            'science' => '🧩 علم المنطق',
            'content' => '<p>مقدمات متقدمة في علم المنطق</p>',
            'excerpt' => 'مقدمات في علم المنطق',
            'duration' => '6 أجزاء',
            'level' => 'المستوى الثاني',
            'lessons' => array(
                array('title' => 'المحاضرة الأولى - الجزء الأول', 'url' => 'https://youtu.be/k8dHUc2MNA8'),
                array('title' => 'المحاضرة الأولى - الجزء الثاني', 'url' => 'https://youtu.be/Uf7PCkVqXv4'),
                array('title' => 'المحاضرة الأولى - الجزء الثالث', 'url' => 'https://youtu.be/QxA19apZ_k0'),
                array('title' => 'المحاضرة الثانية - الجزء الأول', 'url' => 'https://youtu.be/PKzEdJ3_EoQ'),
                array('title' => 'المحاضرة الثانية - الجزء الثاني', 'url' => 'https://youtu.be/NTnHpxbSZ_Y'),
                array('title' => 'المحاضرة الثانية - الجزء الأخير', 'url' => 'https://youtu.be/NEfzUDX3ESo'),
            )
        ),
        array(
            'title' => 'مقدمات في علم المنطق - تمهيدي',
            'science' => '🧩 علم المنطق',
            'content' => '<p>مقدمات تمهيدية في علم المنطق</p>',
            'excerpt' => 'مقدمات تمهيدية في المنطق',
            'duration' => 'حصتان',
            'level' => 'المستوى الأول',
            'lessons' => array(
                array('title' => 'الحصة الأولى', 'url' => 'https://youtu.be/0ByVdfN4eJM'),
                array('title' => 'الحصة الثانية', 'url' => 'https://youtu.be/cpoZIVwNGUo'),
            )
        ),
        // علم مصطلح الحديث
        array(
            'title' => 'شرح البيقونية - تمهيدي',
            'science' => '📜 علم مصطلح الحديث',
            'content' => '<p>شرح منظومة البيقونية في مصطلح الحديث</p>',
            'excerpt' => 'شرح البيقونية للمبتدئين',
            'duration' => '7 حصص',
            'level' => 'المستوى الأول',
            'lessons' => array(
                array('title' => 'الحصة الأولى', 'url' => 'https://youtu.be/3OJ1SmnyqgI'),
                array('title' => 'الحصة الثانية', 'url' => 'https://youtu.be/t-6vWESGPDo'),
                array('title' => 'الحصة الثالثة', 'url' => 'https://youtu.be/DDIwXQ_-CS8'),
                array('title' => 'الحصة الرابعة', 'url' => 'https://youtu.be/MJ2tqDleV7o'),
                array('title' => 'الحصة الخامسة', 'url' => 'https://youtu.be/ZWSTjG4c8Xw'),
                array('title' => 'الحصة السادسة', 'url' => 'https://youtu.be/NzehgV5Vgbg'),
                array('title' => 'الحصة السابعة', 'url' => 'https://youtu.be/fL8fBR57pOI'),
            )
        ),
        array(
            'title' => 'قواعد التحديث من فنون مصطلح الحديث',
            'science' => '📜 علم مصطلح الحديث',
            'content' => '<p>دراسة قواعد التحديث وفنون مصطلح الحديث</p>',
            'excerpt' => 'قواعد التحديث',
            'duration' => '12 حصة',
            'level' => 'المستوى الثاني',
            'lessons' => array(
                array('title' => 'الحصة الأولى', 'url' => 'https://youtu.be/4okfUT06tbs'),
                array('title' => 'الحصة الثانية', 'url' => 'https://youtu.be/M9nWVlGrvv8'),
                array('title' => 'الحصة الثالثة', 'url' => 'https://youtu.be/9ms0-qeDQR0'),
                array('title' => 'الحصة الرابعة', 'url' => 'https://youtu.be/K03A0rshfks'),
                array('title' => 'الحصة الخامسة', 'url' => 'https://youtu.be/ruzjMfUUviw'),
                array('title' => 'الحصة السادسة', 'url' => 'https://youtu.be/gG5JUYyMzoI'),
                array('title' => 'الحصة السابعة', 'url' => 'https://youtu.be/t2sXQ3ABJ7M'),
                array('title' => 'الحصة الثامنة', 'url' => 'https://youtu.be/88xTFmia4Vg'),
                array('title' => 'الحصة التاسعة', 'url' => 'https://youtu.be/3cp_oIZowr0'),
                array('title' => 'الحصة العاشرة', 'url' => 'https://youtu.be/q0uMLoVhZW8'),
                array('title' => 'الحصة الحادية عشرة', 'url' => 'https://youtu.be/RW5df0RhP1w'),
                array('title' => 'الحصة الأخيرة', 'url' => 'https://youtu.be/0PxtftB43XM'),
            )
        ),
        array(
            'title' => 'شرح متن البيقونية',
            'science' => '📜 علم مصطلح الحديث',
            'content' => '<p>شرح مفصل لمتن البيقونية في مصطلح الحديث</p>',
            'excerpt' => 'شرح متن البيقونية',
            'duration' => '18 درس',
            'level' => 'المستوى الثاني',
            'lessons' => array(
                array('title' => 'مقدمة - الجزء الأول', 'url' => 'https://youtu.be/dts8gBZZpnQ'),
                array('title' => 'مقدمة - الجزء الثاني', 'url' => 'https://youtu.be/E0X_RlKClSQ'),
                array('title' => 'شرح الأبيات 1-2', 'url' => 'https://youtu.be/n_yNykVLzxY'),
                array('title' => 'شرح الأبيات 3-4', 'url' => 'https://youtu.be/CkhZhe0DnpM'),
                array('title' => 'شرح: الحديث الحسن والضعيف', 'url' => 'https://youtu.be/hmhLH1X6PMI'),
                array('title' => 'شرح: الحديث المرفوع والمقطوع', 'url' => 'https://youtu.be/zqRfS3yiFi4'),
                array('title' => 'شرح الحديث: المسلسل والمتصل', 'url' => 'https://youtu.be/8c8fxSa3lkY'),
                array('title' => 'شرح الحديث العزيز والمشهور', 'url' => 'https://youtu.be/LQT4ifchMpM'),
                array('title' => 'شرح الحديث المعنعن والمؤنئن', 'url' => 'https://youtu.be/1N1VQxLoGbQ'),
                array('title' => 'شرح الحديث الموقوف والمرسل', 'url' => 'https://youtu.be/LY_2h2Mn37g'),
                array('title' => 'شرح الحديث: الغريب', 'url' => 'https://youtu.be/ZSX6K1DXJOY'),
                array('title' => 'شرح الحديث المنقطع والعضل', 'url' => 'https://youtu.be/Pv8UQ6vTbXY'),
                array('title' => 'شرح الحديث: المدلس وبيان أنواعه', 'url' => 'https://youtu.be/ulgUFTHF7PM'),
                array('title' => 'شرح الحديث الشاذ والمقلوب', 'url' => 'https://youtu.be/Gz64iLNY_uw'),
                array('title' => 'شرح الحديث الفرد والمعلل', 'url' => 'https://youtu.be/_APgFjXvvCE'),
                array('title' => 'شرح الحديث: المضطرب والمدرج', 'url' => 'https://youtu.be/UMsVo-DISkA'),
                array('title' => 'شرح الحديث: المدبج، المتفق، المؤتلف، المختلف', 'url' => 'https://youtu.be/YiDzIWa8GzA'),
                array('title' => 'المنكر والمتروك والموضوع ثم خاتمة النظم', 'url' => 'https://youtu.be/dbPpdEdphqk'),
            )
        ),
        // أصول الفقه
        array(
            'title' => 'متن نظم الورقات',
            'science' => '⚖️ أصول الفقه',
            'content' => '<p>شرح متن نظم الورقات في أصول الفقه</p>',
            'excerpt' => 'متن نظم الورقات في أصول الفقه',
            'duration' => '54 محاضرة',
            'level' => 'المستوى الثاني',
            'lessons' => array(
                array('title' => 'المحاضرة الأولى', 'url' => 'https://youtu.be/8pQiXOCjmw8'),
                array('title' => 'المحاضرة الثانية', 'url' => 'https://youtu.be/HYkYWm0teCk'),
                array('title' => 'المحاضرة الثالثة - الجزء الأول', 'url' => 'https://youtu.be/oCxV3TS6-sE'),
                array('title' => 'المحاضرة الثالثة - الجزء الثاني', 'url' => 'https://youtu.be/QN3byHIBG5U'),
                array('title' => 'المحاضرة الرابعة', 'url' => 'https://youtu.be/VWwwtIOLZDk'),
                array('title' => 'المحاضرة الخامسة', 'url' => 'https://youtu.be/kTdRTbFkXF8'),
                array('title' => 'المحاضرة السادسة', 'url' => 'https://youtu.be/s8y2rYQJpEg'),
                array('title' => 'المحاضرة السابعة', 'url' => 'https://youtu.be/O5VVH-124P8'),
                array('title' => 'المحاضرة الثامنة - الجزء الأول', 'url' => 'https://youtu.be/QK_Fl7ee_6Q'),
                array('title' => 'المحاضرة الثامنة - الجزء الثاني', 'url' => 'https://youtu.be/Y0JK_wVE0w0'),
                array('title' => 'المحاضرة التاسعة', 'url' => 'https://youtu.be/1l5d-WnVHgo'),
                array('title' => 'المحاضرة العاشرة', 'url' => 'https://youtu.be/withCllHn_Y'),
                array('title' => 'المحاضرة الحادية عشرة', 'url' => 'https://youtu.be/no_sVAbuers'),
                array('title' => 'المحاضرة الثانية عشرة', 'url' => 'https://youtu.be/FYQ_F1AU_3A'),
                array('title' => 'المحاضرة الثالثة عشرة', 'url' => 'https://youtu.be/wf8G0N5k7_Y'),
                array('title' => 'المحاضرة الرابعة عشرة - الجزء الأول', 'url' => 'https://youtu.be/Nwm4oZJTLd0'),
                array('title' => 'المحاضرة الرابعة عشرة - الجزء الثاني', 'url' => 'https://youtu.be/6TsvVfF5f6s'),
                array('title' => 'المحاضرة الخامسة عشرة', 'url' => 'https://youtu.be/8s9oDivNQaM'),
                array('title' => 'المحاضرة السادسة عشرة', 'url' => 'https://youtu.be/107mffO3xmk'),
                array('title' => 'المحاضرة السابعة عشرة', 'url' => 'https://youtu.be/8hqUC-1azNQ'),
                array('title' => 'المحاضرة الثامنة عشرة', 'url' => 'https://youtu.be/K3E_T1Jazbo'),
                array('title' => 'المحاضرة التاسعة عشرة', 'url' => 'https://youtu.be/r4y9dbu2wNU'),
                array('title' => 'المحاضرة العشرون', 'url' => 'https://youtu.be/F9DN31yTjoc'),
                array('title' => 'المحاضرة الحادية و العشرون', 'url' => 'https://youtu.be/DgJoawrwLao'),
                array('title' => 'المحاضرة الثانية و عشرون', 'url' => 'https://youtu.be/r9ELsDxETSE'),
                array('title' => 'المحاضرة الثالثة و عشرون', 'url' => 'https://youtu.be/46K0YmOrDv8'),
                array('title' => 'المحاضرة الرابعة و عشرون', 'url' => 'https://youtu.be/8PnFSwmbFBg'),
                array('title' => 'المحاضرة الخامسة و العشرون', 'url' => 'https://youtu.be/uIa3d96EWZc'),
                array('title' => 'المحاضرة السادسة و العشرون', 'url' => 'https://youtu.be/SioECHTXcm0'),
                array('title' => 'المحاضرة السابعة و العشرون', 'url' => 'https://youtu.be/LmlrQ6DFfCE'),
                array('title' => 'المحاضرة الثامنة و العشرون', 'url' => 'https://youtu.be/WDfgRnC5cQA'),
                array('title' => 'المحاضرة التاسعة و العشرون', 'url' => 'https://youtu.be/lcHpIi8ewYY'),
                array('title' => 'المحاضرة الثلاثون', 'url' => 'https://youtu.be/AS5C-0G40eI'),
                array('title' => 'المحاضرة الحادية و الثلاثون', 'url' => 'https://youtu.be/S0lp-LMoJ6w'),
                array('title' => 'المحاضرة الثانية و الثلاثون', 'url' => 'https://youtu.be/XbiPEReCqyM'),
                array('title' => 'المحاضرة الثالثة و الثلاثون', 'url' => 'https://youtu.be/qfSbtefh32g'),
                array('title' => 'المحاضرة الرابعة و الثلاثون', 'url' => 'https://youtu.be/4Mbt1NICf1A'),
                array('title' => 'المحاضرة الخامسة و الثلاثون', 'url' => 'https://youtu.be/xo9UOgWsfz8'),
                array('title' => 'المحاضرة السادسة و الثلاثون', 'url' => 'https://youtu.be/K4r7xeob6_M'),
                array('title' => 'المحاضرة السابعة و الثلاثون', 'url' => 'https://youtu.be/Io4PfdZ854Y'),
                array('title' => 'المحاضرة الثامنة و الثلاثون', 'url' => 'https://youtu.be/l213dEaAE5I'),
                array('title' => 'المحاضرة التاسعة و الثلاثون', 'url' => 'https://youtu.be/KIdu5WnjUpU'),
                array('title' => 'المحاضرة الأربعون', 'url' => 'https://youtu.be/G4p5kGGw0oo'),
                array('title' => 'المحاضرة الحادية و الأربعون', 'url' => 'https://youtu.be/hUY3_xPWjq4'),
                array('title' => 'المحاضرة الثانية و الأربعون', 'url' => 'https://youtu.be/4LMiS2Qr-lY'),
                array('title' => 'المحاضرة الثالثة و الأربعون', 'url' => 'https://youtu.be/hRBZ12sQITM'),
                array('title' => 'المحاضرة الرابعة و الأربعون', 'url' => 'https://youtu.be/zNbr3g5paCg'),
                array('title' => 'المحاضرة الخامسة و الأربعون', 'url' => 'https://youtu.be/SU9nYKPOjLo'),
                array('title' => 'المحاضرة السادسة و الأربعون', 'url' => 'https://youtu.be/rMQVXNmZTv4'),
                array('title' => 'المحاضرة السابعة و الأربعون', 'url' => 'https://youtu.be/JSMzLDWCX0U'),
                array('title' => 'المحاضرة الثامنة و الأربعون', 'url' => 'https://youtu.be/Ric6ZCPqyUk'),
                array('title' => 'المحاضرة التاسعة و الأربعون', 'url' => 'https://youtu.be/vs3IXjrfbu8'),
                array('title' => 'المحاضرة الخمسون', 'url' => 'https://youtu.be/VpeFba5HZFM'),
                array('title' => 'المحاضرة الحادية و الخمسون', 'url' => 'https://youtu.be/o07pJoWXR-M'),
                array('title' => 'المحاضرة الثانية و الخمسون', 'url' => 'https://youtu.be/N_6rDnTmXB0'),
                array('title' => 'المحاضرة الثالثة و الستون', 'url' => 'https://youtu.be/nNR2zDxgaOg'),
                array('title' => 'المحاضرة الرابعة و الستون', 'url' => 'https://youtu.be/aIWf2ScRA1E'),
            )
        ),
        array(
            'title' => 'مذكرة في أصول الفقه',
            'science' => '⚖️ أصول الفقه',
            'content' => '<p>دراسة مذكرة مختصرة في أصول الفقه</p>',
            'excerpt' => 'مذكرة في أصول الفقه',
            'duration' => '13 حصة',
            'level' => 'المستوى الثاني',
            'lessons' => array(
                array('title' => 'الحصة الثانية', 'url' => 'https://youtu.be/06Id2lHReuU'),
                array('title' => 'الحصة الثالثة', 'url' => 'https://youtu.be/HR3xGtM4RJ0'),
                array('title' => 'الحصة الرابعة', 'url' => 'https://youtu.be/t1goyav1pxs'),
                array('title' => 'الحصة الخامسة', 'url' => 'https://youtu.be/_CZTbX-2Id4'),
                array('title' => 'الحصة السادسة', 'url' => 'https://youtu.be/GrT9z9kmCuo'),
                array('title' => 'الحصة السابعة', 'url' => 'https://youtu.be/qWFJ8j4tu6Q'),
                array('title' => 'الحصة الثامنة', 'url' => 'https://youtu.be/7WY9GFiVEn8'),
                array('title' => 'الحصة التاسعة', 'url' => 'https://youtu.be/_qBZftC0xug'),
                array('title' => 'الحصة العاشرة', 'url' => 'https://youtu.be/syra_mk_U-8'),
                array('title' => 'الحصة الحادية عشر', 'url' => 'https://youtu.be/1EDBTGSkhHY'),
                array('title' => 'الحصة الثانية عشرة', 'url' => 'https://youtu.be/BnUmk_SeHJI'),
                array('title' => 'الحصة الثالثة عشرة', 'url' => 'https://youtu.be/as9YHWhf1_I'),
                array('title' => 'تصحيح وقراءة لأبيات مراقي السعود', 'url' => 'https://youtu.be/AktFJGHNjz4'),
            )
        ),
        array(
            'title' => 'شرح نظم الورقات للعمريطي',
            'science' => '⚖️ أصول الفقه',
            'content' => '<p>شرح مبسط لنظم الورقات للعمريطي</p>',
            'excerpt' => 'شرح نظم الورقات للعمريطي',
            'duration' => '19 حصة',
            'level' => 'المستوى الأول',
            'lessons' => array(
                array('title' => 'الحصة الأولى', 'url' => 'https://youtu.be/0O2AyBIWBbg'),
                array('title' => 'الحصة الثانية', 'url' => 'https://youtu.be/TA8G1RD0bmc'),
                array('title' => 'الحصة الثالثة', 'url' => 'https://youtu.be/k9apqAf84qI'),
                array('title' => 'الحصة الرابعة', 'url' => 'https://youtu.be/YAauh36za_A'),
                array('title' => 'الحصة الخامسة', 'url' => 'https://youtu.be/NCZL3lr2aT'),
                array('title' => 'الحصة السادسة', 'url' => 'https://youtu.be/ZI9xayD_eyw'),
                array('title' => 'الحصة السابعة', 'url' => 'https://youtu.be/2G-Dn-jUZ6M'),
                array('title' => 'الحصة الثامنة', 'url' => 'https://youtu.be/98fwqadgw7M'),
                array('title' => 'الحصة التاسعة', 'url' => 'https://youtu.be/ZxSUr_dVVx0'),
                array('title' => 'الحصة العاشرة', 'url' => 'https://youtu.be/J9gCxGhnTcE'),
                array('title' => 'الحصة الحادية عشرة', 'url' => 'https://youtu.be/AMiM-HBxweU'),
                array('title' => 'الحصة الثانية عشرة', 'url' => 'https://youtu.be/ZOc0tPJ8bWw'),
                array('title' => 'الحصة الثالثة عشرة', 'url' => 'https://youtu.be/ZOc0tPJ8bWd'),
                array('title' => 'الحصة الرابعة عشرة', 'url' => 'https://youtu.be/QRqyRGPNFgw'),
                array('title' => 'الحصة الخامسة عشرة', 'url' => 'https://youtu.be/iDavp_VoHWc'),
                array('title' => 'الحصة السادسة عشرة', 'url' => 'https://youtu.be/cU9Omw_21hk'),
                array('title' => 'الحصة السابعة عشر', 'url' => 'https://youtu.be/mNLONm11MLk'),
                array('title' => 'الحصة الثامنة عشرة', 'url' => 'https://youtu.be/I0FCRlE_GNk'),
                array('title' => 'الحصة الأخيرة', 'url' => 'https://youtu.be/rx0BJZnD87Y'),
            )
        ),
        // علم العقيدة (علم الكلام)
        array(
            'title' => 'شرح رسالة أبي زيد القيرواني',
            'science' => '🕋 علم العقيدة (علم الكلام)',
            'content' => '<p>شرح رسالة أبي زيد القيرواني في العقيدة</p>',
            'excerpt' => 'شرح رسالة أبي زيد القيرواني',
            'duration' => '5 حصص',
            'level' => 'المستوى الثاني',
            'lessons' => array(
                array('title' => 'الحصة الأولى', 'url' => 'https://youtu.be/t3h8ty-iL1g'),
                array('title' => 'الحصة الثانية', 'url' => 'https://youtu.be/cri8cResyPE'),
                array('title' => 'الحصة الثالثة', 'url' => 'https://youtu.be/9Oatfc-pEfE'),
                array('title' => 'الحصة الرابعة', 'url' => 'https://youtu.be/bRKQBudVcmc'),
                array('title' => 'الحصة الخامسة', 'url' => 'https://youtu.be/pXGoMW4cVwU'),
            )
        ),
        // علم التزكية
        array(
            'title' => 'شرح كتاب "مبادئ التصوف وهوادي التعرف" من متن ابن عاشر',
            'science' => '🌿 علم التزكية',
            'content' => '<p>شرح كتاب مبادئ التصوف وهوادي التعرف من متن ابن عاشر</p>',
            'excerpt' => 'مبادئ التصوف وهوادي التعرف',
            'duration' => 'حصتان',
            'level' => 'المستوى الثاني',
            'lessons' => array(
                array('title' => 'الحصة الأولى', 'url' => 'https://youtu.be/Aff79iMY3XI'),
                array('title' => 'الحصة الثانية', 'url' => 'https://youtu.be/3mvGvyQe-NU'),
            )
        ),
        // مطالعة (كتاب مختار)
        array(
            'title' => 'شرح كتاب حلية طالب العلم',
            'science' => '📚 مطالعة (كتاب مختار)',
            'content' => '<p>شرح كتاب حلية طالب العلم للشيخ بكر أبو زيد</p>',
            'excerpt' => 'شرح كتاب حلية طالب العلم',
            'duration' => '5 حصص',
            'level' => 'المستوى الثاني',
            'lessons' => array(
                array('title' => 'الحصة الأولى', 'url' => 'https://youtu.be/d31wjBfoMWU'),
                array('title' => 'الحصة الثانية', 'url' => 'https://youtu.be/ebw3Wfe8-Q4'),
                array('title' => 'الحصة الثالثة', 'url' => 'https://youtu.be/-rxkz2EKfaY'),
                array('title' => 'الحصة الرابعة', 'url' => 'https://youtu.be/h_J5rkUHtO4'),
                array('title' => 'الحصة الخامسة', 'url' => 'https://youtu.be/CvlboGOzrHY'),
            )
        ),
        array(
            'title' => 'قراءة في كتاب حِلية طالب العِلم',
            'science' => '📚 مطالعة (كتاب مختار)',
            'content' => '<p>قراءة موجهة في كتاب حلية طالب العلم</p>',
            'excerpt' => 'قراءة في كتاب حلية طالب العلم',
            'duration' => '6 حصص',
            'level' => 'المستوى الأول',
            'lessons' => array(
                array('title' => 'الحصة الأولى', 'url' => 'https://youtu.be/5C-Wg5yb09s'),
                array('title' => 'الحصة الثانية', 'url' => 'https://youtu.be/f9yitm66g3g'),
                array('title' => 'الحصة الثالثة', 'url' => 'https://youtu.be/EPR2E3HsMv4'),
                array('title' => 'الحصة الرابعة', 'url' => 'https://youtu.be/iS8qdXFzF7E'),
                array('title' => 'الحصة الخامسة', 'url' => 'https://youtu.be/ugK6nlvR6Fs'),
                array('title' => 'الحصة السادسة والأخيرة', 'url' => 'https://youtu.be/wFgENz7vE0Y'),
            )
        ),
        array(
            'title' => 'كتاب التعالم لبكر أبو زيد',
            'science' => '📚 مطالعة (كتاب مختار)',
            'content' => '<p>قراءة وشرح كتاب التعالم للشيخ بكر أبو زيد</p>',
            'excerpt' => 'كتاب التعالم لبكر أبو زيد',
            'duration' => '8 حصص',
            'level' => 'المستوى الثاني',
            'lessons' => array(
                array('title' => 'الحصة الأولى', 'url' => 'https://youtu.be/2W5LDn4_gQk'),
                array('title' => 'الحصة الثانية', 'url' => 'https://youtu.be/LU0FWBjMja4'),
                array('title' => 'الحصة الثالثة', 'url' => 'https://youtu.be/f6TrHohLKKY'),
                array('title' => 'الحصة الرابعة', 'url' => 'https://youtu.be/iAruBdu2Iks'),
                array('title' => 'الحصة الخامسة', 'url' => 'https://youtu.be/cHkcrKbkLUs'),
                array('title' => 'الحصة السادسة', 'url' => 'https://youtu.be/ZHaWc0-FcI0'),
                array('title' => 'الحصة السابعة', 'url' => 'https://youtu.be/fA0NYoANhBk'),
                array('title' => 'الحصة الثامنة والاخيرة', 'url' => 'https://youtu.be/SEs3pT2ZVik'),
            )
        ),
        array(
            'title' => 'شرح الشمقمية',
            'science' => '📚 مطالعة (كتاب مختار)',
            'content' => '<p>شرح الشمقمية في الآداب والأخلاق</p>',
            'excerpt' => 'شرح الشمقمية',
            'duration' => '17 حصة',
            'level' => 'المستوى الثاني',
            'lessons' => array(
                array('title' => 'الحصة الأولى', 'url' => 'https://youtu.be/KL_1M8NpHz4'),
                array('title' => 'الحصة الثانية', 'url' => 'https://youtu.be/nKoKd1iqglA'),
                array('title' => 'الحصة الثالثة', 'url' => 'https://youtu.be/o4EV6Sf6N4o'),
                array('title' => 'الحصة الرابعة', 'url' => 'https://youtu.be/nHVoGUIYryo'),
                array('title' => 'الحصة الخامسة', 'url' => 'https://youtu.be/tdVJajkOwds'),
                array('title' => 'الحصة السادسة', 'url' => 'https://youtu.be/RRXMhkXN2rY'),
                array('title' => 'الحصة السابعة', 'url' => 'https://youtu.be/App5Rx8-FwE'),
                array('title' => 'الحصة الثامنة', 'url' => 'https://youtu.be/hSjzirNYiWI'),
                array('title' => 'الحصة التاسعة', 'url' => 'https://youtu.be/bugM-OvJJok'),
                array('title' => 'الحصة العاشرة', 'url' => 'https://youtu.be/xbwSkl9SovM'),
                array('title' => 'الحصة الحادية عشرة', 'url' => 'https://youtu.be/HONlqc6Csq4'),
                array('title' => 'الحصة الثانية عشرة', 'url' => 'https://youtu.be/pE4upUegoO0'),
                array('title' => 'الحصة الثالثة عشرة', 'url' => 'https://youtu.be/7WmsdzEAhBA'),
                array('title' => 'الحصة الرابعة عشرة', 'url' => 'https://youtu.be/FC73f95j-_I'),
                array('title' => 'الحصة الخامسة عشرة', 'url' => 'https://youtu.be/f79f0xMBPv0'),
                array('title' => 'الحصة السادسة عشرة', 'url' => 'https://youtu.be/quDafaQSCjM'),
                array('title' => 'الحصة السابعة عشرة', 'url' => 'https://youtu.be/KkFL41cMAT8'),
            )
        ),
        array(
            'title' => 'شرح قطر الندى',
            'science' => '📚 مطالعة (كتاب مختار)',
            'content' => '<p>شرح كتاب قطر الندى في النحو</p>',
            'excerpt' => 'شرح قطر الندى',
            'duration' => '3 حصص',
            'level' => 'المستوى الأول',
            'lessons' => array(
                array('title' => 'الحصة الأولى', 'url' => 'https://youtu.be/yFE7nOinulU'),
                array('title' => 'الحصة الثانية', 'url' => 'https://youtu.be/IwhkWSKYJ6E'),
                array('title' => 'الحصة الثالثة', 'url' => 'https://youtu.be/unwnOKVl19k'),
            )
        ),
        array(
            'title' => 'قراءة في مقالات البشير الإبراهيمي',
            'science' => '📚 مطالعة (كتاب مختار)',
            'content' => '<p>قراءة موجهة في مقالات البشير الإبراهيمي</p>',
            'excerpt' => 'قراءة في مقالات البشير الإبراهيمي',
            'duration' => '3 حصص',
            'level' => 'المستوى الأول',
            'lessons' => array(
                array('title' => 'الحصة الأولى', 'url' => 'https://youtu.be/didsPF-DNIQ'),
                array('title' => 'الحصة الثانية', 'url' => 'https://youtu.be/eMo0rwIZrnE'),
                array('title' => 'الحصة الثالثة', 'url' => 'https://youtu.be/JCf1DQzsLMk'),
            )
        ),
        // مسار تعليمي
        array(
            'title' => 'محاضرة تحفيزية وكلام مجمل عن المقرر',
            'science' => '🎓 مسار تعليمي',
            'content' => '<p>محاضرة تحفيزية للطلاب مع كلام مجمل عن المقرر الدراسي</p>',
            'excerpt' => 'محاضرة تحفيزية وكلام مجمل عن المقرر',
            'duration' => 'محاضرة واحدة',
            'level' => 'المستوى الثاني',
            'lessons' => array(
                array('title' => 'مقدمة حوارية تحفيزية وكلام مجمل عن المقرر', 'url' => 'https://youtu.be/MPnmiG-rpC8'),
            )
        ),
    );

    $total_lessons = 0;
    foreach ($detailed_courses as $course_data) {
        $science_term = get_term_by('name', $course_data['science'], 'fiqh_course_science');

        $course_id = wp_insert_post(array(
            'post_title' => $course_data['title'],
            'post_content' => $course_data['content'],
            'post_excerpt' => $course_data['excerpt'],
            'post_status' => 'publish',
            'post_type' => 'fiqh_course',
            'post_author' => 1,
        ));

        if ($course_id && !is_wp_error($course_id)) {
            // إضافة التصنيف
            if ($science_term) {
                wp_set_post_terms($course_id, array($science_term->term_id), 'fiqh_course_science');
            }

            // إضافة meta data
            update_post_meta($course_id, '_fiqh_course_duration', $course_data['duration']);

            // ربط المقرر بالمستوى المناسب
            if (isset($batch_ids[$course_data['level']])) {
                update_post_meta($course_id, '_fiqh_course_levels', array($batch_ids[$course_data['level']]));
            }

            // إنشاء الدروس (المحاضرات)
            if (!empty($course_data['lessons'])) {
                foreach ($course_data['lessons'] as $index => $lesson) {
                    $lesson_id = wp_insert_post(array(
                        'post_title' => $lesson['title'],
                        'post_content' => '<p>محاضرة من مقرر: ' . $course_data['title'] . '</p>',
                        'post_status' => 'publish',
                        'post_type' => 'fiqh_lesson',
                        'post_author' => 1,
                        'menu_order' => $index + 1,
                    ));

                    if ($lesson_id && !is_wp_error($lesson_id)) {
                        update_post_meta($lesson_id, '_fiqh_lesson_course_id', $course_id);
                        update_post_meta($lesson_id, '_fiqh_lesson_video_url', $lesson['url']);
                        update_post_meta($lesson_id, '_fiqh_lesson_duration', '30-45 دقيقة');
                        $total_lessons++;
                    }
                }
            }

            echo '<p style="color: green;">✅ تم إنشاء مقرر: <strong>' . $course_data['title'] . '</strong> مع <strong>' . count($course_data['lessons']) . '</strong> محاضرة (' . $course_data['level'] . ')</p>';
        }
    }

    // 7. ملخص نهائي
    echo '<h2>✅ اكتمل إنشاء المحتوى التجريبي!</h2>';
    echo '<div class="updated"><p><strong>تم بنجاح!</strong> تم إنشاء محتوى تجريبي كامل بالبيانات الحقيقية.</p></div>';

    echo '<h3>ملخص المحتوى المنشأ:</h3>';
    echo '<ul>';
    echo '<li>✅ <strong>12</strong> علم شرعي</li>';
    echo '<li>✅ <strong>2</strong> مستوى دراسي (الأول والثاني)</li>';
    echo '<li>✅ <strong>3</strong> مدراء</li>';
    echo '<li>✅ <strong>12</strong> طالبة في المستوى الثاني</li>';
    echo '<li>✅ <strong>15</strong> طالب/ة في المستوى الأول</li>';
    echo '<li>✅ <strong>' . count($detailed_courses) . '</strong> مقرر دراسي مفصل</li>';
    echo '<li>✅ <strong>' . $total_lessons . '</strong> محاضرة مع روابط يوتيوب</li>';
    echo '</ul>';

    echo '<h3>تفصيل المقررات حسب العلوم:</h3>';
    echo '<ul>';
    echo '<li>📚 <strong>علم الفقه:</strong> 4 مقررات (2 للمستوى الأول، 2 للمستوى الثاني)</li>';
    echo '<li>📚 <strong>علم الميراث:</strong> 1 مقرر (المستوى الثاني)</li>';
    echo '<li>📚 <strong>علم النحو:</strong> 2 مقرر (1 لكل مستوى)</li>';
    echo '<li>📚 <strong>علم الصرف:</strong> 2 مقرر (1 لكل مستوى)</li>';
    echo '<li>📚 <strong>علم التفسير:</strong> 2 مقرر (المستوى الثاني)</li>';
    echo '<li>📚 <strong>علم المنطق:</strong> 2 مقرر (1 لكل مستوى)</li>';
    echo '<li>📚 <strong>علم مصطلح الحديث:</strong> 3 مقررات (1 للمستوى الأول، 2 للمستوى الثاني)</li>';
    echo '<li>📚 <strong>أصول الفقه:</strong> 3 مقررات (1 للمستوى الأول، 2 للمستوى الثاني)</li>';
    echo '<li>📚 <strong>علم العقيدة (علم الكلام):</strong> 1 مقرر (المستوى الثاني)</li>';
    echo '<li>📚 <strong>علم التزكية:</strong> 1 مقرر (المستوى الثاني)</li>';
    echo '<li>📚 <strong>مطالعة (كتاب مختار):</strong> 6 مقررات (3 لكل مستوى)</li>';
    echo '<li>📚 <strong>مسار تعليمي:</strong> 1 مقرر (المستوى الثاني)</li>';
    echo '</ul>';

    echo '<h3>معلومات تسجيل الدخول:</h3>';
    echo '<p><strong>ملاحظة:</strong> تم إنشاء الحسابات بكلمات المرور المحددة من قبل الإدارة.</p>';
    echo '<p><strong>الإدارة:</strong> mohamed, admin, عبد الحميد كرومي</p>';

    echo '<p style="margin-top: 20px;"><a href="' . home_url() . '" class="button button-primary button-large">زيارة الموقع</a> ';
    echo '<a href="' . admin_url('edit.php?post_type=fiqh_course&page=fiqh-levels') . '" class="button button-large">إدارة المستويات</a></p>';
}
