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
                <?php
                $exercises = get_post_meta(get_the_ID(), '_fiqh_lesson_exercises', true);
                if ($exercises) :
                ?>
                    <button class="lesson-tab" data-tab="exercises"><?php _e('التمارين', 'fiqhlearning'); ?></button>
                <?php endif; ?>
                <button class="lesson-tab" data-tab="questions"><?php _e('الأسئلة', 'fiqhlearning'); ?></button>
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

                <!-- تبويب التمارين -->
                <?php if ($exercises) : ?>
                    <div class="tab-pane" id="exercises-pane">
                        <div class="exercises-section">
                            <div class="lesson-content">
                                <?php echo wp_kses_post($exercises); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- تبويب الأسئلة -->
                <div class="tab-pane" id="questions-pane">
                    <div class="questions-section">

                        <!-- إضافة سؤال جديد -->
                        <?php if (is_user_logged_in()) : ?>
                            <div class="add-question-form card">
                                <h4><?php _e('اطرح سؤالك', 'fiqhlearning'); ?></h4>
                                <form id="add-lesson-question-form">
                                    <textarea
                                        name="question_content"
                                        id="question-content"
                                        rows="4"
                                        placeholder="<?php _e('اكتب سؤالك هنا...', 'fiqhlearning'); ?>"
                                        required
                                    ></textarea>
                                    <button type="submit" class="btn btn-primary">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="22" y1="2" x2="11" y2="13"></line>
                                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                        </svg>
                                        <?php _e('إرسال السؤال', 'fiqhlearning'); ?>
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>

                        <!-- قائمة الأسئلة والأجوبة -->
                        <div class="questions-list">
                            <?php
                            global $wpdb;
                            $table_name = $wpdb->prefix . 'fiqh_questions';

                            $questions = $wpdb->get_results($wpdb->prepare(
                                "SELECT * FROM $table_name WHERE lesson_id = %d ORDER BY created_at DESC",
                                get_the_ID()
                            ));

                            if ($questions) :
                                foreach ($questions as $question) :
                                    $question_user = get_userdata($question->user_id);
                                    $can_answer = current_user_can('administrator') || current_user_can('teacher');
                                    ?>
                                    <div class="question-item card" data-question-id="<?php echo $question->id; ?>">
                                        <div class="question-header">
                                            <div class="question-author">
                                                <strong><?php echo esc_html($question_user->display_name); ?></strong>
                                                <span class="question-date"><?php echo human_time_diff(strtotime($question->created_at), current_time('timestamp')) . ' ' . __('مضت', 'fiqhlearning'); ?></span>
                                            </div>
                                        </div>
                                        <div class="question-content">
                                            <?php echo wp_kses_post(nl2br($question->question)); ?>
                                        </div>

                                        <!-- الإجابة -->
                                        <?php if ($question->answer) : ?>
                                            <div class="question-answer">
                                                <div class="answer-header">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="9 11 12 14 22 4"></polyline>
                                                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                                    </svg>
                                                    <strong><?php _e('إجابة المشرف', 'fiqhlearning'); ?></strong>
                                                </div>
                                                <div class="answer-content">
                                                    <?php echo wp_kses_post(nl2br($question->answer)); ?>
                                                </div>
                                            </div>
                                        <?php elseif ($can_answer) : ?>
                                            <div class="answer-form">
                                                <form class="add-answer-form" data-question-id="<?php echo $question->id; ?>">
                                                    <textarea
                                                        name="answer_content"
                                                        rows="3"
                                                        placeholder="<?php _e('اكتب الإجابة هنا...', 'fiqhlearning'); ?>"
                                                        required
                                                    ></textarea>
                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        <?php _e('إرسال الإجابة', 'fiqhlearning'); ?>
                                                    </button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php
                                endforeach;
                            else :
                                ?>
                                <div class="no-questions">
                                    <p><?php _e('لا توجد أسئلة بعد. كن أول من يطرح سؤالاً!', 'fiqhlearning'); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

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

        </div>

        <!-- الشريط الجانبي -->
        <aside class="lesson-sidebar">

            <!-- زر إكمال الدرس -->
            <?php if (!$is_completed && !current_user_can('administrator') && !current_user_can('teacher')) : ?>
                <div class="sidebar-widget card">
                    <button class="btn btn-primary btn-lg complete-lesson-btn" style="width: 100%;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        <?php _e('تمييز كمكتمل', 'fiqhlearning'); ?>
                    </button>
                </div>
            <?php endif; ?>

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

    // إضافة سؤال جديد
    $('#add-lesson-question-form').on('submit', function(e) {
        e.preventDefault();
        var $btn = $(this).find('button[type="submit"]');
        var originalText = $btn.html();
        $btn.prop('disabled', true).text('<?php _e('جاري الإرسال...', 'fiqhlearning'); ?>');

        $.ajax({
            url: fiqhData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'fiqh_add_lesson_question',
                nonce: fiqhData.nonce,
                lesson_id: <?php echo get_the_ID(); ?>,
                question: $('#question-content').val()
            },
            success: function(response) {
                if (response.success) {
                    alert('<?php _e('تم إرسال السؤال بنجاح', 'fiqhlearning'); ?>');
                    location.reload();
                } else {
                    alert(response.data || '<?php _e('حدث خطأ أثناء إرسال السؤال', 'fiqhlearning'); ?>');
                }
                $btn.prop('disabled', false).html(originalText);
            },
            error: function() {
                alert('<?php _e('حدث خطأ أثناء إرسال السؤال', 'fiqhlearning'); ?>');
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });

    // إضافة إجابة على سؤال
    $(document).on('submit', '.add-answer-form', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $form.find('button[type="submit"]');
        var questionId = $form.data('question-id');
        var originalText = $btn.text();
        $btn.prop('disabled', true).text('<?php _e('جاري الإرسال...', 'fiqhlearning'); ?>');

        $.ajax({
            url: fiqhData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'fiqh_add_lesson_answer',
                nonce: fiqhData.nonce,
                question_id: questionId,
                answer: $form.find('textarea[name="answer_content"]').val()
            },
            success: function(response) {
                if (response.success) {
                    alert('<?php _e('تم إرسال الإجابة بنجاح', 'fiqhlearning'); ?>');
                    location.reload();
                } else {
                    alert(response.data || '<?php _e('حدث خطأ أثناء إرسال الإجابة', 'fiqhlearning'); ?>');
                }
                $btn.prop('disabled', false).text(originalText);
            },
            error: function() {
                alert('<?php _e('حدث خطأ أثناء إرسال الإجابة', 'fiqhlearning'); ?>');
                $btn.prop('disabled', false).text(originalText);
            }
        });
    });

    // زر إتمام الدرس
    $('.complete-lesson-btn').on('click', function() {
        var $btn = $(this);
        if (confirm('<?php _e('هل أنت متأكد من إتمام هذا الدرس؟', 'fiqhlearning'); ?>')) {
            $btn.prop('disabled', true).text('<?php _e('جاري الحفظ...', 'fiqhlearning'); ?>');

            $.ajax({
                url: fiqhData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'fiqh_complete_lesson',
                    nonce: fiqhData.nonce,
                    lesson_id: <?php echo get_the_ID(); ?>,
                    course_id: <?php echo $course_id; ?>
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('<?php _e('حدث خطأ، حاول مرة أخرى', 'fiqhlearning'); ?>');
                        $btn.prop('disabled', false).html('<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg> <?php _e('تمييز كمكتمل', 'fiqhlearning'); ?>');
                    }
                }
            });
        }
    });
});
</script>

<?php
get_footer();
