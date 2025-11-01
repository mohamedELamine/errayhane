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
            <h1 class="page-title">اتصل بنا</h1>
            <p class="page-description">نسعد بتواصلكم واستفساراتكم</p>
        </header>

        <div class="contact-content">

            <!-- نموذج الاتصال -->
            <div class="contact-form-section">
                <h2 class="section-title">إرسال رسالة</h2>

                <form id="contact-form" class="contact-form" method="post">
                    <?php wp_nonce_field('fiqh_contact_form', 'contact_nonce'); ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="contact-name">الاسم الكامل <span class="required">*</span></label>
                            <input type="text" id="contact-name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="contact-email">البريد الإلكتروني <span class="required">*</span></label>
                            <input type="email" id="contact-email" name="email" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="contact-phone">رقم الهاتف</label>
                        <input type="tel" id="contact-phone" name="phone">
                    </div>

                    <div class="form-group">
                        <label for="contact-subject">الموضوع <span class="required">*</span></label>
                        <select id="contact-subject" name="subject" required>
                            <option value="">اختر الموضوع</option>
                            <option value="enrollment">استفسار عن التسجيل</option>
                            <option value="courses">استفسار عن المقررات</option>
                            <option value="technical">مشكلة تقنية</option>
                            <option value="complaint">شكوى</option>
                            <option value="suggestion">اقتراح</option>
                            <option value="other">أخرى</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="contact-message">الرسالة <span class="required">*</span></label>
                        <textarea id="contact-message" name="message" rows="6" required></textarea>
                    </div>

                    <div class="form-group">
                        <div class="privacy-notice">
                            <input type="checkbox" id="privacy-agreement" name="privacy" required>
                            <label for="privacy-agreement">
                                أوافق على <a href="<?php echo home_url('/privacy-policy'); ?>">سياسة الخصوصية</a>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="icon-send"></i> إرسال الرسالة
                    </button>

                    <div id="form-message" class="form-message" style="display: none;"></div>
                </form>
            </div>

            <!-- معلومات الاتصال -->
            <aside class="contact-info-section">

                <!-- بطاقة العنوان -->
                <div class="info-card">
                    <div class="info-icon">
                        <i class="icon-location"></i>
                    </div>
                    <div class="info-content">
                        <h3>العنوان</h3>
                        <p>
                            المملكة المغربية<br>
                            الرباط - حي النهضة<br>
                            شارع الفقه المالكي، رقم 123
                        </p>
                    </div>
                </div>

                <!-- بطاقة الهاتف -->
                <div class="info-card">
                    <div class="info-icon">
                        <i class="icon-phone"></i>
                    </div>
                    <div class="info-content">
                        <h3>الهاتف</h3>
                        <p dir="ltr">
                            <a href="tel:+212537123456">+212 537 123 456</a><br>
                            <a href="tel:+212661234567">+212 661 234 567</a>
                        </p>
                    </div>
                </div>

                <!-- بطاقة البريد -->
                <div class="info-card">
                    <div class="info-icon">
                        <i class="icon-email"></i>
                    </div>
                    <div class="info-content">
                        <h3>البريد الإلكتروني</h3>
                        <p>
                            <a href="mailto:info@fiqhlearning.com">info@fiqhlearning.com</a><br>
                            <a href="mailto:support@fiqhlearning.com">support@fiqhlearning.com</a>
                        </p>
                    </div>
                </div>

                <!-- أوقات العمل -->
                <div class="info-card">
                    <div class="info-icon">
                        <i class="icon-clock"></i>
                    </div>
                    <div class="info-content">
                        <h3>أوقات العمل</h3>
                        <p>
                            <strong>من الأحد إلى الخميس:</strong><br>
                            9:00 صباحاً - 5:00 مساءً<br><br>
                            <strong>الجمعة والسبت:</strong><br>
                            مغلق
                        </p>
                    </div>
                </div>

                <!-- روابط التواصل الاجتماعي -->
                <div class="social-links-card">
                    <h3>تابعنا على</h3>
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
        <div class="map-section">
            <h2 class="section-title">موقعنا على الخريطة</h2>
            <div class="map-container">
                <!-- يمكن إضافة Google Maps أو OpenStreetMap هنا -->
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3307.9599999999996!2d-6.8498!3d34.0132!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzTCsDAwJzQ3LjUiTiA2wrA1MCc1OS4zIlc!5e0!3m2!1sen!2sma!4v1234567890"
                    width="100%"
                    height="400"
                    style="border:0; border-radius: 12px;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>

    </div>
</main>

<style>
.contact-page {
    padding: 40px 0;
    background: var(--color-bg-light);
}

.page-header {
    text-align: center;
    margin-bottom: 50px;
}

.page-title {
    font-size: 2.8rem;
    color: var(--color-primary-dark);
    margin-bottom: 15px;
}

.page-description {
    font-size: 1.2rem;
    color: var(--color-text-secondary);
}

.contact-content {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 40px;
    margin-bottom: 50px;
}

.contact-form-section {
    background: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.section-title {
    font-size: 1.8rem;
    color: var(--color-primary-dark);
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 3px solid var(--color-primary);
}

.contact-form {
    max-width: 100%;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--color-text-dark);
}

.required {
    color: #DC2626;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #E5E7EB;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s;
    font-family: 'Cairo', sans-serif;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--color-primary);
}

.form-group textarea {
    resize: vertical;
    min-height: 150px;
}

.privacy-notice {
    display: flex;
    align-items: center;
    gap: 10px;
}

.privacy-notice input[type="checkbox"] {
    width: auto;
}

.privacy-notice label {
    margin-bottom: 0;
    font-weight: normal;
}

.privacy-notice a {
    color: var(--color-primary);
    text-decoration: underline;
}

.form-message {
    margin-top: 20px;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
}

.form-message.success {
    background: #D1FAE5;
    color: #065F46;
    border: 1px solid #10B981;
}

.form-message.error {
    background: #FEE2E2;
    color: #991B1B;
    border: 1px solid #DC2626;
}

.contact-info-section {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.info-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    display: flex;
    gap: 20px;
    align-items: flex-start;
}

.info-icon {
    width: 50px;
    height: 50px;
    background: var(--color-primary-light);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-primary);
    font-size: 24px;
    flex-shrink: 0;
}

.info-content h3 {
    font-size: 1.2rem;
    color: var(--color-primary-dark);
    margin-bottom: 10px;
}

.info-content p {
    color: var(--color-text);
    line-height: 1.8;
}

.info-content a {
    color: var(--color-primary);
    text-decoration: none;
}

.info-content a:hover {
    text-decoration: underline;
}

.social-links-card {
    background: var(--color-primary);
    color: white;
    padding: 25px;
    border-radius: 12px;
    text-align: center;
}

.social-links-card h3 {
    margin-bottom: 20px;
    font-size: 1.2rem;
}

.social-links {
    display: flex;
    justify-content: center;
    gap: 15px;
    flex-wrap: wrap;
}

.social-link {
    width: 45px;
    height: 45px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    transition: transform 0.3s, box-shadow 0.3s;
}

.social-link:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.social-link.telegram { color: #0088cc; }
.social-link.whatsapp { color: #25D366; }
.social-link.facebook { color: #1877F2; }
.social-link.twitter { color: #1DA1F2; }
.social-link.youtube { color: #FF0000; }

.map-section {
    background: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.map-container {
    margin-top: 25px;
}

@media (max-width: 1024px) {
    .contact-content {
        grid-template-columns: 1fr;
    }

    .form-row {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .page-title {
        font-size: 2rem;
    }

    .contact-form-section {
        padding: 25px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contact-form');
    const formMessage = document.getElementById('form-message');

    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(contactForm);
            formData.append('action', 'fiqh_contact_form');

            // إرسال عبر AJAX
            fetch(fiqhData.ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                formMessage.style.display = 'block';

                if (data.success) {
                    formMessage.className = 'form-message success';
                    formMessage.textContent = 'تم إرسال رسالتك بنجاح! سنتواصل معك قريباً.';
                    contactForm.reset();
                } else {
                    formMessage.className = 'form-message error';
                    formMessage.textContent = data.data || 'حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة مرة أخرى.';
                }

                // إخفاء الرسالة بعد 5 ثوانٍ
                setTimeout(() => {
                    formMessage.style.display = 'none';
                }, 5000);
            })
            .catch(error => {
                formMessage.style.display = 'block';
                formMessage.className = 'form-message error';
                formMessage.textContent = 'حدث خطأ في الاتصال. يرجى المحاولة لاحقاً.';
            });
        });
    }
});
</script>

<?php get_footer(); ?>
