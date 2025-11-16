/**
 * السكربتات الرئيسية - FiqhLearning Theme
 * الإصدار: 1.0.0
 */

(function($) {
    'use strict';

    // التهيئة عند تحميل الصفحة
    $(document).ready(function() {
        initDarkMode();
        initSearchModal();
        initBackToTop();
        initMenuToggle();
        initLessonProgress();
        initQuestionForm();
        initPDFViewer();
    });

    /**
     * تفعيل الوضع الليلي
     */
    function initDarkMode() {
        const darkModeToggle = $('.dark-mode-toggle');
        const body = $('body');

        // التحقق من الحالة المحفوظة
        if (localStorage.getItem('fiqh_dark_mode') === 'true') {
            body.addClass('dark-mode');
            $('.moon-icon').show();
            $('.sun-icon').hide();
        }

        darkModeToggle.on('click', function() {
            body.toggleClass('dark-mode');
            const isDark = body.hasClass('dark-mode');

            // حفظ الحالة
            localStorage.setItem('fiqh_dark_mode', isDark);
            document.cookie = 'fiqh_dark_mode=' + isDark + '; path=/; max-age=31536000';

            // تبديل الأيقونات
            if (isDark) {
                $('.sun-icon').hide();
                $('.moon-icon').show();
            } else {
                $('.sun-icon').show();
                $('.moon-icon').hide();
            }
        });
    }

    /**
     * مربع البحث المنبثق
     */
    function initSearchModal() {
        const searchToggle = $('.search-toggle');
        const searchModal = $('.search-modal');
        const searchModalClose = $('.search-modal-close');

        searchToggle.on('click', function() {
            searchModal.fadeIn(300);
            $('.search-field').focus();
        });

        searchModalClose.on('click', function() {
            searchModal.fadeOut(300);
        });

        searchModal.on('click', function(e) {
            if ($(e.target).is('.search-modal')) {
                searchModal.fadeOut(300);
            }
        });

        // إغلاق بالضغط على ESC
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && searchModal.is(':visible')) {
                searchModal.fadeOut(300);
            }
        });
    }

    /**
     * زر العودة للأعلى
     */
    function initBackToTop() {
        const backToTopBtn = $('#back-to-top');

        $(window).on('scroll', function() {
            if ($(this).scrollTop() > 300) {
                backToTopBtn.fadeIn();
            } else {
                backToTopBtn.fadeOut();
            }
        });

        backToTopBtn.on('click', function() {
            $('html, body').animate({ scrollTop: 0 }, 600);
        });
    }

    /**
     * قائمة الموبايل
     */
    function initMenuToggle() {
        const menuToggle = $('.menu-toggle');
        const mainNavigation = $('.main-navigation');
        const body = $('body');

        // فتح/إغلاق القائمة
        menuToggle.on('click', function(e) {
            e.stopPropagation();
            $(this).toggleClass('active');
            mainNavigation.toggleClass('active');
            body.toggleClass('menu-open');
        });

        // إغلاق القائمة عند النقر على overlay
        body.on('click', function(e) {
            if (body.hasClass('menu-open') && !$(e.target).closest('.main-navigation, .menu-toggle').length) {
                menuToggle.removeClass('active');
                mainNavigation.removeClass('active');
                body.removeClass('menu-open');
            }
        });

        // إغلاق القائمة عند النقر على رابط
        $('.nav-menu a').on('click', function() {
            menuToggle.removeClass('active');
            mainNavigation.removeClass('active');
            body.removeClass('menu-open');
        });

        // إغلاق القائمة بزر ESC
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && body.hasClass('menu-open')) {
                menuToggle.removeClass('active');
                mainNavigation.removeClass('active');
                body.removeClass('menu-open');
            }
        });
    }

    /**
     * تتبع تقدم الدرس
     */
    function initLessonProgress() {
        if (!$('body').hasClass('single-fiqh_lesson')) {
            return;
        }

        const lessonId = $('body').data('lesson-id');
        const video = $('iframe[src*="youtube"]');

        // تتبع مشاهدة الفيديو (يمكن تحسينه باستخدام YouTube API)
        if (video.length) {
            // إرسال تحديث التقدم عند الخروج من الصفحة
            $(window).on('beforeunload', function() {
                updateProgress(lessonId, 50); // نسبة افتراضية
            });
        }

        // زر إكمال الدرس
        $('.complete-lesson-btn').on('click', function() {
            completeLesson(lessonId);
        });
    }

    /**
     * تحديث التقدم عبر AJAX
     */
    function updateProgress(lessonId, percentage) {
        $.ajax({
            url: fiqhData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'fiqh_update_progress',
                nonce: fiqhData.nonce,
                lesson_id: lessonId,
                percentage: percentage
            },
            success: function(response) {
                if (response.success) {
                    console.log('Progress updated');
                }
            }
        });
    }

    /**
     * إكمال الدرس
     */
    function completeLesson(lessonId) {
        $.ajax({
            url: fiqhData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'fiqh_complete_lesson',
                nonce: fiqhData.nonce,
                lesson_id: lessonId
            },
            success: function(response) {
                if (response.success) {
                    showNotification('success', 'تم إكمال الدرس بنجاح!');
                    location.reload();
                } else {
                    showNotification('error', response.data.message);
                }
            }
        });
    }

    /**
     * نموذج الأسئلة
     */
    function initQuestionForm() {
        // نموذج إضافة سؤال في الدرس
        $('#add-lesson-question-form').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const lessonId = $('.single-lesson-page').data('lesson-id');
            const questionContent = form.find('#question-content').val();

            // تعطيل الزر أثناء الإرسال
            submitBtn.prop('disabled', true).text('جاري الإرسال...');

            $.ajax({
                url: fiqhData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'fiqh_add_lesson_question',
                    nonce: fiqhData.nonce,
                    lesson_id: lessonId,
                    question: questionContent
                },
                success: function(response) {
                    if (response.success) {
                        showNotification('success', response.data.message);
                        form[0].reset();
                        // إعادة تحميل الصفحة لعرض السؤال الجديد
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        var errorMsg = (response.data && response.data.message) ? response.data.message : 'حدث خطأ أثناء إرسال السؤال';
                        showNotification('error', errorMsg);
                        submitBtn.prop('disabled', false).html('<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg> إرسال السؤال');
                    }
                },
                error: function() {
                    showNotification('error', 'حدث خطأ في الاتصال');
                    submitBtn.prop('disabled', false).html('<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg> إرسال السؤال');
                }
            });
        });

        // نموذج الإجابة على سؤال
        $(document).on('submit', '.add-answer-form', function(e) {
            e.preventDefault();

            const form = $(this);
            const questionId = form.data('question-id');
            const answerContent = form.find('textarea[name="answer_content"]').val();
            const submitBtn = form.find('button[type="submit"]');

            // تعطيل الزر أثناء الإرسال
            submitBtn.prop('disabled', true).text('جاري الإرسال...');

            $.ajax({
                url: fiqhData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'fiqh_add_lesson_answer',
                    nonce: fiqhData.nonce,
                    question_id: questionId,
                    answer: answerContent
                },
                success: function(response) {
                    if (response.success) {
                        showNotification('success', response.data.message);
                        // إعادة تحميل الصفحة لعرض الإجابة
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        var errorMsg = (response.data && response.data.message) ? response.data.message : 'حدث خطأ أثناء إرسال الإجابة';
                        showNotification('error', errorMsg);
                        submitBtn.prop('disabled', false).text('إرسال الإجابة');
                    }
                },
                error: function() {
                    showNotification('error', 'حدث خطأ في الاتصال');
                    submitBtn.prop('disabled', false).text('إرسال الإجابة');
                }
            });
        });

        // نموذج الأسئلة القديم (للصفحات الأخرى)
        $('#add-question-form').on('submit', function(e) {
            e.preventDefault();

            const formData = $(this).serialize();

            $.ajax({
                url: fiqhData.ajaxUrl,
                type: 'POST',
                data: formData + '&action=fiqh_add_question&nonce=' + fiqhData.nonce,
                success: function(response) {
                    if (response.success) {
                        showNotification('success', response.data.message);
                        $('#add-question-form')[0].reset();
                        $('#add-question-modal').fadeOut();
                    } else {
                        showNotification('error', response.data.message);
                    }
                }
            });
        });
    }

    /**
     * عرض ملفات PDF
     */
    function initPDFViewer() {
        $('.view-pdf-btn').on('click', function(e) {
            e.preventDefault();
            const pdfUrl = $(this).data('pdf-url');
            const pdfTitle = $(this).data('pdf-title') || 'عرض PDF';

            // إنشاء modal لعرض PDF
            const modal = $('<div class="pdf-modal">').html(`
                <div class="pdf-modal-content">
                    <div class="pdf-modal-header">
                        <h3>${pdfTitle}</h3>
                        <button class="pdf-modal-close">&times;</button>
                    </div>
                    <div class="pdf-modal-body">
                        <canvas id="pdf-canvas"></canvas>
                    </div>
                </div>
            `);

            $('body').append(modal);
            modal.fadeIn();

            // تحميل PDF باستخدام PDF.js
            if (typeof pdfjsLib !== 'undefined') {
                loadPDF(pdfUrl);
            } else {
                $('.pdf-modal-body').html('<iframe src="' + pdfUrl + '" width="100%" height="600px"></iframe>');
            }

            // إغلاق Modal
            modal.find('.pdf-modal-close').on('click', function() {
                modal.fadeOut(function() {
                    modal.remove();
                });
            });
        });
    }

    /**
     * تحميل PDF باستخدام PDF.js
     */
    function loadPDF(url) {
        // هذه وظيفة مبسطة - يمكن تحسينها
        const canvas = document.getElementById('pdf-canvas');
        const context = canvas.getContext('2d');

        pdfjsLib.getDocument(url).promise.then(function(pdf) {
            pdf.getPage(1).then(function(page) {
                const viewport = page.getViewport({ scale: 1.5 });
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                const renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };

                page.render(renderContext);
            });
        });
    }

    /**
     * عرض الإشعارات
     */
    function showNotification(type, message) {
        const notification = $('<div class="notification notification-' + type + '">').text(message);

        $('body').append(notification);
        notification.fadeIn().delay(3000).fadeOut(function() {
            $(this).remove();
        });
    }

    /**
     * المفضلة
     */
    $('.favorite-btn').on('click', function() {
        const itemId = $(this).data('item-id');
        const itemType = $(this).data('item-type');
        const btn = $(this);

        $.ajax({
            url: fiqhData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'fiqh_toggle_favorite',
                nonce: fiqhData.nonce,
                item_id: itemId,
                item_type: itemType
            },
            success: function(response) {
                if (response.success) {
                    btn.toggleClass('active');
                    showNotification('success', response.data.message);
                }
            }
        });
    });

})(jQuery);
