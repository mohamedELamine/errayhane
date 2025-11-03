<?php
/**
 * معالجات AJAX للقالب
 *
 * @package FiqhLearning
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * حفظ ملاحظات الدرس
 */
function fiqh_save_lesson_notes() {
    check_ajax_referer('fiqh-ajax-nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => __('يجب تسجيل الدخول', 'fiqhlearning')));
    }

    $user_id = get_current_user_id();
    $lesson_id = isset($_POST['lesson_id']) ? intval($_POST['lesson_id']) : 0;
    $notes = isset($_POST['notes']) ? wp_kses_post($_POST['notes']) : '';

    if (!$lesson_id) {
        wp_send_json_error(array('message' => __('معرف الدرس مطلوب', 'fiqhlearning')));
    }

    update_user_meta($user_id, '_fiqh_lesson_notes_' . $lesson_id, $notes);

    wp_send_json_success(array('message' => __('تم حفظ الملاحظات بنجاح', 'fiqhlearning')));
}
add_action('wp_ajax_fiqh_save_lesson_notes', 'fiqh_save_lesson_notes');

/**
 * جلب دروس مقرر معين
 */
function fiqh_get_course_lessons_ajax() {
    $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;

    if (!$course_id) {
        wp_send_json_error(array('message' => __('معرف المقرر مطلوب', 'fiqhlearning')));
    }

    $lessons = get_posts(array(
        'post_type' => 'fiqh_lesson',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_fiqh_lesson_course_id',
                'value' => $course_id,
            )
        ),
        'meta_key' => '_fiqh_lesson_order',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
    ));

    if ($lessons) {
        wp_send_json_success(array('lessons' => $lessons));
    } else {
        wp_send_json_error(array('message' => __('لا توجد دروس', 'fiqhlearning')));
    }
}
add_action('wp_ajax_fiqh_get_course_lessons', 'fiqh_get_course_lessons_ajax');
add_action('wp_ajax_nopriv_fiqh_get_course_lessons', 'fiqh_get_course_lessons_ajax');

/**
 * إضافة سؤال جديد في الدرس
 */
function fiqh_add_lesson_question() {
    check_ajax_referer('fiqh-ajax-nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(__('يجب تسجيل الدخول', 'fiqhlearning'));
    }

    $user_id = get_current_user_id();
    $lesson_id = isset($_POST['lesson_id']) ? intval($_POST['lesson_id']) : 0;
    $question = isset($_POST['question']) ? sanitize_textarea_field($_POST['question']) : '';

    if (!$lesson_id || empty($question)) {
        wp_send_json_error(__('جميع الحقول مطلوبة', 'fiqhlearning'));
    }

    // التحقق من إمكانية الوصول للدرس
    if (!fiqh_can_access_lesson($lesson_id, $user_id)) {
        wp_send_json_error(__('ليس لديك صلاحية للوصول', 'fiqhlearning'));
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'fiqh_questions';

    // الحصول على course_id من الدرس
    $course_id = get_post_meta($lesson_id, '_fiqh_lesson_course_id', true);

    $result = $wpdb->insert(
        $table_name,
        array(
            'user_id' => $user_id,
            'lesson_id' => $lesson_id,
            'course_id' => $course_id,
            'question' => $question,
            'status' => 'pending',
            'created_at' => current_time('mysql'),
        ),
        array('%d', '%d', '%d', '%s', '%s', '%s')
    );

    if ($result) {
        wp_send_json_success(array(
            'message' => __('تم إرسال السؤال بنجاح', 'fiqhlearning'),
            'question_id' => $wpdb->insert_id
        ));
    } else {
        wp_send_json_error(__('حدث خطأ أثناء إرسال السؤال', 'fiqhlearning'));
    }
}
add_action('wp_ajax_fiqh_add_lesson_question', 'fiqh_add_lesson_question');

/**
 * إضافة إجابة على سؤال
 */
function fiqh_add_lesson_answer() {
    check_ajax_referer('fiqh-ajax-nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(__('يجب تسجيل الدخول', 'fiqhlearning'));
    }

    // التحقق من الصلاحيات - فقط المشرفين والمعلمين
    if (!current_user_can('administrator') && !current_user_can('teacher')) {
        wp_send_json_error(__('ليس لديك صلاحية لإضافة إجابة', 'fiqhlearning'));
    }

    $question_id = isset($_POST['question_id']) ? intval($_POST['question_id']) : 0;
    $answer = isset($_POST['answer']) ? sanitize_textarea_field($_POST['answer']) : '';

    if (!$question_id || empty($answer)) {
        wp_send_json_error(__('جميع الحقول مطلوبة', 'fiqhlearning'));
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'fiqh_questions';

    $result = $wpdb->update(
        $table_name,
        array(
            'answer' => $answer,
            'answered_by' => get_current_user_id(),
            'answered_at' => current_time('mysql'),
            'status' => 'answered',
        ),
        array('id' => $question_id),
        array('%s', '%d', '%s', '%s'),
        array('%d')
    );

    if ($result !== false) {
        wp_send_json_success(array(
            'message' => __('تم إرسال الإجابة بنجاح', 'fiqhlearning')
        ));
    } else {
        wp_send_json_error(__('حدث خطأ أثناء إرسال الإجابة', 'fiqhlearning'));
    }
}
add_action('wp_ajax_fiqh_add_lesson_answer', 'fiqh_add_lesson_answer');

/**
 * تمييز الدرس كمكتمل
 */
function fiqh_complete_lesson() {
    check_ajax_referer('fiqh-ajax-nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(__('يجب تسجيل الدخول', 'fiqhlearning'));
    }

    $user_id = get_current_user_id();
    $lesson_id = isset($_POST['lesson_id']) ? intval($_POST['lesson_id']) : 0;
    $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;

    if (!$lesson_id || !$course_id) {
        wp_send_json_error(__('البيانات غير مكتملة', 'fiqhlearning'));
    }

    // التحقق من إمكانية الوصول للدرس
    if (!fiqh_can_access_lesson($lesson_id, $user_id)) {
        wp_send_json_error(__('ليس لديك صلاحية للوصول', 'fiqhlearning'));
    }

    // إكمال الدرس
    $result = FiqhLearning_Progress::complete_lesson($user_id, $lesson_id, $course_id);

    if ($result) {
        wp_send_json_success(__('تم تمييز الدرس كمكتمل', 'fiqhlearning'));
    } else {
        wp_send_json_error(__('حدث خطأ أثناء حفظ التقدم', 'fiqhlearning'));
    }
}
add_action('wp_ajax_fiqh_complete_lesson', 'fiqh_complete_lesson');

/**
 * إضافة ملاحظة جديدة
 */
function fiqh_add_note() {
    check_ajax_referer('fiqh-ajax-nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(__('يجب تسجيل الدخول', 'fiqhlearning'));
    }

    $user_id = get_current_user_id();
    $lesson_id = isset($_POST['lesson_id']) ? intval($_POST['lesson_id']) : 0;
    $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
    $note_text = isset($_POST['note_text']) ? wp_kses_post($_POST['note_text']) : '';

    if (!$lesson_id || !$course_id || empty($note_text)) {
        wp_send_json_error(__('البيانات غير مكتملة', 'fiqhlearning'));
    }

    // التحقق من إمكانية الوصول للدرس
    if (!fiqh_can_access_lesson($lesson_id, $user_id)) {
        wp_send_json_error(__('ليس لديك صلاحية للوصول', 'fiqhlearning'));
    }

    $note_id = FiqhLearning_Notes::add_note($user_id, $lesson_id, $course_id, $note_text);

    if (is_wp_error($note_id)) {
        wp_send_json_error($note_id->get_error_message());
    }

    $note = FiqhLearning_Notes::get_note($note_id);

    wp_send_json_success(array(
        'message' => __('تم إضافة الملاحظة بنجاح', 'fiqhlearning'),
        'note' => $note
    ));
}
add_action('wp_ajax_fiqh_add_note', 'fiqh_add_note');

/**
 * تحديث ملاحظة
 */
function fiqh_update_note() {
    check_ajax_referer('fiqh-ajax-nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(__('يجب تسجيل الدخول', 'fiqhlearning'));
    }

    $user_id = get_current_user_id();
    $note_id = isset($_POST['note_id']) ? intval($_POST['note_id']) : 0;
    $note_text = isset($_POST['note_text']) ? wp_kses_post($_POST['note_text']) : '';

    if (!$note_id || empty($note_text)) {
        wp_send_json_error(__('البيانات غير مكتملة', 'fiqhlearning'));
    }

    $result = FiqhLearning_Notes::update_note($note_id, $user_id, $note_text);

    if (is_wp_error($result)) {
        wp_send_json_error($result->get_error_message());
    }

    $note = FiqhLearning_Notes::get_note($note_id);

    wp_send_json_success(array(
        'message' => __('تم تحديث الملاحظة بنجاح', 'fiqhlearning'),
        'note' => $note
    ));
}
add_action('wp_ajax_fiqh_update_note', 'fiqh_update_note');

/**
 * حذف ملاحظة
 */
function fiqh_delete_note() {
    check_ajax_referer('fiqh-ajax-nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(__('يجب تسجيل الدخول', 'fiqhlearning'));
    }

    $user_id = get_current_user_id();
    $note_id = isset($_POST['note_id']) ? intval($_POST['note_id']) : 0;

    if (!$note_id) {
        wp_send_json_error(__('البيانات غير مكتملة', 'fiqhlearning'));
    }

    $result = FiqhLearning_Notes::delete_note($note_id, $user_id);

    if (is_wp_error($result)) {
        wp_send_json_error($result->get_error_message());
    }

    wp_send_json_success(__('تم حذف الملاحظة بنجاح', 'fiqhlearning'));
}
add_action('wp_ajax_fiqh_delete_note', 'fiqh_delete_note');

/**
 * الحصول على ملاحظات الدرس
 */
function fiqh_get_lesson_notes() {
    check_ajax_referer('fiqh-ajax-nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(__('يجب تسجيل الدخول', 'fiqhlearning'));
    }

    $user_id = get_current_user_id();
    $lesson_id = isset($_POST['lesson_id']) ? intval($_POST['lesson_id']) : 0;

    if (!$lesson_id) {
        wp_send_json_error(__('البيانات غير مكتملة', 'fiqhlearning'));
    }

    $notes = FiqhLearning_Notes::get_lesson_notes($user_id, $lesson_id);

    wp_send_json_success(array('notes' => $notes));
}
add_action('wp_ajax_fiqh_get_lesson_notes', 'fiqh_get_lesson_notes');

/**
 * معالج نموذج الاتصال
 */
function fiqh_contact_form() {
    // التحقق من nonce
    check_ajax_referer('fiqh-ajax-nonce', 'nonce');

    // جلب البيانات
    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $subject = isset($_POST['subject']) ? sanitize_text_field($_POST['subject']) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';
    $privacy = isset($_POST['privacy']) ? sanitize_text_field($_POST['privacy']) : '';

    // التحقق من البيانات المطلوبة
    if (empty($name) || empty($email) || empty($subject) || empty($message) || empty($privacy)) {
        wp_send_json_error(__('جميع الحقول المطلوبة يجب أن تكون مملوءة', 'fiqhlearning'));
    }

    // التحقق من صحة البريد الإلكتروني
    if (!is_email($email)) {
        wp_send_json_error(__('البريد الإلكتروني غير صحيح', 'fiqhlearning'));
    }

    // ترجمة الموضوع
    $subject_translations = array(
        'enrollment' => __('استفسار عن التسجيل', 'fiqhlearning'),
        'courses' => __('استفسار عن المقررات', 'fiqhlearning'),
        'technical' => __('مشكلة تقنية', 'fiqhlearning'),
        'complaint' => __('شكوى', 'fiqhlearning'),
        'suggestion' => __('اقتراح', 'fiqhlearning'),
        'other' => __('أخرى', 'fiqhlearning'),
    );
    $subject_text = isset($subject_translations[$subject]) ? $subject_translations[$subject] : $subject;

    // إعداد البريد الإلكتروني
    $to = 'rayhaneschool@gmail.com'; // البريد المحدد من المستخدم
    $email_subject = sprintf('[%s] رسالة جديدة من %s - %s', get_bloginfo('name'), $name, $subject_text);

    $email_body = sprintf(
        "رسالة جديدة من نموذج الاتصال\n\n" .
        "الاسم: %s\n" .
        "البريد الإلكتروني: %s\n" .
        "رقم الهاتف: %s\n" .
        "الموضوع: %s\n\n" .
        "الرسالة:\n%s\n\n" .
        "---\n" .
        "تم إرسال هذه الرسالة من %s\n" .
        "التاريخ: %s",
        $name,
        $email,
        $phone ? $phone : __('غير محدد', 'fiqhlearning'),
        $subject_text,
        $message,
        get_bloginfo('url'),
        current_time('mysql')
    );

    // إعداد headers
    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . get_bloginfo('name') . ' <noreply@' . parse_url(get_bloginfo('url'), PHP_URL_HOST) . '>',
        'Reply-To: ' . $name . ' <' . $email . '>',
    );

    // إرسال البريد الإلكتروني
    $sent = wp_mail($to, $email_subject, $email_body, $headers);

    // إرسال نسخة للمرسل (اختياري)
    $copy_to_sender = get_theme_mod('contact_form_send_copy', false);
    if ($copy_to_sender && $sent) {
        $copy_subject = sprintf('[%s] نسخة من رسالتك - %s', get_bloginfo('name'), $subject_text);
        $copy_body = sprintf(
            "شكراً لتواصلك معنا!\n\n" .
            "هذه نسخة من رسالتك:\n\n%s\n\n" .
            "سنتواصل معك في أقرب وقت ممكن.\n\n" .
            "تحياتنا،\n" .
            "%s",
            $message,
            get_bloginfo('name')
        );
        $copy_headers = array(
            'Content-Type: text/plain; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <noreply@' . parse_url(get_bloginfo('url'), PHP_URL_HOST) . '>',
        );
        wp_mail($email, $copy_subject, $copy_body, $copy_headers);
    }

    if ($sent) {
        wp_send_json_success(__('تم إرسال رسالتك بنجاح! سنتواصل معك قريباً.', 'fiqhlearning'));
    } else {
        wp_send_json_error(__('حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة مرة أخرى أو التواصل معنا مباشرة.', 'fiqhlearning'));
    }
}
add_action('wp_ajax_fiqh_contact_form', 'fiqh_contact_form');
add_action('wp_ajax_nopriv_fiqh_contact_form', 'fiqh_contact_form');

/**
 * إضافة سؤال عام من صفحة الأسئلة
 */
function fiqh_add_question() {
    check_ajax_referer('fiqh-ajax-nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => __('يجب تسجيل الدخول', 'fiqhlearning')));
    }

    $user_id = get_current_user_id();
    $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
    $lesson_id = isset($_POST['lesson_id']) ? intval($_POST['lesson_id']) : 0;
    $question_text = isset($_POST['question_text']) ? sanitize_textarea_field($_POST['question_text']) : '';
    $is_anonymous = isset($_POST['is_anonymous']) ? intval($_POST['is_anonymous']) : 0;

    if (!$course_id || empty($question_text)) {
        wp_send_json_error(array('message' => __('المقرر ونص السؤال مطلوبان', 'fiqhlearning')));
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'fiqh_questions';

    $result = $wpdb->insert(
        $table_name,
        array(
            'user_id' => $user_id,
            'course_id' => $course_id,
            'lesson_id' => $lesson_id ? $lesson_id : null,
            'question' => $question_text,
            'is_anonymous' => $is_anonymous,
            'status' => 'pending',
            'created_at' => current_time('mysql'),
        ),
        array('%d', '%d', '%d', '%s', '%d', '%s', '%s')
    );

    if ($result) {
        wp_send_json_success(array(
            'message' => __('تم إرسال السؤال بنجاح', 'fiqhlearning'),
            'question_id' => $wpdb->insert_id
        ));
    } else {
        wp_send_json_error(array('message' => __('حدث خطأ أثناء إرسال السؤال', 'fiqhlearning')));
    }
}
add_action('wp_ajax_fiqh_add_question', 'fiqh_add_question');

/**
 * جلب قائمة الأسئلة
 */
function fiqh_get_questions() {
    check_ajax_referer('fiqh-ajax-nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => __('يجب تسجيل الدخول', 'fiqhlearning')));
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'fiqh_questions';

    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $per_page = 10;
    $offset = ($page - 1) * $per_page;

    $my_questions = isset($_POST['my_questions']) ? intval($_POST['my_questions']) : 0;
    $science_id = isset($_POST['science_id']) ? intval($_POST['science_id']) : 0;
    $course_id_filter = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
    $status_filter = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

    // بناء الاستعلام
    $where = array("q.status != 'deleted'");
    $joins = array();

    if ($my_questions) {
        $where[] = $wpdb->prepare("q.user_id = %d", get_current_user_id());
    }

    if ($science_id) {
        $joins[] = "INNER JOIN {$wpdb->prefix}term_relationships tr ON q.course_id = tr.object_id";
        $where[] = $wpdb->prepare("tr.term_taxonomy_id = %d", $science_id);
    }

    if ($course_id_filter) {
        $where[] = $wpdb->prepare("q.course_id = %d", $course_id_filter);
    }

    if ($status_filter) {
        $where[] = $wpdb->prepare("q.status = %s", $status_filter);
    }

    if ($search) {
        $where[] = $wpdb->prepare("q.question LIKE %s", '%' . $wpdb->esc_like($search) . '%');
    }

    $where_clause = implode(' AND ', $where);
    $join_clause = implode(' ', $joins);

    // جلب الأسئلة
    $query = "SELECT q.*,
              u.display_name as user_name,
              c.post_title as course_name,
              l.post_title as lesson_name,
              au.display_name as answerer_name
              FROM $table_name q
              LEFT JOIN {$wpdb->users} u ON q.user_id = u.ID
              LEFT JOIN {$wpdb->posts} c ON q.course_id = c.ID
              LEFT JOIN {$wpdb->posts} l ON q.lesson_id = l.ID
              LEFT JOIN {$wpdb->users} au ON q.answered_by = au.ID
              $join_clause
              WHERE $where_clause
              ORDER BY q.created_at DESC
              LIMIT %d OFFSET %d";

    $questions = $wpdb->get_results($wpdb->prepare($query, $per_page, $offset));

    // تنسيق البيانات
    foreach ($questions as $q) {
        $q->created_at = human_time_diff(strtotime($q->created_at), current_time('timestamp')) . ' ' . __('مضت', 'fiqhlearning');
        $q->question_text = wp_trim_words($q->question, 50);
        $q->answer_text = $q->answer ? wp_trim_words($q->answer, 50) : null;
    }

    // حساب الإحصائيات
    $stats_query = "SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'answered' THEN 1 ELSE 0 END) as answered,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending
                    FROM $table_name q
                    $join_clause
                    WHERE $where_clause";
    $stats = $wpdb->get_row($stats_query);

    // حساب عدد الصفحات
    $total_pages = ceil($stats->total / $per_page);

    wp_send_json_success(array(
        'questions' => $questions,
        'stats' => $stats,
        'total_pages' => $total_pages
    ));
}
add_action('wp_ajax_fiqh_get_questions', 'fiqh_get_questions');
