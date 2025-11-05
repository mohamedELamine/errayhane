<?php
/**
 * واجهة إدارة الاشتراكات الشهرية
 *
 * @package FiqhLearning_LMS
 */

if (!defined('ABSPATH')) {
    exit;
}

class FiqhLearning_Subscriptions_Admin {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_post_fiqh_add_subscription', array($this, 'handle_add_subscription'));
        add_action('admin_post_fiqh_edit_subscription', array($this, 'handle_edit_subscription'));
        add_action('admin_post_fiqh_delete_subscription', array($this, 'handle_delete_subscription'));
        add_action('admin_post_fiqh_export_subscriptions', array($this, 'handle_export_subscriptions'));
    }

    /**
     * إضافة قائمة في wp-admin
     */
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=fiqh_course',
            __('إدارة الاشتراكات', 'fiqh-lms'),
            __('الاشتراكات الشهرية', 'fiqh-lms'),
            'manage_options',
            'fiqh-subscriptions',
            array($this, 'render_subscriptions_page')
        );
    }

    /**
     * عرض صفحة الاشتراكات
     */
    public function render_subscriptions_page() {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';
        $subscription_id = isset($_GET['subscription_id']) ? intval($_GET['subscription_id']) : 0;

        if ($action === 'add') {
            $this->render_add_subscription_form();
        } elseif ($action === 'edit' && $subscription_id) {
            $this->render_edit_subscription_form($subscription_id);
        } else {
            $this->render_subscriptions_list();
        }
    }

    /**
     * الحصول على مستوى الطالب تلقائياً
     */
    private function get_student_level($user_id) {
        global $wpdb;

        // البحث عن أول مستوى نشط للطالب
        $batch = $wpdb->get_row($wpdb->prepare(
            "SELECT b.* FROM {$wpdb->prefix}fiqh_batches b
            INNER JOIN {$wpdb->prefix}fiqh_batch_students bs ON b.id = bs.batch_id
            WHERE bs.user_id = %d AND bs.status = 'active'
            ORDER BY b.level_order DESC
            LIMIT 1",
            $user_id
        ));

        return $batch;
    }

    /**
     * التحقق من التأخر في الدفع
     */
    private function check_payment_overdue($subscription) {
        if (!$subscription || !$subscription->start_date) {
            return false;
        }

        $start_date = new DateTime($subscription->start_date);
        $current_date = new DateTime();

        // حساب عدد الأشهر المفترضة
        $interval = $start_date->diff($current_date);
        $expected_months = ($interval->y * 12) + $interval->m;

        // إذا كان العدد الفعلي للدفعات أقل من المتوقع
        if ($subscription->total_payments < $expected_months && $subscription->status === 'active') {
            return true;
        }

        return false;
    }

    /**
     * عرض قائمة الاشتراكات
     */
    private function render_subscriptions_list() {
        global $wpdb;

        // فلترة حسب الشهر
        $selected_month = isset($_GET['month']) ? sanitize_text_field($_GET['month']) : '';
        $selected_status = isset($_GET['status_filter']) ? sanitize_text_field($_GET['status_filter']) : '';

        $where_clauses = array("1=1");

        if ($selected_month) {
            $where_clauses[] = $wpdb->prepare(
                "(DATE_FORMAT(s.start_date, '%%Y-%%m') <= %s AND (s.end_date IS NULL OR DATE_FORMAT(s.end_date, '%%Y-%%m') >= %s))",
                $selected_month, $selected_month
            );
        }

        if ($selected_status) {
            $where_clauses[] = $wpdb->prepare("s.status = %s", $selected_status);
        }

        $where_sql = implode(' AND ', $where_clauses);

        $subscriptions = $wpdb->get_results(
            "SELECT s.*, u.display_name, u.user_email, b.name as batch_name
            FROM {$wpdb->prefix}fiqh_subscriptions s
            LEFT JOIN {$wpdb->users} u ON s.user_id = u.ID
            LEFT JOIN {$wpdb->prefix}fiqh_batches b ON s.batch_id = b.id
            WHERE $where_sql
            ORDER BY s.created_at DESC"
        );
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">
                <span class="dashicons dashicons-money-alt" style="font-size: 28px; vertical-align: middle;"></span>
                <?php _e('إدارة الاشتراكات الشهرية', 'fiqh-lms'); ?>
            </h1>
            <a href="<?php echo admin_url('edit.php?post_type=fiqh_course&page=fiqh-subscriptions&action=add'); ?>" class="page-title-action">
                <?php _e('إضافة اشتراك جديد', 'fiqh-lms'); ?>
            </a>
            <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=fiqh_export_subscriptions' . ($selected_month ? '&month=' . $selected_month : '') . ($selected_status ? '&status_filter=' . $selected_status : '')), 'export_subscriptions'); ?>" class="page-title-action">
                <span class="dashicons dashicons-download" style="vertical-align: middle;"></span>
                <?php _e('تصدير إلى CSV', 'fiqh-lms'); ?>
            </a>
            <hr class="wp-header-end">

            <?php
            // رسائل النجاح/الخطأ
            if (isset($_GET['message'])) {
                $message = $_GET['message'];
                if ($message === 'added') {
                    echo '<div class="notice notice-success is-dismissible"><p>' . __('تمت إضافة الاشتراك بنجاح', 'fiqh-lms') . '</p></div>';
                } elseif ($message === 'updated') {
                    echo '<div class="notice notice-success is-dismissible"><p>' . __('تم تحديث الاشتراك بنجاح', 'fiqh-lms') . '</p></div>';
                } elseif ($message === 'deleted') {
                    echo '<div class="notice notice-success is-dismissible"><p>' . __('تم حذف الاشتراك بنجاح', 'fiqh-lms') . '</p></div>';
                }
            }
            ?>

            <!-- الفلاتر -->
            <div class="tablenav top" style="margin: 20px 0;">
                <form method="get" style="display: inline-block;">
                    <input type="hidden" name="post_type" value="fiqh_course">
                    <input type="hidden" name="page" value="fiqh-subscriptions">

                    <label for="month"><?php _e('الشهر:', 'fiqh-lms'); ?></label>
                    <input type="month" id="month" name="month" value="<?php echo esc_attr($selected_month); ?>" style="margin: 0 10px;">

                    <label for="status_filter"><?php _e('الحالة:', 'fiqh-lms'); ?></label>
                    <select id="status_filter" name="status_filter" style="margin: 0 10px;">
                        <option value=""><?php _e('الكل', 'fiqh-lms'); ?></option>
                        <option value="active" <?php selected($selected_status, 'active'); ?>><?php _e('نشط', 'fiqh-lms'); ?></option>
                        <option value="paused" <?php selected($selected_status, 'paused'); ?>><?php _e('موقوف', 'fiqh-lms'); ?></option>
                        <option value="cancelled" <?php selected($selected_status, 'cancelled'); ?>><?php _e('ملغي', 'fiqh-lms'); ?></option>
                    </select>

                    <button type="submit" class="button"><?php _e('تطبيق الفلاتر', 'fiqh-lms'); ?></button>
                    <?php if ($selected_month || $selected_status): ?>
                        <a href="<?php echo admin_url('edit.php?post_type=fiqh_course&page=fiqh-subscriptions'); ?>" class="button">
                            <?php _e('إعادة تعيين', 'fiqh-lms'); ?>
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <?php if ($subscriptions): ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th style="width: 60px;"><?php _e('الرقم', 'fiqh-lms'); ?></th>
                            <th><?php _e('الطالب', 'fiqh-lms'); ?></th>
                            <th><?php _e('البريد الإلكتروني', 'fiqh-lms'); ?></th>
                            <th><?php _e('المستوى الدراسي', 'fiqh-lms'); ?></th>
                            <th><?php _e('الاشتراك الشهري', 'fiqh-lms'); ?></th>
                            <th><?php _e('تاريخ البداية', 'fiqh-lms'); ?></th>
                            <th><?php _e('تاريخ النهاية', 'fiqh-lms'); ?></th>
                            <th><?php _e('عدد الدفعات', 'fiqh-lms'); ?></th>
                            <th><?php _e('المجموع المدفوع', 'fiqh-lms'); ?></th>
                            <th><?php _e('الحالة', 'fiqh-lms'); ?></th>
                            <th style="width: 150px;"><?php _e('الإجراءات', 'fiqh-lms'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subscriptions as $subscription):
                            $is_overdue = $this->check_payment_overdue($subscription);
                            ?>
                            <tr <?php echo $is_overdue ? 'style="background-color: #fff3cd;"' : ''; ?>>
                                <td><?php echo $subscription->id; ?></td>
                                <td><strong><?php echo esc_html($subscription->display_name); ?></strong></td>
                                <td><?php echo esc_html($subscription->user_email); ?></td>
                                <td><?php echo $subscription->batch_name ? esc_html($subscription->batch_name) : '-'; ?></td>
                                <td><strong><?php echo number_format($subscription->monthly_amount, 2); ?> د.م</strong></td>
                                <td><?php echo date_i18n('Y-m-d', strtotime($subscription->start_date)); ?></td>
                                <td><?php echo $subscription->end_date ? date_i18n('Y-m-d', strtotime($subscription->end_date)) : '-'; ?></td>
                                <td><?php echo $subscription->total_payments; ?></td>
                                <td><strong><?php echo number_format($subscription->total_paid, 2); ?> د.م</strong></td>
                                <td>
                                    <?php
                                    if ($is_overdue) {
                                        echo '<span style="color: #d63638; font-weight: bold;">⚠ متأخر عن الدفع</span>';
                                    } else {
                                        $status_labels = array(
                                            'active' => '<span style="color: #00a32a;">● نشط</span>',
                                            'paused' => '<span style="color: #dba617;">● موقوف</span>',
                                            'cancelled' => '<span style="color: #d63638;">● ملغي</span>',
                                        );
                                        echo $status_labels[$subscription->status] ?? $subscription->status;
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="<?php echo admin_url('edit.php?post_type=fiqh_course&page=fiqh-subscriptions&action=edit&subscription_id=' . $subscription->id); ?>" class="button button-small">
                                        <?php _e('تعديل', 'fiqh-lms'); ?>
                                    </a>
                                    <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=fiqh_delete_subscription&subscription_id=' . $subscription->id), 'delete_subscription_' . $subscription->id); ?>"
                                       class="button button-small"
                                       onclick="return confirm('<?php _e('هل أنت متأكد من حذف هذا الاشتراك؟', 'fiqh-lms'); ?>');">
                                        <?php _e('حذف', 'fiqh-lms'); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="notice notice-info" style="margin-top: 20px;">
                    <p><?php _e('لا توجد اشتراكات بعد. أضف اشتراك جديد للبدء.', 'fiqh-lms'); ?></p>
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
     * نموذج إضافة اشتراك
     */
    private function render_add_subscription_form() {
        $students = get_users(array('role' => 'student', 'orderby' => 'display_name'));
        ?>
        <div class="wrap">
            <h1><?php _e('إضافة اشتراك جديد', 'fiqh-lms'); ?></h1>
            <hr class="wp-header-end">

            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" id="subscription-form">
                <?php wp_nonce_field('fiqh_add_subscription', 'fiqh_subscription_nonce'); ?>
                <input type="hidden" name="action" value="fiqh_add_subscription">

                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="user_id"><?php _e('الطالب', 'fiqh-lms'); ?> <span class="required" style="color: red;">*</span></label>
                            </th>
                            <td>
                                <select id="user_id" name="user_id" class="regular-text" required>
                                    <option value=""><?php _e('-- اختر طالباً --', 'fiqh-lms'); ?></option>
                                    <?php foreach ($students as $student): ?>
                                        <option value="<?php echo $student->ID; ?>"
                                                data-email="<?php echo esc_attr($student->user_email); ?>">
                                            <?php echo esc_html($student->display_name); ?> (<?php echo esc_html($student->user_email); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="description"><?php _e('سيتم التعرف على مستوى الطالب تلقائياً', 'fiqh-lms'); ?></p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="monthly_amount"><?php _e('الاشتراك الشهري (د.م)', 'fiqh-lms'); ?> <span class="required" style="color: red;">*</span></label>
                            </th>
                            <td>
                                <input type="number" id="monthly_amount" name="monthly_amount" class="regular-text" step="0.01" min="0" required>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="start_date"><?php _e('تاريخ البداية', 'fiqh-lms'); ?> <span class="required" style="color: red;">*</span></label>
                            </th>
                            <td>
                                <input type="date" id="start_date" name="start_date" class="regular-text" required>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="end_date"><?php _e('تاريخ النهاية', 'fiqh-lms'); ?></label>
                            </th>
                            <td>
                                <input type="date" id="end_date" name="end_date" class="regular-text">
                                <p class="description"><?php _e('اتركه فارغاً إذا كان الاشتراك مفتوحاً', 'fiqh-lms'); ?></p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="status"><?php _e('الحالة', 'fiqh-lms'); ?></label>
                            </th>
                            <td>
                                <select id="status" name="status" class="regular-text">
                                    <option value="active" selected><?php _e('نشط', 'fiqh-lms'); ?></option>
                                    <option value="paused"><?php _e('موقوف', 'fiqh-lms'); ?></option>
                                    <option value="cancelled"><?php _e('ملغي', 'fiqh-lms'); ?></option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="notes"><?php _e('ملاحظات', 'fiqh-lms'); ?></label>
                            </th>
                            <td>
                                <textarea id="notes" name="notes" rows="4" class="large-text"></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('إضافة الاشتراك', 'fiqh-lms'); ?>">
                    <a href="<?php echo admin_url('edit.php?post_type=fiqh_course&page=fiqh-subscriptions'); ?>" class="button">
                        <?php _e('إلغاء', 'fiqh-lms'); ?>
                    </a>
                </p>
            </form>
        </div>
        <?php
    }

    /**
     * نموذج تعديل اشتراك
     */
    private function render_edit_subscription_form($subscription_id) {
        global $wpdb;

        $subscription = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}fiqh_subscriptions WHERE id = %d",
            $subscription_id
        ));

        if (!$subscription) {
            echo '<div class="wrap"><div class="notice notice-error"><p>' . __('الاشتراك غير موجود', 'fiqh-lms') . '</p></div></div>';
            return;
        }

        $user = get_userdata($subscription->user_id);
        $students = get_users(array('role' => 'student', 'orderby' => 'display_name'));
        ?>
        <div class="wrap">
            <h1><?php _e('تعديل الاشتراك', 'fiqh-lms'); ?>: <?php echo esc_html($user->display_name); ?></h1>
            <hr class="wp-header-end">

            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <?php wp_nonce_field('fiqh_edit_subscription_' . $subscription_id, 'fiqh_subscription_nonce'); ?>
                <input type="hidden" name="action" value="fiqh_edit_subscription">
                <input type="hidden" name="subscription_id" value="<?php echo $subscription_id; ?>">

                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="user_id"><?php _e('الطالب', 'fiqh-lms'); ?> <span class="required" style="color: red;">*</span></label>
                            </th>
                            <td>
                                <select id="user_id" name="user_id" class="regular-text" required>
                                    <?php foreach ($students as $student): ?>
                                        <option value="<?php echo $student->ID; ?>" <?php selected($subscription->user_id, $student->ID); ?>>
                                            <?php echo esc_html($student->display_name); ?> (<?php echo esc_html($student->user_email); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="monthly_amount"><?php _e('الاشتراك الشهري (د.م)', 'fiqh-lms'); ?> <span class="required" style="color: red;">*</span></label>
                            </th>
                            <td>
                                <input type="number" id="monthly_amount" name="monthly_amount" class="regular-text" step="0.01" min="0" value="<?php echo esc_attr($subscription->monthly_amount); ?>" required>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="start_date"><?php _e('تاريخ البداية', 'fiqh-lms'); ?> <span class="required" style="color: red;">*</span></label>
                            </th>
                            <td>
                                <input type="date" id="start_date" name="start_date" class="regular-text" value="<?php echo esc_attr($subscription->start_date); ?>" required>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="end_date"><?php _e('تاريخ النهاية', 'fiqh-lms'); ?></label>
                            </th>
                            <td>
                                <input type="date" id="end_date" name="end_date" class="regular-text" value="<?php echo esc_attr($subscription->end_date); ?>">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="total_payments"><?php _e('عدد الدفعات', 'fiqh-lms'); ?></label>
                            </th>
                            <td>
                                <input type="number" id="total_payments" name="total_payments" class="regular-text" min="0" value="<?php echo esc_attr($subscription->total_payments); ?>">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="total_paid"><?php _e('المجموع المدفوع (د.م)', 'fiqh-lms'); ?></label>
                            </th>
                            <td>
                                <input type="number" id="total_paid" name="total_paid" class="regular-text" step="0.01" min="0" value="<?php echo esc_attr($subscription->total_paid); ?>">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="status"><?php _e('الحالة', 'fiqh-lms'); ?></label>
                            </th>
                            <td>
                                <select id="status" name="status" class="regular-text">
                                    <option value="active" <?php selected($subscription->status, 'active'); ?>><?php _e('نشط', 'fiqh-lms'); ?></option>
                                    <option value="paused" <?php selected($subscription->status, 'paused'); ?>><?php _e('موقوف', 'fiqh-lms'); ?></option>
                                    <option value="cancelled" <?php selected($subscription->status, 'cancelled'); ?>><?php _e('ملغي', 'fiqh-lms'); ?></option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="notes"><?php _e('ملاحظات', 'fiqh-lms'); ?></label>
                            </th>
                            <td>
                                <textarea id="notes" name="notes" rows="4" class="large-text"><?php echo esc_textarea($subscription->notes); ?></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('حفظ التغييرات', 'fiqh-lms'); ?>">
                    <a href="<?php echo admin_url('edit.php?post_type=fiqh_course&page=fiqh-subscriptions'); ?>" class="button">
                        <?php _e('إلغاء', 'fiqh-lms'); ?>
                    </a>
                </p>
            </form>
        </div>
        <?php
    }

    /**
     * معالجة إضافة اشتراك
     */
    public function handle_add_subscription() {
        if (!current_user_can('manage_options')) {
            wp_die(__('ليس لديك صلاحية', 'fiqh-lms'));
        }

        check_admin_referer('fiqh_add_subscription', 'fiqh_subscription_nonce');

        global $wpdb;

        $user_id = intval($_POST['user_id']);

        // الحصول على مستوى الطالب تلقائياً
        $level = $this->get_student_level($user_id);
        $batch_id = $level ? $level->id : null;

        $data = array(
            'user_id' => $user_id,
            'batch_id' => $batch_id,
            'monthly_amount' => floatval($_POST['monthly_amount']),
            'start_date' => sanitize_text_field($_POST['start_date']),
            'end_date' => !empty($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : null,
            'status' => sanitize_text_field($_POST['status']),
            'notes' => sanitize_textarea_field($_POST['notes']),
        );

        $wpdb->insert($wpdb->prefix . 'fiqh_subscriptions', $data);

        wp_redirect(admin_url('edit.php?post_type=fiqh_course&page=fiqh-subscriptions&message=added'));
        exit;
    }

    /**
     * معالجة تعديل اشتراك
     */
    public function handle_edit_subscription() {
        if (!current_user_can('manage_options')) {
            wp_die(__('ليس لديك صلاحية', 'fiqh-lms'));
        }

        $subscription_id = intval($_POST['subscription_id']);
        check_admin_referer('fiqh_edit_subscription_' . $subscription_id, 'fiqh_subscription_nonce');

        global $wpdb;

        $user_id = intval($_POST['user_id']);

        // الحصول على مستوى الطالب تلقائياً
        $level = $this->get_student_level($user_id);
        $batch_id = $level ? $level->id : null;

        $data = array(
            'user_id' => $user_id,
            'batch_id' => $batch_id,
            'monthly_amount' => floatval($_POST['monthly_amount']),
            'start_date' => sanitize_text_field($_POST['start_date']),
            'end_date' => !empty($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : null,
            'total_payments' => intval($_POST['total_payments']),
            'total_paid' => floatval($_POST['total_paid']),
            'status' => sanitize_text_field($_POST['status']),
            'notes' => sanitize_textarea_field($_POST['notes']),
        );

        $wpdb->update(
            $wpdb->prefix . 'fiqh_subscriptions',
            $data,
            array('id' => $subscription_id)
        );

        wp_redirect(admin_url('edit.php?post_type=fiqh_course&page=fiqh-subscriptions&message=updated'));
        exit;
    }

    /**
     * معالجة حذف اشتراك
     */
    public function handle_delete_subscription() {
        if (!current_user_can('manage_options')) {
            wp_die(__('ليس لديك صلاحية', 'fiqh-lms'));
        }

        $subscription_id = intval($_GET['subscription_id']);
        check_admin_referer('delete_subscription_' . $subscription_id);

        global $wpdb;

        $wpdb->delete(
            $wpdb->prefix . 'fiqh_subscriptions',
            array('id' => $subscription_id)
        );

        wp_redirect(admin_url('edit.php?post_type=fiqh_course&page=fiqh-subscriptions&message=deleted'));
        exit;
    }

    /**
     * معالجة تصدير الاشتراكات إلى CSV
     */
    public function handle_export_subscriptions() {
        if (!current_user_can('manage_options')) {
            wp_die(__('ليس لديك صلاحية', 'fiqh-lms'));
        }

        check_admin_referer('export_subscriptions');

        global $wpdb;

        // تنظيف أي output سابق
        ob_clean();
        ob_start();

        // فلترة حسب الشهر إذا كان محدداً
        $selected_month = isset($_GET['month']) ? sanitize_text_field($_GET['month']) : '';
        $selected_status = isset($_GET['status_filter']) ? sanitize_text_field($_GET['status_filter']) : '';

        $where_clauses = array("1=1");

        if ($selected_month) {
            $where_clauses[] = $wpdb->prepare(
                "(DATE_FORMAT(s.start_date, '%%Y-%%m') <= %s AND (s.end_date IS NULL OR DATE_FORMAT(s.end_date, '%%Y-%%m') >= %s))",
                $selected_month, $selected_month
            );
        }

        if ($selected_status) {
            $where_clauses[] = $wpdb->prepare("s.status = %s", $selected_status);
        }

        $where_sql = implode(' AND ', $where_clauses);

        $subscriptions = $wpdb->get_results(
            "SELECT s.*, u.display_name, u.user_email, b.name as batch_name
            FROM {$wpdb->prefix}fiqh_subscriptions s
            LEFT JOIN {$wpdb->users} u ON s.user_id = u.ID
            LEFT JOIN {$wpdb->prefix}fiqh_batches b ON s.batch_id = b.id
            WHERE $where_sql
            ORDER BY s.created_at DESC"
        );

        // إعداد headers للتصدير
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="subscriptions_' . date('Y-m-d') . '.csv"');
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
        foreach ($subscriptions as $subscription) {
            $is_overdue = $this->check_payment_overdue($subscription);

            $status_text = '';
            if ($is_overdue) {
                $status_text = 'متأخر عن الدفع';
            } else {
                $status_map = array(
                    'active' => 'نشط',
                    'paused' => 'موقوف',
                    'cancelled' => 'ملغي',
                );
                $status_text = $status_map[$subscription->status] ?? $subscription->status;
            }

            fputcsv($output, array(
                $subscription->display_name,
                $subscription->user_email,
                $subscription->batch_name ?: '-',
                number_format($subscription->monthly_amount, 2) . ' د.م',
                $subscription->start_date,
                $subscription->end_date ?: '-',
                $subscription->total_payments,
                number_format($subscription->total_paid, 2) . ' د.م',
                $status_text,
                $subscription->notes
            ));
        }

        fclose($output);

        // تنظيف output buffer وإنهاء التنفيذ
        ob_end_flush();
        exit;
    }
}

// تهيئة
FiqhLearning_Subscriptions_Admin::get_instance();
