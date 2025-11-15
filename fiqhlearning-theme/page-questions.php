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
            <div class="unauthorized-message card" style="text-align: center; padding: var(--spacing-3xl);">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto var(--spacing-lg); color: var(--color-gray);">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <h2><?php _e('يجب تسجيل الدخول', 'fiqhlearning'); ?></h2>
                <p><?php _e('يرجى تسجيل الدخول لعرض الأسئلة والإجابات', 'fiqhlearning'); ?></p>
                <a href="https://arraihane.com/login/" class="btn btn-primary btn-lg">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                        <polyline points="10 17 15 12 10 7"></polyline>
                        <line x1="15" y1="12" x2="3" y2="12"></line>
                    </svg>
                    <?php _e('تسجيل الدخول', 'fiqhlearning'); ?>
                </a>
            </div>
        </div>
    </main>
    <?php
    get_footer();
    return;
}

$current_user_id = get_current_user_id();
?>

<main id="primary" class="site-main">
    <div class="container">

        <!-- رأس الصفحة -->
        <div class="page-header">
            <h1 class="page-title">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <?php _e('الأسئلة والإجابات', 'fiqhlearning'); ?>
            </h1>
            <button class="btn btn-primary btn-lg" id="add-question-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <?php _e('طرح سؤال جديد', 'fiqhlearning'); ?>
            </button>
        </div>

        <div class="questions-container">

            <!-- الفلاتر الجانبية -->
            <aside class="questions-filters">

                <!-- بحث -->
                <div class="filter-group card">
                    <h3>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                        <?php _e('بحث', 'fiqhlearning'); ?>
                    </h3>
                    <form id="questions-search-form">
                        <div class="search-input-wrapper">
                            <input type="text" id="questions-search" placeholder="<?php _e('ابحث في الأسئلة...', 'fiqhlearning'); ?>" class="filter-input">
                            <button type="submit" class="search-btn">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <path d="m21 21-4.35-4.35"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- فلتر العلم -->
                <div class="filter-group card">
                    <h3><?php _e('العلم', 'fiqhlearning'); ?></h3>
                    <select id="filter-science" class="filter-select">
                        <option value=""><?php _e('جميع العلوم', 'fiqhlearning'); ?></option>
                        <?php
                        $sciences = get_terms(array('taxonomy' => 'fiqh_course_science', 'hide_empty' => true));
                        if ($sciences && !is_wp_error($sciences)) :
                            foreach ($sciences as $science) :
                                ?>
                                <option value="<?php echo $science->term_id; ?>"><?php echo esc_html($science->name); ?></option>
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
                        <option value="answered"><?php _e('مُجابة', 'fiqhlearning'); ?></option>
                        <option value="pending"><?php _e('بانتظار الإجابة', 'fiqhlearning'); ?></option>
                    </select>
                </div>

                <!-- أسئلتي -->
                <div class="filter-group card">
                    <button class="btn btn-outline btn-block" id="my-questions-toggle">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <?php _e('أسئلتي فقط', 'fiqhlearning'); ?>
                    </button>
                </div>

            </aside>

            <!-- المحتوى الرئيسي -->
            <div class="questions-main-content">

                <!-- إحصائيات -->
                <div class="questions-stats card">
                    <div class="stat-item">
                        <div class="stat-number" id="questions-counter">0</div>
                        <div class="stat-label"><?php _e('سؤال', 'fiqhlearning'); ?></div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" id="answered-counter">0</div>
                        <div class="stat-label"><?php _e('مُجابة', 'fiqhlearning'); ?></div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" id="pending-counter">0</div>
                        <div class="stat-label"><?php _e('بانتظار', 'fiqhlearning'); ?></div>
                    </div>
                </div>

                <!-- قائمة الأسئلة -->
                <div id="questions-list" class="questions-list">
                    <!-- سيتم ملؤها عبر AJAX -->
                    <div class="loading-spinner">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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

                <!-- Pagination -->
                <div id="questions-pagination" class="pagination" style="display: none;">
                    <button class="btn btn-outline" id="prev-page-btn" disabled>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                        <?php _e('السابق', 'fiqhlearning'); ?>
                    </button>
                    <span id="page-info">1 / 1</span>
                    <button class="btn btn-outline" id="next-page-btn">
                        <?php _e('التالي', 'fiqhlearning'); ?>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </button>
                </div>

            </div>

        </div>

    </div>
</main>

<!-- Modal إضافة سؤال -->
<div id="add-question-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <button class="modal-close" id="close-question-modal">&times;</button>
        <h2><?php _e('طرح سؤال جديد', 'fiqhlearning'); ?></h2>
        <p class="modal-description"><?php _e('اطرح سؤالك وسيتم الإجابة عليه من قبل المعلمين المختصين', 'fiqhlearning'); ?></p>

        <form id="add-question-form" class="question-form">

            <div class="form-group">
                <label for="question-course">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                    <?php _e('المقرر *', 'fiqhlearning'); ?>
                </label>
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
                <label for="question-lesson">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <?php _e('الدرس (اختياري)', 'fiqhlearning'); ?>
                </label>
                <select id="question-lesson" name="lesson_id" class="form-control">
                    <option value=""><?php _e('-- اختر الدرس --', 'fiqhlearning'); ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="question-text">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                    <?php _e('نص السؤال *', 'fiqhlearning'); ?>
                </label>
                <textarea id="question-text" name="question_text" required rows="6" class="form-control" placeholder="<?php _e('اكتب سؤالك بشكل واضح ومفصل...', 'fiqhlearning'); ?>"></textarea>
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_anonymous" id="question-anonymous" value="1">
                    <span><?php _e('طرح السؤال كمجهول', 'fiqhlearning'); ?></span>
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
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
    let currentPage = 1;
    let totalPages = 1;
    let myQuestionsOnly = false;

    // تحميل الأسئلة
    function loadQuestions() {
        $('#questions-list').html('<div class="loading-spinner"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg><p><?php _e('جاري تحميل الأسئلة...', 'fiqhlearning'); ?></p></div>');

        $.ajax({
            url: fiqhData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'fiqh_get_questions',
                nonce: fiqhData.nonce,
                page: currentPage,
                my_questions: myQuestionsOnly ? 1 : 0,
                ...currentFilters
            },
            success: function(response) {
                if (response.success && response.data.questions && response.data.questions.length > 0) {
                    renderQuestions(response.data.questions);
                    updateStats(response.data.stats);
                    totalPages = response.data.total_pages || 1;
                    updatePagination();
                } else {
                    $('#questions-list').html('<div class="no-questions card"><svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg><h3><?php _e('لا توجد أسئلة', 'fiqhlearning'); ?></h3><p><?php _e('لم يتم العثور على أسئلة. كن أول من يطرح سؤالاً!', 'fiqhlearning'); ?></p></div>');
                    $('#questions-counter, #answered-counter, #pending-counter').text('0');
                    $('#questions-pagination').hide();
                }
            },
            error: function() {
                $('#questions-list').html('<div class="no-questions card"><p><?php _e('حدث خطأ أثناء تحميل الأسئلة', 'fiqhlearning'); ?></p></div>');
            }
        });
    }

    // عرض الأسئلة
    function renderQuestions(questions) {
        let html = '';
        questions.forEach(function(q) {
            const statusText = q.status === 'answered' ? '<?php _e('مُجابة', 'fiqhlearning'); ?>' : '<?php _e('بانتظار', 'fiqhlearning'); ?>';
            const statusClass = q.status === 'answered' ? 'status-answered' : 'status-pending';
            const userName = q.is_anonymous === '1' ? '<?php _e('مجهول', 'fiqhlearning'); ?>' : q.user_name;

            html += '<div class="question-card card" data-question-id="' + q.id + '">';
            html += '<div class="question-header">';
            html += '<div class="question-author-info">';
            html += '<div class="author-avatar">' + userName.charAt(0) + '</div>';
            html += '<div class="author-details">';
            html += '<span class="author-name">' + userName + '</span>';
            html += '<span class="question-date">' + q.created_at + '</span>';
            html += '</div>';
            html += '</div>';
            html += '<span class="question-status ' + statusClass + '">' + statusText + '</span>';
            html += '</div>';

            html += '<div class="question-content">';
            html += '<h3 class="question-title">' + q.question_text + '</h3>';
            html += '</div>';

            html += '<div class="question-footer">';
            html += '<div class="question-meta">';
            if (q.course_name) {
                html += '<span class="meta-item"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>' + q.course_name + '</span>';
            }
            if (q.lesson_name) {
                html += '<span class="meta-item"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>' + q.lesson_name + '</span>';
            }
            html += '</div>';
            html += '</div>';

            // عرض الإجابة إن وجدت
            if (q.status === 'answered' && q.answer_text) {
                html += '<div class="answer-section">';
                html += '<div class="answer-header">';
                html += '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>';
                html += '<span><?php _e('الإجابة', 'fiqhlearning'); ?></span>';
                if (q.answerer_name) {
                    html += '<span class="answerer-name"> • ' + q.answerer_name + '</span>';
                }
                html += '</div>';
                html += '<div class="answer-content">' + q.answer_text + '</div>';
                html += '</div>';
            }

            html += '</div>';
        });
        $('#questions-list').html(html);
    }

    // تحديث الإحصائيات
    function updateStats(stats) {
        $('#questions-counter').text(stats.total || 0);
        $('#answered-counter').text(stats.answered || 0);
        $('#pending-counter').text(stats.pending || 0);
    }

    // تحديث الصفحات
    function updatePagination() {
        if (totalPages > 1) {
            $('#questions-pagination').show();
            $('#page-info').text(currentPage + ' / ' + totalPages);
            $('#prev-page-btn').prop('disabled', currentPage === 1);
            $('#next-page-btn').prop('disabled', currentPage === totalPages);
        } else {
            $('#questions-pagination').hide();
        }
    }

    // الصفحة السابقة
    $('#prev-page-btn').on('click', function() {
        if (currentPage > 1) {
            currentPage--;
            loadQuestions();
        }
    });

    // الصفحة التالية
    $('#next-page-btn').on('click', function() {
        if (currentPage < totalPages) {
            currentPage++;
            loadQuestions();
        }
    });

    // فتح Modal
    $('#add-question-btn').on('click', function() {
        $('#add-question-modal').fadeIn();
    });

    // إغلاق Modal
    $('#close-question-modal, #cancel-question-btn').on('click', function() {
        $('#add-question-modal').fadeOut();
    });

    // إغلاق عند النقر خارج Modal
    $(document).on('click', function(e) {
        if ($(e.target).is('#add-question-modal')) {
            $('#add-question-modal').fadeOut();
        }
    });

    // تحميل الدروس عند اختيار مقرر
    $('#question-course').on('change', function() {
        let courseId = $(this).val();
        $('#question-lesson').html('<option value=""><?php _e('-- اختر الدرس --', 'fiqhlearning'); ?></option>');

        if (courseId) {
            $.ajax({
                url: fiqhData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'fiqh_get_course_lessons',
                    nonce: fiqhData.nonce,
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

    // إرسال السؤال
    $('#add-question-form').on('submit', function(e) {
        e.preventDefault();

        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<svg class="spinner" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line></svg> <?php _e('جاري الإرسال...', 'fiqhlearning'); ?>');

        $.ajax({
            url: fiqhData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'fiqh_add_question',
                nonce: fiqhData.nonce,
                course_id: $('#question-course').val(),
                lesson_id: $('#question-lesson').val(),
                question_text: $('#question-text').val(),
                is_anonymous: $('#question-anonymous').is(':checked') ? 1 : 0
            },
            success: function(response) {
                if (response.success) {
                    $('#add-question-modal').fadeOut();
                    $('#add-question-form')[0].reset();
                    currentPage = 1;
                    loadQuestions();
                    alert('<?php _e('تم إرسال السؤال بنجاح', 'fiqhlearning'); ?>');
                } else {
                    alert(response.data.message || '<?php _e('حدث خطأ أثناء إرسال السؤال', 'fiqhlearning'); ?>');
                }
            },
            error: function() {
                alert('<?php _e('حدث خطأ أثناء إرسال السؤال', 'fiqhlearning'); ?>');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg> <?php _e('إرسال السؤال', 'fiqhlearning'); ?>');
            }
        });
    });

    // الفلاتر
    $('#filter-science, #filter-course, #filter-status').on('change', function() {
        currentFilters = {
            science_id: $('#filter-science').val(),
            course_id: $('#filter-course').val(),
            status: $('#filter-status').val()
        };
        currentPage = 1;
        loadQuestions();
    });

    // البحث
    $('#questions-search-form').on('submit', function(e) {
        e.preventDefault();
        currentFilters.search = $('#questions-search').val();
        currentPage = 1;
        loadQuestions();
    });

    // أسئلتي فقط
    $('#my-questions-toggle').on('click', function() {
        myQuestionsOnly = !myQuestionsOnly;
        $(this).toggleClass('active');
        currentPage = 1;
        loadQuestions();
    });

    // تحميل الأسئلة عند فتح الصفحة
    loadQuestions();
});
</script>

<style>
/* تنسيقات إضافية خاصة بصفحة الأسئلة */
.search-input-wrapper {
    position: relative;
    display: flex;
}

.search-input-wrapper .filter-input {
    flex: 1;
    padding-left: 40px;
}

.search-btn {
    position: absolute;
    left: 5px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    padding: 8px;
    cursor: pointer;
    color: var(--color-primary);
}

.questions-stats {
    display: flex;
    justify-content: space-around;
    padding: var(--spacing-lg);
    margin-bottom: var(--spacing-xl);
    text-align: center;
}

.stat-item .stat-number {
    font-size: var(--text-3xl);
    font-weight: 700;
    color: var(--color-primary);
}

.stat-item .stat-label {
    font-size: var(--text-sm);
    color: var(--color-gray);
    margin-top: var(--spacing-xs);
}

.question-card {
    margin-bottom: var(--spacing-lg);
}

.question-author-info {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.author-avatar {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-full);
    background: linear-gradient(135deg, var(--color-primary), var(--color-primary-light));
    color: var(--color-white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: var(--text-lg);
}

.author-details {
    display: flex;
    flex-direction: column;
}

.author-name {
    font-weight: 600;
    color: var(--color-dark);
}

.question-date {
    font-size: var(--text-sm);
    color: var(--color-gray);
}

.question-title {
    font-size: var(--text-lg);
    margin: var(--spacing-md) 0;
    line-height: 1.7;
}

.question-meta {
    display: flex;
    gap: var(--spacing-lg);
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    font-size: var(--text-sm);
    color: var(--color-dark-gray);
}

.answer-section {
    margin-top: var(--spacing-md);
    padding-top: var(--spacing-md);
    border-top: 2px solid var(--color-light-gray);
    background-color: var(--color-light);
    padding: var(--spacing-lg);
    border-radius: var(--radius-md);
}

.answer-header {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    color: var(--color-success);
    font-weight: 600;
    margin-bottom: var(--spacing-sm);
}

.answerer-name {
    color: var(--color-gray);
}

.answer-content {
    line-height: 1.7;
    color: var(--color-dark);
}

.modal-description {
    color: var(--color-gray);
    margin-bottom: var(--spacing-xl);
}

.form-group label {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    margin-bottom: var(--spacing-xs);
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

#my-questions-toggle.active {
    background-color: var(--color-primary);
    color: var(--color-white);
}
</style>

<?php
get_footer();
