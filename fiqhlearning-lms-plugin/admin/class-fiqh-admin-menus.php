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
        add_action('admin_init', array($this, 'handle_export_requests'));
    }

    /**
     * معالجة طلبات التصدير قبل أي output
     */
    public function handle_export_requests() {
        // التحقق من أننا في صفحة الاشتراكات
        if (!isset($_GET['page']) || $_GET['page'] !== 'fiqh-subscriptions') {
            return;
        }

        // معالجة تصدير الاشتراكات
        if (isset($_GET['export']) && $_GET['export'] === 'subscriptions' && isset($_GET['export_nonce']) && wp_verify_nonce($_GET['export_nonce'], 'export_subscriptions')) {
            $this->export_subscriptions_csv();
            exit;
        }

        // معالجة تصدير دفعات طالب
        if (isset($_GET['export']) && $_GET['export'] === 'payments' && isset($_GET['subscription_id']) && isset($_GET['export_nonce']) && wp_verify_nonce($_GET['export_nonce'], 'export_payments')) {
            $subscription_id = intval($_GET['subscription_id']);
            $this->export_payments_csv($subscription_id);
            exit;
        }

        // معالجة تصدير تقرير شامل
        if (isset($_GET['export']) && $_GET['export'] === 'full_report' && isset($_GET['export_nonce']) && wp_verify_nonce($_GET['export_nonce'], 'export_full_report')) {
            $this->export_full_report_csv();
            exit;
        }
    }

    /**
     * إضافة قوائم الإدارة
     */
    public function add_admin_menus() {
        // تم حذف قائمة التسجيل العام - النظام يعتمد على: العلوم ← المقررات ← المستويات ← الطلاب

        // أداة إصلاح التسجيلات
        add_submenu_page(
            'edit.php?post_type=fiqh_course',
            __('إصلاح التسجيلات', 'fiqh-lms'),
            __('🔧 إصلاح التسجيلات', 'fiqh-lms'),
            'manage_options',
            'fix-enrollments',
            array($this, 'fix_enrollments_page')
        );

        // أداة التشخيص الكامل
        add_submenu_page(
            'edit.php?post_type=fiqh_course',
            __('تشخيص النظام', 'fiqh-lms'),
            __('🔍 تشخيص النظام', 'fiqh-lms'),
            'manage_options',
            'diagnose-system',
            array($this, 'diagnose_system_page')
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

        // قائمة الاشتراكات
        add_submenu_page(
            'edit.php?post_type=fiqh_course',
            __('إدارة الاشتراكات', 'fiqh-lms'),
            __('الاشتراكات', 'fiqh-lms'),
            'manage_options',
            'fiqh-subscriptions',
            array($this, 'subscriptions_page')
        );

        // تم إزالة قائمة الإعدادات - الإعدادات متوفرة في WordPress Customizer
    }

    /**
     * صفحة إصلاح التسجيلات
     */
    public function fix_enrollments_page() {
        include_once plugin_dir_path(__FILE__) . 'fix-enrollments.php';
    }

    /**
     * صفحة التشخيص الكامل
     */
    public function diagnose_system_page() {
        include_once plugin_dir_path(__FILE__) . 'diagnose-system.php';
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

        // تم إزالة المقررات الأكثر تسجيلاً

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
     * صفحة إدارة الاشتراكات
     */
    public function subscriptions_page() {
        global $wpdb;

        // معالجة إضافة اشتراك جديد
        if (isset($_POST['add_subscription']) && check_admin_referer('add_subscription_action', 'subscription_nonce')) {
            $user_id = intval($_POST['user_id']);
            $monthly_amount = floatval($_POST['monthly_amount']);
            $start_date = sanitize_text_field($_POST['start_date']);
            $notes = sanitize_textarea_field($_POST['notes']);

            // التعرف التلقائي على مستوى الطالب
            $batch_id = $wpdb->get_var($wpdb->prepare(
                "SELECT batch_id FROM {$wpdb->prefix}fiqh_batch_students
                WHERE user_id = %d AND status = 'active'
                ORDER BY enrolled_at DESC LIMIT 1",
                $user_id
            ));

            $result = $wpdb->insert(
                $wpdb->prefix . 'fiqh_subscriptions',
                array(
                    'user_id' => $user_id,
                    'batch_id' => $batch_id,
                    'monthly_amount' => $monthly_amount,
                    'start_date' => $start_date,
                    'status' => 'active',
                    'notes' => $notes
                ),
                array('%d', '%d', '%f', '%s', '%s', '%s')
            );

            if ($result) {
                echo '<div class="notice notice-success is-dismissible"><p>' . __('تم إضافة الاشتراك بنجاح', 'fiqh-lms') . '</p></div>';
            }
        }

        // معالجة حذف اشتراك
        if (isset($_POST['delete_subscription']) && check_admin_referer('delete_subscription_action', 'delete_subscription_nonce')) {
            $subscription_id = intval($_POST['subscription_id']);

            $wpdb->delete(
                $wpdb->prefix . 'fiqh_subscriptions',
                array('id' => $subscription_id),
                array('%d')
            );

            // حذف الدفعات المرتبطة
            $wpdb->delete(
                $wpdb->prefix . 'fiqh_subscription_payments',
                array('subscription_id' => $subscription_id),
                array('%d')
            );

            echo '<div class="notice notice-success is-dismissible"><p>' . __('تم حذف الاشتراك بنجاح', 'fiqh-lms') . '</p></div>';
        }

        // معالجة تعديل اشتراك
        if (isset($_POST['edit_subscription']) && check_admin_referer('edit_subscription_action', 'edit_subscription_nonce')) {
            $subscription_id = intval($_POST['subscription_id']);
            $monthly_amount = floatval($_POST['monthly_amount']);
            $status = sanitize_text_field($_POST['status']);
            $notes = sanitize_textarea_field($_POST['notes']);

            $wpdb->update(
                $wpdb->prefix . 'fiqh_subscriptions',
                array(
                    'monthly_amount' => $monthly_amount,
                    'status' => $status,
                    'notes' => $notes
                ),
                array('id' => $subscription_id),
                array('%f', '%s', '%s'),
                array('%d')
            );

            echo '<div class="notice notice-success is-dismissible"><p>' . __('تم تعديل الاشتراك بنجاح', 'fiqh-lms') . '</p></div>';
        }

        // معالجة حذف دفعة
        if (isset($_POST['delete_payment']) && check_admin_referer('delete_payment_action', 'delete_payment_nonce')) {
            $payment_id = intval($_POST['payment_id']);

            $wpdb->delete(
                $wpdb->prefix . 'fiqh_subscription_payments',
                array('id' => $payment_id),
                array('%d')
            );

            echo '<div class="notice notice-success is-dismissible"><p>' . __('تم حذف الدفعة بنجاح', 'fiqh-lms') . '</p></div>';
        }

        // معالجة تسجيل دفعة
        if (isset($_POST['add_payment']) && check_admin_referer('add_payment_action', 'payment_nonce')) {
            $subscription_id = intval($_POST['subscription_id']);
            $amount = floatval($_POST['payment_amount']);
            $payment_month = sanitize_text_field($_POST['payment_month']);
            $payment_date = sanitize_text_field($_POST['payment_date']);
            $payment_method = sanitize_text_field($_POST['payment_method']);
            $payment_notes = sanitize_textarea_field($_POST['payment_notes']);

            // الحصول على معلومات الاشتراك
            $subscription = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}fiqh_subscriptions WHERE id = %d",
                $subscription_id
            ));

            if ($subscription) {
                $result = $wpdb->insert(
                    $wpdb->prefix . 'fiqh_subscription_payments',
                    array(
                        'subscription_id' => $subscription_id,
                        'user_id' => $subscription->user_id,
                        'amount' => $amount,
                        'payment_month' => $payment_month,
                        'payment_date' => $payment_date,
                        'payment_method' => $payment_method,
                        'status' => 'paid',
                        'notes' => $payment_notes,
                        'created_by' => get_current_user_id()
                    ),
                    array('%d', '%d', '%f', '%s', '%s', '%s', '%s', '%s', '%d')
                );

                if ($result) {
                    echo '<div class="notice notice-success is-dismissible"><p>' . __('تم تسجيل الدفعة بنجاح', 'fiqh-lms') . '</p></div>';
                }
            }
        }

        // معالجة تعديل حالة اشتراك
        if (isset($_POST['update_subscription_status']) && check_admin_referer('update_status_action', 'status_nonce')) {
            $subscription_id = intval($_POST['subscription_id']);
            $new_status = sanitize_text_field($_POST['new_status']);

            $wpdb->update(
                $wpdb->prefix . 'fiqh_subscriptions',
                array('status' => $new_status),
                array('id' => $subscription_id),
                array('%s'),
                array('%d')
            );

            echo '<div class="notice notice-success is-dismissible"><p>' . __('تم تحديث حالة الاشتراك', 'fiqh-lms') . '</p></div>';
        }

        // جلب قائمة الطلاب
        $students = get_users(array('role' => 'student', 'orderby' => 'display_name'));

        // فلتر الشهر
        $selected_month = isset($_GET['filter_month']) ? sanitize_text_field($_GET['filter_month']) : date('Y-m');

        // جلب الاشتراكات مع تفاصيل الطالب
        $subscriptions = $wpdb->get_results(
            "SELECT
                s.*,
                u.display_name as student_name,
                u.user_email,
                b.name as batch_name,
                (SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_subscription_payments WHERE subscription_id = s.id) as total_payments,
                (SELECT SUM(amount) FROM {$wpdb->prefix}fiqh_subscription_payments WHERE subscription_id = s.id) as total_paid,
                (SELECT MAX(payment_month) FROM {$wpdb->prefix}fiqh_subscription_payments WHERE subscription_id = s.id) as last_payment_month
            FROM {$wpdb->prefix}fiqh_subscriptions s
            LEFT JOIN {$wpdb->users} u ON s.user_id = u.ID
            LEFT JOIN {$wpdb->prefix}fiqh_batches b ON s.batch_id = b.id
            WHERE s.status = 'active'
            ORDER BY u.display_name"
        );

        ?>
        <div class="wrap fiqh-subscriptions-page">
            <h1 class="wp-heading-inline">
                <span class="dashicons dashicons-money-alt" style="font-size: 28px; vertical-align: middle;"></span>
                <?php _e('إدارة الاشتراكات', 'fiqh-lms'); ?>
            </h1>
            <a href="#" class="page-title-action" id="add-subscription-btn">
                <?php _e('+ إضافة اشتراك جديد', 'fiqh-lms'); ?>
            </a>
            <a href="<?php echo wp_nonce_url(admin_url('edit.php?post_type=fiqh_course&page=fiqh-subscriptions&export=subscriptions'), 'export_subscriptions', 'export_nonce'); ?>" class="page-title-action">
                <span class="dashicons dashicons-download" style="vertical-align: middle;"></span>
                <?php _e('تصدير الاشتراكات', 'fiqh-lms'); ?>
            </a>
            <a href="<?php echo wp_nonce_url(admin_url('edit.php?post_type=fiqh_course&page=fiqh-subscriptions&export=full_report'), 'export_full_report', 'export_nonce'); ?>" class="page-title-action">
                <span class="dashicons dashicons-media-spreadsheet" style="vertical-align: middle;"></span>
                <?php _e('تصدير تقرير شامل', 'fiqh-lms'); ?>
            </a>

            <hr class="wp-header-end">

            <!-- فلتر الشهر -->
            <div style="margin: 15px 0; padding: 10px; background: #fff; border: 1px solid #dcdcde; border-radius: 4px;">
                <form method="get" style="display: inline-flex; align-items: center; gap: 10px;">
                    <input type="hidden" name="post_type" value="fiqh_course">
                    <input type="hidden" name="page" value="fiqh-subscriptions">
                    <label for="filter_month" style="font-weight: 600;">
                        <?php _e('عرض دفعات شهر:', 'fiqh-lms'); ?>
                    </label>
                    <input type="month" name="filter_month" id="filter_month" value="<?php echo esc_attr($selected_month); ?>" style="padding: 5px 10px;">
                    <button type="submit" class="button"><?php _e('تطبيق', 'fiqh-lms'); ?></button>
                    <a href="?post_type=fiqh_course&page=fiqh-subscriptions" class="button"><?php _e('إعادة تعيين', 'fiqh-lms'); ?></a>
                </form>
            </div>

            <style>
                .fiqh-subscriptions-page .subscriptions-stats {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 20px;
                    margin: 20px 0;
                }
                .fiqh-subscriptions-page .stat-card {
                    background: #fff;
                    padding: 20px;
                    border-radius: 8px;
                    border: 1px solid #dcdcde;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                }
                .fiqh-subscriptions-page .stat-value {
                    font-size: 32px;
                    font-weight: 700;
                    color: #2271b1;
                }
                .fiqh-subscriptions-page .stat-label {
                    color: #646970;
                    font-size: 14px;
                }
                .subscription-modal {
                    display: none;
                    position: fixed;
                    z-index: 100000;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0,0,0,0.5);
                }
                .subscription-modal-content {
                    background-color: #fefefe;
                    margin: 5% auto;
                    padding: 30px;
                    border: 1px solid #888;
                    width: 80%;
                    max-width: 600px;
                    border-radius: 8px;
                }
                .subscription-modal-close {
                    color: #aaa;
                    float: left;
                    font-size: 28px;
                    font-weight: bold;
                    cursor: pointer;
                }
                .subscription-modal-close:hover {
                    color: #000;
                }
                .status-badge {
                    display: inline-block;
                    padding: 4px 12px;
                    border-radius: 12px;
                    font-size: 12px;
                    font-weight: 600;
                }
                .status-active {
                    background: #d4edda;
                    color: #155724;
                }
                .status-suspended {
                    background: #fff3cd;
                    color: #856404;
                }
                .status-expired {
                    background: #f8d7da;
                    color: #721c24;
                }
            </style>

            <!-- إحصائيات سريعة -->
            <?php
            $total_active = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_subscriptions WHERE status = 'active'");
            $month_revenue = $wpdb->get_var($wpdb->prepare(
                "SELECT SUM(amount) FROM {$wpdb->prefix}fiqh_subscription_payments WHERE payment_month = %s",
                $selected_month
            ));

            // حساب المتأخرين تلقائياً: الطلاب الذين لم يدفعوا عن الشهر المختار
            $pending_count = 0;
            foreach ($subscriptions as $sub) {
                $paid_for_month = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_subscription_payments
                    WHERE subscription_id = %d AND payment_month = %s",
                    $sub->id, $selected_month
                ));
                if ($paid_for_month == 0) {
                    $pending_count++;
                }
            }
            ?>
            <div class="subscriptions-stats">
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format_i18n($total_active); ?></div>
                    <div class="stat-label"><?php _e('الاشتراكات النشطة', 'fiqh-lms'); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($month_revenue ? $month_revenue : 0, 2); ?> <?php _e('د.م', 'fiqh-lms'); ?></div>
                    <div class="stat-label"><?php echo sprintf(__('إيرادات %s', 'fiqh-lms'), $selected_month); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" style="color: #d63638;"><?php echo number_format_i18n($pending_count); ?></div>
                    <div class="stat-label"><?php echo sprintf(__('متأخرون عن %s', 'fiqh-lms'), $selected_month); ?></div>
                </div>
            </div>

            <!-- جدول الاشتراكات -->
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('الطالب', 'fiqh-lms'); ?></th>
                        <th><?php _e('المستوى', 'fiqh-lms'); ?></th>
                        <th><?php _e('الاشتراك الشهري', 'fiqh-lms'); ?></th>
                        <th><?php echo sprintf(__('حالة %s', 'fiqh-lms'), $selected_month); ?></th>
                        <th><?php _e('إجراءات', 'fiqh-lms'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($subscriptions): ?>
                        <?php foreach ($subscriptions as $sub):
                            // التحقق من دفع الشهر المختار
                            $paid_for_month = $wpdb->get_var($wpdb->prepare(
                                "SELECT amount FROM {$wpdb->prefix}fiqh_subscription_payments
                                WHERE subscription_id = %d AND payment_month = %s LIMIT 1",
                                $sub->id, $selected_month
                            ));
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo esc_html($sub->student_name); ?></strong><br>
                                <small><?php echo esc_html($sub->user_email); ?></small>
                            </td>
                            <td><?php echo $sub->batch_name ? esc_html($sub->batch_name) : '<span style="color: #999;">تلقائي</span>'; ?></td>
                            <td><strong><?php echo number_format($sub->monthly_amount, 2); ?></strong> <?php _e('د.م', 'fiqh-lms'); ?></td>
                            <td>
                                <?php if ($paid_for_month): ?>
                                    <span class="status-badge status-active">
                                        ✓ <?php _e('مدفوع', 'fiqh-lms'); ?> (<?php echo number_format($paid_for_month, 2); ?> د.م)
                                    </span>
                                <?php else: ?>
                                    <span class="status-badge status-expired">
                                        ✗ <?php _e('لم يدفع', 'fiqh-lms'); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="button button-small add-payment-btn" data-subscription-id="<?php echo $sub->id; ?>" data-student-name="<?php echo esc_attr($sub->student_name); ?>" data-amount="<?php echo $sub->monthly_amount; ?>">
                                    <?php _e('تسجيل دفعة', 'fiqh-lms'); ?>
                                </button>
                                <button type="button" class="button button-small edit-subscription-btn" data-subscription-id="<?php echo $sub->id; ?>" data-amount="<?php echo $sub->monthly_amount; ?>" data-notes="<?php echo esc_attr($sub->notes); ?>">
                                    <?php _e('تعديل', 'fiqh-lms'); ?>
                                </button>
                                <button type="button" class="button button-small button-link-delete delete-subscription-btn" data-subscription-id="<?php echo $sub->id; ?>" data-student-name="<?php echo esc_attr($sub->student_name); ?>">
                                    <?php _e('حذف', 'fiqh-lms'); ?>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5"><?php _e('لا توجد اشتراكات نشطة حالياً', 'fiqh-lms'); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Modal إضافة اشتراك -->
            <div id="add-subscription-modal" class="subscription-modal">
                <div class="subscription-modal-content">
                    <span class="subscription-modal-close">&times;</span>
                    <h2><?php _e('إضافة اشتراك جديد', 'fiqh-lms'); ?></h2>
                    <form method="post" action="">
                        <?php wp_nonce_field('add_subscription_action', 'subscription_nonce'); ?>

                        <table class="form-table">
                            <tr>
                                <th><label for="user_id"><?php _e('الطالب', 'fiqh-lms'); ?> *</label></th>
                                <td>
                                    <select name="user_id" id="user_id" required style="width: 100%;">
                                        <option value=""><?php _e('-- اختر طالباً --', 'fiqh-lms'); ?></option>
                                        <?php foreach ($students as $student): ?>
                                            <option value="<?php echo $student->ID; ?>">
                                                <?php echo esc_html($student->display_name); ?> (<?php echo esc_html($student->user_email); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <p class="description"><?php _e('المستوى الدراسي سيتم تحديده تلقائياً من مستوى الطالب النشط', 'fiqh-lms'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="monthly_amount"><?php _e('قيمة الاشتراك الشهري', 'fiqh-lms'); ?> *</label></th>
                                <td>
                                    <input type="number" name="monthly_amount" id="monthly_amount" step="0.01" min="0" required style="width: 200px;">
                                    <span><?php _e('د.م', 'fiqh-lms'); ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="start_date"><?php _e('تاريخ البداية', 'fiqh-lms'); ?> *</label></th>
                                <td><input type="date" name="start_date" id="start_date" required value="<?php echo date('Y-m-d'); ?>"></td>
                            </tr>
                            <tr>
                                <th><label for="notes"><?php _e('ملاحظات', 'fiqh-lms'); ?></label></th>
                                <td><textarea name="notes" id="notes" rows="3" style="width: 100%;"></textarea></td>
                            </tr>
                        </table>

                        <p class="submit">
                            <button type="submit" name="add_subscription" class="button button-primary">
                                <?php _e('إضافة الاشتراك', 'fiqh-lms'); ?>
                            </button>
                        </p>
                    </form>
                </div>
            </div>

            <!-- Modal تسجيل دفعة -->
            <div id="add-payment-modal" class="subscription-modal">
                <div class="subscription-modal-content">
                    <span class="subscription-modal-close">&times;</span>
                    <h2><?php _e('تسجيل دفعة شهرية', 'fiqh-lms'); ?></h2>
                    <p id="payment-student-name" style="font-weight: 600; color: #2271b1;"></p>
                    <form method="post" action="">
                        <?php wp_nonce_field('add_payment_action', 'payment_nonce'); ?>
                        <input type="hidden" name="subscription_id" id="payment_subscription_id">

                        <table class="form-table">
                            <tr>
                                <th><label for="payment_amount"><?php _e('المبلغ المدفوع', 'fiqh-lms'); ?> *</label></th>
                                <td>
                                    <input type="number" name="payment_amount" id="payment_amount" step="0.01" min="0" required style="width: 200px;">
                                    <span><?php _e('د.م', 'fiqh-lms'); ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="payment_month"><?php _e('الشهر', 'fiqh-lms'); ?> *</label></th>
                                <td><input type="month" name="payment_month" id="payment_month" required value="<?php echo date('Y-m'); ?>"></td>
                            </tr>
                            <tr>
                                <th><label for="payment_date"><?php _e('تاريخ الدفع', 'fiqh-lms'); ?> *</label></th>
                                <td><input type="date" name="payment_date" id="payment_date" required value="<?php echo date('Y-m-d'); ?>"></td>
                            </tr>
                            <tr>
                                <th><label for="payment_method"><?php _e('طريقة الدفع', 'fiqh-lms'); ?></label></th>
                                <td>
                                    <select name="payment_method" id="payment_method" style="width: 200px;">
                                        <option value="cash"><?php _e('نقداً', 'fiqh-lms'); ?></option>
                                        <option value="bank_transfer"><?php _e('تحويل بنكي', 'fiqh-lms'); ?></option>
                                        <option value="check"><?php _e('شيك', 'fiqh-lms'); ?></option>
                                        <option value="online"><?php _e('دفع إلكتروني', 'fiqh-lms'); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="payment_notes"><?php _e('ملاحظات', 'fiqh-lms'); ?></label></th>
                                <td><textarea name="payment_notes" id="payment_notes" rows="2" style="width: 100%;"></textarea></td>
                            </tr>
                        </table>

                        <p class="submit">
                            <button type="submit" name="add_payment" class="button button-primary">
                                <?php _e('تسجيل الدفعة', 'fiqh-lms'); ?>
                            </button>
                        </p>
                    </form>
                </div>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            // فتح modal إضافة اشتراك
            $('#add-subscription-btn').on('click', function(e) {
                e.preventDefault();
                $('#add-subscription-modal').show();
            });

            // فتح modal تسجيل دفعة
            $('.add-payment-btn').on('click', function() {
                var subscriptionId = $(this).data('subscription-id');
                var studentName = $(this).data('student-name');
                var amount = $(this).data('amount');

                $('#payment_subscription_id').val(subscriptionId);
                $('#payment_amount').val(amount);
                $('#payment-student-name').text('<?php _e('الطالب:', 'fiqh-lms'); ?> ' + studentName);
                $('#add-payment-modal').show();
            });

            // إغلاق modals
            $('.subscription-modal-close').on('click', function() {
                $(this).closest('.subscription-modal').hide();
            });

            // إغلاق عند النقر خارج المحتوى
            $('.subscription-modal').on('click', function(e) {
                if (e.target === this) {
                    $(this).hide();
                }
            });
        });
        </script>
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
     * تصدير الاشتراكات إلى CSV
     */
    private function export_subscriptions_csv() {
        global $wpdb;

        // جلب البيانات
        $subscriptions = $wpdb->get_results(
            "SELECT
                s.*,
                u.display_name as student_name,
                u.user_email,
                b.name as batch_name,
                (SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_subscription_payments WHERE subscription_id = s.id) as total_payments,
                (SELECT SUM(amount) FROM {$wpdb->prefix}fiqh_subscription_payments WHERE subscription_id = s.id) as total_paid
            FROM {$wpdb->prefix}fiqh_subscriptions s
            LEFT JOIN {$wpdb->users} u ON s.user_id = u.ID
            LEFT JOIN {$wpdb->prefix}fiqh_batches b ON s.batch_id = b.id
            ORDER BY s.created_at DESC"
        );

        // تحديد headers للتحميل
        $filename = 'subscriptions-' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Pragma: no-cache');
        header('Expires: 0');

        // فتح output stream
        $output = fopen('php://output', 'w');

        // إضافة BOM لدعم UTF-8 في Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // كتابة الرؤوس
        fputcsv($output, array(
            'الطالب',
            'البريد الإلكتروني',
            'المستوى الدراسي',
            'الاشتراك الشهري',
            'تاريخ البداية',
            'تاريخ النهاية',
            'عدد الدفعات',
            'المجموع المدفوع',
            'الحالة',
            'ملاحظات'
        ));

        // كتابة البيانات
        foreach ($subscriptions as $sub) {
            $status_text = array(
                'active' => 'نشط',
                'suspended' => 'معلق',
                'expired' => 'منتهي'
            );

            fputcsv($output, array(
                $sub->student_name,
                $sub->user_email,
                $sub->batch_name ? $sub->batch_name : '-',
                number_format($sub->monthly_amount, 2) . ' د.م',
                $sub->start_date,
                $sub->end_date ? $sub->end_date : '-',
                $sub->total_payments,
                number_format($sub->total_paid, 2) . ' د.م',
                $status_text[$sub->status],
                $sub->notes
            ));
        }

        fclose($output);
    }

    /**
     * تصدير دفعات اشتراك معين إلى CSV
     */
    private function export_payments_csv($subscription_id) {
        global $wpdb;

        // جلب معلومات الاشتراك
        $subscription = $wpdb->get_row($wpdb->prepare(
            "SELECT s.*, u.display_name as student_name
            FROM {$wpdb->prefix}fiqh_subscriptions s
            LEFT JOIN {$wpdb->users} u ON s.user_id = u.ID
            WHERE s.id = %d",
            $subscription_id
        ));

        if (!$subscription) {
            wp_die(__('الاشتراك غير موجود', 'fiqh-lms'));
        }

        // جلب الدفعات
        $payments = $wpdb->get_results($wpdb->prepare(
            "SELECT
                p.*,
                u.display_name as created_by_name
            FROM {$wpdb->prefix}fiqh_subscription_payments p
            LEFT JOIN {$wpdb->users} u ON p.created_by = u.ID
            WHERE p.subscription_id = %d
            ORDER BY p.payment_date DESC",
            $subscription_id
        ));

        // تحديد headers للتحميل
        $filename = 'payments-' . sanitize_title($subscription->student_name) . '-' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Pragma: no-cache');
        header('Expires: 0');

        // فتح output stream
        $output = fopen('php://output', 'w');

        // إضافة BOM لدعم UTF-8 في Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // معلومات الاشتراك في الأعلى
        fputcsv($output, array('معلومات الاشتراك'));
        fputcsv($output, array('الطالب:', $subscription->student_name));
        fputcsv($output, array('الاشتراك الشهري:', number_format($subscription->monthly_amount, 2) . ' د.م'));
        fputcsv($output, array('تاريخ البداية:', $subscription->start_date));
        fputcsv($output, array(''));

        // كتابة الرؤوس
        fputcsv($output, array(
            'الشهر',
            'المبلغ',
            'تاريخ الدفع',
            'طريقة الدفع',
            'الحالة',
            'سجّلها',
            'ملاحظات'
        ));

        // كتابة البيانات
        $payment_methods = array(
            'cash' => 'نقداً',
            'bank_transfer' => 'تحويل بنكي',
            'check' => 'شيك',
            'online' => 'دفع إلكتروني'
        );

        foreach ($payments as $payment) {
            fputcsv($output, array(
                $payment->payment_month,
                number_format($payment->amount, 2) . ' د.م',
                $payment->payment_date,
                isset($payment_methods[$payment->payment_method]) ? $payment_methods[$payment->payment_method] : $payment->payment_method,
                $payment->status === 'paid' ? 'مدفوع' : 'معلق',
                $payment->created_by_name,
                $payment->notes
            ));
        }

        fclose($output);
    }

    /**
     * تصدير تقرير شامل (اشتراكات + دفعات) إلى CSV
     */
    private function export_full_report_csv() {
        global $wpdb;

        // جلب جميع الاشتراكات مع دفعاتها
        $subscriptions = $wpdb->get_results(
            "SELECT
                s.*,
                u.display_name as student_name,
                u.user_email,
                b.name as batch_name
            FROM {$wpdb->prefix}fiqh_subscriptions s
            LEFT JOIN {$wpdb->users} u ON s.user_id = u.ID
            LEFT JOIN {$wpdb->prefix}fiqh_batches b ON s.batch_id = b.id
            ORDER BY u.display_name"
        );

        // تحديد headers للتحميل
        $filename = 'full-report-' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Pragma: no-cache');
        header('Expires: 0');

        // فتح output stream
        $output = fopen('php://output', 'w');

        // إضافة BOM لدعم UTF-8 في Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // عنوان التقرير
        fputcsv($output, array('تقرير الاشتراكات والدفعات الشامل'));
        fputcsv($output, array('تاريخ التقرير: ' . date('Y-m-d H:i:s')));
        fputcsv($output, array(''));

        $payment_methods = array(
            'cash' => 'نقداً',
            'bank_transfer' => 'تحويل بنكي',
            'check' => 'شيك',
            'online' => 'دفع إلكتروني'
        );

        // لكل اشتراك
        foreach ($subscriptions as $sub) {
            // معلومات الطالب
            fputcsv($output, array('===== ' . $sub->student_name . ' ====='));
            fputcsv($output, array('البريد الإلكتروني:', $sub->user_email));
            fputcsv($output, array('المستوى:', $sub->batch_name ? $sub->batch_name : '-'));
            fputcsv($output, array('الاشتراك الشهري:', number_format($sub->monthly_amount, 2) . ' د.م'));
            fputcsv($output, array('تاريخ البداية:', $sub->start_date));
            fputcsv($output, array(''));

            // جلب دفعات هذا الاشتراك
            $payments = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}fiqh_subscription_payments
                WHERE subscription_id = %d
                ORDER BY payment_date DESC",
                $sub->id
            ));

            if ($payments) {
                fputcsv($output, array('الشهر', 'المبلغ', 'تاريخ الدفع', 'طريقة الدفع', 'الحالة'));
                $total = 0;
                foreach ($payments as $payment) {
                    fputcsv($output, array(
                        $payment->payment_month,
                        number_format($payment->amount, 2) . ' د.م',
                        $payment->payment_date,
                        isset($payment_methods[$payment->payment_method]) ? $payment_methods[$payment->payment_method] : $payment->payment_method,
                        $payment->status === 'paid' ? 'مدفوع' : 'معلق'
                    ));
                    $total += $payment->amount;
                }
                fputcsv($output, array('المجموع:', number_format($total, 2) . ' د.م'));
            } else {
                fputcsv($output, array('لا توجد دفعات مسجلة'));
            }

            fputcsv($output, array(''));
            fputcsv($output, array(''));
        }

        // إحصائيات عامة في النهاية
        $total_active = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_subscriptions WHERE status = 'active'");
        $total_revenue = $wpdb->get_var("SELECT SUM(amount) FROM {$wpdb->prefix}fiqh_subscription_payments");
        $monthly_revenue = $wpdb->get_var("SELECT SUM(amount) FROM {$wpdb->prefix}fiqh_subscription_payments WHERE MONTH(payment_date) = MONTH(CURRENT_DATE()) AND YEAR(payment_date) = YEAR(CURRENT_DATE())");

        fputcsv($output, array('===== إحصائيات عامة ====='));
        fputcsv($output, array('إجمالي الاشتراكات النشطة:', $total_active));
        fputcsv($output, array('إجمالي الإيرادات:', number_format($total_revenue, 2) . ' د.م'));
        fputcsv($output, array('إيرادات هذا الشهر:', number_format($monthly_revenue, 2) . ' د.م'));

        fclose($output);
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
