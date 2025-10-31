<?php
/**
 * قالب صفحة الدرس
 *
 * @package FiqhLearning
 */

get_header();

// جلب معلومات المقرر
$course_id = get_post_meta(get_the_ID(), '_fiqh_lesson_course_id', true);
$course = get_post($course_id);

// التحقق من صلاحية الوصول
$can_access = fiqh_user_can_access(get_the_ID(), get_current_user_id());

if (!$can_access) {
    ?>
    <main id="primary" class="site-main">
        <div class="container">
            <?php fiqh_unauthorized_message(); ?>
        </div>
    </main>
    <?php
    get_footer();
    return;
}

// جلب معلومات الدرس
$video_url = get_post_meta(get_the_ID(), '_fiqh_lesson_video_url', true);
$audio_url = get_post_meta(get_the_ID(), '_fiqh_lesson_audio_url', true);
$pdf_url = get_post_meta(get_the_ID(), '_fiqh_lesson_pdf_url', true);
$lesson_duration = get_post_meta(get_the_ID(), '_fiqh_lesson_duration', true);

// جلب تقدم الطالب
$progress = FiqhLearning_Progress::get_lesson_progress(get_current_user_id(), get_the_ID());
$is_completed = $progress && $progress->status === 'completed';

// تسجيل بدء الدرس إذا لم يكن مسجلاً
if (!$progress && !current_user_can('administrator') && !current_user_can('teacher')) {
    FiqhLearning_Progress::start_lesson(get_current_user_id(), get_the_ID(), $course_id);
}

?>

<main id="primary" class="site-main single-lesson-page" data-lesson-id="<?php echo get_the_ID(); ?>">
    <div class="lesson-container">

        <!-- المحتوى الرئيسي -->
        <div class="lesson-main-content">

            <?php fiqh_breadcrumb(); ?>

            <!-- عنوان الدرس -->
            <header class="lesson-header">
                <h1 class="lesson-title"><?php the_title(); ?></h1>

                <div class="lesson-meta">
                    <?php if ($course) : ?>
                        <a href="<?php echo get_permalink($course_id); ?>" class="lesson-course-link">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                            </svg>
                            <?php echo esc_html($course->post_title); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($lesson_duration) : ?>
                        <span class="lesson-duration">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <?php echo esc_html($lesson_duration); ?>
                        </span>
                    <?php endif; ?>

                    <?php if ($is_completed) : ?>
                        <span class="lesson-status-completed">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <?php _e('مكتمل', 'fiqhlearning'); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </header>

            <!-- مشغل الفيديو -->
            <?php if ($video_url) : ?>
                <div class="video-container">
                    <?php
                    // تحويل رابط YouTube إلى embed
                    $embed_url = $video_url;
                    if (strpos($video_url, 'youtube.com/watch?v=') !== false) {
                        $video_id = substr($video_url, strpos($video_url, 'v=') + 2);
                        if (strpos($video_id, '&') !== false) {
                            $video_id = substr($video_id, 0, strpos($video_id, '&'));
                        }
                        $embed_url = 'https://www.youtube.com/embed/' . $video_id;
                    } elseif (strpos($video_url, 'youtu.be/') !== false) {
                        $video_id = substr($video_url, strpos($video_url, 'youtu.be/') + 9);
                        $embed_url = 'https://www.youtube.com/embed/' . $video_id;
                    }
                    ?>
                    <iframe
                        src="<?php echo esc_url($embed_url); ?>"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                    </iframe>
                </div>
            <?php endif; ?>

            <!-- التبويبات -->
            <div class="lesson-tabs">
                <button class="lesson-tab active" data-tab="notes"><?php _e('الملاحظات', 'fiqhlearning'); ?></button>
                <?php if ($pdf_url) : ?>
                    <button class="lesson-tab" data-tab="attachments"><?php _e('المرفقات', 'fiqhlearning'); ?></button>
                <?php endif; ?>
                <button class="lesson-tab" data-tab="my-notes"><?php _e('ملاحظتي', 'fiqhlearning'); ?></button>
            </div>

            <!-- محتوى التبويبات -->
            <div class="lesson-tab-content">

                <!-- تبويب الملاحظات -->
                <div class="tab-pane active" id="notes-pane">
                    <div class="lesson-content">
                        <?php the_content(); ?>
                    </div>
                </div>

                <!-- تبويب المرفقات -->
                <?php if ($pdf_url) : ?>
                    <div class="tab-pane" id="attachments-pane">
                        <div class="attachments-section">
                            <button class="btn btn-primary view-pdf-btn" data-pdf-url="<?php echo esc_url($pdf_url); ?>" data-pdf-title="<?php the_title(); ?>">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                </svg>
                                <?php _e('عرض PDF', 'fiqhlearning'); ?>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- تبويب ملاحظتي -->
                <div class="tab-pane" id="my-notes-pane">
                    <div class="my-notes-section">
                        <?php
                        $user_notes = get_user_meta(get_current_user_id(), '_fiqh_lesson_notes_' . get_the_ID(), true);
                        ?>
                        <form id="save-notes-form" class="notes-form">
                            <textarea
                                name="lesson_notes"
                                id="lesson-notes"
                                rows="10"
                                placeholder="<?php _e('اكتب ملاحظاتك الخاصة هنا...', 'fiqhlearning'); ?>"
                            ><?php echo esc_textarea($user_notes); ?></textarea>
                            <button type="submit" class="btn btn-primary">
                                <?php _e('حفظ الملاحظات', 'fiqhlearning'); ?>
                            </button>
                        </form>
                    </div>
                </div>

            </div>

            <!-- زر إكمال الدرس -->
            <?php if (!$is_completed && !current_user_can('administrator') && !current_user_can('teacher')) : ?>
                <div class="lesson-actions">
                    <button class="btn btn-primary btn-lg complete-lesson-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        <?php _e('تمييز كمكتمل', 'fiqhlearning'); ?>
                    </button>
                </div>
            <?php endif; ?>

        </div>

        <!-- الشريط الجانبي -->
        <aside class="lesson-sidebar">

            <!-- مشغل الصوت -->
            <?php if ($audio_url) : ?>
                <div class="sidebar-widget card">
                    <h3><?php _e('الدرس الصوتي', 'fiqhlearning'); ?></h3>
                    <div class="audio-player-widget">
                        <audio controls style="width: 100%;">
                            <source src="<?php echo esc_url($audio_url); ?>" type="audio/mpeg">
                            <?php _e('متصفحك لا يدعم تشغيل الملفات الصوتية', 'fiqhlearning'); ?>
                        </audio>
                    </div>
                </div>
            <?php endif; ?>

            <!-- قائمة الدروس -->
            <div class="sidebar-widget card lessons-list-widget">
                <h3><?php _e('دروس المقرر', 'fiqhlearning'); ?></h3>

                <?php
                // جلب جميع دروس المقرر
                $all_lessons = get_posts(array(
                    'post_type' => 'fiqh_lesson',
                    'posts_per_page' => -1,
                    'meta_query' => array(
                        array(
                            'key' => '_fiqh_lesson_course_id',
                            'value' => $course_id,
                        )
                    ),
                    'meta_key' => '_fiqh_lesson_order',
                    'orderby' => 'meta_value_num',
                    'order' => 'ASC',
                ));

                if ($all_lessons) :
                    ?>
                    <div class="lessons-list-sidebar">
                        <?php
                        foreach ($all_lessons as $sidebar_lesson) :
                            $lesson_progress_sidebar = FiqhLearning_Progress::get_lesson_progress(get_current_user_id(), $sidebar_lesson->ID);
                            $is_completed_sidebar = $lesson_progress_sidebar && $lesson_progress_sidebar->status === 'completed';
                            $is_current = $sidebar_lesson->ID === get_the_ID();
                            ?>
                            <div class="lesson-item-sidebar <?php echo $is_completed_sidebar ? 'completed' : ''; ?> <?php echo $is_current ? 'active' : ''; ?>">
                                <a href="<?php echo get_permalink($sidebar_lesson->ID); ?>">
                                    <div class="lesson-icon-sidebar">
                                        <?php if ($is_completed_sidebar) : ?>
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                            </svg>
                                        <?php elseif ($is_current) : ?>
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"></circle>
                                            </svg>
                                        <?php else : ?>
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polygon points="5 3 19 12 5 21 5 3"></polygon>
                                            </svg>
                                        <?php endif; ?>
                                    </div>
                                    <span class="lesson-title-sidebar"><?php echo esc_html($sidebar_lesson->post_title); ?></span>
                                </a>
                            </div>
                            <?php
                        endforeach;
                        ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- نسبة الإنجاز -->
            <?php if (!current_user_can('administrator') && !current_user_can('teacher')) : ?>
                <div class="sidebar-widget card">
                    <h3><?php _e('تقدمك', 'fiqhlearning'); ?></h3>
                    <?php
                    $course_progress = fiqh_get_course_progress($course_id, get_current_user_id());
                    fiqh_display_progress_bar($course_progress);
                    ?>
                </div>
            <?php endif; ?>

        </aside>

    </div>
</main>

<script>
jQuery(document).ready(function($) {
    // التبديل بين التبويبات
    $('.lesson-tab').on('click', function() {
        $('.lesson-tab').removeClass('active');
        $('.tab-pane').removeClass('active');

        $(this).addClass('active');
        var tabId = $(this).data('tab') + '-pane';
        $('#' + tabId).addClass('active');
    });

    // حفظ الملاحظات
    $('#save-notes-form').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: fiqhData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'fiqh_save_lesson_notes',
                nonce: fiqhData.nonce,
                lesson_id: <?php echo get_the_ID(); ?>,
                notes: $('#lesson-notes').val()
            },
            success: function(response) {
                if (response.success) {
                    alert('<?php _e('تم حفظ الملاحظات بنجاح', 'fiqhlearning'); ?>');
                }
            }
        });
    });
});
</script>

<?php
get_footer();
