<?php
/**
 * Custom Widgets
 *
 * @package FiqhLearning
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ودجة الإحصائيات
 */
class Fiqh_Stats_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'fiqh_stats_widget',
            __('إحصائيات FiqhLearning', 'fiqhlearning'),
            array('description' => __('عرض إحصائيات المنصة (المقررات، الدروس، الطلاب، المعلمين)', 'fiqhlearning'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];

        ?>
        <div class="stats-section">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                    </div>
                    <div class="stat-number"><?php echo wp_count_posts('fiqh_course')->publish; ?></div>
                    <div class="stat-label"><?php _e('مقرر دراسي', 'fiqhlearning'); ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                        </svg>
                    </div>
                    <div class="stat-number"><?php echo wp_count_posts('fiqh_lesson')->publish; ?></div>
                    <div class="stat-label"><?php _e('درس', 'fiqhlearning'); ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <div class="stat-number">
                        <?php echo count(get_users(array('role' => 'student'))); ?>
                    </div>
                    <div class="stat-label"><?php _e('طالب', 'fiqhlearning'); ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="stat-number"><?php echo wp_count_posts('fiqh_teacher')->publish; ?></div>
                    <div class="stat-label"><?php _e('معلم', 'fiqhlearning'); ?></div>
                </div>
            </div>
        </div>
        <?php

        echo $args['after_widget'];
    }

    public function form($instance) {
        ?>
        <p><?php _e('هذه الودجة لا تحتاج إلى إعدادات. تعرض إحصائيات المنصة تلقائياً.', 'fiqhlearning'); ?></p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        return array();
    }
}

/**
 * تسجيل الودجات
 */
function fiqhlearning_register_widgets() {
    register_widget('Fiqh_Stats_Widget');
}
add_action('widgets_init', 'fiqhlearning_register_widgets');
