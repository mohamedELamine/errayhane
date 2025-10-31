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
        $level = get_post_meta($post->ID, '_fiqh_course_level', true);
        $teacher_id = get_post_meta($post->ID, '_fiqh_course_teacher_id', true);

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

            <p>
                <label for="fiqh_course_level"><strong><?php _e('مستوى المقرر', 'fiqh-lms'); ?></strong></label><br>
                <select id="fiqh_course_level" name="fiqh_course_level">
                    <option value="beginner" <?php selected($level, 'beginner'); ?>><?php _e('مبتدئ', 'fiqh-lms'); ?></option>
                    <option value="intermediate" <?php selected($level, 'intermediate'); ?>><?php _e('متوسط', 'fiqh-lms'); ?></option>
                    <option value="advanced" <?php selected($level, 'advanced'); ?>><?php _e('متقدم', 'fiqh-lms'); ?></option>
                </select>
            </p>

            <p>
                <label for="fiqh_course_teacher_id"><strong><?php _e('المعلم', 'fiqh-lms'); ?></strong></label><br>
                <select id="fiqh_course_teacher_id" name="fiqh_course_teacher_id">
                    <option value=""><?php _e('-- اختر المعلم --', 'fiqh-lms'); ?></option>
                    <?php
                    $teachers = get_posts(array('post_type' => 'fiqh_teacher', 'posts_per_page' => -1));
                    foreach ($teachers as $teacher) {
                        echo '<option value="' . $teacher->ID . '" ' . selected($teacher_id, $teacher->ID, false) . '>' . esc_html($teacher->post_title) . '</option>';
                    }
                    ?>
                </select>
            </p>
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

        if (isset($_POST['fiqh_course_level'])) {
            update_post_meta($post_id, '_fiqh_course_level', sanitize_text_field($_POST['fiqh_course_level']));
        }

        if (isset($_POST['fiqh_course_teacher_id'])) {
            update_post_meta($post_id, '_fiqh_course_teacher_id', intval($_POST['fiqh_course_teacher_id']));
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
