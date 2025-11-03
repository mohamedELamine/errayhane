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

        // معالجة الإجابة على سؤال
        if (isset($_POST['answer_question']) && check_admin_referer('answer_question_action', 'answer_question_nonce')) {
            $question_id = intval($_POST['question_id']);
            $answer_text = sanitize_textarea_field($_POST['answer_text']);

            if ($question_id && $answer_text) {
                $updated = $wpdb->update(
                    $wpdb->prefix . 'fiqh_questions',
                    array(
                        'answer_text' => $answer_text,
                        'answered_by' => get_current_user_id(),
                        'answered_at' => current_time('mysql'),
                        'status' => 'answered'
                    ),
                    array('id' => $question_id),
                    array('%s', '%d', '%s', '%s'),
                    array('%d')
                );

                if ($updated) {
                    echo '<div class="notice notice-success is-dismissible"><p>' . __('تم الإجابة على السؤال بنجاح', 'fiqh-lms') . '</p></div>';
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

                                <?php if ($question->status === 'answered' && (isset($question->answer) || isset($question->answer_text))) : ?>
                                    <h3><?php _e('الإجابة:', 'fiqh-lms'); ?></h3>
                                    <div class="question-full-text" style="border-right-color: #00a32a;">
                                        <?php echo isset($question->answer) ? nl2br(esc_html($question->answer)) : nl2br(esc_html($question->answer_text)); ?>
                                    </div>
                                    <p><em><?php _e('أجاب في:', 'fiqh-lms'); ?> <?php echo isset($question->answered_at) && $question->answered_at ? esc_html($question->answered_at) : '-'; ?></em></p>
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

    /**
     * إخفاء المعلمين والدروس من القائمة الجانبية
     */
    public function hide_post_types_from_menu() {
        remove_menu_page('edit.php?post_type=fiqh_teacher');
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
