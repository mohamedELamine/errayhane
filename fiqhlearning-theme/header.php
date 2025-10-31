<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#primary"><?php _e('انتقل إلى المحتوى', 'fiqhlearning'); ?></a>

    <header id="masthead" class="site-header">
        <!-- شريط الإعلانات العلوي (اختياري) -->
        <?php if (get_theme_mod('fiqh_top_bar_enabled', false)) : ?>
        <div class="top-bar">
            <div class="container">
                <div class="top-bar-content">
                    <div class="top-bar-right">
                        <?php if (get_theme_mod('fiqh_telegram_link')) : ?>
                            <a href="<?php echo esc_url(get_theme_mod('fiqh_telegram_link')); ?>" target="_blank" class="top-link">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69.01-.03.01-.14-.07-.2-.08-.06-.19-.04-.27-.02-.12.03-1.99 1.27-5.62 3.72-.53.36-1.01.54-1.44.53-.47-.01-1.38-.27-2.06-.49-.83-.27-1.49-.42-1.43-.88.03-.24.38-.49 1.05-.74 4.11-1.79 6.86-2.97 8.24-3.54 3.92-1.63 4.73-1.92 5.26-1.93.12 0 .38.03.55.17.14.12.18.28.2.44.02.12.04.35.02.54z"/>
                                </svg>
                                قناة التليجرام
                            </a>
                        <?php endif; ?>
                        <?php if (get_theme_mod('fiqh_whatsapp_link')) : ?>
                            <a href="<?php echo esc_url(get_theme_mod('fiqh_whatsapp_link')); ?>" target="_blank" class="top-link">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                واتساب
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="top-bar-left">
                        <span class="top-text"><?php echo esc_html(get_theme_mod('fiqh_top_bar_text', 'مرحباً بكم في منصة تعلم الفقه المالكي')); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- الهيدر الرئيسي -->
        <div class="main-header">
            <div class="container">
                <div class="header-content">
                    <!-- الشعار -->
                    <div class="site-branding">
                        <?php if (has_custom_logo()) : ?>
                            <?php the_custom_logo(); ?>
                        <?php else : ?>
                            <h1 class="site-title">
                                <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                    <?php bloginfo('name'); ?>
                                </a>
                            </h1>
                            <?php
                            $description = get_bloginfo('description', 'display');
                            if ($description || is_customize_preview()) :
                            ?>
                                <p class="site-description"><?php echo $description; ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <!-- القائمة الرئيسية -->
                    <nav id="site-navigation" class="main-navigation">
                        <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                            <span class="menu-icon"></span>
                            <span class="screen-reader-text"><?php _e('القائمة', 'fiqhlearning'); ?></span>
                        </button>

                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'primary',
                            'menu_id' => 'primary-menu',
                            'menu_class' => 'nav-menu',
                            'container' => 'ul',
                            'fallback_cb' => false,
                        ));
                        ?>
                    </nav>

                    <!-- أيقونات الإجراءات -->
                    <div class="header-actions">
                        <!-- زر البحث -->
                        <button class="search-toggle" aria-label="<?php _e('بحث', 'fiqhlearning'); ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                        </button>

                        <!-- زر الوضع الليلي -->
                        <button class="dark-mode-toggle" aria-label="<?php _e('تبديل الوضع الليلي', 'fiqhlearning'); ?>">
                            <svg class="sun-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="5"></circle>
                                <line x1="12" y1="1" x2="12" y2="3"></line>
                                <line x1="12" y1="21" x2="12" y2="23"></line>
                                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                <line x1="1" y1="12" x2="3" y2="12"></line>
                                <line x1="21" y1="12" x2="23" y2="12"></line>
                                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                            </svg>
                            <svg class="moon-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: none;">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                            </svg>
                        </button>

                        <!-- أيقونة المستخدم -->
                        <?php if (is_user_logged_in()) : ?>
                            <?php
                            $current_user = wp_get_current_user();
                            $user_role = $current_user->roles[0];
                            $dashboard_link = ($user_role === 'student') ? home_url('/student-dashboard/') :
                                            (($user_role === 'teacher') ? admin_url() : admin_url());
                            ?>
                            <div class="user-menu-wrapper">
                                <button class="user-menu-toggle" aria-label="<?php _e('حسابي', 'fiqhlearning'); ?>">
                                    <?php echo get_avatar(get_current_user_id(), 32); ?>
                                    <span class="user-name"><?php echo esc_html($current_user->display_name); ?></span>
                                </button>

                                <div class="user-dropdown">
                                    <a href="<?php echo esc_url($dashboard_link); ?>" class="dropdown-item">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="3" width="7" height="7"></rect>
                                            <rect x="14" y="3" width="7" height="7"></rect>
                                            <rect x="14" y="14" width="7" height="7"></rect>
                                            <rect x="3" y="14" width="7" height="7"></rect>
                                        </svg>
                                        <?php _e('لوحة التحكم', 'fiqhlearning'); ?>
                                    </a>
                                    <a href="<?php echo esc_url(get_edit_profile_url()); ?>" class="dropdown-item">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                        <?php _e('الملف الشخصي', 'fiqhlearning'); ?>
                                    </a>
                                    <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="dropdown-item">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                            <polyline points="16 17 21 12 16 7"></polyline>
                                            <line x1="21" y1="12" x2="9" y2="12"></line>
                                        </svg>
                                        <?php _e('تسجيل الخروج', 'fiqhlearning'); ?>
                                    </a>
                                </div>
                            </div>
                        <?php else : ?>
                            <a href="<?php echo esc_url(wp_login_url()); ?>" class="btn btn-primary btn-login">
                                <?php _e('تسجيل الدخول', 'fiqhlearning'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- مربع البحث المنبثق -->
        <div class="search-modal" style="display: none;">
            <div class="search-modal-content">
                <button class="search-modal-close" aria-label="<?php _e('إغلاق', 'fiqhlearning'); ?>">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
                <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                    <input type="search" class="search-field" placeholder="<?php _e('ابحث عن مقررات، دروس، مقالات...', 'fiqhlearning'); ?>" value="<?php echo get_search_query(); ?>" name="s" />
                    <button type="submit" class="search-submit">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                        <span class="screen-reader-text"><?php _e('بحث', 'fiqhlearning'); ?></span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <div id="content" class="site-content">
