<?php
/**
 * معالجات AJAX
 *
 * @package FiqhLearning_LMS
 */

if (!defined('ABSPATH')) {
    exit;
}

class FiqhLearning_Ajax {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->define_hooks();
    }

    private function define_hooks() {
        // تحديث التقدم
        add_action('wp_ajax_fiqh_update_progress', array($this, 'update_progress'));

        // إكمال درس
        add_action('wp_ajax_fiqh_complete_lesson', array($this, 'complete_lesson'));

        // إضافة سؤال
        add_action('wp_ajax_fiqh_add_question', array($this, 'add_question'));

        // إضافة إجابة
        add_action('wp_ajax_fiqh_add_answer', array($this, 'add_answer'));

        // جلب الأسئلة بالفلاتر
        add_action('wp_ajax_fiqh_get_questions', array($this, 'get_questions'));
        add_action('wp_ajax_nopriv_fiqh_get_questions', array($this, 'get_questions'));

        // إضافة للمفضلة
        add_action('wp_ajax_fiqh_toggle_favorite', array($this, 'toggle_favorite'));
    }

    /**
     * تحديث تقدم الدرس
     */
    public function update_progress() {
        check_ajax_referer('fiqh-ajax-nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('يجب تسجيل الدخول', 'fiqh-lms')));
        }

        $user_id = get_current_user_id();
        $lesson_id = isset($_POST['lesson_id']) ? intval($_POST['lesson_id']) : 0;
        $percentage = isset($_POST['percentage']) ? intval($_POST['percentage']) : 0;
        $position = isset($_POST['position']) ? sanitize_text_field($_POST['position']) : null;

        if (!$lesson_id) {
            wp_send_json_error(array('message' => __('معرف الدرس مطلوب', 'fiqh-lms')));
        }

        $result = FiqhLearning_Progress::update_progress($user_id, $lesson_id, $percentage, $position);

        if ($result !== false) {
            wp_send_json_success(array('message' => __('تم تحديث التقدم', 'fiqh-lms')));
        } else {
            wp_send_json_error(array('message' => __('فشل تحديث التقدم', 'fiqh-lms')));
        }
    }

    /**
     * إكمال درس
     */
    public function complete_lesson() {
        check_ajax_referer('fiqh-ajax-nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('يجب تسجيل الدخول', 'fiqh-lms')));
        }

        $user_id = get_current_user_id();
        $lesson_id = isset($_POST['lesson_id']) ? intval($_POST['lesson_id']) : 0;

        if (!$lesson_id) {
            wp_send_json_error(array('message' => __('معرف الدرس مطلوب', 'fiqh-lms')));
        }

        $result = FiqhLearning_Progress::complete_lesson($user_id, $lesson_id);

        if ($result !== false) {
            wp_send_json_success(array('message' => __('تم إكمال الدرس', 'fiqh-lms')));
        } else {
            wp_send_json_error(array('message' => __('فشل إكمال الدرس', 'fiqh-lms')));
        }
    }

    /**
     * إضافة سؤال
     */
    public function add_question() {
        check_ajax_referer('fiqh-ajax-nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('يجب تسجيل الدخول', 'fiqh-lms')));
        }

        $user_id = get_current_user_id();
        $question_text = isset($_POST['question_text']) ? wp_kses_post($_POST['question_text']) : '';
        $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : null;
        $lesson_id = isset($_POST['lesson_id']) ? intval($_POST['lesson_id']) : null;
        $is_anonymous = isset($_POST['is_anonymous']) && $_POST['is_anonymous'] === 'true';

        if (empty($question_text)) {
            wp_send_json_error(array('message' => __('نص السؤال مطلوب', 'fiqh-lms')));
        }

        $question_id = FiqhLearning_Questions::add_question($user_id, $question_text, $course_id, $lesson_id, $is_anonymous);

        if ($question_id) {
            wp_send_json_success(array(
                'message' => __('تم إضافة السؤال بنجاح', 'fiqh-lms'),
                'question_id' => $question_id,
            ));
        } else {
            wp_send_json_error(array('message' => __('فشل إضافة السؤال', 'fiqh-lms')));
        }
    }

    /**
     * إضافة إجابة
     */
    public function add_answer() {
        check_ajax_referer('fiqh-ajax-nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('يجب تسجيل الدخول', 'fiqh-lms')));
        }

        $user_id = get_current_user_id();
        $question_id = isset($_POST['question_id']) ? intval($_POST['question_id']) : 0;
        $answer_text = isset($_POST['answer_text']) ? wp_kses_post($_POST['answer_text']) : '';

        if (!$question_id || empty($answer_text)) {
            wp_send_json_error(array('message' => __('معرف السؤال ونص الإجابة مطلوبان', 'fiqh-lms')));
        }

        $answer_id = FiqhLearning_Questions::add_answer($question_id, $user_id, $answer_text);

        if ($answer_id) {
            wp_send_json_success(array(
                'message' => __('تم إضافة الإجابة بنجاح', 'fiqh-lms'),
                'answer_id' => $answer_id,
            ));
        } else {
            wp_send_json_error(array('message' => __('فشل إضافة الإجابة', 'fiqh-lms')));
        }
    }

    /**
     * جلب الأسئلة بالفلاتر
     */
    public function get_questions() {
        $args = array(
            'course_id' => isset($_POST['course_id']) ? intval($_POST['course_id']) : null,
            'lesson_id' => isset($_POST['lesson_id']) ? intval($_POST['lesson_id']) : null,
            'status' => isset($_POST['status']) ? sanitize_text_field($_POST['status']) : null,
            'limit' => isset($_POST['limit']) ? intval($_POST['limit']) : 20,
            'offset' => isset($_POST['offset']) ? intval($_POST['offset']) : 0,
        );

        $questions = FiqhLearning_Questions::get_questions($args);

        if ($questions) {
            wp_send_json_success(array('questions' => $questions));
        } else {
            wp_send_json_error(array('message' => __('لم يتم العثور على أسئلة', 'fiqh-lms')));
        }
    }

    /**
     * تبديل المفضلة
     */
    public function toggle_favorite() {
        check_ajax_referer('fiqh-ajax-nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('يجب تسجيل الدخول', 'fiqh-lms')));
        }

        global $wpdb;
        $table = $wpdb->prefix . 'fiqh_favorites';

        $user_id = get_current_user_id();
        $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
        $item_type = isset($_POST['item_type']) ? sanitize_text_field($_POST['item_type']) : '';

        if (!$item_id || !$item_type) {
            wp_send_json_error(array('message' => __('بيانات غير صحيحة', 'fiqh-lms')));
        }

        // التحقق من وجود العنصر في المفضلة
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE user_id = %d AND item_id = %d AND item_type = %s",
            $user_id,
            $item_id,
            $item_type
        ));

        if ($exists) {
            // إزالة من المفضلة
            $wpdb->delete($table, array('id' => $exists), array('%d'));
            wp_send_json_success(array('action' => 'removed', 'message' => __('تم الإزالة من المفضلة', 'fiqh-lms')));
        } else {
            // إضافة للمفضلة
            $wpdb->insert($table, array(
                'user_id' => $user_id,
                'item_id' => $item_id,
                'item_type' => $item_type,
            ), array('%d', '%d', '%s'));
            wp_send_json_success(array('action' => 'added', 'message' => __('تم الإضافة للمفضلة', 'fiqh-lms')));
        }
    }
}
