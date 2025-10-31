<?php
/**
 * قالب عرض "لا يوجد محتوى"
 *
 * @package FiqhLearning
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<section class="no-results not-found">
    <header class="page-header">
        <h1 class="page-title"><?php _e('لم يتم العثور على محتوى', 'fiqhlearning'); ?></h1>
    </header>

    <div class="page-content">
        <?php if (is_search()) : ?>
            <p><?php _e('عذراً، لم نجد نتائج مطابقة لبحثك. يرجى المحاولة مرة أخرى بكلمات مختلفة.', 'fiqhlearning'); ?></p>
            <?php get_search_form(); ?>
        <?php else : ?>
            <p><?php _e('لا يوجد محتوى لعرضه في هذه الصفحة.', 'fiqhlearning'); ?></p>
        <?php endif; ?>
    </div>
</section>
