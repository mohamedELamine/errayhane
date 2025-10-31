<?php
/**
 * الملف الرئيسي للقالب
 *
 * @package FiqhLearning
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <?php
        if (have_posts()) :

            if (is_home() && !is_front_page()) :
                ?>
                <header class="page-header">
                    <h1 class="page-title"><?php single_post_title(); ?></h1>
                </header>
                <?php
            endif;

            // بداية الحلقة
            while (have_posts()) :
                the_post();

                get_template_part('template-parts/content', get_post_type());

            endwhile;

            // الترقيم pagination
            the_posts_pagination(array(
                'mid_size' => 2,
                'prev_text' => __('&rarr; السابق', 'fiqhlearning'),
                'next_text' => __('التالي &larr;', 'fiqhlearning'),
            ));

        else :

            get_template_part('template-parts/content', 'none');

        endif;
        ?>
    </div>
</main><!-- #main -->

<?php
get_sidebar();
get_footer();
