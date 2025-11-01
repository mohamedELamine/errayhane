# محتوى تجريبي لمنصة FiqhLearning
# Demo Content for FiqhLearning Platform

## 📋 نظرة عامة | Overview

هذا المجلد يحتوي على محتوى تجريبي كامل لمنصة FiqhLearning يمكن استيراده بطرق متعددة لاختبار المنصة.

This folder contains complete demo content for FiqhLearning platform that can be imported in multiple ways for testing purposes.

---

## 📦 محتويات الحزمة | Package Contents

### 1. **demo-content.sql**
- ملف SQL كامل يحتوي على جميع البيانات التجريبية
- Complete SQL file containing all demo data
- **الحجم:** ~50 KB

### 2. **demo-content.json**
- ملف JSON بتنسيق قياسي للاستيراد عبر REST API
- Standard JSON format for importing via REST API
- **الحجم:** ~35 KB

### 3. **README.md** (هذا الملف)
- دليل الاستيراد والاستخدام
- Import and usage guide

---

## 📊 ما يتضمنه المحتوى التجريبي | What's Included

| المحتوى | العدد | الوصف |
|---------|------|-------|
| 🏫 **المواد الدراسية** | 5 | الفقه، الحديث، التفسير، العقيدة، اللغة العربية |
| 📅 **السنوات الدراسية** | 4 | من السنة الأولى إلى الرابعة |
| 📆 **الفصول الدراسية** | 2 | الفصل الأول والثاني |
| 📚 **أنواع المقررات** | 3 | أساسي، تكميلي، إثرائي |
| 👥 **الدفعات** | 1 | دفعة 2024-2025 |
| 📖 **المقررات** | 1 | فقه العبادات - المذهب المالكي |
| 📝 **الدروس** | 5 | من مقدمة في الطهارة إلى أركان الصلاة |
| 👨‍🎓 **الطلاب** | 1 | طالب تجريبي (student_demo) |
| ❓ **الأسئلة والأجوبة** | 3 | سؤالان مُجاب عليهما + سؤال معلق |
| 🔔 **الإشعارات** | 3 | إشعارات ترحيبية وتعليمية |
| 📰 **المقالات** | 1 | مقالة عن أهمية الفقه المالكي |

---

## 🚀 طرق الاستيراد | Import Methods

### الطريقة 1: استيراد SQL مباشر (موصى بها)
**Recommended Method: Direct SQL Import**

#### عبر phpMyAdmin:
1. افتح phpMyAdmin
2. اختر قاعدة البيانات الخاصة بـ WordPress
3. اذهب إلى تبويب "Import" أو "استيراد"
4. اختر ملف `demo-content.sql`
5. تأكد من تحديد "UTF-8" كترميز
6. اضغط "Go" أو "تنفيذ"

#### عبر سطر الأوامر:
```bash
# استبدل القيم بمعلومات قاعدة بياناتك
mysql -u USERNAME -p DATABASE_NAME < demo-content.sql
```

#### عبر WP-CLI:
```bash
wp db import demo-content.sql
```

⚠️ **ملاحظات هامة:**
- تأكد من أن بادئة الجداول في الملف (`wp_`) تطابق بادئة قاعدة بياناتك
- قد تحتاج لتعديل IDs إذا كانت لديك بيانات موجودة مسبقاً
- قم بعمل نسخة احتياطية من قاعدة البيانات قبل الاستيراد

---

### الطريقة 2: عبر واجهة WordPress (الطريقة الآمنة)
**WordPress Interface Method (Safe)**

1. سجل دخول إلى لوحة تحكم WordPress
2. اذهب إلى: **أدوات** ← **محتوى تجريبي** (Tools → Demo Content)
3. اضغط على زر "إنشاء المحتوى التجريبي"
4. انتظر حتى اكتمال العملية

> 📝 **ملاحظة:** هذه الطريقة تستخدم السكريبت المدمج في `demo-content.php`

---

### الطريقة 3: استيراد عبر REST API (للمطورين)
**REST API Import (For Developers)**

استخدم ملف `demo-content.json` للاستيراد البرمجي:

```php
// مثال على استيراد المقرر
$course_data = json_decode(file_get_contents('demo-content.json'), true);

foreach ($course_data['courses'] as $course) {
    $post_id = wp_insert_post([
        'post_title' => $course['title'],
        'post_content' => $course['content'],
        'post_type' => 'fiqh_course',
        'post_status' => 'publish'
    ]);

    // إضافة Meta Data
    foreach ($course['meta'] as $key => $value) {
        update_post_meta($post_id, '_fiqh_course_' . $key, $value);
    }
}
```

أو عبر REST API:

```bash
# استيراد عبر cURL
curl -X POST "https://yoursite.com/wp-json/fiqh/v1/import" \
  -H "Content-Type: application/json" \
  -d @demo-content.json
```

---

## 🔐 بيانات الدخول | Login Credentials

### الطالب التجريبي | Demo Student
- **اسم المستخدم:** `student_demo`
- **كلمة المرور:** `demo123`
- **البريد الإلكتروني:** `student@demo.local`
- **الدور:** طالب (Student)

### حساب المدير | Administrator
- استخدم حساب المدير الموجود في موقعك
- Use your existing administrator account

---

## 📝 تفاصيل المحتوى | Content Details

### 1. المقرر: فقه العبادات
**Course: Fiqh Al-Ibadat (Maliki Madhab)**

| رقم الدرس | العنوان | المدة | الحالة |
|-----------|---------|------|--------|
| 1 | مقدمة في الطهارة | 45 دقيقة | ✅ مكتمل |
| 2 | الوضوء وأحكامه | 50 دقيقة | ✅ مكتمل |
| 3 | الغسل والتيمم | 55 دقيقة | 🔄 قيد التقدم (45%) |
| 4 | شروط الصلاة | 60 دقيقة | ⏳ لم يبدأ |
| 5 | أركان الصلاة وواجباتها | 65 دقيقة | ⏳ لم يبدأ |

### 2. الأسئلة والأجوبة
**Questions & Answers**

1. **سؤال مجاب عليه ومميز:** شروط صحة الوضوء (15 مشاهدة)
2. **سؤال مجاب عليه:** هل يجب الدلك في الوضوء؟ (8 مشاهدات)
3. **سؤال معلق:** الفرق بين الحدث الأصغر والأكبر (3 مشاهدات)

### 3. تقدم الطالب
**Student Progress**

- الدروس المكتملة: 2/5 (40%)
- الدرس الحالي: الغسل والتيمم (45% مكتمل)
- التسجيل في المقرر: نشط
- الدفعة: 2024-2025

---

## 🔧 استكشاف الأخطاء | Troubleshooting

### ❌ خطأ: "Duplicate entry"
**السبب:** IDs موجودة مسبقاً في قاعدة البيانات
**الحل:**
1. احذف البيانات التجريبية القديمة أولاً
2. أو عدّل IDs في ملف SQL

```sql
-- حذف البيانات التجريبية
DELETE FROM wp_posts WHERE ID BETWEEN 1000 AND 2000;
DELETE FROM wp_users WHERE ID = 100;
DELETE FROM wp_fiqh_batches WHERE id = 1;
```

### ❌ خطأ: "Table doesn't exist"
**السبب:** جداول قاعدة البيانات المخصصة غير موجودة
**الحل:** فعّل الإضافة أولاً لإنشاء الجداول

```bash
# عبر WP-CLI
wp plugin activate fiqhlearning-lms
```

### ❌ خطأ: "Unknown collation"
**السبب:** ترميز قاعدة البيانات غير متوافق
**الحل:** عدّل الترميز في ملف SQL

```sql
-- غيّر من
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci

-- إلى
CHARACTER SET utf8 COLLATE utf8_general_ci
```

### ❌ مشكلة: الصور والملفات المرفقة غير ظاهرة
**السبب:** الروابط التجريبية تشير إلى Archive.org
**الحل:** استبدل الروابط بملفاتك الخاصة

---

## 🔄 تحديث المحتوى | Updating Content

لتحديث المحتوى التجريبي بعد التعديل:

```bash
# 1. حذف البيانات القديمة
wp db query "DELETE FROM wp_posts WHERE ID BETWEEN 1000 AND 2000"

# 2. استيراد البيانات الجديدة
wp db import demo-content.sql

# 3. تحديث الروابط الداخلية
wp search-replace 'example.com' 'yoursite.com'
```

---

## 🗑️ حذف المحتوى التجريبي | Removing Demo Content

### الطريقة 1: SQL
```sql
-- حذف المنشورات التجريبية
DELETE FROM wp_posts WHERE ID BETWEEN 1000 AND 2100;
DELETE FROM wp_postmeta WHERE post_id BETWEEN 1000 AND 2100;

-- حذف المستخدمين التجريبيين
DELETE FROM wp_users WHERE ID = 100;
DELETE FROM wp_usermeta WHERE user_id = 100;

-- حذف البيانات المخصصة
DELETE FROM wp_fiqh_batches WHERE id = 1;
DELETE FROM wp_fiqh_enrollments WHERE user_id = 100;
DELETE FROM wp_fiqh_progress WHERE user_id = 100;
DELETE FROM wp_fiqh_questions WHERE user_id = 100;
DELETE FROM wp_fiqh_notifications WHERE user_id = 100;

-- حذف التصنيفات التجريبية
DELETE FROM wp_terms WHERE term_id BETWEEN 100 AND 410;
DELETE FROM wp_term_taxonomy WHERE term_id BETWEEN 100 AND 410;
```

### الطريقة 2: عبر WordPress
1. احذف المقرر "فقه العبادات" من المقررات
2. احذف الدروس الخمسة من الدروس
3. احذف المستخدم "student_demo"
4. احذف الدفعة "2024-2025" من لوحة الدفعات

---

## 📚 الموارد الإضافية | Additional Resources

### الروابط المستخدمة في المحتوى التجريبي
- **كتاب PDF تجريبي:** [Archive.org - Islamic Book](https://archive.org/download/FP22640/22640.pdf)
- **ملف صوتي تجريبي:** [Archive.org - Sample Audio](https://archive.org/download/sample-audio/sample-audio.mp3)
- **فيديو تجريبي:** [YouTube Sample](https://www.youtube.com/watch?v=dQw4w9WgXcQ)

### استبدال الروابط بملفاتك الخاصة

```sql
-- تحديث روابط PDF
UPDATE wp_postmeta
SET meta_value = 'https://yoursite.com/uploads/your-book.pdf'
WHERE meta_key = '_fiqh_lesson_pdf_url';

-- تحديث روابط الصوت
UPDATE wp_postmeta
SET meta_value = 'https://yoursite.com/uploads/your-audio.mp3'
WHERE meta_key = '_fiqh_lesson_audio_url';

-- تحديث روابط الفيديو
UPDATE wp_postmeta
SET meta_value = 'https://www.youtube.com/watch?v=YOUR_VIDEO_ID'
WHERE meta_key = '_fiqh_lesson_video_url';
```

---

## 📞 الدعم الفني | Support

إذا واجهت أي مشاكل في الاستيراد:

1. تحقق من **متطلبات النظام:**
   - WordPress 6.0+
   - PHP 7.4+
   - MySQL 5.7+ أو MariaDB 10.3+

2. تأكد من **تفعيل:**
   - قالب FiqhLearning
   - إضافة FiqhLearning LMS

3. راجع **سجل الأخطاء:**
   ```bash
   tail -f wp-content/debug.log
   ```

4. **اتصل بنا:**
   - GitHub Issues: [errayhane/issues](https://github.com/mohamedELamine/errayhane/issues)
   - البريد الإلكتروني: support@fiqhlearning.com

---

## 📄 الترخيص | License

هذا المحتوى التجريبي مرخص تحت GPL v2 أو أحدث، مثل WordPress نفسه.

This demo content is licensed under GPL v2 or later, just like WordPress itself.

---

## ✅ قائمة التحقق | Checklist

قبل الاستيراد:
- [ ] عمل نسخة احتياطية من قاعدة البيانات
- [ ] تفعيل القالب والإضافة
- [ ] التحقق من بادئة الجداول
- [ ] مراجعة متطلبات النظام

بعد الاستيراد:
- [ ] اختبار تسجيل الدخول بحساب الطالب التجريبي
- [ ] التحقق من ظهور المقرر والدروس
- [ ] اختبار تشغيل الفيديو والصوت
- [ ] مراجعة الأسئلة والإشعارات
- [ ] التحقق من التقدم والإحصائيات

---

## 🎉 جاهز للبدء!

الآن يمكنك اختبار جميع ميزات منصة FiqhLearning بمحتوى واقعي ومتكامل!

**Now you're ready to test all FiqhLearning platform features with realistic, complete content!**

---

**آخر تحديث:** 2024-11-01
**الإصدار:** 1.0.0
**الحالة:** ✅ جاهز للاستخدام
