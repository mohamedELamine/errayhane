<?php
/**
 * منطق التحكم بالوصول للمقررات حسب المستوى
 *
 * @package FiqhLearning_LMS
 */

if (!defined('ABSPATH')) {
    exit;
}

class FiqhLearning_Access_Control {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // تعديل الاستعلامات لإخفاء المقررات غير المتاحة للطالب
        add_action('pre_get_posts', array($this, 'filter_courses_by_level'));

        // منع الوصول المباشر للمقررات غير المتاحة
        add_action('template_redirect', array($this, 'check_course_access'));
    }

    /**
     * فلترة المقررات حسب مستوى الطالب
     */
    public function filter_courses_by_level($query) {
        // فقط في الصفحات الأمامية وليس في wp-admin
        if (is_admin() || !$query->is_main_query()) {
            return;
        }

        // فقط للمقررات
        if (!isset($query->query_vars['post_type']) || $query->query_vars['post_type'] !== 'fiqh_course') {
            return;
        }

        // فقط للطلبة المسجلين
        if (!is_user_logged_in()) {
            return;
        }

        $user = wp_get_current_user();
        if (!in_array('student', (array) $user->roles)) {
            return;
        }

        // جلب مستوى الطالب
        $student_level = get_user_meta($user->ID, '_fiqh_student_level', true);
        if (!$student_level) {
            return;
        }

        // جلب ترتيب المستوى الخاص بالطالب
        global $wpdb;
        $student_level_order = $wpdb->get_var($wpdb->prepare(
            "SELECT level_order FROM {$wpdb->prefix}fiqh_batches WHERE id = %d",
            $student_level
        ));

        if ($student_level_order === null) {
            return;
        }

        // جلب جميع المستويات التي يمكن للطالب الوصول إليها (الأقل أو المساوي)
        $accessible_levels = $wpdb->get_col($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}fiqh_batches WHERE level_order <= %d AND status = 'active'",
            $student_level_order
        ));

        if (empty($accessible_levels)) {
            return;
        }

        // إضافة meta query للفلترة
        $meta_query = array(
            array(
                'key' => '_fiqh_course_levels',
                'value' => serialize($accessible_levels),
                'compare' => 'REGEXP'
            )
        );

        $existing_meta_query = $query->get('meta_query');
        if (!empty($existing_meta_query)) {
            $meta_query = array_merge($existing_meta_query, $meta_query);
        }

        $query->set('meta_query', $meta_query);
    }

    /**
     * التحقق من إمكانية الوصول للمقرر
     */
    public function check_course_access() {
        // فقط على صفحات المقررات والدروس
        if (!is_singular(array('fiqh_course', 'fiqh_lesson'))) {
            return;
        }

        // السماح للمشرفين بالوصول لكل شيء
        if (current_user_can('administrator')) {
            return;
        }

        // يجب أن يكون المستخدم مسجلاً للدخول
        if (!is_user_logged_in()) {
            wp_redirect(home_url('/login'));
            exit;
        }

        $user = wp_get_current_user();

        // فقط للطلاب (المشرفون والمعلمون لديهم صلاحية كاملة)
        if (!in_array('student', (array) $user->roles)) {
            return;
        }

        $post_id = get_the_ID();
        $course_id = $post_id;

        // إذا كانت صفحة درس، نجلب المقرر المرتبط
        if (is_singular('fiqh_lesson')) {
            $course_id = get_post_meta($post_id, '_fiqh_lesson_course_id', true);
            if (!$course_id) {
                return;
            }
        }

        // جلب مستوى الطالب
        $student_level = get_user_meta($user->ID, '_fiqh_student_level', true);
        if (!$student_level) {
            // الطالب ليس لديه مستوى محدد - السماح بالوصول مؤقتاً
            return;
        }

        // التحقق من إمكانية الوصول للمقرر
        if (!$this->can_student_access_course($user->ID, $course_id)) {
            wp_redirect(home_url());
            exit;
        }
    }

    /**
     * التحقق من إمكانية وصول الطالب للمقرر حسب المستوى
     *
     * @param int $user_id معرف الطالب
     * @param int $course_id معرف المقرر
     * @return bool
     */
    public static function can_student_access_course($user_id, $course_id) {
        global $wpdb;

        // جلب مستوى الطالب
        $student_level = get_user_meta($user_id, '_fiqh_student_level', true);
        if (!$student_level) {
            // الطالب ليس لديه مستوى محدد - السماح بالوصول مؤقتاً
            return true;
        }

        // جلب ترتيب مستوى الطالب
        $student_level_order = $wpdb->get_var($wpdb->prepare(
            "SELECT level_order FROM {$wpdb->prefix}fiqh_batches WHERE id = %d",
            $student_level
        ));

        if ($student_level_order === null) {
            return false;
        }

        // جلب مستويات المقرر
        $course_levels = get_post_meta($course_id, '_fiqh_course_levels', true);
        if (!is_array($course_levels) || empty($course_levels)) {
            // المقرر لا ينتمي لأي مستوى - السماح بالوصول
            return true;
        }

        // التحقق من أن أحد مستويات المقرر أقل أو يساوي مستوى الطالب
        foreach ($course_levels as $course_level_id) {
            $course_level_order = $wpdb->get_var($wpdb->prepare(
                "SELECT level_order FROM {$wpdb->prefix}fiqh_batches WHERE id = %d",
                $course_level_id
            ));

            if ($course_level_order !== null && $course_level_order <= $student_level_order) {
                // المقرر ينتمي لمستوى أقل أو مساوي - السماح بالوصول
                return true;
            }
        }

        // المقرر ينتمي لمستوى أعلى فقط - منع الوصول
        return false;
    }

    /**
     * جلب المقررات المتاحة للطالب حسب مستواه
     *
     * @param int $user_id معرف الطالب
     * @return array قائمة معرفات المقررات المتاحة
     */
    public static function get_accessible_courses_for_student($user_id) {
        global $wpdb;

        // جلب مستوى الطالب
        $student_level = get_user_meta($user_id, '_fiqh_student_level', true);
        if (!$student_level) {
            // الطالب ليس لديه مستوى - إرجاع جميع المقررات
            return get_posts(array(
                'post_type' => 'fiqh_course',
                'posts_per_page' => -1,
                'fields' => 'ids',
                'post_status' => 'publish'
            ));
        }

        // جلب ترتيب مستوى الطالب
        $student_level_order = $wpdb->get_var($wpdb->prepare(
            "SELECT level_order FROM {$wpdb->prefix}fiqh_batches WHERE id = %d",
            $student_level
        ));

        if ($student_level_order === null) {
            return array();
        }

        // جلب جميع المستويات المتاحة (الأقل أو المساوي)
        $accessible_levels = $wpdb->get_col($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}fiqh_batches WHERE level_order <= %d AND status = 'active'",
            $student_level_order
        ));

        if (empty($accessible_levels)) {
            return array();
        }

        // جلب جميع المقررات
        $all_courses = get_posts(array(
            'post_type' => 'fiqh_course',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'post_status' => 'publish'
        ));

        $accessible_courses = array();
        foreach ($all_courses as $course_id) {
            $course_levels = get_post_meta($course_id, '_fiqh_course_levels', true);

            // إذا لم يتم تحديد مستوى للمقرر، فهو متاح للجميع
            if (!is_array($course_levels) || empty($course_levels)) {
                $accessible_courses[] = $course_id;
                continue;
            }

            // التحقق من وجود مستوى متاح
            foreach ($course_levels as $course_level_id) {
                if (in_array($course_level_id, $accessible_levels)) {
                    $accessible_courses[] = $course_id;
                    break;
                }
            }
        }

        return $accessible_courses;
    }
}

// تهيئة الـ Class
FiqhLearning_Access_Control::get_instance();
