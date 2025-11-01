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

    $result = $wpdb->insert(
        $table_name,
        array(
            'user_id' => $user_id,
            'lesson_id' => $lesson_id,
            'question' => $question,
            'created_at' => current_time('mysql'),
        ),
        array('%d', '%d', '%s', '%s')
    );

    if ($result) {
        wp_send_json_success(__('تم إرسال السؤال بنجاح', 'fiqhlearning'));
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
        ),
        array('id' => $question_id),
        array('%s', '%d', '%s'),
        array('%d')
    );

    if ($result !== false) {
        wp_send_json_success(__('تم إرسال الإجابة بنجاح', 'fiqhlearning'));
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
