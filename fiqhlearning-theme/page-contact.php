<?php
/**
 * Template Name: اتصل بنا
 * Contact Page
 *
 * @package FiqhLearning
 */

get_header();
?>

<main class="contact-page">
    <div class="container">

        <header class="page-header">
            <h1 class="page-title"><?php echo get_theme_mod('contact_page_title', __('اتصل بنا', 'fiqhlearning')); ?></h1>
            <p class="page-description"><?php echo get_theme_mod('contact_page_description', __('نسعد بتواصلكم واستفساراتكم', 'fiqhlearning')); ?></p>
        </header>

        <div class="contact-content">

            <!-- نموذج الاتصال -->
            <div class="contact-form-section">
                <h2 class="section-title"><?php echo get_theme_mod('contact_form_title', __('إرسال رسالة', 'fiqhlearning')); ?></h2>

                <form id="contact-form" class="contact-form" method="post">
                    <?php wp_nonce_field('fiqh_contact_form', 'contact_nonce'); ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="contact-name"><?php echo get_theme_mod('contact_label_name', __('الاسم الكامل', 'fiqhlearning')); ?> <span class="required">*</span></label>
                            <input type="text" id="contact-name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="contact-email"><?php echo get_theme_mod('contact_label_email', __('البريد الإلكتروني', 'fiqhlearning')); ?> <span class="required">*</span></label>
                            <input type="email" id="contact-email" name="email" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="contact-phone"><?php echo get_theme_mod('contact_label_phone', __('رقم الهاتف', 'fiqhlearning')); ?></label>
                        <input type="tel" id="contact-phone" name="phone">
                    </div>

                    <div class="form-group">
                        <label for="contact-subject"><?php echo get_theme_mod('contact_label_subject', __('الموضوع', 'fiqhlearning')); ?> <span class="required">*</span></label>
                        <select id="contact-subject" name="subject" required>
                            <option value=""><?php echo get_theme_mod('contact_subject_placeholder', __('اختر الموضوع', 'fiqhlearning')); ?></option>
                            <option value="enrollment"><?php echo get_theme_mod('contact_subject_enrollment', __('استفسار عن التسجيل', 'fiqhlearning')); ?></option>
                            <option value="courses"><?php echo get_theme_mod('contact_subject_courses', __('استفسار عن المقررات', 'fiqhlearning')); ?></option>
                            <option value="technical"><?php echo get_theme_mod('contact_subject_technical', __('مشكلة تقنية', 'fiqhlearning')); ?></option>
                            <option value="complaint"><?php echo get_theme_mod('contact_subject_complaint', __('شكوى', 'fiqhlearning')); ?></option>
                            <option value="suggestion"><?php echo get_theme_mod('contact_subject_suggestion', __('اقتراح', 'fiqhlearning')); ?></option>
                            <option value="other"><?php echo get_theme_mod('contact_subject_other', __('أخرى', 'fiqhlearning')); ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="contact-message"><?php echo get_theme_mod('contact_label_message', __('الرسالة', 'fiqhlearning')); ?> <span class="required">*</span></label>
                        <textarea id="contact-message" name="message" rows="6" required></textarea>
                    </div>

                    <div class="form-group">
                        <div class="privacy-notice">
                            <input type="checkbox" id="privacy-agreement" name="privacy" required>
                            <label for="privacy-agreement">
                                <?php echo get_theme_mod('contact_privacy_text', __('أوافق على', 'fiqhlearning')); ?> <a href="<?php echo home_url('/privacy-policy'); ?>"><?php _e('سياسة الخصوصية', 'fiqhlearning'); ?></a>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="icon-send"></i> <?php echo get_theme_mod('contact_button_submit', __('إرسال الرسالة', 'fiqhlearning')); ?>
                    </button>

                    <div id="form-message" class="form-message" style="display: none;"></div>
                </form>
            </div>

            <!-- معلومات الاتصال -->
            <aside class="contact-info-section">

                <!-- بطاقة العنوان -->
                <div class="info-card">
                    <div class="info-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                    </div>
                    <div class="info-content">
                        <h3><?php echo get_theme_mod('contact_label_address', __('العنوان', 'fiqhlearning')); ?></h3>
                        <p><?php echo nl2br(esc_html(get_theme_mod('contact_address', __('المملكة المغربية - الرباط', 'fiqhlearning')))); ?></p>
                    </div>
                </div>

                <!-- بطاقة الهاتف -->
                <?php $contact_phone = get_theme_mod('contact_phone', '+212 661 234 567'); ?>
                <?php if ($contact_phone) : ?>
                <div class="info-card">
                    <div class="info-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                    </div>
                    <div class="info-content">
                        <h3><?php echo get_theme_mod('contact_label_phone_info', __('الهاتف', 'fiqhlearning')); ?></h3>
                        <p dir="ltr">
                            <?php
                            $phones = explode("\n", $contact_phone);
                            foreach ($phones as $phone) {
                                $phone = trim($phone);
                                if ($phone) {
                                    $clean_phone = preg_replace('/[^0-9+]/', '', $phone);
                                    echo '<a href="tel:' . esc_attr($clean_phone) . '">' . esc_html($phone) . '</a><br>';
                                }
                            }
                            ?>
                        </p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- بطاقة البريد -->
                <div class="info-card">
                    <div class="info-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </div>
                    <div class="info-content">
                        <h3><?php echo get_theme_mod('contact_label_email_info', __('البريد الإلكتروني', 'fiqhlearning')); ?></h3>
                        <p>
                            <?php $primary_email = get_theme_mod('contact_email_primary', 'rayhaneschool@gmail.com'); ?>
                            <a href="mailto:<?php echo esc_attr($primary_email); ?>"><?php echo esc_html($primary_email); ?></a>
                            <?php
                            $contact_email_secondary = get_theme_mod('contact_email_secondary');
                            if ($contact_email_secondary) {
                                echo '<br><a href="mailto:' . esc_attr($contact_email_secondary) . '">' . esc_html($contact_email_secondary) . '</a>';
                            }
                            ?>
                        </p>
                    </div>
                </div>

                <!-- أوقات العمل -->
                <?php $work_hours = get_theme_mod('contact_work_hours'); ?>
                <?php if ($work_hours) : ?>
                <div class="info-card">
                    <div class="info-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div class="info-content">
                        <h3><?php echo get_theme_mod('contact_label_work_hours', __('أوقات العمل', 'fiqhlearning')); ?></h3>
                        <p><?php echo nl2br(esc_html($work_hours)); ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- روابط التواصل الاجتماعي -->
                <div class="social-links-card">
                    <h3><?php echo get_theme_mod('contact_social_title', __('تابعنا على', 'fiqhlearning')); ?></h3>
                    <div class="social-links">
                        <?php
                        $telegram = get_theme_mod('fiqh_telegram_url');
                        $whatsapp = get_theme_mod('fiqh_whatsapp_url');
                        $facebook = get_theme_mod('fiqh_facebook_url');
                        $twitter = get_theme_mod('fiqh_twitter_url');
                        $youtube = get_theme_mod('fiqh_youtube_url');

                        if ($telegram): ?>
                            <a href="<?php echo esc_url($telegram); ?>" target="_blank" class="social-link telegram">
                                <i class="icon-telegram"></i>
                            </a>
                        <?php endif;

                        if ($whatsapp): ?>
                            <a href="<?php echo esc_url($whatsapp); ?>" target="_blank" class="social-link whatsapp">
                                <i class="icon-whatsapp"></i>
                            </a>
                        <?php endif;

                        if ($facebook): ?>
                            <a href="<?php echo esc_url($facebook); ?>" target="_blank" class="social-link facebook">
                                <i class="icon-facebook"></i>
                            </a>
                        <?php endif;

                        if ($twitter): ?>
                            <a href="<?php echo esc_url($twitter); ?>" target="_blank" class="social-link twitter">
                                <i class="icon-twitter"></i>
                            </a>
                        <?php endif;

                        if ($youtube): ?>
                            <a href="<?php echo esc_url($youtube); ?>" target="_blank" class="social-link youtube">
                                <i class="icon-youtube"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

            </aside>

        </div>

        <!-- الخريطة (اختياري) -->
        <?php if (get_theme_mod('contact_map_enabled', true)) :
            $map_url = get_theme_mod('contact_map_url', '');
        ?>
        <div class="map-section">
            <h2 class="section-title"><?php echo get_theme_mod('contact_map_title', __('موقعنا على الخريطة', 'fiqhlearning')); ?></h2>
            <div class="map-container">
                <?php if ($map_url) : ?>
                    <iframe
                        src="<?php echo esc_url($map_url); ?>"
                        width="100%"
                        height="400"
                        style="border:0; border-radius: 12px;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                <?php else : ?>
                    <div class="map-placeholder" style="background-color: var(--color-light); padding: var(--spacing-3xl); text-align: center; border-radius: 12px;">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto var(--spacing-md); color: var(--color-border);">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <p style="color: var(--color-dark-gray);">
                            <?php _e('لم يتم تحديد موقع على الخريطة بعد. يرجى إضافة رابط الخريطة من إعدادات التخصيص.', 'fiqhlearning'); ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</main>

<script>
jQuery(document).ready(function($) {
    $('#contact-form').on('submit', function(e) {
        e.preventDefault();

        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        var originalBtnText = $submitBtn.html();
        var $formMessage = $('#form-message');

        // تعطيل الزر
        $submitBtn.prop('disabled', true).html('<svg class="spinner" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" opacity="0.25"></circle><path d="M4 12a8 8 0 0 1 8-8" opacity="0.75"></path></svg> <?php echo get_theme_mod('contact_msg_sending', __('جاري الإرسال...', 'fiqhlearning')); ?>');

        // جمع البيانات
        var formData = {
            action: 'fiqh_contact_form',
            nonce: fiqhData.nonce,
            name: $('#contact-name').val(),
            email: $('#contact-email').val(),
            phone: $('#contact-phone').val(),
            subject: $('#contact-subject').val(),
            message: $('#contact-message').val(),
            privacy: $('#privacy-agreement').is(':checked') ? 'on' : ''
        };

        // إرسال عبر AJAX
        $.ajax({
            url: fiqhData.ajaxUrl,
            type: 'POST',
            data: formData,
            success: function(response) {
                $formMessage.show();

                if (response.success) {
                    $formMessage.removeClass('error').addClass('success');
                    $formMessage.text(response.data || '<?php echo get_theme_mod('contact_msg_success', __('تم إرسال رسالتك بنجاح! سنتواصل معك قريباً.', 'fiqhlearning')); ?>');
                    $form[0].reset();
                } else {
                    $formMessage.removeClass('success').addClass('error');
                    $formMessage.text(response.data || '<?php echo get_theme_mod('contact_msg_error', __('حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة مرة أخرى.', 'fiqhlearning')); ?>');
                }

                // إخفاء الرسالة بعد 7 ثوانٍ
                setTimeout(function() {
                    $formMessage.fadeOut();
                }, 7000);
            },
            error: function() {
                $formMessage.show().removeClass('success').addClass('error');
                $formMessage.text('<?php echo get_theme_mod('contact_msg_connection_error', __('حدث خطأ في الاتصال. يرجى المحاولة لاحقاً.', 'fiqhlearning')); ?>');
            },
            complete: function() {
                $submitBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });
});
</script>

<?php get_footer(); ?>
