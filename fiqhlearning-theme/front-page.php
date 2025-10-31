<?php
/**
 * قالب الصفحة الرئيسية
 *
 * @package FiqhLearning
 */

get_header();
?>

<main id="primary" class="site-main">

    <!-- قسم Hero -->
    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title"><?php echo get_bloginfo('name'); ?></h1>
            <p class="hero-description"><?php echo get_bloginfo('description'); ?></p>
            <div class="hero-actions">
                <a href="<?php echo get_post_type_archive_link('fiqh_course'); ?>" class="btn btn-primary btn-lg">
                    <?php _e('تصفح المقررات', 'fiqhlearning'); ?>
                </a>
                <?php if (!is_user_logged_in()) : ?>
                    <a href="<?php echo wp_login_url(); ?>" class="btn btn-secondary btn-lg">
                        <?php _e('تسجيل الدخول', 'fiqhlearning'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- قسم الإحصائيات -->
    <section class="section">
        <div class="container">
            <div class="stats-section">
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo wp_count_posts('fiqh_course')->publish; ?></div>
                        <div class="stat-label"><?php _e('مقرر دراسي', 'fiqhlearning'); ?></div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo wp_count_posts('fiqh_lesson')->publish; ?></div>
                        <div class="stat-label"><?php _e('درس', 'fiqhlearning'); ?></div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">
                            <?php
                            $student_count = count(get_users(array('role' => 'student')));
                            echo $student_count;
                            ?>
                        </div>
                        <div class="stat-label"><?php _e('طالب', 'fiqhlearning'); ?></div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">
                            <?php
                            $teacher_count = wp_count_posts('fiqh_teacher')->publish;
                            echo $teacher_count;
                            ?>
                        </div>
                        <div class="stat-label"><?php _e('معلم', 'fiqhlearning'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم المقررات المميزة -->
    <section class="section featured-courses-section">
        <div class="container">
            <div class="section-header">
                <h2><?php _e('المقررات المميزة', 'fiqhlearning'); ?></h2>
                <a href="<?php echo get_post_type_archive_link('fiqh_course'); ?>" class="btn btn-secondary">
                    <?php _e('عرض جميع المقررات', 'fiqhlearning'); ?>
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

                // إذا لم توجد مقررات مميزة، نعرض أحدث 3 مقررات
                if (empty($featured_courses)) {
                    $featured_courses = get_posts(array(
                        'post_type' => 'fiqh_course',
                        'posts_per_page' => 3,
                        'orderby' => 'date',
                        'order' => 'DESC',
                    ));
                }

                if ($featured_courses) :
                    foreach ($featured_courses as $post) :
                        setup_postdata($post);
                        ?>
                        <div class="card course-card">
                            <?php if (has_post_thumbnail()) : ?>
                                <img src="<?php echo get_the_post_thumbnail_url(null, 'fiqh-course-thumb'); ?>" alt="<?php the_title(); ?>" class="course-thumbnail">
                            <?php endif; ?>

                            <div class="course-content">
                                <h3 class="course-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>

                                <div class="course-meta">
                                    <?php
                                    $teacher_id = get_post_meta(get_the_ID(), '_fiqh_course_teacher_id', true);
                                    if ($teacher_id) {
                                        $teacher = get_post($teacher_id);
                                        if ($teacher) {
                                            echo '<span class="course-teacher"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg> ' . esc_html($teacher->post_title) . '</span>';
                                        }
                                    }

                                    $duration = get_post_meta(get_the_ID(), '_fiqh_course_duration', true);
                                    if ($duration) {
                                        echo '<span class="course-duration"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> ' . esc_html($duration) . '</span>';
                                    }
                                    ?>
                                </div>

                                <div class="course-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                                </div>

                                <a href="<?php the_permalink(); ?>" class="btn btn-primary">
                                    <?php _e('عرض المقرر', 'fiqhlearning'); ?>
                                </a>
                            </div>

                            <!-- زر الكتاب عند Hover -->
                            <?php
                            $book_url = get_post_meta(get_the_ID(), '_fiqh_course_book_url', true);
                            if ($book_url) :
                            ?>
                                <div class="course-hover-actions">
                                    <button class="btn btn-accent view-pdf-btn" data-pdf-url="<?php echo esc_url($book_url); ?>" data-pdf-title="<?php the_title(); ?>">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                            <line x1="16" y1="13" x2="8" y2="13"></line>
                                            <line x1="16" y1="17" x2="8" y2="17"></line>
                                            <polyline points="10 9 9 9 8 9"></polyline>
                                        </svg>
                                        <?php _e('الكتاب', 'fiqhlearning'); ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php
                    endforeach;
                    wp_reset_postdata();
                else :
                    ?>
                    <div class="no-courses-message">
                        <p><?php _e('لا توجد مقررات متاحة حالياً', 'fiqhlearning'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- قسم آخر المقالات -->
    <?php
    $recent_posts = get_posts(array(
        'post_type' => 'post',
        'posts_per_page' => 3,
        'orderby' => 'date',
        'order' => 'DESC',
    ));

    if ($recent_posts) :
    ?>
        <section class="section blog-section">
            <div class="container">
                <div class="section-header">
                    <h2><?php _e('آخر المقالات', 'fiqhlearning'); ?></h2>
                    <a href="<?php echo get_permalink(get_option('page_for_posts')); ?>" class="btn btn-secondary">
                        <?php _e('جميع المقالات', 'fiqhlearning'); ?>
                    </a>
                </div>

                <div class="posts-grid">
                    <?php
                    foreach ($recent_posts as $post) :
                        setup_postdata($post);
                        ?>
                        <article class="card post-card">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <img src="<?php echo get_the_post_thumbnail_url(null, 'medium'); ?>" alt="<?php the_title(); ?>" class="post-thumbnail">
                                </a>
                            <?php endif; ?>

                            <div class="post-content">
                                <h3 class="post-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>

                                <div class="post-meta">
                                    <span class="post-date">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                        <?php echo fiqh_format_date(get_the_date()); ?>
                                    </span>
                                </div>

                                <div class="post-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                </div>

                                <a href="<?php the_permalink(); ?>" class="btn btn-outline">
                                    <?php _e('قراءة المزيد', 'fiqhlearning'); ?>
                                </a>
                            </div>
                        </article>
                        <?php
                    endforeach;
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- قسم الدعوة للعمل -->
    <section class="section cta-section">
        <div class="container">
            <div class="cta-box">
                <h2><?php _e('ابدأ رحلتك التعليمية اليوم', 'fiqhlearning'); ?></h2>
                <p><?php _e('انضم إلى آلاف الطلاب واحصل على تعليم فقهي متميز', 'fiqhlearning'); ?></p>
                <a href="<?php echo get_post_type_archive_link('fiqh_course'); ?>" class="btn btn-primary btn-lg">
                    <?php _e('ابدأ الآن', 'fiqhlearning'); ?>
                </a>
            </div>
        </div>
    </section>

</main>

<?php
get_footer();
