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
        add_action('admin_menu', array($this, 'hide_post_types_from_menu'), 999);
        add_action('admin_menu', array($this, 'add_questions_count_badge'));
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
     * صفحة الأسئلة
     */
    public function questions_page() {
        global $wpdb;

        // معالجة حذف سؤال
        if (isset($_POST['delete_question']) && check_admin_referer('delete_question_action', 'delete_question_nonce')) {
            $question_id = intval($_POST['question_id']);

            if ($question_id) {
                $updated = $wpdb->update(
                    $wpdb->prefix . 'fiqh_questions',
                    array('status' => 'deleted'),
                    array('id' => $question_id),
                    array('%s'),
                    array('%d')
                );

                if ($updated) {
                    echo '<div class="notice notice-success is-dismissible"><p>' . __('تم حذف السؤال بنجاح', 'fiqh-lms') . '</p></div>';
                }
            }
        }

        // معالجة الإجابة على سؤال
        if (isset($_POST['answer_question']) && check_admin_referer('answer_question_action', 'answer_question_nonce')) {
            $question_id = intval($_POST['question_id']);
            $answer_text = sanitize_textarea_field($_POST['answer_text']);

            if ($question_id && $answer_text) {
                $updated = $wpdb->update(
                    $wpdb->prefix . 'fiqh_questions',
                    array(
                        'answer' => $answer_text,
                        'answered_by' => get_current_user_id(),
                        'answered_at' => current_time('mysql'),
                        'status' => 'answered'
                    ),
                    array('id' => $question_id),
                    array('%s', '%d', '%s', '%s'),
                    array('%d')
                );

                if ($updated) {
                    // إرسال إشعار للطالب
                    $question_data = $wpdb->get_row($wpdb->prepare(
                        "SELECT user_id, course_id FROM {$wpdb->prefix}fiqh_questions WHERE id = %d",
                        $question_id
                    ));

                    if ($question_data && $question_data->user_id) {
                        $course = get_post($question_data->course_id);
                        $course_title = $course ? $course->post_title : __('المقرر', 'fiqh-lms');

                        $wpdb->insert(
                            $wpdb->prefix . 'fiqh_notifications',
                            array(
                                'user_id' => $question_data->user_id,
                                'title' => __('تم الإجابة على سؤالك', 'fiqh-lms'),
                                'message' => sprintf(__('تم الإجابة على سؤالك في مقرر: %s', 'fiqh-lms'), $course_title),
                                'type' => 'success',
                                'action_url' => get_permalink(get_page_by_path('questions')),
                                'created_at' => current_time('mysql')
                            ),
                            array('%d', '%s', '%s', '%s', '%s', '%s')
                        );
                    }

                    echo '<div class="notice notice-success is-dismissible"><p>' . __('تم الإجابة على السؤال بنجاح وإرسال إشعار للطالب', 'fiqh-lms') . '</p></div>';
                }
            }
        }

        // جلب الأسئلة مع كل التفاصيل
        $questions = $wpdb->get_results(
            "SELECT
                q.*,
                u.display_name as student_name,
                c.post_title as course_name,
                l.post_title as lesson_name,
                bs.batch_id
            FROM {$wpdb->prefix}fiqh_questions q
            LEFT JOIN {$wpdb->users} u ON q.user_id = u.ID
            LEFT JOIN {$wpdb->posts} c ON q.course_id = c.ID
            LEFT JOIN {$wpdb->posts} l ON q.lesson_id = l.ID
            LEFT JOIN {$wpdb->prefix}fiqh_batch_students bs ON q.user_id = bs.user_id AND bs.status = 'active'
            WHERE q.status != 'deleted'
            ORDER BY q.created_at DESC
            LIMIT 100"
        );
        ?>
        <div class="wrap">
            <h1><?php _e('إدارة الأسئلة والإجابات', 'fiqh-lms'); ?></h1>

            <style>
                .question-details-modal {
                    display: none;
                    position: fixed;
                    z-index: 100000;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0,0,0,0.5);
                }
                .question-details-content {
                    background-color: #fefefe;
                    margin: 5% auto;
                    padding: 30px;
                    border: 1px solid #888;
                    width: 80%;
                    max-width: 800px;
                    border-radius: 8px;
                    max-height: 80vh;
                    overflow-y: auto;
                }
                .question-details-close {
                    color: #aaa;
                    float: left;
                    font-size: 28px;
                    font-weight: bold;
                    cursor: pointer;
                }
                .question-details-close:hover {
                    color: #000;
                }
                .question-full-text {
                    background: #f5f5f5;
                    padding: 20px;
                    border-radius: 5px;
                    margin: 20px 0;
                    line-height: 1.8;
                }
                .question-meta-item {
                    margin: 10px 0;
                    padding: 10px;
                    background: #fff;
                    border-right: 3px solid #2271b1;
                }
                .answer-form {
                    margin-top: 30px;
                }
                .answer-form textarea {
                    width: 100%;
                    min-height: 150px;
                    padding: 10px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                }
            </style>

            <?php if ($questions) : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th style="width: 35%;"><?php _e('السؤال', 'fiqh-lms'); ?></th>
                            <th><?php _e('الطالب', 'fiqh-lms'); ?></th>
                            <th><?php _e('المقرر', 'fiqh-lms'); ?></th>
                            <th><?php _e('الدرس', 'fiqh-lms'); ?></th>
                            <th><?php _e('العلم', 'fiqh-lms'); ?></th>
                            <th><?php _e('الدفعة', 'fiqh-lms'); ?></th>
                            <th><?php _e('التاريخ', 'fiqh-lms'); ?></th>
                            <th><?php _e('الحالة', 'fiqh-lms'); ?></th>
                            <th><?php _e('إجراءات', 'fiqh-lms'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($questions as $question) :
                            $course = get_post($question->course_id);
                            $lesson = get_post($question->lesson_id);

                            // جلب العلم من المقرر
                            $sciences = wp_get_post_terms($question->course_id, 'fiqh_course_science');
                            $science_name = !empty($sciences) ? $sciences[0]->name : '-';

                            // جلب اسم الدفعة
                            $batch_name = '-';
                            if ($question->batch_id) {
                                $batch = $wpdb->get_var($wpdb->prepare(
                                    "SELECT name FROM {$wpdb->prefix}fiqh_batches WHERE id = %d",
                                    $question->batch_id
                                ));
                                if ($batch) {
                                    $batch_name = $batch;
                                }
                            }
                        ?>
                        <tr>
                            <td><?php echo isset($question->question) ? esc_html(wp_trim_words($question->question, 8)) : '-'; ?></td>
                            <td><?php echo $question->is_anonymous ? __('مجهول', 'fiqh-lms') : esc_html($question->student_name); ?></td>
                            <td>
                                <?php if ($course) : ?>
                                    <a href="<?php echo get_permalink($course->ID); ?>" target="_blank">
                                        <?php echo esc_html($course->post_title); ?>
                                    </a>
                                <?php else : ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($lesson) : ?>
                                    <a href="<?php echo get_permalink($lesson->ID); ?>" target="_blank">
                                        <?php echo esc_html($lesson->post_title); ?>
                                    </a>
                                <?php else : ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?php echo esc_html($science_name); ?></td>
                            <td><?php echo esc_html($batch_name); ?></td>
                            <td><?php echo esc_html(date_i18n('Y/m/d', strtotime($question->created_at))); ?></td>
                            <td>
                                <?php if ($question->status === 'answered') : ?>
                                    <span style="color: #00a32a;">✓ <?php _e('مُجابة', 'fiqh-lms'); ?></span>
                                <?php else : ?>
                                    <span style="color: #d63638;">⏳ <?php _e('بانتظار', 'fiqh-lms'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="button button-small" onclick="openQuestionModal(<?php echo $question->id; ?>)">
                                    <?php _e('عرض وإجابة', 'fiqh-lms'); ?>
                                </button>
                                <form method="post" style="display:inline-block; margin-right: 5px;" onsubmit="return confirm('<?php _e('هل أنت متأكد من حذف هذا السؤال؟', 'fiqh-lms'); ?>');">
                                    <?php wp_nonce_field('delete_question_action', 'delete_question_nonce'); ?>
                                    <input type="hidden" name="question_id" value="<?php echo $question->id; ?>">
                                    <button type="submit" name="delete_question" class="button button-small" style="color: #d63638;">
                                        <?php _e('حذف', 'fiqh-lms'); ?>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal للسؤال -->
                        <div id="question-modal-<?php echo $question->id; ?>" class="question-details-modal">
                            <div class="question-details-content">
                                <span class="question-details-close" onclick="closeQuestionModal(<?php echo $question->id; ?>)">&times;</span>
                                <h2><?php _e('تفاصيل السؤال', 'fiqh-lms'); ?></h2>

                                <div class="question-meta-item">
                                    <strong><?php _e('الطالب:', 'fiqh-lms'); ?></strong>
                                    <?php echo $question->is_anonymous ? __('مجهول', 'fiqh-lms') : esc_html($question->student_name); ?>
                                </div>

                                <div class="question-meta-item">
                                    <strong><?php _e('المقرر:', 'fiqh-lms'); ?></strong>
                                    <?php if ($course) : ?>
                                        <a href="<?php echo get_permalink($course->ID); ?>" target="_blank">
                                            <?php echo esc_html($course->post_title); ?>
                                        </a>
                                    <?php else : ?>
                                        -
                                    <?php endif; ?>
                                </div>

                                <?php if ($lesson) : ?>
                                <div class="question-meta-item">
                                    <strong><?php _e('الدرس:', 'fiqh-lms'); ?></strong>
                                    <a href="<?php echo get_permalink($lesson->ID); ?>" target="_blank">
                                        <?php echo esc_html($lesson->post_title); ?>
                                    </a>
                                </div>
                                <?php endif; ?>

                                <div class="question-meta-item">
                                    <strong><?php _e('العلم الشرعي:', 'fiqh-lms'); ?></strong>
                                    <?php echo esc_html($science_name); ?>
                                </div>

                                <div class="question-meta-item">
                                    <strong><?php _e('الدفعة:', 'fiqh-lms'); ?></strong>
                                    <?php echo esc_html($batch_name); ?>
                                </div>

                                <div class="question-meta-item">
                                    <strong><?php _e('التاريخ:', 'fiqh-lms'); ?></strong>
                                    <?php echo esc_html($question->created_at); ?>
                                </div>

                                <h3><?php _e('نص السؤال:', 'fiqh-lms'); ?></h3>
                                <div class="question-full-text">
                                    <?php echo nl2br(esc_html($question->question)); ?>
                                </div>

                                <?php if ($question->status === 'answered' && $question->answer) : ?>
                                    <h3><?php _e('الإجابة:', 'fiqh-lms'); ?></h3>
                                    <div class="question-full-text" style="border-right-color: #00a32a;">
                                        <?php echo nl2br(esc_html($question->answer)); ?>
                                    </div>
                                    <p><em><?php _e('أجاب في:', 'fiqh-lms'); ?> <?php echo esc_html($question->answered_at); ?></em></p>

                                    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd;">
                                        <form method="post" onsubmit="return confirm('<?php _e('هل أنت متأكد من حذف هذا السؤال؟', 'fiqh-lms'); ?>');">
                                            <?php wp_nonce_field('delete_question_action', 'delete_question_nonce'); ?>
                                            <input type="hidden" name="question_id" value="<?php echo $question->id; ?>">
                                            <button type="submit" name="delete_question" class="button" style="color: #d63638;">
                                                <?php _e('حذف السؤال', 'fiqh-lms'); ?>
                                            </button>
                                        </form>
                                    </div>
                                <?php else : ?>
                                    <div class="answer-form">
                                        <h3><?php _e('الإجابة على السؤال:', 'fiqh-lms'); ?></h3>
                                        <form method="post" action="">
                                            <?php wp_nonce_field('answer_question_action', 'answer_question_nonce'); ?>
                                            <input type="hidden" name="question_id" value="<?php echo $question->id; ?>">
                                            <textarea name="answer_text" required placeholder="<?php _e('اكتب إجابتك هنا...', 'fiqh-lms'); ?>"></textarea>
                                            <p class="submit">
                                                <input type="submit" name="answer_question" class="button button-primary" value="<?php _e('حفظ الإجابة', 'fiqh-lms'); ?>">
                                            </p>
                                        </form>
                                    </div>

                                    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd;">
                                        <form method="post" onsubmit="return confirm('<?php _e('هل أنت متأكد من حذف هذا السؤال؟', 'fiqh-lms'); ?>');">
                                            <?php wp_nonce_field('delete_question_action', 'delete_question_nonce'); ?>
                                            <input type="hidden" name="question_id" value="<?php echo $question->id; ?>">
                                            <button type="submit" name="delete_question" class="button" style="color: #d63638;">
                                                <?php _e('حذف السؤال', 'fiqh-lms'); ?>
                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php _e('لا توجد أسئلة حالياً', 'fiqh-lms'); ?></p>
            <?php endif; ?>
        </div>

        <script>
        function openQuestionModal(questionId) {
            document.getElementById('question-modal-' + questionId).style.display = 'block';
        }

        function closeQuestionModal(questionId) {
            document.getElementById('question-modal-' + questionId).style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('question-details-modal')) {
                event.target.style.display = 'none';
            }
        }
        </script>
        <?php
    }

    /**
     * صفحة التقارير
     */
    public function reports_page() {
        global $wpdb;

        // جلب الإحصائيات العامة
        $total_students = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->users} u INNER JOIN {$wpdb->usermeta} m ON u.ID = m.user_id WHERE m.meta_key = 'wp_capabilities' AND m.meta_value LIKE '%student%'");
        $total_courses = wp_count_posts('fiqh_course')->publish;
        $total_lessons = wp_count_posts('fiqh_lesson')->publish;
        $total_enrollments = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_enrollments WHERE status = 'active'");
        $total_questions = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_questions");
        $pending_questions = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_questions WHERE status = 'pending'");

        // التسجيلات الأخيرة (آخر 7 أيام)
        $enrollments_last_7_days = $wpdb->get_results(
            "SELECT DATE(enrolled_at) as date, COUNT(*) as count
            FROM {$wpdb->prefix}fiqh_enrollments
            WHERE enrolled_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY DATE(enrolled_at)
            ORDER BY date ASC"
        );

        // المقررات الأكثر تسجيلاً
        $popular_courses = $wpdb->get_results(
            "SELECT c.ID, c.post_title, COUNT(e.id) as enrollment_count
            FROM {$wpdb->posts} c
            LEFT JOIN {$wpdb->prefix}fiqh_enrollments e ON c.ID = e.course_id
            WHERE c.post_type = 'fiqh_course' AND c.post_status = 'publish'
            GROUP BY c.ID
            ORDER BY enrollment_count DESC
            LIMIT 5"
        );

        // إحصائيات الأسئلة
        $questions_stats = $wpdb->get_results(
            "SELECT status, COUNT(*) as count
            FROM {$wpdb->prefix}fiqh_questions
            GROUP BY status"
        );

        // جلب قائمة الطلاب لاختيار تقرير مفصل
        $students = get_users(array('role' => 'student', 'orderby' => 'display_name'));

        // إذا تم اختيار طالب
        $selected_student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;
        $student_report = null;
        if ($selected_student_id) {
            $student_report = $this->get_student_detailed_report($selected_student_id);
        }

        ?>
        <div class="wrap fiqh-reports-page">
            <h1 class="wp-heading-inline">
                <span class="dashicons dashicons-chart-bar" style="font-size: 28px; vertical-align: middle;"></span>
                <?php _e('التقارير والإحصائيات', 'fiqh-lms'); ?>
            </h1>

            <?php if ($selected_student_id && $student_report): ?>
                <a href="<?php echo admin_url('edit.php?post_type=fiqh_course&page=fiqh-reports'); ?>" class="page-title-action">
                    <?php _e('← العودة للإحصائيات العامة', 'fiqh-lms'); ?>
                </a>
                <a href="#" onclick="window.print(); return false;" class="page-title-action">
                    <span class="dashicons dashicons-printer"></span> <?php _e('طباعة', 'fiqh-lms'); ?>
                </a>
            <?php endif; ?>

            <hr class="wp-header-end">

            <style>
                .fiqh-reports-page .stats-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 20px;
                    margin: 20px 0;
                }
                .fiqh-reports-page .stat-card {
                    background: #fff;
                    padding: 20px;
                    border-radius: 8px;
                    border: 1px solid #dcdcde;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                }
                .fiqh-reports-page .stat-card .stat-icon {
                    font-size: 32px;
                    color: #2271b1;
                    margin-bottom: 10px;
                }
                .fiqh-reports-page .stat-card .stat-value {
                    font-size: 32px;
                    font-weight: 700;
                    color: #1e1e1e;
                    line-height: 1;
                    margin-bottom: 5px;
                }
                .fiqh-reports-page .stat-card .stat-label {
                    color: #646970;
                    font-size: 14px;
                }
                .fiqh-reports-page .chart-container {
                    background: #fff;
                    padding: 20px;
                    border-radius: 8px;
                    border: 1px solid #dcdcde;
                    margin: 20px 0;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                }
                .fiqh-reports-page .chart-container h2 {
                    margin-top: 0;
                    padding-bottom: 10px;
                    border-bottom: 2px solid #2271b1;
                }
                .fiqh-reports-page canvas {
                    max-height: 300px;
                }
                .fiqh-reports-page .two-column-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 20px;
                }
                @media (max-width: 1024px) {
                    .fiqh-reports-page .two-column-grid {
                        grid-template-columns: 1fr;
                    }
                }
                .student-selector {
                    background: #fff;
                    padding: 20px;
                    border-radius: 8px;
                    border: 1px solid #dcdcde;
                    margin: 20px 0;
                }
                .student-report-header {
                    background: linear-gradient(135deg, #2271b1 0%, #0d5a8f 100%);
                    color: #fff;
                    padding: 30px;
                    border-radius: 8px;
                    margin: 20px 0;
                }
                .progress-bar-container {
                    background: #f0f0f1;
                    border-radius: 8px;
                    height: 24px;
                    overflow: hidden;
                    margin: 10px 0;
                }
                .progress-bar {
                    background: linear-gradient(90deg, #2271b1 0%, #00a32a 100%);
                    height: 100%;
                    transition: width 0.3s ease;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: #fff;
                    font-size: 12px;
                    font-weight: 600;
                }
                @media print {
                    .page-title-action, .student-selector { display: none !important; }
                }
            </style>

            <?php if ($selected_student_id && $student_report): ?>
                <!-- تقرير الطالب المفصل -->
                <?php $this->render_student_report($student_report); ?>
            <?php else: ?>
                <!-- الإحصائيات العامة -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon"><span class="dashicons dashicons-groups"></span></div>
                        <div class="stat-value"><?php echo number_format_i18n($total_students); ?></div>
                        <div class="stat-label"><?php _e('إجمالي الطلاب', 'fiqh-lms'); ?></div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon"><span class="dashicons dashicons-book"></span></div>
                        <div class="stat-value"><?php echo number_format_i18n($total_courses); ?></div>
                        <div class="stat-label"><?php _e('المقررات المنشورة', 'fiqh-lms'); ?></div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon"><span class="dashicons dashicons-welcome-learn-more"></span></div>
                        <div class="stat-value"><?php echo number_format_i18n($total_lessons); ?></div>
                        <div class="stat-label"><?php _e('إجمالي الدروس', 'fiqh-lms'); ?></div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon"><span class="dashicons dashicons-yes-alt"></span></div>
                        <div class="stat-value"><?php echo number_format_i18n($total_enrollments); ?></div>
                        <div class="stat-label"><?php _e('التسجيلات النشطة', 'fiqh-lms'); ?></div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon"><span class="dashicons dashicons-editor-help"></span></div>
                        <div class="stat-value"><?php echo number_format_i18n($total_questions); ?></div>
                        <div class="stat-label"><?php _e('إجمالي الأسئلة', 'fiqh-lms'); ?></div>
                    </div>

                    <div class="stat-card" style="border-right: 4px solid #d63638;">
                        <div class="stat-icon" style="color: #d63638;"><span class="dashicons dashicons-warning"></span></div>
                        <div class="stat-value" style="color: #d63638;"><?php echo number_format_i18n($pending_questions); ?></div>
                        <div class="stat-label"><?php _e('أسئلة بانتظار الإجابة', 'fiqh-lms'); ?></div>
                    </div>
                </div>

                <div class="two-column-grid">
                    <!-- مخطط التسجيلات -->
                    <div class="chart-container">
                        <h2><?php _e('التسجيلات (آخر 7 أيام)', 'fiqh-lms'); ?></h2>
                        <canvas id="enrollmentsChart"></canvas>
                    </div>

                    <!-- مخطط الأسئلة -->
                    <div class="chart-container">
                        <h2><?php _e('حالة الأسئلة', 'fiqh-lms'); ?></h2>
                        <canvas id="questionsChart"></canvas>
                    </div>
                </div>

                <!-- المقررات الأكثر شعبية -->
                <div class="chart-container">
                    <h2><?php _e('المقررات الأكثر تسجيلاً', 'fiqh-lms'); ?></h2>
                    <canvas id="popularCoursesChart"></canvas>
                </div>

                <!-- محدد الطالب -->
                <div class="student-selector">
                    <h2><?php _e('تقرير مفصل لطالب', 'fiqh-lms'); ?></h2>
                    <form method="get" action="">
                        <input type="hidden" name="post_type" value="fiqh_course">
                        <input type="hidden" name="page" value="fiqh-reports">
                        <p>
                            <label for="student_id"><strong><?php _e('اختر الطالب:', 'fiqh-lms'); ?></strong></label><br>
                            <select name="student_id" id="student_id" style="min-width: 300px;">
                                <option value=""><?php _e('-- اختر طالباً --', 'fiqh-lms'); ?></option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?php echo $student->ID; ?>">
                                        <?php echo esc_html($student->display_name); ?> (<?php echo esc_html($student->user_email); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="button button-primary"><?php _e('عرض التقرير', 'fiqh-lms'); ?></button>
                        </p>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!$selected_student_id): ?>
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // مخطط التسجيلات
            <?php
            $dates = array();
            $counts = array();
            // ملء آخر 7 أيام
            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $dates[] = date_i18n('j M', strtotime($date));
                $found = false;
                foreach ($enrollments_last_7_days as $enrollment) {
                    if ($enrollment->date == $date) {
                        $counts[] = $enrollment->count;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $counts[] = 0;
                }
            }
            ?>
            const enrollmentsCtx = document.getElementById('enrollmentsChart');
            new Chart(enrollmentsCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($dates); ?>,
                    datasets: [{
                        label: '<?php _e('عدد التسجيلات', 'fiqh-lms'); ?>',
                        data: <?php echo json_encode($counts); ?>,
                        borderColor: '#2271b1',
                        backgroundColor: 'rgba(34, 113, 177, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });

            // مخطط الأسئلة
            <?php
            $questions_data = array('pending' => 0, 'answered' => 0);
            foreach ($questions_stats as $stat) {
                $questions_data[$stat->status] = $stat->count;
            }
            ?>
            const questionsCtx = document.getElementById('questionsChart');
            new Chart(questionsCtx, {
                type: 'doughnut',
                data: {
                    labels: ['<?php _e('بانتظار الإجابة', 'fiqh-lms'); ?>', '<?php _e('مُجابة', 'fiqh-lms'); ?>'],
                    datasets: [{
                        data: [<?php echo $questions_data['pending']; ?>, <?php echo $questions_data['answered']; ?>],
                        backgroundColor: ['#d63638', '#00a32a']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });

            // مخطط المقررات الأكثر شعبية
            const popularCoursesCtx = document.getElementById('popularCoursesChart');
            new Chart(popularCoursesCtx, {
                type: 'bar',
                data: {
                    labels: [
                        <?php
                        foreach ($popular_courses as $course) {
                            echo '"' . esc_js($course->post_title) . '",';
                        }
                        ?>
                    ],
                    datasets: [{
                        label: '<?php _e('عدد الطلاب المسجلين', 'fiqh-lms'); ?>',
                        data: [
                            <?php
                            foreach ($popular_courses as $course) {
                                echo $course->enrollment_count . ',';
                            }
                            ?>
                        ],
                        backgroundColor: '#2271b1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        });
        </script>
        <?php endif; ?>
        <?php
    }

    /**
     * جلب تقرير مفصل لطالب
     */
    private function get_student_detailed_report($student_id) {
        global $wpdb;

        $student = get_userdata($student_id);
        if (!$student) {
            return null;
        }

        $report = array(
            'student' => $student,
            'enrollments' => array(),
            'total_progress' => 0,
            'questions_count' => 0,
            'answered_questions' => 0
        );

        // جلب التسجيلات
        $enrollments = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}fiqh_enrollments WHERE user_id = %d",
            $student_id
        ));

        $total_progress_sum = 0;
        foreach ($enrollments as $enrollment) {
            $course = get_post($enrollment->course_id);
            if ($course) {
                // جلب الدروس المكتملة
                $completed = $wpdb->get_results($wpdb->prepare(
                    "SELECT * FROM {$wpdb->prefix}fiqh_progress
                    WHERE user_id = %d AND course_id = %d AND status = 'completed'",
                    $student_id,
                    $enrollment->course_id
                ));

                // حساب إجمالي الدروس في المقرر
                $total_lessons = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->posts}
                    WHERE post_type = 'fiqh_lesson'
                    AND post_status = 'publish'
                    AND post_parent = %d",
                    $enrollment->course_id
                ));

                // حساب نسبة التقدم
                $progress = 0;
                if ($total_lessons > 0) {
                    $progress = round((count($completed) / $total_lessons) * 100);
                }

                $course_data = array(
                    'course' => $course,
                    'enrolled_at' => $enrollment->enrolled_at,
                    'progress' => $progress,
                    'status' => $enrollment->status,
                    'completed_lessons' => array()
                );

                foreach ($completed as $lesson_progress) {
                    $lesson = get_post($lesson_progress->lesson_id);
                    if ($lesson) {
                        $course_data['completed_lessons'][] = $lesson;
                    }
                }

                $report['enrollments'][] = $course_data;
                $total_progress_sum += $progress;
            }
        }

        if (count($enrollments) > 0) {
            $report['total_progress'] = round($total_progress_sum / count($enrollments));
        }

        // إحصائيات الأسئلة
        $report['questions_count'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_questions WHERE user_id = %d",
            $student_id
        ));

        $report['answered_questions'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_questions WHERE user_id = %d AND status = 'answered'",
            $student_id
        ));

        return $report;
    }

    /**
     * عرض تقرير الطالب
     */
    private function render_student_report($report) {
        $student = $report['student'];
        ?>
        <div class="student-report-header">
            <h2 style="margin: 0 0 10px 0; font-size: 28px;">
                <span class="dashicons dashicons-id" style="vertical-align: middle;"></span>
                <?php echo esc_html($student->display_name); ?>
            </h2>
            <p style="margin: 0; opacity: 0.9;">
                <strong><?php _e('البريد الإلكتروني:', 'fiqh-lms'); ?></strong> <?php echo esc_html($student->user_email); ?>
            </p>
            <p style="margin: 5px 0 0 0; opacity: 0.9;">
                <strong><?php _e('تاريخ التسجيل:', 'fiqh-lms'); ?></strong>
                <?php echo date_i18n(get_option('date_format'), strtotime($student->user_registered)); ?>
            </p>
        </div>

        <!-- إحصائيات سريعة -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><span class="dashicons dashicons-book-alt"></span></div>
                <div class="stat-value"><?php echo count($report['enrollments']); ?></div>
                <div class="stat-label"><?php _e('المقررات المسجلة', 'fiqh-lms'); ?></div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="color: #00a32a;"><span class="dashicons dashicons-chart-line"></span></div>
                <div class="stat-value" style="color: #00a32a;"><?php echo $report['total_progress']; ?>%</div>
                <div class="stat-label"><?php _e('متوسط التقدم', 'fiqh-lms'); ?></div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><span class="dashicons dashicons-editor-help"></span></div>
                <div class="stat-value"><?php echo $report['questions_count']; ?></div>
                <div class="stat-label"><?php _e('الأسئلة المطروحة', 'fiqh-lms'); ?></div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="color: #00a32a;"><span class="dashicons dashicons-yes"></span></div>
                <div class="stat-value" style="color: #00a32a;"><?php echo $report['answered_questions']; ?></div>
                <div class="stat-label"><?php _e('الأسئلة المجابة', 'fiqh-lms'); ?></div>
            </div>
        </div>

        <!-- تفاصيل المقررات -->
        <div class="chart-container">
            <h2><?php _e('تفاصيل المقررات', 'fiqh-lms'); ?></h2>

            <?php if (empty($report['enrollments'])): ?>
                <p><?php _e('لم يسجل الطالب في أي مقرر بعد.', 'fiqh-lms'); ?></p>
            <?php else: ?>
                <?php foreach ($report['enrollments'] as $enrollment): ?>
                    <div style="margin-bottom: 30px; padding: 20px; background: #f6f7f7; border-radius: 8px; border-right: 4px solid #2271b1;">
                        <h3 style="margin-top: 0;">
                            <a href="<?php echo get_permalink($enrollment['course']->ID); ?>" target="_blank">
                                <?php echo esc_html($enrollment['course']->post_title); ?>
                            </a>
                        </h3>

                        <p style="color: #646970; margin: 10px 0;">
                            <strong><?php _e('تاريخ التسجيل:', 'fiqh-lms'); ?></strong>
                            <?php echo date_i18n(get_option('date_format'), strtotime($enrollment['enrolled_at'])); ?>
                            &nbsp;|&nbsp;
                            <strong><?php _e('الحالة:', 'fiqh-lms'); ?></strong>
                            <span style="color: <?php echo $enrollment['status'] == 'completed' ? '#00a32a' : '#2271b1'; ?>;">
                                <?php echo $enrollment['status'] == 'completed' ? __('مكتمل', 'fiqh-lms') : __('نشط', 'fiqh-lms'); ?>
                            </span>
                        </p>

                        <div>
                            <strong><?php _e('التقدم:', 'fiqh-lms'); ?></strong>
                            <div class="progress-bar-container">
                                <div class="progress-bar" style="width: <?php echo $enrollment['progress']; ?>%;">
                                    <?php echo $enrollment['progress']; ?>%
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($enrollment['completed_lessons'])): ?>
                            <details style="margin-top: 15px;">
                                <summary style="cursor: pointer; font-weight: 600;">
                                    <?php printf(__('الدروس المكتملة (%d)', 'fiqh-lms'), count($enrollment['completed_lessons'])); ?>
                                </summary>
                                <ul style="margin: 10px 0; padding-right: 20px;">
                                    <?php foreach ($enrollment['completed_lessons'] as $lesson): ?>
                                        <li>
                                            <a href="<?php echo get_permalink($lesson->ID); ?>" target="_blank">
                                                <?php echo esc_html($lesson->post_title); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </details>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * صفحة الإعدادات
     */
    public function settings_page() {
        // معالجة حفظ الإعدادات
        if (isset($_POST['fiqh_save_settings']) && check_admin_referer('fiqh_settings_action', 'fiqh_settings_nonce')) {
            // إعدادات عامة
            update_option('fiqh_site_name', sanitize_text_field($_POST['fiqh_site_name']));
            update_option('fiqh_admin_email', sanitize_email($_POST['fiqh_admin_email']));
            update_option('fiqh_enable_notifications', isset($_POST['fiqh_enable_notifications']) ? 1 : 0);

            // إعدادات البريد الإلكتروني
            update_option('fiqh_email_from_name', sanitize_text_field($_POST['fiqh_email_from_name']));
            update_option('fiqh_email_from_address', sanitize_email($_POST['fiqh_email_from_address']));
            update_option('fiqh_email_enrollment_subject', sanitize_text_field($_POST['fiqh_email_enrollment_subject']));
            update_option('fiqh_email_enrollment_body', wp_kses_post($_POST['fiqh_email_enrollment_body']));

            // إعدادات المقررات
            update_option('fiqh_auto_enroll', isset($_POST['fiqh_auto_enroll']) ? 1 : 0);
            update_option('fiqh_certificate_enabled', isset($_POST['fiqh_certificate_enabled']) ? 1 : 0);
            update_option('fiqh_min_completion_percentage', intval($_POST['fiqh_min_completion_percentage']));

            echo '<div class="notice notice-success is-dismissible"><p>' . __('تم حفظ الإعدادات بنجاح', 'fiqh-lms') . '</p></div>';
        }

        // جلب الإعدادات الحالية
        $site_name = get_option('fiqh_site_name', get_bloginfo('name'));
        $admin_email = get_option('fiqh_admin_email', get_option('admin_email'));
        $enable_notifications = get_option('fiqh_enable_notifications', 1);

        $email_from_name = get_option('fiqh_email_from_name', get_bloginfo('name'));
        $email_from_address = get_option('fiqh_email_from_address', get_option('admin_email'));
        $email_enrollment_subject = get_option('fiqh_email_enrollment_subject', 'مرحباً بك في {course_name}');
        $email_enrollment_body = get_option('fiqh_email_enrollment_body', 'عزيزي {student_name},\n\nتم تسجيلك بنجاح في مقرر {course_name}.\n\nيمكنك البدء بمشاهدة الدروس من خلال لوحة التحكم الخاصة بك.\n\nمع أطيب التمنيات،\nفريق {site_name}');

        $auto_enroll = get_option('fiqh_auto_enroll', 0);
        $certificate_enabled = get_option('fiqh_certificate_enabled', 1);
        $min_completion = get_option('fiqh_min_completion_percentage', 80);

        ?>
        <div class="wrap fiqh-settings-page">
            <h1 class="wp-heading-inline">
                <span class="dashicons dashicons-admin-settings" style="font-size: 28px; vertical-align: middle;"></span>
                <?php _e('إعدادات FiqhLearning LMS', 'fiqh-lms'); ?>
            </h1>

            <hr class="wp-header-end">

            <style>
                .fiqh-settings-page .settings-nav {
                    background: #fff;
                    border: 1px solid #dcdcde;
                    border-bottom: none;
                    margin: 20px 0 0 0;
                }
                .fiqh-settings-page .settings-nav ul {
                    margin: 0;
                    padding: 0;
                    list-style: none;
                    display: flex;
                    gap: 0;
                }
                .fiqh-settings-page .settings-nav li {
                    margin: 0;
                }
                .fiqh-settings-page .settings-nav a {
                    display: block;
                    padding: 12px 20px;
                    text-decoration: none;
                    color: #50575e;
                    border-left: 1px solid #dcdcde;
                    transition: all 0.2s;
                }
                .fiqh-settings-page .settings-nav li:last-child a {
                    border-left: none;
                }
                .fiqh-settings-page .settings-nav a:hover {
                    background: #f6f7f7;
                }
                .fiqh-settings-page .settings-nav a.active {
                    background: #f6f7f7;
                    color: #2271b1;
                    font-weight: 600;
                }
                .fiqh-settings-page .settings-content {
                    background: #fff;
                    border: 1px solid #dcdcde;
                    padding: 30px;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                }
                .fiqh-settings-page .settings-section {
                    display: none;
                }
                .fiqh-settings-page .settings-section.active {
                    display: block;
                }
                .fiqh-settings-page .settings-section h2 {
                    margin-top: 0;
                    padding-bottom: 10px;
                    border-bottom: 2px solid #2271b1;
                }
                .fiqh-settings-page .form-table th {
                    width: 200px;
                }
                .fiqh-settings-page .description {
                    color: #646970;
                    font-size: 13px;
                    margin-top: 5px;
                }
            </style>

            <nav class="settings-nav">
                <ul>
                    <li><a href="#general" class="settings-tab active" data-tab="general">
                        <span class="dashicons dashicons-admin-generic" style="vertical-align: middle;"></span>
                        <?php _e('عام', 'fiqh-lms'); ?>
                    </a></li>
                    <li><a href="#email" class="settings-tab" data-tab="email">
                        <span class="dashicons dashicons-email" style="vertical-align: middle;"></span>
                        <?php _e('البريد الإلكتروني', 'fiqh-lms'); ?>
                    </a></li>
                    <li><a href="#courses" class="settings-tab" data-tab="courses">
                        <span class="dashicons dashicons-book" style="vertical-align: middle;"></span>
                        <?php _e('المقررات', 'fiqh-lms'); ?>
                    </a></li>
                </ul>
            </nav>

            <div class="settings-content">
                <form method="post" action="">
                    <?php wp_nonce_field('fiqh_settings_action', 'fiqh_settings_nonce'); ?>

                    <!-- الإعدادات العامة -->
                    <div id="general-section" class="settings-section active">
                        <h2><?php _e('الإعدادات العامة', 'fiqh-lms'); ?></h2>
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="fiqh_site_name"><?php _e('اسم المنصة', 'fiqh-lms'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="fiqh_site_name" name="fiqh_site_name" value="<?php echo esc_attr($site_name); ?>" class="regular-text">
                                    <p class="description"><?php _e('سيظهر في رسائل البريد الإلكتروني والإشعارات', 'fiqh-lms'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="fiqh_admin_email"><?php _e('البريد الإلكتروني للإدارة', 'fiqh-lms'); ?></label>
                                </th>
                                <td>
                                    <input type="email" id="fiqh_admin_email" name="fiqh_admin_email" value="<?php echo esc_attr($admin_email); ?>" class="regular-text">
                                    <p class="description"><?php _e('سيتم إرسال إشعارات النظام إلى هذا البريد', 'fiqh-lms'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('الإشعارات', 'fiqh-lms'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="fiqh_enable_notifications" value="1" <?php checked($enable_notifications, 1); ?>>
                                        <?php _e('تفعيل إشعارات البريد الإلكتروني', 'fiqh-lms'); ?>
                                    </label>
                                    <p class="description"><?php _e('إرسال إشعارات للطلاب عند التسجيل والأحداث المهمة', 'fiqh-lms'); ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- إعدادات البريد الإلكتروني -->
                    <div id="email-section" class="settings-section">
                        <h2><?php _e('إعدادات البريد الإلكتروني', 'fiqh-lms'); ?></h2>
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="fiqh_email_from_name"><?php _e('اسم المرسل', 'fiqh-lms'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="fiqh_email_from_name" name="fiqh_email_from_name" value="<?php echo esc_attr($email_from_name); ?>" class="regular-text">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="fiqh_email_from_address"><?php _e('بريد المرسل', 'fiqh-lms'); ?></label>
                                </th>
                                <td>
                                    <input type="email" id="fiqh_email_from_address" name="fiqh_email_from_address" value="<?php echo esc_attr($email_from_address); ?>" class="regular-text">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="fiqh_email_enrollment_subject"><?php _e('موضوع رسالة التسجيل', 'fiqh-lms'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="fiqh_email_enrollment_subject" name="fiqh_email_enrollment_subject" value="<?php echo esc_attr($email_enrollment_subject); ?>" class="large-text">
                                    <p class="description">
                                        <?php _e('المتغيرات المتاحة:', 'fiqh-lms'); ?>
                                        <code>{student_name}</code>, <code>{course_name}</code>, <code>{site_name}</code>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="fiqh_email_enrollment_body"><?php _e('نص رسالة التسجيل', 'fiqh-lms'); ?></label>
                                </th>
                                <td>
                                    <textarea id="fiqh_email_enrollment_body" name="fiqh_email_enrollment_body" rows="8" class="large-text"><?php echo esc_textarea($email_enrollment_body); ?></textarea>
                                    <p class="description">
                                        <?php _e('المتغيرات المتاحة:', 'fiqh-lms'); ?>
                                        <code>{student_name}</code>, <code>{course_name}</code>, <code>{site_name}</code>, <code>{dashboard_url}</code>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- إعدادات المقررات -->
                    <div id="courses-section" class="settings-section">
                        <h2><?php _e('إعدادات المقررات', 'fiqh-lms'); ?></h2>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php _e('التسجيل التلقائي', 'fiqh-lms'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="fiqh_auto_enroll" value="1" <?php checked($auto_enroll, 1); ?>>
                                        <?php _e('تفعيل التسجيل التلقائي للطلاب الجدد', 'fiqh-lms'); ?>
                                    </label>
                                    <p class="description"><?php _e('سيتم تسجيل جميع الطلاب الجدد تلقائياً في المقررات المتاحة', 'fiqh-lms'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('الشهادات', 'fiqh-lms'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="fiqh_certificate_enabled" value="1" <?php checked($certificate_enabled, 1); ?>>
                                        <?php _e('تفعيل شهادات إتمام المقررات', 'fiqh-lms'); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="fiqh_min_completion_percentage"><?php _e('نسبة الإتمام المطلوبة', 'fiqh-lms'); ?></label>
                                </th>
                                <td>
                                    <input type="number" id="fiqh_min_completion_percentage" name="fiqh_min_completion_percentage" value="<?php echo esc_attr($min_completion); ?>" min="0" max="100" step="5" class="small-text">
                                    <span>%</span>
                                    <p class="description"><?php _e('النسبة المئوية المطلوبة للحصول على الشهادة', 'fiqh-lms'); ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <p class="submit">
                        <button type="submit" name="fiqh_save_settings" class="button button-primary">
                            <span class="dashicons dashicons-yes" style="vertical-align: middle;"></span>
                            <?php _e('حفظ التغييرات', 'fiqh-lms'); ?>
                        </button>
                    </p>
                </form>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.settings-tab');
            const sections = document.querySelectorAll('.settings-section');

            tabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetTab = this.dataset.tab;

                    // إزالة الكلاس النشط من جميع التبويبات
                    tabs.forEach(t => t.classList.remove('active'));
                    sections.forEach(s => s.classList.remove('active'));

                    // إضافة الكلاس النشط للتبويب المحدد
                    this.classList.add('active');
                    document.getElementById(targetTab + '-section').classList.add('active');

                    // تحديث الـ URL
                    window.location.hash = targetTab;
                });
            });

            // التحقق من الـ hash في الـ URL
            if (window.location.hash) {
                const hash = window.location.hash.substring(1);
                const targetTab = document.querySelector(`[data-tab="${hash}"]`);
                if (targetTab) {
                    targetTab.click();
                }
            }
        });
        </script>
        <?php
    }

    /**
     * إخفاء الدروس من القائمة الجانبية
     */
    public function hide_post_types_from_menu() {
        // تم حذف fiqh_teacher من النظام (1.3)
        remove_menu_page('edit.php?post_type=fiqh_lesson');
    }

    /**
     * إضافة عداد الأسئلة غير المجابة
     */
    public function add_questions_count_badge() {
        global $menu, $wpdb;

        // حساب عدد الأسئلة غير المجابة
        $pending_count = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_questions WHERE status = 'pending'"
        );

        if ($pending_count > 0) {
            // البحث عن عنصر القائمة
            foreach ($menu as $key => $item) {
                if ($item[2] === 'edit.php?post_type=fiqh_course') {
                    // إضافة العداد إلى submenu الأسئلة
                    global $submenu;
                    if (isset($submenu['edit.php?post_type=fiqh_course'])) {
                        foreach ($submenu['edit.php?post_type=fiqh_course'] as $subkey => $subitem) {
                            if ($subitem[2] === 'fiqh-questions') {
                                $submenu['edit.php?post_type=fiqh_course'][$subkey][0] .= ' <span class="update-plugins count-' . $pending_count . '"><span class="plugin-count">' . $pending_count . '</span></span>';
                            }
                        }
                    }
                    break;
                }
            }
        }
    }
}
