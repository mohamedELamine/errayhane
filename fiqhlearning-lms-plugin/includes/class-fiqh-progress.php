<?php
/**
 * إدارة تقدم الطالب في الدروس
 *
 * @package FiqhLearning_LMS
 */

if (!defined('ABSPATH')) {
    exit;
}

class FiqhLearning_Progress {

    /**
     * بدء درس
     */
    public static function start_lesson($user_id, $lesson_id, $course_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'fiqh_progress';

        // التحقق من وجود سجل مسبق
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE user_id = %d AND lesson_id = %d",
            $user_id,
            $lesson_id
        ));

        if ($exists) {
            return $exists;
        }

        $wpdb->insert(
            $table,
            array(
                'user_id' => $user_id,
                'lesson_id' => $lesson_id,
                'course_id' => $course_id,
                'status' => 'in_progress',
                'progress_percentage' => 0,
            ),
            array('%d', '%d', '%d', '%s', '%d')
        );

        return $wpdb->insert_id;
    }

    /**
     * تحديث تقدم الدرس
     */
    public static function update_progress($user_id, $lesson_id, $percentage, $position = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'fiqh_progress';

        $data = array('progress_percentage' => $percentage);
        if ($position) {
            $data['last_position'] = $position;
        }

        if ($percentage >= 100) {
            $data['status'] = 'completed';
            $data['completed_at'] = current_time('mysql');
        }

        return $wpdb->update(
            $table,
            $data,
            array('user_id' => $user_id, 'lesson_id' => $lesson_id),
            array('%d', '%s', '%s'),
            array('%d', '%d')
        );
    }

    /**
     * إكمال درس
     */
    public static function complete_lesson($user_id, $lesson_id) {
        return self::update_progress($user_id, $lesson_id, 100);
    }

    /**
     * الحصول على تقدم المستخدم في درس
     */
    public static function get_lesson_progress($user_id, $lesson_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'fiqh_progress';

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE user_id = %d AND lesson_id = %d",
            $user_id,
            $lesson_id
        ));
    }

    /**
     * الحصول على نسبة الإنجاز لمقرر
     */
    public static function get_course_progress_percentage($user_id, $course_id) {
        global $wpdb;
        $progress_table = $wpdb->prefix . 'fiqh_progress';

        // عدد الدروس الكلي
        $total_lessons = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}postmeta
            WHERE meta_key = '_fiqh_lesson_course_id' AND meta_value = %d",
            $course_id
        ));

        if ($total_lessons == 0) {
            return 0;
        }

        // عدد الدروس المكتملة
        $completed_lessons = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $progress_table
            WHERE user_id = %d AND course_id = %d AND status = 'completed'",
            $user_id,
            $course_id
        ));

        return round(($completed_lessons / $total_lessons) * 100);
    }

    /**
     * الحصول على جميع دروس مقرر مع التقدم
     */
    public static function get_course_lessons_with_progress($user_id, $course_id) {
        global $wpdb;

        $lessons = get_posts(array(
            'post_type' => 'fiqh_lesson',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_fiqh_lesson_course_id',
                    'value' => $course_id,
                )
            ),
            'orderby' => 'menu_order',
            'order' => 'ASC',
        ));

        foreach ($lessons as &$lesson) {
            $lesson->progress = self::get_lesson_progress($user_id, $lesson->ID);
        }

        return $lessons;
    }
}
