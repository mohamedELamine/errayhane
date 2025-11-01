<?php
/**
 * Template Name: الاختبارات والتقارير
 * Tests & Reports Page
 *
 * @package FiqhLearning
 */

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

$current_user = wp_get_current_user();
$user_id = $current_user->ID;

get_header();

global $wpdb;

// جلب محاولات الاختبارات للطالب
$quiz_attempts = $wpdb->get_results($wpdb->prepare(
    "SELECT qa.*, q.title as quiz_title, q.lesson_id
    FROM {$wpdb->prefix}fiqh_quiz_attempts qa
    INNER JOIN {$wpdb->prefix}fiqh_quizzes q ON qa.quiz_id = q.id
    WHERE qa.user_id = %d
    ORDER BY qa.started_at DESC",
    $user_id
), ARRAY_A);

// جلب التقارير/الشكاوى
$reports = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}fiqh_reports
    WHERE user_id = %d
    ORDER BY created_at DESC",
    $user_id
), ARRAY_A);

// إحصائيات
$total_attempts = count($quiz_attempts);
$passed_attempts = count(array_filter($quiz_attempts, function($a) { return $a['passed'] == 1; }));
$average_score = $total_attempts > 0 ? round(array_sum(array_column($quiz_attempts, 'score')) / $total_attempts, 2) : 0;
?>

<main class="tests-reports-page">
    <div class="container">

        <header class="page-header">
            <h1 class="page-title">الاختبارات والتقارير</h1>
            <p class="page-description">تابع أداءك الأكاديمي وسجل اختباراتك</p>
        </header>

        <!-- إحصائيات -->
        <div class="stats-section">
            <div class="stat-box">
                <div class="stat-icon"><i class="icon-file"></i></div>
                <div class="stat-value"><?php echo $total_attempts; ?></div>
                <div class="stat-label">اختبار مكتمل</div>
            </div>
            <div class="stat-box">
                <div class="stat-icon success"><i class="icon-checkmark"></i></div>
                <div class="stat-value"><?php echo $passed_attempts; ?></div>
                <div class="stat-label">اختبار ناجح</div>
            </div>
            <div class="stat-box">
                <div class="stat-icon warning"><i class="icon-chart"></i></div>
                <div class="stat-value"><?php echo $average_score; ?>%</div>
                <div class="stat-label">المعدل العام</div>
            </div>
        </div>

        <!-- سجل الاختبارات -->
        <section class="section-card">
            <h2 class="section-title">سجل الاختبارات</h2>

            <?php if (!empty($quiz_attempts)): ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>الاختبار</th>
                                <th>التاريخ</th>
                                <th>الدرجة</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($quiz_attempts as $attempt): ?>
                                <tr>
                                    <td><?php echo esc_html($attempt['quiz_title']); ?></td>
                                    <td><?php echo fiqh_format_date($attempt['started_at']); ?></td>
                                    <td>
                                        <strong><?php echo $attempt['score']; ?></strong> / <?php echo $attempt['max_score']; ?>
                                    </td>
                                    <td>
                                        <?php if ($attempt['passed']): ?>
                                            <span class="badge badge-success">ناجح</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">راسب</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-primary">عرض التفاصيل</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="icon-file-empty"></i>
                    <p>لم تجرِ أي اختبار بعد</p>
                </div>
            <?php endif; ?>
        </section>

        <!-- التقارير والشكاوى -->
        <section class="section-card">
            <div class="section-header">
                <h2 class="section-title">الشكاوى والتقارير</h2>
                <button class="btn btn-primary" data-toggle="modal" data-target="#new-report-modal">
                    <i class="icon-plus"></i> إضافة تقرير جديد
                </button>
            </div>

            <?php if (!empty($reports)): ?>
                <div class="reports-list">
                    <?php foreach ($reports as $report): ?>
                        <div class="report-item">
                            <div class="report-header">
                                <h3 class="report-subject"><?php echo esc_html($report['subject']); ?></h3>
                                <span class="report-status status-<?php echo esc_attr($report['status']); ?>">
                                    <?php
                                    $status_labels = ['pending' => 'قيد المراجعة', 'resolved' => 'تم الحل', 'closed' => 'مغلق'];
                                    echo $status_labels[$report['status']] ?? 'غير معروف';
                                    ?>
                                </span>
                            </div>
                            <p class="report-message"><?php echo nl2br(esc_html($report['message'])); ?></p>
                            <?php if ($report['admin_response']): ?>
                                <div class="admin-response">
                                    <strong>رد الإدارة:</strong>
                                    <p><?php echo nl2br(esc_html($report['admin_response'])); ?></p>
                                </div>
                            <?php endif; ?>
                            <div class="report-meta">
                                <span><i class="icon-calendar"></i> <?php echo fiqh_format_date($report['created_at']); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="icon-document"></i>
                    <p>لا توجد تقارير أو شكاوى</p>
                </div>
            <?php endif; ?>
        </section>

    </div>
</main>

<!-- Modal: إضافة تقرير جديد -->
<div id="new-report-modal" class="modal">
    <div class="modal-content">
        <span class="modal-close">&times;</span>
        <h2>إضافة تقرير جديد</h2>
        <form id="new-report-form">
            <div class="form-group">
                <label for="report-subject">الموضوع</label>
                <input type="text" id="report-subject" name="subject" required>
            </div>
            <div class="form-group">
                <label for="report-message">الرسالة</label>
                <textarea id="report-message" name="message" rows="6" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">إرسال التقرير</button>
        </form>
    </div>
</div>

<style>
.tests-reports-page {
    padding: 40px 0;
    background: var(--color-bg-light);
}

.page-header {
    text-align: center;
    margin-bottom: 40px;
}

.page-title {
    font-size: 2.5rem;
    color: var(--color-primary-dark);
    margin-bottom: 10px;
}

.page-description {
    font-size: 1.1rem;
    color: var(--color-text-secondary);
}

.stats-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

.stat-box {
    background: white;
    padding: 30px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.stat-icon {
    width: 60px;
    height: 60px;
    background: var(--color-primary);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    margin-bottom: 15px;
}

.stat-icon.success { background: #10B981; }
.stat-icon.warning { background: #F59E0B; }

.stat-value {
    font-size: 2.5rem;
    font-weight: bold;
    color: var(--color-primary);
    margin-bottom: 5px;
}

.stat-label {
    color: var(--color-text-secondary);
}

.section-card {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 30px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.section-title {
    font-size: 1.8rem;
    color: var(--color-primary-dark);
}

.table-responsive {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table thead {
    background: var(--color-bg-light);
}

.data-table th, .data-table td {
    padding: 15px;
    text-align: right;
    border-bottom: 1px solid #E5E7EB;
}

.reports-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.report-item {
    padding: 20px;
    background: var(--color-bg-light);
    border-radius: 8px;
    border-right: 4px solid var(--color-primary);
}

.report-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.report-subject {
    font-size: 1.2rem;
    color: var(--color-primary-dark);
}

.admin-response {
    background: #EFF6FF;
    padding: 15px;
    border-radius: 8px;
    margin-top: 15px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
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

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
}

.form-group input, .form-group textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #D1D5DB;
    border-radius: 8px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('new-report-modal');
    const btn = document.querySelector('[data-target="#new-report-modal"]');
    const span = document.querySelector('.modal-close');

    if (btn) {
        btn.addEventListener('click', () => modal.style.display = 'block');
    }

    if (span) {
        span.addEventListener('click', () => modal.style.display = 'none');
    }

    window.addEventListener('click', (e) => {
        if (e.target == modal) modal.style.display = 'none';
    });
});
</script>

<?php get_footer(); ?>
