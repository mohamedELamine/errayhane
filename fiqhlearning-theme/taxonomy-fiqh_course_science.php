<?php
/**
 * قالب عرض العلوم (Sciences Taxonomy)
 *
 * @package FiqhLearning
 */

get_header();

// Get current term
$current_term = get_queried_object();
?>

<main id="primary" class="site-main">
    <div class="container">

        <!-- رأس الصفحة -->
        <header class="taxonomy-header">
            <div class="taxonomy-hero">
                <div class="taxonomy-title-wrapper">
                    <h1 class="taxonomy-title">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-left: 10px;">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                        </svg>
                        <?php echo esc_html($current_term->name); ?>
                    </h1>

                    <?php if ($current_term->description) : ?>
                        <p class="taxonomy-description"><?php echo esc_html($current_term->description); ?></p>
                    <?php endif; ?>

                    <div class="taxonomy-meta">
                        <span class="course-count">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                            <?php echo $current_term->count; ?> <?php _e('مقرر', 'fiqhlearning'); ?>
                        </span>
                    </div>
                </div>
            </div>

            <?php fiqh_breadcrumb(); ?>
        </header>

        <!-- فلاتر البحث -->
        <div class="courses-filters">
            <form method="get" class="filters-form">
                <!-- بحث بالاسم -->
                <div class="filter-group">
                    <input type="text" name="s" placeholder="<?php _e('ابحث عن مقرر في هذا العلم...', 'fiqhlearning'); ?>" value="<?php echo get_search_query(); ?>" class="filter-search">
                </div>

                <!-- فلتر الفصل الدراسي -->
                <?php
                $semesters = get_terms(array(
                    'taxonomy' => 'fiqh_course_semester',
                    'hide_empty' => true,
                ));

                if ($semesters && !is_wp_error($semesters)) :
                ?>
                    <div class="filter-group">
                        <select name="semester" class="filter-select">
                            <option value=""><?php _e('جميع الفصول', 'fiqhlearning'); ?></option>
                            <?php foreach ($semesters as $semester) : ?>
                                <option value="<?php echo esc_attr($semester->slug); ?>" <?php selected(isset($_GET['semester']) ? $_GET['semester'] : '', $semester->slug); ?>>
                                    <?php echo esc_html($semester->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <!-- فلتر نوع المقرر -->
                <?php
                $types = get_terms(array(
                    'taxonomy' => 'fiqh_course_type',
                    'hide_empty' => true,
                ));

                if ($types && !is_wp_error($types)) :
                ?>
                    <div class="filter-group">
                        <select name="type" class="filter-select">
                            <option value=""><?php _e('جميع الأنواع', 'fiqhlearning'); ?></option>
                            <?php foreach ($types as $type) : ?>
                                <option value="<?php echo esc_attr($type->slug); ?>" <?php selected(isset($_GET['type']) ? $_GET['type'] : '', $type->slug); ?>>
                                    <?php echo esc_html($type->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <?php _e('بحث', 'fiqhlearning'); ?>
                </button>
            </form>
        </div>

        <!-- قائمة المقررات -->
        <div class="courses-list">
            <?php
            if (have_posts()) :
                ?>
                <div class="courses-grid">
                    <?php
                    while (have_posts()) :
                        the_post();
                        ?>
                        <div class="card course-card">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <img src="<?php echo get_the_post_thumbnail_url(null, 'fiqh-course-thumb'); ?>" alt="<?php the_title(); ?>" class="course-thumbnail">
                                </a>
                            <?php else : ?>
                                <div class="course-thumbnail-placeholder">
                                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                    </svg>
                                </div>
                            <?php endif; ?>

                            <div class="course-content">
                                <h3 class="course-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>

                                <div class="course-meta">
                                    <!-- المعلم -->
                                    <?php
                                    $teacher_id = get_post_meta(get_the_ID(), '_fiqh_course_teacher_id', true);
                                    if ($teacher_id) {
                                        $teacher = get_post($teacher_id);
                                        if ($teacher) {
                                            echo '<span class="course-teacher"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg> ' . esc_html($teacher->post_title) . '</span>';
                                        }
                                    }

                                    // المدة
                                    $duration = get_post_meta(get_the_ID(), '_fiqh_course_duration', true);
                                    if ($duration) {
                                        echo '<span class="course-duration"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> ' . esc_html($duration) . '</span>';
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
                                    echo '<span class="course-lessons"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg> ' . $lessons_count . ' ' . __('درس', 'fiqhlearning') . '</span>';
                                    ?>
                                </div>

                                <!-- التصنيفات الإضافية -->
                                <div class="course-categories">
                                    <?php
                                    // عرض الفصل الدراسي
                                    $semester_terms = get_the_terms(get_the_ID(), 'fiqh_course_semester');
                                    if ($semester_terms && !is_wp_error($semester_terms)) {
                                        foreach ($semester_terms as $term) {
                                            echo '<span class="category-badge badge-semester">' . esc_html($term->name) . '</span>';
                                        }
                                    }

                                    // عرض نوع المقرر
                                    $type_terms = get_the_terms(get_the_ID(), 'fiqh_course_type');
                                    if ($type_terms && !is_wp_error($type_terms)) {
                                        foreach ($type_terms as $term) {
                                            echo '<span class="category-badge badge-type">' . esc_html($term->name) . '</span>';
                                        }
                                    }
                                    ?>
                                </div>

                                <div class="course-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                                </div>

                                <!-- نسبة الإنجاز للطالب المسجل -->
                                <?php if (is_user_logged_in() && FiqhLearning_Enrollments::is_user_enrolled(get_current_user_id(), get_the_ID())) : ?>
                                    <?php
                                    $progress = fiqh_get_course_progress(get_the_ID(), get_current_user_id());
                                    fiqh_display_progress_bar($progress, __('تقدمك:', 'fiqhlearning'));
                                    ?>
                                <?php endif; ?>

                                <a href="<?php the_permalink(); ?>" class="btn btn-primary" style="margin-top: auto;">
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
                                        </svg>
                                        <?php _e('الكتاب', 'fiqhlearning'); ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php
                    endwhile;
                    ?>
                </div>

                <!-- الترقيم -->
                <div class="pagination">
                    <?php
                    the_posts_pagination(array(
                        'mid_size' => 2,
                        'prev_text' => __('&rarr; السابق', 'fiqhlearning'),
                        'next_text' => __('التالي &larr;', 'fiqhlearning'),
                    ));
                    ?>
                </div>
                <?php
            else :
                ?>
                <div class="no-results">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <h2><?php _e('لا توجد مقررات', 'fiqhlearning'); ?></h2>
                    <p><?php _e('لم يتم العثور على مقررات في هذا العلم', 'fiqhlearning'); ?></p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</main>

<?php
get_footer();
