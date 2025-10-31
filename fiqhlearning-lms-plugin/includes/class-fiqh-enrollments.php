<?php
/**
 * إدارة تسجيل الطلاب في المقررات
 *
 * @package FiqhLearning_LMS
 */

if (!defined('ABSPATH')) {
    exit;
}

class FiqhLearning_Enrollments {

    /**
     * تسجيل طالب في مقرر
     */
    public static function enroll_user($user_id, $course_id, $batch_id = null, $track_id = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'fiqh_enrollments';

        // التحقق من عدم التسجيل المسبق
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE user_id = %d AND course_id = %d",
            $user_id,
            $course_id
        ));

        if ($exists > 0) {
            return new WP_Error('already_enrolled', __('الطالب مسجل بالفعل في هذا المقرر', 'fiqh-lms'));
        }

        $result = $wpdb->insert(
            $table,
            array(
                'user_id' => $user_id,
                'course_id' => $course_id,
                'batch_id' => $batch_id,
                'track_id' => $track_id,
                'status' => 'active',
            ),
            array('%d', '%d', '%d', '%d', '%s')
        );

        if ($result) {
            return $wpdb->insert_id;
        }

        return false;
    }

    /**
     * إلغاء تسجيل طالب من مقرر
     */
    public static function unenroll_user($user_id, $course_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'fiqh_enrollments';

        return $wpdb->update(
            $table,
            array('status' => 'inactive'),
            array('user_id' => $user_id, 'course_id' => $course_id),
            array('%s'),
            array('%d', '%d')
        );
    }

    /**
     * التحقق من تسجيل طالب في مقرر
     */
    public static function is_user_enrolled($user_id, $course_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'fiqh_enrollments';

        $enrolled = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE user_id = %d AND course_id = %d AND status = 'active'",
            $user_id,
            $course_id
        ));

        return $enrolled > 0;
    }

    /**
     * الحصول على جميع مقررات طالب
     */
    public static function get_user_courses($user_id, $status = 'active') {
        global $wpdb;
        $table = $wpdb->prefix . 'fiqh_enrollments';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE user_id = %d AND status = %s ORDER BY enrolled_at DESC",
            $user_id,
            $status
        ));
    }

    /**
     * الحصول على جميع الطلاب المسجلين في مقرر
     */
    public static function get_course_students($course_id, $status = 'active') {
        global $wpdb;
        $table = $wpdb->prefix . 'fiqh_enrollments';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE course_id = %d AND status = %s ORDER BY enrolled_at DESC",
            $course_id,
            $status
        ));
    }

    /**
     * تسجيل عدة طلاب في مقرر واحد
     */
    public static function bulk_enroll($user_ids, $course_id, $batch_id = null, $track_id = null) {
        $results = array('success' => 0, 'failed' => 0, 'errors' => array());

        foreach ($user_ids as $user_id) {
            $result = self::enroll_user($user_id, $course_id, $batch_id, $track_id);
            if (is_wp_error($result)) {
                $results['failed']++;
                $results['errors'][] = array('user_id' => $user_id, 'error' => $result->get_error_message());
            } else {
                $results['success']++;
            }
        }

        return $results;
    }
}
