<?php
/**
 * واجهة إدارة الدفعات في wp-admin
 *
 * @package FiqhLearning_LMS
 */

if (!defined('ABSPATH')) {
    exit;
}

class FiqhLearning_Batches_Admin {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_post_fiqh_add_batch', array($this, 'handle_add_batch'));
        add_action('admin_post_fiqh_edit_batch', array($this, 'handle_edit_batch'));
        add_action('admin_post_fiqh_delete_batch', array($this, 'handle_delete_batch'));
    }

    /**
     * إضافة قائمة في wp-admin
     */
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=fiqh_course',
            __('إدارة الدفعات', 'fiqh-lms'),
            __('الدفعات', 'fiqh-lms'),
            'manage_options',
            'fiqh-batches',
            array($this, 'render_batches_page')
        );
    }

    /**
     * عرض صفحة الدفعات
     */
    public function render_batches_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'fiqh_batches';

        // الإجراء
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';
        $batch_id = isset($_GET['batch_id']) ? intval($_GET['batch_id']) : 0;

        if ($action === 'add') {
            $this->render_add_batch_form();
        } elseif ($action === 'edit' && $batch_id) {
            $this->render_edit_batch_form($batch_id);
        } else {
            $this->render_batches_list();
        }
    }

    /**
     * عرض قائمة الدفعات
     */
    private function render_batches_list() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'fiqh_batches';

        $batches = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">
                <?php _e('إدارة الدفعات', 'fiqh-lms'); ?>
            </h1>
            <a href="<?php echo admin_url('edit.php?post_type=fiqh_course&page=fiqh-batches&action=add'); ?>" class="page-title-action">
                <?php _e('إضافة دفعة جديدة', 'fiqh-lms'); ?>
            </a>
            <hr class="wp-header-end">

            <?php
            // رسائل النجاح/الخطأ
            if (isset($_GET['message'])) {
                $message = $_GET['message'];
                if ($message === 'added') {
                    echo '<div class="notice notice-success is-dismissible"><p>' . __('تمت إضافة الدفعة بنجاح', 'fiqh-lms') . '</p></div>';
                } elseif ($message === 'updated') {
                    echo '<div class="notice notice-success is-dismissible"><p>' . __('تم تحديث الدفعة بنجاح', 'fiqh-lms') . '</p></div>';
                } elseif ($message === 'deleted') {
                    echo '<div class="notice notice-success is-dismissible"><p>' . __('تم حذف الدفعة بنجاح', 'fiqh-lms') . '</p></div>';
                }
            }
            ?>

            <?php if ($batches): ?>
                <table class="wp-list-table widefat fixed striped" style="margin-top: 20px;">
                    <thead>
                        <tr>
                            <th style="width: 60px;"><?php _e('الرقم', 'fiqh-lms'); ?></th>
                            <th><?php _e('اسم الدفعة', 'fiqh-lms'); ?></th>
                            <th><?php _e('الوصف', 'fiqh-lms'); ?></th>
                            <th><?php _e('تاريخ البداية', 'fiqh-lms'); ?></th>
                            <th><?php _e('تاريخ النهاية', 'fiqh-lms'); ?></th>
                            <th><?php _e('الحالة', 'fiqh-lms'); ?></th>
                            <th><?php _e('عدد الطلاب', 'fiqh-lms'); ?></th>
                            <th style="width: 150px;"><?php _e('الإجراءات', 'fiqh-lms'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($batches as $batch):
                            // عدد الطلاب في الدفعة
                            $students_count = $wpdb->get_var($wpdb->prepare(
                                "SELECT COUNT(DISTINCT user_id) FROM {$wpdb->prefix}fiqh_enrollments WHERE batch_id = %d",
                                $batch->id
                            ));
                            ?>
                            <tr>
                                <td><?php echo $batch->id; ?></td>
                                <td><strong><?php echo esc_html($batch->name); ?></strong></td>
                                <td><?php echo esc_html(wp_trim_words($batch->description, 10)); ?></td>
                                <td><?php echo $batch->start_date ? date_i18n('Y-m-d', strtotime($batch->start_date)) : '-'; ?></td>
                                <td><?php echo $batch->end_date ? date_i18n('Y-m-d', strtotime($batch->end_date)) : '-'; ?></td>
                                <td>
                                    <?php
                                    $status_labels = array(
                                        'active' => '<span style="color: #10B981; font-weight: bold;">● نشطة</span>',
                                        'completed' => '<span style="color: #6B7280;">● منتهية</span>',
                                        'upcoming' => '<span style="color: #3B82F6;">● قادمة</span>',
                                    );
                                    echo $status_labels[$batch->status] ?? $batch->status;
                                    ?>
                                </td>
                                <td><?php echo $students_count; ?> <?php _e('طالب', 'fiqh-lms'); ?></td>
                                <td>
                                    <a href="<?php echo admin_url('edit.php?post_type=fiqh_course&page=fiqh-batches&action=edit&batch_id=' . $batch->id); ?>" class="button button-small">
                                        <?php _e('تعديل', 'fiqh-lms'); ?>
                                    </a>
                                    <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=fiqh_delete_batch&batch_id=' . $batch->id), 'delete_batch_' . $batch->id); ?>"
                                       class="button button-small"
                                       onclick="return confirm('<?php _e('هل أنت متأكد من حذف هذه الدفعة؟', 'fiqh-lms'); ?>');">
                                        <?php _e('حذف', 'fiqh-lms'); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="notice notice-info" style="margin-top: 20px;">
                    <p><?php _e('لا توجد دفعات بعد. أضف دفعة جديدة للبدء.', 'fiqh-lms'); ?></p>
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
     * نموذج إضافة دفعة
     */
    private function render_add_batch_form() {
        ?>
        <div class="wrap">
            <h1><?php _e('إضافة دفعة جديدة', 'fiqh-lms'); ?></h1>
            <hr class="wp-header-end">

            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" class="fiqh-batch-form">
                <?php wp_nonce_field('fiqh_add_batch', 'fiqh_batch_nonce'); ?>
                <input type="hidden" name="action" value="fiqh_add_batch">

                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="batch_name"><?php _e('اسم الدفعة', 'fiqh-lms'); ?> <span class="required" style="color: red;">*</span></label>
                            </th>
                            <td>
                                <input type="text" id="batch_name" name="batch_name" class="regular-text" required>
                                <p class="description"><?php _e('مثال: دفعة 2024-2025', 'fiqh-lms'); ?></p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="batch_description"><?php _e('الوصف', 'fiqh-lms'); ?></label>
                            </th>
                            <td>
                                <textarea id="batch_description" name="batch_description" rows="4" class="large-text"></textarea>
                                <p class="description"><?php _e('وصف مختصر للدفعة', 'fiqh-lms'); ?></p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="batch_start_date"><?php _e('تاريخ البداية', 'fiqh-lms'); ?></label>
                            </th>
                            <td>
                                <input type="date" id="batch_start_date" name="batch_start_date" class="regular-text">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="batch_end_date"><?php _e('تاريخ النهاية', 'fiqh-lms'); ?></label>
                            </th>
                            <td>
                                <input type="date" id="batch_end_date" name="batch_end_date" class="regular-text">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="batch_status"><?php _e('الحالة', 'fiqh-lms'); ?></label>
                            </th>
                            <td>
                                <select id="batch_status" name="batch_status" class="regular-text">
                                    <option value="upcoming"><?php _e('قادمة', 'fiqh-lms'); ?></option>
                                    <option value="active" selected><?php _e('نشطة', 'fiqh-lms'); ?></option>
                                    <option value="completed"><?php _e('منتهية', 'fiqh-lms'); ?></option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('إضافة الدفعة', 'fiqh-lms'); ?>">
                    <a href="<?php echo admin_url('edit.php?post_type=fiqh_course&page=fiqh-batches'); ?>" class="button">
                        <?php _e('إلغاء', 'fiqh-lms'); ?>
                    </a>
                </p>
            </form>
        </div>
        <?php
    }

    /**
     * نموذج تعديل دفعة
     */
    private function render_edit_batch_form($batch_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'fiqh_batches';
        $batch = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $batch_id));

        if (!$batch) {
            echo '<div class="wrap"><div class="notice notice-error"><p>' . __('الدفعة غير موجودة', 'fiqh-lms') . '</p></div></div>';
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php _e('تعديل الدفعة', 'fiqh-lms'); ?>: <?php echo esc_html($batch->name); ?></h1>
            <hr class="wp-header-end">

            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" class="fiqh-batch-form">
                <?php wp_nonce_field('fiqh_edit_batch_' . $batch_id, 'fiqh_batch_nonce'); ?>
                <input type="hidden" name="action" value="fiqh_edit_batch">
                <input type="hidden" name="batch_id" value="<?php echo $batch_id; ?>">

                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="batch_name"><?php _e('اسم الدفعة', 'fiqh-lms'); ?> <span class="required" style="color: red;">*</span></label>
                            </th>
                            <td>
                                <input type="text" id="batch_name" name="batch_name" class="regular-text" value="<?php echo esc_attr($batch->name); ?>" required>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="batch_description"><?php _e('الوصف', 'fiqh-lms'); ?></label>
                            </th>
                            <td>
                                <textarea id="batch_description" name="batch_description" rows="4" class="large-text"><?php echo esc_textarea($batch->description); ?></textarea>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="batch_start_date"><?php _e('تاريخ البداية', 'fiqh-lms'); ?></label>
                            </th>
                            <td>
                                <input type="date" id="batch_start_date" name="batch_start_date" class="regular-text" value="<?php echo esc_attr($batch->start_date); ?>">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="batch_end_date"><?php _e('تاريخ النهاية', 'fiqh-lms'); ?></label>
                            </th>
                            <td>
                                <input type="date" id="batch_end_date" name="batch_end_date" class="regular-text" value="<?php echo esc_attr($batch->end_date); ?>">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="batch_status"><?php _e('الحالة', 'fiqh-lms'); ?></label>
                            </th>
                            <td>
                                <select id="batch_status" name="batch_status" class="regular-text">
                                    <option value="upcoming" <?php selected($batch->status, 'upcoming'); ?>><?php _e('قادمة', 'fiqh-lms'); ?></option>
                                    <option value="active" <?php selected($batch->status, 'active'); ?>><?php _e('نشطة', 'fiqh-lms'); ?></option>
                                    <option value="completed" <?php selected($batch->status, 'completed'); ?>><?php _e('منتهية', 'fiqh-lms'); ?></option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('حفظ التغييرات', 'fiqh-lms'); ?>">
                    <a href="<?php echo admin_url('edit.php?post_type=fiqh_course&page=fiqh-batches'); ?>" class="button">
                        <?php _e('إلغاء', 'fiqh-lms'); ?>
                    </a>
                </p>
            </form>
        </div>
        <?php
    }

    /**
     * معالجة إضافة دفعة
     */
    public function handle_add_batch() {
        if (!current_user_can('manage_options')) {
            wp_die(__('ليس لديك صلاحية', 'fiqh-lms'));
        }

        check_admin_referer('fiqh_add_batch', 'fiqh_batch_nonce');

        global $wpdb;
        $table_name = $wpdb->prefix . 'fiqh_batches';

        $data = array(
            'name' => sanitize_text_field($_POST['batch_name']),
            'description' => sanitize_textarea_field($_POST['batch_description']),
            'start_date' => sanitize_text_field($_POST['batch_start_date']),
            'end_date' => sanitize_text_field($_POST['batch_end_date']),
            'status' => sanitize_text_field($_POST['batch_status']),
        );

        $wpdb->insert($table_name, $data);

        wp_redirect(admin_url('edit.php?post_type=fiqh_course&page=fiqh-batches&message=added'));
        exit;
    }

    /**
     * معالجة تعديل دفعة
     */
    public function handle_edit_batch() {
        if (!current_user_can('manage_options')) {
            wp_die(__('ليس لديك صلاحية', 'fiqh-lms'));
        }

        $batch_id = intval($_POST['batch_id']);
        check_admin_referer('fiqh_edit_batch_' . $batch_id, 'fiqh_batch_nonce');

        global $wpdb;
        $table_name = $wpdb->prefix . 'fiqh_batches';

        $data = array(
            'name' => sanitize_text_field($_POST['batch_name']),
            'description' => sanitize_textarea_field($_POST['batch_description']),
            'start_date' => sanitize_text_field($_POST['batch_start_date']),
            'end_date' => sanitize_text_field($_POST['batch_end_date']),
            'status' => sanitize_text_field($_POST['batch_status']),
        );

        $wpdb->update($table_name, $data, array('id' => $batch_id));

        wp_redirect(admin_url('edit.php?post_type=fiqh_course&page=fiqh-batches&message=updated'));
        exit;
    }

    /**
     * معالجة حذف دفعة
     */
    public function handle_delete_batch() {
        if (!current_user_can('manage_options')) {
            wp_die(__('ليس لديك صلاحية', 'fiqh-lms'));
        }

        $batch_id = intval($_GET['batch_id']);
        check_admin_referer('delete_batch_' . $batch_id);

        global $wpdb;
        $table_name = $wpdb->prefix . 'fiqh_batches';

        $wpdb->delete($table_name, array('id' => $batch_id));

        wp_redirect(admin_url('edit.php?post_type=fiqh_course&page=fiqh-batches&message=deleted'));
        exit;
    }
}

// تهيئة
FiqhLearning_Batches_Admin::get_instance();
