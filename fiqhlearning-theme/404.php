<?php
/**
 * صفحة 404 - الصفحة غير موجودة
 *
 * @package مدرسة_الريحان
 * @version 1.3.0
 */

get_header();
?>

<div class="error-404-page">
    <div class="container">
        <div class="error-404-content">
            <!-- أيقونة -->
            <div class="error-icon">
                <svg width="200" height="200" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- كتاب مفتوح -->
                    <path d="M100 40L30 60V160L100 140L170 160V60L100 40Z" fill="var(--color-light)" stroke="var(--color-primary)" stroke-width="3"/>
                    <path d="M100 40V140" stroke="var(--color-primary)" stroke-width="3"/>
                    <!-- صفحات الكتاب -->
                    <path d="M50 80H80M50 100H80M50 120H80" stroke="var(--color-primary-light)" stroke-width="2" stroke-linecap="round"/>
                    <path d="M120 80H150M120 100H150M120 120H150" stroke="var(--color-primary-light)" stroke-width="2" stroke-linecap="round"/>
                    <!-- علامة استفهام -->
                    <circle cx="100" cy="90" r="25" fill="var(--color-primary)" opacity="0.1"/>
                    <text x="100" y="105" font-size="40" font-weight="bold" text-anchor="middle" fill="var(--color-primary)">؟</text>
                </svg>
            </div>

            <!-- رقم الخطأ -->
            <h1 class="error-404-title">404</h1>

            <!-- الرسالة -->
            <h2 class="error-404-subtitle">
                <?php _e('عذراً، الصفحة غير موجودة', 'fiqhlearning'); ?>
            </h2>

            <p class="error-404-text">
                <?php _e('الصفحة التي تبحث عنها قد تكون محذوفة أو تم نقلها إلى مكان آخر.', 'fiqhlearning'); ?>
            </p>

            <!-- أزرار الإجراءات -->
            <div class="error-404-actions">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary btn-lg">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    <?php _e('العودة للصفحة الرئيسية', 'fiqhlearning'); ?>
                </a>

                <?php if (get_page_by_path('courses')) : ?>
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('courses'))); ?>" class="btn btn-secondary btn-lg">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                        </svg>
                        <?php _e('تصفح المقررات', 'fiqhlearning'); ?>
                    </a>
                <?php endif; ?>
            </div>

            <!-- البحث -->
            <div class="error-404-search">
                <h3><?php _e('أو ابحث عما تريد:', 'fiqhlearning'); ?></h3>
                <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                    <div class="search-form-wrapper">
                        <input type="search"
                               class="search-field"
                               placeholder="<?php echo esc_attr_x('ابحث عن مقرر، درس، أو مقالة...', 'placeholder', 'fiqhlearning'); ?>"
                               value="<?php echo get_search_query(); ?>"
                               name="s"
                               required />
                        <button type="submit" class="search-submit">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="m21 21-4.35-4.35"/>
                            </svg>
                            <span class="screen-reader-text"><?php _e('بحث', 'fiqhlearning'); ?></span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- روابط سريعة -->
            <div class="error-404-quick-links">
                <h3><?php _e('روابط مفيدة:', 'fiqhlearning'); ?></h3>
                <ul class="quick-links-list">
                    <li>
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <?php _e('الصفحة الرئيسية', 'fiqhlearning'); ?>
                        </a>
                    </li>
                    <?php
                    // عرض الروابط المفيدة
                    $useful_pages = array(
                        'courses' => 'المقررات',
                        'about' => 'عن المدرسة',
                        'contact' => 'اتصل بنا',
                        'dashboard' => 'لوحة التحكم'
                    );

                    foreach ($useful_pages as $slug => $title) {
                        $page = get_page_by_path($slug);
                        if ($page) {
                            echo '<li><a href="' . esc_url(get_permalink($page)) . '">' . esc_html__($title, 'fiqhlearning') . '</a></li>';
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.error-404-page {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    padding: var(--spacing-3xl) 0;
}

.error-404-content {
    text-align: center;
    background: var(--color-white);
    padding: var(--spacing-3xl);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-xl);
    max-width: 700px;
    margin: 0 auto;
}

.error-icon {
    margin-bottom: var(--spacing-2xl);
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-20px);
    }
}

.error-404-title {
    font-size: 6rem;
    font-weight: 900;
    color: var(--color-primary);
    margin-bottom: var(--spacing-md);
    line-height: 1;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
}

.error-404-subtitle {
    font-size: var(--text-3xl);
    color: var(--color-dark);
    margin-bottom: var(--spacing-md);
}

.error-404-text {
    font-size: var(--text-lg);
    color: var(--color-dark-gray);
    margin-bottom: var(--spacing-2xl);
    line-height: 1.6;
}

.error-404-actions {
    display: flex;
    gap: var(--spacing-md);
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: var(--spacing-2xl);
}

.error-404-actions .btn {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.error-404-search {
    margin: var(--spacing-2xl) 0;
    padding-top: var(--spacing-2xl);
    border-top: 1px solid var(--color-light-gray);
}

.error-404-search h3 {
    font-size: var(--text-xl);
    margin-bottom: var(--spacing-lg);
    color: var(--color-dark);
}

.search-form-wrapper {
    display: flex;
    max-width: 500px;
    margin: 0 auto;
    box-shadow: var(--shadow-md);
    border-radius: var(--radius-lg);
    overflow: hidden;
}

.search-form-wrapper .search-field {
    flex: 1;
    padding: var(--spacing-md) var(--spacing-lg);
    border: 2px solid var(--color-primary);
    border-left: none;
    font-size: var(--text-base);
    outline: none;
}

.search-form-wrapper .search-submit {
    background-color: var(--color-primary);
    color: var(--color-white);
    border: none;
    padding: var(--spacing-md) var(--spacing-xl);
    cursor: pointer;
    transition: background-color var(--transition-fast);
}

.search-form-wrapper .search-submit:hover {
    background-color: var(--color-primary-dark);
}

.error-404-quick-links {
    margin-top: var(--spacing-2xl);
    padding-top: var(--spacing-2xl);
    border-top: 1px solid var(--color-light-gray);
}

.error-404-quick-links h3 {
    font-size: var(--text-lg);
    margin-bottom: var(--spacing-md);
    color: var(--color-dark);
}

.quick-links-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-md);
    justify-content: center;
}

.quick-links-list li a {
    display: inline-block;
    padding: var(--spacing-sm) var(--spacing-lg);
    background-color: var(--color-light);
    color: var(--color-primary);
    border-radius: var(--radius-md);
    transition: all var(--transition-fast);
}

.quick-links-list li a:hover {
    background-color: var(--color-primary);
    color: var(--color-white);
    transform: translateY(-2px);
}

/* Dark Mode */
body.dark-mode .error-404-page {
    background: linear-gradient(135deg, #1a1f2e 0%, #242936 100%);
}

body.dark-mode .error-404-content {
    background-color: var(--color-light);
}

body.dark-mode .error-404-title {
    color: var(--color-primary-light);
}

body.dark-mode .error-404-subtitle,
body.dark-mode .error-404-search h3,
body.dark-mode .error-404-quick-links h3 {
    color: var(--color-white);
}

body.dark-mode .search-form-wrapper .search-field {
    background-color: var(--color-light-gray);
    color: var(--color-white);
}

body.dark-mode .quick-links-list li a {
    background-color: var(--color-light-gray);
    color: var(--color-primary-light);
}

/* Responsive */
@media (max-width: 768px) {
    .error-404-page {
        padding: var(--spacing-2xl) 0;
    }

    .error-404-content {
        padding: var(--spacing-xl);
    }

    .error-404-title {
        font-size: 4rem;
    }

    .error-404-subtitle {
        font-size: var(--text-2xl);
    }

    .error-404-actions {
        flex-direction: column;
    }

    .error-404-actions .btn {
        width: 100%;
        justify-content: center;
    }

    .error-icon svg {
        width: 150px;
        height: 150px;
    }
}

@media (max-width: 480px) {
    .error-404-title {
        font-size: 3rem;
    }

    .error-404-subtitle {
        font-size: var(--text-xl);
    }

    .quick-links-list {
        flex-direction: column;
        align-items: stretch;
    }

    .quick-links-list li a {
        display: block;
        text-align: center;
    }
}
</style>

<?php
get_footer();
?>
