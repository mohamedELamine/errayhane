<?php
/**
 * إدارة ملاحظات الدروس
 *
 * @package FiqhLearning_LMS
 */

if (!defined('ABSPATH')) {
    exit;
}

class FiqhLearning_Notes {

    /**
     * إضافة ملاحظة جديدة
     */
    public static function add_note($user_id, $lesson_id, $course_id, $note_text) {
        global $wpdb;

        $table = $wpdb->prefix . 'fiqh_lesson_notes';

        $result = $wpdb->insert(
            $table,
            array(
                'user_id' => $user_id,
                'lesson_id' => $lesson_id,
                'course_id' => $course_id,
                'note_text' => sanitize_textarea_field($note_text),
            ),
            array('%d', '%d', '%d', '%s')
        );

        if ($result === false) {
            return new WP_Error('db_error', __('فشل في إضافة الملاحظة', 'fiqh-lms'));
        }

        return $wpdb->insert_id;
    }

    /**
     * تحديث ملاحظة
     */
    public static function update_note($note_id, $user_id, $note_text) {
        global $wpdb;

        $table = $wpdb->prefix . 'fiqh_lesson_notes';

        // التحقق من أن المستخدم هو صاحب الملاحظة
        $note = self::get_note($note_id);
        if (!$note || $note->user_id != $user_id) {
            return new WP_Error('permission_denied', __('ليس لديك صلاحية لتحديث هذه الملاحظة', 'fiqh-lms'));
        }

        $result = $wpdb->update(
            $table,
            array(
                'note_text' => sanitize_textarea_field($note_text),
            ),
            array('id' => $note_id),
            array('%s'),
            array('%d')
        );

        if ($result === false) {
            return new WP_Error('db_error', __('فشل في تحديث الملاحظة', 'fiqh-lms'));
        }

        return true;
    }

    /**
     * حذف ملاحظة
     */
    public static function delete_note($note_id, $user_id) {
        global $wpdb;

        $table = $wpdb->prefix . 'fiqh_lesson_notes';

        // التحقق من أن المستخدم هو صاحب الملاحظة
        $note = self::get_note($note_id);
        if (!$note || $note->user_id != $user_id) {
            return new WP_Error('permission_denied', __('ليس لديك صلاحية لحذف هذه الملاحظة', 'fiqh-lms'));
        }

        $result = $wpdb->delete(
            $table,
            array('id' => $note_id),
            array('%d')
        );

        if ($result === false) {
            return new WP_Error('db_error', __('فشل في حذف الملاحظة', 'fiqh-lms'));
        }

        return true;
    }

    /**
     * الحصول على ملاحظة واحدة
     */
    public static function get_note($note_id) {
        global $wpdb;

        $table = $wpdb->prefix . 'fiqh_lesson_notes';

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $note_id
        ));
    }

    /**
     * الحصول على جميع ملاحظات المستخدم لدرس معين
     */
    public static function get_lesson_notes($user_id, $lesson_id) {
        global $wpdb;

        $table = $wpdb->prefix . 'fiqh_lesson_notes';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table
            WHERE user_id = %d AND lesson_id = %d
            ORDER BY created_at DESC",
            $user_id,
            $lesson_id
        ));
    }

    /**
     * الحصول على جميع ملاحظات المستخدم لمقرر معين
     */
    public static function get_course_notes($user_id, $course_id) {
        global $wpdb;

        $table = $wpdb->prefix . 'fiqh_lesson_notes';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT n.*, l.post_title as lesson_title
            FROM $table n
            LEFT JOIN {$wpdb->posts} l ON n.lesson_id = l.ID
            WHERE n.user_id = %d AND n.course_id = %d
            ORDER BY n.created_at DESC",
            $user_id,
            $course_id
        ));
    }

    /**
     * الحصول على جميع ملاحظات المستخدم
     */
    public static function get_all_user_notes($user_id, $limit = 50) {
        global $wpdb;

        $table = $wpdb->prefix . 'fiqh_lesson_notes';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT n.*,
                    l.post_title as lesson_title,
                    c.post_title as course_title
            FROM $table n
            LEFT JOIN {$wpdb->posts} l ON n.lesson_id = l.ID
            LEFT JOIN {$wpdb->posts} c ON n.course_id = c.ID
            WHERE n.user_id = %d
            ORDER BY n.created_at DESC
            LIMIT %d",
            $user_id,
            $limit
        ));
    }

    /**
     * عدد ملاحظات المستخدم
     */
    public static function count_user_notes($user_id) {
        global $wpdb;

        $table = $wpdb->prefix . 'fiqh_lesson_notes';

        return (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE user_id = %d",
            $user_id
        ));
    }

    /**
     * ترحيل الملاحظات القديمة من user_meta إلى الجدول الجديد
     */
    public static function migrate_old_notes() {
        global $wpdb;

        // البحث عن جميع user_meta التي تحتوي على ملاحظات
        $old_notes = $wpdb->get_results(
            "SELECT user_id, meta_key, meta_value
            FROM {$wpdb->usermeta}
            WHERE meta_key LIKE '_fiqh_lesson_notes_%'"
        );

        $migrated = 0;

        foreach ($old_notes as $old_note) {
            // استخراج lesson_id من meta_key
            $lesson_id = str_replace('_fiqh_lesson_notes_', '', $old_note->meta_key);

            if (empty($old_note->meta_value) || !is_numeric($lesson_id)) {
                continue;
            }

            // الحصول على course_id من الدرس
            $course_id = get_post_meta($lesson_id, '_fiqh_lesson_course_id', true);

            if (!$course_id) {
                continue;
            }

            // إضافة الملاحظة إلى الجدول الجديد
            $result = self::add_note(
                $old_note->user_id,
                $lesson_id,
                $course_id,
                $old_note->meta_value
            );

            if (!is_wp_error($result)) {
                $migrated++;
                // حذف الملاحظة القديمة
                delete_user_meta($old_note->user_id, $old_note->meta_key);
            }
        }

        return $migrated;
    }
}
