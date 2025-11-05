<?php
/**
 * إدارة قاعدة البيانات - إنشاء الجداول المخصصة
 *
 * @package FiqhLearning_LMS
 */

if (!defined('ABSPATH')) {
    exit;
}

class FiqhLearning_Database {

    /**
     * إنشاء جميع الجداول المطلوبة
     */
    public static function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // جدول الدفعات/المستويات (Batches/Levels)
        $table_batches = $wpdb->prefix . 'fiqh_batches';
        $sql_batches = "CREATE TABLE IF NOT EXISTS $table_batches (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            description text,
            level_order int(3) DEFAULT 0,
            start_date date,
            end_date date,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY status (status),
            KEY level_order (level_order)
        ) $charset_collate;";
        dbDelta($sql_batches);

        // جدول ربط الطلبة بالمستويات (Batch Students)
        $table_batch_students = $wpdb->prefix . 'fiqh_batch_students';
        $sql_batch_students = "CREATE TABLE IF NOT EXISTS $table_batch_students (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            batch_id bigint(20) UNSIGNED NOT NULL,
            status varchar(20) DEFAULT 'active',
            enrolled_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY batch_id (batch_id),
            KEY status (status),
            UNIQUE KEY user_batch (user_id, batch_id)
        ) $charset_collate;";
        dbDelta($sql_batch_students);

        // جدول المسارات (Tracks)
        $table_tracks = $wpdb->prefix . 'fiqh_tracks';
        $sql_tracks = "CREATE TABLE IF NOT EXISTS $table_tracks (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            description text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        dbDelta($sql_tracks);

        // جدول التسجيل في المقررات (Enrollments)
        $table_enrollments = $wpdb->prefix . 'fiqh_enrollments';
        $sql_enrollments = "CREATE TABLE IF NOT EXISTS $table_enrollments (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            course_id bigint(20) UNSIGNED NOT NULL,
            batch_id bigint(20) UNSIGNED,
            track_id bigint(20) UNSIGNED,
            enrolled_at datetime DEFAULT CURRENT_TIMESTAMP,
            status varchar(20) DEFAULT 'active',
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY course_id (course_id),
            KEY batch_id (batch_id),
            KEY status (status),
            UNIQUE KEY user_course (user_id, course_id)
        ) $charset_collate;";
        dbDelta($sql_enrollments);

        // جدول التقدم (Progress)
        $table_progress = $wpdb->prefix . 'fiqh_progress';
        $sql_progress = "CREATE TABLE IF NOT EXISTS $table_progress (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            lesson_id bigint(20) UNSIGNED NOT NULL,
            course_id bigint(20) UNSIGNED NOT NULL,
            status varchar(20) DEFAULT 'in_progress',
            progress_percentage int(3) DEFAULT 0,
            last_position varchar(50),
            completed_at datetime,
            started_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY lesson_id (lesson_id),
            KEY course_id (course_id),
            KEY status (status),
            UNIQUE KEY user_lesson (user_id, lesson_id)
        ) $charset_collate;";
        dbDelta($sql_progress);

        // جدول الأسئلة (Questions) - نظام مبسط مع الإجابة في نفس الجدول
        $table_questions = $wpdb->prefix . 'fiqh_questions';
        $sql_questions = "CREATE TABLE IF NOT EXISTS $table_questions (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            course_id bigint(20) UNSIGNED,
            lesson_id bigint(20) UNSIGNED,
            question text NOT NULL,
            answer text,
            answered_by bigint(20) UNSIGNED,
            answered_at datetime,
            is_anonymous tinyint(1) DEFAULT 0,
            status varchar(20) DEFAULT 'pending',
            is_featured tinyint(1) DEFAULT 0,
            views_count int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY course_id (course_id),
            KEY lesson_id (lesson_id),
            KEY answered_by (answered_by),
            KEY status (status),
            KEY is_featured (is_featured)
        ) $charset_collate;";
        dbDelta($sql_questions);

        // جدول الاختبارات (Quizzes)
        $table_quizzes = $wpdb->prefix . 'fiqh_quizzes';
        $sql_quizzes = "CREATE TABLE IF NOT EXISTS $table_quizzes (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            lesson_id bigint(20) UNSIGNED NOT NULL,
            title varchar(255) NOT NULL,
            description text,
            passing_score int(3) DEFAULT 70,
            time_limit int(11) DEFAULT 0,
            attempts_allowed int(3) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY lesson_id (lesson_id)
        ) $charset_collate;";
        dbDelta($sql_quizzes);

        // جدول أسئلة الاختبارات (Quiz Questions)
        $table_quiz_questions = $wpdb->prefix . 'fiqh_quiz_questions';
        $sql_quiz_questions = "CREATE TABLE IF NOT EXISTS $table_quiz_questions (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            quiz_id bigint(20) UNSIGNED NOT NULL,
            question_text text NOT NULL,
            question_type varchar(50) DEFAULT 'multiple_choice',
            points int(3) DEFAULT 1,
            order_num int(3) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY quiz_id (quiz_id)
        ) $charset_collate;";
        dbDelta($sql_quiz_questions);

        // جدول خيارات أسئلة الاختبارات (Quiz Question Options)
        $table_quiz_options = $wpdb->prefix . 'fiqh_quiz_options';
        $sql_quiz_options = "CREATE TABLE IF NOT EXISTS $table_quiz_options (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            question_id bigint(20) UNSIGNED NOT NULL,
            option_text text NOT NULL,
            is_correct tinyint(1) DEFAULT 0,
            order_num int(3) DEFAULT 0,
            PRIMARY KEY (id),
            KEY question_id (question_id)
        ) $charset_collate;";
        dbDelta($sql_quiz_options);

        // جدول محاولات الاختبارات (Quiz Attempts)
        $table_quiz_attempts = $wpdb->prefix . 'fiqh_quiz_attempts';
        $sql_quiz_attempts = "CREATE TABLE IF NOT EXISTS $table_quiz_attempts (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            quiz_id bigint(20) UNSIGNED NOT NULL,
            score decimal(5,2) DEFAULT 0,
            max_score decimal(5,2) DEFAULT 100,
            passed tinyint(1) DEFAULT 0,
            started_at datetime DEFAULT CURRENT_TIMESTAMP,
            completed_at datetime,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY quiz_id (quiz_id)
        ) $charset_collate;";
        dbDelta($sql_quiz_attempts);

        // جدول إجابات محاولات الاختبارات (Quiz Attempt Answers)
        $table_quiz_attempt_answers = $wpdb->prefix . 'fiqh_quiz_attempt_answers';
        $sql_quiz_attempt_answers = "CREATE TABLE IF NOT EXISTS $table_quiz_attempt_answers (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            attempt_id bigint(20) UNSIGNED NOT NULL,
            question_id bigint(20) UNSIGNED NOT NULL,
            answer_text text,
            selected_option_id bigint(20) UNSIGNED,
            is_correct tinyint(1) DEFAULT 0,
            points_earned decimal(5,2) DEFAULT 0,
            PRIMARY KEY (id),
            KEY attempt_id (attempt_id),
            KEY question_id (question_id)
        ) $charset_collate;";
        dbDelta($sql_quiz_attempt_answers);

        // جدول الشكاوى/التقارير (Reports)
        $table_reports = $wpdb->prefix . 'fiqh_reports';
        $sql_reports = "CREATE TABLE IF NOT EXISTS $table_reports (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            subject varchar(255) NOT NULL,
            message text NOT NULL,
            status varchar(20) DEFAULT 'pending',
            admin_response text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            responded_at datetime,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY status (status)
        ) $charset_collate;";
        dbDelta($sql_reports);

        // جدول المفضلة (Favorites)
        $table_favorites = $wpdb->prefix . 'fiqh_favorites';
        $sql_favorites = "CREATE TABLE IF NOT EXISTS $table_favorites (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            item_id bigint(20) UNSIGNED NOT NULL,
            item_type varchar(50) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY item_id (item_id),
            KEY item_type (item_type),
            UNIQUE KEY user_item (user_id, item_id, item_type)
        ) $charset_collate;";
        dbDelta($sql_favorites);

        // جدول الإشعارات (Notifications)
        $table_notifications = $wpdb->prefix . 'fiqh_notifications';
        $sql_notifications = "CREATE TABLE IF NOT EXISTS $table_notifications (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            title varchar(255) NOT NULL,
            message text NOT NULL,
            type varchar(50) DEFAULT 'info',
            is_read tinyint(1) DEFAULT 0,
            action_url varchar(255),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY is_read (is_read),
            KEY type (type)
        ) $charset_collate;";
        dbDelta($sql_notifications);

        // جدول ملاحظات الدروس (Lesson Notes)
        $table_notes = $wpdb->prefix . 'fiqh_lesson_notes';
        $sql_notes = "CREATE TABLE IF NOT EXISTS $table_notes (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            lesson_id bigint(20) UNSIGNED NOT NULL,
            course_id bigint(20) UNSIGNED NOT NULL,
            note_text text NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY lesson_id (lesson_id),
            KEY course_id (course_id)
        ) $charset_collate;";
        dbDelta($sql_notes);

        // جدول الاشتراكات الشهرية (Student Subscriptions)
        $table_subscriptions = $wpdb->prefix . 'fiqh_subscriptions';
        $sql_subscriptions = "CREATE TABLE IF NOT EXISTS $table_subscriptions (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            batch_id bigint(20) UNSIGNED,
            monthly_amount decimal(10,2) NOT NULL DEFAULT 0,
            subscription_type varchar(50) DEFAULT 'monthly',
            status varchar(20) DEFAULT 'active',
            start_date date NOT NULL,
            end_date date,
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY batch_id (batch_id),
            KEY status (status),
            KEY start_date (start_date)
        ) $charset_collate;";
        dbDelta($sql_subscriptions);

        // جدول دفعات الاشتراك (Subscription Payments)
        $table_payments = $wpdb->prefix . 'fiqh_subscription_payments';
        $sql_payments = "CREATE TABLE IF NOT EXISTS $table_payments (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            subscription_id bigint(20) UNSIGNED NOT NULL,
            user_id bigint(20) UNSIGNED NOT NULL,
            amount decimal(10,2) NOT NULL,
            payment_month varchar(7) NOT NULL,
            payment_date date NOT NULL,
            payment_method varchar(50) DEFAULT 'cash',
            status varchar(20) DEFAULT 'paid',
            notes text,
            created_by bigint(20) UNSIGNED,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY subscription_id (subscription_id),
            KEY user_id (user_id),
            KEY payment_month (payment_month),
            KEY status (status),
            KEY payment_date (payment_date)
        ) $charset_collate;";
        dbDelta($sql_payments);

        // تحديث رقم إصدار قاعدة البيانات
        update_option('fiqh_lms_db_version', '1.0.0');
    }

    /**
     * حذف الجداول (استخدام حذر!)
     */
    public static function drop_tables() {
        global $wpdb;

        $tables = array(
            'fiqh_batches',
            'fiqh_tracks',
            'fiqh_enrollments',
            'fiqh_progress',
            'fiqh_questions',
            'fiqh_answers',
            'fiqh_quizzes',
            'fiqh_quiz_questions',
            'fiqh_quiz_options',
            'fiqh_quiz_attempts',
            'fiqh_quiz_attempt_answers',
            'fiqh_reports',
            'fiqh_favorites',
            'fiqh_notifications',
        );

        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}{$table}");
        }
    }
}
