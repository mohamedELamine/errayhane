<?php
/**
 * Template Name: تسجيل الدخول
 * صفحة تسجيل الدخول المخصصة للطلاب
 *
 * @package FiqhLearning
 */

// إعادة توجيه المستخدمين المسجلين إلى لوحة التحكم
if (is_user_logged_in()) {
    wp_redirect(home_url('/dashboard'));
    exit;
}

get_header();

// معالجة رسائل الخطأ
$login_error = isset($_GET['login']) ? $_GET['login'] : '';
$error_message = '';

switch ($login_error) {
    case 'failed':
        $error_message = __('اسم المستخدم أو كلمة المرور غير صحيحة', 'fiqhlearning');
        break;
    case 'empty':
        $error_message = __('يرجى إدخال اسم المستخدم وكلمة المرور', 'fiqhlearning');
        break;
    case 'inactive':
        $error_message = __('حسابك غير مفعل. يرجى التواصل مع الإدارة', 'fiqhlearning');
        break;
    case 'logged_out':
        $error_message = __('تم تسجيل الخروج بنجاح', 'fiqhlearning');
        break;
}
?>

<main class="login-page">
    <div class="login-container">
        <div class="login-box card">
            <!-- شعار المنصة -->
            <div class="login-header">
                <div class="login-logo">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                </div>
                <h1 class="login-title"><?php echo esc_html(get_theme_mod('login_page_title', __('تسجيل الدخول', 'fiqhlearning'))); ?></h1>
                <p class="login-subtitle"><?php echo esc_html(get_theme_mod('login_page_subtitle', __('مرحباً بك في منصة تعلم الفقه', 'fiqhlearning'))); ?></p>
            </div>

            <!-- رسائل الخطأ والنجاح -->
            <?php if ($error_message) : ?>
                <div class="alert <?php echo $login_error === 'logged_out' ? 'alert-success' : 'alert-error'; ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <?php if ($login_error === 'logged_out') : ?>
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        <?php else : ?>
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        <?php endif; ?>
                    </svg>
                    <?php echo esc_html($error_message); ?>
                </div>
            <?php endif; ?>

            <!-- نموذج تسجيل الدخول -->
            <form class="login-form" method="post" action="<?php echo esc_url(home_url('/wp-login.php')); ?>">
                <input type="hidden" name="redirect_to" value="<?php echo esc_url(home_url('/dashboard')); ?>">

                <div class="form-group">
                    <label for="user_login" class="form-label">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <?php echo esc_html(get_theme_mod('login_username_label', __('اسم المستخدم أو البريد الإلكتروني', 'fiqhlearning'))); ?>
                    </label>
                    <input
                        type="text"
                        id="user_login"
                        name="log"
                        class="form-control"
                        required
                        autocomplete="username"
                        placeholder="<?php echo esc_attr(get_theme_mod('login_username_placeholder', __('أدخل اسم المستخدم', 'fiqhlearning'))); ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="user_pass" class="form-label">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <?php echo esc_html(get_theme_mod('login_password_label', __('كلمة المرور', 'fiqhlearning'))); ?>
                    </label>
                    <input
                        type="password"
                        id="user_pass"
                        name="pwd"
                        class="form-control"
                        required
                        autocomplete="current-password"
                        placeholder="<?php echo esc_attr(get_theme_mod('login_password_placeholder', __('أدخل كلمة المرور', 'fiqhlearning'))); ?>"
                    >
                </div>

                <div class="form-group-inline">
                    <label class="checkbox-label">
                        <input type="checkbox" name="rememberme" value="forever">
                        <span><?php echo esc_html(get_theme_mod('login_remember_me_text', __('تذكرني', 'fiqhlearning'))); ?></span>
                    </label>
                    <a href="<?php echo home_url('/forgot-password'); ?>" class="forgot-link">
                        <?php echo esc_html(get_theme_mod('login_forgot_password_text', __('نسيت كلمة المرور؟', 'fiqhlearning'))); ?>
                    </a>
                </div>

                <?php wp_nonce_field('login', 'login_nonce'); ?>

                <button type="submit" class="btn btn-primary btn-block">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                        <polyline points="10 17 15 12 10 7"></polyline>
                        <line x1="15" y1="12" x2="3" y2="12"></line>
                    </svg>
                    <?php echo esc_html(get_theme_mod('login_button_text', __('تسجيل الدخول', 'fiqhlearning'))); ?>
                </button>
            </form>

            <!-- روابط إضافية -->
            <div class="login-footer">
                <p>
                    <?php echo esc_html(get_theme_mod('login_no_account_text', __('ليس لديك حساب؟', 'fiqhlearning'))); ?>
                    <a href="<?php echo home_url('/'); ?>" class="register-link">
                        <?php echo esc_html(get_theme_mod('login_contact_admin_text', __('تواصل مع الإدارة', 'fiqhlearning'))); ?>
                    </a>
                </p>
            </div>
        </div>

        <!-- معلومات إضافية -->
        <div class="login-info">
            <div class="info-card card">
                <div class="info-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                </div>
                <h3><?php echo esc_html(get_theme_mod('login_info_1_title', __('تعلم الفقه بسهولة', 'fiqhlearning'))); ?></h3>
                <p><?php echo esc_html(get_theme_mod('login_info_1_desc', __('منصة تعليمية متكاملة لدراسة الفقه الإسلامي بأسلوب عصري وميسر', 'fiqhlearning'))); ?></p>
            </div>

            <div class="info-card card">
                <div class="info-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <h3><?php echo esc_html(get_theme_mod('login_info_2_title', __('معلمون متخصصون', 'fiqhlearning'))); ?></h3>
                <p><?php echo esc_html(get_theme_mod('login_info_2_desc', __('نخبة من المعلمين المتخصصين في الفقه والعلوم الشرعية', 'fiqhlearning'))); ?></p>
            </div>

            <div class="info-card card">
                <div class="info-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <h3><?php echo esc_html(get_theme_mod('login_info_3_title', __('تعلم بالسرعة المناسبة', 'fiqhlearning'))); ?></h3>
                <p><?php echo esc_html(get_theme_mod('login_info_3_desc', __('ادرس في أي وقت ومن أي مكان بالسرعة التي تناسبك', 'fiqhlearning'))); ?></p>
            </div>
        </div>
    </div>
</main>

<?php
get_footer();
