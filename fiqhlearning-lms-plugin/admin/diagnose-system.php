<?php
/**
 * صفحة التشخيص الكامل للنظام
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!current_user_can('manage_options')) {
    wp_die('ليس لديك صلاحية الوصول لهذه الصفحة');
}

global $wpdb;

echo '<div class="wrap">';
echo '<h1>🔍 تشخيص كامل للنظام</h1>';

// 1. فحص جدول التسجيلات
echo '<div style="background: white; padding: 20px; margin: 20px 0; border: 1px solid #ccc; border-radius: 5px;">';
echo '<h2>1️⃣ فحص جدول التسجيلات (fiqh_enrollments)</h2>';

$table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}fiqh_enrollments'");
if (!$table_exists) {
    echo '<p style="color: red;">❌ جدول fiqh_enrollments غير موجود!</p>';
} else {
    echo '<p style="color: green;">✅ جدول fiqh_enrollments موجود</p>';

    $total_enrollments = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_enrollments");
    echo '<p><strong>إجمالي التسجيلات:</strong> ' . $total_enrollments . '</p>';

    $active_enrollments = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_enrollments WHERE status = 'active'");
    echo '<p><strong>التسجيلات النشطة:</strong> ' . $active_enrollments . '</p>';

    // عرض آخر 10 تسجيلات
    $recent = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}fiqh_enrollments ORDER BY enrolled_at DESC LIMIT 10");
    if ($recent) {
        echo '<h3>آخر 10 تسجيلات:</h3>';
        echo '<table class="wp-list-table widefat">';
        echo '<thead><tr><th>ID</th><th>الطالب</th><th>المقرر</th><th>الحالة</th><th>التاريخ</th></tr></thead><tbody>';
        foreach ($recent as $r) {
            $user = get_userdata($r->user_id);
            $course = get_post($r->course_id);
            echo '<tr>';
            echo '<td>' . $r->id . '</td>';
            echo '<td>' . ($user ? $user->display_name : 'N/A') . '</td>';
            echo '<td>' . ($course ? $course->post_title : 'N/A') . '</td>';
            echo '<td>' . $r->status . '</td>';
            echo '<td>' . $r->enrolled_at . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    }
}
echo '</div>';

// 2. فحص الطلاب
echo '<div style="background: white; padding: 20px; margin: 20px 0; border: 1px solid #ccc; border-radius: 5px;">';
echo '<h2>2️⃣ فحص الطلاب</h2>';

$students = get_users(array('role' => 'student'));
echo '<p><strong>عدد الطلاب:</strong> ' . count($students) . '</p>';

if ($students) {
    echo '<table class="wp-list-table widefat">';
    echo '<thead><tr><th>ID</th><th>الاسم</th><th>البريد</th><th>المستوى</th><th>التسجيلات</th></tr></thead><tbody>';
    foreach ($students as $student) {
        $level = get_user_meta($student->ID, '_fiqh_student_level', true);
        $enrollments_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_enrollments WHERE user_id = %d AND status = 'active'",
            $student->ID
        ));

        echo '<tr>';
        echo '<td>' . $student->ID . '</td>';
        echo '<td><strong>' . $student->display_name . '</strong></td>';
        echo '<td>' . $student->user_email . '</td>';

        if ($level) {
            $level_name = $wpdb->get_var($wpdb->prepare(
                "SELECT name FROM {$wpdb->prefix}fiqh_batches WHERE id = %d",
                $level
            ));
            echo '<td><span style="background: #2271b1; color: white; padding: 3px 10px; border-radius: 3px;">' . $level_name . '</span></td>';
        } else {
            echo '<td><span style="background: #dc3545; color: white; padding: 3px 10px; border-radius: 3px;">لا يوجد</span></td>';
        }

        if ($enrollments_count > 0) {
            echo '<td><span style="color: green; font-weight: bold;">' . $enrollments_count . ' مقرر</span></td>';
        } else {
            echo '<td><span style="color: red; font-weight: bold;">0 مقرر ❌</span></td>';
        }
        echo '</tr>';
    }
    echo '</tbody></table>';
}
echo '</div>';

// 3. فحص المقررات
echo '<div style="background: white; padding: 20px; margin: 20px 0; border: 1px solid #ccc; border-radius: 5px;">';
echo '<h2>3️⃣ فحص المقررات</h2>';

$courses = get_posts(array(
    'post_type' => 'fiqh_course',
    'posts_per_page' => -1,
    'post_status' => 'publish'
));

echo '<p><strong>عدد المقررات المنشورة:</strong> ' . count($courses) . '</p>';

if ($courses) {
    echo '<table class="wp-list-table widefat">';
    echo '<thead><tr><th>ID</th><th>العنوان</th><th>المستويات</th><th>عدد الطلاب</th></tr></thead><tbody>';
    foreach ($courses as $course) {
        $levels = get_post_meta($course->ID, '_fiqh_course_levels', true);
        $students_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_enrollments WHERE course_id = %d AND status = 'active'",
            $course->ID
        ));

        echo '<tr>';
        echo '<td>' . $course->ID . '</td>';
        echo '<td><strong>' . $course->post_title . '</strong></td>';

        if (is_array($levels) && !empty($levels)) {
            $level_names = array();
            foreach ($levels as $level_id) {
                $name = $wpdb->get_var($wpdb->prepare(
                    "SELECT name FROM {$wpdb->prefix}fiqh_batches WHERE id = %d",
                    $level_id
                ));
                if ($name) $level_names[] = $name;
            }
            echo '<td>' . implode(', ', $level_names) . '</td>';
        } else {
            echo '<td><em>لا يوجد مستوى محدد</em></td>';
        }

        echo '<td><strong>' . $students_count . '</strong> طالب</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}
echo '</div>';

// 4. فحص المستويات
echo '<div style="background: white; padding: 20px; margin: 20px 0; border: 1px solid #ccc; border-radius: 5px;">';
echo '<h2>4️⃣ فحص المستويات</h2>';

$levels = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}fiqh_batches ORDER BY level_order ASC");

if ($levels) {
    echo '<table class="wp-list-table widefat">';
    echo '<thead><tr><th>ID</th><th>الاسم</th><th>الترتيب</th><th>الحالة</th></tr></thead><tbody>';
    foreach ($levels as $level) {
        echo '<tr>';
        echo '<td>' . $level->id . '</td>';
        echo '<td><strong>' . $level->name . '</strong></td>';
        echo '<td>' . $level->level_order . '</td>';
        echo '<td>' . $level->status . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
} else {
    echo '<p style="color: red;">❌ لا توجد مستويات!</p>';
}
echo '</div>';

// 5. اختبار طالب محدد
echo '<div style="background: #fff3cd; padding: 20px; margin: 20px 0; border: 1px solid #ffc107; border-radius: 5px;">';
echo '<h2>5️⃣ اختبار طالب محدد</h2>';

$test_email = 'noussaybabrmk@gmail.com';
$test_user = get_user_by('email', $test_email);

if ($test_user) {
    echo '<h3>اختبار الطالبة: ' . $test_user->display_name . '</h3>';

    echo '<p><strong>ID:</strong> ' . $test_user->ID . '</p>';
    echo '<p><strong>البريد:</strong> ' . $test_user->user_email . '</p>';

    $level = get_user_meta($test_user->ID, '_fiqh_student_level', true);
    if ($level) {
        $level_info = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}fiqh_batches WHERE id = %d",
            $level
        ));
        echo '<p><strong>المستوى:</strong> ' . $level_info->name . ' (ترتيب: ' . $level_info->level_order . ')</p>';
    } else {
        echo '<p style="color: red;"><strong>المستوى:</strong> ❌ لا يوجد</p>';
    }

    $enrollments = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}fiqh_enrollments WHERE user_id = %d",
        $test_user->ID
    ));

    echo '<p><strong>عدد التسجيلات:</strong> ' . count($enrollments) . '</p>';

    if ($enrollments) {
        echo '<h4>المقررات المسجلة فيها:</h4>';
        echo '<ul>';
        foreach ($enrollments as $enr) {
            $course = get_post($enr->course_id);
            echo '<li>' . ($course ? $course->post_title : 'N/A') . ' - حالة: ' . $enr->status . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p style="color: red; font-weight: bold;">❌ لا توجد تسجيلات لهذه الطالبة!</p>';
        echo '<p style="background: yellow; padding: 10px;"><strong>هذه هي المشكلة!</strong> الطالبة غير مسجلة في أي مقرر.</p>';
    }
} else {
    echo '<p style="color: red;">❌ الطالبة غير موجودة في النظام!</p>';
}

echo '</div>';

// 6. زر الإصلاح السريع
echo '<div style="background: #d4edda; padding: 20px; margin: 20px 0; border: 1px solid #c3e6cb; border-radius: 5px;">';
echo '<h2>6️⃣ إصلاح سريع</h2>';

if (isset($_POST['quick_fix']) && check_admin_referer('quick_fix_action', 'quick_fix_nonce')) {
    echo '<h3 style="color: #155724;">جاري الإصلاح...</h3>';

    $fixed_count = 0;
    $students = get_users(array('role' => 'student'));
    $all_courses = get_posts(array(
        'post_type' => 'fiqh_course',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'fields' => 'ids'
    ));

    foreach ($students as $student) {
        $student_level = get_user_meta($student->ID, '_fiqh_student_level', true);

        if (!$student_level) {
            continue;
        }

        $student_level_order = $wpdb->get_var($wpdb->prepare(
            "SELECT level_order FROM {$wpdb->prefix}fiqh_batches WHERE id = %d",
            $student_level
        ));

        if ($student_level_order === null) {
            continue;
        }

        foreach ($all_courses as $course_id) {
            $course_levels = get_post_meta($course_id, '_fiqh_course_levels', true);

            $can_enroll = false;

            if (!is_array($course_levels) || empty($course_levels)) {
                $can_enroll = true;
            } else {
                foreach ($course_levels as $course_level_id) {
                    $course_level_order = $wpdb->get_var($wpdb->prepare(
                        "SELECT level_order FROM {$wpdb->prefix}fiqh_batches WHERE id = %d",
                        $course_level_id
                    ));

                    if ($course_level_order !== null && $course_level_order <= $student_level_order) {
                        $can_enroll = true;
                        break;
                    }
                }
            }

            if ($can_enroll) {
                $exists = $wpdb->get_var($wpdb->prepare(
                    "SELECT id FROM {$wpdb->prefix}fiqh_enrollments WHERE user_id = %d AND course_id = %d",
                    $student->ID,
                    $course_id
                ));

                if (!$exists) {
                    $result = $wpdb->insert(
                        $wpdb->prefix . 'fiqh_enrollments',
                        array(
                            'user_id' => $student->ID,
                            'course_id' => $course_id,
                            'status' => 'active',
                            'enrolled_at' => current_time('mysql'),
                        ),
                        array('%d', '%d', '%s', '%s')
                    );

                    if ($result) {
                        $fixed_count++;
                    }
                }
            }
        }
    }

    echo '<p style="color: #155724; font-size: 18px; font-weight: bold;">✅ تم إصلاح ' . $fixed_count . ' تسجيل</p>';
    echo '<p><a href="' . admin_url('admin.php?page=diagnose-system') . '" class="button button-primary">تحديث الصفحة</a></p>';
} else {
    echo '<p>اضغط على الزر أدناه لتسجيل جميع الطلاب في المقررات المناسبة تلقائياً:</p>';
    echo '<form method="post">';
    wp_nonce_field('quick_fix_action', 'quick_fix_nonce');
    echo '<button type="submit" name="quick_fix" class="button button-primary button-large">⚡ إصلاح سريع - تسجيل جميع الطلاب</button>';
    echo '</form>';
}

echo '</div>';

echo '</div>';
