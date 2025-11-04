<?php
/**
 * قالب المقال الواحد
 *
 * @package FiqhLearning
 */

get_header();
?>

<?php
while (have_posts()) : the_post();
    $author_id = get_the_author_meta('ID');
    $author_name = get_the_author();
    $author_avatar = get_avatar_url($author_id, array('size' => 96));
    $reading_time = ceil(str_word_count(strip_tags(get_the_content())) / 200);
    ?>

    <!-- Hero Section -->
    <section class="post-hero" <?php if (has_post_thumbnail()) : ?>style="background-image: url('<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'full')); ?>');"<?php endif; ?>>
        <div class="post-hero-overlay"></div>
        <div class="container">
            <div class="post-hero-content">
                <div class="post-hero-meta">
                    <?php
                    $categories = get_the_category();
                    if (!empty($categories)) :
                        foreach ($categories as $category) :
                            ?>
                            <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="post-category-badge">
                                <?php echo esc_html($category->name); ?>
                            </a>
                        <?php
                        endforeach;
                    endif;
                    ?>
                </div>

                <h1 class="post-hero-title"><?php the_title(); ?></h1>

                <div class="post-hero-info">
                    <div class="post-author-info">
                        <img src="<?php echo esc_url($author_avatar); ?>" alt="<?php echo esc_attr($author_name); ?>" class="author-avatar">
                        <div class="author-details">
                            <a href="<?php echo get_author_posts_url($author_id); ?>" class="author-name">
                                <?php echo esc_html($author_name); ?>
                            </a>
                            <div class="post-date-reading">
                                <time datetime="<?php echo get_the_date('c'); ?>">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    <?php echo get_the_date(); ?>
                                </time>
                                <span class="separator">•</span>
                                <span class="reading-time">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                    <?php printf(get_theme_mod('single_post_reading_time_text', __('%d دقائق قراءة', 'fiqhlearning')), $reading_time); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<main id="primary" class="site-main single-post-main">
    <div class="container">
        <article id="post-<?php the_ID(); ?>" <?php post_class('single-post-article'); ?>>

                <!-- محتوى المقال -->
                <div class="post-content">
                    <?php
                    the_content();

                    wp_link_pages(array(
                        'before' => '<div class="page-links">' . get_theme_mod('single_post_pages_label', __('الصفحات:', 'fiqhlearning')),
                        'after'  => '</div>',
                    ));
                    ?>
                </div>

                <!-- تاجات -->
                <?php
                $tags = get_the_tags();
                if ($tags) :
                    ?>
                    <footer class="post-footer">
                        <div class="post-tags">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                <line x1="7" y1="7" x2="7.01" y2="7"></line>
                            </svg>
                            <?php
                            foreach ($tags as $tag) {
                                echo '<a href="' . get_tag_link($tag->term_id) . '" class="tag-badge">' . esc_html($tag->name) . '</a>';
                            }
                            ?>
                        </div>
                    </footer>
                <?php endif; ?>

            </article>

            <!-- معلومات الكاتب -->
            <div class="author-box card">
                <img src="<?php echo esc_url($author_avatar); ?>" alt="<?php echo esc_attr($author_name); ?>" class="author-box-avatar">
                <div class="author-box-content">
                    <h3 class="author-box-name">
                        <a href="<?php echo get_author_posts_url($author_id); ?>">
                            <?php echo esc_html($author_name); ?>
                        </a>
                    </h3>
                    <?php
                    $author_description = get_the_author_meta('description', $author_id);
                    if ($author_description) :
                        ?>
                        <p class="author-box-bio"><?php echo esc_html($author_description); ?></p>
                    <?php else : ?>
                        <p class="author-box-bio"><?php echo get_theme_mod('single_post_default_author_bio', __('كاتب في منصة FiqhLearning', 'fiqhlearning')); ?></p>
                    <?php endif; ?>
                    <a href="<?php echo get_author_posts_url($author_id); ?>" class="btn btn-outline btn-sm">
                        <?php echo get_theme_mod('single_post_view_all_posts', __('عرض جميع المقالات', 'fiqhlearning')); ?>
                    </a>
                </div>
            </div>

            <!-- مقالات ذات صلة -->
            <?php
            $related_args = array(
                'category__in' => wp_get_post_categories($post->ID),
                'post__not_in' => array($post->ID),
                'posts_per_page' => 3,
                'orderby' => 'rand',
            );
            $related_posts = new WP_Query($related_args);

            if ($related_posts->have_posts()) :
                ?>
                <section class="related-posts">
                    <h2 class="related-posts-title">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                        <?php echo get_theme_mod('single_post_related_title', __('مقالات ذات صلة', 'fiqhlearning')); ?>
                    </h2>
                    <div class="related-posts-grid">
                        <?php
                        while ($related_posts->have_posts()) : $related_posts->the_post();
                            ?>
                            <article class="related-post-card card">
                                <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>" class="related-post-thumbnail">
                                        <?php the_post_thumbnail('medium'); ?>
                                    </a>
                                <?php endif; ?>
                                <div class="related-post-content">
                                    <h3 class="related-post-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    <p class="related-post-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                                    <div class="related-post-meta">
                                        <time datetime="<?php echo get_the_date('c'); ?>">
                                            <?php echo get_the_date(); ?>
                                        </time>
                                    </div>
                                </div>
                            </article>
                        <?php
                        endwhile;
                        wp_reset_postdata();
                        ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- التنقل بين المقالات -->
            <nav class="post-navigation">
                <?php
                $prev_post = get_previous_post();
                $next_post = get_next_post();

                if ($prev_post || $next_post) :
                    ?>
                    <div class="post-navigation-grid">
                        <?php if ($prev_post) : ?>
                            <a href="<?php echo get_permalink($prev_post); ?>" class="post-navigation-link prev-post card">
                                <span class="nav-direction">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="15 18 9 12 15 6"></polyline>
                                    </svg>
                                    <?php echo get_theme_mod('single_post_prev_text', __('المقال السابق', 'fiqhlearning')); ?>
                                </span>
                                <span class="nav-title"><?php echo get_the_title($prev_post); ?></span>
                            </a>
                        <?php endif; ?>

                        <?php if ($next_post) : ?>
                            <a href="<?php echo get_permalink($next_post); ?>" class="post-navigation-link next-post card">
                                <span class="nav-direction">
                                    <?php echo get_theme_mod('single_post_next_text', __('المقال التالي', 'fiqhlearning')); ?>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </span>
                                <span class="nav-title"><?php echo get_the_title($next_post); ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </nav>

        </article>

        <!-- التعليقات -->
        <?php
        if (comments_open() || get_comments_number()) :
            comments_template();
        endif;
        ?>

    </div>
</main>

<?php
endwhile;
?>

<?php
get_footer();
