<?php
/**
 * Template Name: لوحة التحكم
 * لوحة التحكم المخصصة للطلاب
 *
 * @package FiqhLearning
 */

// التحقق من تسجيل الدخول
if (!is_user_logged_in()) {
    wp_redirect(home_url('/login'));
    exit;
}

$current_user = wp_get_current_user();
$user_id = get_current_user_id();
$user_roles = $current_user->roles;

get_header();
?>

<main class="dashboard-page">
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="dashboard-sidebar">
            <div class="dashboard-user-card card">
                <div class="user-avatar">
                    <?php echo get_avatar($user_id, 96); ?>
                </div>
                <h3 class="user-name"><?php echo esc_html($current_user->display_name); ?></h3>
                <p class="user-role">
                    <?php
                    if (in_array('student', $user_roles)) {
                        _e('طالب', 'fiqhlearning');
                    } elseif (in_array('teacher', $user_roles)) {
                        _e('معلم', 'fiqhlearning');
                    }
                    ?>
                </p>
            </div>

            <nav class="dashboard-nav">
                <ul class="dashboard-menu">
                    <li class="active">
                        <a href="#overview" data-tab="overview">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                            <?php _e('نظرة عامة', 'fiqhlearning'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#courses" data-tab="courses">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                            <?php _e('مقرراتي', 'fiqhlearning'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#progress" data-tab="progress">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                            </svg>
                            <?php _e('التقدم الدراسي', 'fiqhlearning'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo get_permalink(get_page_by_path('questions')); ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>
                            <?php _e('الأسئلة', 'fiqhlearning'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#settings" data-tab="settings">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M12 1v6m0 6v6m0-18l4 2m-8 0l4-2m0 18l-4-2m8 0l-4 2M1 12h6m6 0h6M1 12l2-4m0 8l-2-4m22 0h-6m6 0l-2 4m0-8l2 4"></path>
                            </svg>
                            <?php _e('الإعدادات', 'fiqhlearning'); ?>
                        </a>
                    </li>
                </ul>

                <div class="dashboard-logout">
                    <a href="<?php echo wp_logout_url(); ?>" class="btn btn-outline btn-block">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        <?php _e('تسجيل الخروج', 'fiqhlearning'); ?>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="dashboard-main">
            <!-- Tab: نظرة عامة -->
            <div id="overview-tab" class="dashboard-tab active">
                <div class="dashboard-header">
                    <h1><?php _e('مرحباً، ', 'fiqhlearning'); ?><?php echo esc_html($current_user->display_name); ?></h1>
                    <p><?php _e('نتمنى لك يوماً دراسياً مثمراً', 'fiqhlearning'); ?></p>
                </div>

                <!-- إحصائيات سريعة -->
                <?php
                global $wpdb;

                // عدد المقررات المسجل فيها
                $enrolled_courses = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_enrollments WHERE user_id = %d AND status = 'active'",
                    $user_id
                ));

                // عدد الدروس المكتملة
                $completed_lessons = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_progress WHERE user_id = %d AND status = 'completed'",
                    $user_id
                ));

                // عدد الأسئلة المطروحة
                $questions_asked = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_questions WHERE user_id = %d AND status != 'deleted'",
                    $user_id
                ));

                // عدد الملاحظات
                $notes_count = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_notes WHERE user_id = %d",
                    $user_id
                ));
                ?>

                <div class="dashboard-stats">
                    <div class="stat-card card">
                        <div class="stat-icon stat-primary">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $enrolled_courses; ?></div>
                            <div class="stat-label"><?php _e('مقرراتي', 'fiqhlearning'); ?></div>
                        </div>
                    </div>

                    <div class="stat-card card">
                        <div class="stat-icon stat-success">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $completed_lessons; ?></div>
                            <div class="stat-label"><?php _e('درس مكتمل', 'fiqhlearning'); ?></div>
                        </div>
                    </div>

                    <div class="stat-card card">
                        <div class="stat-icon stat-info">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $questions_asked; ?></div>
                            <div class="stat-label"><?php _e('سؤال مطروح', 'fiqhlearning'); ?></div>
                        </div>
                    </div>

                    <div class="stat-card card">
                        <div class="stat-icon stat-warning">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $notes_count; ?></div>
                            <div class="stat-label"><?php _e('ملاحظة', 'fiqhlearning'); ?></div>
                        </div>
                    </div>
                </div>

                <!-- المقررات الحالية -->
                <section class="dashboard-section">
                    <div class="section-header">
                        <h2><?php _e('مقرراتي الحالية', 'fiqhlearning'); ?></h2>
                        <a href="#courses" data-tab="courses" class="btn btn-outline btn-sm">
                            <?php _e('عرض الكل', 'fiqhlearning'); ?>
                        </a>
                    </div>

                    <?php
                    $enrollments = $wpdb->get_results($wpdb->prepare(
                        "SELECT e.*, c.post_title as course_title
                        FROM {$wpdb->prefix}fiqh_enrollments e
                        LEFT JOIN {$wpdb->posts} c ON e.course_id = c.ID
                        WHERE e.user_id = %d AND e.status = 'active'
                        ORDER BY e.enrolled_at DESC
                        LIMIT 3",
                        $user_id
                    ));

                    if ($enrollments) :
                    ?>
                        <div class="courses-grid">
                            <?php foreach ($enrollments as $enrollment) :
                                $course_id = $enrollment->course_id;
                                $course = get_post($course_id);
                                $progress = fiqh_get_course_progress($course_id, $user_id);
                            ?>
                                <article class="course-card card">
                                    <?php if (has_post_thumbnail($course_id)) : ?>
                                        <div class="course-thumbnail">
                                            <?php echo get_the_post_thumbnail($course_id, 'medium'); ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="course-content">
                                        <h3 class="course-title">
                                            <a href="<?php echo get_permalink($course_id); ?>">
                                                <?php echo esc_html($enrollment->course_title); ?>
                                            </a>
                                        </h3>

                                        <div class="course-progress-bar">
                                            <div class="progress-fill" style="width: <?php echo intval($progress); ?>%"></div>
                                        </div>
                                        <div class="progress-text"><?php echo intval($progress); ?>% <?php _e('مكتمل', 'fiqhlearning'); ?></div>

                                        <a href="<?php echo get_permalink($course_id); ?>" class="btn btn-primary btn-sm btn-block">
                                            <?php _e('متابعة الدراسة', 'fiqhlearning'); ?>
                                        </a>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="empty-state card">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                            <h3><?php _e('لا توجد مقررات حالياً', 'fiqhlearning'); ?></h3>
                            <p><?php _e('لم تسجل في أي مقرر بعد', 'fiqhlearning'); ?></p>
                            <a href="<?php echo get_post_type_archive_link('fiqh_course'); ?>" class="btn btn-primary">
                                <?php _e('تصفح المقررات', 'fiqhlearning'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- الأسئلة الأخيرة -->
                <section class="dashboard-section">
                    <div class="section-header">
                        <h2><?php _e('أسئلتي الأخيرة', 'fiqhlearning'); ?></h2>
                        <a href="<?php echo get_permalink(get_page_by_path('questions')); ?>" class="btn btn-outline btn-sm">
                            <?php _e('عرض الكل', 'fiqhlearning'); ?>
                        </a>
                    </div>

                    <?php
                    $recent_questions = $wpdb->get_results($wpdb->prepare(
                        "SELECT q.*, c.post_title as course_name
                        FROM {$wpdb->prefix}fiqh_questions q
                        LEFT JOIN {$wpdb->posts} c ON q.course_id = c.ID
                        WHERE q.user_id = %d AND q.status != 'deleted'
                        ORDER BY q.created_at DESC
                        LIMIT 5",
                        $user_id
                    ));

                    if ($recent_questions) :
                    ?>
                        <div class="questions-list">
                            <?php foreach ($recent_questions as $question) : ?>
                                <div class="question-item card">
                                    <div class="question-status <?php echo $question->status === 'answered' ? 'status-answered' : 'status-pending'; ?>">
                                        <?php echo $question->status === 'answered' ? __('مُجابة', 'fiqhlearning') : __('بانتظار', 'fiqhlearning'); ?>
                                    </div>
                                    <div class="question-content">
                                        <p class="question-text"><?php echo esc_html(wp_trim_words($question->question_text, 20)); ?></p>
                                        <div class="question-meta">
                                            <?php if ($question->course_name) : ?>
                                                <span class="course-badge"><?php echo esc_html($question->course_name); ?></span>
                                            <?php endif; ?>
                                            <span class="question-date"><?php echo human_time_diff(strtotime($question->created_at), current_time('timestamp')) . ' ' . __('مضت', 'fiqhlearning'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="empty-state-small card">
                            <p><?php _e('لم تطرح أي أسئلة بعد', 'fiqhlearning'); ?></p>
                        </div>
                    <?php endif; ?>
                </section>
            </div>

            <!-- Tab: المقررات -->
            <div id="courses-tab" class="dashboard-tab">
                <div class="dashboard-header">
                    <h1><?php _e('مقرراتي', 'fiqhlearning'); ?></h1>
                </div>

                <?php
                $all_enrollments = $wpdb->get_results($wpdb->prepare(
                    "SELECT e.*, c.post_title as course_title
                    FROM {$wpdb->prefix}fiqh_enrollments e
                    LEFT JOIN {$wpdb->posts} c ON e.course_id = c.ID
                    WHERE e.user_id = %d AND e.status = 'active'
                    ORDER BY e.enrolled_at DESC",
                    $user_id
                ));

                if ($all_enrollments) :
                ?>
                    <div class="courses-grid">
                        <?php foreach ($all_enrollments as $enrollment) :
                            $course_id = $enrollment->course_id;
                            $course = get_post($course_id);
                            $progress = fiqh_get_course_progress($course_id, $user_id);
                        ?>
                            <article class="course-card card">
                                <?php if (has_post_thumbnail($course_id)) : ?>
                                    <div class="course-thumbnail">
                                        <?php echo get_the_post_thumbnail($course_id, 'medium'); ?>
                                    </div>
                                <?php endif; ?>

                                <div class="course-content">
                                    <h3 class="course-title">
                                        <a href="<?php echo get_permalink($course_id); ?>">
                                            <?php echo esc_html($enrollment->course_title); ?>
                                        </a>
                                    </h3>

                                    <div class="course-progress-bar">
                                        <div class="progress-fill" style="width: <?php echo intval($progress); ?>%"></div>
                                    </div>
                                    <div class="progress-text"><?php echo intval($progress); ?>% <?php _e('مكتمل', 'fiqhlearning'); ?></div>

                                    <a href="<?php echo get_permalink($course_id); ?>" class="btn btn-primary btn-sm btn-block">
                                        <?php _e('متابعة الدراسة', 'fiqhlearning'); ?>
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="empty-state card">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                        <h3><?php _e('لا توجد مقررات حالياً', 'fiqhlearning'); ?></h3>
                        <p><?php _e('لم تسجل في أي مقرر بعد', 'fiqhlearning'); ?></p>
                        <a href="<?php echo get_post_type_archive_link('fiqh_course'); ?>" class="btn btn-primary">
                            <?php _e('تصفح المقررات', 'fiqhlearning'); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tab: التقدم الدراسي -->
            <div id="progress-tab" class="dashboard-tab">
                <div class="dashboard-header">
                    <h1><?php _e('التقدم الدراسي', 'fiqhlearning'); ?></h1>
                </div>

                <?php
                // المستوى الدراسي الحالي
                $current_batch = $wpdb->get_row($wpdb->prepare(
                    "SELECT b.* FROM {$wpdb->prefix}fiqh_batches b
                    LEFT JOIN {$wpdb->prefix}fiqh_batch_students bs ON b.id = bs.batch_id
                    WHERE bs.user_id = %d AND bs.status = 'active' AND b.status = 'active'
                    ORDER BY b.start_date DESC
                    LIMIT 1",
                    $user_id
                ));

                if ($current_batch) :
                ?>
                    <div class="batch-info-card card">
                        <h3><?php _e('المستوى الدراسي الحالي', 'fiqhlearning'); ?></h3>
                        <div class="batch-name"><?php echo esc_html($current_batch->name); ?></div>
                        <?php if ($current_batch->start_date && $current_batch->end_date) : ?>
                            <div class="batch-dates">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <?php echo esc_html($current_batch->start_date . ' - ' . $current_batch->end_date); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- الإحصائيات التفصيلية -->
                <div class="progress-details">
                    <h3><?php _e('إحصائياتي', 'fiqhlearning'); ?></h3>

                    <div class="progress-stats-grid">
                        <div class="progress-stat-item card">
                            <div class="progress-stat-label"><?php _e('المقررات المسجلة', 'fiqhlearning'); ?></div>
                            <div class="progress-stat-value"><?php echo $enrolled_courses; ?></div>
                        </div>
                        <div class="progress-stat-item card">
                            <div class="progress-stat-label"><?php _e('الدروس المكتملة', 'fiqhlearning'); ?></div>
                            <div class="progress-stat-value"><?php echo $completed_lessons; ?></div>
                        </div>
                        <div class="progress-stat-item card">
                            <div class="progress-stat-label"><?php _e('الأسئلة المطروحة', 'fiqhlearning'); ?></div>
                            <div class="progress-stat-value"><?php echo $questions_asked; ?></div>
                        </div>
                        <div class="progress-stat-item card">
                            <div class="progress-stat-label"><?php _e('الملاحظات', 'fiqhlearning'); ?></div>
                            <div class="progress-stat-value"><?php echo $notes_count; ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: الإعدادات -->
            <div id="settings-tab" class="dashboard-tab">
                <div class="dashboard-header">
                    <h1><?php _e('الإعدادات', 'fiqhlearning'); ?></h1>
                </div>

                <div class="settings-section card">
                    <h3><?php _e('المعلومات الشخصية', 'fiqhlearning'); ?></h3>

                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label"><?php _e('الاسم', 'fiqhlearning'); ?></div>
                            <div class="info-value"><?php echo esc_html($current_user->display_name); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><?php _e('البريد الإلكتروني', 'fiqhlearning'); ?></div>
                            <div class="info-value"><?php echo esc_html($current_user->user_email); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><?php _e('اسم المستخدم', 'fiqhlearning'); ?></div>
                            <div class="info-value"><?php echo esc_html($current_user->user_login); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><?php _e('تاريخ التسجيل', 'fiqhlearning'); ?></div>
                            <div class="info-value"><?php echo date_i18n(get_option('date_format'), strtotime($current_user->user_registered)); ?></div>
                        </div>
                    </div>

                    <p class="settings-note">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        <?php _e('لتعديل معلوماتك الشخصية، يرجى التواصل مع الإدارة', 'fiqhlearning'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Tab switching functionality
document.addEventListener('DOMContentLoaded', function() {
    const menuLinks = document.querySelectorAll('.dashboard-menu a[data-tab]');
    const tabs = document.querySelectorAll('.dashboard-tab');

    menuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            const targetTab = this.getAttribute('data-tab');

            // Remove active class from all menu items and tabs
            menuLinks.forEach(l => l.parentElement.classList.remove('active'));
            tabs.forEach(tab => tab.classList.remove('active'));

            // Add active class to clicked menu item and corresponding tab
            this.parentElement.classList.add('active');
            document.getElementById(targetTab + '-tab').classList.add('active');

            // Update URL hash without jumping
            history.pushState(null, null, '#' + targetTab);
        });
    });

    // Handle initial hash on page load
    const hash = window.location.hash.substring(1);
    if (hash) {
        const targetLink = document.querySelector(`[data-tab="${hash}"]`);
        if (targetLink) {
            targetLink.click();
        }
    }
});
</script>

<?php
get_footer();
