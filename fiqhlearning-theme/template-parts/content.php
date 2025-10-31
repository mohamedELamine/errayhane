<?php
/**
 * قالب عرض المحتوى الافتراضي
 *
 * @package FiqhLearning
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('card'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <div class="post-thumbnail">
            <?php the_post_thumbnail('large'); ?>
        </div>
    <?php endif; ?>

    <div class="post-content">
        <header class="entry-header">
            <?php
            if (is_singular()) :
                the_title('<h1 class="entry-title">', '</h1>');
            else :
                the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
            endif;
            ?>
        </header>

        <div class="entry-content">
            <?php
            if (is_singular()) :
                the_content();
            else :
                the_excerpt();
            endif;
            ?>
        </div>

        <?php if (!is_singular()) : ?>
        <footer class="entry-footer">
            <a href="<?php the_permalink(); ?>" class="btn btn-primary">
                <?php _e('قراءة المزيد', 'fiqhlearning'); ?>
            </a>
        </footer>
        <?php endif; ?>
    </div>
</article>
