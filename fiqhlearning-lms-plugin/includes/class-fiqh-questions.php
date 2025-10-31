<?php
/**
 * إدارة الأسئلة والإجابات
 *
 * @package FiqhLearning_LMS
 */

if (!defined('ABSPATH')) {
    exit;
}

class FiqhLearning_Questions {

    /**
     * إضافة سؤال جديد
     */
    public static function add_question($user_id, $question_text, $course_id = null, $lesson_id = null, $is_anonymous = false) {
        global $wpdb;
        $table = $wpdb->prefix . 'fiqh_questions';

        $result = $wpdb->insert(
            $table,
            array(
                'user_id' => $user_id,
                'course_id' => $course_id,
                'lesson_id' => $lesson_id,
                'question_text' => wp_kses_post($question_text),
                'is_anonymous' => $is_anonymous ? 1 : 0,
                'status' => 'pending',
            ),
            array('%d', '%d', '%d', '%s', '%d', '%s')
        );

        if ($result) {
            return $wpdb->insert_id;
        }

        return false;
    }

    /**
     * إضافة إجابة على سؤال
     */
    public static function add_answer($question_id, $user_id, $answer_text) {
        global $wpdb;
        $table = $wpdb->prefix . 'fiqh_answers';

        $result = $wpdb->insert(
            $table,
            array(
                'question_id' => $question_id,
                'user_id' => $user_id,
                'answer_text' => wp_kses_post($answer_text),
                'is_approved' => current_user_can('manage_options') ? 1 : 0,
            ),
            array('%d', '%d', '%s', '%d')
        );

        if ($result) {
            // تحديث حالة السؤال إلى "مجاب"
            if (current_user_can('manage_options') || current_user_can('edit_posts')) {
                self::update_question_status($question_id, 'answered');
            }

            return $wpdb->insert_id;
        }

        return false;
    }

    /**
     * الحصول على سؤال
     */
    public static function get_question($question_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'fiqh_questions';

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $question_id
        ));
    }

    /**
     * الحصول على أسئلة بفلاتر
     */
    public static function get_questions($args = array()) {
        global $wpdb;
        $table = $wpdb->prefix . 'fiqh_questions';

        $defaults = array(
            'course_id' => null,
            'lesson_id' => null,
            'user_id' => null,
            'status' => null,
            'is_featured' => null,
            'limit' => 20,
            'offset' => 0,
            'orderby' => 'created_at',
            'order' => 'DESC',
        );

        $args = wp_parse_args($args, $defaults);

        $where = array('1=1');
        $where_values = array();

        if ($args['course_id']) {
            $where[] = 'course_id = %d';
            $where_values[] = $args['course_id'];
        }

        if ($args['lesson_id']) {
            $where[] = 'lesson_id = %d';
            $where_values[] = $args['lesson_id'];
        }

        if ($args['user_id']) {
            $where[] = 'user_id = %d';
            $where_values[] = $args['user_id'];
        }

        if ($args['status']) {
            $where[] = 'status = %s';
            $where_values[] = $args['status'];
        }

        if ($args['is_featured'] !== null) {
            $where[] = 'is_featured = %d';
            $where_values[] = $args['is_featured'];
        }

        $where_clause = implode(' AND ', $where);
        $order = strtoupper($args['order']) === 'ASC' ? 'ASC' : 'DESC';

        $query = "SELECT * FROM $table WHERE $where_clause ORDER BY {$args['orderby']} $order LIMIT %d OFFSET %d";
        $where_values[] = $args['limit'];
        $where_values[] = $args['offset'];

        if (!empty($where_values)) {
            $query = $wpdb->prepare($query, $where_values);
        }

        return $wpdb->get_results($query);
    }

    /**
     * الحصول على إجابات سؤال
     */
    public static function get_answers($question_id, $approved_only = true) {
        global $wpdb;
        $table = $wpdb->prefix . 'fiqh_answers';

        $where = 'question_id = %d';
        if ($approved_only) {
            $where .= ' AND is_approved = 1';
        }

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE $where ORDER BY is_best_answer DESC, created_at ASC",
            $question_id
        ));
    }

    /**
     * تحديث حالة السؤال
     */
    public static function update_question_status($question_id, $status) {
        global $wpdb;
        $table = $wpdb->prefix . 'fiqh_questions';

        return $wpdb->update(
            $table,
            array('status' => $status),
            array('id' => $question_id),
            array('%s'),
            array('%d')
        );
    }

    /**
     * زيادة عدد المشاهدات
     */
    public static function increment_views($question_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'fiqh_questions';

        return $wpdb->query($wpdb->prepare(
            "UPDATE $table SET views_count = views_count + 1 WHERE id = %d",
            $question_id
        ));
    }

    /**
     * تحديد إجابة كأفضل إجابة
     */
    public static function mark_as_best_answer($answer_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'fiqh_answers';

        // إلغاء أي إجابة أخرى كانت الأفضل
        $answer = $wpdb->get_row($wpdb->prepare("SELECT question_id FROM $table WHERE id = %d", $answer_id));
        if ($answer) {
            $wpdb->update($table, array('is_best_answer' => 0), array('question_id' => $answer->question_id));
        }

        // تحديد الإجابة الحالية كالأفضل
        return $wpdb->update(
            $table,
            array('is_best_answer' => 1),
            array('id' => $answer_id),
            array('%d'),
            array('%d')
        );
    }

    /**
     * البحث في الأسئلة
     */
    public static function search_questions($search_term, $args = array()) {
        global $wpdb;
        $table = $wpdb->prefix . 'fiqh_questions';

        $defaults = array(
            'limit' => 20,
            'offset' => 0,
        );

        $args = wp_parse_args($args, $defaults);

        $search_term = '%' . $wpdb->esc_like($search_term) . '%';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE question_text LIKE %s ORDER BY created_at DESC LIMIT %d OFFSET %d",
            $search_term,
            $args['limit'],
            $args['offset']
        ));
    }
}
