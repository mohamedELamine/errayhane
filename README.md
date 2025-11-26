# تطبيق التعليم الفقهي - Android

تطبيق أندرويد بتقنية Flutter متصل بموقع التعليم الفقهي.

## نظرة عامة

هذا تطبيق أندرويد مستقل يتصل بـ WordPress REST API لموقع التعليم الفقهي، يوفر للطلبة:
- تسجيل دخول آمن
- عرض العلوم والمقررات حسب المستوى
- مشاهدة الدروس مع مشغل فيديو
- نظام أسئلة وأجوبة
- تتبع التقدم

## المواصفات التقنية

### Android
- **compileSdk:** 35
- **targetSdk:** 35
- **minSdk:** 24 (Android 7.0+)
- **NDK:** 27.0.12077973

### Build System
- **Gradle:** 8.9
- **AGP:** 8.7.3
- **Kotlin:** 2.0.21
- **Java:** 17

### Flutter
- **SDK:** >=3.5.0
- **Dart:** Latest stable

## التثبيت والتشغيل

### المتطلبات
- Flutter SDK >=3.5.0
- Java JDK 17
- Android Studio (اختياري)

### الخطوات

1. **تكوين الإعدادات:**
```bash
cd fiqh_learning_app
```

افتح `lib/utils/config.dart` وحدث رابط الموقع:
```dart
static const String baseUrl = 'https://yourwebsite.com';
```

2. **تثبيت الحزم:**
```bash
flutter pub get
```

3. **التشغيل:**
```bash
# للتطوير
flutter run

# بناء APK
flutter build apk --release

# بناء App Bundle
flutter build appbundle --release
```

## البنية

```
fiqh_learning_app/
├── lib/
│   ├── main.dart
│   ├── models/          # نماذج البيانات
│   ├── services/        # API و المصادقة
│   ├── screens/         # الشاشات
│   └── utils/           # الإعدادات
├── android/             # إعدادات Android
└── pubspec.yaml         # الحزم
```

## API Endpoints المطلوبة

يجب أن يوفر الموقع الـ endpoints التالية:

- `POST /wp-json/fiqh-lms/v1/auth/login`
- `GET /wp-json/fiqh-lms/v1/auth/user`
- `GET /wp-json/fiqh-lms/v1/sciences`
- `GET /wp-json/fiqh-lms/v1/courses/{id}`
- `GET /wp-json/fiqh-lms/v1/lessons`
- `GET /wp-json/fiqh-lms/v1/lessons/{id}`
- `POST /wp-json/fiqh-lms/v1/progress/update`
- `GET /wp-json/fiqh-lms/v1/questions`
- `POST /wp-json/fiqh-lms/v1/questions`

## الحزم المستخدمة

- provider ^6.1.2
- http ^1.2.2
- shared_preferences ^2.3.2
- youtube_player_flutter ^9.1.1
- video_player ^2.9.2
- cached_network_image ^3.4.1
- flutter_html ^3.0.0-beta.2
- url_launcher ^6.3.1
- webview_flutter ^4.10.0

## الإصدارات

- **الحالي:** 1.0.0+1
- **Min Android:** 7.0 (API 24)
- **Target Android:** 14+ (API 35)

## الترخيص

MIT License
