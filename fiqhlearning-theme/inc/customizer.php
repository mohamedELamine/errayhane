<?php
/**
 * إعدادات المخصص (Customizer)
 *
 * @package FiqhLearning
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * إضافة إعدادات المخصص
 */
function fiqhlearning_customize_register($wp_customize) {
    // قسم الإعدادات العامة
    $wp_customize->add_section('fiqh_general_settings', array(
        'title' => __('إعدادات عامة', 'fiqhlearning'),
        'priority' => 30,
    ));

    // تفعيل الشريط العلوي
    $wp_customize->add_setting('fiqh_top_bar_enabled', array(
        'default' => false,
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control('fiqh_top_bar_enabled', array(
        'label' => __('تفعيل الشريط العلوي', 'fiqhlearning'),
        'section' => 'fiqh_general_settings',
        'type' => 'checkbox',
    ));

    // نص الشريط العلوي
    $wp_customize->add_setting('fiqh_top_bar_text', array(
        'default' => 'مرحباً بكم في منصة تعلم الفقه المالكي',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('fiqh_top_bar_text', array(
        'label' => __('نص الشريط العلوي', 'fiqhlearning'),
        'section' => 'fiqh_general_settings',
        'type' => 'text',
    ));

    // قسم روابط التواصل الاجتماعي
    $wp_customize->add_section('fiqh_social_links', array(
        'title' => __('روابط التواصل الاجتماعي', 'fiqhlearning'),
        'priority' => 35,
    ));

    // رابط تليجرام
    $wp_customize->add_setting('fiqh_telegram_link', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control('fiqh_telegram_link', array(
        'label' => __('رابط قناة التليجرام', 'fiqhlearning'),
        'section' => 'fiqh_social_links',
        'type' => 'url',
    ));

    // رابط واتساب
    $wp_customize->add_setting('fiqh_whatsapp_link', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control('fiqh_whatsapp_link', array(
        'label' => __('رابط واتساب', 'fiqhlearning'),
        'section' => 'fiqh_social_links',
        'type' => 'url',
    ));

    // رابط فيسبوك
    $wp_customize->add_setting('fiqh_facebook_link', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control('fiqh_facebook_link', array(
        'label' => __('رابط فيسبوك', 'fiqhlearning'),
        'section' => 'fiqh_social_links',
        'type' => 'url',
    ));

    // رابط تويتر
    $wp_customize->add_setting('fiqh_twitter_link', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control('fiqh_twitter_link', array(
        'label' => __('رابط تويتر', 'fiqhlearning'),
        'section' => 'fiqh_social_links',
        'type' => 'url',
    ));

    // ==========================================================================
    // قسم الصفحة الرئيسية (Homepage)
    // ==========================================================================

    $wp_customize->add_section('fiqh_homepage_section', array(
        'title'       => __('الصفحة الرئيسية', 'fiqhlearning'),
        'description' => __('تخصيص محتوى الصفحة الرئيسية', 'fiqhlearning'),
        'priority'    => 40,
    ));

    // عنوان Hero الرئيسي
    $wp_customize->add_setting('hero_title', array(
        'default'           => 'مدرسة الريحان للعلوم الشرعية',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('hero_title', array(
        'label'       => __('العنوان الرئيسي', 'fiqhlearning'),
        'description' => __('العنوان الكبير في أعلى الصفحة الرئيسية', 'fiqhlearning'),
        'section'     => 'fiqh_homepage_section',
        'type'        => 'text',
    ));

    // وصف Hero
    $wp_customize->add_setting('hero_description', array(
        'default'           => 'منصة تعليمية متخصصة في الفقه المالكي والعلوم الشرعية - تعلم على يد نخبة من المشايخ والأساتذة المتخصصين',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('hero_description', array(
        'label'       => __('الوصف', 'fiqhlearning'),
        'description' => __('النص التوضيحي تحت العنوان الرئيسي', 'fiqhlearning'),
        'section'     => 'fiqh_homepage_section',
        'type'        => 'textarea',
    ));

    // ==========================================================================
    // قسم صفحة عن المعهد (About Page)
    // ==========================================================================

    $wp_customize->add_section('fiqh_about_section', array(
        'title'       => __('صفحة عن المعهد', 'fiqhlearning'),
        'description' => __('تخصيص محتوى صفحة عن المعهد', 'fiqhlearning'),
        'priority'    => 41,
    ));

    // عنوان الصفحة
    $wp_customize->add_setting('about_page_title', array(
        'default'           => 'مدرسة الريحان للعلوم الشرعية',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('about_page_title', array(
        'label'       => __('عنوان الصفحة', 'fiqhlearning'),
        'section'     => 'fiqh_about_section',
        'type'        => 'text',
    ));

    // وصف الصفحة
    $wp_customize->add_setting('about_page_description', array(
        'default'           => 'منصة تعليمية متخصصة في تعليم الفقه المالكي والعلوم الشرعية منذ تأسيسها',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('about_page_description', array(
        'label'       => __('وصف الصفحة', 'fiqhlearning'),
        'section'     => 'fiqh_about_section',
        'type'        => 'textarea',
    ));

    // صورة المقدمة
    $wp_customize->add_setting('about_intro_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'about_intro_image', array(
        'label'       => __('صورة المقدمة', 'fiqhlearning'),
        'description' => __('صورة توضيحية في قسم المقدمة', 'fiqhlearning'),
        'section'     => 'fiqh_about_section',
    )));

    // عنوان المقدمة
    $wp_customize->add_setting('about_intro_title', array(
        'default'           => 'نبذة عن المدرسة',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('about_intro_title', array(
        'label'       => __('عنوان المقدمة', 'fiqhlearning'),
        'section'     => 'fiqh_about_section',
        'type'        => 'text',
    ));

    // نص المقدمة
    $wp_customize->add_setting('about_intro_content', array(
        'default'           => 'مدرسة الريحان للعلوم الشرعية هي منصة تعليمية رائدة في مجال تعليم الفقه المالكي والعلوم الشرعية. نسعى لتقديم تعليم شرعي متميز يجمع بين الأصالة والمعاصرة، على يد نخبة من المشايخ والأساتذة المتخصصين.',
        'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('about_intro_content', array(
        'label'       => __('نص المقدمة', 'fiqhlearning'),
        'description' => __('يمكنك استخدام HTML البسيط', 'fiqhlearning'),
        'section'     => 'fiqh_about_section',
        'type'        => 'textarea',
    ));

    // الرؤية
    $wp_customize->add_setting('about_vision_title', array(
        'default'           => 'رؤيتنا',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('about_vision_title', array(
        'label'       => __('عنوان الرؤية', 'fiqhlearning'),
        'section'     => 'fiqh_about_section',
        'type'        => 'text',
    ));

    $wp_customize->add_setting('about_vision', array(
        'default'           => 'أن نكون المرجع الأول في تعليم الفقه المالكي والعلوم الشرعية عبر الإنترنت في العالم العربي والإسلامي.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('about_vision', array(
        'label'       => __('نص الرؤية', 'fiqhlearning'),
        'section'     => 'fiqh_about_section',
        'type'        => 'textarea',
    ));

    // الرسالة
    $wp_customize->add_setting('about_mission_title', array(
        'default'           => 'رسالتنا',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('about_mission_title', array(
        'label'       => __('عنوان الرسالة', 'fiqhlearning'),
        'section'     => 'fiqh_about_section',
        'type'        => 'text',
    ));

    $wp_customize->add_setting('about_mission', array(
        'default'           => 'تقديم تعليم شرعي متميز ومتكامل يجمع بين الأصالة والمعاصرة، ونشر العلم الشرعي الصحيح بطريقة ميسرة وواضحة.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('about_mission', array(
        'label'       => __('نص الرسالة', 'fiqhlearning'),
        'section'     => 'fiqh_about_section',
        'type'        => 'textarea',
    ));

    // القيم
    $wp_customize->add_setting('about_values_title', array(
        'default'           => 'قيمنا',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('about_values_title', array(
        'label'       => __('عنوان القيم', 'fiqhlearning'),
        'section'     => 'fiqh_about_section',
        'type'        => 'text',
    ));

    $wp_customize->add_setting('about_values', array(
        'default'           => 'الأصالة في المنهج، الجودة في التعليم، التيسير على المتعلمين، الالتزام بالمنهج المالكي، التطوير المستمر.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('about_values', array(
        'label'       => __('نص القيم', 'fiqhlearning'),
        'description' => __('يمكن الفصل بينها بفاصلة', 'fiqhlearning'),
        'section'     => 'fiqh_about_section',
        'type'        => 'textarea',
    ));

    // ==========================================================================
    // قسم صفحة خطة الدراسة (Study Plan)
    // ==========================================================================

    $wp_customize->add_section('fiqh_study_plan_section', array(
        'title'       => __('صفحة خطة الدراسة', 'fiqhlearning'),
        'description' => __('تخصيص محتوى صفحة خطة الدراسة', 'fiqhlearning'),
        'priority'    => 42,
    ));

    // عنوان الصفحة
    $wp_customize->add_setting('study_plan_title', array(
        'default'           => 'خطة الدراسة',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('study_plan_title', array(
        'label'       => __('عنوان الصفحة', 'fiqhlearning'),
        'section'     => 'fiqh_study_plan_section',
        'type'        => 'text',
    ));

    // وصف الصفحة
    $wp_customize->add_setting('study_plan_description', array(
        'default'           => 'منهج دراسي متكامل للعلوم الشرعية على مدار عدة مستويات',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('study_plan_description', array(
        'label'       => __('وصف الصفحة', 'fiqhlearning'),
        'section'     => 'fiqh_study_plan_section',
        'type'        => 'textarea',
    ));

    // نص المقدمة
    $wp_customize->add_setting('study_plan_intro', array(
        'default'           => 'تم تقسيم الخطة الدراسية إلى عدة مستويات متدرجة، بحيث يبدأ الطالب من المستوى الأول ويتقدم تدريجياً نحو المستويات المتقدمة. كل مستوى يحتوي على مجموعة من المقررات الدراسية المتخصصة.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('study_plan_intro', array(
        'label'       => __('نص المقدمة', 'fiqhlearning'),
        'description' => __('نص توضيحي عن نظام المستويات', 'fiqhlearning'),
        'section'     => 'fiqh_study_plan_section',
        'type'        => 'textarea',
    ));

    // ==========================================================================
    // قسم صفحة دليل اللوائح (Regulations)
    // ==========================================================================

    $wp_customize->add_section('fiqh_regulations_section', array(
        'title'       => __('صفحة دليل اللوائح', 'fiqhlearning'),
        'description' => __('تخصيص محتوى صفحة دليل اللوائح والأنظمة', 'fiqhlearning'),
        'priority'    => 43,
    ));

    // عنوان الصفحة
    $wp_customize->add_setting('regulations_title', array(
        'default'           => 'دليل اللوائح والأنظمة',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('regulations_title', array(
        'label'       => __('عنوان الصفحة', 'fiqhlearning'),
        'section'     => 'fiqh_regulations_section',
        'type'        => 'text',
    ));

    // وصف الصفحة
    $wp_customize->add_setting('regulations_description', array(
        'default'           => 'اللوائح والأنظمة المنظمة للدراسة في مدرسة الريحان للعلوم الشرعية',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('regulations_description', array(
        'label'       => __('وصف الصفحة', 'fiqhlearning'),
        'section'     => 'fiqh_regulations_section',
        'type'        => 'textarea',
    ));

    // ==========================================================================
    // قسم صفحة الأسئلة (Questions)
    // ==========================================================================

    $wp_customize->add_section('fiqh_questions_section', array(
        'title'       => __('صفحة الأسئلة', 'fiqhlearning'),
        'description' => __('تخصيص محتوى صفحة الأسئلة والإجابات', 'fiqhlearning'),
        'priority'    => 44,
    ));

    // عنوان الصفحة
    $wp_customize->add_setting('questions_page_title', array(
        'default'           => 'الأسئلة والإجابات',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('questions_page_title', array(
        'label'       => __('عنوان الصفحة', 'fiqhlearning'),
        'section'     => 'fiqh_questions_section',
        'type'        => 'text',
    ));

    // وصف الصفحة
    $wp_customize->add_setting('questions_page_description', array(
        'default'           => 'اطرح أسئلتك واستفسر عن ما يشكل عليك في المقررات الدراسية',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('questions_page_description', array(
        'label'       => __('وصف الصفحة', 'fiqhlearning'),
        'section'     => 'fiqh_questions_section',
        'type'        => 'textarea',
    ));
}
add_action('customize_register', 'fiqhlearning_customize_register');
