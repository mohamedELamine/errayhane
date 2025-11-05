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

    // شارة Hero
    $wp_customize->add_setting('hero_badge', array(
        'default'           => 'منصة تعلم الفقه المالكي',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('hero_badge', array(
        'label'       => __('نص الشارة', 'fiqhlearning'),
        'description' => __('النص الصغير فوق العنوان', 'fiqhlearning'),
        'section'     => 'fiqh_homepage_section',
        'type'        => 'text',
    ));

    // مميزات Hero (3 مميزات)
    $wp_customize->add_setting('hero_feature_1', array(
        'default'           => 'دروس شاملة ومتنوعة',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('hero_feature_1', array(
        'label'       => __('الميزة الأولى', 'fiqhlearning'),
        'section'     => 'fiqh_homepage_section',
        'type'        => 'text',
    ));

    $wp_customize->add_setting('hero_feature_2', array(
        'default'           => 'شهادات معتمدة',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('hero_feature_2', array(
        'label'       => __('الميزة الثانية', 'fiqhlearning'),
        'section'     => 'fiqh_homepage_section',
        'type'        => 'text',
    ));

    $wp_customize->add_setting('hero_feature_3', array(
        'default'           => 'مدرسون متخصصون',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('hero_feature_3', array(
        'label'       => __('الميزة الثالثة', 'fiqhlearning'),
        'section'     => 'fiqh_homepage_section',
        'type'        => 'text',
    ));

    // خصائص قسم عن المدرسة
    $wp_customize->add_setting('about_feature_1_title', array(
        'default'           => 'منهج شامل',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('about_feature_1_title', array(
        'label'       => __('عنوان الخاصية الأولى', 'fiqhlearning'),
        'section'     => 'fiqh_homepage_section',
        'type'        => 'text',
    ));

    $wp_customize->add_setting('about_feature_1_desc', array(
        'default'           => 'دروس متكاملة في الفقه المالكي',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('about_feature_1_desc', array(
        'label'       => __('وصف الخاصية الأولى', 'fiqhlearning'),
        'section'     => 'fiqh_homepage_section',
        'type'        => 'text',
    ));

    $wp_customize->add_setting('about_feature_2_title', array(
        'default'           => 'أساتذة متخصصون',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('about_feature_2_title', array(
        'label'       => __('عنوان الخاصية الثانية', 'fiqhlearning'),
        'section'     => 'fiqh_homepage_section',
        'type'        => 'text',
    ));

    $wp_customize->add_setting('about_feature_2_desc', array(
        'default'           => 'نخبة من العلماء والمشايخ',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('about_feature_2_desc', array(
        'label'       => __('وصف الخاصية الثانية', 'fiqhlearning'),
        'section'     => 'fiqh_homepage_section',
        'type'        => 'text',
    ));

    $wp_customize->add_setting('about_feature_3_title', array(
        'default'           => 'شهادات معتمدة',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('about_feature_3_title', array(
        'label'       => __('عنوان الخاصية الثالثة', 'fiqhlearning'),
        'section'     => 'fiqh_homepage_section',
        'type'        => 'text',
    ));

    $wp_customize->add_setting('about_feature_3_desc', array(
        'default'           => 'شهادات إتمام للمقررات',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('about_feature_3_desc', array(
        'label'       => __('وصف الخاصية الثالثة', 'fiqhlearning'),
        'section'     => 'fiqh_homepage_section',
        'type'        => 'text',
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

    // نصوص الميزات الأربعة
    $about_features = array(
        array('key' => 'feature1_title', 'label' => 'الميزة 1 - العنوان', 'default' => 'منهج أصيل ومعتمد'),
        array('key' => 'feature1_desc', 'label' => 'الميزة 1 - الوصف', 'default' => 'منهج دراسي معتمد'),
        array('key' => 'feature2_title', 'label' => 'الميزة 2 - العنوان', 'default' => 'أساتذة متخصصون'),
        array('key' => 'feature2_desc', 'label' => 'الميزة 2 - الوصف', 'default' => 'نخبة من المعلمين المتخصصين'),
        array('key' => 'feature3_title', 'label' => 'الميزة 3 - العنوان', 'default' => 'محتوى تعليمي شامل'),
        array('key' => 'feature3_desc', 'label' => 'الميزة 3 - الوصف', 'default' => 'دروس ومحتوى غني'),
        array('key' => 'feature4_title', 'label' => 'الميزة 4 - العنوان', 'default' => 'شهادات معتمدة'),
        array('key' => 'feature4_desc', 'label' => 'الميزة 4 - الوصف', 'default' => 'شهادات رسمية معتمدة'),
    );

    foreach ($about_features as $feature) {
        $wp_customize->add_setting("about_{$feature['key']}", array(
            'default'           => $feature['default'],
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control("about_{$feature['key']}", array(
            'label'   => $feature['label'],
            'section' => 'fiqh_about_section',
            'type'    => 'text',
        ));
    }

    // قسم القيم والرؤية - العناوين والنصوص
    $about_values_items = array(
        array('key' => 'value1_title', 'label' => 'القيمة 1 - العنوان', 'default' => 'الأصالة العلمية'),
        array('key' => 'value1_desc', 'label' => 'القيمة 1 - الوصف', 'default' => 'الالتزام بالمنهج العلمي الأصيل في تدريس الفقه المالكي'),
        array('key' => 'value2_title', 'label' => 'القيمة 2 - العنوان', 'default' => 'التميز التعليمي'),
        array('key' => 'value2_desc', 'label' => 'القيمة 2 - الوصف', 'default' => 'تقديم محتوى تعليمي عالي الجودة بأساليب عصرية ومبتكرة'),
        array('key' => 'value3_title', 'label' => 'القيمة 3 - العنوان', 'default' => 'الجودة والإتقان'),
        array('key' => 'value3_desc', 'label' => 'القيمة 3 - الوصف', 'default' => 'الحرص على إتقان العمل وتقديم أفضل الخدمات التعليمية'),
        array('key' => 'value4_title', 'label' => 'القيمة 4 - العنوان', 'default' => 'التطوير المستمر'),
        array('key' => 'value4_desc', 'label' => 'القيمة 4 - الوصف', 'default' => 'السعي الدائم لتطوير المحتوى والخدمات التعليمية'),
    );

    foreach ($about_values_items as $value) {
        $wp_customize->add_setting("about_{$value['key']}", array(
            'default'           => $value['default'],
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control("about_{$value['key']}", array(
            'label'   => $value['label'],
            'section' => 'fiqh_about_section',
            'type'    => strpos($value['key'], 'desc') !== false ? 'textarea' : 'text',
        ));
    }

    // قسم فريق العمل
    $about_team_labels = array(
        'team_title' => array('label' => 'عنوان "فريق العمل"', 'default' => 'فريق العمل'),
        'team_member1_name' => array('label' => 'العضو 1 - الاسم', 'default' => 'د. أحمد المالكي'),
        'team_member1_role' => array('label' => 'العضو 1 - الدور', 'default' => 'المشرف العام'),
        'team_member2_name' => array('label' => 'العضو 2 - الاسم', 'default' => 'د. محمد الفقيه'),
        'team_member2_role' => array('label' => 'العضو 2 - الدور', 'default' => 'أستاذ الفقه المالكي'),
    );

    foreach ($about_team_labels as $key => $data) {
        $wp_customize->add_setting("about_{$key}", array(
            'default'           => $data['default'],
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control("about_{$key}", array(
            'label'   => $data['label'],
            'section' => 'fiqh_about_section',
            'type'    => 'text',
        ));
    }

    // القسم الأخير (CTA)
    $about_cta_labels = array(
        'cta_title' => array('label' => 'CTA - العنوان', 'default' => 'انضم إلى رحلتنا التعليمية'),
        'cta_desc' => array('label' => 'CTA - الوصف', 'default' => 'ابدأ رحلتك في تعلم الفقه المالكي والعلوم الشرعية مع مدرسة الريحان'),
        'cta_button' => array('label' => 'CTA - نص الزر', 'default' => 'تواصل معنا'),
    );

    foreach ($about_cta_labels as $key => $data) {
        $wp_customize->add_setting("about_{$key}", array(
            'default'           => $data['default'],
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control("about_{$key}", array(
            'label'   => $data['label'],
            'section' => 'fiqh_about_section',
            'type'    => $key === 'cta_desc' ? 'textarea' : 'text',
        ));
    }

    // Timeline section title
    $wp_customize->add_setting('about_timeline_title', array(
        'default'           => 'مسيرة المدرسة',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('about_timeline_title', array(
        'label'       => __('عنوان مسيرة المدرسة', 'fiqhlearning'),
        'section'     => 'fiqh_about_section',
        'type'        => 'text',
    ));

    // Timeline section description
    $wp_customize->add_setting('about_timeline_description', array(
        'default'           => 'رحلتنا منذ التأسيس حتى اليوم',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('about_timeline_description', array(
        'label'       => __('وصف مسيرة المدرسة', 'fiqhlearning'),
        'section'     => 'fiqh_about_section',
        'type'        => 'text',
    ));

    // Timeline events - Create settings for 6 events
    for ($i = 1; $i <= 6; $i++) {
        // Event year
        $wp_customize->add_setting("about_timeline_event_{$i}_year", array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control("about_timeline_event_{$i}_year", array(
            'label'       => sprintf(__('الحدث %d - السنة', 'fiqhlearning'), $i),
            'section'     => 'fiqh_about_section',
            'type'        => 'text',
        ));

        // Event title
        $wp_customize->add_setting("about_timeline_event_{$i}_title", array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control("about_timeline_event_{$i}_title", array(
            'label'       => sprintf(__('الحدث %d - العنوان', 'fiqhlearning'), $i),
            'section'     => 'fiqh_about_section',
            'type'        => 'text',
        ));

        // Event description
        $wp_customize->add_setting("about_timeline_event_{$i}_description", array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_textarea_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control("about_timeline_event_{$i}_description", array(
            'label'       => sprintf(__('الحدث %d - الوصف', 'fiqhlearning'), $i),
            'section'     => 'fiqh_about_section',
            'type'        => 'textarea',
        ));
    }

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

    // نصوص إضافية لصفحة خطة الدراسة
    $study_plan_labels = array(
        'breadcrumb_home' => array('label' => 'Breadcrumb: الرئيسية', 'default' => 'الرئيسية'),
        'breadcrumb_study_plan' => array('label' => 'Breadcrumb: خطة الدراسة', 'default' => 'خطة الدراسة'),
        'levels_system_title' => array('label' => 'عنوان نظام المستويات', 'default' => 'نظام المستويات الدراسية'),
        'level_label' => array('label' => 'تسمية "المستوى"', 'default' => 'المستوى'),
        'start_date_label' => array('label' => 'تسمية "البداية:"', 'default' => 'البداية:'),
        'end_date_label' => array('label' => 'تسمية "النهاية:"', 'default' => 'النهاية:'),
        'courses_title' => array('label' => 'عنوان "المقررات الدراسية"', 'default' => 'المقررات الدراسية'),
        'lesson_label' => array('label' => 'تسمية "درس"', 'default' => 'درس'),
        'no_levels_title' => array('label' => 'عنوان "لم يتم إضافة مستويات"', 'default' => 'لم يتم إضافة مستويات دراسية بعد'),
        'no_levels_desc' => array('label' => 'وصف "لم يتم إضافة مستويات"', 'default' => 'سيتم إضافة الخطة الدراسية قريباً'),
        'browse_courses_btn' => array('label' => 'زر "تصفح المقررات"', 'default' => 'تصفح المقررات'),
        'cta_title' => array('label' => 'CTA: العنوان', 'default' => 'مستعد للبدء؟'),
        'cta_desc' => array('label' => 'CTA: الوصف', 'default' => 'ابدأ رحلتك التعليمية الآن وانضم إلى مدرسة الريحان'),
        'cta_button_courses' => array('label' => 'CTA: زر المقررات', 'default' => 'تصفح المقررات'),
        'cta_button_about' => array('label' => 'CTA: زر عن المدرسة', 'default' => 'عن المدرسة'),
    );

    foreach ($study_plan_labels as $key => $data) {
        $wp_customize->add_setting("study_plan_{$key}", array(
            'default'           => $data['default'],
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control("study_plan_{$key}", array(
            'label'   => $data['label'],
            'section' => 'fiqh_study_plan_section',
            'type'    => 'text',
        ));
    }

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

    // نصوص إضافية لصفحة دليل اللوائح
    $regulations_labels = array(
        'breadcrumb_home' => array('label' => 'Breadcrumb: الرئيسية', 'default' => 'الرئيسية'),
        'breadcrumb_regulations' => array('label' => 'Breadcrumb: دليل اللوائح', 'default' => 'دليل اللوائح'),
        'sections_title' => array('label' => 'عنوان "الأقسام"', 'default' => 'الأقسام'),
        'admission_title' => array('label' => 'عنوان "شروط القبول"', 'default' => 'شروط القبول'),
        'enrollment_title' => array('label' => 'عنوان "نظام التسجيل"', 'default' => 'نظام التسجيل'),
        'attendance_title' => array('label' => 'عنوان "الحضور والغياب"', 'default' => 'الحضور والغياب'),
        'exams_title' => array('label' => 'عنوان "الاختبارات"', 'default' => 'الاختبارات'),
        'grades_title' => array('label' => 'عنوان "نظام الدرجات"', 'default' => 'نظام الدرجات'),
        'behavior_title' => array('label' => 'عنوان "السلوك والانضباط"', 'default' => 'السلوك والانضباط'),
        'certificates_title' => array('label' => 'عنوان "الشهادات"', 'default' => 'الشهادات'),
        'rights_title' => array('label' => 'عنوان "الحقوق والواجبات"', 'default' => 'الحقوق والواجبات'),
    );

    foreach ($regulations_labels as $key => $data) {
        $wp_customize->add_setting("regulations_{$key}", array(
            'default'           => $data['default'],
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control("regulations_{$key}", array(
            'label'   => $data['label'],
            'section' => 'fiqh_regulations_section',
            'type'    => 'text',
        ));
    }

    // محتوى أقسام اللوائح
    $regulations_contents = array(
        'admission' => 'شروط القبول',
        'enrollment' => 'نظام التسجيل',
        'attendance' => 'الحضور والغياب',
        'exams' => 'الاختبارات',
        'grades' => 'نظام الدرجات',
        'behavior' => 'السلوك والانضباط',
        'certificates' => 'الشهادات',
        'rights' => 'الحقوق والواجبات',
    );

    foreach ($regulations_contents as $key => $label) {
        $wp_customize->add_setting("regulations_{$key}", array(
            'default'           => '',
            'sanitize_callback' => 'wp_kses_post',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control("regulations_{$key}", array(
            'label'       => sprintf(__('محتوى %s', 'fiqhlearning'), $label),
            'description' => __('يمكن استخدام HTML البسيط', 'fiqhlearning'),
            'section'     => 'fiqh_regulations_section',
            'type'        => 'textarea',
        ));
    }

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

    // ==========================================================================
    // قسم صفحة المقالات (Blog)
    // ==========================================================================

    $wp_customize->add_section('fiqh_blog_section', array(
        'title'       => __('صفحة المقالات', 'fiqhlearning'),
        'description' => __('تخصيص محتوى صفحة المقالات والأرشيف', 'fiqhlearning'),
        'priority'    => 45,
    ));

    // شارة المقالات
    $wp_customize->add_setting('blog_badge', array(
        'default'           => 'مدونة المعهد',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('blog_badge', array(
        'label'       => __('شارة المقالات', 'fiqhlearning'),
        'description' => __('النص الذي يظهر فوق عنوان المقالات', 'fiqhlearning'),
        'section'     => 'fiqh_blog_section',
        'type'        => 'text',
    ));

    // عنوان صفحة المقالات
    $wp_customize->add_setting('blog_title', array(
        'default'           => 'آخر المقالات والمستجدات',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('blog_title', array(
        'label'       => __('عنوان صفحة المقالات', 'fiqhlearning'),
        'section'     => 'fiqh_blog_section',
        'type'        => 'text',
    ));

    // وصف صفحة المقالات
    $wp_customize->add_setting('blog_description', array(
        'default'           => 'تابع آخر المقالات والأخبار والمستجدات في الفقه المالكي والعلوم الشرعية',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('blog_description', array(
        'label'       => __('وصف صفحة المقالات', 'fiqhlearning'),
        'section'     => 'fiqh_blog_section',
        'type'        => 'textarea',
    ));

    // نص زر البحث
    $wp_customize->add_setting('blog_search_button', array(
        'default'           => 'بحث',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('blog_search_button', array(
        'label'       => __('نص زر البحث', 'fiqhlearning'),
        'section'     => 'fiqh_blog_section',
        'type'        => 'text',
    ));

    // نص "جميع التصنيفات"
    $wp_customize->add_setting('blog_all_categories', array(
        'default'           => 'جميع التصنيفات',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('blog_all_categories', array(
        'label'       => __('نص "جميع التصنيفات"', 'fiqhlearning'),
        'section'     => 'fiqh_blog_section',
        'type'        => 'text',
    ));

    // نص placeholder للبحث
    $wp_customize->add_setting('blog_search_placeholder', array(
        'default'           => 'ابحث في المقالات...',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('blog_search_placeholder', array(
        'label'       => __('نص البحث (Placeholder)', 'fiqhlearning'),
        'section'     => 'fiqh_blog_section',
        'type'        => 'text',
    ));

    // نص نتائج البحث
    $wp_customize->add_setting('blog_search_results_text', array(
        'default'           => 'نتائج البحث عن: <strong>%s</strong>',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('blog_search_results_text', array(
        'label'       => __('نص نتائج البحث', 'fiqhlearning'),
        'description' => __('استخدم %s للكلمة المبحوثة', 'fiqhlearning'),
        'section'     => 'fiqh_blog_section',
        'type'        => 'text',
    ));

    // نص عدد المقالات (واحد)
    $wp_customize->add_setting('blog_results_count_single', array(
        'default'           => 'عُثر على مقالة واحدة',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('blog_results_count_single', array(
        'label'       => __('نص عدد المقالات (واحد)', 'fiqhlearning'),
        'section'     => 'fiqh_blog_section',
        'type'        => 'text',
    ));

    // نص عدد المقالات (متعدد)
    $wp_customize->add_setting('blog_results_count_multiple', array(
        'default'           => 'عُثر على %s مقالة',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('blog_results_count_multiple', array(
        'label'       => __('نص عدد المقالات (متعدد)', 'fiqhlearning'),
        'description' => __('استخدم %s للعدد', 'fiqhlearning'),
        'section'     => 'fiqh_blog_section',
        'type'        => 'text',
    ));

    // نص "اقرأ المزيد"
    $wp_customize->add_setting('blog_read_more_text', array(
        'default'           => 'اقرأ المزيد',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('blog_read_more_text', array(
        'label'       => __('نص "اقرأ المزيد"', 'fiqhlearning'),
        'section'     => 'fiqh_blog_section',
        'type'        => 'text',
    ));

    // نص زر السابق في الترقيم
    $wp_customize->add_setting('blog_pagination_prev', array(
        'default'           => 'السابق',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('blog_pagination_prev', array(
        'label'       => __('نص زر السابق (Pagination)', 'fiqhlearning'),
        'section'     => 'fiqh_blog_section',
        'type'        => 'text',
    ));

    // نص زر التالي في الترقيم
    $wp_customize->add_setting('blog_pagination_next', array(
        'default'           => 'التالي',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('blog_pagination_next', array(
        'label'       => __('نص زر التالي (Pagination)', 'fiqhlearning'),
        'section'     => 'fiqh_blog_section',
        'type'        => 'text',
    ));

    // نصوص "No posts found"
    $wp_customize->add_setting('blog_no_posts_title', array(
        'default'           => 'لم يتم العثور على مقالات',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('blog_no_posts_title', array(
        'label'       => __('عنوان "لا توجد مقالات"', 'fiqhlearning'),
        'section'     => 'fiqh_blog_section',
        'type'        => 'text',
    ));

    $wp_customize->add_setting('blog_no_posts_description', array(
        'default'           => 'جرب البحث بكلمات مختلفة أو تصفح جميع التصنيفات',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('blog_no_posts_description', array(
        'label'       => __('وصف "لا توجد مقالات"', 'fiqhlearning'),
        'section'     => 'fiqh_blog_section',
        'type'        => 'textarea',
    ));

    $wp_customize->add_setting('blog_no_posts_button', array(
        'default'           => 'عرض جميع المقالات',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('blog_no_posts_button', array(
        'label'       => __('نص زر "عرض جميع المقالات"', 'fiqhlearning'),
        'section'     => 'fiqh_blog_section',
        'type'        => 'text',
    ));

    // ==========================================================================
    // قسم صفحة المقال الفردي (Single Post)
    // ==========================================================================

    $wp_customize->add_section('fiqh_single_post_section', array(
        'title'       => __('صفحة المقال الفردي', 'fiqhlearning'),
        'description' => __('تخصيص نصوص صفحة المقال الفردي', 'fiqhlearning'),
        'priority'    => 46,
    ));

    // تسمية الكاتب
    $wp_customize->add_setting('single_post_author_label', array(
        'default'           => 'الكاتب',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('single_post_author_label', array(
        'label'       => __('تسمية الكاتب', 'fiqhlearning'),
        'section'     => 'fiqh_single_post_section',
        'type'        => 'text',
    ));

    // صيغة وقت القراءة
    $wp_customize->add_setting('single_post_reading_time_format', array(
        'default'           => '%d دقائق قراءة',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('single_post_reading_time_format', array(
        'label'       => __('صيغة وقت القراءة', 'fiqhlearning'),
        'description' => __('استخدم %d لرقم الدقائق', 'fiqhlearning'),
        'section'     => 'fiqh_single_post_section',
        'type'        => 'text',
    ));

    // نص "لا توجد تعليقات"
    $wp_customize->add_setting('single_post_no_comments', array(
        'default'           => 'لا توجد تعليقات',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('single_post_no_comments', array(
        'label'       => __('نص "لا توجد تعليقات"', 'fiqhlearning'),
        'section'     => 'fiqh_single_post_section',
        'type'        => 'text',
    ));

    // صيغة عدد التعليقات
    $wp_customize->add_setting('single_post_comments_format', array(
        'default'           => '%d تعليق',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('single_post_comments_format', array(
        'label'       => __('صيغة عدد التعليقات', 'fiqhlearning'),
        'description' => __('استخدم %d للعدد', 'fiqhlearning'),
        'section'     => 'fiqh_single_post_section',
        'type'        => 'text',
    ));

    // تسمية الوسوم
    $wp_customize->add_setting('single_post_tags_label', array(
        'default'           => 'الوسوم:',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('single_post_tags_label', array(
        'label'       => __('تسمية الوسوم', 'fiqhlearning'),
        'section'     => 'fiqh_single_post_section',
        'type'        => 'text',
    ));

    // عنوان قسم المشاركة
    $wp_customize->add_setting('single_post_share_title', array(
        'default'           => 'شارك المقال',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('single_post_share_title', array(
        'label'       => __('عنوان قسم المشاركة', 'fiqhlearning'),
        'section'     => 'fiqh_single_post_section',
        'type'        => 'text',
    ));

    // نصوص أزرار المشاركة
    $share_buttons = array(
        'facebook' => 'فيسبوك',
        'twitter' => 'تويتر',
        'whatsapp' => 'واتساب',
        'telegram' => 'تيليجرام',
    );

    foreach ($share_buttons as $platform => $label) {
        $wp_customize->add_setting("single_post_share_{$platform}_text", array(
            'default'           => $label,
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control("single_post_share_{$platform}_text", array(
            'label'       => sprintf(__('نص زر %s', 'fiqhlearning'), $label),
            'section'     => 'fiqh_single_post_section',
            'type'        => 'text',
        ));

        $wp_customize->add_setting("single_post_share_{$platform}", array(
            'default'           => sprintf('شارك على %s', $label),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control("single_post_share_{$platform}", array(
            'label'       => sprintf(__('نص aria-label لـ %s', 'fiqhlearning'), $label),
            'section'     => 'fiqh_single_post_section',
            'type'        => 'text',
        ));
    }

    // عنوان قسم المؤلف
    $wp_customize->add_setting('single_post_author_bio_title', array(
        'default'           => 'عن المؤلف',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('single_post_author_bio_title', array(
        'label'       => __('عنوان قسم المؤلف', 'fiqhlearning'),
        'section'     => 'fiqh_single_post_section',
        'type'        => 'text',
    ));

    // نص رابط مقالات المؤلف
    $wp_customize->add_setting('single_post_author_posts_link', array(
        'default'           => 'جميع مقالات المؤلف',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('single_post_author_posts_link', array(
        'label'       => __('نص رابط مقالات المؤلف', 'fiqhlearning'),
        'section'     => 'fiqh_single_post_section',
        'type'        => 'text',
    ));

    // عنوان قسم التعليقات (بدون تعليقات)
    $wp_customize->add_setting('single_post_comments_section_title_zero', array(
        'default'           => 'التعليقات',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('single_post_comments_section_title_zero', array(
        'label'       => __('عنوان قسم التعليقات (بدون تعليقات)', 'fiqhlearning'),
        'section'     => 'fiqh_single_post_section',
        'type'        => 'text',
    ));

    // عنوان قسم التعليقات (مع العدد)
    $wp_customize->add_setting('single_post_comments_section_title', array(
        'default'           => 'التعليقات (%d)',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('single_post_comments_section_title', array(
        'label'       => __('عنوان قسم التعليقات (مع العدد)', 'fiqhlearning'),
        'description' => __('استخدم %d للعدد', 'fiqhlearning'),
        'section'     => 'fiqh_single_post_section',
        'type'        => 'text',
    ));

    // عنوان المقالات ذات الصلة
    $wp_customize->add_setting('single_post_related_posts_title', array(
        'default'           => 'مقالات ذات صلة',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('single_post_related_posts_title', array(
        'label'       => __('عنوان المقالات ذات الصلة', 'fiqhlearning'),
        'section'     => 'fiqh_single_post_section',
        'type'        => 'text',
    ));

    // نص "اقرأ المزيد"
    $wp_customize->add_setting('single_post_read_more', array(
        'default'           => 'اقرأ المزيد',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('single_post_read_more', array(
        'label'       => __('نص "اقرأ المزيد"', 'fiqhlearning'),
        'section'     => 'fiqh_single_post_section',
        'type'        => 'text',
    ));

    // نص وقت القراءة (الصيغة الكاملة)
    $wp_customize->add_setting('single_post_reading_time_text', array(
        'default'           => '%d دقائق قراءة',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('single_post_reading_time_text', array(
        'label'       => __('نص وقت القراءة', 'fiqhlearning'),
        'description' => __('استخدم %d لعدد الدقائق', 'fiqhlearning'),
        'section'     => 'fiqh_single_post_section',
        'type'        => 'text',
    ));

    // نص "الصفحات:"
    $wp_customize->add_setting('single_post_pages_label', array(
        'default'           => 'الصفحات:',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('single_post_pages_label', array(
        'label'       => __('نص "الصفحات:"', 'fiqhlearning'),
        'description' => __('يظهر عند تقسيم المقال لعدة صفحات', 'fiqhlearning'),
        'section'     => 'fiqh_single_post_section',
        'type'        => 'text',
    ));

    // نص السيرة الذاتية الافتراضية
    $wp_customize->add_setting('single_post_default_author_bio', array(
        'default'           => 'كاتب في منصة FiqhLearning',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('single_post_default_author_bio', array(
        'label'       => __('نص السيرة الافتراضية للكاتب', 'fiqhlearning'),
        'description' => __('يظهر عندما لا تكون للكاتب سيرة مخصصة', 'fiqhlearning'),
        'section'     => 'fiqh_single_post_section',
        'type'        => 'text',
    ));

    // نص "عرض جميع المقالات"
    $wp_customize->add_setting('single_post_view_all_posts', array(
        'default'           => 'عرض جميع المقالات',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('single_post_view_all_posts', array(
        'label'       => __('نص "عرض جميع المقالات"', 'fiqhlearning'),
        'section'     => 'fiqh_single_post_section',
        'type'        => 'text',
    ));

    // نص "مقالات ذات صلة"
    $wp_customize->add_setting('single_post_related_title', array(
        'default'           => 'مقالات ذات صلة',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('single_post_related_title', array(
        'label'       => __('عنوان "مقالات ذات صلة"', 'fiqhlearning'),
        'section'     => 'fiqh_single_post_section',
        'type'        => 'text',
    ));

    // نص "المقال السابق"
    $wp_customize->add_setting('single_post_prev_text', array(
        'default'           => 'المقال السابق',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('single_post_prev_text', array(
        'label'       => __('نص "المقال السابق"', 'fiqhlearning'),
        'section'     => 'fiqh_single_post_section',
        'type'        => 'text',
    ));

    // نص "المقال التالي"
    $wp_customize->add_setting('single_post_next_text', array(
        'default'           => 'المقال التالي',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('single_post_next_text', array(
        'label'       => __('نص "المقال التالي"', 'fiqhlearning'),
        'section'     => 'fiqh_single_post_section',
        'type'        => 'text',
    ));
}
add_action('customize_register', 'fiqhlearning_customize_register');
