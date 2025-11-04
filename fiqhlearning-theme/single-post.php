<?php
/**
 * قالب المقال الفردي مع التعليقات
 *
 * @package FiqhLearning
 */

get_header();

while (have_posts()) : the_post();
    $categories = get_the_category();
    $tags = get_the_tags();
    $content = get_the_content();
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200);
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>

    <!-- رأس المقالة -->
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
                        <small><?php echo get_theme_mod('single_post_author_label', __('الكاتب', 'fiqhlearning')); ?></small>
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
                    <?php printf(get_theme_mod('single_post_reading_time_format', __('%d دقائق قراءة', 'fiqhlearning')), $reading_time); ?>
                </span>
                <?php if (comments_open() || get_comments_number()) : ?>
                    <span class="post-comments-count">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                        <?php
                        $comments_count = get_comments_number();
                        if ($comments_count == 0) {
                            echo get_theme_mod('single_post_no_comments', __('لا توجد تعليقات', 'fiqhlearning'));
                        } else {
                            printf(
                                get_theme_mod('single_post_comments_format', __('%d تعليق', 'fiqhlearning')),
                                $comments_count
                            );
                        }
                        ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- صورة المقالة البارزة -->
    <?php if (has_post_thumbnail()) : ?>
        <div class="post-featured-image">
            <div class="container-narrow">
                <?php the_post_thumbnail('large', array('class' => 'featured-image')); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- محتوى المقالة -->
    <div class="post-content">
        <div class="container-narrow">
            <?php the_content(); ?>
        </div>
    </div>

    <!-- الوسوم -->
    <?php if (!empty($tags)) : ?>
        <div class="post-tags">
            <div class="container-narrow">
                <div class="tags-label">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                        <line x1="7" y1="7" x2="7.01" y2="7"></line>
                    </svg>
                    <strong><?php echo get_theme_mod('single_post_tags_label', __('الوسوم:', 'fiqhlearning')); ?></strong>
                </div>
                <div class="tags-list">
                    <?php foreach ($tags as $tag) : ?>
                        <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="tag">
                            <?php echo esc_html($tag->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- أزرار المشاركة الاجتماعية -->
    <div class="post-share">
        <div class="container-narrow">
            <div class="share-section">
                <h3><?php echo get_theme_mod('single_post_share_title', __('شارك المقال', 'fiqhlearning')); ?></h3>
                <div class="share-buttons">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="share-button facebook"
                       aria-label="<?php echo get_theme_mod('single_post_share_facebook', __('شارك على فيسبوك', 'fiqhlearning')); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        <span><?php echo get_theme_mod('single_post_share_facebook_text', __('فيسبوك', 'fiqhlearning')); ?></span>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="share-button twitter"
                       aria-label="<?php echo get_theme_mod('single_post_share_twitter', __('شارك على تويتر', 'fiqhlearning')); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                        <span><?php echo get_theme_mod('single_post_share_twitter_text', __('تويتر', 'fiqhlearning')); ?></span>
                    </a>
                    <a href="https://api.whatsapp.com/send?text=<?php echo urlencode(get_the_title() . ' ' . get_permalink()); ?>"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="share-button whatsapp"
                       aria-label="<?php echo get_theme_mod('single_post_share_whatsapp', __('شارك على واتساب', 'fiqhlearning')); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        <span><?php echo get_theme_mod('single_post_share_whatsapp_text', __('واتساب', 'fiqhlearning')); ?></span>
                    </a>
                    <a href="https://t.me/share/url?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="share-button telegram"
                       aria-label="<?php echo get_theme_mod('single_post_share_telegram', __('شارك على تيليجرام', 'fiqhlearning')); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                        </svg>
                        <span><?php echo get_theme_mod('single_post_share_telegram_text', __('تيليجرام', 'fiqhlearning')); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- معلومات المؤلف -->
    <div class="post-author-bio">
        <div class="container-narrow">
            <div class="author-card">
                <div class="author-avatar">
                    <?php echo get_avatar(get_the_author_meta('ID'), 80); ?>
                </div>
                <div class="author-info">
                    <h3><?php echo get_theme_mod('single_post_author_bio_title', __('عن المؤلف', 'fiqhlearning')); ?></h3>
                    <h4><?php the_author(); ?></h4>
                    <?php if (get_the_author_meta('description')) : ?>
                        <p><?php the_author_meta('description'); ?></p>
                    <?php endif; ?>
                    <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" class="author-link">
                        <?php echo get_theme_mod('single_post_author_posts_link', __('جميع مقالات المؤلف', 'fiqhlearning')); ?>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- قسم التعليقات -->
    <?php if (comments_open() || get_comments_number()) : ?>
        <div class="post-comments-section">
            <div class="container-narrow">
                <h2 class="comments-title">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                    <?php
                    $comments_count = get_comments_number();
                    if ($comments_count == 0) {
                        echo get_theme_mod('single_post_comments_section_title_zero', __('التعليقات', 'fiqhlearning'));
                    } else {
                        printf(
                            get_theme_mod('single_post_comments_section_title', __('التعليقات (%d)', 'fiqhlearning')),
                            $comments_count
                        );
                    }
                    ?>
                </h2>
                <?php comments_template(); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- المقالات ذات الصلة -->
    <?php
    $related_posts = get_posts(array(
        'category__in' => wp_get_post_categories(get_the_ID()),
        'numberposts' => 3,
        'post__not_in' => array(get_the_ID()),
    ));

    if (!empty($related_posts)) :
    ?>
        <div class="related-posts">
            <div class="container-narrow">
                <h2 class="section-title">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    <?php echo get_theme_mod('single_post_related_posts_title', __('مقالات ذات صلة', 'fiqhlearning')); ?>
                </h2>
                <div class="related-posts-grid">
                    <?php foreach ($related_posts as $related_post) :
                        $related_categories = get_the_category($related_post->ID);
                    ?>
                        <article class="related-post-card card">
                            <?php if (has_post_thumbnail($related_post->ID)) : ?>
                                <a href="<?php echo get_permalink($related_post->ID); ?>" class="related-post-image">
                                    <?php echo get_the_post_thumbnail($related_post->ID, 'medium'); ?>
                                </a>
                            <?php endif; ?>
                            <div class="related-post-content">
                                <?php if (!empty($related_categories)) : ?>
                                    <a href="<?php echo esc_url(get_category_link($related_categories[0]->term_id)); ?>" class="related-post-category">
                                        <?php echo esc_html($related_categories[0]->name); ?>
                                    </a>
                                <?php endif; ?>
                                <h3 class="related-post-title">
                                    <a href="<?php echo get_permalink($related_post->ID); ?>">
                                        <?php echo get_the_title($related_post->ID); ?>
                                    </a>
                                </h3>
                                <div class="related-post-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt($related_post->ID), 20); ?>
                                </div>
                                <div class="related-post-meta">
                                    <span class="related-post-date">
                                        <?php echo get_the_date('', $related_post->ID); ?>
                                    </span>
                                </div>
                                <a href="<?php echo get_permalink($related_post->ID); ?>" class="related-post-link">
                                    <?php echo get_theme_mod('single_post_read_more', __('اقرأ المزيد', 'fiqhlearning')); ?>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="15 18 9 12 15 6"></polyline>
                                    </svg>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

</article>

<?php
endwhile;
get_footer();
