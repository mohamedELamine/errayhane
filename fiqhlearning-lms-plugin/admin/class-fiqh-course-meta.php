<?php
/**
 * إدارة Meta Boxes للمقررات
 *
 * @package FiqhLearning_LMS
 */

if (!defined('ABSPATH')) {
    exit;
}

class FiqhLearning_Course_Meta {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_fiqh_course', array($this, 'save_course_meta'), 10, 2);
    }

    /**
     * إضافة Meta Boxes
     */
    public function add_meta_boxes() {
        // معلومات المقرر الأساسية
        add_meta_box(
            'fiqh_course_details',
            __('معلومات المقرر', 'fiqh-lms'),
            array($this, 'render_course_details_metabox'),
            'fiqh_course',
            'normal',
            'high'
        );

        // ربط المقرر بالمستوى
        add_meta_box(
            'fiqh_course_level',
            __('المستوى الدراسي', 'fiqh-lms'),
            array($this, 'render_course_level_metabox'),
            'fiqh_course',
            'side',
            'default'
        );
    }

    /**
     * Meta Box: معلومات المقرر
     */
    public function render_course_details_metabox($post) {
        wp_nonce_field('fiqh_course_meta_nonce', 'fiqh_course_meta_nonce');

        $book_url = get_post_meta($post->ID, '_fiqh_course_book_url', true);
        $duration = get_post_meta($post->ID, '_fiqh_course_duration', true);
        ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="fiqh_course_book_url"><?php _e('رابط الكتاب (PDF)', 'fiqh-lms'); ?></label>
                </th>
                <td>
                    <input type="url" id="fiqh_course_book_url" name="fiqh_course_book_url" value="<?php echo esc_url($book_url); ?>" class="large-text" placeholder="https://example.com/book.pdf">
                    <p class="description"><?php _e('رابط مباشر لملف PDF الخاص بكتاب المقرر', 'fiqh-lms'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="fiqh_course_duration"><?php _e('مدة المقرر', 'fiqh-lms'); ?></label>
                </th>
                <td>
                    <input type="text" id="fiqh_course_duration" name="fiqh_course_duration" value="<?php echo esc_attr($duration); ?>" class="regular-text" placeholder="12 أسبوع">
                    <p class="description"><?php _e('مثال: 12 أسبوع، 3 أشهر، فصل دراسي واحد', 'fiqh-lms'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Meta Box: المستوى الدراسي
     */
    public function render_course_level_metabox($post) {
        global $wpdb;

        $selected_levels = get_post_meta($post->ID, '_fiqh_course_levels', true);
        if (!is_array($selected_levels)) {
            $selected_levels = !empty($selected_levels) ? array($selected_levels) : array();
        }

        // جلب جميع المستويات
        $levels = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}fiqh_batches WHERE status = 'active' ORDER BY name ASC");

        ?>
        <div class="fiqh-levels-select">
            <p><strong><?php _e('اختر المستويات التي ينتمي إليها هذا المقرر:', 'fiqh-lms'); ?></strong></p>
            <?php if ($levels && count($levels) > 0) : ?>
                <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #f9f9f9;">
                    <?php foreach ($levels as $level) : ?>
                        <label style="display: block; margin-bottom: 8px;">
                            <input type="checkbox" name="fiqh_course_levels[]" value="<?php echo esc_attr($level->id); ?>" <?php checked(in_array($level->id, $selected_levels)); ?>>
                            <strong><?php echo esc_html($level->name); ?></strong>
                            <?php if ($level->start_date && $level->end_date) : ?>
                                <br><span style="font-size: 11px; color: #666;">
                                    <?php echo esc_html($level->start_date . ' - ' . $level->end_date); ?>
                                </span>
                            <?php endif; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <p class="description" style="margin-top: 10px;">
                    <?php _e('يمكنك اختيار مستوى واحد أو أكثر', 'fiqh-lms'); ?>
                </p>
            <?php else : ?>
                <p style="color: #d63638;">
                    <?php _e('لا توجد مستويات نشطة. يرجى إضافة مستوى أولاً.', 'fiqh-lms'); ?>
                    <br>
                    <a href="<?php echo admin_url('edit.php?post_type=fiqh_course&page=fiqh-levels'); ?>" class="button button-secondary">
                        <?php _e('إدارة المستويات', 'fiqh-lms'); ?>
                    </a>
                </p>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Meta Box: المعلم
     */
    public function render_course_teacher_metabox($post) {
        $teacher_id = get_post_meta($post->ID, '_fiqh_course_teacher_id', true);

        // جلب جميع المعلمين
        $teachers = get_posts(array(
            'post_type' => 'fiqh_teacher',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ));

        ?>
        <p>
            <label for="fiqh_course_teacher_id"><strong><?php _e('اختر المعلم:', 'fiqh-lms'); ?></strong></label>
        </p>
        <select name="fiqh_course_teacher_id" id="fiqh_course_teacher_id" class="widefat">
            <option value=""><?php _e('-- لا يوجد معلم --', 'fiqh-lms'); ?></option>
            <?php foreach ($teachers as $teacher) : ?>
                <option value="<?php echo $teacher->ID; ?>" <?php selected($teacher_id, $teacher->ID); ?>>
                    <?php echo esc_html($teacher->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description">
            <?php _e('المعلم المسؤول عن تدريس هذا المقرر', 'fiqh-lms'); ?>
        </p>
        <?php
    }

    /**
     * حفظ بيانات المقرر
     */
    public function save_course_meta($post_id, $post) {
        // التحقق من nonce
        if (!isset($_POST['fiqh_course_meta_nonce']) || !wp_verify_nonce($_POST['fiqh_course_meta_nonce'], 'fiqh_course_meta_nonce')) {
            return;
        }

        // التحقق من الصلاحيات
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // التحقق من auto-save
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // حفظ رابط الكتاب
        if (isset($_POST['fiqh_course_book_url'])) {
            update_post_meta($post_id, '_fiqh_course_book_url', esc_url_raw($_POST['fiqh_course_book_url']));
        }

        // حفظ مدة المقرر
        if (isset($_POST['fiqh_course_duration'])) {
            update_post_meta($post_id, '_fiqh_course_duration', sanitize_text_field($_POST['fiqh_course_duration']));
        }

        // حفظ المستويات
        if (isset($_POST['fiqh_course_levels']) && is_array($_POST['fiqh_course_levels'])) {
            $levels = array_map('intval', $_POST['fiqh_course_levels']);
            update_post_meta($post_id, '_fiqh_course_levels', $levels);
        } else {
            // إذا لم يتم اختيار أي مستوى، نحذف القيمة
            delete_post_meta($post_id, '_fiqh_course_levels');
        }
    }
}

// تهيئة الـ Class
FiqhLearning_Course_Meta::get_instance();
