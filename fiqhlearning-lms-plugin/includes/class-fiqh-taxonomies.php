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
        self::register_course_science();
        self::register_course_semester();
        self::register_course_type();
    }

    /**
     * تصنيف العلوم
     */
    private static function register_course_science() {
        $labels = array(
            'name' => __('العلوم', 'fiqh-lms'),
            'singular_name' => __('علم', 'fiqh-lms'),
            'menu_name' => __('العلوم', 'fiqh-lms'),
            'all_items' => __('جميع العلوم', 'fiqh-lms'),
            'edit_item' => __('تعديل العلم', 'fiqh-lms'),
            'view_item' => __('عرض العلم', 'fiqh-lms'),
            'update_item' => __('تحديث العلم', 'fiqh-lms'),
            'add_new_item' => __('إضافة علم جديد', 'fiqh-lms'),
            'new_item_name' => __('اسم العلم الجديد', 'fiqh-lms'),
            'search_items' => __('بحث في العلوم', 'fiqh-lms'),
        );

        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => false,
            'rewrite' => array('slug' => 'science'),
            'show_in_rest' => true,
        );

        register_taxonomy('fiqh_course_science', array('fiqh_course'), $args);
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
            'view_item' => __('عرض الفصل', 'fiqh-lms'),
            'update_item' => __('تحديث الفصل', 'fiqh-lms'),
            'add_new_item' => __('إضافة فصل جديد', 'fiqh-lms'),
            'new_item_name' => __('اسم الفصل الجديد', 'fiqh-lms'),
            'search_items' => __('بحث في الفصول', 'fiqh-lms'),
        );

        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
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
            'singular_name' => __('نوع', 'fiqh-lms'),
            'menu_name' => __('الأنواع', 'fiqh-lms'),
            'all_items' => __('جميع الأنواع', 'fiqh-lms'),
            'edit_item' => __('تعديل النوع', 'fiqh-lms'),
            'view_item' => __('عرض النوع', 'fiqh-lms'),
            'update_item' => __('تحديث النوع', 'fiqh-lms'),
            'add_new_item' => __('إضافة نوع جديد', 'fiqh-lms'),
            'new_item_name' => __('اسم النوع الجديد', 'fiqh-lms'),
            'search_items' => __('بحث في الأنواع', 'fiqh-lms'),
        );

        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => false,
            'show_tagcloud' => false,
            'rewrite' => array('slug' => 'type'),
            'show_in_rest' => true,
        );

        register_taxonomy('fiqh_course_type', array('fiqh_course'), $args);
    }
}
