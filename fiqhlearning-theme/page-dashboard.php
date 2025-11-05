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
                    "SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_lesson_notes WHERE user_id = %d",
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
                                            <div class="progress-fill" style="width: <?php echo intval($progress) . '%'; ?>"></div>
                                        </div>
                                        <div class="progress-text"><?php echo intval($progress) . '%'; ?> <?php _e('مكتمل', 'fiqhlearning'); ?></div>

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
                                        <p class="question-text"><?php echo esc_html(wp_trim_words($question->question, 20)); ?></p>
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
                                        <div class="progress-fill" style="width: <?php echo intval($progress) . '%'; ?>"></div>
                                    </div>
                                    <div class="progress-text"><?php echo intval($progress) . '%'; ?> <?php _e('مكتمل', 'fiqhlearning'); ?></div>

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
                    <h1><?php echo esc_html(get_theme_mod('dashboard_progress_title', __('التقدم الدراسي', 'fiqhlearning'))); ?></h1>
                    <p class="dashboard-subtitle"><?php echo esc_html(get_theme_mod('dashboard_progress_subtitle', __('تتبع تقدمك الأكاديمي وإحصائياتك', 'fiqhlearning'))); ?></p>
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

                // حساب نسبة التقدم الإجمالية
                $total_lessons = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(DISTINCT l.id) FROM {$wpdb->prefix}fiqh_lessons l
                    INNER JOIN {$wpdb->prefix}fiqh_courses c ON l.course_id = c.id
                    INNER JOIN {$wpdb->prefix}fiqh_batch_courses bc ON c.id = bc.course_id
                    INNER JOIN {$wpdb->prefix}fiqh_batch_students bs ON bc.batch_id = bs.batch_id
                    WHERE bs.user_id = %d AND bs.status = 'active'",
                    $user_id
                ));

                $overall_progress = $total_lessons > 0 ? round(($completed_lessons / $total_lessons) * 100) : 0;

                if ($current_batch) :
                ?>
                    <!-- بطاقة المستوى الدراسي -->
                    <div class="batch-info-card card">
                        <div class="batch-header">
                            <div class="batch-icon">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                                </svg>
                            </div>
                            <div class="batch-info">
                                <h3><?php echo esc_html(get_theme_mod('dashboard_current_batch_label', __('المستوى الدراسي الحالي', 'fiqhlearning'))); ?></h3>
                                <div class="batch-name"><?php echo esc_html($current_batch->name); ?></div>
                            </div>
                            <div class="batch-status">
                                <span class="badge badge-success"><?php echo esc_html(get_theme_mod('dashboard_batch_status_active', __('نشط', 'fiqhlearning'))); ?></span>
                            </div>
                        </div>

                        <?php if ($current_batch->start_date && $current_batch->end_date) : ?>
                            <div class="batch-dates">
                                <div class="batch-date-item">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    <div>
                                        <span class="date-label"><?php echo esc_html(get_theme_mod('dashboard_batch_start_date', __('تاريخ البداية', 'fiqhlearning'))); ?>:</span>
                                        <span class="date-value"><?php echo esc_html($current_batch->start_date); ?></span>
                                    </div>
                                </div>
                                <div class="batch-date-item">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    <div>
                                        <span class="date-label"><?php echo esc_html(get_theme_mod('dashboard_batch_end_date', __('تاريخ النهاية', 'fiqhlearning'))); ?>:</span>
                                        <span class="date-value"><?php echo esc_html($current_batch->end_date); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- نسبة التقدم الإجمالية -->
                        <div class="overall-progress">
                            <div class="progress-header">
                                <span class="progress-label"><?php echo esc_html(get_theme_mod('dashboard_overall_progress_label', __('التقدم الإجمالي', 'fiqhlearning'))); ?></span>
                                <span class="progress-percentage"><?php echo $overall_progress; ?>%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $overall_progress; ?>%;"></div>
                            </div>
                            <div class="progress-stats-inline">
                                <span><?php echo $completed_lessons; ?> <?php echo esc_html(get_theme_mod('dashboard_progress_of', __('من', 'fiqhlearning'))); ?> <?php echo $total_lessons; ?> <?php echo esc_html(get_theme_mod('dashboard_progress_lessons', __('درس', 'fiqhlearning'))); ?></span>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- لا يوجد مستوى دراسي -->
                    <div class="no-batch-card card">
                        <div class="no-batch-icon">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                        </div>
                        <h3><?php echo esc_html(get_theme_mod('dashboard_no_batch_title', __('لم تسجل في أي مستوى دراسي بعد', 'fiqhlearning'))); ?></h3>
                        <p><?php echo esc_html(get_theme_mod('dashboard_no_batch_message', __('يرجى التواصل مع الإدارة للتسجيل في مستوى دراسي', 'fiqhlearning'))); ?></p>
                    </div>
                <?php endif; ?>

                <!-- الإحصائيات التفصيلية -->
                <div class="progress-details">
                    <h3><?php echo esc_html(get_theme_mod('dashboard_my_stats_title', __('إحصائياتي', 'fiqhlearning'))); ?></h3>

                    <div class="progress-stats-grid">
                        <!-- المقررات المسجلة -->
                        <div class="progress-stat-item card">
                            <div class="stat-icon" style="background-color: #e3f2fd;">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#1976d2" stroke-width="2">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                </svg>
                            </div>
                            <div class="stat-content">
                                <div class="progress-stat-value"><?php echo $enrolled_courses; ?></div>
                                <div class="progress-stat-label"><?php echo esc_html(get_theme_mod('dashboard_stat_enrolled_courses', __('المقررات المسجلة', 'fiqhlearning'))); ?></div>
                            </div>
                        </div>

                        <!-- الدروس المكتملة -->
                        <div class="progress-stat-item card">
                            <div class="stat-icon" style="background-color: #e8f5e9;">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#388e3c" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                            </div>
                            <div class="stat-content">
                                <div class="progress-stat-value"><?php echo $completed_lessons; ?></div>
                                <div class="progress-stat-label"><?php echo esc_html(get_theme_mod('dashboard_stat_completed_lessons', __('الدروس المكتملة', 'fiqhlearning'))); ?></div>
                            </div>
                        </div>

                        <!-- الأسئلة المطروحة -->
                        <div class="progress-stat-item card">
                            <div class="stat-icon" style="background-color: #fff3e0;">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#f57c00" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                                </svg>
                            </div>
                            <div class="stat-content">
                                <div class="progress-stat-value"><?php echo $questions_asked; ?></div>
                                <div class="progress-stat-label"><?php echo esc_html(get_theme_mod('dashboard_stat_questions_asked', __('الأسئلة المطروحة', 'fiqhlearning'))); ?></div>
                            </div>
                        </div>

                        <!-- الملاحظات -->
                        <div class="progress-stat-item card">
                            <div class="stat-icon" style="background-color: #f3e5f5;">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#7b1fa2" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                            </div>
                            <div class="stat-content">
                                <div class="progress-stat-value"><?php echo $notes_count; ?></div>
                                <div class="progress-stat-label"><?php echo esc_html(get_theme_mod('dashboard_stat_notes_count', __('الملاحظات', 'fiqhlearning'))); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تقدم المقررات -->
                <?php if ($enrolled_courses > 0): ?>
                <div class="courses-progress-section">
                    <h3><?php echo esc_html(get_theme_mod('dashboard_courses_progress_title', __('تقدمك في المقررات', 'fiqhlearning'))); ?></h3>

                    <?php
                    // الحصول على تفاصيل تقدم كل مقرر
                    $courses_progress = $wpdb->get_results($wpdb->prepare(
                        "SELECT
                            c.id,
                            c.title,
                            COUNT(DISTINCT l.id) as total_lessons,
                            COUNT(DISTINCT lp.lesson_id) as completed_lessons_count
                        FROM {$wpdb->prefix}fiqh_courses c
                        INNER JOIN {$wpdb->prefix}fiqh_batch_courses bc ON c.id = bc.course_id
                        INNER JOIN {$wpdb->prefix}fiqh_batch_students bs ON bc.batch_id = bs.batch_id
                        LEFT JOIN {$wpdb->prefix}fiqh_lessons l ON c.id = l.course_id
                        LEFT JOIN {$wpdb->prefix}fiqh_lesson_progress lp ON l.id = lp.lesson_id AND lp.user_id = %d AND lp.status = 'completed'
                        WHERE bs.user_id = %d AND bs.status = 'active'
                        GROUP BY c.id, c.title
                        ORDER BY c.title",
                        $user_id, $user_id
                    ));

                    if ($courses_progress):
                    ?>
                    <div class="courses-progress-list">
                        <?php foreach ($courses_progress as $course):
                            $course_progress = $course->total_lessons > 0 ? round(($course->completed_lessons_count / $course->total_lessons) * 100) : 0;
                        ?>
                        <div class="course-progress-item card">
                            <div class="course-progress-header">
                                <div class="course-info">
                                    <h4><?php echo esc_html($course->title); ?></h4>
                                    <p class="course-stats">
                                        <?php echo $course->completed_lessons_count; ?> <?php echo esc_html(get_theme_mod('dashboard_progress_of', __('من', 'fiqhlearning'))); ?>
                                        <?php echo $course->total_lessons; ?> <?php echo esc_html(get_theme_mod('dashboard_progress_lessons', __('درس', 'fiqhlearning'))); ?>
                                    </p>
                                </div>
                                <div class="course-percentage">
                                    <span class="percentage-value"><?php echo $course_progress; ?>%</span>
                                </div>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $course_progress; ?>%;"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Tab: الإعدادات -->
            <div id="settings-tab" class="dashboard-tab">
                <div class="dashboard-header">
                    <h1><?php echo esc_html(get_theme_mod('dashboard_settings_title', __('الإعدادات', 'fiqhlearning'))); ?></h1>
                    <p><?php echo esc_html(get_theme_mod('dashboard_settings_subtitle', __('إدارة حسابك وتفضيلاتك الشخصية', 'fiqhlearning'))); ?></p>
                </div>

                <!-- تعديل الملف الشخصي -->
                <div class="settings-section card">
                    <div class="settings-section-header">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <h3><?php echo esc_html(get_theme_mod('dashboard_profile_section_title', __('المعلومات الشخصية', 'fiqhlearning'))); ?></h3>
                    </div>

                    <form id="profile-update-form" class="settings-form" method="post">
                        <?php wp_nonce_field('update_profile', 'profile_nonce'); ?>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="display_name"><?php echo esc_html(get_theme_mod('dashboard_label_display_name', __('الاسم الكامل', 'fiqhlearning'))); ?></label>
                                <input type="text" id="display_name" name="display_name" class="form-control" value="<?php echo esc_attr($current_user->display_name); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="user_email"><?php echo esc_html(get_theme_mod('dashboard_label_email', __('البريد الإلكتروني', 'fiqhlearning'))); ?></label>
                                <input type="email" id="user_email" name="user_email" class="form-control" value="<?php echo esc_attr($current_user->user_email); ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="user_login"><?php echo esc_html(get_theme_mod('dashboard_label_username', __('اسم المستخدم', 'fiqhlearning'))); ?></label>
                                <input type="text" id="user_login" class="form-control" value="<?php echo esc_attr($current_user->user_login); ?>" disabled>
                                <small class="form-text"><?php echo esc_html(get_theme_mod('dashboard_username_note', __('لا يمكن تغيير اسم المستخدم', 'fiqhlearning'))); ?></small>
                            </div>
                            <div class="form-group">
                                <label><?php echo esc_html(get_theme_mod('dashboard_label_registered', __('تاريخ التسجيل', 'fiqhlearning'))); ?></label>
                                <input type="text" class="form-control" value="<?php echo date_i18n(get_option('date_format'), strtotime($current_user->user_registered)); ?>" disabled>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                    <polyline points="7 3 7 8 15 8"></polyline>
                                </svg>
                                <?php echo esc_html(get_theme_mod('dashboard_save_profile_btn', __('حفظ التغييرات', 'fiqhlearning'))); ?>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- تغيير كلمة المرور -->
                <div class="settings-section card">
                    <div class="settings-section-header">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <h3><?php echo esc_html(get_theme_mod('dashboard_password_section_title', __('تغيير كلمة المرور', 'fiqhlearning'))); ?></h3>
                    </div>

                    <form id="password-change-form" class="settings-form" method="post">
                        <?php wp_nonce_field('change_password', 'password_nonce'); ?>

                        <div class="form-group">
                            <label for="current_password"><?php echo esc_html(get_theme_mod('dashboard_label_current_password', __('كلمة المرور الحالية', 'fiqhlearning'))); ?></label>
                            <input type="password" id="current_password" name="current_password" class="form-control" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="new_password"><?php echo esc_html(get_theme_mod('dashboard_label_new_password', __('كلمة المرور الجديدة', 'fiqhlearning'))); ?></label>
                                <input type="password" id="new_password" name="new_password" class="form-control" required>
                                <small class="form-text"><?php echo esc_html(get_theme_mod('dashboard_password_note', __('يجب أن تحتوي على 8 أحرف على الأقل', 'fiqhlearning'))); ?></small>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password"><?php echo esc_html(get_theme_mod('dashboard_label_confirm_password', __('تأكيد كلمة المرور', 'fiqhlearning'))); ?></label>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                    <polyline points="7 3 7 8 15 8"></polyline>
                                </svg>
                                <?php echo esc_html(get_theme_mod('dashboard_change_password_btn', __('تحديث كلمة المرور', 'fiqhlearning'))); ?>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- إعدادات الإشعارات -->
                <div class="settings-section card">
                    <div class="settings-section-header">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <h3><?php echo esc_html(get_theme_mod('dashboard_notifications_section_title', __('الإشعارات', 'fiqhlearning'))); ?></h3>
                    </div>

                    <form id="notifications-form" class="settings-form" method="post">
                        <?php wp_nonce_field('update_notifications', 'notifications_nonce'); ?>

                        <div class="notification-options">
                            <div class="notification-item">
                                <div class="notification-info">
                                    <h4><?php echo esc_html(get_theme_mod('dashboard_notif_new_lessons', __('دروس جديدة', 'fiqhlearning'))); ?></h4>
                                    <p><?php echo esc_html(get_theme_mod('dashboard_notif_new_lessons_desc', __('تلقي إشعار عند إضافة دروس جديدة في مقرراتك', 'fiqhlearning'))); ?></p>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" name="notify_new_lessons" value="1" <?php checked(get_user_meta($user_id, 'notify_new_lessons', true), '1'); ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="notification-item">
                                <div class="notification-info">
                                    <h4><?php echo esc_html(get_theme_mod('dashboard_notif_answers', __('إجابة الأسئلة', 'fiqhlearning'))); ?></h4>
                                    <p><?php echo esc_html(get_theme_mod('dashboard_notif_answers_desc', __('تلقي إشعار عند الإجابة على أسئلتك', 'fiqhlearning'))); ?></p>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" name="notify_question_answers" value="1" <?php checked(get_user_meta($user_id, 'notify_question_answers', true), '1'); ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="notification-item">
                                <div class="notification-info">
                                    <h4><?php echo esc_html(get_theme_mod('dashboard_notif_announcements', __('الإعلانات', 'fiqhlearning'))); ?></h4>
                                    <p><?php echo esc_html(get_theme_mod('dashboard_notif_announcements_desc', __('تلقي إشعار بالإعلانات والتحديثات الهامة', 'fiqhlearning'))); ?></p>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" name="notify_announcements" value="1" <?php checked(get_user_meta($user_id, 'notify_announcements', true), '1'); ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                    <polyline points="7 3 7 8 15 8"></polyline>
                                </svg>
                                <?php echo esc_html(get_theme_mod('dashboard_save_notifications_btn', __('حفظ الإعدادات', 'fiqhlearning'))); ?>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- معلومات الحساب -->
                <div class="settings-section card">
                    <div class="settings-section-header">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        <h3><?php echo esc_html(get_theme_mod('dashboard_account_info_title', __('معلومات الحساب', 'fiqhlearning'))); ?></h3>
                    </div>

                    <div class="account-info-grid">
                        <div class="account-info-item">
                            <div class="account-info-label"><?php echo esc_html(get_theme_mod('dashboard_account_status', __('حالة الحساب', 'fiqhlearning'))); ?></div>
                            <div class="account-info-value">
                                <span class="badge badge-success"><?php echo esc_html(get_theme_mod('dashboard_account_active', __('نشط', 'fiqhlearning'))); ?></span>
                            </div>
                        </div>
                        <div class="account-info-item">
                            <div class="account-info-label"><?php echo esc_html(get_theme_mod('dashboard_account_type', __('نوع الحساب', 'fiqhlearning'))); ?></div>
                            <div class="account-info-value">
                                <?php
                                if (in_array('student', $user_roles)) {
                                    echo esc_html(get_theme_mod('dashboard_role_student', __('طالب', 'fiqhlearning')));
                                } elseif (in_array('teacher', $user_roles)) {
                                    echo esc_html(get_theme_mod('dashboard_role_teacher', __('معلم', 'fiqhlearning')));
                                }
                                ?>
                            </div>
                        </div>
                    </div>
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
