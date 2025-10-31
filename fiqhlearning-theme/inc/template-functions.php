<?php
/**
 * دوال مساعدة للقوالب
 *
 * @package FiqhLearning
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * عرض Breadcrumb
 */
function fiqh_breadcrumb() {
    if (is_front_page()) {
        return;
    }

    echo '<nav class="breadcrumb" aria-label="breadcrumb">';
    echo '<a href="' . home_url('/') . '">' . __('الرئيسية', 'fiqhlearning') . '</a>';

    if (is_single()) {
        $post_type = get_post_type();
        if ($post_type === 'fiqh_course') {
            echo ' / <a href="' . get_post_type_archive_link('fiqh_course') . '">' . __('المقررات', 'fiqhlearning') . '</a>';
        } elseif ($post_type === 'fiqh_lesson') {
            echo ' / <a href="' . get_post_type_archive_link('fiqh_course') . '">' . __('المقررات', 'fiqhlearning') . '</a>';
            $course_id = get_post_meta(get_the_ID(), '_fiqh_lesson_course_id', true);
            if ($course_id) {
                echo ' / <a href="' . get_permalink($course_id) . '">' . get_the_title($course_id) . '</a>';
            }
        }
        echo ' / <span>' . get_the_title() . '</span>';
    } elseif (is_page()) {
        echo ' / <span>' . get_the_title() . '</span>';
    } elseif (is_category()) {
        echo ' / <span>' . single_cat_title('', false) . '</span>';
    } elseif (is_archive()) {
        echo ' / <span>' . post_type_archive_title('', false) . '</span>';
    }

    echo '</nav>';
}

/**
 * عرض عداد الإنجاز
 */
function fiqh_display_progress_bar($percentage, $label = '') {
    $percentage = max(0, min(100, intval($percentage)));
    ?>
    <div class="progress-bar-wrapper">
        <?php if ($label) : ?>
            <div class="progress-label">
                <span><?php echo esc_html($label); ?></span>
                <span><?php echo esc_html($percentage); ?>%</span>
            </div>
        <?php endif; ?>
        <div class="progress-bar">
            <div class="progress-fill" style="width: <?php echo esc_attr($percentage); ?>%"></div>
        </div>
    </div>
    <?php
}

/**
 * عرض أيقونة نوع المقرر
 */
function fiqh_course_type_icon($type) {
    $icons = array(
        'أساسي' => 'star',
        'تكميلي' => 'book',
        'إثرائي' => 'lightbulb',
    );

    $icon = isset($icons[$type]) ? $icons[$type] : 'book';

    return '<svg class="course-type-icon"><use xlink:href="#icon-' . $icon . '"></use></svg>';
}

/**
 * عرض مدة الفيديو بصيغة قابلة للقراءة
 */
function fiqh_format_duration($duration_string) {
    // يفترض أن duration_string مثل "45 دقيقة" أو "1 ساعة"
    return $duration_string;
}

/**
 * التحقق من أن المستخدم يمكنه الوصول للمحتوى
 */
function fiqh_user_can_access($post_id, $user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    if (!$user_id) {
        return false;
    }

    // المدراء والمعلمون لديهم وصول كامل
    $user = get_userdata($user_id);
    if (in_array('administrator', $user->roles) || in_array('teacher', $user->roles)) {
        return true;
    }

    // التحقق من التسجيل في المقرر
    $post_type = get_post_type($post_id);

    if ($post_type === 'fiqh_lesson') {
        $course_id = get_post_meta($post_id, '_fiqh_lesson_course_id', true);
        return FiqhLearning_Enrollments::is_user_enrolled($user_id, $course_id);
    } elseif ($post_type === 'fiqh_course') {
        return FiqhLearning_Enrollments::is_user_enrolled($user_id, $post_id);
    }

    return true;
}

/**
 * عرض رسالة "غير مخول"
 */
function fiqh_unauthorized_message() {
    ?>
    <div class="unauthorized-message">
        <div class="unauthorized-icon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </div>
        <h2><?php _e('غير مصرح بالوصول', 'fiqhlearning'); ?></h2>
        <p><?php _e('عذراً، لا يمكنك الوصول إلى هذا المحتوى. يرجى التواصل مع الإدارة.', 'fiqhlearning'); ?></p>
        <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
            <?php _e('العودة للرئيسية', 'fiqhlearning'); ?>
        </a>
    </div>
    <?php
}
