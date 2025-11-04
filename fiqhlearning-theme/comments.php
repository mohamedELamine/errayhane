<?php
/**
 * قالب التعليقات المخصص
 *
 * @package FiqhLearning
 */

if (post_password_required()) {
    return;
}
?>

<section id="comments" class="comments-section">
    <div class="comments-container">

        <?php if (have_comments()) : ?>
            <h2 class="comments-title">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                <?php
                $comments_number = get_comments_number();
                if ($comments_number == 1) {
                    printf(_x('تعليق واحد', 'comments title', 'fiqhlearning'));
                } else {
                    printf(
                        _nx(
                            '%1$s تعليق',
                            '%1$s تعليقات',
                            $comments_number,
                            'comments title',
                            'fiqhlearning'
                        ),
                        number_format_i18n($comments_number)
                    );
                }
                ?>
            </h2>

            <ol class="comment-list">
                <?php
                wp_list_comments(array(
                    'style'       => 'ol',
                    'short_ping'  => true,
                    'avatar_size' => 60,
                    'callback'    => 'fiqh_custom_comment',
                ));
                ?>
            </ol>

            <?php
            the_comments_navigation(array(
                'prev_text' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg> ' . __('التعليقات السابقة', 'fiqhlearning'),
                'next_text' => __('التعليقات التالية', 'fiqhlearning') . ' <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>',
            ));
            ?>

        <?php endif; ?>

        <?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) : ?>
            <p class="no-comments"><?php _e('التعليقات مغلقة.', 'fiqhlearning'); ?></p>
        <?php endif; ?>

        <?php
        $commenter = wp_get_current_commenter();
        $req = get_option('require_name_email');
        $aria_req = ($req ? " aria-required='true'" : '');

        comment_form(array(
            'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title">',
            'title_reply_after'  => '</h3>',
            'title_reply'        => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg> ' . __('اترك تعليقاً', 'fiqhlearning'),
            'logged_in_as'       => '<p class="logged-in-as">' . sprintf(__('مسجل الدخول كـ <a href="%1$s">%2$s</a>. <a href="%3$s" title="تسجيل الخروج من هذا الحساب">تسجيل الخروج؟</a>', 'fiqhlearning'), admin_url('profile.php'), $user_identity, wp_logout_url(apply_filters('the_permalink', get_permalink()))) . '</p>',
            'comment_notes_before' => '',
            'comment_notes_after'  => '',
            'fields' => array(
                'author' => '<div class="comment-form-row"><div class="comment-form-field"><label for="author">' . __('الاسم', 'fiqhlearning') . ($req ? ' <span class="required">*</span>' : '') . '</label><input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30"' . $aria_req . ' placeholder="' . esc_attr__('اسمك الكامل', 'fiqhlearning') . '" /></div>',
                'email'  => '<div class="comment-form-field"><label for="email">' . __('البريد الإلكتروني', 'fiqhlearning') . ($req ? ' <span class="required">*</span>' : '') . '</label><input id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email']) . '" size="30"' . $aria_req . ' placeholder="' . esc_attr__('بريدك الإلكتروني', 'fiqhlearning') . '" /></div></div>',
                'url'    => '<div class="comment-form-field"><label for="url">' . __('الموقع الإلكتروني', 'fiqhlearning') . '</label><input id="url" name="url" type="url" value="' . esc_attr($commenter['comment_author_url']) . '" size="30" placeholder="' . esc_attr__('موقعك الإلكتروني (اختياري)', 'fiqhlearning') . '" /></div>',
            ),
            'comment_field' => '<div class="comment-form-field"><label for="comment">' . __('التعليق', 'fiqhlearning') . ' <span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" placeholder="' . esc_attr__('اكتب تعليقك هنا...', 'fiqhlearning') . '"></textarea></div>',
            'class_submit'  => 'btn btn-primary',
            'submit_button' => '<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
            'submit_field'  => '<div class="comment-form-submit">%1$s %2$s</div>',
        ));
        ?>

    </div>
</section>
