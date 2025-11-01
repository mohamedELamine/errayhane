<?php
/**
 * Template Name: لوحة الطالب
 * Student Dashboard Page
 *
 * @package FiqhLearning
 */

// التحقق من تسجيل الدخول
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

$current_user = wp_get_current_user();
$user_id = $current_user->ID;

// التحقق من أن المستخدم طالب
if (!in_array('student', $current_user->roles) && !in_array('administrator', $current_user->roles)) {
    wp_redirect(home_url());
    exit;
}

get_header();

global $wpdb;

// جلب المقررات المسجل فيها الطالب
$enrolled_courses = $wpdb->get_results($wpdb->prepare(
    "SELECT e.*, p.ID, p.post_title, p.post_excerpt
    FROM {$wpdb->prefix}fiqh_enrollments e
    INNER JOIN {$wpdb->prefix}posts p ON e.course_id = p.ID
    WHERE e.user_id = %d AND e.status = 'active' AND p.post_status = 'publish'
    ORDER BY e.enrolled_at DESC",
    $user_id
));

// إحصائيات الطالب
$total_courses = count($enrolled_courses);
$total_completed_lessons = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_progress
    WHERE user_id = %d AND status = 'completed'",
    $user_id
));

// حساب نسبة الإنجاز الإجمالية
$total_progress = 0;
if ($total_courses > 0) {
    foreach ($enrolled_courses as $course) {
        $total_progress += fiqh_get_course_progress($course->course_id, $user_id);
    }
    $average_progress = round($total_progress / $total_courses);
} else {
    $average_progress = 0;
}

// الإشعارات الحديثة
$notifications = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}fiqh_notifications
    WHERE user_id = %d
    ORDER BY created_at DESC
    LIMIT 5",
    $user_id
), ARRAY_A);

// الأسئلة الأخيرة للطالب
$my_questions = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}fiqh_questions
    WHERE user_id = %d
    ORDER BY created_at DESC
    LIMIT 3",
    $user_id
), ARRAY_A);

$unread_notifications = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_notifications
    WHERE user_id = %d AND is_read = 0",
    $user_id
));

?>

<main class="student-dashboard-page">
    <div class="container">

        <!-- Welcome Header -->
        <div class="dashboard-header">
            <div class="welcome-section">
                <div class="user-avatar">
                    <?php echo get_avatar($user_id, 80); ?>
                </div>
                <div class="welcome-text">
                    <h1 class="welcome-title">مرحباً، <?php echo esc_html($current_user->display_name); ?></h1>
                    <p class="welcome-subtitle">نتمنى لك يوماً دراسياً موفقاً</p>
                </div>
            </div>

            <div class="quick-actions">
                <a href="<?php echo home_url('/questions'); ?>" class="btn btn-primary">
                    <i class="icon-question"></i> طرح سؤال
                </a>
                <a href="#contact-admin" class="btn btn-outline-primary" data-toggle="modal">
                    <i class="icon-message"></i> استفسار إداري
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-icon" style="background: #10B981;">
                    <i class="icon-book"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo $total_courses; ?></div>
                    <div class="stat-label">مقررات مسجلة</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: #3B82F6;">
                    <i class="icon-checkmark"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo $total_completed_lessons; ?></div>
                    <div class="stat-label">دروس مكتملة</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: #F59E0B;">
                    <i class="icon-chart"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo $average_progress; ?>%</div>
                    <div class="stat-label">نسبة الإنجاز</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: #8B5CF6;">
                    <i class="icon-bell"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo $unread_notifications; ?></div>
                    <div class="stat-label">إشعارات جديدة</div>
                </div>
            </div>
        </div>

        <div class="dashboard-content">

            <!-- المقررات المسجلة -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="icon-book"></i> مقرراتي الدراسية
                    </h2>
                    <a href="<?php echo get_post_type_archive_link('fiqh_course'); ?>" class="view-all-link">
                        عرض الكل
                    </a>
                </div>

                <?php if (!empty($enrolled_courses)): ?>
                    <div class="courses-grid">
                        <?php foreach ($enrolled_courses as $enrollment):
                            $course = get_post($enrollment->course_id);
                            $progress = fiqh_get_course_progress($course->ID, $user_id);
                            $course_type = get_the_terms($course->ID, 'fiqh_course_type');
                            $course_type_name = !empty($course_type) ? $course_type[0]->name : 'عام';

                            // عدد الدروس
                            $lessons_count = $wpdb->get_var($wpdb->prepare(
                                "SELECT COUNT(*) FROM {$wpdb->prefix}posts p
                                INNER JOIN {$wpdb->prefix}postmeta pm ON p.ID = pm.post_id
                                WHERE p.post_type = 'fiqh_lesson' AND p.post_status = 'publish'
                                AND pm.meta_key = '_fiqh_lesson_course_id' AND pm.meta_value = %d",
                                $course->ID
                            ));
                            ?>
                            <div class="course-card">
                                <?php if (has_post_thumbnail($course->ID)): ?>
                                    <div class="course-thumbnail">
                                        <a href="<?php echo get_permalink($course->ID); ?>">
                                            <?php echo get_the_post_thumbnail($course->ID, 'fiqh-course-thumb'); ?>
                                        </a>
                                        <div class="progress-overlay">
                                            <div class="circular-progress" data-progress="<?php echo $progress; ?>">
                                                <svg width="60" height="60">
                                                    <circle cx="30" cy="30" r="25" fill="none" stroke="#fff" stroke-width="3" opacity="0.3"/>
                                                    <circle cx="30" cy="30" r="25" fill="none" stroke="#fff" stroke-width="3"
                                                            stroke-dasharray="<?php echo 2 * pi() * 25; ?>"
                                                            stroke-dashoffset="<?php echo 2 * pi() * 25 * (1 - $progress / 100); ?>"
                                                            transform="rotate(-90 30 30)"/>
                                                </svg>
                                                <span class="progress-text"><?php echo $progress; ?>%</span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="course-content">
                                    <span class="course-type badge badge-<?php echo sanitize_html_class($course_type_name); ?>">
                                        <?php echo esc_html($course_type_name); ?>
                                    </span>

                                    <h3 class="course-title">
                                        <a href="<?php echo get_permalink($course->ID); ?>">
                                            <?php echo get_the_title($course->ID); ?>
                                        </a>
                                    </h3>

                                    <div class="course-meta">
                                        <span><i class="icon-video"></i> <?php echo $lessons_count; ?> درس</span>
                                        <span><i class="icon-calendar"></i> <?php echo fiqh_format_date($enrollment->enrolled_at); ?></span>
                                    </div>

                                    <div class="progress-bar-wrapper">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?php echo $progress; ?>%;"></div>
                                        </div>
                                        <span class="progress-percentage"><?php echo $progress; ?>%</span>
                                    </div>

                                    <a href="<?php echo get_permalink($course->ID); ?>" class="btn btn-sm btn-primary btn-block">
                                        متابعة الدراسة
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="icon-book-open"></i>
                        <p>لم تسجل في أي مقرر بعد</p>
                        <a href="<?php echo get_post_type_archive_link('fiqh_course'); ?>" class="btn btn-primary">
                            استعرض المقررات
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- الإشعارات والأسئلة -->
            <aside class="dashboard-sidebar">

                <!-- الإشعارات -->
                <div class="sidebar-widget notifications-widget">
                    <h3 class="widget-title">
                        <i class="icon-bell"></i> الإشعارات
                        <?php if ($unread_notifications > 0): ?>
                            <span class="badge badge-danger"><?php echo $unread_notifications; ?></span>
                        <?php endif; ?>
                    </h3>

                    <?php if (!empty($notifications)): ?>
                        <ul class="notifications-list">
                            <?php foreach ($notifications as $notification): ?>
                                <li class="notification-item <?php echo $notification['is_read'] ? '' : 'unread'; ?>">
                                    <div class="notification-icon">
                                        <i class="icon-<?php echo esc_attr($notification['type']); ?>"></i>
                                    </div>
                                    <div class="notification-content">
                                        <h4><?php echo esc_html($notification['title']); ?></h4>
                                        <p><?php echo esc_html($notification['message']); ?></p>
                                        <span class="notification-date">
                                            <?php echo human_time_diff(strtotime($notification['created_at']), current_time('timestamp')); ?> مضت
                                        </span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="no-notifications">لا توجد إشعارات حالياً</p>
                    <?php endif; ?>
                </div>

                <!-- أسئلتي الأخيرة -->
                <div class="sidebar-widget my-questions-widget">
                    <h3 class="widget-title">
                        <i class="icon-question"></i> أسئلتي الأخيرة
                    </h3>

                    <?php if (!empty($my_questions)): ?>
                        <ul class="questions-list">
                            <?php foreach ($my_questions as $question): ?>
                                <li class="question-item">
                                    <p class="question-text">
                                        <?php echo wp_trim_words($question['question'], 15); ?>
                                    </p>
                                    <div class="question-meta">
                                        <span class="status status-<?php echo esc_attr($question['status']); ?>">
                                            <?php echo $question['status'] === 'answered' ? 'تمت الإجابة' : 'قيد الانتظار'; ?>
                                        </span>
                                        <span class="date">
                                            <?php echo fiqh_format_date($question['created_at']); ?>
                                        </span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="<?php echo home_url('/questions'); ?>" class="view-all-btn">
                            عرض جميع أسئلتي
                        </a>
                    <?php else: ?>
                        <p class="no-questions">لم تطرح أي سؤال بعد</p>
                        <a href="<?php echo home_url('/questions'); ?>" class="btn btn-sm btn-primary btn-block">
                            طرح سؤال جديد
                        </a>
                    <?php endif; ?>
                </div>

                <!-- روابط سريعة -->
                <div class="sidebar-widget quick-links-widget">
                    <h3 class="widget-title">
                        <i class="icon-link"></i> روابط سريعة
                    </h3>
                    <ul class="quick-links">
                        <li><a href="<?php echo home_url('/questions'); ?>"><i class="icon-question"></i> الأسئلة والأجوبة</a></li>
                        <li><a href="<?php echo home_url('/tests-reports'); ?>"><i class="icon-file"></i> الاختبارات والتقارير</a></li>
                        <li><a href="<?php echo home_url('/policies'); ?>"><i class="icon-book"></i> اللوائح والأنظمة</a></li>
                        <li><a href="<?php echo admin_url('profile.php'); ?>"><i class="icon-settings"></i> الإعدادات</a></li>
                    </ul>
                </div>

            </aside>

        </div>

    </div>
</main>

<!-- Modal استفسار إداري -->
<div id="contact-admin" class="modal">
    <div class="modal-content">
        <span class="modal-close">&times;</span>
        <h2>إرسال استفسار إداري</h2>
        <form id="admin-inquiry-form" class="inquiry-form">
            <div class="form-group">
                <label for="inquiry-subject">الموضوع</label>
                <input type="text" id="inquiry-subject" name="subject" required>
            </div>
            <div class="form-group">
                <label for="inquiry-message">الرسالة</label>
                <textarea id="inquiry-message" name="message" rows="6" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">إرسال الاستفسار</button>
        </form>
    </div>
</div>

<style>
/* سيتم نقل الـ CSS إلى ملف منفصل لاحقاً */
.student-dashboard-page {
    padding: 40px 0;
    background: var(--color-bg-light);
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.welcome-section {
    display: flex;
    align-items: center;
    gap: 20px;
}

.user-avatar img {
    border-radius: 50%;
    border: 3px solid var(--color-primary);
}

.welcome-title {
    font-size: 2rem;
    color: var(--color-primary-dark);
    margin-bottom: 5px;
}

.welcome-subtitle {
    color: var(--color-text-secondary);
    font-size: 1.1rem;
}

.quick-actions {
    display: flex;
    gap: 15px;
}

.stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: transform 0.3s;
}

.stat-card:hover {
    transform: translateY(-3px);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: var(--color-primary);
}

.stat-label {
    color: var(--color-text-secondary);
    font-size: 0.95rem;
}

.dashboard-content {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 30px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.section-title {
    font-size: 1.5rem;
    color: var(--color-primary-dark);
    display: flex;
    align-items: center;
    gap: 10px;
}

.courses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
}

.course-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
}

.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.course-thumbnail {
    position: relative;
    overflow: hidden;
}

.course-thumbnail img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}

.progress-overlay {
    position: absolute;
    top: 10px;
    left: 10px;
}

.circular-progress {
    position: relative;
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-weight: bold;
    font-size: 0.9rem;
}

.course-content {
    padding: 20px;
}

.course-meta {
    display: flex;
    gap: 15px;
    margin: 10px 0;
    font-size: 0.9rem;
    color: var(--color-text-secondary);
}

.progress-bar-wrapper {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 15px 0;
}

.progress-bar {
    flex: 1;
    height: 8px;
    background: #E5E7EB;
    border-radius: 10px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: var(--color-primary);
    transition: width 0.3s;
}

.sidebar-widget {
    background: white;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.widget-title {
    font-size: 1.2rem;
    color: var(--color-primary-dark);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.notifications-list {
    list-style: none;
    padding: 0;
}

.notification-item {
    display: flex;
    gap: 12px;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 10px;
    transition: background 0.2s;
}

.notification-item:hover {
    background: var(--color-bg-light);
}

.notification-item.unread {
    background: #EFF6FF;
    border-right: 3px solid var(--color-primary);
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--color-primary-light);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-primary);
    flex-shrink: 0;
}

.notification-content h4 {
    font-size: 0.95rem;
    margin-bottom: 5px;
}

.notification-content p {
    font-size: 0.85rem;
    color: var(--color-text-secondary);
    margin-bottom: 5px;
}

.notification-date {
    font-size: 0.75rem;
    color: var(--color-text-light);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 12px;
}

.empty-state i {
    font-size: 64px;
    color: var(--color-primary-light);
    margin-bottom: 20px;
}

.modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
}

.modal-content {
    background: white;
    margin: 5% auto;
    padding: 40px;
    border-radius: 12px;
    max-width: 600px;
    position: relative;
}

.modal-close {
    position: absolute;
    top: 15px;
    left: 15px;
    font-size: 28px;
    cursor: pointer;
}

@media (max-width: 1024px) {
    .dashboard-content {
        grid-template-columns: 1fr;
    }

    .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 20px;
    }
}
</style>

<script>
// Modal handling
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('contact-admin');
    const btn = document.querySelector('[data-toggle="modal"]');
    const span = document.querySelector('.modal-close');

    if (btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            modal.style.display = 'block';
        });
    }

    if (span) {
        span.addEventListener('click', function() {
            modal.style.display = 'none';
        });
    }

    window.addEventListener('click', function(e) {
        if (e.target == modal) {
            modal.style.display = 'none';
        }
    });

    // Form submission
    const form = document.getElementById('admin-inquiry-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            // AJAX submission will be implemented in main.js
            alert('سيتم إرسال استفسارك قريباً');
        });
    }
});
</script>

<?php get_footer(); ?>
