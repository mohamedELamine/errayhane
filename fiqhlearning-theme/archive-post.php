<?php
/**
 * B'D( #14JA 'DEB'D'* ('DE/HF))
 * E9 'D(-+ 'DAD'*1 H'D@ Pagination
 *
 * @package FiqhLearning
 */

get_header();

// 'D-5HD 9DI E9'ED'* 'D(-+ H'DAD*1)
$search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$category_filter = isset($_GET['cat']) ? intval($_GET['cat']) : 0;
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

// %9/'/ '3*9D'E 'DEB'D'*
$args = array(
    'post_type' => 'post',
    'posts_per_page' => 9,
    'paged' => $paged,
    'post_status' => 'publish',
);

if ($search_query) {
    $args['s'] = $search_query;
}

if ($category_filter) {
    $args['cat'] = $category_filter;
}

$blog_query = new WP_Query($args);
$total_posts = $blog_query->found_posts;
?>

<main class="blog-archive-page">

    <!-- Hero Section -->
    <section class="blog-hero">
        <div class="container">
            <div class="blog-hero-content">
                <div class="hero-badge">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    <?php echo get_theme_mod('blog_badge', __(''DE/HF)', 'fiqhlearning')); ?>
                </div>
                <h1 class="page-title">
                    <?php echo get_theme_mod('blog_title', __(''DEB'D'* H'D#.('1', 'fiqhlearning')); ?>
                </h1>
                <p class="page-description">
                    <?php echo get_theme_mod('blog_description', __(''7D9 9DI ".1 'DEB'D'* H'D#.('1 'D.'5) ('DE/13) H'D9DHE 'D419J)', 'fiqhlearning')); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- 'D(-+ H'DAD'*1 -->
    <section class="blog-filters-section">
        <div class="container">
            <div class="blog-filters">
                <!-- FEH0, 'D(-+ -->
                <form method="get" class="blog-search-form" role="search">
                    <div class="search-input-wrapper">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                        <input
                            type="search"
                            name="s"
                            placeholder="<?php _e(''(-+ AJ 'DEB'D'*...', 'fiqhlearning'); ?>"
                            value="<?php echo esc_attr($search_query); ?>"
                            class="search-input"
                        >
                        <?php if ($category_filter) : ?>
                            <input type="hidden" name="cat" value="<?php echo $category_filter; ?>">
                        <?php endif; ?>
                        <button type="submit" class="search-button">
                            <?php echo get_theme_mod('blog_search_button', __('(-+', 'fiqhlearning')); ?>
                        </button>
                    </div>
                </form>

                <!-- AD'*1 'D*5FJA'* -->
                <div class="category-filters">
                    <a href="<?php echo get_post_type_archive_link('post'); ?>"
                       class="category-filter <?php echo !$category_filter ? 'active' : ''; ?>">
                        <?php echo get_theme_mod('blog_all_categories', __(',EJ9 'D*5FJA'*', 'fiqhlearning')); ?>
                    </a>
                    <?php
                    $categories = get_categories(array(
                        'orderby' => 'count',
                        'order' => 'DESC',
                        'hide_empty' => true,
                    ));

                    foreach ($categories as $category) :
                        $filter_url = add_query_arg(array('cat' => $category->term_id), get_post_type_archive_link('post'));
                        if ($search_query) {
                            $filter_url = add_query_arg('s', $search_query, $filter_url);
                        }
                    ?>
                        <a href="<?php echo esc_url($filter_url); ?>"
                           class="category-filter <?php echo $category_filter == $category->term_id ? 'active' : ''; ?>">
                            <?php echo esc_html($category->name); ?>
                            <span class="count">(<?php echo $category->count; ?>)</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 9/'/ 'DF*'&, -->
            <div class="results-info">
                <?php if ($search_query) : ?>
                    <p><?php printf(__('F*'&, 'D(-+ 9F: <strong>%s</strong>', 'fiqhlearning'), esc_html($search_query)); ?></p>
                <?php endif; ?>
                <p class="total-results">
                    <?php printf(_n('9O+1 9DI EB'D) H'-/)', '9O+1 9DI %s EB'D)', $total_posts, 'fiqhlearning'), number_format_i18n($total_posts)); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- B'&E) 'DEB'D'* -->
    <section class="blog-posts-section">
        <div class="container">
            <?php if ($blog_query->have_posts()) : ?>
                <div class="blog-grid">
                    <?php while ($blog_query->have_posts()) : $blog_query->the_post(); ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('blog-card'); ?>>
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="blog-card-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium_large', array('loading' => 'lazy')); ?>
                                    </a>
                                    <?php
                                    $categories = get_the_category();
                                    if (!empty($categories)) :
                                    ?>
                                        <span class="blog-category-badge">
                                            <?php echo esc_html($categories[0]->name); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <div class="blog-card-content">
                                <div class="blog-card-meta">
                                    <span class="post-date">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        <?php echo get_the_date(); ?>
                                    </span>
                                    <span class="post-author">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                        <?php the_author(); ?>
                                    </span>
                                    <span class="post-comments">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                        </svg>
                                        <?php comments_number('0', '1', '%'); ?>
                                    </span>
                                </div>

                                <h2 class="blog-card-title">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_title(); ?>
                                    </a>
                                </h2>

                                <div class="blog-card-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 25); ?>
                                </div>

                                <a href="<?php the_permalink(); ?>" class="read-more-btn">
                                    <?php _e(''B1# 'DE2J/', 'fiqhlearning'); ?>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                        <polyline points="12 5 19 12 12 19"></polyline>
                                    </svg>
                                </a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>

                <!-- Pagination -->
                <?php if ($blog_query->max_num_pages > 1) : ?>
                    <div class="blog-pagination">
                        <?php
                        echo paginate_links(array(
                            'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                            'format' => '?paged=%#%',
                            'current' => max(1, $paged),
                            'total' => $blog_query->max_num_pages,
                            'prev_text' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg> ' . __(''D3'(B', 'fiqhlearning'),
                            'next_text' => __(''D*'DJ', 'fiqhlearning') . ' <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>',
                            'type' => 'list',
                            'end_size' => 2,
                            'mid_size' => 2,
                        ));
                        ?>
                    </div>
                <?php endif; ?>

            <?php else : ?>
                <div class="no-posts-found">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <h2><?php _e('DE J*E 'D9+H1 9DI EB'D'*', 'fiqhlearning'); ?></h2>
                    <p><?php _e(',1( 'D(-+ (CDE'* E.*DA) #H *5A- ,EJ9 'D*5FJA'*', 'fiqhlearning'); ?></p>
                    <a href="<?php echo get_post_type_archive_link('post'); ?>" class="btn btn-primary">
                        <?php _e('916 ,EJ9 'DEB'D'*', 'fiqhlearning'); ?>
                    </a>
                </div>
            <?php endif; ?>

            <?php wp_reset_postdata(); ?>
        </div>
    </section>

</main>

<style>
/* Hero Section */
.blog-hero {
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
    color: var(--color-white);
    padding: var(--spacing-4xl) 0 var(--spacing-3xl);
    text-align: center;
}

.blog-hero-content {
    max-width: 800px;
    margin: 0 auto;
}

/* 'D(-+ H'DAD'*1 */
.blog-filters-section {
    padding: var(--spacing-2xl) 0;
    background: var(--color-light);
}

.blog-filters {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-lg);
    margin-bottom: var(--spacing-lg);
}

.blog-search-form {
    width: 100%;
}

.search-input-wrapper {
    display: flex;
    align-items: center;
    background: var(--color-white);
    border: 2px solid var(--color-light-gray);
    border-radius: var(--radius-lg);
    padding: var(--spacing-xs);
    gap: var(--spacing-sm);
    transition: border-color var(--transition-base);
}

.search-input-wrapper:focus-within {
    border-color: var(--color-primary);
}

.search-input-wrapper svg {
    color: var(--color-dark-gray);
    flex-shrink: 0;
    margin-right: var(--spacing-sm);
}

.search-input {
    flex: 1;
    border: none;
    outline: none;
    font-size: var(--text-base);
    padding: var(--spacing-sm);
}

.search-button {
    background: var(--color-primary);
    color: var(--color-white);
    border: none;
    padding: var(--spacing-sm) var(--spacing-xl);
    border-radius: var(--radius-md);
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
}

.search-button:hover {
    background: var(--color-primary-dark);
}

.category-filters {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-sm);
}

.category-filter {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    padding: var(--spacing-sm) var(--spacing-lg);
    background: var(--color-white);
    border: 2px solid var(--color-light-gray);
    border-radius: var(--radius-full);
    color: var(--color-dark);
    text-decoration: none;
    font-weight: 500;
    transition: all var(--transition-base);
}

.category-filter:hover,
.category-filter.active {
    background: var(--color-primary);
    color: var(--color-white);
    border-color: var(--color-primary);
}

.category-filter .count {
    opacity: 0.7;
    font-size: var(--text-sm);
}

.results-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: var(--spacing-lg);
    border-top: 1px solid var(--color-light-gray);
    color: var(--color-dark-gray);
}

/* 'DEB'D'* */
.blog-posts-section {
    padding: var(--spacing-3xl) 0;
}

.blog-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: var(--spacing-2xl);
}

.blog-card {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-base);
    transition: all var(--transition-base);
    display: flex;
    flex-direction: column;
}

.blog-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.blog-card-thumbnail {
    position: relative;
    width: 100%;
    height: 220px;
    overflow: hidden;
}

.blog-card-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform var(--transition-base);
}

.blog-card:hover .blog-card-thumbnail img {
    transform: scale(1.05);
}

.blog-category-badge {
    position: absolute;
    top: var(--spacing-md);
    right: var(--spacing-md);
    background: var(--color-primary);
    color: var(--color-white);
    padding: var(--spacing-xs) var(--spacing-md);
    border-radius: var(--radius-full);
    font-size: var(--text-sm);
    font-weight: 600;
}

.blog-card-content {
    padding: var(--spacing-lg);
    flex: 1;
    display: flex;
    flex-direction: column;
}

.blog-card-meta {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-md);
    font-size: var(--text-sm);
    color: var(--color-dark-gray);
}

.blog-card-meta > span {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.blog-card-title {
    font-size: var(--text-xl);
    margin-bottom: var(--spacing-sm);
    line-height: 1.4;
}

.blog-card-title a {
    color: var(--color-dark);
    text-decoration: none;
    transition: color var(--transition-base);
}

.blog-card-title a:hover {
    color: var(--color-primary);
}

.blog-card-excerpt {
    color: var(--color-dark-gray);
    line-height: 1.6;
    margin-bottom: var(--spacing-md);
    flex: 1;
}

.read-more-btn {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    color: var(--color-primary);
    font-weight: 600;
    text-decoration: none;
    transition: all var(--transition-base);
    align-self: flex-start;
}

.read-more-btn:hover {
    gap: var(--spacing-sm);
    color: var(--color-primary-dark);
}

/* Pagination */
.blog-pagination {
    margin-top: var(--spacing-3xl);
    display: flex;
    justify-content: center;
}

.blog-pagination ul {
    display: flex;
    gap: var(--spacing-sm);
    list-style: none;
    padding: 0;
    margin: 0;
}

.blog-pagination li {
    display: inline-block;
}

.blog-pagination a,
.blog-pagination span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 44px;
    height: 44px;
    padding: var(--spacing-sm) var(--spacing-md);
    background: var(--color-white);
    border: 2px solid var(--color-light-gray);
    border-radius: var(--radius-md);
    color: var(--color-dark);
    text-decoration: none;
    font-weight: 500;
    transition: all var(--transition-base);
}

.blog-pagination a:hover {
    background: var(--color-primary);
    color: var(--color-white);
    border-color: var(--color-primary);
}

.blog-pagination .current {
    background: var(--color-primary);
    color: var(--color-white);
    border-color: var(--color-primary);
}

/* D' *H,/ F*'&, */
.no-posts-found {
    text-align: center;
    padding: var(--spacing-4xl) var(--spacing-lg);
}

.no-posts-found svg {
    color: var(--color-dark-gray);
    margin-bottom: var(--spacing-lg);
}

.no-posts-found h2 {
    font-size: var(--text-2xl);
    color: var(--color-dark);
    margin-bottom: var(--spacing-md);
}

.no-posts-found p {
    color: var(--color-dark-gray);
    margin-bottom: var(--spacing-xl);
}

/* Responsive */
@media (max-width: 768px) {
    .blog-grid {
        grid-template-columns: 1fr;
    }

    .results-info {
        flex-direction: column;
        gap: var(--spacing-sm);
        text-align: center;
    }

    .category-filters {
        justify-content: center;
    }
}
</style>

<?php get_footer(); ?>
