<?php
/**
 * Template Name: دليل اللوائح
 * قالب صفحة دليل اللوائح والأنظمة
 *
 * @package FiqhLearning
 */

get_header();
?>

<main id="primary" class="site-main page-regulations">

    <!-- Hero Section -->
    <section class="page-hero">
        <div class="container">
            <div class="page-hero-content">
                <div class="breadcrumb">
                    <a href="<?php echo home_url(); ?>"><?php echo get_theme_mod('regulations_breadcrumb_home', __('الرئيسية', 'fiqhlearning')); ?></a>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                    <span><?php echo get_theme_mod('regulations_breadcrumb_current', __('دليل اللوائح', 'fiqhlearning')); ?></span>
                </div>
                <h1 class="page-title">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    <?php echo get_theme_mod('regulations_title', __('دليل اللوائح والأنظمة', 'fiqhlearning')); ?>
                </h1>
                <p class="page-description">
                    <?php echo get_theme_mod('regulations_description', __('اللوائح والأنظمة المنظمة للدراسة في مدرسة الريحان للعلوم الشرعية', 'fiqhlearning')); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Regulations Content -->
    <section class="section">
        <div class="container">
            <div class="regulations-layout">

                <!-- Sidebar Navigation -->
                <aside class="regulations-sidebar">
                    <nav class="regulations-nav card">
                        <h3><?php echo get_theme_mod('regulations_sidebar_title', __('الأقسام', 'fiqhlearning')); ?></h3>
                        <ul class="regulations-menu">
                            <li><a href="#admission" class="active"><?php echo get_theme_mod('regulations_nav_admission', __('شروط القبول', 'fiqhlearning')); ?></a></li>
                            <li><a href="#enrollment"><?php echo get_theme_mod('regulations_nav_enrollment', __('نظام التسجيل', 'fiqhlearning')); ?></a></li>
                            <li><a href="#attendance"><?php echo get_theme_mod('regulations_nav_attendance', __('الحضور والغياب', 'fiqhlearning')); ?></a></li>
                            <li><a href="#exams"><?php echo get_theme_mod('regulations_nav_exams', __('الاختبارات', 'fiqhlearning')); ?></a></li>
                            <li><a href="#grades"><?php echo get_theme_mod('regulations_nav_grades', __('نظام الدرجات', 'fiqhlearning')); ?></a></li>
                            <li><a href="#behavior"><?php echo get_theme_mod('regulations_nav_behavior', __('السلوك والانضباط', 'fiqhlearning')); ?></a></li>
                            <li><a href="#certificates"><?php echo get_theme_mod('regulations_nav_certificates', __('الشهادات', 'fiqhlearning')); ?></a></li>
                            <li><a href="#rights"><?php echo get_theme_mod('regulations_nav_rights', __('الحقوق والواجبات', 'fiqhlearning')); ?></a></li>
                        </ul>
                    </nav>
                </aside>

                <!-- Main Content -->
                <div class="regulations-content">

                    <!-- شروط القبول -->
                    <section id="admission" class="regulation-section card">
                        <div class="regulation-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="8.5" cy="7" r="4"></circle>
                                <polyline points="17 11 19 13 23 9"></polyline>
                            </svg>
                        </div>
                        <h2><?php echo get_theme_mod('regulations_section_admission_title', __('شروط القبول', 'fiqhlearning')); ?></h2>
                        <div class="regulation-content">
                            <?php echo wpautop(get_theme_mod('regulations_admission', __('1. أن يكون المتقدم مسلماً<br>2. أن يكون حسن السيرة والسلوك<br>3. أن يجتاز المقابلة الشخصية (إن وجدت)<br>4. الالتزام بلوائح وأنظمة المدرسة<br>5. تقديم الوثائق المطلوبة', 'fiqhlearning'))); ?>
                        </div>
                    </section>

                    <!-- نظام التسجيل -->
                    <section id="enrollment" class="regulation-section card">
                        <div class="regulation-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                            </svg>
                        </div>
                        <h2><?php echo get_theme_mod('regulations_section_enrollment_title', __('نظام التسجيل', 'fiqhlearning')); ?></h2>
                        <div class="regulation-content">
                            <?php echo wpautop(get_theme_mod('regulations_enrollment', __('1. يتم التسجيل في المقررات من خلال لوحة التحكم<br>2. يبدأ التسجيل في بداية كل فصل دراسي<br>3. يمكن للطالب الانسحاب خلال الأسبوعين الأولين<br>4. لا يحق للطالب التسجيل في مستوى قبل إنهاء المستوى السابق<br>5. يتم إلحاق الطالب بالمستوى المناسب حسب معرفته السابقة', 'fiqhlearning'))); ?>
                        </div>
                    </section>

                    <!-- الحضور والغياب -->
                    <section id="attendance" class="regulation-section card">
                        <div class="regulation-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <h2><?php echo get_theme_mod('regulations_section_attendance_title', __('الحضور والغياب', 'fiqhlearning')); ?></h2>
                        <div class="regulation-content">
                            <?php echo wpautop(get_theme_mod('regulations_attendance', __('1. يُشترط حضور 75% من الدروس المقررة<br>2. في حالة تجاوز نسبة الغياب يُحرم الطالب من دخول الاختبار النهائي<br>3. يمكن تقديم عذر مقبول للإدارة في حالة الغياب الطارئ<br>4. يتم احتساب الحضور من خلال النظام الإلكتروني<br>5. الالتزام بمواعيد الدروس المحددة', 'fiqhlearning'))); ?>
                        </div>
                    </section>

                    <!-- الاختبارات -->
                    <section id="exams" class="regulation-section card">
                        <div class="regulation-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 11l3 3L22 4"></path>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                            </svg>
                        </div>
                        <h2><?php echo get_theme_mod('regulations_section_exams_title', __('الاختبارات', 'fiqhlearning')); ?></h2>
                        <div class="regulation-content">
                            <?php echo wpautop(get_theme_mod('regulations_exams', __('1. تُعقد اختبارات تقويمية خلال الفصل الدراسي<br>2. يُعقد اختبار نهائي في نهاية كل مقرر<br>3. يتم الإعلان عن مواعيد الاختبارات مسبقاً<br>4. يُشترط الحضور في وقت الاختبار المحدد<br>5. لا يُسمح بالغش أو التعاون في الاختبارات<br>6. يمكن طلب إعادة الاختبار في حالات خاصة', 'fiqhlearning'))); ?>
                        </div>
                    </section>

                    <!-- نظام الدرجات -->
                    <section id="grades" class="regulation-section card">
                        <div class="regulation-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                            </svg>
                        </div>
                        <h2><?php echo get_theme_mod('regulations_section_grades_title', __('نظام الدرجات', 'fiqhlearning')); ?></h2>
                        <div class="regulation-content">
                            <?php echo wpautop(get_theme_mod('regulations_grades', __('توزيع الدرجات:<br>• أعمال الفصل: 40 درجة<br>• الاختبار النهائي: 60 درجة<br>• المجموع الكلي: 100 درجة<br><br>درجة النجاح: 60 درجة من 100<br><br>التقديرات:<br>• امتياز: 90-100<br>• جيد جداً: 80-89<br>• جيد: 70-79<br>• مقبول: 60-69<br>• راسب: أقل من 60', 'fiqhlearning'))); ?>
                        </div>
                    </section>

                    <!-- السلوك والانضباط -->
                    <section id="behavior" class="regulation-section card">
                        <div class="regulation-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                        </div>
                        <h2><?php echo get_theme_mod('regulations_section_behavior_title', __('السلوك والانضباط', 'fiqhlearning')); ?></h2>
                        <div class="regulation-content">
                            <?php echo wpautop(get_theme_mod('regulations_behavior', __('1. الالتزام بالأخلاق الإسلامية<br>2. احترام المعلمين والزملاء<br>3. عدم الإساءة أو التعدي على الآخرين<br>4. الالتزام بآداب الحوار والنقاش<br>5. المحافظة على ممتلكات المدرسة<br>6. عدم نشر معلومات خاصة دون إذن<br>7. الالتزام بقواعد المنصة الإلكترونية', 'fiqhlearning'))); ?>
                        </div>
                    </section>

                    <!-- الشهادات -->
                    <section id="certificates" class="regulation-section card">
                        <div class="regulation-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <path d="M12 18v-6"></path>
                                <path d="M9 15l3 3 3-3"></path>
                            </svg>
                        </div>
                        <h2><?php echo get_theme_mod('regulations_section_certificates_title', __('الشهادات', 'fiqhlearning')); ?></h2>
                        <div class="regulation-content">
                            <?php echo wpautop(get_theme_mod('regulations_certificates', __('1. يحصل الطالب على شهادة إتمام لكل مقرر ينجح فيه<br>2. تُمنح شهادة إتمام المستوى عند إنهاء جميع مقررات المستوى<br>3. تُمنح شهادة التخرج عند إنهاء جميع المستويات<br>4. الشهادات معتمدة من المدرسة<br>5. يمكن طلب نسخة رقمية أو ورقية من الشهادة<br>6. تحتوي الشهادة على رقم مرجعي للتحقق', 'fiqhlearning'))); ?>
                        </div>
                    </section>

                    <!-- الحقوق والواجبات -->
                    <section id="rights" class="regulation-section card">
                        <div class="regulation-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                        </div>
                        <h2><?php echo get_theme_mod('regulations_section_rights_title', __('الحقوق والواجبات', 'fiqhlearning')); ?></h2>
                        <div class="regulation-content">
                            <?php echo wpautop(get_theme_mod('regulations_rights', __('<strong>حقوق الطالب:</strong><br>• الحصول على تعليم عالي الجودة<br>• الوصول إلى جميع المواد التعليمية<br>• طرح الأسئلة والحصول على الإجابات<br>• الحصول على تقييم عادل<br>• الحصول على الشهادات المستحقة<br><br><strong>واجبات الطالب:</strong><br>• الالتزام بمواعيد الدروس<br>• إنجاز الواجبات المطلوبة<br>• الاحترام والأدب مع الجميع<br>• الالتزام بلوائح المدرسة<br>• المشاركة الإيجابية في التعلم', 'fiqhlearning'))); ?>
                        </div>
                    </section>

                </div>

            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section cta-section-secondary">
        <div class="container">
            <div class="cta-box">
                <h2><?php echo get_theme_mod('regulations_cta_title', __('لديك استفسار؟', 'fiqhlearning')); ?></h2>
                <p><?php echo get_theme_mod('regulations_cta_desc', __('تواصل معنا للحصول على مزيد من المعلومات عن اللوائح والأنظمة', 'fiqhlearning')); ?></p>
                <div class="cta-actions">
                    <?php
                    $contact_page = get_page_by_path('contact');
                    if ($contact_page) :
                    ?>
                        <a href="<?php echo get_permalink($contact_page); ?>" class="btn btn-primary btn-lg">
                            <?php echo get_theme_mod('regulations_cta_contact_btn', __('اتصل بنا', 'fiqhlearning')); ?>
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo get_post_type_archive_link('fiqh_course'); ?>" class="btn btn-outline btn-lg">
                        <?php echo get_theme_mod('regulations_cta_courses_btn', __('تصفح المقررات', 'fiqhlearning')); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>

<script>
// Smooth scroll للقائمة الجانبية
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('.regulations-menu a');

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            // إزالة active من جميع الروابط
            navLinks.forEach(l => l.classList.remove('active'));

            // إضافة active للرابط الحالي
            this.classList.add('active');

            // Scroll إلى القسم
            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);

            if (targetSection) {
                targetSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // تحديث active link عند التمرير
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id');
                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === `#${id}`) {
                        link.classList.add('active');
                    }
                });
            }
        });
    }, {
        rootMargin: '-100px 0px -66%'
    });

    document.querySelectorAll('.regulation-section').forEach(section => {
        observer.observe(section);
    });
});
</script>

<?php
get_footer();
