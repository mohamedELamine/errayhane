<?php
/**
 * FiqhLearning Theme Functions
 *
 * @package FiqhLearning
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// تعريف الثوابت
define('FIQH_THEME_VERSION', '1.0.0');
define('FIQH_THEME_DIR', get_template_directory());
define('FIQH_THEME_URI', get_template_directory_uri());

/**
 * إعداد القالب
 */
function fiqhlearning_setup() {
    // دعم الترجمة
    load_theme_textdomain('fiqhlearning', FIQH_THEME_DIR . '/languages');

    // دعم العنوان التلقائي
    add_theme_support('title-tag');

    // دعم الصور المميزة
    add_theme_support('post-thumbnails');

    // أحجام صور مخصصة
    add_image_size('fiqh-hero', 1920, 800, true);
    add_image_size('fiqh-course-thumb', 600, 400, true);
    add_image_size('fiqh-lesson-thumb', 800, 450, true);
    add_image_size('fiqh-teacher-avatar', 300, 300, true);

    // دعم القوائم
    register_nav_menus(array(
        'primary' => __('القائمة الرئيسية', 'fiqhlearning'),
        'footer' => __('قائمة الفوتر', 'fiqhlearning'),
        'student-dashboard' => __('قائمة لوحة الطالب', 'fiqhlearning'),
    ));

    // دعم HTML5
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'script',
        'style',
    ));

    // دعم الخلاصات التلقائية
    add_theme_support('automatic-feed-links');

    // دعم Gutenberg
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    add_theme_support('editor-styles');
    add_editor_style('assets/css/editor-style.css');

    // دعم الألوان المخصصة
    add_theme_support('custom-background', array(
        'default-color' => 'F7FAFC',
    ));

    add_theme_support('custom-logo', array(
        'height' => 60,
        'width' => 200,
        'flex-height' => true,
        'flex-width' => true,
    ));
}
add_action('after_setup_theme', 'fiqhlearning_setup');

/**
 * تسجيل وتحميل الأنماط والسكربتات
 */
function fiqhlearning_enqueue_scripts() {
    // تحميل الخطوط العربية
    wp_enqueue_style(
        'fiqh-fonts',
        'https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&family=Noto+Naskh+Arabic:wght@400;500;600;700&display=swap',
        array(),
        null
    );

    // تحميل الأنماط الرئيسية
    wp_enqueue_style('fiqh-style', get_stylesheet_uri(), array(), FIQH_THEME_VERSION);

    // تحميل أنماط إضافية
    wp_enqueue_style('fiqh-main', FIQH_THEME_URI . '/assets/css/main.css', array('fiqh-style'), FIQH_THEME_VERSION);
    wp_enqueue_style('fiqh-components', FIQH_THEME_URI . '/assets/css/components.css', array('fiqh-main'), FIQH_THEME_VERSION);
    wp_enqueue_style('fiqh-responsive', FIQH_THEME_URI . '/assets/css/responsive.css', array('fiqh-main'), FIQH_THEME_VERSION);

    // تحميل أنماط صفحة البحث
    if (is_search()) {
        wp_enqueue_style('fiqh-search', FIQH_THEME_URI . '/assets/css/search.css', array('fiqh-main'), FIQH_THEME_VERSION);
    }

    // تحميل أنماط صفحة الدرس
    if (is_singular('fiqh_lesson')) {
        wp_enqueue_style('fiqh-lesson', FIQH_THEME_URI . '/assets/css/lesson.css', array('fiqh-main'), FIQH_THEME_VERSION);
    }

    // تحميل أنماط الصفحة الرئيسية
    if (is_front_page() || is_home()) {
        wp_enqueue_style('fiqh-home', FIQH_THEME_URI . '/assets/css/home.css', array('fiqh-main'), FIQH_THEME_VERSION);
    }

    // تحميل أنماط صفحة الاتصال
    if (is_page_template('page-contact.php')) {
        wp_enqueue_style('fiqh-contact', FIQH_THEME_URI . '/assets/css/contact.css', array('fiqh-main'), FIQH_THEME_VERSION);
    }

    // تحميل أنماط صفحة عن المدرسة
    if (is_page_template('page-about.php')) {
        wp_enqueue_style('fiqh-about', FIQH_THEME_URI . '/assets/css/about.css', array('fiqh-main'), FIQH_THEME_VERSION);
    }

    // تحميل أنماط صفحة المقال الواحد
    if (is_single() && get_post_type() == 'post') {
        wp_enqueue_style('fiqh-post', FIQH_THEME_URI . '/assets/css/post.css', array('fiqh-main'), FIQH_THEME_VERSION);
    }

    // تحميل أنماط صفحات خطة الدراسة ودليل اللوائح
    if (is_page_template('page-study-plan.php') || is_page_template('page-regulations.php')) {
        wp_enqueue_style('fiqh-pages', FIQH_THEME_URI . '/assets/css/pages.css', array('fiqh-main'), FIQH_THEME_VERSION);
    }

    // تحميل PDF.js للعرض المدمج
    wp_enqueue_script('pdfjs', FIQH_THEME_URI . '/assets/js/pdf.min.js', array(), '3.11.174', true);

    // تحميل السكربتات الرئيسية
    wp_enqueue_script('fiqh-main', FIQH_THEME_URI . '/assets/js/main.js', array('jquery'), FIQH_THEME_VERSION, true);

    // تمرير متغيرات إلى JavaScript
    wp_localize_script('fiqh-main', 'fiqhData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('fiqh-ajax-nonce'),
        'themeUrl' => FIQH_THEME_URI,
        'isUserLoggedIn' => is_user_logged_in(),
        'currentUserId' => get_current_user_id(),
        'strings' => array(
            'loading' => __('جاري التحميل...', 'fiqhlearning'),
            'error' => __('حدث خطأ، يرجى المحاولة مرة أخرى', 'fiqhlearning'),
            'success' => __('تمت العملية بنجاح', 'fiqhlearning'),
        )
    ));

    // تحميل سكربت التعليقات إذا كانت مفعلة
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'fiqhlearning_enqueue_scripts');

/**
 * تسجيل مناطق الودجات
 */
function fiqhlearning_widgets_init() {
    // Sidebar الرئيسي
    register_sidebar(array(
        'name' => __('الشريط الجانبي الرئيسي', 'fiqhlearning'),
        'id' => 'sidebar-main',
        'description' => __('يظهر في الصفحات الداخلية', 'fiqhlearning'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));

    // Footer - 3 أعمدة
    for ($i = 1; $i <= 3; $i++) {
        register_sidebar(array(
            'name' => sprintf(__('فوتر - عمود %d', 'fiqhlearning'), $i),
            'id' => 'footer-' . $i,
            'description' => sprintf(__('عمود الفوتر رقم %d', 'fiqhlearning'), $i),
            'before_widget' => '<div id="%1$s" class="widget footer-widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="widget-title">',
            'after_title' => '</h4>',
        ));
    }

    // منطقة الصفحة الرئيسية
    register_sidebar(array(
        'name' => __('الصفحة الرئيسية', 'fiqhlearning'),
        'id' => 'homepage',
        'description' => __('ودجات تظهر في الصفحة الرئيسية', 'fiqhlearning'),
        'before_widget' => '<div id="%1$s" class="homepage-widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));
}
add_action('widgets_init', 'fiqhlearning_widgets_init');

/**
 * إضافة أدوار مخصصة للمستخدمين
 */
function fiqhlearning_add_custom_roles() {
    // التحقق من عدم تكرار إضافة الأدوار
    if (get_role('student')) {
        return;
    }

    // دور الطالب
    add_role('student', __('طالب', 'fiqhlearning'), array(
        'read' => true,
        'edit_posts' => false,
        'delete_posts' => false,
    ));

    // دور المعلم
    add_role('teacher', __('معلم', 'fiqhlearning'), array(
        'read' => true,
        'edit_posts' => true,
        'edit_published_posts' => true,
        'publish_posts' => true,
        'delete_posts' => false,
        'upload_files' => true,
    ));
}
add_action('init', 'fiqhlearning_add_custom_roles');

/**
 * تخصيص صفحة تسجيل الدخول
 */
function fiqhlearning_login_logo() {
    if (has_custom_logo()) {
        $custom_logo_id = get_theme_mod('custom_logo');
        $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
        ?>
        <style type="text/css">
            #login h1 a, .login h1 a {
                background-image: url(<?php echo esc_url($logo[0]); ?>);
                height: 80px;
                width: 320px;
                background-size: contain;
                background-repeat: no-repeat;
                padding-bottom: 30px;
            }
        </style>
        <?php
    }
}
add_action('login_enqueue_scripts', 'fiqhlearning_login_logo');

/**
 * تغيير رابط شعار صفحة تسجيل الدخول
 */
function fiqhlearning_login_logo_url() {
    return home_url();
}
add_filter('login_headerurl', 'fiqhlearning_login_logo_url');

/**
 * تغيير نص شعار صفحة تسجيل الدخول
 */
function fiqhlearning_login_logo_url_title() {
    return get_bloginfo('name');
}
add_filter('login_headertext', 'fiqhlearning_login_logo_url_title');

/**
 * إعادة التوجيه بعد تسجيل الدخول
 */
function fiqhlearning_login_redirect($redirect_to, $request, $user) {
    // في حالة وجود خطأ في تسجيل الدخول
    if (isset($user->errors) && !empty($user->errors)) {
        return home_url('/login?login=failed');
    }

    // إعادة توجيه الطلاب إلى لوحة التحكم الخاصة بهم
    if (isset($user->roles) && is_array($user->roles)) {
        if (in_array('student', $user->roles)) {
            return home_url('/dashboard');
        }

        // إعادة توجيه المعلمين إلى لوحة التحكم الخاصة بهم
        if (in_array('teacher', $user->roles)) {
            return home_url('/dashboard');
        }

        // المدراء يذهبون إلى لوحة تحكم WordPress
        if (in_array('administrator', $user->roles)) {
            return admin_url();
        }
    }

    return $redirect_to;
}
add_filter('login_redirect', 'fiqhlearning_login_redirect', 10, 3);

/**
 * منع الطلاب من الوصول إلى لوحة تحكم WordPress
 */
function fiqhlearning_block_admin_access() {
    // السماح للطلبات AJAX
    if (defined('DOING_AJAX') && DOING_AJAX) {
        return;
    }

    // منع الطلاب من الوصول إلى لوحة التحكم
    if (is_admin() && is_user_logged_in()) {
        $user = wp_get_current_user();

        if (in_array('student', $user->roles)) {
            wp_redirect(home_url('/dashboard'));
            exit;
        }
    }
}
add_action('admin_init', 'fiqhlearning_block_admin_access');

/**
 * إعادة توجيه بعد تسجيل الخروج
 */
function fiqhlearning_logout_redirect() {
    wp_redirect(home_url('/login?login=logged_out'));
    exit;
}
add_action('wp_logout', 'fiqhlearning_logout_redirect');

/**
 * معالجة أخطاء تسجيل الدخول المخصصة
 */
function fiqhlearning_custom_login_failed($username) {
    $referrer = wp_get_referer();

    // إذا كان المستخدم قادماً من صفحة تسجيل الدخول المخصصة
    if ($referrer && strpos($referrer, '/login') !== false) {
        wp_redirect(home_url('/login?login=failed'));
        exit;
    }
}
add_action('wp_login_failed', 'fiqhlearning_custom_login_failed');

/**
 * معالجة حقول تسجيل الدخول الفارغة
 */
function fiqhlearning_blank_login_fields($user, $username, $password) {
    $referrer = wp_get_referer();

    if ($referrer && strpos($referrer, '/login') !== false) {
        if (empty($username) || empty($password)) {
            wp_redirect(home_url('/login?login=empty'));
            exit;
        }
    }

    return $user;
}
add_filter('authenticate', 'fiqhlearning_blank_login_fields', 30, 3);

/**
 * تعطيل صفحة التسجيل العامة
 */
function fiqhlearning_disable_public_registration() {
    if (isset($_GET['action']) && $_GET['action'] == 'register') {
        wp_redirect(wp_login_url());
        exit();
    }
}
add_action('init', 'fiqhlearning_disable_public_registration');

/**
 * إزالة رابط التسجيل من صفحة تسجيل الدخول
 */
function fiqhlearning_remove_register_link() {
    return null;
}
add_filter('register', 'fiqhlearning_remove_register_link');

/**
 * فلتر excerpt length
 */
function fiqhlearning_excerpt_length($length) {
    return 30;
}
add_filter('excerpt_length', 'fiqhlearning_excerpt_length', 999);

/**
 * فلتر excerpt more
 */
function fiqhlearning_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'fiqhlearning_excerpt_more');

/**
 * إضافة كلاسات مخصصة للـ body
 */
function fiqhlearning_body_classes($classes) {
    if (is_user_logged_in()) {
        $classes[] = 'logged-in-user';

        $user = wp_get_current_user();
        if (in_array('student', $user->roles)) {
            $classes[] = 'user-role-student';
        } elseif (in_array('teacher', $user->roles)) {
            $classes[] = 'user-role-teacher';
        } elseif (in_array('administrator', $user->roles)) {
            $classes[] = 'user-role-admin';
        }
    }

    // إضافة كلاس لوضع الظلام إذا كان مفعلاً
    if (isset($_COOKIE['fiqh_dark_mode']) && $_COOKIE['fiqh_dark_mode'] === 'true') {
        $classes[] = 'dark-mode';
    }

    return $classes;
}
add_filter('body_class', 'fiqhlearning_body_classes');

/**
 * دالة مساعدة للتحقق من صلاحيات الوصول للدروس
 */
function fiqh_can_access_lesson($lesson_id, $user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    // المدراء والمعلمون لديهم وصول كامل
    $user = get_userdata($user_id);
    if (in_array('administrator', $user->roles) || in_array('teacher', $user->roles)) {
        return true;
    }

    // التحقق من تسجيل الطالب في المقرر
    global $wpdb;
    $course_id = get_post_meta($lesson_id, '_fiqh_lesson_course_id', true);

    $enrolled = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_enrollments
        WHERE user_id = %d AND course_id = %d AND status = 'active'",
        $user_id,
        $course_id
    ));

    return $enrolled > 0;
}

/**
 * دالة مساعدة لحساب نسبة الإنجاز
 */
function fiqh_get_course_progress($course_id, $user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    global $wpdb;

    // عدد الدروس الكلي
    $total_lessons = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}posts
        WHERE post_type = 'fiqh_lesson' AND post_status = 'publish'
        AND ID IN (SELECT ID FROM {$wpdb->prefix}postmeta WHERE meta_key = '_fiqh_lesson_course_id' AND meta_value = %d)",
        $course_id
    ));

    if ($total_lessons == 0) {
        return 0;
    }

    // عدد الدروس المكتملة
    $completed_lessons = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_progress
        WHERE user_id = %d AND course_id = %d AND status = 'completed'",
        $user_id,
        $course_id
    ));

    return round(($completed_lessons / $total_lessons) * 100);
}

/**
 * دالة مساعدة لتنسيق التاريخ بالعربية
 */
function fiqh_format_date($date) {
    $timestamp = strtotime($date);
    $months = array(
        1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
        5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
        9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
    );

    $day = date('d', $timestamp);
    $month = $months[(int)date('m', $timestamp)];
    $year = date('Y', $timestamp);

    return "$day $month $year";
}

/**
 * تخصيص عدد المقررات والعلوم المعروضة في كل صفحة
 */
function fiqh_custom_posts_per_page($query) {
    if (!is_admin() && $query->is_main_query()) {
        // صفحة أرشيف المقررات: 10 مقررات في الصفحة
        if (is_post_type_archive('fiqh_course')) {
            $query->set('posts_per_page', 10);
        }

        // صفحة أرشيف العلوم (taxonomy): 10 مقررات في الصفحة
        if (is_tax('fiqh_course_science')) {
            $query->set('posts_per_page', 10);
        }
    }
}
add_action('pre_get_posts', 'fiqh_custom_posts_per_page');

/**
 * تعطيل التسجيل الجديد للمستخدمين
 */
function fiqhlearning_disable_registration() {
    return false;
}
add_filter('option_users_can_register', 'fiqhlearning_disable_registration');

// إخفاء خيار التسجيل من صفحة الإعدادات
function fiqhlearning_remove_registration_option() {
    add_filter('pre_option_users_can_register', '__return_zero');
}
add_action('admin_init', 'fiqhlearning_remove_registration_option');

// إعادة توجيه أي محاولة للوصول إلى صفحة التسجيل
function fiqhlearning_block_registration_page() {
    if (isset($_GET['action']) && $_GET['action'] === 'register') {
        wp_redirect(home_url('/login'));
        exit;
    }
}
add_action('login_init', 'fiqhlearning_block_registration_page');

/**
 * تنسيق التعليقات المخصص
 */
function fiqh_custom_comment($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    ?>
    <li <?php comment_class('comment-item'); ?> id="comment-<?php comment_ID(); ?>">
        <article class="comment-body">
            <div class="comment-avatar">
                <?php echo get_avatar($comment, 60, '', '', array('class' => 'avatar')); ?>
            </div>
            <div class="comment-content-wrapper">
                <div class="comment-meta">
                    <div class="comment-author-info">
                        <span class="comment-author-name"><?php echo get_comment_author_link(); ?></span>
                        <?php if (get_comment_author_email() == get_the_author_meta('user_email')) : ?>
                            <span class="author-badge"><?php _e('الكاتب', 'fiqhlearning'); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="comment-metadata">
                        <time datetime="<?php comment_time('c'); ?>">
                            <?php printf(_x('%1$s في %2$s', '1: date, 2: time', 'fiqhlearning'), get_comment_date(), get_comment_time()); ?>
                        </time>
                    </div>
                </div>

                <div class="comment-text">
                    <?php if ($comment->comment_approved == '0') : ?>
                        <p class="comment-awaiting-moderation"><?php _e('تعليقك في انتظار المراجعة.', 'fiqhlearning'); ?></p>
                    <?php endif; ?>
                    <?php comment_text(); ?>
                </div>

                <div class="comment-actions">
                    <?php
                    comment_reply_link(array_merge($args, array(
                        'reply_text' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg> ' . __('رد', 'fiqhlearning'),
                        'depth' => $depth,
                        'max_depth' => $args['max_depth']
                    )));
                    ?>
                    <?php if (current_user_can('edit_comment', $comment->comment_ID)) : ?>
                        <a href="<?php echo get_edit_comment_link(); ?>" class="comment-edit-link">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            <?php _e('تعديل', 'fiqhlearning'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </article>
    <?php
}

/**
 * تضمين ملفات إضافية
 */
require_once FIQH_THEME_DIR . '/inc/customizer.php';
require_once FIQH_THEME_DIR . '/inc/template-functions.php';
require_once FIQH_THEME_DIR . '/inc/ajax-handlers.php';
require_once FIQH_THEME_DIR . '/inc/widgets.php';
