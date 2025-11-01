<?php
/**
 * Template Name: صفحة الأسئلة
 *
 * @package FiqhLearning
 */

get_header();

if (!is_user_logged_in()) {
    ?>
    <main id="primary" class="site-main">
        <div class="container">
            <div class="unauthorized-message">
                <h2><?php _e('يجب تسجيل الدخول', 'fiqhlearning'); ?></h2>
                <p><?php _e('يرجى تسجيل الدخول لعرض الأسئلة والإجابات', 'fiqhlearning'); ?></p>
                <a href="<?php echo wp_login_url(get_permalink()); ?>" class="btn btn-primary">
                    <?php _e('تسجيل الدخول', 'fiqhlearning'); ?>
                </a>
            </div>
        </div>
    </main>
    <?php
    get_footer();
    return;
}

?>

<main id="primary" class="site-main questions-page">
    <div class="questions-container">

        <!-- الفلاتر الجانبية -->
        <aside class="questions-filters">

            <!-- بحث -->
            <div class="filter-group card">
                <h3><?php _e('بحث', 'fiqhlearning'); ?></h3>
                <form id="questions-search-form" class="search-form-questions">
                    <input type="text" id="questions-search" placeholder="<?php _e('ابحث عن سؤال...', 'fiqhlearning'); ?>" class="filter-input">
                    <button type="submit" class="btn btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                    </button>
                </form>
            </div>

            <!-- فلتر المادة -->
            <div class="filter-group card">
                <h3><?php _e('العلم', 'fiqhlearning'); ?></h3>
                <select id="filter-category" class="filter-select">
                    <option value=""><?php _e('جميع العلوم', 'fiqhlearning'); ?></option>
                    <?php
                    $categories = get_terms(array('taxonomy' => 'fiqh_course_science', 'hide_empty' => true));
                    if ($categories && !is_wp_error($categories)) :
                        foreach ($categories as $category) :
                            ?>
                            <option value="<?php echo $category->term_id; ?>"><?php echo esc_html($category->name); ?></option>
                        <?php
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>

            <!-- فلتر المقرر -->
            <div class="filter-group card">
                <h3><?php _e('المقرر', 'fiqhlearning'); ?></h3>
                <select id="filter-course" class="filter-select">
                    <option value=""><?php _e('جميع المقررات', 'fiqhlearning'); ?></option>
                    <?php
                    $courses = get_posts(array('post_type' => 'fiqh_course', 'posts_per_page' => -1));
                    foreach ($courses as $course) :
                        ?>
                        <option value="<?php echo $course->ID; ?>"><?php echo esc_html($course->post_title); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- فلتر الحالة -->
            <div class="filter-group card">
                <h3><?php _e('الحالة', 'fiqhlearning'); ?></h3>
                <select id="filter-status" class="filter-select">
                    <option value=""><?php _e('الكل', 'fiqhlearning'); ?></option>
                    <option value="answered"><?php _e('مجابة', 'fiqhlearning'); ?></option>
                    <option value="pending"><?php _e('بانتظار الإجابة', 'fiqhlearning'); ?></option>
                </select>
            </div>

        </aside>

        <!-- المحتوى الرئيسي -->
        <div class="questions-main-content">

            <!-- رأس الصفحة -->
            <div class="questions-header">
                <h1><?php the_title(); ?></h1>
                <button class="btn btn-primary" id="add-question-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <?php _e('أضف سؤال', 'fiqhlearning'); ?>
                </button>
            </div>

            <!-- العداد -->
            <div class="questions-count">
                <span id="questions-counter">0</span> <?php _e('سؤال', 'fiqhlearning'); ?>
            </div>

            <!-- قائمة الأسئلة -->
            <div id="questions-list" class="questions-list">
                <!-- سيتم ملؤها عبر AJAX -->
                <div class="loading-spinner">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="2" x2="12" y2="6"></line>
                        <line x1="12" y1="18" x2="12" y2="22"></line>
                        <line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line>
                        <line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line>
                        <line x1="2" y1="12" x2="6" y2="12"></line>
                        <line x1="18" y1="12" x2="22" y2="12"></line>
                        <line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line>
                        <line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line>
                    </svg>
                    <p><?php _e('جاري تحميل الأسئلة...', 'fiqhlearning'); ?></p>
                </div>
            </div>

        </div>

        <!-- الشريط الجانبي الأيمن -->
        <aside class="questions-sidebar">

            <!-- أسئلتي -->
            <div class="sidebar-widget card">
                <h3><?php _e('أسئلتي', 'fiqhlearning'); ?></h3>
                <button class="btn btn-outline btn-block" id="my-questions-btn">
                    <?php _e('عرض أسئلتي فقط', 'fiqhlearning'); ?>
                </button>
            </div>

            <!-- المفضلة -->
            <div class="sidebar-widget card">
                <h3><?php _e('المفضلة', 'fiqhlearning'); ?></h3>
                <button class="btn btn-outline btn-block" id="favorites-btn">
                    <?php _e('عرض المفضلة', 'fiqhlearning'); ?>
                </button>
            </div>

            <!-- أرشيف -->
            <div class="sidebar-widget card">
                <h3><?php _e('أرشيف', 'fiqhlearning'); ?></h3>
                <ul class="archive-list">
                    <li><a href="#"><?php _e('هذا الأسبوع', 'fiqhlearning'); ?></a></li>
                    <li><a href="#"><?php _e('هذا الشهر', 'fiqhlearning'); ?></a></li>
                    <li><a href="#"><?php _e('السنة الحالية', 'fiqhlearning'); ?></a></li>
                </ul>
            </div>

        </aside>

    </div>
</main>

<!-- Modal إضافة سؤال -->
<div id="add-question-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <button class="modal-close" id="close-question-modal">&times;</button>
        <h2><?php _e('إضافة سؤال جديد', 'fiqhlearning'); ?></h2>

        <form id="add-question-form" class="question-form">

            <div class="form-group">
                <label for="question-course"><?php _e('المقرر', 'fiqhlearning'); ?></label>
                <select id="question-course" name="course_id" required class="form-control">
                    <option value=""><?php _e('-- اختر المقرر --', 'fiqhlearning'); ?></option>
                    <?php
                    foreach ($courses as $course) :
                        ?>
                        <option value="<?php echo $course->ID; ?>"><?php echo esc_html($course->post_title); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="question-lesson"><?php _e('الدرس (اختياري)', 'fiqhlearning'); ?></label>
                <select id="question-lesson" name="lesson_id" class="form-control">
                    <option value=""><?php _e('-- اختر الدرس --', 'fiqhlearning'); ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="question-text"><?php _e('نص السؤال', 'fiqhlearning'); ?></label>
                <textarea id="question-text" name="question_text" required rows="6" class="form-control" placeholder="<?php _e('اكتب سؤالك هنا...', 'fiqhlearning'); ?>"></textarea>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_anonymous" id="question-anonymous" value="1">
                    <?php _e('السؤال كمجهول', 'fiqhlearning'); ?>
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <?php _e('إرسال السؤال', 'fiqhlearning'); ?>
                </button>
                <button type="button" class="btn btn-secondary" id="cancel-question-btn">
                    <?php _e('إلغاء', 'fiqhlearning'); ?>
                </button>
            </div>

        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    let currentFilters = {};

    // تحميل الأسئلة
    function loadQuestions() {
        $.ajax({
            url: fiqhData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'fiqh_get_questions',
                ...currentFilters
            },
            success: function(response) {
                if (response.success && response.data.questions) {
                    renderQuestions(response.data.questions);
                    $('#questions-counter').text(response.data.questions.length);
                } else {
                    $('#questions-list').html('<div class="no-questions"><p><?php _e('لا توجد أسئلة', 'fiqhlearning'); ?></p></div>');
                    $('#questions-counter').text('0');
                }
            }
        });
    }

    // عرض الأسئلة
    function renderQuestions(questions) {
        let html = '';
        questions.forEach(function(question) {
            html += '<div class="question-card card">';
            html += '<div class="question-header">';
            html += '<div class="question-author">';
            html += '<h3>' + question.question_text.substring(0, 100) + '...</h3>';
            html += '</div>';
            html += '<span class="question-status status-' + question.status + '">' + question.status + '</span>';
            html += '</div>';
            html += '<div class="question-meta">';
            html += '<span class="question-date">' + question.created_at + '</span>';
            html += '<span class="question-views">' + question.views_count + ' <?php _e('مشاهدة', 'fiqhlearning'); ?></span>';
            html += '</div>';
            html += '</div>';
        });
        $('#questions-list').html(html);
    }

    // فتح Modal
    $('#add-question-btn').on('click', function() {
        $('#add-question-modal').fadeIn();
    });

    // إغلاق Modal
    $('#close-question-modal, #cancel-question-btn').on('click', function() {
        $('#add-question-modal').fadeOut();
    });

    // تحميل الدروس عند اختيار مقرر
    $('#question-course').on('change', function() {
        let courseId = $(this).val();
        if (courseId) {
            $.ajax({
                url: fiqhData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'fiqh_get_course_lessons',
                    course_id: courseId
                },
                success: function(response) {
                    if (response.success && response.data.lessons) {
                        let options = '<option value=""><?php _e('-- اختر الدرس --', 'fiqhlearning'); ?></option>';
                        response.data.lessons.forEach(function(lesson) {
                            options += '<option value="' + lesson.ID + '">' + lesson.post_title + '</option>';
                        });
                        $('#question-lesson').html(options);
                    }
                }
            });
        }
    });

    // الفلاتر
    $('#filter-category, #filter-course, #filter-status').on('change', function() {
        currentFilters = {
            course_id: $('#filter-course').val(),
            status: $('#filter-status').val()
        };
        loadQuestions();
    });

    // البحث
    $('#questions-search-form').on('submit', function(e) {
        e.preventDefault();
        currentFilters.search = $('#questions-search').val();
        loadQuestions();
    });

    // تحميل الأسئلة عند فتح الصفحة
    loadQuestions();
});
</script>

<?php
get_footer();
