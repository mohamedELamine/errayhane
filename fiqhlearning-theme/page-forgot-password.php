<?php
/**
 * Template Name: استعادة كلمة المرور
 * صفحة استعادة كلمة المرور المخصصة
 *
 * @package FiqhLearning
 */

// إعادة توجيه المستخدمين المسجلين
if (is_user_logged_in()) {
    wp_redirect(home_url('/dashboard'));
    exit;
}

get_header();

// معالجة طلب استعادة كلمة المرور
$reset_status = isset($_GET['reset']) ? $_GET['reset'] : '';
$message = '';
$message_type = '';

switch ($reset_status) {
    case 'sent':
        $message = __('تم إرسال رابط استعادة كلمة المرور إلى بريدك الإلكتروني', 'fiqhlearning');
        $message_type = 'success';
        break;
    case 'invalid':
        $message = __('البريد الإلكتروني المدخل غير صحيح أو غير مسجل', 'fiqhlearning');
        $message_type = 'error';
        break;
    case 'empty':
        $message = __('يرجى إدخال البريد الإلكتروني', 'fiqhlearning');
        $message_type = 'error';
        break;
}

// معالجة نموذج استعادة كلمة المرور
if (isset($_POST['reset_password_submit'])) {
    // التحقق من nonce
    if (!isset($_POST['reset_password_nonce']) || !wp_verify_nonce($_POST['reset_password_nonce'], 'reset_password_action')) {
        $message = __('حدث خطأ أثناء معالجة الطلب', 'fiqhlearning');
        $message_type = 'error';
    } else {
        $user_email = sanitize_email($_POST['user_email']);

        if (empty($user_email)) {
            wp_redirect(add_query_arg('reset', 'empty', get_permalink()));
            exit;
        }

        // التحقق من وجود المستخدم
        $user = get_user_by('email', $user_email);

        if (!$user) {
            wp_redirect(add_query_arg('reset', 'invalid', get_permalink()));
            exit;
        }

        // إرسال رابط استعادة كلمة المرور
        $reset_sent = retrieve_password($user_email);

        if (is_wp_error($reset_sent)) {
            wp_redirect(add_query_arg('reset', 'invalid', get_permalink()));
            exit;
        } else {
            wp_redirect(add_query_arg('reset', 'sent', get_permalink()));
            exit;
        }
    }
}
?>

<main class="forgot-password-page">
    <div class="forgot-password-container">
        <div class="forgot-password-box card">
            <!-- شعار المنصة -->
            <div class="forgot-password-header">
                <div class="forgot-password-logo">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                </div>
                <h1 class="forgot-password-title"><?php _e('استعادة كلمة المرور', 'fiqhlearning'); ?></h1>
                <p class="forgot-password-subtitle"><?php _e('أدخل بريدك الإلكتروني وسنرسل لك رابط استعادة كلمة المرور', 'fiqhlearning'); ?></p>
            </div>

            <!-- رسائل الخطأ والنجاح -->
            <?php if ($message) : ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <?php if ($message_type === 'success') : ?>
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        <?php else : ?>
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        <?php endif; ?>
                    </svg>
                    <?php echo esc_html($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($reset_status !== 'sent') : ?>
                <!-- نموذج استعادة كلمة المرور -->
                <form class="forgot-password-form" method="post" action="">
                    <?php wp_nonce_field('reset_password_action', 'reset_password_nonce'); ?>

                    <div class="form-group">
                        <label for="user_email" class="form-label">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                            <?php _e('البريد الإلكتروني', 'fiqhlearning'); ?>
                        </label>
                        <input
                            type="email"
                            id="user_email"
                            name="user_email"
                            class="form-control"
                            required
                            autocomplete="email"
                            placeholder="<?php _e('أدخل بريدك الإلكتروني', 'fiqhlearning'); ?>"
                        >
                    </div>

                    <button type="submit" name="reset_password_submit" class="btn btn-primary btn-block">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <?php _e('إرسال رابط الاستعادة', 'fiqhlearning'); ?>
                    </button>
                </form>
            <?php else : ?>
                <!-- رسالة النجاح -->
                <div class="success-message">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <p><?php _e('تحقق من بريدك الإلكتروني واتبع التعليمات لاستعادة كلمة المرور', 'fiqhlearning'); ?></p>
                </div>
            <?php endif; ?>

            <!-- روابط إضافية -->
            <div class="forgot-password-footer">
                <p>
                    <a href="<?php echo home_url('/login'); ?>" class="back-to-login-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                        <?php _e('العودة إلى تسجيل الدخول', 'fiqhlearning'); ?>
                    </a>
                </p>
            </div>
        </div>

        <!-- معلومات إضافية -->
        <div class="forgot-password-info">
            <div class="info-card card">
                <div class="info-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </div>
                <h3><?php _e('هل تحتاج مساعدة؟', 'fiqhlearning'); ?></h3>
                <p><?php _e('إذا لم تتلق رسالة البريد الإلكتروني، تحقق من مجلد البريد المزعج أو تواصل مع الإدارة', 'fiqhlearning'); ?></p>
            </div>

            <div class="info-card card">
                <div class="info-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </div>
                <h3><?php _e('أمان الحساب', 'fiqhlearning'); ?></h3>
                <p><?php _e('استخدم كلمة مرور قوية تحتوي على أحرف وأرقام ورموز لحماية حسابك', 'fiqhlearning'); ?></p>
            </div>

            <div class="info-card card">
                <div class="info-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                </div>
                <h3><?php _e('تواصل معنا', 'fiqhlearning'); ?></h3>
                <p><?php _e('للحصول على المساعدة، يمكنك التواصل مع فريق الدعم الفني عبر صفحة الاتصال', 'fiqhlearning'); ?></p>
            </div>
        </div>
    </div>
</main>

<style>
/* تنسيق صفحة استعادة كلمة المرور */
.forgot-password-page {
    min-height: 80vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--spacing-2xl) var(--spacing-lg);
    background: linear-gradient(135deg, var(--color-primary-light) 0%, var(--color-background) 100%);
}

.forgot-password-container {
    width: 100%;
    max-width: 1200px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--spacing-2xl);
    align-items: start;
}

.forgot-password-box {
    padding: var(--spacing-2xl);
}

.forgot-password-header {
    text-align: center;
    margin-bottom: var(--spacing-2xl);
}

.forgot-password-logo {
    color: var(--color-primary);
    margin-bottom: var(--spacing-lg);
}

.forgot-password-title {
    font-size: var(--font-size-2xl);
    margin-bottom: var(--spacing-sm);
    color: var(--color-text);
}

.forgot-password-subtitle {
    color: var(--color-text-light);
    font-size: var(--font-size-base);
}

.forgot-password-form {
    margin-top: var(--spacing-xl);
}

.success-message {
    text-align: center;
    padding: var(--spacing-xl);
}

.success-message svg {
    color: var(--color-success);
    margin-bottom: var(--spacing-lg);
}

.forgot-password-footer {
    margin-top: var(--spacing-xl);
    text-align: center;
    padding-top: var(--spacing-lg);
    border-top: 1px solid var(--color-border);
}

.back-to-login-link {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    color: var(--color-primary);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.back-to-login-link:hover {
    gap: var(--spacing-sm);
    color: var(--color-primary-dark);
}

.forgot-password-info {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-lg);
}

.forgot-password-info .info-card {
    padding: var(--spacing-xl);
    text-align: center;
}

.forgot-password-info .info-icon {
    color: var(--color-primary);
    margin-bottom: var(--spacing-md);
}

.forgot-password-info h3 {
    font-size: var(--font-size-lg);
    margin-bottom: var(--spacing-sm);
    color: var(--color-text);
}

.forgot-password-info p {
    color: var(--color-text-light);
    font-size: var(--font-size-sm);
    line-height: 1.6;
}

/* وضع داكن */
body.dark-mode .forgot-password-page {
    background: linear-gradient(135deg, var(--color-primary-dark) 0%, var(--color-background-dark) 100%);
}

/* استجابة للأجهزة المحمولة */
@media (max-width: 968px) {
    .forgot-password-container {
        grid-template-columns: 1fr;
    }

    .forgot-password-info {
        display: none;
    }
}
</style>

<?php
get_footer();
