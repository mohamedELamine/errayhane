<?php
/**
 * تسجيل التصنيفات المخصصة (Taxonomies)
 *
 * @package FiqhLearning_LMS
 */

if (!defined('ABSPATH')) {
    exit;
}

class FiqhLearning_Taxonomies {

    /**
     * تسجيل جميع Taxonomies
     */
    public static function register_taxonomies() {
        self::register_course_category();
        self::register_course_year();
        self::register_course_semester();
        self::register_course_type();
    }

    /**
     * تصنيف المواد
     */
    private static function register_course_category() {
        $labels = array(
            'name' => __('المواد', 'fiqh-lms'),
            'singular_name' => __('مادة', 'fiqh-lms'),
            'menu_name' => __('المواد', 'fiqh-lms'),
            'all_items' => __('جميع المواد', 'fiqh-lms'),
            'edit_item' => __('تعديل المادة', 'fiqh-lms'),
            'view_item' => __('عرض المادة', 'fiqh-lms'),
            'update_item' => __('تحديث المادة', 'fiqh-lms'),
            'add_new_item' => __('إضافة مادة جديدة', 'fiqh-lms'),
            'new_item_name' => __('اسم المادة الجديدة', 'fiqh-lms'),
            'search_items' => __('بحث في المواد', 'fiqh-lms'),
        );

        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => false,
            'rewrite' => array('slug' => 'course-category'),
            'show_in_rest' => true,
        );

        register_taxonomy('fiqh_course_category', array('fiqh_course', 'fiqh_lesson'), $args);
    }

    /**
     * تصنيف السنوات الدراسية
     */
    private static function register_course_year() {
        $labels = array(
            'name' => __('السنوات الدراسية', 'fiqh-lms'),
            'singular_name' => __('سنة دراسية', 'fiqh-lms'),
            'menu_name' => __('السنوات', 'fiqh-lms'),
            'all_items' => __('جميع السنوات', 'fiqh-lms'),
            'edit_item' => __('تعديل السنة', 'fiqh-lms'),
            'update_item' => __('تحديث السنة', 'fiqh-lms'),
            'add_new_item' => __('إضافة سنة جديدة', 'fiqh-lms'),
            'new_item_name' => __('اسم السنة الجديدة', 'fiqh-lms'),
        );

        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => false,
            'show_tagcloud' => false,
            'rewrite' => array('slug' => 'year'),
            'show_in_rest' => true,
        );

        register_taxonomy('fiqh_course_year', array('fiqh_course'), $args);
    }

    /**
     * تصنيف الفصول الدراسية
     */
    private static function register_course_semester() {
        $labels = array(
            'name' => __('الفصول الدراسية', 'fiqh-lms'),
            'singular_name' => __('فصل دراسي', 'fiqh-lms'),
            'menu_name' => __('الفصول', 'fiqh-lms'),
            'all_items' => __('جميع الفصول', 'fiqh-lms'),
            'edit_item' => __('تعديل الفصل', 'fiqh-lms'),
            'update_item' => __('تحديث الفصل', 'fiqh-lms'),
            'add_new_item' => __('إضافة فصل جديد', 'fiqh-lms'),
            'new_item_name' => __('اسم الفصل الجديد', 'fiqh-lms'),
        );

        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => false,
            'show_tagcloud' => false,
            'rewrite' => array('slug' => 'semester'),
            'show_in_rest' => true,
        );

        register_taxonomy('fiqh_course_semester', array('fiqh_course'), $args);
    }

    /**
     * تصنيف أنواع المقررات
     */
    private static function register_course_type() {
        $labels = array(
            'name' => __('أنواع المقررات', 'fiqh-lms'),
            'singular_name' => __('نوع المقرر', 'fiqh-lms'),
            'menu_name' => __('الأنواع', 'fiqh-lms'),
            'all_items' => __('جميع الأنواع', 'fiqh-lms'),
            'edit_item' => __('تعديل النوع', 'fiqh-lms'),
            'update_item' => __('تحديث النوع', 'fiqh-lms'),
            'add_new_item' => __('إضافة نوع جديد', 'fiqh-lms'),
            'new_item_name' => __('اسم النوع الجديد', 'fiqh-lms'),
        );

        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => false,
            'show_tagcloud' => false,
            'rewrite' => array('slug' => 'course-type'),
            'show_in_rest' => true,
        );

        register_taxonomy('fiqh_course_type', array('fiqh_course'), $args);
    }
}
