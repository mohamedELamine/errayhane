<?php
/**
 * تسجيل أنواع المحتوى المخصصة (Custom Post Types)
 *
 * @package FiqhLearning_LMS
 */

if (!defined('ABSPATH')) {
    exit;
}

class FiqhLearning_Post_Types {

    /**
     * تسجيل جميع Custom Post Types
     */
    public static function register_post_types() {
        self::register_course_post_type();
        self::register_lesson_post_type();
    }

    /**
     * تسجيل نوع محتوى المقرر (Course)
     */
    private static function register_course_post_type() {
        $labels = array(
            'name' => __('المقررات', 'fiqh-lms'),
            'singular_name' => __('مقرر', 'fiqh-lms'),
            'menu_name' => __('المقررات', 'fiqh-lms'),
            'name_admin_bar' => __('مقرر', 'fiqh-lms'),
            'add_new' => __('إضافة جديد', 'fiqh-lms'),
            'add_new_item' => __('إضافة مقرر جديد', 'fiqh-lms'),
            'new_item' => __('مقرر جديد', 'fiqh-lms'),
            'edit_item' => __('تعديل المقرر', 'fiqh-lms'),
            'view_item' => __('عرض المقرر', 'fiqh-lms'),
            'all_items' => __('جميع المقررات', 'fiqh-lms'),
            'search_items' => __('بحث في المقررات', 'fiqh-lms'),
            'parent_item_colon' => __('المقرر الأب:', 'fiqh-lms'),
            'not_found' => __('لم يتم العثور على مقررات', 'fiqh-lms'),
            'not_found_in_trash' => __('لم يتم العثور على مقررات في سلة المهملات', 'fiqh-lms'),
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'courses', 'with_front' => false),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-welcome-learn-more',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'show_in_rest' => true,
        );

        register_post_type('fiqh_course', $args);
    }

    /**
     * تسجيل نوع محتوى الدرس (Lesson)
     */
    private static function register_lesson_post_type() {
        $labels = array(
            'name' => __('الدروس', 'fiqh-lms'),
            'singular_name' => __('درس', 'fiqh-lms'),
            'menu_name' => __('الدروس', 'fiqh-lms'),
            'name_admin_bar' => __('درس', 'fiqh-lms'),
            'add_new' => __('إضافة جديد', 'fiqh-lms'),
            'add_new_item' => __('إضافة درس جديد', 'fiqh-lms'),
            'new_item' => __('درس جديد', 'fiqh-lms'),
            'edit_item' => __('تعديل الدرس', 'fiqh-lms'),
            'view_item' => __('عرض الدرس', 'fiqh-lms'),
            'all_items' => __('جميع الدروس', 'fiqh-lms'),
            'search_items' => __('بحث في الدروس', 'fiqh-lms'),
            'not_found' => __('لم يتم العثور على دروس', 'fiqh-lms'),
            'not_found_in_trash' => __('لم يتم العثور على دروس في سلة المهملات', 'fiqh-lms'),
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => 'edit.php?post_type=fiqh_course',
            'query_var' => true,
            'rewrite' => array('slug' => 'lessons', 'with_front' => false),
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
            'show_in_rest' => true,
        );

        register_post_type('fiqh_lesson', $args);
    }

}
