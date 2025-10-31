<?php
/**
 * قالب صفحة المقرر الواحد
 *
 * @package FiqhLearning
 */

get_header();

// التحقق من صلاحية الوصول
$can_access = is_user_logged_in() && (
    current_user_can('administrator') ||
    current_user_can('teacher') ||
    FiqhLearning_Enrollments::is_user_enrolled(get_current_user_id(), get_the_ID())
);

?>

<main id="primary" class="site-main single-course-page">
    <div class="container">

        <?php
        while (have_posts()) :
            the_post();
            ?>

            <!-- Breadcrumb -->
            <?php fiqh_breadcrumb(); ?>

            <!-- معلومات المقرر -->
            <div class="course-header-section">
                <div class="course-header-content">
                    <h1 class="course-title"><?php the_title(); ?></h1>

                    <div class="course-meta-info">
                        <?php
                        // المعلم
                        $teacher_id = get_post_meta(get_the_ID(), '_fiqh_course_teacher_id', true);
                        if ($teacher_id) {
                            $teacher = get_post($teacher_id);
                            if ($teacher) {
                                ?>
                                <div class="meta-item">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                    <span><?php echo esc_html($teacher->post_title); ?></span>
                                </div>
                                <?php
                            }
                        }

                        // المدة
                        $duration = get_post_meta(get_the_ID(), '_fiqh_course_duration', true);
                        if ($duration) {
                            ?>
                            <div class="meta-item">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <span><?php echo esc_html($duration); ?></span>
                            </div>
                            <?php
                        }

                        // عدد الدروس
                        global $wpdb;
                        $lessons_count = $wpdb->get_var($wpdb->prepare(
                            "SELECT COUNT(*) FROM {$wpdb->posts} p
                            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
                            WHERE p.post_type = 'fiqh_lesson' AND p.post_status = 'publish'
                            AND pm.meta_key = '_fiqh_lesson_course_id' AND pm.meta_value = %d",
                            get_the_ID()
                        ));
                        ?>
                        <div class="meta-item">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                            </svg>
                            <span><?php echo $lessons_count . ' ' . __('درس', 'fiqhlearning'); ?></span>
                        </div>

                        <!-- التصنيفات -->
                        <?php
                        $terms = get_the_terms(get_the_ID(), 'fiqh_course_category');
                        if ($terms && !is_wp_error($terms)) :
                            foreach ($terms as $term) :
                                ?>
                                <span class="category-badge"><?php echo esc_html($term->name); ?></span>
                            <?php
                            endforeach;
                        endif;
                        ?>
                    </div>

                    <!-- زر الكتاب -->
                    <?php
                    $book_url = get_post_meta(get_the_ID(), '_fiqh_course_book_url', true);
                    if ($book_url) :
                    ?>
                        <button class="btn btn-accent view-pdf-btn" data-pdf-url="<?php echo esc_url($book_url); ?>" data-pdf-title="<?php the_title(); ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                            </svg>
                            <?php _e('عرض الكتاب', 'fiqhlearning'); ?>
                        </button>
                    <?php endif; ?>
                </div>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="course-header-image">
                        <img src="<?php echo get_the_post_thumbnail_url(null, 'large'); ?>" alt="<?php the_title(); ?>">
                    </div>
                <?php endif; ?>
            </div>

            <!-- وصف المقرر -->
            <?php if (get_the_content()) : ?>
                <div class="course-description card">
                    <h2><?php _e('عن المقرر', 'fiqhlearning'); ?></h2>
                    <div class="content">
                        <?php the_content(); ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- قائمة الدروس -->
            <div class="course-lessons-section">
                <h2><?php _e('دروس المقرر', 'fiqhlearning'); ?></h2>

                <?php if ($can_access) : ?>
                    <?php
                    // نسبة الإنجاز
                    if (!current_user_can('administrator') && !current_user_can('teacher')) {
                        $progress = fiqh_get_course_progress(get_the_ID(), get_current_user_id());
                        fiqh_display_progress_bar($progress, __('تقدمك في المقرر:', 'fiqhlearning'));
                    }

                    // جلب الدروس
                    $lessons = get_posts(array(
                        'post_type' => 'fiqh_lesson',
                        'posts_per_page' => -1,
                        'meta_query' => array(
                            array(
                                'key' => '_fiqh_lesson_course_id',
                                'value' => get_the_ID(),
                            )
                        ),
                        'meta_key' => '_fiqh_lesson_order',
                        'orderby' => 'meta_value_num',
                        'order' => 'ASC',
                    ));

                    if ($lessons) :
                        ?>
                        <div class="lessons-list">
                            <?php
                            foreach ($lessons as $lesson) :
                                setup_postdata($lesson);
                                $lesson_progress = FiqhLearning_Progress::get_lesson_progress(get_current_user_id(), $lesson->ID);
                                $is_completed = $lesson_progress && $lesson_progress->status === 'completed';
                                ?>
                                <div class="lesson-item <?php echo $is_completed ? 'completed' : ''; ?>">
                                    <a href="<?php echo get_permalink($lesson->ID); ?>" class="lesson-link">
                                        <div class="lesson-icon">
                                            <?php if ($is_completed) : ?>
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                                </svg>
                                            <?php else : ?>
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polygon points="5 3 19 12 5 21 5 3"></polygon>
                                                </svg>
                                            <?php endif; ?>
                                        </div>

                                        <div class="lesson-info">
                                            <h3 class="lesson-title"><?php echo esc_html($lesson->post_title); ?></h3>

                                            <?php
                                            $lesson_duration = get_post_meta($lesson->ID, '_fiqh_lesson_duration', true);
                                            if ($lesson_duration) :
                                                ?>
                                                <span class="lesson-duration"><?php echo esc_html($lesson_duration); ?></span>
                                            <?php endif; ?>
                                        </div>

                                        <?php if ($lesson_progress && $lesson_progress->progress_percentage > 0 && $lesson_progress->progress_percentage < 100) : ?>
                                            <div class="lesson-progress-mini">
                                                <div class="progress-circle" style="--progress: <?php echo $lesson_progress->progress_percentage; ?>%;">
                                                    <span><?php echo $lesson_progress->progress_percentage; ?>%</span>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </a>
                                </div>
                                <?php
                            endforeach;
                            wp_reset_postdata();
                            ?>
                        </div>
                        <?php
                    else :
                        ?>
                        <div class="no-lessons-message">
                            <p><?php _e('لا توجد دروس في هذا المقرر حالياً', 'fiqhlearning'); ?></p>
                        </div>
                    <?php endif; ?>

                <?php else : ?>
                    <!-- رسالة للمستخدمين غير المصرح لهم -->
                    <?php fiqh_unauthorized_message(); ?>
                <?php endif; ?>

            </div>

        <?php endwhile; ?>

    </div>
</main>

<?php
get_footer();
