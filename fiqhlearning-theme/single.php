<?php
/**
 * قالب المقال الواحد
 *
 * @package FiqhLearning
 */

get_header();
?>

<main id="primary" class="site-main single-post-main">

        <?php
        while (have_posts()) : the_post();
            $author_id = get_the_author_meta('ID');
            $author_name = get_the_author();
            $author_avatar = get_avatar_url($author_id, array('size' => 96));
            $reading_time = ceil(str_word_count(strip_tags(get_the_content())) / 200); // متوسط 200 كلمة في الدقيقة
            $has_thumbnail = has_post_thumbnail();
            ?>

            <!-- Hero Section -->
            <div class="post-hero" <?php if ($has_thumbnail) : ?>style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.7)), url('<?php echo get_the_post_thumbnail_url(null, 'full'); ?>');"<?php endif; ?>>
                <div class="container">
                    <div class="post-hero-content">
                        <!-- التصنيفات -->
                        <div class="post-hero-categories">
                            <?php
                            $categories = get_the_category();
                            if (!empty($categories)) :
                                foreach ($categories as $category) :
                                    ?>
                                    <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="post-category-badge-hero">
                                        <?php echo esc_html($category->name); ?>
                                    </a>
                                <?php
                                endforeach;
                            endif;
                            ?>
                        </div>

                        <!-- العنوان -->
                        <h1 class="post-hero-title"><?php the_title(); ?></h1>

                        <!-- معلومات الكاتب والتاريخ -->
                        <div class="post-hero-meta">
                            <div class="post-hero-author">
                                <img src="<?php echo esc_url($author_avatar); ?>" alt="<?php echo esc_attr($author_name); ?>" class="hero-author-avatar">
                                <div class="hero-author-details">
                                    <a href="<?php echo get_author_posts_url($author_id); ?>" class="hero-author-name">
                                        <?php echo esc_html($author_name); ?>
                                    </a>
                                    <div class="hero-post-info">
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
                                            <?php echo $reading_time; ?> <?php _e('دقائق قراءة', 'fiqhlearning'); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- محتوى المقال -->
            <div class="container">
            <article id="post-<?php the_ID(); ?>" <?php post_class('single-post-article'); ?>>

                <!-- محتوى المقال -->
                <div class="post-content">
                    <?php
                    the_content();

                    wp_link_pages(array(
                        'before' => '<div class="page-links">' . __('الصفحات:', 'fiqhlearning'),
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
                        <p class="author-box-bio"><?php _e('كاتب في منصة FiqhLearning', 'fiqhlearning'); ?></p>
                    <?php endif; ?>
                    <a href="<?php echo get_author_posts_url($author_id); ?>" class="btn btn-outline btn-sm">
                        <?php _e('عرض جميع المقالات', 'fiqhlearning'); ?>
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
                        <?php _e('مقالات ذات صلة', 'fiqhlearning'); ?>
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
                                    <?php _e('المقال السابق', 'fiqhlearning'); ?>
                                </span>
                                <span class="nav-title"><?php echo get_the_title($prev_post); ?></span>
                            </a>
                        <?php endif; ?>

                        <?php if ($next_post) : ?>
                            <a href="<?php echo get_permalink($next_post); ?>" class="post-navigation-link next-post card">
                                <span class="nav-direction">
                                    <?php _e('المقال التالي', 'fiqhlearning'); ?>
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

            <!-- التعليقات -->
            <?php
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;
            ?>

        <?php
        endwhile;
        ?>

    </div>
    </div><!-- .container -->
</main>

<style>
/* Hero Section للمقال */
.post-hero {
    background-color: var(--color-primary-dark);
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    padding: 100px 0 60px;
    position: relative;
    margin-bottom: 50px;
}

.post-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(11, 94, 59, 0.85) 0%, rgba(8, 66, 41, 0.9) 100%);
    z-index: 1;
}

.post-hero-content {
    position: relative;
    z-index: 2;
    max-width: 900px;
    margin: 0 auto;
    text-align: center;
    color: var(--color-white);
}

.post-hero-categories {
    margin-bottom: 20px;
}

.post-category-badge-hero {
    display: inline-block;
    padding: 6px 16px;
    background: rgba(255, 255, 255, 0.2);
    color: var(--color-white);
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    margin: 0 5px;
    backdrop-filter: blur(10px);
    transition: all var(--transition-base);
}

.post-category-badge-hero:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
}

.post-hero-title {
    font-size: 3rem;
    line-height: 1.2;
    margin-bottom: 30px;
    color: var(--color-white);
    font-weight: 800;
}

.post-hero-meta {
    display: flex;
    justify-content: center;
}

.post-hero-author {
    display: flex;
    align-items: center;
    gap: 15px;
}

.hero-author-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: 3px solid rgba(255, 255, 255, 0.3);
}

.hero-author-details {
    text-align: right;
}

.hero-author-name {
    color: var(--color-white);
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    display: block;
    margin-bottom: 5px;
}

.hero-author-name:hover {
    text-decoration: underline;
}

.hero-post-info {
    display: flex;
    align-items: center;
    gap: 10px;
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.875rem;
}

.hero-post-info svg {
    vertical-align: middle;
}

.hero-post-info .separator {
    opacity: 0.5;
}

/* تحسين محتوى المقال */
.single-post-article {
    background: var(--color-white);
    padding: 40px 50px;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
    margin-bottom: 40px;
}

.post-content {
    font-size: 1.125rem;
    line-height: 1.8;
    color: var(--color-dark-gray);
}

.post-content h2 {
    color: var(--color-primary);
    font-size: 2rem;
    margin-top: 40px;
    margin-bottom: 20px;
}

.post-content h3 {
    color: var(--color-primary-dark);
    font-size: 1.5rem;
    margin-top: 30px;
    margin-bottom: 15px;
}

.post-content p {
    margin-bottom: 20px;
}

.post-content img {
    max-width: 100%;
    height: auto;
    border-radius: var(--radius-lg);
    margin: 30px 0;
}

.post-content blockquote {
    border-right: 4px solid var(--color-primary);
    padding: 20px 30px;
    margin: 30px 0;
    background: var(--color-light);
    border-radius: var(--radius-md);
    font-style: italic;
    color: var(--color-dark-gray);
}

.post-content ul,
.post-content ol {
    margin: 20px 0;
    padding-right: 30px;
}

.post-content li {
    margin-bottom: 10px;
}

/* Dark Mode */
body.dark-mode .post-hero::before {
    background: linear-gradient(135deg, rgba(11, 94, 59, 0.95) 0%, rgba(8, 66, 41, 1) 100%);
}

body.dark-mode .single-post-article {
    background: var(--color-light);
}

body.dark-mode .post-content {
    color: var(--color-text);
}

body.dark-mode .post-content blockquote {
    background: var(--color-light-gray);
}

/* Responsive */
@media (max-width: 768px) {
    .post-hero {
        padding: 60px 0 40px;
    }

    .post-hero-title {
        font-size: 2rem;
    }

    .post-hero-author {
        flex-direction: column;
        text-align: center;
    }

    .hero-author-details {
        text-align: center;
    }

    .single-post-article {
        padding: 30px 20px;
    }

    .post-content {
        font-size: 1rem;
    }

    .post-content h2 {
        font-size: 1.5rem;
    }
}
</style>

<?php
get_footer();
