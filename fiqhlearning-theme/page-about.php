<?php
/**
 * Template Name: عن المدرسة
 * صفحة عن المدرسة مع Timeline
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
                <h1 class="about-hero-title">
                    <?php echo get_theme_mod('about_hero_title', __('مدرسة الريحان للعلوم الشرعية', 'fiqhlearning')); ?>
                </h1>
                <p class="about-hero-subtitle">
                    <?php echo get_theme_mod('about_hero_subtitle', __('منارة علم ومعرفة في الفقه المالكي والعلوم الشرعية', 'fiqhlearning')); ?>
                </p>
            </div>
            <div class="about-hero-image">
                <?php if (get_theme_mod('about_hero_image')) : ?>
                    <img src="<?php echo esc_url(get_theme_mod('about_hero_image')); ?>" alt="<?php _e('مدرسة الريحان', 'fiqhlearning'); ?>">
                <?php else : ?>
                    <svg viewBox="0 0 500 400" xmlns="http://www.w3.org/2000/svg" class="about-illustration">
                        <!-- مسجد -->
                        <path d="M250 50 L200 100 L200 350 L300 350 L300 100 Z" fill="var(--color-primary)" opacity="0.1"/>
                        <path d="M150 100 L350 100" stroke="var(--color-primary)" stroke-width="4"/>
                        <circle cx="250" cy="30" r="15" fill="var(--color-accent)"/>
                        <rect x="230" y="150" width="40" height="80" rx="5" fill="var(--color-primary)" opacity="0.3"/>
                        <!-- قبة -->
                        <path d="M250 50 Q200 80, 200 100 L300 100 Q300 80, 250 50 Z" fill="var(--color-primary)" opacity="0.2"/>
                    </svg>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- المقدمة -->
    <section class="about-intro section">
        <div class="container">
            <div class="about-intro-content">
                <h2><?php echo get_theme_mod('about_intro_title', __('من نحن', 'fiqhlearning')); ?></h2>
                <div class="about-text">
                    <?php
                    echo wpautop(get_theme_mod('about_intro_text', __('مدرسة الريحان للعلوم الشرعية هي منصة تعليمية إلكترونية متخصصة في تعليم الفقه المالكي والعلوم الشرعية. نسعى لتقديم تعليم عالي الجودة يجمع بين الأصالة والمعاصرة، من خلال دروس مرئية ومسموعة ومواد تعليمية متنوعة.', 'fiqhlearning')));
                    ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Timeline -->
    <section class="about-timeline section">
        <div class="container">
            <h2 class="section-title"><?php _e('مسيرتنا', 'fiqhlearning'); ?></h2>

            <div class="timeline">
                <?php
                // الحصول على عناصر Timeline من Customizer
                $timeline_items = array(
                    array(
                        'year' => get_theme_mod('timeline_1_year', '2020'),
                        'title' => get_theme_mod('timeline_1_title', __('التأسيس', 'fiqhlearning')),
                        'description' => get_theme_mod('timeline_1_desc', __('تأسست مدرسة الريحان برؤية طموحة لنشر العلم الشرعي', 'fiqhlearning')),
                    ),
                    array(
                        'year' => get_theme_mod('timeline_2_year', '2021'),
                        'title' => get_theme_mod('timeline_2_title', __('إطلاق المنصة الإلكترونية', 'fiqhlearning')),
                        'description' => get_theme_mod('timeline_2_desc', __('إطلاق المنصة الإلكترونية الأولى للتعليم عن بعد', 'fiqhlearning')),
                    ),
                    array(
                        'year' => get_theme_mod('timeline_3_year', '2022'),
                        'title' => get_theme_mod('timeline_3_title', __('توسع المقررات', 'fiqhlearning')),
                        'description' => get_theme_mod('timeline_3_desc', __('إضافة مقررات جديدة في التفسير والحديث والعقيدة', 'fiqhlearning')),
                    ),
                    array(
                        'year' => get_theme_mod('timeline_4_year', '2023'),
                        'title' => get_theme_mod('timeline_4_title', __('الاعتماد الأكاديمي', 'fiqhlearning')),
                        'description' => get_theme_mod('timeline_4_desc', __('حصول المدرسة على الاعتماد الأكاديمي الرسمي', 'fiqhlearning')),
                    ),
                    array(
                        'year' => get_theme_mod('timeline_5_year', '2024'),
                        'title' => get_theme_mod('timeline_5_title', __('التطوير المستمر', 'fiqhlearning')),
                        'description' => get_theme_mod('timeline_5_desc', __('تحديث شامل للمنصة وإضافة ميزات تفاعلية جديدة', 'fiqhlearning')),
                    ),
                );

                $index = 0;
                foreach ($timeline_items as $item) :
                    $index++;
                    $side = ($index % 2 == 0) ? 'right' : 'left';
                ?>
                    <div class="timeline-item timeline-<?php echo $side; ?>">
                        <div class="timeline-marker">
                            <div class="timeline-year"><?php echo esc_html($item['year']); ?></div>
                        </div>
                        <div class="timeline-content card">
                            <h3 class="timeline-title"><?php echo esc_html($item['title']); ?></h3>
                            <p class="timeline-description"><?php echo esc_html($item['description']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- الرؤية والرسالة -->
    <section class="about-vision-mission section">
        <div class="container">
            <div class="vision-mission-grid">
                <div class="vision-card card">
                    <div class="card-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </div>
                    <h3><?php _e('رؤيتنا', 'fiqhlearning'); ?></h3>
                    <p><?php echo get_theme_mod('about_vision', __('أن نكون المنصة الرائدة في تعليم العلوم الشرعية إلكترونياً، ونساهم في تخريج جيل واعٍ بأحكام دينه.', 'fiqhlearning')); ?></p>
                </div>

                <div class="mission-card card">
                    <div class="card-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <h3><?php _e('رسالتنا', 'fiqhlearning'); ?></h3>
                    <p><?php echo get_theme_mod('about_mission', __('تقديم تعليم شرعي عالي الجودة يجمع بين الأصالة والمعاصرة، بأساليب تعليمية حديثة وميسرة للجميع.', 'fiqhlearning')); ?></p>
                </div>

                <div class="values-card card">
                    <div class="card-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <h3><?php _e('قيمنا', 'fiqhlearning'); ?></h3>
                    <p><?php echo get_theme_mod('about_values', __('الأمانة العلمية، الالتزام بالمنهج السليم، التميز في الأداء، الاحترام المتبادل، والتطوير المستمر.', 'fiqhlearning')); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- الإحصائيات -->
    <section class="about-stats section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number"><?php echo get_theme_mod('stat_students', '1000'); ?>+</div>
                    <div class="stat-label"><?php _e('طالب وطالبة', 'fiqhlearning'); ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo get_theme_mod('stat_courses', '50'); ?>+</div>
                    <div class="stat-label"><?php _e('مقرر دراسي', 'fiqhlearning'); ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo get_theme_mod('stat_lessons', '500'); ?>+</div>
                    <div class="stat-label"><?php _e('درس تعليمي', 'fiqhlearning'); ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo get_theme_mod('stat_teachers', '15'); ?>+</div>
                    <div class="stat-label"><?php _e('معلم متخصص', 'fiqhlearning'); ?></div>
                </div>
            </div>
        </div>
    </section>

    <!-- الفريق -->
    <section class="about-team section">
        <div class="container">
            <h2 class="section-title"><?php _e('فريق العمل', 'fiqhlearning'); ?></h2>
            <p class="section-subtitle"><?php _e('نخبة من المعلمين المتخصصين في العلوم الشرعية', 'fiqhlearning'); ?></p>

            <div class="team-grid">
                <?php
                // يمكن للمدير إضافة أعضاء الفريق من Customizer
                for ($i = 1; $i <= 4; $i++) :
                    $name = get_theme_mod("team_member_{$i}_name");
                    $role = get_theme_mod("team_member_{$i}_role");
                    $image = get_theme_mod("team_member_{$i}_image");

                    if ($name) :
                ?>
                    <div class="team-member card">
                        <?php if ($image) : ?>
                            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($name); ?>" class="team-photo">
                        <?php else : ?>
                            <div class="team-photo-placeholder">
                                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </div>
                        <?php endif; ?>
                        <h4 class="team-name"><?php echo esc_html($name); ?></h4>
                        <p class="team-role"><?php echo esc_html($role); ?></p>
                    </div>
                <?php
                    endif;
                endfor;
                ?>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="about-cta section">
        <div class="container">
            <div class="cta-box">
                <h2><?php _e('انضم إلى رحلة التعلم', 'fiqhlearning'); ?></h2>
                <p><?php _e('ابدأ مسيرتك التعليمية اليوم في مدرسة الريحان للعلوم الشرعية', 'fiqhlearning'); ?></p>
                <div class="cta-buttons">
                    <a href="<?php echo get_post_type_archive_link('fiqh_course'); ?>" class="btn btn-primary btn-lg">
                        <?php _e('تصفح المقررات', 'fiqhlearning'); ?>
                    </a>
                    <a href="<?php echo get_permalink(get_page_by_path('contact')); ?>" class="btn btn-secondary btn-lg">
                        <?php _e('تواصل معنا', 'fiqhlearning'); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>

<style>
/* About Page Styles */
.about-page {
    background: var(--color-light);
}

/* Hero Section */
.about-hero {
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
    color: var(--color-white);
    padding: 80px 0;
    position: relative;
    overflow: hidden;
}

.about-hero::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100%;
    height: 100%;
    background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="none"/><path d="M0 50 Q 25 25, 50 50 T 100 50" stroke="rgba(255,255,255,0.05)" fill="none" stroke-width="2"/></svg>') repeat;
    opacity: 0.1;
}

.about-hero .container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    align-items: center;
    position: relative;
    z-index: 1;
}

.about-hero-title {
    font-size: 3rem;
    margin-bottom: 20px;
    line-height: 1.2;
}

.about-hero-subtitle {
    font-size: 1.25rem;
    opacity: 0.9;
    line-height: 1.6;
}

.about-hero-image {
    display: flex;
    align-items: center;
    justify-content: center;
}

.about-illustration {
    max-width: 100%;
    height: auto;
}

/* Intro Section */
.about-intro {
    padding: 80px 0;
}

.about-intro-content {
    max-width: 800px;
    margin: 0 auto;
    text-align: center;
}

.about-intro-content h2 {
    font-size: 2.5rem;
    color: var(--color-primary);
    margin-bottom: 30px;
}

.about-text {
    font-size: 1.125rem;
    line-height: 1.8;
    color: var(--color-dark-gray);
}

/* Timeline */
.about-timeline {
    padding: 80px 0;
    background: var(--color-white);
}

.timeline {
    position: relative;
    max-width: 1000px;
    margin: 60px auto 0;
    padding: 0 20px;
}

.timeline::before {
    content: '';
    position: absolute;
    right: 50%;
    transform: translateX(50%);
    width: 4px;
    height: 100%;
    background: var(--color-primary-light);
}

.timeline-item {
    position: relative;
    margin-bottom: 50px;
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    gap: 30px;
    align-items: center;
}

.timeline-left {
    text-align: left;
}

.timeline-left .timeline-content {
    grid-column: 1;
}

.timeline-right {
    text-align: right;
}

.timeline-right .timeline-content {
    grid-column: 3;
}

.timeline-marker {
    grid-column: 2;
    position: relative;
    z-index: 2;
}

.timeline-year {
    background: var(--color-primary);
    color: var(--color-white);
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.125rem;
    box-shadow: 0 4px 12px rgba(11, 94, 59, 0.3);
}

.timeline-content {
    padding: 25px;
    background: var(--color-white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    transition: transform var(--transition-base), box-shadow var(--transition-base);
}

.timeline-content:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.timeline-title {
    font-size: 1.5rem;
    color: var(--color-primary);
    margin-bottom: 10px;
}

.timeline-description {
    color: var(--color-dark-gray);
    line-height: 1.6;
}

/* Vision & Mission */
.about-vision-mission {
    padding: 80px 0;
}

.vision-mission-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    margin-top: 40px;
}

.vision-card, .mission-card, .values-card {
    text-align: center;
    padding: 40px 30px;
}

.card-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    background: var(--color-primary-light);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-primary);
}

.vision-card h3, .mission-card h3, .values-card h3 {
    font-size: 1.5rem;
    color: var(--color-primary);
    margin-bottom: 15px;
}

.vision-card p, .mission-card p, .values-card p {
    color: var(--color-dark-gray);
    line-height: 1.7;
}

/* Stats */
.about-stats {
    background: var(--color-primary);
    color: var(--color-white);
    padding: 60px 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 40px;
    text-align: center;
}

.stat-number {
    font-size: 3rem;
    font-weight: bold;
    margin-bottom: 10px;
}

.stat-label {
    font-size: 1.125rem;
    opacity: 0.9;
}

/* Team */
.about-team {
    padding: 80px 0;
    background: var(--color-white);
}

.team-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
    margin-top: 40px;
}

.team-member {
    text-align: center;
    padding: 30px 20px;
    transition: transform var(--transition-base);
}

.team-member:hover {
    transform: translateY(-10px);
}

.team-photo, .team-photo-placeholder {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    margin: 0 auto 20px;
    object-fit: cover;
}

.team-photo-placeholder {
    background: var(--color-light-gray);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-gray);
}

.team-name {
    font-size: 1.25rem;
    color: var(--color-primary);
    margin-bottom: 5px;
}

.team-role {
    color: var(--color-dark-gray);
    font-size: 0.95rem;
}

/* CTA */
.about-cta {
    padding: 80px 0;
}

.cta-box {
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
    color: var(--color-white);
    padding: 60px 40px;
    border-radius: var(--radius-xl);
    text-align: center;
}

.cta-box h2 {
    font-size: 2.5rem;
    margin-bottom: 15px;
}

.cta-box p {
    font-size: 1.25rem;
    margin-bottom: 30px;
    opacity: 0.9;
}

.cta-buttons {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
}

/* Dark Mode */
body.dark-mode .about-page {
    background: var(--color-dark);
}

body.dark-mode .about-timeline,
body.dark-mode .about-team {
    background: var(--color-light);
}

body.dark-mode .timeline-content,
body.dark-mode .vision-card,
body.dark-mode .mission-card,
body.dark-mode .values-card,
body.dark-mode .team-member {
    background: var(--color-light-gray);
}

/* Responsive */
@media (max-width: 1024px) {
    .about-hero .container {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .vision-mission-grid,
    .team-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .about-hero-title {
        font-size: 2rem;
    }

    .timeline::before {
        right: 20px;
        transform: none;
    }

    .timeline-item {
        grid-template-columns: auto 1fr;
        gap: 20px;
    }

    .timeline-left .timeline-content,
    .timeline-right .timeline-content {
        grid-column: 2;
    }

    .timeline-marker {
        grid-column: 1;
    }

    .vision-mission-grid,
    .team-grid,
    .stats-grid {
        grid-template-columns: 1fr;
    }

    .cta-buttons {
        flex-direction: column;
    }

    .cta-buttons .btn {
        width: 100%;
    }
}
</style>

<?php get_footer(); ?>
