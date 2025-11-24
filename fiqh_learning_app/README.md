# تطبيق التعليم الفقهي - Flutter

تطبيق أندرويد متصل بموقع التعليم الفقهي يتيح للطلبة الوصول إلى الدروس والمقررات الدراسية.

## المميزات

- 🔐 تسجيل دخول آمن للطلبة المسجلين على الموقع
- 📚 عرض العلوم والمقررات حسب مستوى الطالب
- 📖 عرض الدروس بشكل منظم ومرتب
- 🎥 مشغل فيديو YouTube متكامل
- ❓ نظام أسئلة وأجوبة مزامن مع الموقع
- 📊 تتبع تقدم الطالب في الدروس
- 🔄 مزامنة كاملة مع الموقع

## المتطلبات

- Flutter SDK (>=3.0.0)
- Android Studio أو VS Code
- حساب مسجل على موقع التعليم الفقهي

## التثبيت

1. استنساخ المشروع:
\`\`\`bash
git clone <repository-url>
cd fiqh_learning_app
\`\`\`

2. تثبيت الحزم:
\`\`\`bash
flutter pub get
\`\`\`

3. تكوين الإعدادات:
افتح ملف `lib/utils/config.dart` وقم بتغيير `baseUrl` إلى رابط موقعك:
\`\`\`dart
static const String baseUrl = 'https://yourwebsite.com';
\`\`\`

4. تشغيل التطبيق:
\`\`\`bash
flutter run
\`\`\`

## بنية المشروع

\`\`\`
lib/
├── main.dart                 # نقطة البداية
├── models/                   # نماذج البيانات
│   ├── user_model.dart
│   ├── science_model.dart
│   ├── course_model.dart
│   ├── lesson_model.dart
│   └── question_model.dart
├── services/                 # الخدمات
│   ├── api_service.dart      # خدمة API
│   └── auth_provider.dart    # مزود المصادقة
├── screens/                  # الشاشات
│   ├── login_screen.dart
│   ├── home_screen.dart
│   ├── courses_screen.dart
│   ├── lessons_screen.dart
│   └── lesson_detail_screen.dart
├── widgets/                  # الويدجتس المخصصة
└── utils/                    # الأدوات المساعدة
    └── config.dart           # الإعدادات
\`\`\`

## API Endpoints

التطبيق يستخدم WordPress REST API التالي:

- `POST /wp-json/fiqh-lms/v1/auth/login` - تسجيل الدخول
- `GET /wp-json/fiqh-lms/v1/auth/user` - بيانات المستخدم
- `GET /wp-json/fiqh-lms/v1/sciences` - العلوم والمقررات
- `GET /wp-json/fiqh-lms/v1/courses/{id}` - تفاصيل المقرر
- `GET /wp-json/fiqh-lms/v1/lessons` - الدروس
- `GET /wp-json/fiqh-lms/v1/lessons/{id}` - تفاصيل الدرس
- `POST /wp-json/fiqh-lms/v1/progress/update` - تحديث التقدم
- `GET /wp-json/fiqh-lms/v1/questions` - الأسئلة
- `POST /wp-json/fiqh-lms/v1/questions` - إضافة سؤال

## الحزم المستخدمة

- `provider` - إدارة الحالة
- `http` - الاتصال بالـ API
- `shared_preferences` - التخزين المحلي
- `youtube_player_flutter` - مشغل فيديو YouTube
- `cached_network_image` - تخزين الصور مؤقتاً
- `flutter_html` - عرض محتوى HTML
- `url_launcher` - فتح الروابط

## ملاحظات مهمة

1. تأكد من تحديث `baseUrl` في `config.dart` قبل البناء
2. تأكد من أن WordPress plugin مثبت ومفعل على الموقع
3. تأكد من تفعيل HTTPS على الموقع للأمان
4. للإنتاج، استخدم JWT بدلاً من Basic Authentication

## الترخيص

هذا المشروع مرخص بموجب رخصة MIT.
