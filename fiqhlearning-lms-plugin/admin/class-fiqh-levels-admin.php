<?php
/**
 * واجهة إدارة المستويات في wp-admin
 *
 * @package FiqhLearning_LMS
 */

if (!defined('ABSPATH')) {
    exit;
}

class FiqhLearning_Levels_Admin {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_post_fiqh_add_level', array($this, 'handle_add_level'));
        add_action('admin_post_fiqh_edit_level', array($this, 'handle_edit_level'));
        add_action('admin_post_fiqh_delete_level', array($this, 'handle_delete_level'));
    }

    /**
     * إضافة قائمة في wp-admin
     */
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=fiqh_course',
            __('إدارة المستويات', 'fiqh-lms'),
            __('المستويات', 'fiqh-lms'),
            'manage_options',
            'fiqh-levels',
            array($this, 'render_levels_page')
        );
    }

    /**
     * عرض صفحة المستويات
     */
    public function render_levels_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'fiqh_levels';

        // الإجراء
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';
        $level_id = isset($_GET['level_id']) ? intval($_GET['level_id']) : 0;

        if ($action === 'add') {
            $this->render_add_level_form();
        } elseif ($action === 'edit' && $level_id) {
            $this->render_edit_level_form($level_id);
        } else {
            $this->render_levels_list();
        }
    }

    /**
     * عرض قائمة المستويات
     */
    private function render_levels_list() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'fiqh_levels';

        $levels = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">
                <?php _e('إدارة المستويات', 'fiqh-lms'); ?>
            </h1>
            <a href="<?php echo admin_url('edit.php?post_type=fiqh_course&page=fiqh-levels&action=add'); ?>" class="page-title-action">
                <?php _e('إضافة مستوى جديدة', 'fiqh-lms'); ?>
            </a>
            <hr class="wp-header-end">

            <?php
            // رسائل النجاح/الخطأ
            if (isset($_GET['message'])) {
                $message = $_GET['message'];
                if ($message === 'added') {
                    echo '<div class="notice notice-success is-dismissible"><p>' . __('تمت إضافة المستوى بنجاح', 'fiqh-lms') . '</p></div>';
                } elseif ($message === 'updated') {
                    echo '<div class="notice notice-success is-dismissible"><p>' . __('تم تحديث المستوى بنجاح', 'fiqh-lms') . '</p></div>';
                } elseif ($message === 'deleted') {
                    echo '<div class="notice notice-success is-dismissible"><p>' . __('تم حذف المستوى بنجاح', 'fiqh-lms') . '</p></div>';
                }
            }
            ?>

            <?php if ($levels): ?>
                <table class="wp-list-table widefat fixed striped" style="margin-top: 20px;">
                    <thead>
                        <tr>
                            <th style="width: 60px;"><?php _e('الرقم', 'fiqh-lms'); ?></th>
                            <th><?php _e('اسم المستوى', 'fiqh-lms'); ?></th>
                            <th><?php _e('الوصف', 'fiqh-lms'); ?></th>
                            <th><?php _e('تاريخ البداية', 'fiqh-lms'); ?></th>
                            <th><?php _e('تاريخ النهاية', 'fiqh-lms'); ?></th>
                            <th><?php _e('الحالة', 'fiqh-lms'); ?></th>
                            <th><?php _e('عدد الطلاب', 'fiqh-lms'); ?></th>
                            <th style="width: 150px;"><?php _e('الإجراءات', 'fiqh-lms'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($levels as $level):
                            // عدد الطلاب في المستوى
                            $students_count = $wpdb->get_var($wpdb->prepare(
                                "SELECT COUNT(DISTINCT user_id) FROM {$wpdb->prefix}fiqh_enrollments WHERE level_id = %d",
                                $level->id
                            ));
                            ?>
                            <tr>
                                <td><?php echo $level->id; ?></td>
                                <td><strong><?php echo esc_html($level->name); ?></strong></td>
                                <td><?php echo esc_html(wp_trim_words($level->description, 10)); ?></td>
                                <td><?php echo $level->start_date ? date_i18n('Y-m-d', strtotime($level->start_date)) : '-'; ?></td>
                                <td><?php echo $level->end_date ? date_i18n('Y-m-d', strtotime($level->end_date)) : '-'; ?></td>
                                <td>
                                    <?php
                                    $status_labels = array(
                                        'active' => '<span style="color: #10B981; font-weight: bold;">● نشطة</span>',
                                        'completed' => '<span style="color: #6B7280;">● منتهية</span>',
                                        'upcoming' => '<span style="color: #3B82F6;">● قادمة</span>',
                                    );
                                    echo $status_labels[$level->status] ?? $level->status;
                                    ?>
                                </td>
                                <td><?php echo $students_count; ?> <?php _e('طالب', 'fiqh-lms'); ?></td>
                                <td>
                                    <a href="<?php echo admin_url('edit.php?post_type=fiqh_course&page=fiqh-levels&action=edit&level_id=' . $level->id); ?>" class="button button-small">
                                        <?php _e('تعديل', 'fiqh-lms'); ?>
                                    </a>
                                    <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=fiqh_delete_level&level_id=' . $level->id), 'delete_level_' . $level->id); ?>"
                                       class="button button-small"
                                       onclick="return confirm('<?php _e('هل أنت متأكد من حذف هذه المستوى؟', 'fiqh-lms'); ?>');">
                                        <?php _e('حذف', 'fiqh-lms'); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="notice notice-info" style="margin-top: 20px;">
                    <p><?php _e('لا توجد دفعات بعد. أضف مستوى جديدة للبدء.', 'fiqh-lms'); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <style>
        .wp-list-table th {
            background: #f0f0f1;
            padding: 12px;
        }
        .wp-list-table td {
            padding: 10px;
            vertical-align: middle;
        }
        </style>
        <?php
    }

    /**
     * نموذج إضافة مستوى
     */
    private function render_add_level_form() {
        ?>
        <div class="wrap">
            <h1><?php _e('إضافة مستوى جديدة', 'fiqh-lms'); ?></h1>
            <hr class="wp-header-end">

            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" class="fiqh-level-form">
                <?php wp_nonce_field('fiqh_add_level', 'fiqh_level_nonce'); ?>
                <input type="hidden" name="action" value="fiqh_add_level">

                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="level_name"><?php _e('اسم المستوى', 'fiqh-lms'); ?> <span class="required" style="color: red;">*</span></label>
                            </th>
                            <td>
                                <input type="text" id="level_name" name="level_name" class="regular-text" required>
                                <p class="description"><?php _e('مثال: مستوى 2024-2025', 'fiqh-lms'); ?></p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="level_description"><?php _e('الوصف', 'fiqh-lms'); ?></label>
                            </th>
                            <td>
                                <textarea id="level_description" name="level_description" rows="4" class="large-text"></textarea>
                                <p class="description"><?php _e('وصف مختصر للمستوى', 'fiqh-lms'); ?></p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="level_start_date"><?php _e('تاريخ البداية', 'fiqh-lms'); ?></label>
                            </th>
                            <td>
                                <input type="date" id="level_start_date" name="level_start_date" class="regular-text">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="level_end_date"><?php _e('تاريخ النهاية', 'fiqh-lms'); ?></label>
                            </th>
                            <td>
                                <input type="date" id="level_end_date" name="level_end_date" class="regular-text">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="level_status"><?php _e('الحالة', 'fiqh-lms'); ?></label>
                            </th>
                            <td>
                                <select id="level_status" name="level_status" class="regular-text">
                                    <option value="upcoming"><?php _e('قادمة', 'fiqh-lms'); ?></option>
                                    <option value="active" selected><?php _e('نشطة', 'fiqh-lms'); ?></option>
                                    <option value="completed"><?php _e('منتهية', 'fiqh-lms'); ?></option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('إضافة المستوى', 'fiqh-lms'); ?>">
                    <a href="<?php echo admin_url('edit.php?post_type=fiqh_course&page=fiqh-levels'); ?>" class="button">
                        <?php _e('إلغاء', 'fiqh-lms'); ?>
                    </a>
                </p>
            </form>
        </div>
        <?php
    }

    /**
     * نموذج تعديل مستوى
     */
    private function render_edit_level_form($level_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'fiqh_levels';
        $level = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $level_id));

        if (!$level) {
            echo '<div class="wrap"><div class="notice notice-error"><p>' . __('المستوى غير موجودة', 'fiqh-lms') . '</p></div></div>';
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php _e('تعديل المستوى', 'fiqh-lms'); ?>: <?php echo esc_html($level->name); ?></h1>
            <hr class="wp-header-end">

            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" class="fiqh-level-form">
                <?php wp_nonce_field('fiqh_edit_level_' . $level_id, 'fiqh_level_nonce'); ?>
                <input type="hidden" name="action" value="fiqh_edit_level">
                <input type="hidden" name="level_id" value="<?php echo $level_id; ?>">

                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="level_name"><?php _e('اسم المستوى', 'fiqh-lms'); ?> <span class="required" style="color: red;">*</span></label>
                            </th>
                            <td>
                                <input type="text" id="level_name" name="level_name" class="regular-text" value="<?php echo esc_attr($level->name); ?>" required>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="level_description"><?php _e('الوصف', 'fiqh-lms'); ?></label>
                            </th>
                            <td>
                                <textarea id="level_description" name="level_description" rows="4" class="large-text"><?php echo esc_textarea($level->description); ?></textarea>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="level_start_date"><?php _e('تاريخ البداية', 'fiqh-lms'); ?></label>
                            </th>
                            <td>
                                <input type="date" id="level_start_date" name="level_start_date" class="regular-text" value="<?php echo esc_attr($level->start_date); ?>">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="level_end_date"><?php _e('تاريخ النهاية', 'fiqh-lms'); ?></label>
                            </th>
                            <td>
                                <input type="date" id="level_end_date" name="level_end_date" class="regular-text" value="<?php echo esc_attr($level->end_date); ?>">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="level_status"><?php _e('الحالة', 'fiqh-lms'); ?></label>
                            </th>
                            <td>
                                <select id="level_status" name="level_status" class="regular-text">
                                    <option value="upcoming" <?php selected($level->status, 'upcoming'); ?>><?php _e('قادمة', 'fiqh-lms'); ?></option>
                                    <option value="active" <?php selected($level->status, 'active'); ?>><?php _e('نشطة', 'fiqh-lms'); ?></option>
                                    <option value="completed" <?php selected($level->status, 'completed'); ?>><?php _e('منتهية', 'fiqh-lms'); ?></option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('حفظ التغييرات', 'fiqh-lms'); ?>">
                    <a href="<?php echo admin_url('edit.php?post_type=fiqh_course&page=fiqh-levels'); ?>" class="button">
                        <?php _e('إلغاء', 'fiqh-lms'); ?>
                    </a>
                </p>
            </form>
        </div>
        <?php
    }

    /**
     * معالجة إضافة مستوى
     */
    public function handle_add_level() {
        if (!current_user_can('manage_options')) {
            wp_die(__('ليس لديك صلاحية', 'fiqh-lms'));
        }

        check_admin_referer('fiqh_add_level', 'fiqh_level_nonce');

        global $wpdb;
        $table_name = $wpdb->prefix . 'fiqh_levels';

        $data = array(
            'name' => sanitize_text_field($_POST['level_name']),
            'description' => sanitize_textarea_field($_POST['level_description']),
            'start_date' => sanitize_text_field($_POST['level_start_date']),
            'end_date' => sanitize_text_field($_POST['level_end_date']),
            'status' => sanitize_text_field($_POST['level_status']),
        );

        $wpdb->insert($table_name, $data);

        wp_redirect(admin_url('edit.php?post_type=fiqh_course&page=fiqh-levels&message=added'));
        exit;
    }

    /**
     * معالجة تعديل مستوى
     */
    public function handle_edit_level() {
        if (!current_user_can('manage_options')) {
            wp_die(__('ليس لديك صلاحية', 'fiqh-lms'));
        }

        $level_id = intval($_POST['level_id']);
        check_admin_referer('fiqh_edit_level_' . $level_id, 'fiqh_level_nonce');

        global $wpdb;
        $table_name = $wpdb->prefix . 'fiqh_levels';

        $data = array(
            'name' => sanitize_text_field($_POST['level_name']),
            'description' => sanitize_textarea_field($_POST['level_description']),
            'start_date' => sanitize_text_field($_POST['level_start_date']),
            'end_date' => sanitize_text_field($_POST['level_end_date']),
            'status' => sanitize_text_field($_POST['level_status']),
        );

        $wpdb->update($table_name, $data, array('id' => $level_id));

        wp_redirect(admin_url('edit.php?post_type=fiqh_course&page=fiqh-levels&message=updated'));
        exit;
    }

    /**
     * معالجة حذف مستوى
     */
    public function handle_delete_level() {
        if (!current_user_can('manage_options')) {
            wp_die(__('ليس لديك صلاحية', 'fiqh-lms'));
        }

        $level_id = intval($_GET['level_id']);
        check_admin_referer('delete_level_' . $level_id);

        global $wpdb;
        $table_name = $wpdb->prefix . 'fiqh_levels';

        $wpdb->delete($table_name, array('id' => $level_id));

        wp_redirect(admin_url('edit.php?post_type=fiqh_course&page=fiqh-levels&message=deleted'));
        exit;
    }
}

// تهيئة
FiqhLearning_Levels_Admin::get_instance();
