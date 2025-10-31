<?php
/**
 * قوائم لوحة الإدارة
 *
 * @package FiqhLearning_LMS
 */

if (!defined('ABSPATH')) {
    exit;
}

class FiqhLearning_Admin_Menus {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menus'));
    }

    /**
     * إضافة قوائم الإدارة
     */
    public function add_admin_menus() {
        // قائمة التسجيل في المقررات
        add_submenu_page(
            'edit.php?post_type=fiqh_course',
            __('التسجيل في المقررات', 'fiqh-lms'),
            __('التسجيل', 'fiqh-lms'),
            'manage_options',
            'fiqh-enrollments',
            array($this, 'enrollments_page')
        );

        // قائمة الدفعات
        add_submenu_page(
            'edit.php?post_type=fiqh_course',
            __('الدفعات', 'fiqh-lms'),
            __('الدفعات', 'fiqh-lms'),
            'manage_options',
            'fiqh-batches',
            array($this, 'batches_page')
        );

        // قائمة الأسئلة والإجابات
        add_submenu_page(
            'edit.php?post_type=fiqh_course',
            __('الأسئلة والإجابات', 'fiqh-lms'),
            __('الأسئلة', 'fiqh-lms'),
            'manage_options',
            'fiqh-questions',
            array($this, 'questions_page')
        );

        // قائمة التقارير
        add_submenu_page(
            'edit.php?post_type=fiqh_course',
            __('التقارير', 'fiqh-lms'),
            __('التقارير', 'fiqh-lms'),
            'manage_options',
            'fiqh-reports',
            array($this, 'reports_page')
        );

        // قائمة الإعدادات
        add_submenu_page(
            'edit.php?post_type=fiqh_course',
            __('إعدادات FiqhLearning', 'fiqh-lms'),
            __('الإعدادات', 'fiqh-lms'),
            'manage_options',
            'fiqh-settings',
            array($this, 'settings_page')
        );
    }

    /**
     * صفحة التسجيل في المقررات
     */
    public function enrollments_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('إدارة التسجيل في المقررات', 'fiqh-lms'); ?></h1>

            <div class="fiqh-enrollment-form">
                <h2><?php _e('تسجيل طالب في مقرر', 'fiqh-lms'); ?></h2>
                <form method="post" action="">
                    <?php wp_nonce_field('fiqh_enroll_student', 'fiqh_enroll_nonce'); ?>

                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="student_id"><?php _e('الطالب', 'fiqh-lms'); ?></label></th>
                            <td>
                                <select name="student_id" id="student_id" required>
                                    <option value=""><?php _e('-- اختر الطالب --', 'fiqh-lms'); ?></option>
                                    <?php
                                    $students = get_users(array('role' => 'student'));
                                    foreach ($students as $student) {
                                        echo '<option value="' . $student->ID . '">' . esc_html($student->display_name) . ' (' . esc_html($student->user_email) . ')</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="course_id"><?php _e('المقرر', 'fiqh-lms'); ?></label></th>
                            <td>
                                <select name="course_id" id="course_id" required>
                                    <option value=""><?php _e('-- اختر المقرر --', 'fiqh-lms'); ?></option>
                                    <?php
                                    $courses = get_posts(array('post_type' => 'fiqh_course', 'posts_per_page' => -1));
                                    foreach ($courses as $course) {
                                        echo '<option value="' . $course->ID . '">' . esc_html($course->post_title) . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                    </table>

                    <p class="submit">
                        <input type="submit" name="fiqh_enroll_submit" class="button button-primary" value="<?php _e('تسجيل الطالب', 'fiqh-lms'); ?>">
                    </p>
                </form>
            </div>

            <?php
            // معالجة التسجيل
            if (isset($_POST['fiqh_enroll_submit']) && check_admin_referer('fiqh_enroll_student', 'fiqh_enroll_nonce')) {
                $student_id = intval($_POST['student_id']);
                $course_id = intval($_POST['course_id']);

                if ($student_id && $course_id) {
                    $result = FiqhLearning_Enrollments::enroll_user($student_id, $course_id);
                    if (is_wp_error($result)) {
                        echo '<div class="notice notice-error"><p>' . $result->get_error_message() . '</p></div>';
                    } else {
                        echo '<div class="notice notice-success"><p>' . __('تم تسجيل الطالب بنجاح', 'fiqh-lms') . '</p></div>';
                    }
                }
            }
            ?>

            <hr>

            <h2><?php _e('الطلاب المسجلون', 'fiqh-lms'); ?></h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('الطالب', 'fiqh-lms'); ?></th>
                        <th><?php _e('المقرر', 'fiqh-lms'); ?></th>
                        <th><?php _e('تاريخ التسجيل', 'fiqh-lms'); ?></th>
                        <th><?php _e('الحالة', 'fiqh-lms'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    global $wpdb;
                    $enrollments = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}fiqh_enrollments ORDER BY enrolled_at DESC LIMIT 50");

                    if ($enrollments) {
                        foreach ($enrollments as $enrollment) {
                            $user = get_userdata($enrollment->user_id);
                            $course = get_post($enrollment->course_id);
                            ?>
                            <tr>
                                <td><?php echo $user ? esc_html($user->display_name) : __('مستخدم محذوف', 'fiqh-lms'); ?></td>
                                <td><?php echo $course ? esc_html($course->post_title) : __('مقرر محذوف', 'fiqh-lms'); ?></td>
                                <td><?php echo esc_html($enrollment->enrolled_at); ?></td>
                                <td><?php echo esc_html($enrollment->status); ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo '<tr><td colspan="4">' . __('لا توجد تسجيلات', 'fiqh-lms') . '</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    /**
     * صفحة الدفعات
     */
    public function batches_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('إدارة الدفعات', 'fiqh-lms'); ?></h1>
            <p><?php _e('صفحة إدارة الدفعات قيد التطوير', 'fiqh-lms'); ?></p>
        </div>
        <?php
    }

    /**
     * صفحة الأسئلة
     */
    public function questions_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('إدارة الأسئلة والإجابات', 'fiqh-lms'); ?></h1>

            <?php
            $questions = FiqhLearning_Questions::get_questions(array('limit' => 50));

            if ($questions) {
                ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('السؤال', 'fiqh-lms'); ?></th>
                            <th><?php _e('الطالب', 'fiqh-lms'); ?></th>
                            <th><?php _e('المقرر', 'fiqh-lms'); ?></th>
                            <th><?php _e('التاريخ', 'fiqh-lms'); ?></th>
                            <th><?php _e('الحالة', 'fiqh-lms'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($questions as $question) :
                            $user = get_userdata($question->user_id);
                            $course = get_post($question->course_id);
                        ?>
                        <tr>
                            <td><?php echo esc_html(wp_trim_words($question->question_text, 10)); ?></td>
                            <td><?php echo $user ? esc_html($user->display_name) : __('مجهول', 'fiqh-lms'); ?></td>
                            <td><?php echo $course ? esc_html($course->post_title) : '-'; ?></td>
                            <td><?php echo esc_html($question->created_at); ?></td>
                            <td><?php echo esc_html($question->status); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php
            } else {
                echo '<p>' . __('لا توجد أسئلة', 'fiqh-lms') . '</p>';
            }
            ?>
        </div>
        <?php
    }

    /**
     * صفحة التقارير
     */
    public function reports_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('التقارير والإحصائيات', 'fiqh-lms'); ?></h1>
            <p><?php _e('صفحة التقارير قيد التطوير', 'fiqh-lms'); ?></p>
        </div>
        <?php
    }

    /**
     * صفحة الإعدادات
     */
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('إعدادات FiqhLearning LMS', 'fiqh-lms'); ?></h1>
            <p><?php _e('صفحة الإعدادات قيد التطوير', 'fiqh-lms'); ?></p>
        </div>
        <?php
    }
}
