<?php
/**
 * محتوى تجريبي Demo Content
 *
 * ملاحظة: هذا الملف يُشغّل مرة واحدة فقط لإنشاء محتوى تجريبي
 * للتشغيل: قم بزيارة: wp-admin/admin.php?page=fiqh-demo-content
 *
 * @package FiqhLearning_LMS
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * إنشاء محتوى تجريبي
 */
function fiqh_create_demo_content() {
    global $wpdb;

    // التحقق من الصلاحيات
    if (!current_user_can('manage_options')) {
        wp_die(__('ليس لديك صلاحية', 'fiqh-lms'));
    }

    echo '<div class="wrap">';
    echo '<h1>' . __('إنشاء محتوى تجريبي', 'fiqh-lms') . '</h1>';
    echo '<div class="notice notice-info"><p>' . __('جاري إنشاء المحتوى التجريبي...', 'fiqh-lms') . '</p></div>';

    // 1. إنشاء Taxonomies
    echo '<h2>1. إنشاء التصنيفات</h2>';

    // العلوم
    $sciences = array('الفقه', 'الحديث', 'التفسير', 'العقيدة', 'اللغة العربية');
    foreach ($sciences as $science) {
        $term = term_exists($science, 'fiqh_course_science');
        if (!$term) {
            wp_insert_term($science, 'fiqh_course_science');
            echo '<p>✓ تم إنشاء علم: ' . $science . '</p>';
        }
    }

    // الفصول
    $semesters = array('الفصل الأول', 'الفصل الثاني');
    foreach ($semesters as $semester) {
        $term = term_exists($semester, 'fiqh_course_semester');
        if (!$term) {
            wp_insert_term($semester, 'fiqh_course_semester');
            echo '<p>✓ تم إنشاء فصل: ' . $semester . '</p>';
        }
    }

    // أنواع المقررات
    $types = array('أساسي', 'تكميلي', 'إثرائي');
    foreach ($types as $type) {
        $term = term_exists($type, 'fiqh_course_type');
        if (!$term) {
            wp_insert_term($type, 'fiqh_course_type');
            echo '<p>✓ تم إنشاء نوع: ' . $type . '</p>';
        }
    }

    // 2. إنشاء مستوى تجريبي
    echo '<h2>2. إنشاء مستوى تجريبي</h2>';
    $batch_data = array(
        'name' => 'مستوى 2024-2025',
        'description' => 'مستوى تجريبي للعام الدراسي 2024-2025',
        'start_date' => '2024-09-01',
        'end_date' => '2025-06-30',
        'status' => 'active',
    );
    $wpdb->insert($wpdb->prefix . 'fiqh_batches', $batch_data);
    $batch_id = $wpdb->insert_id;
    echo '<p>✓ تم إنشاء مستوى: ' . $batch_data['name'] . ' (ID: ' . $batch_id . ')</p>';

    // 3. إنشاء مقرر تجريبي
    echo '<h2>3. إنشاء مقرر تجريبي</h2>';
    $course_data = array(
        'post_title' => 'فقه العبادات - المذهب المالكي',
        'post_content' => '<p>مقرر شامل في فقه العبادات على المذهب المالكي، يشمل دراسة الطهارة والصلاة والزكاة والصيام والحج.</p>
<h3>أهداف المقرر:</h3>
<ul>
<li>فهم أحكام الطهارة والصلاة بالتفصيل</li>
<li>معرفة أحكام الزكاة والصوم</li>
<li>دراسة مناسك الحج والعمرة</li>
<li>التطبيق العملي للأحكام الفقهية</li>
</ul>',
        'post_excerpt' => 'دراسة شاملة لفقه العبادات على المذهب المالكي من الطهارة إلى الحج',
        'post_status' => 'publish',
        'post_type' => 'fiqh_course',
        'post_author' => 1,
    );
    $course_id = wp_insert_post($course_data);

    // إضافة Meta Data
    update_post_meta($course_id, '_fiqh_course_book_url', 'https://example.com/books/fiqh-ibadat.pdf');
    update_post_meta($course_id, '_fiqh_course_duration', '12 أسبوع');
    update_post_meta($course_id, '_fiqh_course_teacher_id', 1);

    // إضافة التصنيفات
    wp_set_object_terms($course_id, 'الفقه', 'fiqh_course_science');
    wp_set_object_terms($course_id, 'الفصل الأول', 'fiqh_course_semester');
    wp_set_object_terms($course_id, 'أساسي', 'fiqh_course_type');

    echo '<p>✓ تم إنشاء مقرر: فقه العبادات (ID: ' . $course_id . ')</p>';

    // 4. إنشاء 5 دروس
    echo '<h2>4. إنشاء دروس تجريبية</h2>';
    $lessons = array(
        array(
            'title' => 'الدرس 1: مقدمة في الطهارة',
            'content' => '<p>في هذا الدرس نتعرف على أحكام الطهارة في الفقه المالكي، وأقسام المياه، وأحكام النجاسات.</p>',
            'youtube_id' => 'dQw4w9WgXcQ',
            'audio_url' => 'https://example.com/audio/lesson-01.mp3',
            'pdf_url' => 'https://example.com/pdf/lesson-01.pdf',
            'duration' => '45 دقيقة',
            'order' => 1,
        ),
        array(
            'title' => 'الدرس 2: الوضوء وأحكامه',
            'content' => '<p>دراسة تفصيلية لأركان الوضوء وسننه ومبطلاته.</p>',
            'youtube_id' => 'dQw4w9WgXcQ',
            'audio_url' => 'https://example.com/audio/lesson-02.mp3',
            'pdf_url' => 'https://example.com/pdf/lesson-02.pdf',
            'duration' => '50 دقيقة',
            'order' => 2,
        ),
        array(
            'title' => 'الدرس 3: الغسل والتيمم',
            'content' => '<p>أحكام الغسل من الجنابة والحيض، وأحكام التيمم وكيفيته.</p>',
            'youtube_id' => 'dQw4w9WgXcQ',
            'audio_url' => 'https://example.com/audio/lesson-03.mp3',
            'pdf_url' => 'https://example.com/pdf/lesson-03.pdf',
            'duration' => '55 دقيقة',
            'order' => 3,
        ),
        array(
            'title' => 'الدرس 4: شروط الصلاة',
            'content' => '<p>دراسة شروط صحة الصلاة: الطهارة، ستر العورة، استقبال القبلة، الوقت.</p>',
            'youtube_id' => 'dQw4w9WgXcQ',
            'audio_url' => 'https://example.com/audio/lesson-04.mp3',
            'pdf_url' => 'https://example.com/pdf/lesson-04.pdf',
            'duration' => '60 دقيقة',
            'order' => 4,
        ),
        array(
            'title' => 'الدرس 5: أركان الصلاة وواجباتها',
            'content' => '<p>تفصيل أركان الصلاة الأربعة عشر، وواجباتها وسننها.</p>',
            'youtube_id' => 'dQw4w9WgXcQ',
            'audio_url' => 'https://example.com/audio/lesson-05.mp3',
            'pdf_url' => 'https://example.com/pdf/lesson-05.pdf',
            'duration' => '65 دقيقة',
            'order' => 5,
        ),
    );

    foreach ($lessons as $lesson_data) {
        $lesson_post = array(
            'post_title' => $lesson_data['title'],
            'post_content' => $lesson_data['content'],
            'post_status' => 'publish',
            'post_type' => 'fiqh_lesson',
            'post_author' => 1,
        );
        $lesson_id = wp_insert_post($lesson_post);

        // Meta Data
        update_post_meta($lesson_id, '_fiqh_lesson_course_id', $course_id);
        update_post_meta($lesson_id, '_fiqh_lesson_video_url', 'https://www.youtube.com/watch?v=' . $lesson_data['youtube_id']);
        update_post_meta($lesson_id, '_fiqh_lesson_audio_url', $lesson_data['audio_url']);
        update_post_meta($lesson_id, '_fiqh_lesson_pdf_url', $lesson_data['pdf_url']);
        update_post_meta($lesson_id, '_fiqh_lesson_duration', $lesson_data['duration']);
        update_post_meta($lesson_id, '_fiqh_lesson_order', $lesson_data['order']);
        update_post_meta($lesson_id, '_fiqh_lesson_exercises', '<p><strong>تمرين:</strong> اذكر أركان الوضوء مع الدليل من السنة.</p>');

        echo '<p>✓ تم إنشاء: ' . $lesson_data['title'] . ' (ID: ' . $lesson_id . ')</p>';
    }

    // 5. إنشاء طالب تجريبي
    echo '<h2>5. إنشاء طالب تجريبي</h2>';
    $student_username = 'student_demo';
    $student_email = 'student@demo.local';

    if (!username_exists($student_username) && !email_exists($student_email)) {
        $student_id = wp_create_user($student_username, 'demo123', $student_email);
        $student = get_user_by('id', $student_id);
        $student->set_role('student');
        update_user_meta($student_id, 'first_name', 'طالب');
        update_user_meta($student_id, 'last_name', 'تجريبي');

        echo '<p>✓ تم إنشاء طالب: ' . $student_username . ' / كلمة المرور: demo123 (ID: ' . $student_id . ')</p>';

        // تسجيل الطالب في المقرر
        $wpdb->insert($wpdb->prefix . 'fiqh_enrollments', array(
            'user_id' => $student_id,
            'course_id' => $course_id,
            'batch_id' => $batch_id,
            'status' => 'active',
        ));
        echo '<p>✓ تم تسجيل الطالب في المقرر</p>';
    } else {
        echo '<p>⚠ الطالب موجود مسبقاً</p>';
    }

    // 6. إنشاء سؤال وإجابة تجريبية
    echo '<h2>6. إنشاء سؤال وإجابة تجريبية</h2>';
    $question_data = array(
        'user_id' => 1,
        'course_id' => $course_id,
        'lesson_id' => null,
        'question' => 'ما هي شروط صحة الوضوء في المذهب المالكي؟',
        'answer' => 'شروط صحة الوضوء في المذهب المالكي هي: النية، والإسلام، والتمييز، وطهورية الماء، وإباحته، وانقطاع ما يوجب الوضوء، وعدم الحائل، واستيعاب المحل، والموالاة، والدلك، وعدم المنافي.',
        'answered_by' => 1,
        'answered_at' => current_time('mysql'),
        'status' => 'answered',
        'is_anonymous' => 0,
    );
    $wpdb->insert($wpdb->prefix . 'fiqh_questions', $question_data);
    echo '<p>✓ تم إنشاء سؤال وإجابة تجريبية</p>';

    // 7. إنشاء مقالة تجريبية
    echo '<h2>7. إنشاء مقالة تجريبية</h2>';
    $post_data = array(
        'post_title' => 'أهمية دراسة الفقه المالكي',
        'post_content' => '<p>الفقه المالكي هو أحد المذاهب الفقهية الأربعة المعتمدة في العالم الإسلامي، ويتميز بخصائص عديدة منها:</p>
<ul>
<li>الاعتماد على عمل أهل المدينة كأصل من أصول الفقه</li>
<li>التوسط والاعتدال في الأحكام</li>
<li>الاهتمام بالمقاصد الشرعية</li>
<li>الانتشار الواسع في شمال أفريقيا والأندلس</li>
</ul>
<p>لذا فإن دراسة هذا المذهب ضرورية لكل طالب علم في هذه المناطق.</p>',
        'post_excerpt' => 'نظرة على أهمية دراسة المذهب المالكي وخصائصه المميزة',
        'post_status' => 'publish',
        'post_type' => 'post',
        'post_author' => 1,
        'post_category' => array(1),
    );
    $post_id = wp_insert_post($post_data);
    echo '<p>✓ تم إنشاء مقالة: أهمية دراسة الفقه المالكي (ID: ' . $post_id . ')</p>';

    echo '<div class="notice notice-success" style="margin-top: 30px;"><p><strong>✅ تم إنشاء المحتوى التجريبي بنجاح!</strong></p></div>';

    echo '<h3>ملخص المحتوى المُنشأ:</h3>';
    echo '<ul>';
    echo '<li>✓ 5 علوم (الفقه، الحديث، التفسير، العقيدة، اللغة العربية)</li>';
    echo '<li>✓ 2 فصول دراسية (الأول والثاني)</li>';
    echo '<li>✓ 3 أنواع مقررات (أساسي، تكميلي، إثرائي)</li>';
    echo '<li>✓ 1 مستوى (2024-2025)</li>';
    echo '<li>✓ 1 مقرر (فقه العبادات)</li>';
    echo '<li>✓ 5 دروس</li>';
    echo '<li>✓ 1 طالب (student_demo / demo123)</li>';
    echo '<li>✓ 1 سؤال وإجابة</li>';
    echo '<li>✓ 1 مقالة</li>';
    echo '</ul>';

    echo '<p><a href="' . admin_url() . '" class="button button-primary">العودة إلى لوحة التحكم</a></p>';

    echo '</div>';
}

/**
 * إضافة قائمة في Admin
 */
add_action('admin_menu', function() {
    add_submenu_page(
        'tools.php',
        __('محتوى تجريبي', 'fiqh-lms'),
        __('محتوى تجريبي', 'fiqh-lms'),
        'manage_options',
        'fiqh-demo-content',
        'fiqh_create_demo_content'
    );
});
