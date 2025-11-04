<?php
/**
 * لوحة الإدارة الرئيسية
 *
 * @package FiqhLearning_LMS
 */

if (!defined('ABSPATH')) {
    exit;
}

class FiqhLearning_Admin {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->define_hooks();
    }

    private function define_hooks() {
        // إضافة Meta Boxes
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_course_meta'), 10, 2);
        add_action('save_post', array($this, 'save_lesson_meta'), 10, 2);

        // تحميل الأنماط والسكربتات في الإدارة
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    /**
     * إضافة Meta Boxes للمقررات والدروس
     */
    public function add_meta_boxes() {
        // Meta Box للمقرر
        add_meta_box(
            'fiqh_course_details',
            __('تفاصيل المقرر', 'fiqh-lms'),
            array($this, 'render_course_meta_box'),
            'fiqh_course',
            'normal',
            'high'
        );

        // Meta Box للدرس
        add_meta_box(
            'fiqh_lesson_details',
            __('تفاصيل الدرس', 'fiqh-lms'),
            array($this, 'render_lesson_meta_box'),
            'fiqh_lesson',
            'normal',
            'high'
        );
    }

    /**
     * عرض Meta Box تفاصيل المقرر
     */
    public function render_course_meta_box($post) {
        wp_nonce_field('fiqh_course_meta_nonce', 'fiqh_course_meta_nonce');

        $book_url = get_post_meta($post->ID, '_fiqh_course_book_url', true);
        $duration = get_post_meta($post->ID, '_fiqh_course_duration', true);

        ?>
        <div class="fiqh-meta-box">
            <p>
                <label for="fiqh_course_book_url"><strong><?php _e('رابط ملف الكتاب (PDF)', 'fiqh-lms'); ?></strong></label><br>
                <input type="url" id="fiqh_course_book_url" name="fiqh_course_book_url" value="<?php echo esc_attr($book_url); ?>" class="regular-text">
                <span class="description"><?php _e('رابط ملف PDF للكتاب الدراسي', 'fiqh-lms'); ?></span>
            </p>

            <p>
                <label for="fiqh_course_duration"><strong><?php _e('مدة المقرر', 'fiqh-lms'); ?></strong></label><br>
                <input type="text" id="fiqh_course_duration" name="fiqh_course_duration" value="<?php echo esc_attr($duration); ?>" class="regular-text">
                <span class="description"><?php _e('مثال: 12 أسبوع', 'fiqh-lms'); ?></span>
            </p>

            <hr style="margin: 20px 0;">

            <h3><?php _e('الطلاب المسجلون', 'fiqh-lms'); ?></h3>
            <p class="description"><?php _e('قم بتسجيل الطلاب في هذا المقرر من صفحة المقررات > التسجيل', 'fiqh-lms'); ?></p>

            <?php
            // عرض الطلاب المسجلين
            if ($post->ID) {
                $enrolled_students = FiqhLearning_Enrollments::get_course_students($post->ID);
                if ($enrolled_students) {
                    echo '<table class="wp-list-table widefat striped" style="margin-top: 15px;">';
                    echo '<thead><tr><th>' . __('الطالب', 'fiqh-lms') . '</th><th>' . __('تاريخ التسجيل', 'fiqh-lms') . '</th></tr></thead>';
                    echo '<tbody>';
                    foreach ($enrolled_students as $enrollment) {
                        $user = get_userdata($enrollment->user_id);
                        if ($user) {
                            echo '<tr><td>' . esc_html($user->display_name) . '</td><td>' . esc_html($enrollment->enrolled_at) . '</td></tr>';
                        }
                    }
                    echo '</tbody></table>';
                } else {
                    echo '<p>' . __('لا يوجد طلاب مسجلون في هذا المقرر بعد', 'fiqh-lms') . '</p>';
                }
            }
            ?>
        </div>
        <?php
    }

    /**
     * عرض Meta Box تفاصيل الدرس
     */
    public function render_lesson_meta_box($post) {
        wp_nonce_field('fiqh_lesson_meta_nonce', 'fiqh_lesson_meta_nonce');

        $course_id = get_post_meta($post->ID, '_fiqh_lesson_course_id', true);
        $video_url = get_post_meta($post->ID, '_fiqh_lesson_video_url', true);
        $audio_url = get_post_meta($post->ID, '_fiqh_lesson_audio_url', true);
        $pdf_url = get_post_meta($post->ID, '_fiqh_lesson_pdf_url', true);
        $duration = get_post_meta($post->ID, '_fiqh_lesson_duration', true);
        $order = get_post_meta($post->ID, '_fiqh_lesson_order', true);
        $exercises = get_post_meta($post->ID, '_fiqh_lesson_exercises', true);

        ?>
        <div class="fiqh-meta-box">
            <p>
                <label for="fiqh_lesson_course_id"><strong><?php _e('المقرر', 'fiqh-lms'); ?></strong></label><br>
                <select id="fiqh_lesson_course_id" name="fiqh_lesson_course_id" required>
                    <option value=""><?php _e('-- اختر المقرر --', 'fiqh-lms'); ?></option>
                    <?php
                    $courses = get_posts(array('post_type' => 'fiqh_course', 'posts_per_page' => -1));
                    foreach ($courses as $course) {
                        echo '<option value="' . $course->ID . '" ' . selected($course_id, $course->ID, false) . '>' . esc_html($course->post_title) . '</option>';
                    }
                    ?>
                </select>
            </p>

            <p>
                <label for="fiqh_lesson_video_url"><strong><?php _e('رابط الفيديو (YouTube)', 'fiqh-lms'); ?></strong></label><br>
                <input type="url" id="fiqh_lesson_video_url" name="fiqh_lesson_video_url" value="<?php echo esc_attr($video_url); ?>" class="regular-text">
                <span class="description"><?php _e('رابط فيديو YouTube', 'fiqh-lms'); ?></span>
            </p>

            <p>
                <label for="fiqh_lesson_audio_url"><strong><?php _e('رابط الملف الصوتي', 'fiqh-lms'); ?></strong></label><br>
                <input type="url" id="fiqh_lesson_audio_url" name="fiqh_lesson_audio_url" value="<?php echo esc_attr($audio_url); ?>" class="regular-text">
                <span class="description"><?php _e('رابط خارجي للملف الصوتي', 'fiqh-lms'); ?></span>
            </p>

            <p>
                <label for="fiqh_lesson_pdf_url"><strong><?php _e('رابط ملف PDF', 'fiqh-lms'); ?></strong></label><br>
                <input type="url" id="fiqh_lesson_pdf_url" name="fiqh_lesson_pdf_url" value="<?php echo esc_attr($pdf_url); ?>" class="regular-text">
                <span class="description"><?php _e('ملف PDF للملاحظات/المرفقات', 'fiqh-lms'); ?></span>
            </p>

            <p>
                <label for="fiqh_lesson_duration"><strong><?php _e('مدة الدرس', 'fiqh-lms'); ?></strong></label><br>
                <input type="text" id="fiqh_lesson_duration" name="fiqh_lesson_duration" value="<?php echo esc_attr($duration); ?>" class="regular-text">
                <span class="description"><?php _e('مثال: 45 دقيقة', 'fiqh-lms'); ?></span>
            </p>

            <p>
                <label for="fiqh_lesson_order"><strong><?php _e('ترتيب الدرس', 'fiqh-lms'); ?></strong></label><br>
                <input type="number" id="fiqh_lesson_order" name="fiqh_lesson_order" value="<?php echo esc_attr($order); ?>" min="1">
                <span class="description"><?php _e('ترتيب الدرس في المقرر', 'fiqh-lms'); ?></span>
            </p>

            <hr style="margin: 20px 0;">

            <p>
                <label for="fiqh_lesson_exercises"><strong><?php _e('التمارين', 'fiqh-lms'); ?></strong></label><br>
                <?php
                wp_editor($exercises, 'fiqh_lesson_exercises', array(
                    'textarea_name' => 'fiqh_lesson_exercises',
                    'textarea_rows' => 10,
                    'media_buttons' => false,
                    'teeny' => true,
                    'quicktags' => true,
                ));
                ?>
                <span class="description"><?php _e('أضف التمارين والتطبيقات للدرس', 'fiqh-lms'); ?></span>
            </p>
        </div>
        <?php
    }

    /**
     * حفظ بيانات المقرر
     */
    public function save_course_meta($post_id, $post) {
        if (!isset($_POST['fiqh_course_meta_nonce']) || !wp_verify_nonce($_POST['fiqh_course_meta_nonce'], 'fiqh_course_meta_nonce')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if ($post->post_type !== 'fiqh_course') {
            return;
        }

        if (isset($_POST['fiqh_course_book_url'])) {
            update_post_meta($post_id, '_fiqh_course_book_url', esc_url_raw($_POST['fiqh_course_book_url']));
        }

        if (isset($_POST['fiqh_course_duration'])) {
            update_post_meta($post_id, '_fiqh_course_duration', sanitize_text_field($_POST['fiqh_course_duration']));
        }
    }

    /**
     * حفظ بيانات الدرس
     */
    public function save_lesson_meta($post_id, $post) {
        if (!isset($_POST['fiqh_lesson_meta_nonce']) || !wp_verify_nonce($_POST['fiqh_lesson_meta_nonce'], 'fiqh_lesson_meta_nonce')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if ($post->post_type !== 'fiqh_lesson') {
            return;
        }

        if (isset($_POST['fiqh_lesson_course_id'])) {
            update_post_meta($post_id, '_fiqh_lesson_course_id', intval($_POST['fiqh_lesson_course_id']));
        }

        if (isset($_POST['fiqh_lesson_video_url'])) {
            update_post_meta($post_id, '_fiqh_lesson_video_url', esc_url_raw($_POST['fiqh_lesson_video_url']));
        }

        if (isset($_POST['fiqh_lesson_audio_url'])) {
            update_post_meta($post_id, '_fiqh_lesson_audio_url', esc_url_raw($_POST['fiqh_lesson_audio_url']));
        }

        if (isset($_POST['fiqh_lesson_pdf_url'])) {
            update_post_meta($post_id, '_fiqh_lesson_pdf_url', esc_url_raw($_POST['fiqh_lesson_pdf_url']));
        }

        if (isset($_POST['fiqh_lesson_duration'])) {
            update_post_meta($post_id, '_fiqh_lesson_duration', sanitize_text_field($_POST['fiqh_lesson_duration']));
        }

        if (isset($_POST['fiqh_lesson_order'])) {
            update_post_meta($post_id, '_fiqh_lesson_order', intval($_POST['fiqh_lesson_order']));
        }

        if (isset($_POST['fiqh_lesson_exercises'])) {
            update_post_meta($post_id, '_fiqh_lesson_exercises', wp_kses_post($_POST['fiqh_lesson_exercises']));
        }
    }

    /**
     * تحميل أنماط وسكربتات الإدارة
     */
    public function enqueue_admin_scripts($hook) {
        global $post_type;

        if (!in_array($post_type, array('fiqh_course', 'fiqh_lesson', 'fiqh_teacher'))) {
            return;
        }

        wp_enqueue_style('fiqh-admin', FIQH_LMS_PLUGIN_URL . 'admin/css/admin.css', array(), FIQH_LMS_VERSION);
        wp_enqueue_script('fiqh-admin', FIQH_LMS_PLUGIN_URL . 'admin/js/admin.js', array('jquery'), FIQH_LMS_VERSION, true);
    }
}
