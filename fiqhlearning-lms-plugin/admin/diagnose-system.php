<?php
/**
 * صفحة التشخيص الكامل للنظام
 * يمكن استدعاؤها من صفحة الإدارة أو من استيراد المحتوى التجريبي
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * تشخيص النظام
 *
 * @param bool $show_only_errors إذا كان true، تعرض الأخطاء فقط
 * @param bool $auto_run إذا كان true، تعمل تلقائياً بدون واجهة المستخدم
 * @return array نتائج التشخيص
 */
function fiqh_diagnose_system($show_only_errors = false, $auto_run = false) {
    global $wpdb;

    $errors = array();
    $warnings = array();
    $success = array();

    // 1. فحص جدول التسجيلات
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}fiqh_enrollments'");
    if (!$table_exists) {
        $errors[] = 'جدول fiqh_enrollments غير موجود!';
    } else {
        $total_enrollments = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_enrollments");
        $active_enrollments = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_enrollments WHERE status = 'active'");

        if ($total_enrollments == 0) {
            $warnings[] = 'لا توجد تسجيلات في النظام';
        } else {
            $success[] = "إجمالي التسجيلات: {$total_enrollments} ({$active_enrollments} نشط)";
        }
    }

    // 2. فحص الطلاب
    $students = get_users(array('role' => 'student'));
    if (empty($students)) {
        $errors[] = 'لا يوجد طلاب في النظام';
    } else {
        $students_without_level = 0;
        $students_without_enrollments = 0;

        foreach ($students as $student) {
            $level = get_user_meta($student->ID, '_fiqh_student_level', true);
            if (!$level) {
                $students_without_level++;
            }

            $enrollments_count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_enrollments e
                INNER JOIN {$wpdb->posts} p ON e.course_id = p.ID
                WHERE e.user_id = %d AND e.status = 'active' AND p.post_status = 'publish'",
                $student->ID
            ));

            if ($enrollments_count == 0) {
                $students_without_enrollments++;
            }
        }

        if ($students_without_level > 0) {
            $warnings[] = "{$students_without_level} طالب بدون مستوى محدد";
        }

        if ($students_without_enrollments > 0) {
            $errors[] = "{$students_without_enrollments} طالب بدون تسجيلات";
        }

        if ($students_without_level == 0 && $students_without_enrollments == 0) {
            $success[] = "جميع الطلاب (" . count($students) . ") لديهم مستويات وتسجيلات";
        }
    }

    // 3. فحص المقررات
    $courses = get_posts(array(
        'post_type' => 'fiqh_course',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));

    if (empty($courses)) {
        $errors[] = 'لا توجد مقررات منشورة';
    } else {
        $courses_without_students = 0;
        foreach ($courses as $course) {
            $students_count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_enrollments WHERE course_id = %d AND status = 'active'",
                $course->ID
            ));
            if ($students_count == 0) {
                $courses_without_students++;
            }
        }

        if ($courses_without_students > 0) {
            $warnings[] = "{$courses_without_students} مقرر بدون طلاب";
        } else {
            $success[] = "جميع المقررات (" . count($courses) . ") لديها طلاب";
        }
    }

    // 4. فحص المستويات
    $levels = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}fiqh_batches WHERE status = 'active' ORDER BY level_order");
    if (empty($levels)) {
        $errors[] = 'لا توجد مستويات نشطة';
    } else {
        $success[] = "عدد المستويات النشطة: " . count($levels);
    }

    // 5. اختبار طالب عشوائي
    if (!empty($students)) {
        $random_student = $students[array_rand($students)];
        $student_level = get_user_meta($random_student->ID, '_fiqh_student_level', true);

        $enrollments = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}fiqh_enrollments WHERE user_id = %d AND status = 'active'",
            $random_student->ID
        ));

        $valid_enrollments = 0;
        $invalid_enrollments = 0;

        foreach ($enrollments as $enr) {
            $course = get_post($enr->course_id);
            if ($course && $course->post_status === 'publish') {
                $valid_enrollments++;
            } else {
                $invalid_enrollments++;
            }
        }

        if ($invalid_enrollments > 0) {
            $errors[] = "الطالب {$random_student->display_name}: لديه {$invalid_enrollments} تسجيل لمقررات محذوفة";
        }

        if ($valid_enrollments == 0) {
            $errors[] = "الطالب {$random_student->display_name}: ليس لديه تسجيلات صحيحة";
        } else {
            $success[] = "الطالب {$random_student->display_name}: لديه {$valid_enrollments} تسجيل صحيح";
        }
    }

    // 6. فحص التسجيلات القديمة
    $old_enrollments = $wpdb->get_var("
        SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_enrollments e
        LEFT JOIN {$wpdb->posts} p ON e.course_id = p.ID
        WHERE p.ID IS NULL OR p.post_status != 'publish'
    ");

    if ($old_enrollments > 0) {
        $warnings[] = "يوجد {$old_enrollments} تسجيل لمقررات محذوفة - استخدم زر التنظيف";
    }

    return array(
        'errors' => $errors,
        'warnings' => $warnings,
        'success' => $success
    );
}

// إذا تم استدعاء الصفحة من لوحة التحكم
if (!function_exists('fiqh_diagnose_system_page')) {
    function fiqh_diagnose_system_page() {
        if (!current_user_can('manage_options')) {
            wp_die('ليس لديك صلاحية الوصول لهذه الصفحة');
        }

        global $wpdb;

        // معالجة زر التنظيف
        if (isset($_POST['clean_old_enrollments']) && check_admin_referer('clean_old_enrollments_action', 'clean_old_enrollments_nonce')) {
            $deleted_count = $wpdb->query("
                DELETE e FROM {$wpdb->prefix}fiqh_enrollments e
                LEFT JOIN {$wpdb->posts} p ON e.course_id = p.ID
                WHERE p.ID IS NULL OR p.post_status != 'publish'
            ");

            echo '<div class="notice notice-success"><p>✅ تم حذف ' . $deleted_count . ' تسجيل قديم</p></div>';
        }

        // معالجة زر الإصلاح السريع
        if (isset($_POST['quick_fix']) && check_admin_referer('quick_fix_action', 'quick_fix_nonce')) {
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

            echo '<div class="notice notice-success"><p>✅ تم إصلاح ' . $fixed_count . ' تسجيل</p></div>';
        }

        echo '<div class="wrap">';
        echo '<h1>🔍 تشخيص كامل للنظام</h1>';

        // تشغيل التشخيص
        $results = fiqh_diagnose_system(false, false);

        // عرض الأخطاء
        if (!empty($results['errors'])) {
            echo '<div class="notice notice-error" style="padding: 20px; margin: 20px 0;">';
            echo '<h2 style="margin-top: 0;">❌ أخطاء حرجة</h2>';
            echo '<ul>';
            foreach ($results['errors'] as $error) {
                echo '<li style="color: #d63638; font-weight: bold;">' . $error . '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }

        // عرض التحذيرات
        if (!empty($results['warnings'])) {
            echo '<div class="notice notice-warning" style="padding: 20px; margin: 20px 0;">';
            echo '<h2 style="margin-top: 0;">⚠️ تحذيرات</h2>';
            echo '<ul>';
            foreach ($results['warnings'] as $warning) {
                echo '<li style="color: #dba617;">' . $warning . '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }

        // عرض النجاحات
        if (!empty($results['success'])) {
            echo '<div class="notice notice-success" style="padding: 20px; margin: 20px 0;">';
            echo '<h2 style="margin-top: 0;">✅ كل شيء تمام</h2>';
            echo '<ul>';
            foreach ($results['success'] as $success_msg) {
                echo '<li style="color: #00a32a;">' . $success_msg . '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }

        // عرض الأدوات
        echo '<div style="background: white; padding: 20px; margin: 20px 0; border: 1px solid #ccc; border-radius: 5px;">';
        echo '<h2>🔧 أدوات الإصلاح</h2>';

        // زر الإصلاح السريع
        echo '<div style="margin-bottom: 20px;">';
        echo '<h3>⚡ إصلاح سريع - تسجيل الطلاب</h3>';
        echo '<p>يسجل جميع الطلاب في المقررات المناسبة حسب مستوياتهم.</p>';
        echo '<form method="post" style="display: inline;">';
        wp_nonce_field('quick_fix_action', 'quick_fix_nonce');
        echo '<button type="submit" name="quick_fix" class="button button-primary button-large">⚡ إصلاح سريع</button>';
        echo '</form>';
        echo '</div>';

        // زر التنظيف
        if (!empty($results['warnings'])) {
            echo '<div>';
            echo '<h3>🧹 تنظيف التسجيلات القديمة</h3>';
            echo '<p>يحذف جميع التسجيلات للمقررات المحذوفة أو غير المنشورة.</p>';
            echo '<form method="post" style="display: inline;" onsubmit="return confirm(\'هل أنت متأكد من حذف جميع التسجيلات القديمة؟\');">';
            wp_nonce_field('clean_old_enrollments_action', 'clean_old_enrollments_nonce');
            echo '<button type="submit" name="clean_old_enrollments" class="button button-secondary button-large">🧹 تنظيف</button>';
            echo '</form>';
            echo '</div>';
        }

        echo '</div>';
        echo '</div>';
    }
}

// استدعاء الصفحة إذا كانت في سياق الإدارة
if (is_admin() && isset($_GET['page']) && $_GET['page'] === 'diagnose-system') {
    fiqh_diagnose_system_page();
}
