# منصة المسابقات - تطبيق Flutter

تطبيق المشاركين لمنصة المسابقات المبنية بـ Laravel 13.

## المتطلبات

- Flutter SDK >= 3.6.0
- Laravel Backend يعمل على المنفذ 8000

## الإعداد

```bash
cd mobile
flutter pub get
```

## تشغيل التطبيق

```bash
flutter run
```

## إعدادات الـ API

عدّل `lib/core/constants/app_constants.dart` لتغيير عنوان الـ Backend:

```dart
static const String baseUrl = 'http://10.0.2.2:8000/api';  // للمحاكي
static const String baseUrl = 'http://localhost:8000/api';  // للأجهزة الفعلية
```

## الميزات

- **المصادقة:** تسجيل دخول، إنشاء حساب، استعادة كلمة المرور
- **المسابقات:** تصفح، بحث، فلترة، تفاصيل، النتائج
- **المشاركات:** إرسال (صورة/PDF/نص/رابط)، مشاركاتي، تفاصيل
- **الإشعارات:** إشعارات داخل التطبيق، حالة مقروء/غير مقروء
- **الملف الشخصي:** عرض، تعديل، تغيير كلمة المرور

## التقنيات

- Flutter + Material 3
- Riverpod (State Management)
- Dio (HTTP Client)
- go_router (Navigation)
- SharedPreferences (Token Storage)
- flutter_local_notifications (In-App Notifications)
- image_picker / file_picker (File Uploads)

## البنية

```
lib/
  core/
    api/
    constants/
    router/
    services/
    theme/
    widgets/
  features/
    auth/
    competitions/
    submissions/
    notifications/
    profile/
```
