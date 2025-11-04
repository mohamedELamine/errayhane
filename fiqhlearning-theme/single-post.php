<?php
/**
 * B'D( 'DEB'D) 'DA1/J) E9 'D*9DJB'*
 *
 * @package FiqhLearning
 */

get_header();

while (have_posts()) : the_post();
    $categories = get_the_category();
    $tags = get_the_tags();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>

    <!-- 1#3 'DEB'D) -->
    <header class="post-header">
        <div class="container-narrow">
            <?php if (!empty($categories)) : ?>
                <div class="post-categories">
                    <?php foreach ($categories as $category) : ?>
                        <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="post-category">
                            <?php echo esc_html($category->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <h1 class="post-title"><?php the_title(); ?></h1>

            <div class="post-meta">
                <span class="post-author">
                    <?php echo get_avatar(get_the_author_meta('ID'), 32); ?>
                    <span>
                        <strong><?php the_author(); ?></strong>
                        <small><?php _e(''DC'*(', 'fiqhlearning'); ?></small>
                    </span>
                </span>
                <span class="post-date">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <?php echo get_the_date(); ?>
                </span>
                <span class="post-reading-time">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                    </svg>
                    <?php
                    $content = get_post_field('post_content', get_the_ID());
                    $word_count = str_word_count(strip_tags($content));
                    $reading_time = ceil($word_count / 200);
                    printf(_n('%s /BJB) B1'!)', '%s /BJB) B1'!)', $reading_time, 'fiqhlearning'), $reading_time);
                    ?>
                </span>
                <span class="post-comments-count">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                    <?php comments_number(__('D' *9DJB'*', 'fiqhlearning'), __('*9DJB H'-/', 'fiqhlearning'), __('% *9DJB', 'fiqhlearning')); ?>
                </span>
            </div>
        </div>
    </header>

    <!-- 5H1) 'DEB'D) -->
    <?php if (has_post_thumbnail()) : ?>
        <div class="post-featured-image">
            <div class="container-narrow">
                <?php the_post_thumbnail('full'); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- E-*HI 'DEB'D) -->
    <div class="post-content">
        <div class="container-narrow">
            <?php the_content(); ?>

            <?php
            wp_link_pages(array(
                'before' => '<div class="page-links">' . __(''D5A-'*:', 'fiqhlearning'),
                'after'  => '</div>',
            ));
            ?>
        </div>
    </div>

    <!-- 'DH3HE -->
    <?php if ($tags) : ?>
        <div class="post-tags">
            <div class="container-narrow">
                <div class="tags-wrapper">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                        <line x1="7" y1="7" x2="7.01" y2="7"></line>
                    </svg>
                    <?php foreach ($tags as $tag) : ?>
                        <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="post-tag">
                            <?php echo esc_html($tag->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- 'DE4'1C) 'D',*E'9J) -->
    <div class="post-share">
        <div class="container-narrow">
            <div class="share-wrapper">
                <span class="share-label"><?php _e('4'1C 'DEB'D):', 'fiqhlearning'); ?></span>
                <div class="share-buttons">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>"
                       target="_blank"
                       class="share-btn facebook"
                       title="<?php _e('E4'1C) 9DI Facebook', 'fiqhlearning'); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>"
                       target="_blank"
                       class="share-btn twitter"
                       title="<?php _e('E4'1C) 9DI Twitter', 'fiqhlearning'); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                    </a>
                    <a href="https://wa.me/?text=<?php echo urlencode(get_the_title() . ' - ' . get_permalink()); ?>"
                       target="_blank"
                       class="share-btn whatsapp"
                       title="<?php _e('E4'1C) 9DI WhatsApp', 'fiqhlearning'); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                    </a>
                    <a href="https://t.me/share/url?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>"
                       target="_blank"
                       class="share-btn telegram"
                       title="<?php _e('E4'1C) 9DI Telegram', 'fiqhlearning'); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- E9DHE'* 'DC'*( -->
    <div class="post-author-bio">
        <div class="container-narrow">
            <div class="author-bio-card">
                <div class="author-avatar">
                    <?php echo get_avatar(get_the_author_meta('ID'), 80); ?>
                </div>
                <div class="author-info">
                    <h3><?php the_author(); ?></h3>
                    <?php if (get_the_author_meta('description')) : ?>
                        <p><?php the_author_meta('description'); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- 'D*9DJB'* -->
    <?php
    if (comments_open() || get_comments_number()) :
    ?>
        <div class="post-comments-section">
            <div class="container-narrow">
                <?php comments_template(); ?>
            </div>
        </div>
    <?php endif; ?>

</article>

<!-- EB'D'* 0'* 5D) -->
<?php
$related_posts = get_posts(array(
    'category__in' => wp_get_post_categories(get_the_ID()),
    'numberposts' => 3,
    'post__not_in' => array(get_the_ID()),
));

if ($related_posts) :
?>
    <section class="related-posts">
        <div class="container">
            <h2 class="section-title"><?php _e('EB'D'* 0'* 5D)', 'fiqhlearning'); ?></h2>
            <div class="related-posts-grid">
                <?php foreach ($related_posts as $post) : setup_postdata($post); ?>
                    <article class="related-post-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="related-post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="related-post-content">
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <p><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                        </div>
                    </article>
                <?php endforeach; wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php endwhile; ?>

<style>
.container-narrow {
    max-width: 800px;
    margin: 0 auto;
    padding: 0 var(--spacing-lg);
}

/* 1#3 'DEB'D) */
.post-header {
    padding: var(--spacing-4xl) 0 var(--spacing-2xl);
}

.post-categories {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-lg);
}

.post-category {
    display: inline-block;
    padding: var(--spacing-xs) var(--spacing-md);
    background: var(--color-primary-light);
    color: var(--color-primary-dark);
    border-radius: var(--radius-full);
    font-size: var(--text-sm);
    font-weight: 600;
    text-decoration: none;
    transition: all var(--transition-base);
}

.post-category:hover {
    background: var(--color-primary);
    color: var(--color-white);
}

.post-title {
    font-size: clamp(2rem, 5vw, 3rem);
    line-height: 1.2;
    margin-bottom: var(--spacing-lg);
    color: var(--color-dark);
}

.post-meta {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-lg);
    color: var(--color-dark-gray);
    font-size: var(--text-sm);
}

.post-meta > span {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.post-author {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.post-author img {
    border-radius: 50%;
}

.post-author span {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.post-author small {
    font-size: var(--text-xs);
    opacity: 0.7;
}

/* 5H1) 'DEB'D) */
.post-featured-image {
    margin-bottom: var(--spacing-2xl);
}

.post-featured-image img {
    width: 100%;
    height: auto;
    border-radius: var(--radius-lg);
}

/* E-*HI 'DEB'D) */
.post-content {
    padding: var(--spacing-2xl) 0;
    font-size: var(--text-lg);
    line-height: 1.8;
    color: var(--color-dark);
}

.post-content p {
    margin-bottom: var(--spacing-lg);
}

.post-content h2,
.post-content h3,
.post-content h4 {
    margin-top: var(--spacing-xl);
    margin-bottom: var(--spacing-md);
    color: var(--color-primary-dark);
}

.post-content ul,
.post-content ol {
    margin-bottom: var(--spacing-lg);
    padding-right: var(--spacing-xl);
}

.post-content li {
    margin-bottom: var(--spacing-sm);
}

.post-content img {
    max-width: 100%;
    height: auto;
    border-radius: var(--radius-md);
}

.post-content blockquote {
    margin: var(--spacing-xl) 0;
    padding: var(--spacing-lg);
    background: var(--color-light);
    border-right: 4px solid var(--color-primary);
    font-style: italic;
}

/* 'DH3HE */
.post-tags {
    padding: var(--spacing-2xl) 0;
    border-top: 1px solid var(--color-light-gray);
}

.tags-wrapper {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: var(--spacing-sm);
}

.tags-wrapper svg {
    color: var(--color-dark-gray);
}

.post-tag {
    display: inline-block;
    padding: var(--spacing-xs) var(--spacing-md);
    background: var(--color-light);
    color: var(--color-dark);
    border-radius: var(--radius-full);
    font-size: var(--text-sm);
    text-decoration: none;
    transition: all var(--transition-base);
}

.post-tag:hover {
    background: var(--color-primary);
    color: var(--color-white);
}

/* 'DE4'1C) */
.post-share {
    padding: var(--spacing-2xl) 0;
    background: var(--color-light);
}

.share-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: var(--spacing-md);
}

.share-label {
    font-weight: 600;
    color: var(--color-dark);
}

.share-buttons {
    display: flex;
    gap: var(--spacing-sm);
}

.share-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    color: var(--color-white);
    transition: all var(--transition-base);
}

.share-btn:hover {
    transform: translateY(-3px);
}

.share-btn.facebook { background: #1877f2; }
.share-btn.twitter { background: #1da1f2; }
.share-btn.whatsapp { background: #25d366; }
.share-btn.telegram { background: #0088cc; }

/* E9DHE'* 'DC'*( */
.post-author-bio {
    padding: var(--spacing-2xl) 0;
}

.author-bio-card {
    display: flex;
    gap: var(--spacing-lg);
    padding: var(--spacing-xl);
    background: var(--color-light);
    border-radius: var(--radius-lg);
}

.author-avatar img {
    border-radius: 50%;
}

.author-info h3 {
    margin-bottom: var(--spacing-sm);
    color: var(--color-dark);
}

.author-info p {
    color: var(--color-dark-gray);
    line-height: 1.6;
}

/* 'D*9DJB'* */
.post-comments-section {
    padding: var(--spacing-3xl) 0;
    background: var(--color-light);
}

/* EB'D'* 0'* 5D) */
.related-posts {
    padding: var(--spacing-3xl) 0;
}

.related-posts .section-title {
    text-align: center;
    margin-bottom: var(--spacing-2xl);
}

.related-posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: var(--spacing-xl);
}

.related-post-card {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-base);
    transition: all var(--transition-base);
}

.related-post-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.related-post-thumbnail {
    height: 180px;
    overflow: hidden;
}

.related-post-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.related-post-content {
    padding: var(--spacing-lg);
}

.related-post-content h3 {
    margin-bottom: var(--spacing-sm);
    font-size: var(--text-lg);
}

.related-post-content h3 a {
    color: var(--color-dark);
    text-decoration: none;
}

.related-post-content h3 a:hover {
    color: var(--color-primary);
}

.related-post-content p {
    color: var(--color-dark-gray);
    font-size: var(--text-sm);
}

@media (max-width: 768px) {
    .post-meta {
        flex-direction: column;
        gap: var(--spacing-sm);
    }

    .share-wrapper {
        flex-direction: column;
        text-align: center;
    }

    .author-bio-card {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<?php get_footer(); ?>
