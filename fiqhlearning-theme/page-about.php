<?php
/**
 * Template Name: عن المدرسة
 * About Page with Timeline
 *
 * @package FiqhLearning
 */

get_header();
?>

<main class="about-page">

    <!-- Hero Section -->
    <section class="about-hero">
        <div class="container">
            <div class="about-hero-content">
                <div class="hero-badge">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                    </svg>
                    <?php echo esc_html(get_theme_mod('about_hero_badge', __('عن المدرسة', 'fiqhlearning'))); ?>
                </div>
                <h1 class="page-title">
                    <?php echo get_theme_mod('about_page_title', __('مدرسة الريحان للعلوم الشرعية', 'fiqhlearning')); ?>
                </h1>
                <p class="hero-description">
                    <?php echo get_theme_mod('about_page_description', __('منصة تعليمية متخصصة في تعليم الفقه المالكي والعلوم الشرعية منذ تأسيسها', 'fiqhlearning')); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- مقدمة عن المدرسة -->
    <section class="about-intro">
        <div class="container">
            <div class="about-intro-grid">
                <div class="intro-image">
                    <?php if (get_theme_mod('about_intro_image')) : ?>
                        <img src="<?php echo esc_url(get_theme_mod('about_intro_image')); ?>" alt="<?php echo esc_attr(get_theme_mod('about_hero_badge', __('عن المدرسة', 'fiqhlearning'))); ?>">
                    <?php else : ?>
                        <svg viewBox="0 0 500 400" xmlns="http://www.w3.org/2000/svg">
                            <rect x="50" y="50" width="400" height="300" rx="20" fill="var(--color-primary)" opacity="0.1"/>
                            <path d="M250 100 L150 200 L150 300 L350 300 L350 200 Z" fill="var(--color-primary)" opacity="0.3"/>
                            <circle cx="250" cy="180" r="40" fill="var(--color-accent)" opacity="0.5"/>
                            <rect x="200" y="250" width="100" height="50" rx="5" fill="var(--color-primary)" opacity="0.4"/>
                        </svg>
                    <?php endif; ?>
                </div>
                <div class="intro-content">
                    <h2><?php echo get_theme_mod('about_intro_title', __('رسالتنا', 'fiqhlearning')); ?></h2>
                    <p><?php echo get_theme_mod('about_intro_text', __('نسعى في مدرسة الريحان إلى نشر العلم الشرعي الأصيل وتعليم الفقه المالكي بطريقة عصرية ومبسطة، مع المحافظة على الأصالة والمنهجية العلمية الصحيحة.', 'fiqhlearning')); ?></p>

                    <div class="intro-features">
                        <div class="intro-feature">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <span><?php echo esc_html(get_theme_mod('about_intro_feature_1', __('منهج أصيل ومعتمد', 'fiqhlearning'))); ?></span>
                        </div>
                        <div class="intro-feature">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <span><?php echo esc_html(get_theme_mod('about_intro_feature_2', __('أساتذة متخصصون', 'fiqhlearning'))); ?></span>
                        </div>
                        <div class="intro-feature">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <span><?php echo esc_html(get_theme_mod('about_intro_feature_3', __('محتوى تعليمي شامل', 'fiqhlearning'))); ?></span>
                        </div>
                        <div class="intro-feature">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <span><?php echo esc_html(get_theme_mod('about_intro_feature_4', __('شهادات معتمدة', 'fiqhlearning'))); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Timeline - مسيرة المدرسة -->
    <section class="about-timeline">
        <div class="container">
            <div class="section-header">
                <h2>
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <?php echo get_theme_mod('about_timeline_title', __('مسيرة المدرسة', 'fiqhlearning')); ?>
                </h2>
                <p><?php echo get_theme_mod('about_timeline_description', __('رحلتنا منذ التأسيس حتى اليوم', 'fiqhlearning')); ?></p>
            </div>

            <div class="timeline">
                <?php
                // جلب أحداث Timeline من Customizer
                $timeline_events = array();

                // Default events
                $default_events = array(
                    array(
                        'year' => '2015',
                        'title' => 'التأسيس',
                        'description' => 'تأسيس مدرسة الريحان للعلوم الشرعية بهدف نشر العلم الشرعي'
                    ),
                    array(
                        'year' => '2017',
                        'title' => 'إطلاق المنصة الإلكترونية',
                        'description' => 'إطلاق المنصة التعليمية الإلكترونية الأولى لتعليم الفقه المالكي'
                    ),
                    array(
                        'year' => '2019',
                        'title' => 'توسع البرامج',
                        'description' => 'إضافة برامج جديدة في العقيدة والحديث والتفسير'
                    ),
                    array(
                        'year' => '2021',
                        'title' => 'اعتماد الشهادات',
                        'description' => 'الحصول على اعتماد رسمي لشهادات المدرسة'
                    ),
                    array(
                        'year' => '2023',
                        'title' => 'النسخة المطورة',
                        'description' => 'إطلاق النسخة المطورة من المنصة مع مزايا تعليمية متقدمة'
                    ),
                );

                // Get events from customizer (up to 6 events)
                for ($i = 1; $i <= 6; $i++) {
                    $year = get_theme_mod("about_timeline_event_{$i}_year", '');
                    $title = get_theme_mod("about_timeline_event_{$i}_title", '');
                    $description = get_theme_mod("about_timeline_event_{$i}_description", '');

                    if (!empty($year) && !empty($title)) {
                        $timeline_events[] = array(
                            'year' => $year,
                            'title' => $title,
                            'description' => $description
                        );
                    }
                }

                // If no customizer events, use defaults
                if (empty($timeline_events)) {
                    $timeline_events = $default_events;
                }

                if (!empty($timeline_events)) :
                    foreach ($timeline_events as $index => $event) :
                        $year = isset($event['year']) ? $event['year'] : '';
                        $title = isset($event['title']) ? $event['title'] : '';
                        $description = isset($event['description']) ? $event['description'] : '';
                        ?>
                        <div class="timeline-item <?php echo ($index % 2 == 0) ? 'left' : 'right'; ?>">
                            <div class="timeline-content card">
                                <div class="timeline-year"><?php echo esc_html($year); ?></div>
                                <h3 class="timeline-title"><?php echo esc_html($title); ?></h3>
                                <p class="timeline-description"><?php echo esc_html($description); ?></p>
                            </div>
                            <div class="timeline-dot"></div>
                        </div>
                    <?php
                    endforeach;
                endif;
                ?>
            </div>
        </div>
    </section>

    <!-- رؤيتنا وقيمنا -->
    <section class="about-values">
        <div class="container">
            <h2 class="section-title"><?php echo esc_html(get_theme_mod('about_values_section_title', __('قيمنا ورؤيتنا', 'fiqhlearning'))); ?></h2>
            <div class="values-grid">
                <div class="value-card card">
                    <div class="value-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                    </div>
                    <h3><?php echo esc_html(get_theme_mod('about_value_1_title', __('الأصالة العلمية', 'fiqhlearning'))); ?></h3>
                    <p><?php echo esc_html(get_theme_mod('about_value_1_desc', __('الالتزام بالمنهج العلمي الأصيل في تدريس الفقه المالكي', 'fiqhlearning'))); ?></p>
                </div>

                <div class="value-card card">
                    <div class="value-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <h3><?php echo esc_html(get_theme_mod('about_value_2_title', __('التميز التعليمي', 'fiqhlearning'))); ?></h3>
                    <p><?php echo esc_html(get_theme_mod('about_value_2_desc', __('تقديم محتوى تعليمي عالي الجودة بأساليب عصرية ومبتكرة', 'fiqhlearning'))); ?></p>
                </div>

                <div class="value-card card">
                    <div class="value-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                        </svg>
                    </div>
                    <h3><?php echo esc_html(get_theme_mod('about_value_3_title', __('الجودة والإتقان', 'fiqhlearning'))); ?></h3>
                    <p><?php echo esc_html(get_theme_mod('about_value_3_desc', __('الحرص على إتقان العمل وتقديم أفضل الخدمات التعليمية', 'fiqhlearning'))); ?></p>
                </div>

                <div class="value-card card">
                    <div class="value-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                        </svg>
                    </div>
                    <h3><?php echo esc_html(get_theme_mod('about_value_4_title', __('التطوير المستمر', 'fiqhlearning'))); ?></h3>
                    <p><?php echo esc_html(get_theme_mod('about_value_4_desc', __('السعي الدائم لتطوير المحتوى والخدمات التعليمية', 'fiqhlearning'))); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- دعوة للانضمام -->
    <section class="about-cta">
        <div class="container">
            <div class="cta-box">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                </svg>
                <h2><?php echo esc_html(get_theme_mod('about_cta_title', __('انضم إلى رحلتنا التعليمية', 'fiqhlearning'))); ?></h2>
                <p><?php echo esc_html(get_theme_mod('about_cta_description', __('ابدأ رحلتك في تعلم الفقه المالكي والعلوم الشرعية مع مدرسة الريحان', 'fiqhlearning'))); ?></p>
                <div class="cta-buttons">
                    <a href="<?php echo get_post_type_archive_link('fiqh_course'); ?>" class="btn btn-primary btn-lg">
                        <?php echo esc_html(get_theme_mod('about_cta_button_1', __('تصفح المقررات', 'fiqhlearning'))); ?>
                    </a>
                    <?php
                    $contact_page = get_page_by_path('contact');
                    if ($contact_page) :
                    ?>
                        <a href="<?php echo get_permalink($contact_page); ?>" class="btn btn-secondary btn-lg">
                            <?php echo esc_html(get_theme_mod('about_cta_button_2', __('اتصل بنا', 'fiqhlearning'))); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
