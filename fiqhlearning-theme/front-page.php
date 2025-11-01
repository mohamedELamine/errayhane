<?php
/**
 * قالب الصفحة الرئيسية
 *
 * @package FiqhLearning
 */

get_header();
?>

<main id="primary" class="site-main">

    <!-- قسم Hero المحسّن -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                    <?php echo get_bloginfo('name'); ?>
                </h1>
                <p class="hero-description"><?php echo get_bloginfo('description'); ?></p>
                <div class="hero-actions">
                    <a href="<?php echo get_post_type_archive_link('fiqh_course'); ?>" class="btn btn-primary btn-lg">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                        <?php _e('تصفح المقررات', 'fiqhlearning'); ?>
                    </a>
                    <?php if (!is_user_logged_in()) : ?>
                        <a href="<?php echo home_url('/login'); ?>" class="btn btn-secondary btn-lg">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                                <polyline points="10 17 15 12 10 7"></polyline>
                                <line x1="15" y1="12" x2="3" y2="12"></line>
                            </svg>
                            <?php _e('تسجيل الدخول', 'fiqhlearning'); ?>
                        </a>
                    <?php else : ?>
                        <a href="<?php echo home_url('/dashboard'); ?>" class="btn btn-secondary btn-lg">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                            <?php _e('لوحة التحكم', 'fiqhlearning'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم الإحصائيات -->
    <section class="section">
        <div class="container">
            <div class="stats-section">
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                        </div>
                        <div class="stat-number"><?php echo wp_count_posts('fiqh_course')->publish; ?></div>
                        <div class="stat-label"><?php _e('مقرر دراسي', 'fiqhlearning'); ?></div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                            </svg>
                        </div>
                        <div class="stat-number"><?php echo wp_count_posts('fiqh_lesson')->publish; ?></div>
                        <div class="stat-label"><?php _e('درس', 'fiqhlearning'); ?></div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <div class="stat-number">
                            <?php echo count(get_users(array('role' => 'student'))); ?>
                        </div>
                        <div class="stat-label"><?php _e('طالب', 'fiqhlearning'); ?></div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <div class="stat-number"><?php echo wp_count_posts('fiqh_teacher')->publish; ?></div>
                        <div class="stat-label"><?php _e('معلم', 'fiqhlearning'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم الأحداث القادمة -->
    <?php
    // جلب المقالات من تصنيف "أحداث" أو أحدث المقالات
    $events_cat = get_category_by_slug('events');
    $events_args = array(
        'post_type' => 'post',
        'posts_per_page' => 3,
        'orderby' => 'date',
        'order' => 'DESC',
    );

    if ($events_cat) {
        $events_args['cat'] = $events_cat->term_id;
    }

    $events = get_posts($events_args);

    if ($events) :
    ?>
        <section class="section events-section">
            <div class="container">
                <div class="section-header">
                    <h2>
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <?php _e('الأحداث والفعاليات', 'fiqhlearning'); ?>
                    </h2>
                    <?php if ($events_cat) : ?>
                        <a href="<?php echo get_category_link($events_cat->term_id); ?>" class="btn btn-outline">
                            <?php _e('جميع الأحداث', 'fiqhlearning'); ?>
                        </a>
                    <?php endif; ?>
                </div>

                <div class="events-grid">
                    <?php foreach ($events as $post) : setup_postdata($post); ?>
                        <article class="event-card card">
                            <div class="event-date">
                                <div class="event-day"><?php echo get_the_date('d'); ?></div>
                                <div class="event-month"><?php echo get_the_date('M'); ?></div>
                            </div>
                            <div class="event-content">
                                <h3 class="event-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <p class="event-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                                <a href="<?php the_permalink(); ?>" class="event-link">
                                    <?php _e('معرفة المزيد', 'fiqhlearning'); ?>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                        <polyline points="12 5 19 12 12 19"></polyline>
                                    </svg>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; wp_reset_postdata(); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- قسم العلوم -->
    <?php
    $sciences = get_terms(array(
        'taxonomy' => 'fiqh_course_science',
        'hide_empty' => true,
        'number' => 4,
    ));

    if ($sciences && !is_wp_error($sciences)) :
    ?>
        <section class="section sciences-section">
            <div class="container">
                <div class="section-header">
                    <h2>
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                        </svg>
                        <?php _e('العلوم الشرعية', 'fiqhlearning'); ?>
                    </h2>
                    <a href="<?php echo get_post_type_archive_link('fiqh_course'); ?>" class="btn btn-outline">
                        <?php _e('عرض الكل', 'fiqhlearning'); ?>
                    </a>
                </div>

                <div class="sciences-grid">
                    <?php foreach ($sciences as $science) : ?>
                        <a href="<?php echo get_term_link($science); ?>" class="science-card card">
                            <div class="science-icon">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                                </svg>
                            </div>
                            <h3 class="science-name"><?php echo esc_html($science->name); ?></h3>
                            <p class="science-count"><?php echo $science->count; ?> <?php _e('مقرر', 'fiqhlearning'); ?></p>
                            <?php if ($science->description) : ?>
                                <p class="science-description"><?php echo esc_html(wp_trim_words($science->description, 12)); ?></p>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- قسم المقررات المميزة -->
    <section class="section featured-courses-section">
        <div class="container">
            <div class="section-header">
                <h2>
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
                    <?php _e('المقررات المميزة', 'fiqhlearning'); ?>
                </h2>
                <a href="<?php echo get_post_type_archive_link('fiqh_course'); ?>" class="btn btn-outline">
                    <?php _e('جميع المقررات', 'fiqhlearning'); ?>
                </a>
            </div>

            <div class="courses-grid">
                <?php
                $featured_courses = get_posts(array(
                    'post_type' => 'fiqh_course',
                    'posts_per_page' => 3,
                    'meta_key' => '_fiqh_course_featured',
                    'meta_value' => '1',
                ));

                if (empty($featured_courses)) {
                    $featured_courses = get_posts(array(
                        'post_type' => 'fiqh_course',
                        'posts_per_page' => 3,
                        'orderby' => 'date',
                        'order' => 'DESC',
                    ));
                }

                if ($featured_courses) :
                    foreach ($featured_courses as $post) : setup_postdata($post);
                        $teacher_id = get_post_meta(get_the_ID(), '_fiqh_course_teacher_id', true);
                        $teacher = $teacher_id ? get_post($teacher_id) : null;
                        $duration = get_post_meta(get_the_ID(), '_fiqh_course_duration', true);
                        $book_url = get_post_meta(get_the_ID(), '_fiqh_course_book_url', true);

                        // العلوم
                        $sciences = wp_get_post_terms(get_the_ID(), 'fiqh_course_science');
                        ?>
                        <div class="card course-card">
                            <?php if (has_post_thumbnail()) : ?>
                                <img src="<?php echo get_the_post_thumbnail_url(null, 'fiqh-course-thumb'); ?>" alt="<?php the_title(); ?>" class="course-thumbnail">
                            <?php else : ?>
                                <div class="course-thumbnail-placeholder">
                                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                    </svg>
                                </div>
                            <?php endif; ?>

                            <div class="course-content">
                                <?php if (!empty($sciences)) : ?>
                                    <div class="course-category">
                                        <span class="category-badge"><?php echo esc_html($sciences[0]->name); ?></span>
                                    </div>
                                <?php endif; ?>

                                <h3 class="course-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>

                                <div class="course-meta">
                                    <?php if ($teacher) : ?>
                                        <span class="course-teacher">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="12" cy="7" r="4"></circle>
                                            </svg>
                                            <?php echo esc_html($teacher->post_title); ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($duration) : ?>
                                        <span class="course-duration">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polyline points="12 6 12 12 16 14"></polyline>
                                            </svg>
                                            <?php echo esc_html($duration); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="course-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                                </div>

                                <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-block">
                                    <?php _e('عرض المقرر', 'fiqhlearning'); ?>
                                </a>
                            </div>

                            <?php if ($book_url) : ?>
                                <div class="course-hover-actions">
                                    <button class="btn btn-accent view-pdf-btn" data-pdf-url="<?php echo esc_url($book_url); ?>" data-pdf-title="<?php the_title(); ?>">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                        </svg>
                                        <?php _e('الكتاب', 'fiqhlearning'); ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php
                    endforeach;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
        </div>
    </section>

    <!-- قسم آخر الأسئلة -->
    <?php
    global $wpdb;
    $recent_questions = $wpdb->get_results(
        "SELECT q.*, u.display_name as user_name, c.post_title as course_name
        FROM {$wpdb->prefix}fiqh_questions q
        LEFT JOIN {$wpdb->users} u ON q.user_id = u.ID
        LEFT JOIN {$wpdb->posts} c ON q.course_id = c.ID
        WHERE q.status != 'deleted'
        ORDER BY q.created_at DESC
        LIMIT 3"
    );

    if ($recent_questions) :
    ?>
        <section class="section questions-section">
            <div class="container">
                <div class="section-header">
                    <h2>
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                        <?php _e('آخر الأسئلة', 'fiqhlearning'); ?>
                    </h2>
                    <a href="<?php echo get_permalink(get_page_by_path('questions')); ?>" class="btn btn-outline">
                        <?php _e('جميع الأسئلة', 'fiqhlearning'); ?>
                    </a>
                </div>

                <div class="questions-grid">
                    <?php foreach ($recent_questions as $question) : ?>
                        <div class="question-card-home card">
                            <div class="question-status-badge <?php echo $question->status === 'answered' ? 'status-answered' : 'status-pending'; ?>">
                                <?php echo $question->status === 'answered' ? __('مُجابة', 'fiqhlearning') : __('بانتظار', 'fiqhlearning'); ?>
                            </div>
                            <h4 class="question-title-home">
                                <?php echo esc_html(wp_trim_words($question->question_text, 12)); ?>
                            </h4>
                            <div class="question-meta-home">
                                <span><?php echo $question->is_anonymous ? __('مجهول', 'fiqhlearning') : esc_html($question->user_name); ?></span>
                                <span>•</span>
                                <span><?php echo human_time_diff(strtotime($question->created_at), current_time('timestamp')) . ' ' . __('مضت', 'fiqhlearning'); ?></span>
                            </div>
                            <?php if ($question->course_name) : ?>
                                <div class="question-course-home">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                    </svg>
                                    <?php echo esc_html($question->course_name); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- قسم الدعوة للعمل -->
    <section class="section cta-section">
        <div class="container">
            <div class="cta-box">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto var(--spacing-lg);">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                </svg>
                <h2><?php _e('ابدأ رحلتك التعليمية اليوم', 'fiqhlearning'); ?></h2>
                <p><?php _e('انضم إلى آلاف الطلاب واحصل على تعليم فقهي متميز في الفقه المالكي', 'fiqhlearning'); ?></p>
                <a href="<?php echo get_post_type_archive_link('fiqh_course'); ?>" class="btn btn-primary btn-lg">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                    <?php _e('ابدأ الآن', 'fiqhlearning'); ?>
                </a>
            </div>
        </div>
    </section>

</main>

<?php
get_footer();
