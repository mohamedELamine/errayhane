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
