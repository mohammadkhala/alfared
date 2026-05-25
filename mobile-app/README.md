# تطبيق أبناء الفريد — Flutter App

تطبيق الموبايل للمتجر الإلكتروني (iOS + Android).

## المتطلبات

- Flutter SDK ≥ 3.22
- Dart ≥ 3.4
- Android Studio أو VS Code مع Flutter plugin
- Xcode (لـ iOS فقط)
- حساب Firebase للإشعارات

## التثبيت السريع

```bash
cd mobile-app

# 1) ثبت الـ dependencies
flutter pub get

# 2) أنشئ منصات النظام (مرة واحدة)
flutter create . --org=com.alfared --project-name=alfared_app

# 3) شغّل المشروع
flutter run --dart-define=API_BASE_URL=http://10.0.2.2:8001   # Android emulator
flutter run --dart-define=API_BASE_URL=http://127.0.0.1:8001  # iOS simulator
flutter run --dart-define=API_BASE_URL=http://192.168.1.X:8001 # جهاز حقيقي على نفس الشبكة
```

## بنية المشروع

```
lib/
├── config/           # api_config.dart — base URL
├── models/           # User, Product, Category, Order
├── services/         # api_service (Dio), fcm_service (Firebase)
├── providers/        # AuthProvider, CartProvider
├── theme/            # AppTheme + AppColors
├── widgets/          # ProductCard reusable
└── screens/
    ├── splash_screen.dart
    ├── auth/         # Login + Register
    ├── home/         # Home + Bottom navigation
    ├── products/     # Products list + Detail
    ├── cart/         # Cart
    ├── checkout/     # Checkout + Success
    ├── orders/       # My orders + Detail/tracking
    └── account/      # Account + Wishlist + Loyalty
```

## إعداد Firebase (للإشعارات)

1. اذهب إلى [Firebase Console](https://console.firebase.google.com)
2. أنشئ مشروع جديد
3. أضف تطبيق Android — package: `com.alfared.alfared_app`
   - نزّل `google-services.json` وضعه في `android/app/`
4. أضف تطبيق iOS — bundle: `com.alfared.alfaredApp`
   - نزّل `GoogleService-Info.plist` وأضفه عبر Xcode
5. من Firebase → Project Settings → Cloud Messaging — انسخ **Server key**
6. في ملف `.env` للـ Laravel، أضف:
   ```
   FCM_SERVER_KEY=your-server-key-here
   ```

## بناء النسخة النهائية

```bash
# Android — APK للتجريب
flutter build apk --release

# Android — AAB لـ Play Store
flutter build appbundle --release

# iOS — يحتاج Mac + Apple Developer account
flutter build ios --release
```

## الميزات

- ✅ تسجيل دخول + تسجيل بحساب جديد (مع +970/+972)
- ✅ تصفّح المنتجات والأقسام والـ Banners
- ✅ بحث + فلاتر ترتيب
- ✅ تفاصيل المنتج مع معرض صور + variants + تقييمات
- ✅ سلة متزامنة مع الخادم
- ✅ Checkout مع كوبون + نقاط ولاء + مناطق توصيل
- ✅ تتبع الطلبات بـ timeline تفاعلي
- ✅ المفضلة + نقاط الولاء + سجل المعاملات
- ✅ Push Notifications عند تغيير حالة الطلب
- ✅ RTL كامل + خط Cairo
- ✅ يحدد نفسه كتطبيق في تحليلات الزوار (Backend)

## الخطوط

ضع ملفات الخط في `assets/fonts/`:
- Cairo-Regular.ttf
- Cairo-Bold.ttf
- Cairo-Black.ttf

تنزيل: [Google Fonts — Cairo](https://fonts.google.com/specimen/Cairo)
