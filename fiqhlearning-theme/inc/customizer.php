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
}
add_action('customize_register', 'fiqhlearning_customize_register');
