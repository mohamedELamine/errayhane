<?php
/**
 * إدارة Meta للمستخدمين (الطلاب)
 *
 * @package FiqhLearning_LMS
 */

if (!defined('ABSPATH')) {
    exit;
}

class FiqhLearning_User_Meta {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // إضافة حقول مخصصة في صفحة المستخدم
        add_action('show_user_profile', array($this, 'add_user_level_fields'));
        add_action('edit_user_profile', array($this, 'add_user_level_fields'));

        // حفظ الحقول
        add_action('personal_options_update', array($this, 'save_user_level_fields'));
        add_action('edit_user_profile_update', array($this, 'save_user_level_fields'));

        // إضافة عمود المستوى في قائمة المستخدمين
        add_filter('manage_users_columns', array($this, 'add_level_column'));
        add_filter('manage_users_custom_column', array($this, 'show_level_column_content'), 10, 3);
    }

    /**
     * إضافة حقل المستوى في صفحة المستخدم
     */
    public function add_user_level_fields($user) {
        // فقط للطلاب
        if (!in_array('student', (array) $user->roles) && !in_array('administrator', (array) $user->roles)) {
            return;
        }

        global $wpdb;
        $levels = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}fiqh_batches WHERE status = 'active' ORDER BY name ASC");

        $current_level = get_user_meta($user->ID, '_fiqh_student_level', true);
        ?>
        <h2><?php _e('معلومات الطالب', 'fiqh-lms'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="fiqh_student_level"><?php _e('المستوى الدراسي', 'fiqh-lms'); ?></label>
                </th>
                <td>
                    <select name="fiqh_student_level" id="fiqh_student_level" class="regular-text">
                        <option value=""><?php _e('-- لم يتم تحديد المستوى --', 'fiqh-lms'); ?></option>
                        <?php foreach ($levels as $level) : ?>
                            <option value="<?php echo esc_attr($level->id); ?>" <?php selected($current_level, $level->id); ?>>
                                <?php echo esc_html($level->name); ?>
                                <?php if ($level->start_date && $level->end_date) : ?>
                                    (<?php echo $level->start_date . ' - ' . $level->end_date; ?>)
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">
                        <?php _e('حدد المستوى الدراسي الذي ينتمي إليه هذا الطالب', 'fiqh-lms'); ?>
                    </p>

                    <?php if ($current_level) : ?>
                        <?php
                        $level_info = $wpdb->get_row($wpdb->prepare(
                            "SELECT * FROM {$wpdb->prefix}fiqh_batches WHERE id = %d",
                            $current_level
                        ));

                        if ($level_info) {
                            // حساب عدد المقررات في المستوى
                            $courses_count = 0;
                            $all_courses = get_posts(array('post_type' => 'fiqh_course', 'posts_per_page' => -1, 'fields' => 'ids'));
                            foreach ($all_courses as $course_id) {
                                $course_levels = get_post_meta($course_id, '_fiqh_course_levels', true);
                                if (is_array($course_levels) && in_array($current_level, $course_levels)) {
                                    $courses_count++;
                                }
                            }

                            // حساب عدد المقررات المسجل فيها الطالب
                            $enrolled_courses = $wpdb->get_var($wpdb->prepare(
                                "SELECT COUNT(DISTINCT course_id) FROM {$wpdb->prefix}fiqh_enrollments WHERE user_id = %d",
                                $user->ID
                            ));
                            ?>
                            <div class="notice notice-info inline" style="margin-top: 10px; padding: 10px;">
                                <p><strong><?php _e('معلومات المستوى الحالي:', 'fiqh-lms'); ?></strong></p>
                                <ul style="margin: 5px 0; padding-right: 20px;">
                                    <li><?php _e('اسم المستوى:', 'fiqh-lms'); ?> <strong><?php echo esc_html($level_info->name); ?></strong></li>
                                    <li><?php _e('عدد المقررات في المستوى:', 'fiqh-lms'); ?> <strong><?php echo $courses_count; ?></strong></li>
                                    <li><?php _e('عدد المقررات المسجل فيها الطالب:', 'fiqh-lms'); ?> <strong><?php echo $enrolled_courses; ?></strong></li>
                                    <li>
                                        <a href="<?php echo admin_url('edit.php?post_type=fiqh_course&page=fiqh-levels&action=view&level_id=' . $current_level); ?>">
                                            <?php _e('عرض تفاصيل المستوى', 'fiqh-lms'); ?> &larr;
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        <?php } ?>
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <?php
        // عرض المقررات المتاحة للطالب بناءً على المستوى
        if ($current_level) {
            $available_courses = array();
            $all_courses = get_posts(array('post_type' => 'fiqh_course', 'posts_per_page' => -1));
            foreach ($all_courses as $course) {
                $course_levels = get_post_meta($course->ID, '_fiqh_course_levels', true);
                if (is_array($course_levels) && in_array($current_level, $course_levels)) {
                    // التحقق من تسجيل الطالب
                    $is_enrolled = $wpdb->get_var($wpdb->prepare(
                        "SELECT id FROM {$wpdb->prefix}fiqh_enrollments WHERE user_id = %d AND course_id = %d",
                        $user->ID, $course->ID
                    ));
                    $available_courses[] = array(
                        'course' => $course,
                        'is_enrolled' => (bool) $is_enrolled
                    );
                }
            }

            if (!empty($available_courses)) : ?>
                <h2><?php _e('المقررات المتاحة في المستوى', 'fiqh-lms'); ?></h2>
                <table class="wp-list-table widefat fixed striped" style="margin-top: 10px;">
                    <thead>
                        <tr>
                            <th><?php _e('المقرر', 'fiqh-lms'); ?></th>
                            <th><?php _e('الحالة', 'fiqh-lms'); ?></th>
                            <th><?php _e('الإجراءات', 'fiqh-lms'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($available_courses as $item) : ?>
                            <tr>
                                <td><strong><?php echo esc_html($item['course']->post_title); ?></strong></td>
                                <td>
                                    <?php if ($item['is_enrolled']) : ?>
                                        <span style="color: #10B981;">● <?php _e('مسجل', 'fiqh-lms'); ?></span>
                                    <?php else : ?>
                                        <span style="color: #6B7280;">○ <?php _e('غير مسجل', 'fiqh-lms'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo get_permalink($item['course']->ID); ?>" class="button button-small" target="_blank">
                                        <?php _e('عرض', 'fiqh-lms'); ?>
                                    </a>
                                    <?php if (!$item['is_enrolled']) : ?>
                                        <a href="<?php echo admin_url('edit.php?post_type=fiqh_course&page=fiqh-enrollments'); ?>" class="button button-small button-primary">
                                            <?php _e('تسجيل الطالب', 'fiqh-lms'); ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif;
        }
    }

    /**
     * حفظ حقل المستوى
     */
    public function save_user_level_fields($user_id) {
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }

        if (isset($_POST['fiqh_student_level'])) {
            $level_id = intval($_POST['fiqh_student_level']);

            // حفظ المستوى الجديد
            if ($level_id > 0) {
                update_user_meta($user_id, '_fiqh_student_level', $level_id);

                // تحديث batch_id في جدول التسجيلات
                global $wpdb;
                $wpdb->update(
                    $wpdb->prefix . 'fiqh_enrollments',
                    array('batch_id' => $level_id),
                    array('user_id' => $user_id),
                    array('%d'),
                    array('%d')
                );
            } else {
                // إزالة المستوى إذا تم اختيار "لم يتم تحديد المستوى"
                delete_user_meta($user_id, '_fiqh_student_level');
            }
        }
    }

    /**
     * إضافة عمود المستوى في قائمة المستخدمين
     */
    public function add_level_column($columns) {
        $columns['fiqh_level'] = __('المستوى الدراسي', 'fiqh-lms');
        return $columns;
    }

    /**
     * عرض محتوى عمود المستوى
     */
    public function show_level_column_content($value, $column_name, $user_id) {
        if ($column_name === 'fiqh_level') {
            $user = get_userdata($user_id);

            // فقط للطلاب
            if (!in_array('student', (array) $user->roles)) {
                return '-';
            }

            $level_id = get_user_meta($user_id, '_fiqh_student_level', true);

            if ($level_id) {
                global $wpdb;
                $level = $wpdb->get_row($wpdb->prepare(
                    "SELECT name FROM {$wpdb->prefix}fiqh_batches WHERE id = %d",
                    $level_id
                ));

                if ($level) {
                    return '<strong>' . esc_html($level->name) . '</strong>';
                }
            }

            return '<span style="color: #999;">' . __('لم يتم التحديد', 'fiqh-lms') . '</span>';
        }

        return $value;
    }
}

// تهيئة الـ Class
FiqhLearning_User_Meta::get_instance();
