# دليل الإعداد - تطبيق التعليم الفقهي

## المتطلبات الأساسية

### 1. Flutter SDK
قم بتثبيت Flutter من الموقع الرسمي:
- [تحميل Flutter](https://flutter.dev/docs/get-started/install)
- تأكد من إضافة Flutter إلى PATH

### 2. Android Studio / VS Code
- Android Studio (مفضل) أو VS Code
- Android SDK (API Level 34)
- Java JDK 17 أو أعلى

### 3. التحقق من التثبيت
```bash
flutter doctor -v
```

يجب أن تشاهد:
- ✓ Flutter
- ✓ Android toolchain
- ✓ Connected device

## خطوات الإعداد

### 1. استنساخ المشروع
```bash
git clone <repository-url>
cd fiqh_learning_app
```

### 2. تثبيت الحزم
```bash
flutter pub get
```

### 3. إعداد ملف local.properties (مهم!)

أنشئ أو عدّل ملف `android/local.properties` وأضف:

**على MacOS/Linux:**
```properties
sdk.dir=/Users/YourUsername/Library/Android/sdk
flutter.sdk=/Users/YourUsername/flutter
```

**على Windows:**
```properties
sdk.dir=C\:\\Users\\YourUsername\\AppData\\Local\\Android\\sdk
flutter.sdk=C\:\\src\\flutter
```

للحصول على المسارات الصحيحة، قم بتشغيل:
```bash
flutter doctor -v
```

### 4. تحديث رابط الموقع

افتح `lib/utils/config.dart` وغيّر:
```dart
static const String baseUrl = 'https://yourwebsite.com';
```

### 5. تشغيل التطبيق

**على محاكي/جهاز:**
```bash
flutter run
```

**بناء APK للإنتاج:**
```bash
flutter build apk --release
```

**بناء App Bundle:**
```bash
flutter build appbundle --release
```

## حل المشاكل الشائعة

### مشكلة Gradle/Java Version

إذا واجهت خطأ:
```
Unsupported class file major version 65
```

**الحل:**
1. تأكد من استخدام Java 17 أو أعلى:
```bash
java -version
```

2. إذا كنت تستخدم Java أقدم، قم بتحديثه أو استخدم JAVA_HOME:
```bash
export JAVA_HOME=/path/to/java17
```

### مشكلة Flutter SDK not found

تأكد من إضافة Flutter إلى PATH:
```bash
export PATH="$PATH:/path/to/flutter/bin"
```

### مشكلة Android SDK not found

تأكد من وجود ملف `android/local.properties` مع المسار الصحيح.

### مشكلة Build Failed

1. نظف المشروع:
```bash
flutter clean
flutter pub get
cd android && ./gradlew clean
cd ..
flutter run
```

2. حذف مجلدات البناء:
```bash
rm -rf build/
rm -rf android/.gradle/
rm -rf android/app/build/
```

## هيكل المشروع

```
fiqh_learning_app/
├── lib/
│   ├── main.dart                 # نقطة البدء
│   ├── models/                   # نماذج البيانات
│   ├── services/                 # خدمات API والمصادقة
│   ├── screens/                  # شاشات التطبيق
│   └── utils/                    # الأدوات المساعدة
├── android/                      # إعدادات Android
├── pubspec.yaml                  # الحزم والتبعيات
└── README.md                     # التوثيق
```

## إعدادات الإنتاج

### 1. تغيير اسم التطبيق
في `android/app/src/main/AndroidManifest.xml`:
```xml
android:label="التعليم الفقهي"
```

### 2. تغيير Package Name
في `android/app/build.gradle`:
```gradle
applicationId "com.fiqhlearning.fiqh_learning_app"
```

### 3. إعداد التوقيع (Signing)

1. أنشئ keystore:
```bash
keytool -genkey -v -keystore ~/upload-keystore.jks -keyalg RSA -keysize 2048 -validity 10000 -alias upload
```

2. أنشئ ملف `android/key.properties`:
```properties
storePassword=<password>
keyPassword=<password>
keyAlias=upload
storeFile=<path-to-keystore>
```

3. عدّل `android/app/build.gradle` لاستخدام التوقيع.

## الدعم

للمزيد من المساعدة:
- [Flutter Documentation](https://flutter.dev/docs)
- [Flutter Cookbook](https://flutter.dev/docs/cookbook)
- [Android Documentation](https://developer.android.com/docs)
