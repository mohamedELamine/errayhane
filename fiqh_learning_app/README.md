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

- **Flutter SDK:** >=3.5.0
- **Android Studio:** Arctic Fox أو أحدث
- **Java JDK:** 17 أو أعلى
- **Android SDK:** API 35
- **NDK:** 27.0.12077973 أو أحدث
- **Gradle:** 8.9
- **حساب:** مسجل على موقع التعليم الفقهي

## التثبيت

### 1. استنساخ المشروع
```bash
git clone <repository-url>
cd fiqh_learning_app
```

### 2. تكوين الإعدادات
افتح ملف `lib/utils/config.dart` وقم بتغيير `baseUrl` إلى رابط موقعك:
```dart
static const String baseUrl = 'https://yourwebsite.com';
```

### 3. تثبيت الحزم
```bash
flutter pub get
```

### 4. تشغيل التطبيق
```bash
# للتطوير
flutter run

# للإنتاج
flutter build apk --release
# أو
flutter build appbundle --release
```

## بنية المشروع

```
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
```

## المواصفات التقنية

### Android
- **compileSdk:** 35
- **targetSdk:** 35
- **minSdk:** 24 (Android 7.0+)
- **NDK:** 27.0.12077973
- **AGP:** 8.7.3
- **Kotlin:** 2.0.21
- **Java Target:** 17

### Build System
- **Gradle:** 8.9
- **Gradle Plugin:** 8.7.3
- **Kotlin Gradle Plugin:** 2.0.21

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

- `provider` ^6.1.2 - إدارة الحالة
- `http` ^1.2.2 - الاتصال بالـ API
- `shared_preferences` ^2.3.2 - التخزين المحلي
- `youtube_player_flutter` ^9.1.1 - مشغل فيديو YouTube
- `video_player` ^2.9.2 - مشغل فيديو عام
- `cached_network_image` ^3.4.1 - تخزين الصور مؤقتاً
- `flutter_html` ^3.0.0-beta.2 - عرض محتوى HTML
- `url_launcher` ^6.3.1 - فتح الروابط
- `webview_flutter` ^4.10.0 - عرض صفحات ويب

## ملاحظات مهمة

### قبل البناء
1. ✅ تأكد من تحديث `baseUrl` في `lib/utils/config.dart`
2. ✅ تأكد من أن WordPress plugin مثبت ومفعل
3. ✅ تأكد من تفعيل HTTPS على الموقع للأمان
4. ✅ للإنتاج، استخدم JWT بدلاً من Basic Authentication

### متطلبات النظام
- **Java:** 17 أو أعلى (تحقق بـ `java -version`)
- **Flutter:** أحدث إصدار مستقر (تحقق بـ `flutter --version`)
- **Android SDK:** API Level 35
- **NDK:** 27.0.12077973

### تحسينات الأداء
- تم تفعيل Gradle Parallel Builds
- تم تفعيل Build Cache
- تم تحسين memory allocation
- دعم كامل لـ Java 17

## إصدارات التطبيق

- **النسخة الحالية:** 1.0.0+1
- **Minimum Android:** 7.0 (API 24)
- **Target Android:** 14+ (API 35)

## الترخيص

هذا المشروع مرخص بموجب رخصة MIT.
