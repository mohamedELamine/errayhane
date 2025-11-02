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

        // جلب الإعدادات من instance
        $show_courses = isset($instance['show_courses']) ? $instance['show_courses'] : true;
        $show_lessons = isset($instance['show_lessons']) ? $instance['show_lessons'] : true;
        $show_students = isset($instance['show_students']) ? $instance['show_students'] : true;
        $show_questions = isset($instance['show_questions']) ? $instance['show_questions'] : true;

        ?>
        <div class="stats-section">
            <div class="stats-grid">
                <?php if ($show_courses) : ?>
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
                <?php endif; ?>

                <?php if ($show_lessons) : ?>
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
                <?php endif; ?>

                <?php if ($show_students) : ?>
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
                <?php endif; ?>

                <?php if ($show_questions) :
                    global $wpdb;
                    $questions_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}fiqh_questions WHERE status != 'deleted'");
                ?>
                <div class="stat-item">
                    <div class="stat-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>
                    <div class="stat-number"><?php echo $questions_count; ?></div>
                    <div class="stat-label"><?php _e('سؤال', 'fiqhlearning'); ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php

        echo $args['after_widget'];
    }

    public function form($instance) {
        // الإعدادات الافتراضية
        $show_courses = isset($instance['show_courses']) ? (bool) $instance['show_courses'] : true;
        $show_lessons = isset($instance['show_lessons']) ? (bool) $instance['show_lessons'] : true;
        $show_students = isset($instance['show_students']) ? (bool) $instance['show_students'] : true;
        $show_questions = isset($instance['show_questions']) ? (bool) $instance['show_questions'] : true;
        ?>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_courses); ?> id="<?php echo $this->get_field_id('show_courses'); ?>" name="<?php echo $this->get_field_name('show_courses'); ?>" />
            <label for="<?php echo $this->get_field_id('show_courses'); ?>"><?php _e('عرض عدد المقررات', 'fiqhlearning'); ?></label>
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_lessons); ?> id="<?php echo $this->get_field_id('show_lessons'); ?>" name="<?php echo $this->get_field_name('show_lessons'); ?>" />
            <label for="<?php echo $this->get_field_id('show_lessons'); ?>"><?php _e('عرض عدد الدروس', 'fiqhlearning'); ?></label>
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_students); ?> id="<?php echo $this->get_field_id('show_students'); ?>" name="<?php echo $this->get_field_name('show_students'); ?>" />
            <label for="<?php echo $this->get_field_id('show_students'); ?>"><?php _e('عرض عدد الطلاب', 'fiqhlearning'); ?></label>
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_questions); ?> id="<?php echo $this->get_field_id('show_questions'); ?>" name="<?php echo $this->get_field_name('show_questions'); ?>" />
            <label for="<?php echo $this->get_field_id('show_questions'); ?>"><?php _e('عرض عدد الأسئلة', 'fiqhlearning'); ?></label>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['show_courses'] = (!empty($new_instance['show_courses'])) ? 1 : 0;
        $instance['show_lessons'] = (!empty($new_instance['show_lessons'])) ? 1 : 0;
        $instance['show_students'] = (!empty($new_instance['show_students'])) ? 1 : 0;
        $instance['show_questions'] = (!empty($new_instance['show_questions'])) ? 1 : 0;
        return $instance;
    }
}

/**
 * تسجيل الودجات
 */
function fiqhlearning_register_widgets() {
    register_widget('Fiqh_Stats_Widget');
}
add_action('widgets_init', 'fiqhlearning_register_widgets');
