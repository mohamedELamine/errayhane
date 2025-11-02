<?php
/**
 * محتوى تجريبي Demo Content
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
    ?>
    <div class="wrap">
        <h1><?php _e('إنشاء محتوى تجريبي كامل', 'fiqh-lms'); ?></h1>
        <div class="notice notice-warning">
            <p><strong><?php _e('تحذير:', 'fiqh-lms'); ?></strong> <?php _e('سيتم إنشاء محتوى تجريبي كامل للمنصة. هذه العملية قد تستغرق بعض الوقت.', 'fiqh-lms'); ?></p>
        </div>

        <?php
        if (isset($_GET['run']) && $_GET['run'] === 'yes' && check_admin_referer('fiqh_demo_content')) {
            fiqh_generate_demo_content();
        } else {
            ?>
            <form method="get" action="">
                <input type="hidden" name="page" value="fiqh-demo-content">
                <input type="hidden" name="run" value="yes">
                <?php wp_nonce_field('fiqh_demo_content'); ?>
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
 * إنشاء المحتوى التجريبي الكامل
 */
function fiqh_generate_demo_content() {
    global $wpdb;

    set_time_limit(300); // 5 دقائق

    echo '<div class="updated"><p><strong>' . __('بدأ إنشاء المحتوى التجريبي...', 'fiqh-lms') . '</strong></p></div>';

    // 1. إنشاء التصنيفات
    echo '<h2>1️⃣ إنشاء التصنيفات والعلوم الشرعية</h2>';
    $sciences_data = array(
        array('name' => 'الفقه', 'desc' => 'علم الفقه الإسلامي والأحكام الشرعية'),
        array('name' => 'الحديث', 'desc' => 'علم الحديث النبوي الشريف'),
        array('name' => 'التفسير', 'desc' => 'علم تفسير القرآن الكريم'),
        array('name' => 'العقيدة', 'desc' => 'علم العقيدة والتوحيد'),
        array('name' => 'اللغة العربية', 'desc' => 'علوم اللغة العربية والنحو والصرف'),
        array('name' => 'الأصول', 'desc' => 'علم أصول الفقه'),
    );

    foreach ($sciences_data as $science) {
        $term = term_exists($science['name'], 'fiqh_course_science');
        if (!$term) {
            wp_insert_term($science['name'], 'fiqh_course_science', array(
                'description' => $science['desc']
            ));
            echo '<p style="color: green;">✅ تم إنشاء علم: <strong>' . $science['name'] . '</strong></p>';
        } else {
            echo '<p style="color: orange;">⚠️ العلم موجود مسبقاً: ' . $science['name'] . '</p>';
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
            'name' => 'المستوى الأول - خريف 2024',
            'description' => 'المستوى الدراسي الأول للعام الدراسي 2024-2025',
            'start_date' => '2024-09-01',
            'end_date' => '2024-12-31',
            'status' => 'active'
        ),
        array(
            'name' => 'المستوى الثاني - ربيع 2025',
            'description' => 'المستوى الدراسي الثاني للعام الدراسي 2024-2025',
            'start_date' => '2025-01-01',
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
            $batch_ids[] = $wpdb->insert_id;
            echo '<p style="color: green;">✅ تم إنشاء مستوى: <strong>' . $batch['name'] . '</strong></p>';
        } else {
            $batch_ids[] = $exists;
            echo '<p style="color: orange;">⚠️ المستوى موجود مسبقاً: ' . $batch['name'] . '</p>';
        }
    }

    // 3. إنشاء طلاب تجريبيين
    echo '<h2>3️⃣ إنشاء طلاب تجريبيين</h2>';
    $students_data = array(
        array('username' => 'ahmad_student', 'email' => 'ahmad@example.com', 'display_name' => 'أحمد محمد'),
        array('username' => 'fatima_student', 'email' => 'fatima@example.com', 'display_name' => 'فاطمة علي'),
        array('username' => 'omar_student', 'email' => 'omar@example.com', 'display_name' => 'عمر خالد'),
        array('username' => 'sara_student', 'email' => 'sara@example.com', 'display_name' => 'سارة أحمد'),
    );

    $student_ids = array();
    foreach ($students_data as $student) {
        $user_id = username_exists($student['username']);
        if (!$user_id) {
            $user_id = wp_create_user($student['username'], 'password123', $student['email']);
            wp_update_user(array(
                'ID' => $user_id,
                'display_name' => $student['display_name'],
                'role' => 'student'
            ));
            $student_ids[] = $user_id;
            echo '<p style="color: green;">✅ تم إنشاء طالب: <strong>' . $student['display_name'] . '</strong> (كلمة المرور: password123)</p>';
        } else {
            $student_ids[] = $user_id;
            echo '<p style="color: orange;">⚠️ الطالب موجود مسبقاً: ' . $student['display_name'] . '</p>';
        }
    }

    // ربط الطلاب بالمستويات
    if (!empty($batch_ids) && !empty($student_ids)) {
        foreach ($student_ids as $student_id) {
            foreach ($batch_ids as $batch_id) {
                $exists = $wpdb->get_var($wpdb->prepare(
                    "SELECT id FROM {$wpdb->prefix}fiqh_batch_students WHERE user_id = %d AND batch_id = %d",
                    $student_id,
                    $batch_id
                ));

                if (!$exists) {
                    $wpdb->insert($wpdb->prefix . 'fiqh_batch_students', array(
                        'batch_id' => $batch_id,
                        'user_id' => $student_id,
                        'enrolled_at' => current_time('mysql'),
                        'status' => 'active'
                    ));
                }
            }
        }
        echo '<p style="color: green;">✅ تم ربط الطلاب بالمستويات الدراسية</p>';
    }

    // 4. إنشاء المقررات
    echo '<h2>4️⃣ إنشاء المقررات الدراسية</h2>';
    $courses_data = array(
        array(
            'title' => 'فقه العبادات - المذهب المالكي',
            'science' => 'الفقه',
            'content' => '<p>مقرر شامل في فقه العبادات على المذهب المالكي، يشمل دراسة الطهارة والصلاة والزكاة والصيام والحج.</p>
<h3>أهداف المقرر:</h3>
<ul>
<li>فهم أحكام الطهارة والصلاة بالتفصيل</li>
<li>معرفة أحكام الزكاة والصوم</li>
<li>دراسة مناسك الحج والعمرة</li>
<li>التطبيق العملي للأحكام الفقهية</li>
</ul>',
            'excerpt' => 'دراسة شاملة لفقه العبادات على المذهب المالكي من الطهارة إلى الحج',
            'duration' => '16 أسبوع',
        ),
        array(
            'title' => 'مصطلح الحديث',
            'science' => 'الحديث',
            'content' => '<p>دراسة علم مصطلح الحديث وأنواع الأحاديث والرواة وطرق التصحيح والتضعيف.</p>
<h3>محاور المقرر:</h3>
<ul>
<li>تعريف علم مصطلح الحديث وأهميته</li>
<li>أنواع الحديث: الصحيح، الحسن، الضعيف</li>
<li>علم الجرح والتعديل</li>
<li>دراسة أشهر كتب الحديث</li>
</ul>',
            'excerpt' => 'مقدمة في علم مصطلح الحديث ومعرفة أنواع الأحاديث وطرق التصحيح',
            'duration' => '12 أسبوع',
        ),
        array(
            'title' => 'تفسير جزء عم',
            'science' => 'التفسير',
            'content' => '<p>دراسة تفسير سور جزء عم مع بيان أسباب النزول والإعراب والفوائد.</p>
<h3>منهج التفسير:</h3>
<ul>
<li>تفسير كل سورة مع بيان سبب النزول</li>
<li>شرح المفردات اللغوية</li>
<li>استنباط الفوائد والأحكام</li>
<li>الربط مع الواقع المعاصر</li>
</ul>',
            'excerpt' => 'تفسير مفصل لسور جزء عم مع بيان المعاني والأحكام والفوائد',
            'duration' => '10 أسابيع',
        ),
        array(
            'title' => 'العقيدة الطحاوية',
            'science' => 'العقيدة',
            'content' => '<p>شرح متن العقيدة الطحاوية للإمام الطحاوي رحمه الله.</p>
<h3>محتوى المقرر:</h3>
<ul>
<li>مقدمة في علم العقيدة</li>
<li>شرح أصول العقيدة الإسلامية</li>
<li>التوحيد وأنواعه</li>
<li>الإيمان بالله وأسمائه وصفاته</li>
</ul>',
            'excerpt' => 'شرح العقيدة الطحاوية وبيان أصول العقيدة الإسلامية الصحيحة',
            'duration' => '14 أسبوع',
        ),
        array(
            'title' => 'النحو الواضح - المستوى الأول',
            'science' => 'اللغة العربية',
            'content' => '<p>دراسة قواعد النحو العربي بأسلوب واضح ومبسط للمبتدئين.</p>
<h3>المحاور الرئيسية:</h3>
<ul>
<li>الجملة الاسمية والجملة الفعلية</li>
<li>المرفوعات والمنصوبات والمجرورات</li>
<li>الأفعال وتصريفاتها</li>
<li>التطبيقات العملية</li>
</ul>',
            'excerpt' => 'تعلم قواعد النحو العربي بطريقة سهلة ومبسطة مع تطبيقات عملية',
            'duration' => '12 أسبوع',
        ),
        array(
            'title' => 'أصول الفقه - مقدمة',
            'science' => 'الأصول',
            'content' => '<p>مقدمة في علم أصول الفقه وقواعده الأساسية.</p>
<h3>موضوعات المقرر:</h3>
<ul>
<li>تعريف علم الأصول وأهميته</li>
<li>مصادر التشريع الإسلامي</li>
<li>القواعد الأصولية</li>
<li>الاجتهاد والتقليد</li>
</ul>',
            'excerpt' => 'مدخل إلى علم أصول الفقه ومعرفة قواعد الاستنباط الفقهي',
            'duration' => '14 أسبوع',
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

            // ربط المقرر بالمستويات
            if (!empty($batch_ids)) {
                update_post_meta($course_id, '_fiqh_course_levels', $batch_ids);
            }

            $course_ids[] = $course_id;
            echo '<p style="color: green;">✅ تم إنشاء مقرر: <strong>' . $course_data['title'] . '</strong></p>';
        }
    }

    // 5. إنشاء الدروس لكل مقرر
    echo '<h2>5️⃣ إنشاء الدروس</h2>';
    $lessons_per_course = 5;
    $lesson_titles = array(
        'مقدمة ونظرة عامة',
        'الجزء الأول',
        'الجزء الثاني',
        'التطبيقات العملية',
        'المراجعة والاختبار'
    );

    foreach ($course_ids as $course_id) {
        $course_title = get_the_title($course_id);

        for ($i = 0; $i < $lessons_per_course; $i++) {
            $lesson_id = wp_insert_post(array(
                'post_title' => $lesson_titles[$i] . ' - ' . $course_title,
                'post_content' => '<p>محتوى الدرس: ' . $lesson_titles[$i] . '</p><p>هذا درس تجريبي يحتوي على شرح مفصل للموضوع مع أمثلة وتطبيقات عملية.</p>',
                'post_status' => 'publish',
                'post_type' => 'fiqh_lesson',
                'post_author' => 1,
                'menu_order' => $i + 1,
            ));

            if ($lesson_id && !is_wp_error($lesson_id)) {
                update_post_meta($lesson_id, '_fiqh_lesson_course_id', $course_id);
                update_post_meta($lesson_id, '_fiqh_lesson_video_url', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ');
                update_post_meta($lesson_id, '_fiqh_lesson_duration', ($i + 1) * 10 . ' دقيقة');
            }
        }

        echo '<p style="color: green;">✅ تم إنشاء ' . $lessons_per_course . ' دروس للمقرر: <strong>' . $course_title . '</strong></p>';
    }

    // 6. تسجيل الطلاب في المقررات
    echo '<h2>6️⃣ تسجيل الطلاب في المقررات</h2>';
    if (!empty($student_ids) && !empty($course_ids)) {
        foreach ($student_ids as $student_id) {
            // تسجيل كل طالب في 2-3 مقررات عشوائية
            $random_courses = array_rand(array_flip($course_ids), min(3, count($course_ids)));
            if (!is_array($random_courses)) {
                $random_courses = array($random_courses);
            }

            foreach ($random_courses as $course_id) {
                $exists = $wpdb->get_var($wpdb->prepare(
                    "SELECT id FROM {$wpdb->prefix}fiqh_enrollments WHERE user_id = %d AND course_id = %d",
                    $student_id,
                    $course_id
                ));

                if (!$exists) {
                    $wpdb->insert($wpdb->prefix . 'fiqh_enrollments', array(
                        'user_id' => $student_id,
                        'course_id' => $course_id,
                        'enrolled_at' => current_time('mysql'),
                        'status' => 'active'
                    ));
                }
            }
        }
        echo '<p style="color: green;">✅ تم تسجيل الطلاب في المقررات</p>';
    }

    // 7. إنشاء أسئلة تجريبية
    echo '<h2>7️⃣ إنشاء أسئلة تجريبية</h2>';
    $questions_data = array(
        'ما هو حكم الوضوء قبل الصلاة؟',
        'كيف أحسب زكاة المال؟',
        'ما هي شروط الصيام؟',
        'ما الفرق بين الحج والعمرة؟',
        'كيف أعرب هذه الجملة؟',
        'ما هو تفسير قوله تعالى: "إنا أعطيناك الكوثر"؟',
        'ما هي أركان الإيمان؟',
        'ما الفرق بين الحديث الصحيح والحسن؟',
    );

    if (!empty($student_ids) && !empty($course_ids)) {
        foreach ($questions_data as $index => $question_text) {
            $student_id = $student_ids[array_rand($student_ids)];
            $course_id = $course_ids[array_rand($course_ids)];

            // الحصول على درس من المقرر
            $lessons = get_posts(array(
                'post_type' => 'fiqh_lesson',
                'posts_per_page' => 1,
                'meta_query' => array(
                    array(
                        'key' => '_fiqh_lesson_course_id',
                        'value' => $course_id
                    )
                )
            ));
            $lesson_id = !empty($lessons) ? $lessons[0]->ID : null;

            $is_answered = $index % 3 === 0; // كل سؤال ثالث يكون مجاب

            $wpdb->insert($wpdb->prefix . 'fiqh_questions', array(
                'user_id' => $student_id,
                'course_id' => $course_id,
                'lesson_id' => $lesson_id,
                'question_text' => $question_text,
                'is_anonymous' => $index % 4 === 0,
                'status' => $is_answered ? 'answered' : 'pending',
                'answer_text' => $is_answered ? 'إجابة تجريبية على السؤال. هذا نص توضيحي للإجابة.' : null,
                'answered_by' => $is_answered ? 1 : null,
                'answered_at' => $is_answered ? current_time('mysql') : null,
                'created_at' => current_time('mysql'),
            ));
        }
        echo '<p style="color: green;">✅ تم إنشاء ' . count($questions_data) . ' سؤال تجريبي</p>';
    }

    // 8. إنشاء ملاحظات تجريبية
    echo '<h2>8️⃣ إنشاء ملاحظات تجريبية</h2>';
    if (!empty($student_ids) && !empty($course_ids)) {
        $notes_data = array(
            'ملاحظة مهمة عن الدرس',
            'نقطة يجب مراجعتها',
            'سؤال للمناقشة',
            'فائدة من الدرس',
        );

        $notes_count = 0;
        foreach ($student_ids as $student_id) {
            foreach ($notes_data as $note_text) {
                $course_id = $course_ids[array_rand($course_ids)];
                $wpdb->insert($wpdb->prefix . 'fiqh_notes', array(
                    'user_id' => $student_id,
                    'course_id' => $course_id,
                    'note_text' => $note_text,
                    'created_at' => current_time('mysql'),
                ));
                $notes_count++;
            }
        }
        echo '<p style="color: green;">✅ تم إنشاء ' . $notes_count . ' ملاحظة تجريبية</p>';
    }

    // 9. إنشاء مقالات (Posts) تجريبية
    echo '<h2>9️⃣ إنشاء مقالات وأحداث</h2>';
    $posts_data = array(
        array(
            'title' => 'بدء التسجيل للفصل الدراسي الجديد',
            'category' => 'أحداث',
            'content' => '<p>يسر إدارة المنصة أن تعلن عن بدء التسجيل للفصل الدراسي الجديد. يمكن للطلاب التسجيل في المقررات المتاحة ابتداءً من اليوم.</p><p>للتسجيل، يرجى زيارة قسم المقررات واختيار المقررات المناسبة لمستواكم الدراسي.</p>',
        ),
        array(
            'title' => 'محاضرة خاصة: الفقه المقارن',
            'category' => 'أحداث',
            'content' => '<p>تنظم المنصة محاضرة خاصة بعنوان "الفقه المقارن" يوم الجمعة القادم في تمام الساعة 8 مساءً.</p><p>سيتم بث المحاضرة مباشرة عبر المنصة، ندعو جميع الطلاب للحضور والمشاركة.</p>',
        ),
        array(
            'title' => 'نصائح للمذاكرة الفعالة',
            'category' => 'مقالات علمية',
            'content' => '<p>المذاكرة الفعالة ليست فقط عن عدد الساعات، بل عن جودة الوقت المستثمر. إليك بعض النصائح:</p><ul><li>حدد أوقاتاً منتظمة للمذاكرة</li><li>راجع ملاحظاتك بانتظام</li><li>استخدم طرقاً متنوعة للمراجعة</li></ul>',
        ),
        array(
            'title' => 'إعلان: تحديثات جديدة على المنصة',
            'category' => 'إعلانات',
            'content' => '<p>تم إضافة مميزات جديدة على المنصة تشمل:</p><ul><li>نظام الأسئلة والأجوبة المحسّن</li><li>إمكانية إضافة الملاحظات الشخصية</li><li>تتبع التقدم الدراسي</li></ul>',
        ),
    );

    foreach ($posts_data as $post_data) {
        $category_id = get_cat_ID($post_data['category']);

        $post_id = wp_insert_post(array(
            'post_title' => $post_data['title'],
            'post_content' => $post_data['content'],
            'post_status' => 'publish',
            'post_type' => 'post',
            'post_author' => 1,
            'post_category' => array($category_id)
        ));

        if ($post_id && !is_wp_error($post_id)) {
            echo '<p style="color: green;">✅ تم إنشاء مقال: <strong>' . $post_data['title'] . '</strong></p>';
        }
    }

    // 10. ملخص نهائي
    echo '<h2>✅ اكتمل إنشاء المحتوى التجريبي!</h2>';
    echo '<div class="updated"><p><strong>تم بنجاح!</strong> تم إنشاء محتوى تجريبي كامل للمنصة.</p></div>';

    echo '<h3>ملخص المحتوى المنشأ:</h3>';
    echo '<ul>';
    echo '<li>✅ <strong>6</strong> علوم شرعية</li>';
    echo '<li>✅ <strong>2</strong> مستوى دراسي</li>';
    echo '<li>✅ <strong>4</strong> طلاب تجريبيين</li>';
    echo '<li>✅ <strong>' . count($courses_data) . '</strong> مقررات دراسية</li>';
    echo '<li>✅ <strong>' . (count($courses_data) * $lessons_per_course) . '</strong> درس</li>';
    echo '<li>✅ <strong>' . count($questions_data) . '</strong> سؤال</li>';
    echo '<li>✅ <strong>16</strong> ملاحظة</li>';
    echo '<li>✅ <strong>4</strong> مقالات وأحداث</li>';
    echo '</ul>';

    echo '<h3>معلومات تسجيل الدخول للطلاب التجريبيين:</h3>';
    echo '<table class="widefat"><thead><tr><th>اسم المستخدم</th><th>كلمة المرور</th></tr></thead><tbody>';
    foreach ($students_data as $student) {
        echo '<tr><td>' . $student['username'] . '</td><td>password123</td></tr>';
    }
    echo '</tbody></table>';

    echo '<p style="margin-top: 20px;"><a href="' . home_url() . '" class="button button-primary button-large">زيارة الموقع</a></p>';
}
