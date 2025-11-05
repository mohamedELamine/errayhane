<?php
/**
 * أداة إصلاح التسجيلات - تشخيص وإصلاح مشاكل تسجيل الطلاب
 */

if (!defined('ABSPATH')) {
    exit;
}

// التحقق من الصلاحيات
if (!current_user_can('manage_options')) {
    wp_die('ليس لديك صلاحية الوصول لهذه الصفحة');
}

global $wpdb;

// معالجة طلب الإصلاح
if (isset($_POST['fix_enrollments']) && check_admin_referer('fix_enrollments_action', 'fix_enrollments_nonce')) {
    echo '<div class="wrap">';
    echo '<h1>🔧 إصلاح التسجيلات</h1>';

    // 1. جلب جميع الطلاب
    $students = get_users(array('role' => 'student'));
    echo '<h2>👥 الطلاب (' . count($students) . ')</h2>';

    $enrolled_count = 0;
    $skipped_count = 0;

    foreach ($students as $student) {
        $student_level = get_user_meta($student->ID, '_fiqh_student_level', true);

        echo '<div style="background: #f0f0f1; padding: 10px; margin: 10px 0; border-left: 4px solid #2271b1;">';
        echo '<strong>' . $student->display_name . '</strong> (' . $student->user_email . ')';

        if (!$student_level) {
            echo ' <span style="color: red;">❌ لا يوجد مستوى</span>';
            echo '</div>';
            $skipped_count++;
            continue;
        }

        // جلب اسم المستوى
        $level_name = $wpdb->get_var($wpdb->prepare(
            "SELECT name FROM {$wpdb->prefix}fiqh_batches WHERE id = %d",
            $student_level
        ));

        $level_order = $wpdb->get_var($wpdb->prepare(
            "SELECT level_order FROM {$wpdb->prefix}fiqh_batches WHERE id = %d",
            $student_level
        ));

        echo ' - <span style="color: #2271b1;">المستوى: ' . $level_name . ' (ترتيب: ' . $level_order . ')</span><br>';

        // جلب المقررات المناسبة
        $all_courses = get_posts(array(
            'post_type' => 'fiqh_course',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));

        $student_courses = 0;

        foreach ($all_courses as $course) {
            $course_levels = get_post_meta($course->ID, '_fiqh_course_levels', true);

            $can_enroll = false;

            if (!is_array($course_levels) || empty($course_levels)) {
                $can_enroll = true;
            } else {
                foreach ($course_levels as $course_level_id) {
                    $course_level_order = $wpdb->get_var($wpdb->prepare(
                        "SELECT level_order FROM {$wpdb->prefix}fiqh_batches WHERE id = %d",
                        $course_level_id
                    ));

                    if ($course_level_order !== null && $course_level_order <= $level_order) {
                        $can_enroll = true;
                        break;
                    }
                }
            }

            if ($can_enroll) {
                // التحقق من التسجيل المسبق
                $exists = $wpdb->get_var($wpdb->prepare(
                    "SELECT id FROM {$wpdb->prefix}fiqh_enrollments WHERE user_id = %d AND course_id = %d",
                    $student->ID,
                    $course->ID
                ));

                if (!$exists) {
                    $result = $wpdb->insert(
                        $wpdb->prefix . 'fiqh_enrollments',
                        array(
                            'user_id' => $student->ID,
                            'course_id' => $course->ID,
                            'status' => 'active',
                            'enrolled_at' => current_time('mysql'),
                        ),
                        array('%d', '%d', '%s', '%s')
                    );

                    if ($result) {
                        $enrolled_count++;
                        $student_courses++;
                        echo '  ✅ تسجيل في: <em>' . $course->post_title . '</em><br>';
                    } else {
                        echo '  ❌ فشل التسجيل في: <em>' . $course->post_title . '</em><br>';
                    }
                } else {
                    $student_courses++;
                }
            }
        }

        echo '<strong>المجموع: ' . $student_courses . ' مقرر</strong>';
        echo '</div>';
    }

    echo '<div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; margin: 20px 0; border-radius: 4px;">';
    echo '<h3 style="margin-top: 0;">✅ النتيجة النهائية</h3>';
    echo '<p><strong>تم تسجيل:</strong> ' . $enrolled_count . ' تسجيل جديد</p>';
    echo '<p><strong>تم تخطي:</strong> ' . $skipped_count . ' طالب (بدون مستوى)</p>';
    echo '</div>';

    echo '<a href="' . admin_url('admin.php?page=fix-enrollments') . '" class="button button-primary">العودة</a>';
    echo '</div>';

    return;
}

// صفحة التشخيص
?>
<div class="wrap">
    <h1>🔍 تشخيص وإصلاح التسجيلات</h1>

    <?php
    // جلب الإحصائيات
    $students = get_users(array('role' => 'student'));
    $courses = get_posts(array(
        'post_type' => 'fiqh_course',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));

    $total_enrollments = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_enrollments WHERE status = 'active'");

    echo '<div class="notice notice-info">';
    echo '<p><strong>📊 الإحصائيات الحالية:</strong></p>';
    echo '<ul>';
    echo '<li>👥 عدد الطلاب: <strong>' . count($students) . '</strong></li>';
    echo '<li>📚 عدد المقررات: <strong>' . count($courses) . '</strong></li>';
    echo '<li>✅ عدد التسجيلات النشطة: <strong>' . $total_enrollments . '</strong></li>';
    echo '</ul>';
    echo '</div>';

    // فحص الطلاب
    echo '<h2>👥 تفاصيل الطلاب</h2>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr>';
    echo '<th>الاسم</th>';
    echo '<th>البريد</th>';
    echo '<th>المستوى</th>';
    echo '<th>المقررات المسجل فيها</th>';
    echo '<th>الحالة</th>';
    echo '</tr></thead><tbody>';

    foreach ($students as $student) {
        $student_level = get_user_meta($student->ID, '_fiqh_student_level', true);
        $enrollment_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_enrollments WHERE user_id = %d AND status = 'active'",
            $student->ID
        ));

        echo '<tr>';
        echo '<td>' . $student->display_name . '</td>';
        echo '<td>' . $student->user_email . '</td>';

        if ($student_level) {
            $level_name = $wpdb->get_var($wpdb->prepare(
                "SELECT name FROM {$wpdb->prefix}fiqh_batches WHERE id = %d",
                $student_level
            ));
            echo '<td><span style="background: #2271b1; color: white; padding: 2px 8px; border-radius: 3px;">' . $level_name . '</span></td>';
        } else {
            echo '<td><span style="background: #dc3545; color: white; padding: 2px 8px; border-radius: 3px;">لا يوجد</span></td>';
        }

        echo '<td><strong>' . $enrollment_count . '</strong> مقرر</td>';

        if ($enrollment_count > 0) {
            echo '<td><span style="color: green;">✅ مسجل</span></td>';
        } else {
            echo '<td><span style="color: red;">❌ غير مسجل</span></td>';
        }

        echo '</tr>';
    }

    echo '</tbody></table>';
    ?>

    <div style="margin-top: 30px; padding: 20px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px;">
        <h3>⚠️ هل تريد إصلاح التسجيلات؟</h3>
        <p>هذه الأداة ستقوم بـ:</p>
        <ul>
            <li>✅ فحص جميع الطلاب</li>
            <li>✅ تسجيل كل طالب في المقررات المناسبة لمستواه</li>
            <li>✅ تخطي التسجيلات الموجودة مسبقاً</li>
        </ul>

        <form method="post">
            <?php wp_nonce_field('fix_enrollments_action', 'fix_enrollments_nonce'); ?>
            <button type="submit" name="fix_enrollments" class="button button-primary button-large"
                    onclick="return confirm('هل أنت متأكد من تشغيل أداة الإصلاح؟')">
                🔧 إصلاح التسجيلات الآن
            </button>
        </form>
    </div>
</div>
