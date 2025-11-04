<?php
/**
 * Template Name: خطة الدراسة
 * قالب صفحة خطة الدراسة
 *
 * @package FiqhLearning
 */

get_header();
?>

<main id="primary" class="site-main page-study-plan">

    <!-- Hero Section -->
    <section class="page-hero">
        <div class="container">
            <div class="page-hero-content">
                <div class="breadcrumb">
                    <a href="<?php echo home_url(); ?>"><?php _e('الرئيسية', 'fiqhlearning'); ?></a>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                    <span><?php _e('خطة الدراسة', 'fiqhlearning'); ?></span>
                </div>
                <h1 class="page-title">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                        <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                    </svg>
                    <?php echo get_theme_mod('study_plan_title', __('خطة الدراسة', 'fiqhlearning')); ?>
                </h1>
                <p class="page-description">
                    <?php echo get_theme_mod('study_plan_description', __('منهج دراسي متكامل للعلوم الشرعية على مدار عدة مستويات', 'fiqhlearning')); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Study Plan Content -->
    <section class="section">
        <div class="container">

            <?php
            // جلب المستويات من قاعدة البيانات
            global $wpdb;
            $batches = $wpdb->get_results(
                "SELECT * FROM {$wpdb->prefix}fiqh_batches
                WHERE status = 'active'
                ORDER BY level_order ASC"
            );

            if ($batches) :
            ?>
                <div class="study-plan-intro card">
                    <h2><?php _e('نظام المستويات الدراسية', 'fiqhlearning'); ?></h2>
                    <p><?php echo get_theme_mod('study_plan_intro', __('تم تقسيم الخطة الدراسية إلى عدة مستويات متدرجة، بحيث يبدأ الطالب من المستوى الأول ويتقدم تدريجياً نحو المستويات المتقدمة.', 'fiqhlearning')); ?></p>
                </div>

                <div class="study-levels">
                    <?php
                    foreach ($batches as $index => $batch) :
                        // جلب المقررات التابعة لهذا المستوى
                        $courses = $wpdb->get_results($wpdb->prepare(
                            "SELECT DISTINCT p.* FROM {$wpdb->posts} p
                            INNER JOIN {$wpdb->prefix}fiqh_enrollments e ON p.ID = e.course_id
                            WHERE e.batch_id = %d AND p.post_type = 'fiqh_course' AND p.post_status = 'publish'
                            ORDER BY p.post_title ASC
                            LIMIT 10",
                            $batch->id
                        ));

                        // إذا لم توجد مقررات مربوطة، جلب بعض المقررات كمثال
                        if (empty($courses)) {
                            $courses = get_posts(array(
                                'post_type' => 'fiqh_course',
                                'posts_per_page' => 6,
                                'orderby' => 'date',
                                'order' => 'DESC'
                            ));
                        }
                    ?>
                        <div class="study-level-card card">
                            <div class="level-header">
                                <div class="level-badge">
                                    <?php _e('المستوى', 'fiqhlearning'); ?> <?php echo $batch->level_order; ?>
                                </div>
                                <h3 class="level-name"><?php echo esc_html($batch->name); ?></h3>
                                <?php if ($batch->description) : ?>
                                    <p class="level-description"><?php echo esc_html($batch->description); ?></p>
                                <?php endif; ?>

                                <?php if ($batch->start_date || $batch->end_date) : ?>
                                    <div class="level-dates">
                                        <?php if ($batch->start_date) : ?>
                                            <span class="level-date">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                                </svg>
                                                <?php _e('البداية:', 'fiqhlearning'); ?> <?php echo date_i18n('Y/m/d', strtotime($batch->start_date)); ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($batch->end_date) : ?>
                                            <span class="level-date">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                                </svg>
                                                <?php _e('النهاية:', 'fiqhlearning'); ?> <?php echo date_i18n('Y/m/d', strtotime($batch->end_date)); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if ($courses) : ?>
                                <div class="level-courses">
                                    <h4 class="level-courses-title">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                        </svg>
                                        <?php _e('المقررات الدراسية', 'fiqhlearning'); ?>
                                    </h4>
                                    <div class="courses-list">
                                        <?php foreach ($courses as $course) :
                                            $course_sciences = wp_get_post_terms($course->ID, 'fiqh_course_science');
                                            $lessons_count = get_posts(array(
                                                'post_type' => 'fiqh_lesson',
                                                'meta_key' => '_fiqh_lesson_course_id',
                                                'meta_value' => $course->ID,
                                                'posts_per_page' => -1,
                                                'fields' => 'ids'
                                            ));
                                        ?>
                                            <div class="course-item-small">
                                                <div class="course-item-icon">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                                    </svg>
                                                </div>
                                                <div class="course-item-content">
                                                    <a href="<?php echo get_permalink($course->ID); ?>" class="course-item-title">
                                                        <?php echo esc_html($course->post_title); ?>
                                                    </a>
                                                    <div class="course-item-meta">
                                                        <?php if (!empty($course_sciences)) : ?>
                                                            <span class="course-science-badge">
                                                                <?php echo esc_html($course_sciences[0]->name); ?>
                                                            </span>
                                                        <?php endif; ?>
                                                        <span class="course-lessons-count">
                                                            <?php echo count($lessons_count); ?> <?php _e('درس', 'fiqhlearning'); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php else : ?>
                <!-- محتوى افتراضي عند عدم وجود مستويات -->
                <div class="study-plan-intro card">
                    <h2><?php _e('نظام المستويات الدراسية', 'fiqhlearning'); ?></h2>
                    <p><?php echo get_theme_mod('study_plan_intro', __('تم تقسيم الخطة الدراسية إلى عدة مستويات متدرجة، بحيث يبدأ الطالب من المستوى الأول ويتقدم تدريجياً نحو المستويات المتقدمة.', 'fiqhlearning')); ?></p>
                </div>

                <div class="empty-state card">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                        <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                    </svg>
                    <h3><?php _e('لم يتم إضافة مستويات دراسية بعد', 'fiqhlearning'); ?></h3>
                    <p><?php _e('سيتم إضافة الخطة الدراسية قريباً', 'fiqhlearning'); ?></p>
                    <a href="<?php echo get_post_type_archive_link('fiqh_course'); ?>" class="btn btn-primary">
                        <?php _e('تصفح المقررات', 'fiqhlearning'); ?>
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </section>

    <!-- CTA Section -->
    <section class="section cta-section-secondary">
        <div class="container">
            <div class="cta-box">
                <h2><?php _e('مستعد للبدء؟', 'fiqhlearning'); ?></h2>
                <p><?php _e('ابدأ رحلتك التعليمية الآن وانضم إلى مدرسة الريحان', 'fiqhlearning'); ?></p>
                <div class="cta-actions">
                    <a href="<?php echo get_post_type_archive_link('fiqh_course'); ?>" class="btn btn-primary btn-lg">
                        <?php _e('تصفح المقررات', 'fiqhlearning'); ?>
                    </a>
                    <?php
                    $about_page = get_page_by_path('about');
                    if ($about_page) :
                    ?>
                        <a href="<?php echo get_permalink($about_page); ?>" class="btn btn-outline btn-lg">
                            <?php _e('عن المدرسة', 'fiqhlearning'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

</main>

<?php
get_footer();
