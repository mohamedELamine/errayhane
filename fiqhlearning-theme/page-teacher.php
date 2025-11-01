<?php
/**
 * Template Name: صفحة الأستاذ
 * Template for Teacher Profile Page
 *
 * @package FiqhLearning
 */

get_header();

// يمكن تمرير معرف المعلم عبر URL parameter أو استخدام معلم افتراضي
$teacher_id = isset($_GET['teacher_id']) ? intval($_GET['teacher_id']) : 0;

// إذا لم يتم تحديد معلم، نعرض المعلم الأول من قاعدة البيانات
if (!$teacher_id) {
    $teachers = get_users(array('role' => 'teacher', 'number' => 1));
    if (!empty($teachers)) {
        $teacher_id = $teachers[0]->ID;
    }
}

$teacher = get_userdata($teacher_id);

if (!$teacher) {
    echo '<div class="container"><p class="error-message">لم يتم العثور على الأستاذ المطلوب</p></div>';
    get_footer();
    exit;
}

// بيانات المعلم
$teacher_name = get_user_meta($teacher_id, 'teacher_full_name', true) ?: $teacher->display_name;
$teacher_bio = get_user_meta($teacher_id, 'teacher_bio', true);
$teacher_qualifications = get_user_meta($teacher_id, 'teacher_qualifications', true);
$teacher_books = get_user_meta($teacher_id, 'teacher_books', true);
$teacher_avatar = get_user_meta($teacher_id, 'teacher_avatar_url', true);
$teacher_intro_video = get_user_meta($teacher_id, 'teacher_intro_video', true); // YouTube ID
$teacher_email = get_user_meta($teacher_id, 'teacher_contact_email', true) ?: $teacher->user_email;

// المقررات التي يدرسها
$courses = get_posts(array(
    'post_type' => 'fiqh_course',
    'posts_per_page' => -1,
    'meta_query' => array(
        array(
            'key' => '_fiqh_course_teacher_id',
            'value' => $teacher_id,
        )
    )
));
?>

<main class="teacher-profile-page">
    <div class="container">

        <!-- Header Section -->
        <div class="teacher-header">
            <div class="teacher-avatar-wrapper">
                <?php if ($teacher_avatar): ?>
                    <img src="<?php echo esc_url($teacher_avatar); ?>" alt="<?php echo esc_attr($teacher_name); ?>" class="teacher-avatar">
                <?php else: ?>
                    <div class="teacher-avatar-placeholder">
                        <i class="icon-user"></i>
                    </div>
                <?php endif; ?>
            </div>

            <div class="teacher-header-info">
                <h1 class="teacher-name"><?php echo esc_html($teacher_name); ?></h1>
                <p class="teacher-title">أستاذ العلوم الشرعية - المذهب المالكي</p>

                <?php if ($teacher_email): ?>
                    <div class="teacher-contact">
                        <a href="mailto:<?php echo esc_attr($teacher_email); ?>" class="btn btn-outline-primary">
                            <i class="icon-email"></i> تواصل مع الأستاذ
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Intro Video -->
        <?php if ($teacher_intro_video): ?>
            <div class="teacher-intro-video section-card">
                <h2 class="section-title">مقطع تعريفي</h2>
                <div class="video-wrapper">
                    <iframe
                        src="https://www.youtube.com/embed/<?php echo esc_attr($teacher_intro_video); ?>"
                        frameborder="0"
                        allowfullscreen
                        title="مقطع تعريفي - <?php echo esc_attr($teacher_name); ?>">
                    </iframe>
                </div>
            </div>
        <?php endif; ?>

        <!-- Biography -->
        <?php if ($teacher_bio): ?>
            <div class="teacher-bio section-card">
                <h2 class="section-title">السيرة العلمية</h2>
                <div class="bio-content">
                    <?php echo wpautop(wp_kses_post($teacher_bio)); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Qualifications -->
        <?php if ($teacher_qualifications): ?>
            <div class="teacher-qualifications section-card">
                <h2 class="section-title">المشايخ والإجازات</h2>
                <div class="qualifications-content">
                    <?php echo wpautop(wp_kses_post($teacher_qualifications)); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Books -->
        <?php if ($teacher_books): ?>
            <div class="teacher-books section-card">
                <h2 class="section-title">المؤلفات</h2>
                <div class="books-content">
                    <?php echo wpautop(wp_kses_post($teacher_books)); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Courses Taught -->
        <?php if (!empty($courses)): ?>
            <div class="teacher-courses section-card">
                <h2 class="section-title">المقررات التي يدرسها الأستاذ</h2>
                <div class="courses-grid">
                    <?php foreach ($courses as $course):
                        $course_type = get_the_terms($course->ID, 'fiqh_course_type');
                        $course_type_name = !empty($course_type) ? $course_type[0]->name : 'عام';
                        ?>
                        <div class="course-card">
                            <?php if (has_post_thumbnail($course->ID)): ?>
                                <div class="course-thumbnail">
                                    <?php echo get_the_post_thumbnail($course->ID, 'fiqh-course-thumb'); ?>
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
                                <div class="course-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt($course->ID), 20); ?>
                                </div>
                                <a href="<?php echo get_permalink($course->ID); ?>" class="btn btn-sm btn-primary">
                                    عرض المقرر
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="teacher-stats section-card">
            <h2 class="section-title">الإحصائيات</h2>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number"><?php echo count($courses); ?></div>
                    <div class="stat-label">مقرر دراسي</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">
                        <?php
                        global $wpdb;
                        $total_lessons = 0;
                        foreach ($courses as $course) {
                            $lessons_count = $wpdb->get_var($wpdb->prepare(
                                "SELECT COUNT(*) FROM {$wpdb->prefix}posts p
                                INNER JOIN {$wpdb->prefix}postmeta pm ON p.ID = pm.post_id
                                WHERE p.post_type = 'fiqh_lesson' AND p.post_status = 'publish'
                                AND pm.meta_key = '_fiqh_lesson_course_id' AND pm.meta_value = %d",
                                $course->ID
                            ));
                            $total_lessons += intval($lessons_count);
                        }
                        echo $total_lessons;
                        ?>
                    </div>
                    <div class="stat-label">درس منشور</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">
                        <?php
                        // عدد الطلاب المسجلين في مقررات هذا المعلم
                        $enrolled_students = 0;
                        foreach ($courses as $course) {
                            $count = $wpdb->get_var($wpdb->prepare(
                                "SELECT COUNT(DISTINCT user_id) FROM {$wpdb->prefix}fiqh_enrollments
                                WHERE course_id = %d AND status = 'active'",
                                $course->ID
                            ));
                            $enrolled_students += intval($count);
                        }
                        echo $enrolled_students;
                        ?>
                    </div>
                    <div class="stat-label">طالب مسجل</div>
                </div>
            </div>
        </div>

    </div>
</main>

<style>
.teacher-profile-page {
    padding: 40px 0;
    background: var(--color-bg-light);
}

.teacher-header {
    display: flex;
    align-items: center;
    gap: 30px;
    background: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 30px;
}

.teacher-avatar-wrapper {
    flex-shrink: 0;
}

.teacher-avatar {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid var(--color-primary);
}

.teacher-avatar-placeholder {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background: var(--color-primary-light);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 80px;
    color: var(--color-primary);
}

.teacher-name {
    font-size: 2.5rem;
    color: var(--color-primary-dark);
    margin-bottom: 10px;
    font-family: 'Noto Naskh Arabic', serif;
}

.teacher-title {
    font-size: 1.2rem;
    color: var(--color-text-secondary);
    margin-bottom: 20px;
}

.section-card {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 30px;
}

.section-title {
    font-size: 1.8rem;
    color: var(--color-primary-dark);
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 3px solid var(--color-primary);
    font-family: 'Noto Naskh Arabic', serif;
}

.teacher-intro-video .video-wrapper {
    position: relative;
    padding-bottom: 56.25%;
    height: 0;
    overflow: hidden;
}

.teacher-intro-video iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 8px;
}

.bio-content, .qualifications-content, .books-content {
    line-height: 2;
    font-size: 1.1rem;
    color: var(--color-text);
}

.courses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
}

.course-card {
    background: var(--color-bg-light);
    border-radius: 10px;
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
}

.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

.course-thumbnail img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}

.course-content {
    padding: 20px;
}

.course-type {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    margin-bottom: 10px;
}

.course-title {
    font-size: 1.3rem;
    margin-bottom: 10px;
}

.course-title a {
    color: var(--color-primary-dark);
    text-decoration: none;
}

.course-title a:hover {
    color: var(--color-primary);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 30px;
    text-align: center;
}

.stat-item {
    padding: 20px;
    background: var(--color-bg-light);
    border-radius: 10px;
}

.stat-number {
    font-size: 3rem;
    font-weight: bold;
    color: var(--color-primary);
    margin-bottom: 10px;
}

.stat-label {
    font-size: 1.1rem;
    color: var(--color-text-secondary);
}

@media (max-width: 768px) {
    .teacher-header {
        flex-direction: column;
        text-align: center;
    }

    .courses-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php get_footer(); ?>
