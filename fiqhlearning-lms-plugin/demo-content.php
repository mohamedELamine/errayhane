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

    // 6. إنشاء مقررات تجريبية
    echo '<h2>6️⃣ إنشاء المقررات الدراسية</h2>';
    $courses_data = array(
        array(
            'title' => 'فقه العبادات - المذهب المالكي',
            'science' => '⚖️ علم الفقه',
            'content' => '<p>مقرر شامل في فقه العبادات على المذهب المالكي، يشمل دراسة الطهارة والصلاة والزكاة والصيام والحج.</p>',
            'excerpt' => 'دراسة شاملة لفقه العبادات على المذهب المالكي',
            'duration' => '16 أسبوع',
            'level' => 'المستوى الأول'
        ),
        array(
            'title' => 'مصطلح الحديث',
            'science' => '📜 علم مصطلح الحديث',
            'content' => '<p>دراسة علم مصطلح الحديث وأنواع الأحاديث والرواة وطرق التصحيح والتضعيف.</p>',
            'excerpt' => 'مقدمة في علم مصطلح الحديث',
            'duration' => '12 أسبوع',
            'level' => 'المستوى الأول'
        ),
        array(
            'title' => 'تفسير جزء عم',
            'science' => '📘 علم التفسير',
            'content' => '<p>دراسة تفسير سور جزء عم مع بيان أسباب النزول والإعراب والفوائد.</p>',
            'excerpt' => 'تفسير مفصل لسور جزء عم',
            'duration' => '10 أسابيع',
            'level' => 'المستوى الأول'
        ),
        array(
            'title' => 'العقيدة الطحاوية',
            'science' => '🕋 علم العقيدة (علم الكلام)',
            'content' => '<p>شرح متن العقيدة الطحاوية للإمام الطحاوي رحمه الله.</p>',
            'excerpt' => 'شرح العقيدة الطحاوية',
            'duration' => '14 أسبوع',
            'level' => 'المستوى الثاني'
        ),
        array(
            'title' => 'النحو الواضح',
            'science' => '✍️ علم النحو',
            'content' => '<p>دراسة قواعد النحو العربي بأسلوب واضح ومبسط.</p>',
            'excerpt' => 'تعلم قواعد النحو العربي',
            'duration' => '12 أسبوع',
            'level' => 'المستوى الأول'
        ),
        array(
            'title' => 'أصول الفقه - مقدمة',
            'science' => '⚖️ أصول الفقه',
            'content' => '<p>مقدمة في علم أصول الفقه وقواعده الأساسية.</p>',
            'excerpt' => 'مدخل إلى علم أصول الفقه',
            'duration' => '14 أسبوع',
            'level' => 'المستوى الثاني'
        ),
    );

    $course_ids = array();
    foreach ($courses_data as $course_data) {
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

            $course_ids[] = $course_id;
            echo '<p style="color: green;">✅ تم إنشاء مقرر: <strong>' . $course_data['title'] . '</strong> (' . $course_data['level'] . ')</p>';
        }
    }

    // 7. ملخص نهائي
    echo '<h2>✅ اكتمل إنشاء المحتوى التجريبي!</h2>';
    echo '<div class="updated"><p><strong>تم بنجاح!</strong> تم إنشاء محتوى تجريبي كامل بالبيانات الحقيقية.</p></div>';

    echo '<h3>ملخص المحتوى المنشأ:</h3>';
    echo '<ul>';
    echo '<li>✅ <strong>11</strong> علم شرعي</li>';
    echo '<li>✅ <strong>2</strong> مستوى دراسي (الأول والثاني)</li>';
    echo '<li>✅ <strong>3</strong> مدراء</li>';
    echo '<li>✅ <strong>12</strong> طالبة في المستوى الثاني</li>';
    echo '<li>✅ <strong>15</strong> طالب/ة في المستوى الأول</li>';
    echo '<li>✅ <strong>' . count($courses_data) . '</strong> مقررات دراسية</li>';
    echo '</ul>';

    echo '<h3>معلومات تسجيل الدخول:</h3>';
    echo '<p><strong>ملاحظة:</strong> تم إنشاء الحسابات بكلمات المرور المحددة من قبل الإدارة.</p>';
    echo '<p><strong>الإدارة:</strong> mohamed, admin, عبد الحميد كرومي</p>';

    echo '<p style="margin-top: 20px;"><a href="' . home_url() . '" class="button button-primary button-large">زيارة الموقع</a> ';
    echo '<a href="' . admin_url('edit.php?post_type=fiqh_course&page=fiqh-levels') . '" class="button button-large">إدارة المستويات</a></p>';
}
