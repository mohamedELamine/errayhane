<?php
/**
 * Plugin Name: FiqhLearning LMS
 * Plugin URI: https://fiqhlearning.com
 * Description: نظام إدارة التعلم المدمج لمنصة FiqhLearning - يوفر إدارة كاملة للمقررات والدروس والطلاب بدون الحاجة لإضافات خارجية
 * Version: 1.0.0
 * Author: FiqhLearning Team
 * Author URI: https://fiqhlearning.com
 * Text Domain: fiqh-lms
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// تعريف الثوابت
define('FIQH_LMS_VERSION', '1.0.0');
define('FIQH_LMS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FIQH_LMS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('FIQH_LMS_PLUGIN_FILE', __FILE__);

/**
 * الكلاس الرئيسي للـ Plugin
 */
class FiqhLearning_LMS {

    /**
     * Instance
     */
    private static $instance = null;

    /**
     * Get Instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->load_dependencies();
        $this->define_hooks();
    }

    /**
     * تحميل الملفات المطلوبة
     */
    private function load_dependencies() {
        require_once FIQH_LMS_PLUGIN_DIR . 'includes/class-fiqh-database.php';
        require_once FIQH_LMS_PLUGIN_DIR . 'includes/class-fiqh-post-types.php';
        require_once FIQH_LMS_PLUGIN_DIR . 'includes/class-fiqh-taxonomies.php';
        require_once FIQH_LMS_PLUGIN_DIR . 'includes/class-fiqh-enrollments.php';
        require_once FIQH_LMS_PLUGIN_DIR . 'includes/class-fiqh-progress.php';
        require_once FIQH_LMS_PLUGIN_DIR . 'includes/class-fiqh-questions.php';
        require_once FIQH_LMS_PLUGIN_DIR . 'includes/class-fiqh-notes.php';
        require_once FIQH_LMS_PLUGIN_DIR . 'includes/class-fiqh-ajax.php';
        require_once FIQH_LMS_PLUGIN_DIR . 'includes/class-fiqh-rest-api.php';
        require_once FIQH_LMS_PLUGIN_DIR . 'includes/class-fiqh-access-control.php';
        require_once FIQH_LMS_PLUGIN_DIR . 'admin/class-fiqh-admin.php';
        require_once FIQH_LMS_PLUGIN_DIR . 'admin/class-fiqh-admin-menus.php';
        require_once FIQH_LMS_PLUGIN_DIR . 'admin/class-fiqh-levels-admin.php';
        require_once FIQH_LMS_PLUGIN_DIR . 'admin/class-fiqh-subscriptions-admin.php';
        require_once FIQH_LMS_PLUGIN_DIR . 'admin/class-fiqh-course-meta.php';
        require_once FIQH_LMS_PLUGIN_DIR . 'admin/class-fiqh-user-meta.php';

        // تحميل سكريبت المحتوى التجريبي
        require_once FIQH_LMS_PLUGIN_DIR . 'demo-content.php';
    }

    /**
     * تعريف الـ Hooks
     */
    private function define_hooks() {
        // تفعيل/تعطيل Plugin
        register_activation_hook(FIQH_LMS_PLUGIN_FILE, array($this, 'activate'));
        register_deactivation_hook(FIQH_LMS_PLUGIN_FILE, array($this, 'deactivate'));

        // تهيئة Plugin
        add_action('init', array($this, 'init'), 0);
        add_action('plugins_loaded', array($this, 'load_textdomain'));
    }

    /**
     * تفعيل Plugin
     */
    public function activate() {
        // إنشاء الجداول
        FiqhLearning_Database::create_tables();

        // تسجيل Custom Post Types (مطلوب قبل flush_rewrite_rules)
        FiqhLearning_Post_Types::register_post_types();
        FiqhLearning_Taxonomies::register_taxonomies();

        // تحديث Rewrite Rules
        flush_rewrite_rules();

        // إضافة خيارات افتراضية
        add_option('fiqh_lms_version', FIQH_LMS_VERSION);
        add_option('fiqh_lms_db_version', '1.0.0');
    }

    /**
     * تعطيل Plugin
     */
    public function deactivate() {
        // تحديث Rewrite Rules
        flush_rewrite_rules();
    }

    /**
     * تهيئة Plugin
     */
    public function init() {
        // تسجيل Custom Post Types
        FiqhLearning_Post_Types::register_post_types();

        // تسجيل Taxonomies
        FiqhLearning_Taxonomies::register_taxonomies();

        // تهيئة Admin
        if (is_admin()) {
            FiqhLearning_Admin::get_instance();
            FiqhLearning_Admin_Menus::get_instance();
        }

        // تهيئة AJAX Handlers
        FiqhLearning_Ajax::get_instance();
    }

    /**
     * تحميل ملفات الترجمة
     */
    public function load_textdomain() {
        load_plugin_textdomain('fiqh-lms', false, dirname(plugin_basename(FIQH_LMS_PLUGIN_FILE)) . '/languages');
    }
}

/**
 * تشغيل Plugin
 */
function fiqh_lms() {
    return FiqhLearning_LMS::get_instance();
}

// تشغيل
$GLOBALS['fiqh_lms'] = fiqh_lms();
