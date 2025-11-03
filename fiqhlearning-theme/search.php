<?php
/**
 * صفحة البحث
 *
 * @package مدرسة_الريحان
 * @version 1.3.0
 */

get_header();

// الحصول على معاملات البحث
$search_query = get_search_query();
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$post_type = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : '';
$science = isset($_GET['science']) ? sanitize_text_field($_GET['science']) : '';
$year = isset($_GET['year']) ? sanitize_text_field($_GET['year']) : '';
$status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';

// بناء استعلام البحث
$args = array(
    's' => $search_query,
    'posts_per_page' => 12,
    'paged' => $paged,
);

// إضافة نوع المحتوى
if ($post_type) {
    $args['post_type'] = $post_type;
} else {
    $args['post_type'] = array('post', 'fiqh_course', 'fiqh_lesson');
}

// إضافة تصنيف العلم
if ($science) {
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'fiqh_course_science',
            'field' => 'slug',
            'terms' => $science,
        ),
    );
}

// إضافة السنة
if ($year) {
    $args['date_query'] = array(
        array(
            'year' => $year,
        ),
    );
}

// تنفيذ الاستعلام
$search_results = new WP_Query($args);
?>

<div class="search-page">
    <!-- رأس صفحة البحث -->
    <div class="search-header">
        <div class="container">
            <h1 class="search-title">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>
                <?php
                if ($search_query) {
                    printf(__('نتائج البحث عن: %s', 'fiqhlearning'), '<span class="search-query">"' . esc_html($search_query) . '"</span>');
                } else {
                    _e('البحث في المدرسة', 'fiqhlearning');
                }
                ?>
            </h1>

            <?php if ($search_results->found_posts > 0) : ?>
                <p class="search-results-count">
                    <?php
                    printf(
                        _n(
                            'عثرنا على نتيجة واحدة',
                            'عثرنا على %s نتيجة',
                            $search_results->found_posts,
                            'fiqhlearning'
                        ),
                        '<strong>' . number_format_i18n($search_results->found_posts) . '</strong>'
                    );
                    ?>
                </p>
            <?php endif; ?>

            <!-- نموذج البحث -->
            <form role="search" method="get" class="search-form-advanced" action="<?php echo esc_url(home_url('/')); ?>">
                <div class="search-form-row">
                    <input type="search"
                           class="search-field"
                           placeholder="<?php echo esc_attr_x('ابحث عن مقرر، درس، أو مقالة...', 'placeholder', 'fiqhlearning'); ?>"
                           value="<?php echo get_search_query(); ?>"
                           name="s"
                           required />
                    <button type="submit" class="btn btn-primary search-submit">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.35-4.35"/>
                        </svg>
                        <?php _e('بحث', 'fiqhlearning'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="search-content-wrapper">
            <!-- الفلتر الجانبي -->
            <aside class="search-sidebar">
                <button class="filter-toggle" id="filterToggle">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="4" y1="21" x2="4" y2="14"/>
                        <line x1="4" y1="10" x2="4" y2="3"/>
                        <line x1="12" y1="21" x2="12" y2="12"/>
                        <line x1="12" y1="8" x2="12" y2="3"/>
                        <line x1="20" y1="21" x2="20" y2="16"/>
                        <line x1="20" y1="12" x2="20" y2="3"/>
                        <line x1="1" y1="14" x2="7" y2="14"/>
                        <line x1="9" y1="8" x2="15" y2="8"/>
                        <line x1="17" y1="16" x2="23" y2="16"/>
                    </svg>
                    <?php _e('فلترة النتائج', 'fiqhlearning'); ?>
                </button>

                <div class="filters-wrapper" id="filtersWrapper">
                    <form method="get" action="<?php echo esc_url(home_url('/')); ?>" class="search-filters">
                        <input type="hidden" name="s" value="<?php echo esc_attr($search_query); ?>">

                        <!-- نوع المحتوى -->
                        <div class="filter-group">
                            <h3><?php _e('نوع المحتوى', 'fiqhlearning'); ?></h3>
                            <select name="post_type" class="filter-select">
                                <option value=""><?php _e('جميع الأنواع', 'fiqhlearning'); ?></option>
                                <option value="fiqh_course" <?php selected($post_type, 'fiqh_course'); ?>><?php _e('المقررات', 'fiqhlearning'); ?></option>
                                <option value="fiqh_lesson" <?php selected($post_type, 'fiqh_lesson'); ?>><?php _e('الدروس', 'fiqhlearning'); ?></option>
                                <option value="post" <?php selected($post_type, 'post'); ?>><?php _e('المقالات', 'fiqhlearning'); ?></option>
                            </select>
                        </div>

                        <!-- العلوم -->
                        <?php
                        $sciences = get_terms(array(
                            'taxonomy' => 'fiqh_course_science',
                            'hide_empty' => true,
                        ));

                        if (!empty($sciences) && !is_wp_error($sciences)) :
                        ?>
                            <div class="filter-group">
                                <h3><?php _e('العلم', 'fiqhlearning'); ?></h3>
                                <select name="science" class="filter-select">
                                    <option value=""><?php _e('جميع العلوم', 'fiqhlearning'); ?></option>
                                    <?php foreach ($sciences as $sci) : ?>
                                        <option value="<?php echo esc_attr($sci->slug); ?>" <?php selected($science, $sci->slug); ?>>
                                            <?php echo esc_html($sci->name); ?> (<?php echo $sci->count; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <!-- السنة -->
                        <div class="filter-group">
                            <h3><?php _e('السنة', 'fiqhlearning'); ?></h3>
                            <select name="year" class="filter-select">
                                <option value=""><?php _e('جميع السنوات', 'fiqhlearning'); ?></option>
                                <?php
                                $current_year = date('Y');
                                for ($y = $current_year; $y >= $current_year - 5; $y--) :
                                ?>
                                    <option value="<?php echo $y; ?>" <?php selected($year, $y); ?>><?php echo $y; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- أزرار الإجراءات -->
                        <div class="filter-actions">
                            <button type="submit" class="btn btn-primary btn-block">
                                <?php _e('تطبيق الفلتر', 'fiqhlearning'); ?>
                            </button>
                            <a href="<?php echo esc_url(home_url('/?s=' . urlencode($search_query))); ?>" class="btn btn-secondary btn-block">
                                <?php _e('إعادة تعيين', 'fiqhlearning'); ?>
                            </a>
                        </div>
                    </form>
                </div>
            </aside>

            <!-- النتائج -->
            <main class="search-results-main">
                <?php if ($search_results->have_posts()) : ?>
                    <div class="search-results-grid">
                        <?php
                        while ($search_results->have_posts()) : $search_results->the_post();
                            $post_type_obj = get_post_type_object(get_post_type());
                            $post_type_name = $post_type_obj->labels->singular_name;
                        ?>
                            <article class="search-result-card card">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="result-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('medium', array('class' => 'result-image')); ?>
                                        </a>
                                        <span class="result-type-badge"><?php echo esc_html($post_type_name); ?></span>
                                    </div>
                                <?php else : ?>
                                    <div class="result-thumbnail result-thumbnail-placeholder">
                                        <a href="<?php the_permalink(); ?>">
                                            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <?php if (get_post_type() == 'fiqh_course') : ?>
                                                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                                                <?php elseif (get_post_type() == 'fiqh_lesson') : ?>
                                                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                                                <?php else : ?>
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                                    <polyline points="14 2 14 8 20 8"/>
                                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                                    <polyline points="10 9 9 9 8 9"/>
                                                <?php endif; ?>
                                            </svg>
                                        </a>
                                        <span class="result-type-badge"><?php echo esc_html($post_type_name); ?></span>
                                    </div>
                                <?php endif; ?>

                                <div class="result-content">
                                    <h2 class="result-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h2>

                                    <div class="result-meta">
                                        <span class="result-date">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                                <line x1="16" y1="2" x2="16" y2="6"/>
                                                <line x1="8" y1="2" x2="8" y2="6"/>
                                                <line x1="3" y1="10" x2="21" y2="10"/>
                                            </svg>
                                            <?php echo get_the_date(); ?>
                                        </span>

                                        <?php if (get_post_type() == 'post') : ?>
                                            <span class="result-author">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                                    <circle cx="12" cy="7" r="4"/>
                                                </svg>
                                                <?php the_author(); ?>
                                            </span>
                                        <?php endif; ?>

                                        <?php
                                        // عرض تصنيف العلم للمقررات
                                        if (get_post_type() == 'fiqh_course') :
                                            $terms = get_the_terms(get_the_ID(), 'fiqh_course_science');
                                            if ($terms && !is_wp_error($terms)) :
                                        ?>
                                                <span class="result-science">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                                                        <line x1="7" y1="7" x2="7.01" y2="7"/>
                                                    </svg>
                                                    <?php echo esc_html($terms[0]->name); ?>
                                                </span>
                                        <?php
                                            endif;
                                        endif;
                                        ?>
                                    </div>

                                    <?php if (has_excerpt()) : ?>
                                        <div class="result-excerpt">
                                            <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                                        </div>
                                    <?php endif; ?>

                                    <a href="<?php the_permalink(); ?>" class="result-link">
                                        <?php _e('قراءة المزيد', 'fiqhlearning'); ?>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="5" y1="12" x2="19" y2="12"/>
                                            <polyline points="12 5 19 12 12 19"/>
                                        </svg>
                                    </a>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>

                    <!-- الترقيم -->
                    <?php
                    $pagination = paginate_links(array(
                        'total' => $search_results->max_num_pages,
                        'current' => $paged,
                        'format' => '?paged=%#%',
                        'prev_text' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>',
                        'next_text' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>',
                        'type' => 'array',
                    ));

                    if ($pagination) :
                    ?>
                        <nav class="search-pagination">
                            <ul class="pagination">
                                <?php foreach ($pagination as $page) : ?>
                                    <li><?php echo $page; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>

                <?php else : ?>
                    <!-- لا توجد نتائج -->
                    <div class="no-results">
                        <div class="no-results-icon">
                            <svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="m21 21-4.35-4.35"/>
                                <line x1="11" y1="8" x2="11" y2="14"/>
                                <line x1="8" y1="11" x2="14" y2="11"/>
                            </svg>
                        </div>

                        <h2><?php _e('لم يُعثر على نتائج', 'fiqhlearning'); ?></h2>
                        <p><?php _e('عذراً، لم نتمكن من العثور على أي نتائج تطابق بحثك.', 'fiqhlearning'); ?></p>

                        <div class="no-results-tips">
                            <h3><?php _e('نصائح البحث:', 'fiqhlearning'); ?></h3>
                            <ul>
                                <li><?php _e('تأكد من كتابة الكلمات بشكل صحيح', 'fiqhlearning'); ?></li>
                                <li><?php _e('جرب استخدام كلمات مفتاحية أخرى', 'fiqhlearning'); ?></li>
                                <li><?php _e('جرب استخدام كلمات مفتاحية أكثر عمومية', 'fiqhlearning'); ?></li>
                                <li><?php _e('قلل عدد الكلمات المفتاحية', 'fiqhlearning'); ?></li>
                            </ul>
                        </div>

                        <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary btn-lg">
                            <?php _e('العودة للصفحة الرئيسية', 'fiqhlearning'); ?>
                        </a>
                    </div>
                <?php endif; ?>

                <?php wp_reset_postdata(); ?>
            </main>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterToggle = document.getElementById('filterToggle');
    const filtersWrapper = document.getElementById('filtersWrapper');

    if (filterToggle && filtersWrapper) {
        filterToggle.addEventListener('click', function() {
            filtersWrapper.classList.toggle('active');
            this.classList.toggle('active');
        });
    }
});
</script>

<?php
get_footer();
?>
