# تطبيق أبناء الفريد — Flutter App

تطبيق الموبايل للمتجر الإلكتروني (iOS + Android).
متصل بـ API الخادم: `https://alfared.ps`

## المعلومات الأساسية

| المعلومة | القيمة |
|----------|--------|
| اسم التطبيق | شركة أبناء الفريد |
| Bundle ID (iOS) | `com.alfared.alfaredApp` |
| Package Name (Android) | `ps.alfared.shop` |
| Firebase Project | `alfared-app` |
| API Base URL | `https://alfared.ps/api/v1` |
| Development Team (iOS) | `PFK6UK85DW` |
| Keystore (Android) | `android/app/upload-keystore.jks` |
| Keystore Password | `alfared2024` |
| Key Alias | `upload` |

## المتطلبات

- Flutter SDK >= 3.22
- Dart >= 3.4
- Android Studio أو VS Code مع Flutter plugin
- Xcode >= 15 (لـ iOS)
- حساب Firebase للإشعارات
- macOS لبناء iOS

## التثبيت السريع

```bash
cd mobile-app

# 1) ثبت الـ dependencies
flutter pub get

# 2) شغّل على Android emulator
flutter run -d emulator-5554

# 3) شغّل على iOS simulator
DEVELOPER_DIR=/Applications/Xcode.app/Contents/Developer flutter run -d <simulator-id>
```

> ملاحظة: إذا واجهت مشكلة مع Xcode، تأكد من تنفيذ:
> `sudo xcode-select --switch /Applications/Xcode.app/Contents/Developer`

## بنية المشروع

```
lib/
├── config/           # api_config.dart — base URL + timeouts
├── models/           # User, Product, Category, Order
├── services/         # api_service (Dio + retry), fcm_service (Firebase)
├── providers/        # AuthProvider, CartProvider, LocaleProvider, ThemeProvider
├── theme/            # AppTheme + AppColors
├── utils/            # ImageHelper (تنظيف روابط الصور)
├── widgets/          # ProductCard, ProductCardSkeleton, GuestWall
├── l10n/             # AppStrings (عربي + إنجليزي + عبري)
└── screens/
    ├── splash_screen.dart
    ├── onboarding/   # شاشة الترحيب (مرة واحدة)
    ├── auth/         # Login + Register + OTP
    ├── home/         # Home + MainNavigation + Bottom nav
    ├── products/     # Products list + Detail + Categories
    ├── cart/         # Cart
    ├── checkout/     # Checkout + Success + Lahza WebView
    ├── orders/       # My orders + Detail/tracking
    ├── account/      # Account + Wishlist + Rewards + Addresses
    └── notifications_screen.dart
```

## إعداد Firebase

### Android
- الملف: `android/app/google-services.json`
- Package: `ps.alfared.shop`
- Firebase Project: `alfared-app`

### iOS
- الملف: `ios/Runner/GoogleService-Info.plist`
- Bundle: `com.alfared.alfaredApp`
- AppDelegate مُعد مع `FirebaseApp.configure()`
- Background Modes: `fetch` + `remote-notification`
- Entitlements: `aps-environment = development`

### إرسال إشعار تجريبي على iOS Simulator
```bash
xcrun simctl push <device-id> com.alfared.alfaredApp - <<EOF
{
  "aps": {
    "alert": {"title": "طلب جديد", "body": "تم استلام طلبك بنجاح!"},
    "sound": "default"
  }
}
EOF
```

> ملاحظة: Push Notifications الحقيقية عبر FCM لا تعمل على المحاكي. تحتاج جهاز حقيقي.

## بناء النسخة النهائية

### Android — APK
```bash
flutter build apk --release
# الناتج: build/app/outputs/flutter-apk/app-release.apk
```

### Android — AAB (لـ Google Play)
```bash
flutter build appbundle --release
# الناتج: build/app/outputs/bundle/release/app-release.aab
```

### iOS
```bash
DEVELOPER_DIR=/Applications/Xcode.app/Contents/Developer flutter build ios --release
# أو افتح Xcode:
open ios/Runner.xcworkspace
# ثم Archive من Xcode
```

## التوقيع (Signing)

### Android
- Keystore: `android/app/upload-keystore.jks`
- الإعدادات في: `android/key.properties`
- SHA-256 Fingerprint: `6A:1B:51:BD:4B:7A:F6:8E:FD:F8:2A:7A:C4:00:32:5A:37:98:06:87:DA:6C:D9:6B:C3:54:AF:E8:81:62:12:37`

### iOS
- Automatic Signing مع Development Team: `PFK6UK85DW`
- Apple Developer: Mustafa Albustanji (BGQR7CF8MH)

## الإصلاحات المطبّقة

### 1. إصلاح روابط الصور (ImageHelper)
الخادم يضيف `/storage/` لجميع روابط الصور حتى لو كانت روابط خارجية كاملة (مثل Unsplash).
الحل: `lib/utils/image_helper.dart` — يتحقق إذا الرابط يحتوي `/storage/http` ويزيل الجزء الزائد.

### 2. تحسين الاتصال بالـ API
- Timeout: رُفع من 30 ثانية إلى 60 ثانية (connect + receive)
- Retry: محاولتان إضافيتان تلقائياً لطلبات GET عند فشل الاتصال
- الملف: `lib/services/api_service.dart`

### 3. إعداد iOS Build
- إضافة `DEVELOPMENT_TEAM` لجميع Build Configurations (Debug + Release + Profile)
- إضافة `CODE_SIGN_STYLE = Automatic`
- إضافة Firebase + Push Notification capabilities

## نصوص المتجر (App Store / Google Play)

### اسم التطبيق
```
شركة أبناء الفريد
```

### العنوان الفرعي (Subtitle)
```
متجر التجميل الأول في فلسطين
```

### النص الترويجي (Promotional Text)
```
عروض حصرية يوميا! خصومات تصل إلى 50% على أفضل الماركات العالمية. حمل التطبيق الآن واحصل على نقاط ولاء مع كل طلب. توصيل سريع لجميع مناطق فلسطين والداخل.
```

### الوصف (Description)
```
شركة أبناء الفريد التجارية - أضخم معرض للمنتجات في فلسطين!

أكثر من 5000 منتج من أفضل الماركات العالمية للعناية بالبشرة والشعر والجسم ومستحضرات التجميل، متوفرة الآن بين يديك عبر تطبيقنا.

مميزات التطبيق:
- تصفح أكثر من 5000 منتج من أشهر الماركات العالمية
- أسعار منافسة وعروض يومية حصرية
- بحث سريع وفلاتر متقدمة حسب القسم والسعر والتقييم
- سلة تسوق ذكية متزامنة على جميع أجهزتك
- نظام نقاط ولاء - اكسب نقاط مع كل طلب واستبدلها بخصومات
- كوبونات خصم حصرية لمستخدمي التطبيق
- تتبع طلبك لحظة بلحظة من الاستلام حتى التوصيل
- إشعارات فورية بحالة طلبك والعروض الجديدة
- قائمة المفضلة لحفظ منتجاتك المفضلة
- دفع عند الاستلام أو إلكترونيا عبر بوابة لحظة
- توصيل سريع لجميع مناطق فلسطين والداخل

من نحن:
شركة أبناء الفريد التجارية - الخليل، فلسطين. نوفر لكم أفضل المنتجات بأسعار الجملة للمحلات والمتاجر وصفحات التسويق الإلكتروني والأفراد.

خدمة عملاء متميزة عبر الواتساب على مدار الساعة.

حمل التطبيق الآن وابدأ التسوق!
```

### الكلمات المفتاحية (Keywords)
```
تجميل,عناية,بشرة,شعر,ماركات,فلسطين,الخليل,عروض,خصم,توصيل,متجر,كريم,عطور,مكياج,الفريد
```

### التصنيف
- Primary: Shopping
- Secondary: Lifestyle

### التصنيف العمري
- 4+ (بدون محتوى مقيد)

### التشفير
- يستخدم Standard encryption (HTTPS/TLS)
- غير متوفر في فرنسا

## الميزات

- تسجيل دخول + تسجيل بحساب جديد (مع +970/+972)
- تصفح المنتجات والأقسام والـ Banners
- بحث + فلاتر ترتيب (سعر، تقييم، الأحدث، الأكثر مبيعاً)
- تفاصيل المنتج مع معرض صور + variants (لون/مقاس/وزن) + تقييمات
- سلة متزامنة مع الخادم
- Checkout مع كوبون + نقاط ولاء + مناطق توصيل
- دفع: نقداً عند الاستلام / بوابة لحظة الإلكترونية
- تتبع الطلبات بـ timeline تفاعلي
- طلب إرجاع للطلبات المُسلّمة
- المفضلة + نقاط الولاء + سجل المعاملات
- Push Notifications عند تغيير حالة الطلب
- 3 لغات: عربي + إنجليزي + عبري
- RTL كامل + خط Cairo
- Dark Mode
- شاشة Onboarding للمستخدمين الجدد

## الخطوط

ضع ملفات الخط في `assets/fonts/`:
- Cairo-Regular.ttf
- Cairo-Bold.ttf
- Cairo-Black.ttf

تنزيل: [Google Fonts — Cairo](https://fonts.google.com/specimen/Cairo)
