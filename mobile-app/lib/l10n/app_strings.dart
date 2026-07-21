// Centralised UI strings for ar / he / en.
// Usage: context.watch<LocaleProvider>().s.someKey
// Missing keys fall back to Arabic.

class S {
  S._(this._locale);

  final String _locale;

  // ── app-wide ─────────────────────────────────────────────────────
  String get appTitle          => _t('appTitle');
  String get search            => _t('search');
  String get cancel            => _t('cancel');
  String get save              => _t('save');
  String get send              => _t('send');
  String get apply             => _t('apply');
  String get loading           => _t('loading');
  String get retry             => _t('retry');
  String get ok                => _t('ok');
  String get error             => _t('error');
  String get required          => _t('required');
  String get notes             => _t('notes');
  String get delete            => _t('delete');
  String get deleted           => _t('deleted');
  String get share             => _t('share');
  String get loadError         => _t('loadError');

  // ── navigation ───────────────────────────────────────────────────
  String get navHome           => _t('navHome');
  String get navSearch         => _t('navSearch');
  String get navCart           => _t('navCart');
  String get navWishlist       => _t('navWishlist');
  String get navAccount        => _t('navAccount');

  // ── home ─────────────────────────────────────────────────────────
  String get sectionCategories => _t('sectionCategories');
  String get sectionBestseller => _t('sectionBestseller');
  String get sectionNewArrivals=> _t('sectionNewArrivals');
  String get sectionRecentlyViewed => _t('sectionRecentlyViewed');
  String get seeAll            => _t('seeAll');
  String get featureDelivery   => _t('featureDelivery');
  String get featureOriginal   => _t('featureOriginal');
  String get featureSecurePay  => _t('featureSecurePay');
  String get searchHint        => _t('searchHint');

  // ── product ──────────────────────────────────────────────────────
  String get addToCart         => _t('addToCart');
  String get outOfStock        => _t('outOfStock');
  String get description       => _t('description');
  String get reviews           => _t('reviews');
  String get addReview         => _t('addReview');
  String get yourReview        => _t('yourReview');
  String get noReviews         => _t('noReviews');
  String get quantity          => _t('quantity');
  String get options           => _t('options');
  String get color             => _t('color');
  String get size              => _t('size');
  String saveMoney(String amount) => _t('saveMoney').replaceFirst('%s', amount);

  // ── cart ─────────────────────────────────────────────────────────
  String get cart              => _t('cart');
  String get cartTitle         => _t('cartTitle');
  String get emptyCart         => _t('emptyCart');
  String get emptyCartTitle    => _t('emptyCartTitle');
  String get emptyCartSub      => _t('emptyCartSub');
  String get browseCta         => _t('browseCta');
  String get loginToViewCart   => _t('loginToViewCart');
  String get movedToWishlist   => _t('movedToWishlist');
  String get checkout          => _t('checkout');
  String get subtotal          => _t('subtotal');
  String get shipping          => _t('shipping');
  String get shippingTbd       => _t('shippingTbd');
  String get deliveryFee       => _t('deliveryFee');
  String get discount          => _t('discount');
  String get total             => _t('total');
  String get removeItem        => _t('removeItem');
  String get couponHint        => _t('couponHint');
  String itemsCount(int n)     => '$n ${_t('itemsCountLabel')}';

  // ── checkout form ────────────────────────────────────────────────
  String get personalInfo      => _t('personalInfo');
  String get deliveryAddress   => _t('deliveryAddress');
  String get paymentMethod     => _t('paymentMethod');
  String get cod               => _t('cod');
  String get placeOrder        => _t('placeOrder');
  String get couponCode        => _t('couponCode');
  String get loyaltyPoints     => _t('loyaltyPoints');
  String get firstName         => _t('firstName');
  String get lastName          => _t('lastName');
  String get phoneNumber       => _t('phoneNumber');
  String get emailOptional     => _t('emailOptional');
  String get deliveryZone      => _t('deliveryZone');
  String get pickSavedAddress  => _t('pickSavedAddress');
  String get city              => _t('city');
  String get addressDetail     => _t('addressDetail');
  String get neighborhood      => _t('neighborhood');
  String get building          => _t('building');
  String get deliveryNotes     => _t('deliveryNotes');
  String get couponAndPoints   => _t('couponAndPoints');
  String get loyaltyDiscount   => _t('loyaltyDiscount');
  String get selectZoneMsg     => _t('selectZoneMsg');
  String get searchNeighborhood => _t('searchNeighborhood');
  String loyaltyMaxHint(int max) => _t('loyaltyMaxHintBase').replaceFirst('%d', '$max');

  // ── success ──────────────────────────────────────────────────────
  String get orderSuccessTitle => _t('orderSuccessTitle');
  String get orderSuccessSub   => _t('orderSuccessSub');
  String get backToHome        => _t('backToHome');

  // ── account ──────────────────────────────────────────────────────
  String get myAccount         => _t('myAccount');
  String get myOrders          => _t('myOrders');
  String get myAddresses       => _t('myAddresses');
  String get myWishlist        => _t('myWishlist');
  String get loyalty           => _t('loyalty');
  String get notifications     => _t('notifications');
  String get settings          => _t('settings');
  String get language          => _t('language');
  String get logout            => _t('logout');
  String get login             => _t('login');
  String get register          => _t('register');

  // ── orders ───────────────────────────────────────────────────────
  String get orderNumber       => _t('orderNumber');
  String get orderStatus       => _t('orderStatus');
  String get requestReturn     => _t('requestReturn');
  String get trackOrder        => _t('trackOrder');
  String get orderConfirmed    => _t('orderConfirmed');
  String get orderProcessing   => _t('orderProcessing');
  String get orderSentToDelivery => _t('orderSentToDelivery');
  String get all               => _t('all');
  String get categoriesTitle   => _t('categoriesTitle');
  String get storeTitle           => _t('storeTitle');
  String get noProducts           => _t('noProducts');
  String get sortNewest           => _t('sortNewest');
  String get sortPriceAsc         => _t('sortPriceAsc');
  String get sortPriceDesc        => _t('sortPriceDesc');
  String get sortRating           => _t('sortRating');
  String get sortBestseller       => _t('sortBestseller');
  String get loadPageError        => _t('loadPageError');
  String get checkInternet        => _t('checkInternet');
  String get newBadge             => _t('newBadge');
  String get availableBrands      => _t('availableBrands');
  String get greetingHello        => _t('greetingHello');
  String get whatsapp             => _t('whatsapp');
  String get addedToCartShort     => _t('addedToCartShort');
  String get orderInquiry         => _t('orderInquiry');
  String get trackIntro        => _t('trackIntro');
  String get trackNumberLabel  => _t('trackNumberLabel');
  String get trackSubmit       => _t('trackSubmit');
  String get trackEnterNumber  => _t('trackEnterNumber');
  String get orderShipped      => _t('orderShipped');
  String get orderDelivered    => _t('orderDelivered');
  String get orderCancelled    => _t('orderCancelled');
  String get subtotalLabel     => _t('subtotalLabel');
  String get discountLabel     => _t('discountLabel');
  String get loyaltyLabel      => _t('loyaltyLabel');
  String get freeDelivery      => _t('freeDelivery');
  String get totalLabel        => _t('totalLabel');
  String get productsLabel     => _t('productsLabel');
  String get orderNotFound     => _t('orderNotFound');
  String get returnRequested   => _t('returnRequested');
  String get deliveryTeam      => _t('deliveryTeam');
  String get driver            => _t('driver');
  String get trackingNumberLabel => _t('trackingNumberLabel');
  String get returnReason      => _t('returnReason');
  String get returnHint        => _t('returnHint');
  String get returnSend        => _t('returnSend');
  String get returnSuccess     => _t('returnSuccess');
  String get returnReasonEmpty => _t('returnReasonEmpty');
  String get noOrders          => _t('noOrders');
  String get orderRef          => _t('orderRef');

  // ── notifications ────────────────────────────────────────────────
  String get notifPending      => _t('notifPending');
  String get notifConfirmed    => _t('notifConfirmed');
  String get notifProcessing   => _t('notifProcessing');
  String get notifShipped      => _t('notifShipped');
  String get notifDelivered    => _t('notifDelivered');
  String get notifCancelled    => _t('notifCancelled');
  String get notifUpdate       => _t('notifUpdate');
  String get noNotifications   => _t('noNotifications');
  String get noNotifSub        => _t('noNotifSub');

  // ── home extras ──────────────────────────────────────────────────
  String get sectionOffers     => _t('sectionOffers');
  String get offersSubtitle    => _t('offersSubtitle');
  String get promoCta1         => _t('promoCta1');
  String get promoCta2         => _t('promoCta2');
  String get promoCtaBtn       => _t('promoCtaBtn');
  String get whyUs             => _t('whyUs');
  String get unitProduct       => _t('unitProduct');
  String get unitCustomer      => _t('unitCustomer');
  String get unitRating        => _t('unitRating');

  // ── loyalty screen ───────────────────────────────────────────────
  String get yourBalance       => _t('yourBalance');
  String get pointUnit         => _t('pointUnit');
  String get howToEarn         => _t('howToEarn');
  String get howToRedeem       => _t('howToRedeem');
  String get pointsHistory     => _t('pointsHistory');
  String earnExplain(String amt) => _t('earnExplainBase').replaceFirst('%s', amt);
  String redeemExplain(String ptsPerIls, String minPts) =>
      _t('redeemExplainBase').replaceFirst('%s', ptsPerIls).replaceFirst('%s', minPts);
  String equivalentIls(String amount) => _t('equivalentIlsBase').replaceFirst('%s', amount);

  // ── rewards screen ───────────────────────────────────────────────
  String get rewards           => _t('rewards');
  String get dailyRewards      => _t('dailyRewards');
  String get dailyCheckin      => _t('dailyCheckin');
  String get checkinDone       => _t('checkinDone');
  String get claimCheckin      => _t('claimCheckin');
  String get claimedCheckin    => _t('claimedCheckin');
  String get inviteFriend      => _t('inviteFriend');
  String get invitation        => _t('invitation');
  String get codeCopied        => _t('codeCopied');
  String checkinMsg(int pts)   => _t('checkinMsgBase').replaceFirst('%d', '$pts');
  String inviteMsg(int bonus)  => _t('inviteMsgBase').replaceFirst('%d', '$bonus');
  String currentStreak(int n)  => _t('currentStreakBase').replaceFirst('%d', '$n');

  // ── addresses screen ─────────────────────────────────────────────
  String get newAddress        => _t('newAddress');
  String get noAddresses       => _t('noAddresses');
  String get noAddressesSub    => _t('noAddressesSub');
  String get addNewAddress     => _t('addNewAddress');
  String get defaultTag        => _t('defaultTag');
  String get deleteAddressTitle   => _t('deleteAddressTitle');
  String get deleteAddressConfirm => _t('deleteAddressConfirm');
  String get addressLabel      => _t('addressLabel');
  String get addressNameHint   => _t('addressNameHint');
  String get makeDefault       => _t('makeDefault');
  String get saveAddress       => _t('saveAddress');
  String get saveAddressSuccess=> _t('saveAddressSuccess');

  // ── wishlist screen ──────────────────────────────────────────────
  String get noWishlistItems   => _t('noWishlistItems');

  // ── account screen ───────────────────────────────────────────────
  String get editProfile       => _t('editProfile');
  String get fullName          => _t('fullName');
  String get emailField        => _t('emailField');
  String get phoneOptional     => _t('phoneOptional');
  String get saveChanges       => _t('saveChanges');
  String get changesSaved      => _t('changesSaved');
  String get saveFailed        => _t('saveFailed');
  String get nameRequired      => _t('nameRequired');
  String get emailInvalid      => _t('emailInvalid');
  String get loginPrompt       => _t('loginPrompt');
  String get darkMode          => _t('darkMode');
  String get followUs          => _t('followUs');
  String get contactWhatsapp   => _t('contactWhatsapp');
  String get appVersion        => _t('appVersion');
  String get currentLevel      => _t('currentLevel');
  String get spendingLabel     => _t('spendingLabel');
  String get topTierMsg        => _t('topTierMsg');
  String get ordersCountLabel  => _t('ordersCountLabel');
  String get wishlistLabel     => _t('wishlistLabel');
  String get pointsShortLabel  => _t('pointsShortLabel');
  String get loyaltyPointsCard => _t('loyaltyPointsCard');

  // ── home marquee + flash sale + loyalty card ─────────────────
  String get marqueeText        => _t('marqueeText');
  String get flashSaleTitle     => _t('flashSaleTitle');
  String get flashSaleSubtitle  => _t('flashSaleSubtitle');
  String get flashSaleEndsIn    => _t('flashSaleEndsIn');
  String get timeUnitHour       => _t('timeUnitHour');
  String get timeUnitMin        => _t('timeUnitMin');
  String get timeUnitSec        => _t('timeUnitSec');
  String get badgeOffer         => _t('badgeOffer');
  String get badgeTrending      => _t('badgeTrending');
  String get loyaltyCardTitle   => _t('loyaltyCardTitle');
  String get loyaltyPointUnit   => _t('loyaltyPointUnit');
  String loyaltyProgress(int pct, String tier) =>
      _t('loyaltyProgressBase').replaceFirst('%d', '$pct').replaceFirst('%s', tier);

  // ── onboarding ───────────────────────────────────────────────
  String get onboardTag1      => _t('onboardTag1');
  String get onboardTitle1    => _t('onboardTitle1');
  String get onboardSub1      => _t('onboardSub1');
  String get onboardTag2      => _t('onboardTag2');
  String get onboardTitle2    => _t('onboardTitle2');
  String get onboardSub2      => _t('onboardSub2');
  String get onboardTag3      => _t('onboardTag3');
  String get onboardTitle3    => _t('onboardTitle3');
  String get onboardSub3      => _t('onboardSub3');
  String get onboardNext      => _t('onboardNext');
  String get onboardStart     => _t('onboardStart');
  String get onboardSkipOr    => _t('onboardSkipOr');
  String get onboardSkip      => _t('onboardSkip');

  // ── login ────────────────────────────────────────────────────
  // ── guest wall ──────────────────────────────────────────────
  String get guestLoginTitle  => _t('guestLoginTitle');
  String get guestLoginSub    => _t('guestLoginSub');
  String get continueAsGuest  => _t('continueAsGuest');

  // ── login ────────────────────────────────────────────────────
  String get loginWelcome     => _t('loginWelcome');
  String get loginSubtitle    => _t('loginSubtitle');
  String get loginPhoneLabel  => _t('loginPhoneLabel');
  String get loginPhoneHint   => _t('loginPhoneHint');
  String get loginPhoneHelper => _t('loginPhoneHelper');
  String get loginPassLabel   => _t('loginPassLabel');
  String get loginBtn         => _t('loginBtn');
  String get loginNoAccount   => _t('loginNoAccount');
  String get loginCreate      => _t('loginCreate');
  String get loginPhoneErr    => _t('loginPhoneErr');
  String get loginPassErr     => _t('loginPassErr');

  // ── register ─────────────────────────────────────────────────
  String get registerTitle    => _t('registerTitle');
  String get registerWelcome  => _t('registerWelcome');
  String get registerNameLbl  => _t('registerNameLbl');
  String get registerNameHint => _t('registerNameHint');
  String get registerNameErr  => _t('registerNameErr');
  String get registerEmailLbl => _t('registerEmailLbl');
  String get registerEmailErr => _t('registerEmailErr');
  String get registerPassLbl  => _t('registerPassLbl');
  String get registerPassHelp => _t('registerPassHelp');
  String get registerPassErr  => _t('registerPassErr');
  String get registerBtn          => _t('registerBtn');
  String get registerWaNote       => _t('registerWaNote');
  String get registerDuplicate    => _t('registerDuplicate');
  String get registerOtpFailed    => _t('registerOtpFailed');

  // ── account deletion ─────────────────────────────────────
  String get deleteAccount        => _t('deleteAccount');
  String get deleteAccountTitle   => _t('deleteAccountTitle');
  String get deleteAccountMsg     => _t('deleteAccountMsg');
  String get deleteAccountConfirm => _t('deleteAccountConfirm');
  String get deleteAccountDone    => _t('deleteAccountDone');
  String get deleteAccountFail    => _t('deleteAccountFail');

  // ── language picker (onboarding) ─────────────────────────
  String get chooseLanguage       => _t('chooseLanguage');
  String toReachTier(String emoji, String name) =>
      _t('toReachTierBase').replaceFirst('%s', '$emoji $name');
  String remainingAmount(String amount) => _t('remainingAmountBase').replaceFirst('%s', amount);
  String get newTierLabel    => _t('newTierLabel');
  String get bronzeTierLabel => _t('bronzeTierLabel');
  String get silverTierLabel => _t('silverTierLabel');
  String get goldTierLabel   => _t('goldTierLabel');

  String _t(String key) => AppStrings._data[key]?[_locale]
      ?? AppStrings._data[key]?['ar']
      ?? key;
}

class AppStrings {
  AppStrings._();

  static S from(String localeCode) => S._(localeCode);

  static final Map<String, Map<String, String>> _data = {
    // ── app ─────────────────────────────────────────────────────
    'appTitle':           {'ar': 'أبناء الفريد',        'he': 'בני אל-פריד',          'en': 'Alfared Shop'},
    'search':             {'ar': 'بحث',                 'he': 'חיפוש',                'en': 'Search'},
    'cancel':             {'ar': 'إلغاء',               'he': 'ביטול',                'en': 'Cancel'},
    'save':               {'ar': 'حفظ',                 'he': 'שמור',                 'en': 'Save'},
    'send':               {'ar': 'إرسال',               'he': 'שלח',                  'en': 'Send'},
    'apply':              {'ar': 'تطبيق',               'he': 'החל',                  'en': 'Apply'},
    'loading':            {'ar': 'جارٍ التحميل…',       'he': 'טוען…',                'en': 'Loading…'},
    'retry':              {'ar': 'إعادة المحاولة',       'he': 'נסה שוב',              'en': 'Retry'},
    'ok':                 {'ar': 'موافق',                'he': 'אישור',                'en': 'OK'},
    'error':              {'ar': 'حدث خطأ',             'he': 'שגיאה',                'en': 'Error'},
    'required':           {'ar': 'مطلوب',               'he': 'שדה חובה',             'en': 'Required'},
    'notes':              {'ar': 'ملاحظات',             'he': 'הערות',                'en': 'Notes'},
    'delete':             {'ar': 'حذف',                 'he': 'מחק',                  'en': 'Delete'},
    'deleted':            {'ar': 'تم الحذف',             'he': 'נמחק',                 'en': 'Deleted'},
    'share':              {'ar': 'مشاركة',              'he': 'שתף',                  'en': 'Share'},
    'loadError':          {'ar': 'تعذّر تحميل البيانات', 'he': 'שגיאה בטעינה',         'en': 'Failed to load data'},

    // ── nav ─────────────────────────────────────────────────────
    'navHome':            {'ar': 'الرئيسية',   'he': 'ראשי',        'en': 'Home'},
    'navSearch':          {'ar': 'بحث',        'he': 'חיפוש',       'en': 'Search'},
    'navCart':            {'ar': 'السلة',      'he': 'עגלה',        'en': 'Cart'},
    'navWishlist':        {'ar': 'مفضلة',      'he': 'מועדפים',     'en': 'Wishlist'},
    'navAccount':         {'ar': 'حسابي',      'he': 'חשבוני',      'en': 'Account'},

    // ── home ────────────────────────────────────────────────────
    'sectionCategories':  {'ar': '🏷️ الأقسام',          'he': '🏷️ קטגוריות',    'en': '🏷️ Categories'},
    'sectionBestseller':  {'ar': '🔥 الأكثر مبيعاً',    'he': '🔥 נמכר ביותר',  'en': '🔥 Bestsellers'},
    'sectionNewArrivals': {'ar': '✨ وصل حديثاً',        'he': '✨ חדש במלאי',   'en': '✨ New Arrivals'},
    'sectionRecentlyViewed':{'ar':'🕘 آخر ما شاهدت',    'he': '🕘 נצפה לאחרונה','en': '🕘 Recently Viewed'},
    'seeAll':             {'ar': 'عرض الكل',   'he': 'הצג הכל',    'en': 'See all'},
    'featureDelivery':    {'ar': 'توصيل سريع', 'he': 'משלוח מהיר', 'en': 'Fast Delivery'},
    'featureOriginal':    {'ar': 'منتجات أصلية','he': 'מוצרים מקוריים','en': 'Original Products'},
    'featureSecurePay':   {'ar': 'دفع آمن',    'he': 'תשלום מאובטח','en': 'Secure Payment'},
    'searchHint':         {'ar': 'ابحث عن منتجك…','he': 'חפש מוצר…', 'en': 'Search products…'},

    // ── product ─────────────────────────────────────────────────
    'addToCart':          {'ar': 'أضف للسلة',              'he': 'הוסף לעגלה',       'en': 'Add to Cart'},
    'outOfStock':         {'ar': 'نفد المخزون',             'he': 'אזל מהמלאי',      'en': 'Out of Stock'},
    'description':        {'ar': 'الوصف',                  'he': 'תיאור',            'en': 'Description'},
    'reviews':            {'ar': 'تقييمات العملاء',         'he': 'ביקורות לקוחות',  'en': 'Customer Reviews'},
    'addReview':          {'ar': '+ أضف تقييم',            'he': '+ הוסף ביקורת',   'en': '+ Add Review'},
    'yourReview':         {'ar': 'تعليقك (اختياري)',        'he': 'הערה (אופציונלי)', 'en': 'Your comment (optional)'},
    'noReviews':          {'ar': 'لا توجد تقييمات بعد.',   'he': 'אין ביקורות עדיין.','en': 'No reviews yet.'},
    'quantity':           {'ar': 'الكمية',                 'he': 'כמות',             'en': 'Quantity'},
    'options':            {'ar': 'الخيارات',               'he': 'אפשרויות',         'en': 'Options'},
    'color':              {'ar': 'اللون',                  'he': 'צבע',              'en': 'Color'},
    'size':               {'ar': 'المقاس',                 'he': 'מידה',             'en': 'Size'},
    'saveMoney':          {'ar': 'وفّر %s ₪',              'he': 'חסוך %s ₪',        'en': 'Save %s ₪'},

    // ── cart ────────────────────────────────────────────────────
    'cart':               {'ar': 'السلة',           'he': 'עגלת הקניות',   'en': 'Cart'},
    'cartTitle':          {'ar': 'سلة التسوق',       'he': 'עגלת הקניות',   'en': 'Shopping Cart'},
    'emptyCart':          {'ar': 'السلة فارغة',      'he': 'העגלה ריקה',    'en': 'Your cart is empty'},
    'emptyCartTitle':     {'ar': 'سلتك فارغة',       'he': 'העגלה שלך ריקה','en': 'Your cart is empty'},
    'emptyCartSub':       {'ar': 'ابدأ التسوّق وأضف منتجاتك المفضّلة لتجدها هنا', 'he': 'התחל לקנות והוסף מוצרים מועדפים', 'en': 'Start shopping and add your favourite items here'},
    'browseCta':          {'ar': '🛍️ تصفّح المنتجات', 'he': '🛍️ עיין במוצרים','en': '🛍️ Browse Products'},
    'loginToViewCart':    {'ar': 'سجّل دخولك لعرض سلتك', 'he': 'התחבר לצפות בעגלה', 'en': 'Log in to view your cart'},
    'movedToWishlist':    {'ar': '❤️ نُقل إلى المفضّلة', 'he': '❤️ הועבר למועדפים', 'en': '❤️ Moved to Wishlist'},
    'itemsCountLabel':    {'ar': 'منتجات',           'he': 'פריטים',        'en': 'items'},
    'checkout':           {'ar': 'إتمام الطلب',      'he': 'לתשלום',        'en': 'Checkout'},
    'subtotal':           {'ar': 'المجموع الفرعي',   'he': 'סכום ביניים',   'en': 'Subtotal'},
    'shipping':           {'ar': 'الشحن',            'he': 'משלוח',         'en': 'Shipping'},
    'shippingTbd':        {'ar': 'سيتم تحديده',       'he': 'יוחלט מאוחר',  'en': 'To be determined'},
    'deliveryFee':        {'ar': 'رسوم التوصيل',      'he': 'דמי משלוח',    'en': 'Delivery fee'},
    'discount':           {'ar': 'الخصم',            'he': 'הנחה',          'en': 'Discount'},
    'total':              {'ar': 'الإجمالي',          'he': 'סה"כ',          'en': 'Total'},
    'removeItem':         {'ar': 'إزالة',            'he': 'הסר',           'en': 'Remove'},
    'couponHint':         {'ar': '🏷️ كود الخصم…',    'he': '🏷️ קוד קופון…', 'en': '🏷️ Coupon code…'},

    // ── checkout form ────────────────────────────────────────────
    'personalInfo':       {'ar': '👤 البيانات الشخصية', 'he': '👤 פרטים אישיים',  'en': '👤 Personal Info'},
    'deliveryAddress':    {'ar': '📍 عنوان التوصيل',    'he': '📍 כתובת למשלוח',  'en': '📍 Delivery Address'},
    'paymentMethod':      {'ar': '💳 طريقة الدفع',      'he': '💳 אמצעי תשלום',   'en': '💳 Payment Method'},
    'cod':                {'ar': '💵 دفع عند الاستلام', 'he': '💵 תשלום בעת קבלה','en': '💵 Cash on Delivery'},
    'placeOrder':         {'ar': '✓ تأكيد الطلب',       'he': '✓ אשר הזמנה',      'en': '✓ Place Order'},
    'couponCode':         {'ar': 'كود الخصم',           'he': 'קוד קופון',         'en': 'Coupon code'},
    'loyaltyPoints':      {'ar': 'استخدم نقاط الولاء',  'he': 'השתמש בנקודות',    'en': 'Use loyalty points'},
    'firstName':          {'ar': 'الاسم الأول',         'he': 'שם פרטי',          'en': 'First Name'},
    'lastName':           {'ar': 'الاسم الأخير',        'he': 'שם משפחה',         'en': 'Last Name'},
    'phoneNumber':        {'ar': 'رقم الهاتف',          'he': 'מספר טלפון',       'en': 'Phone Number'},
    'emailOptional':      {'ar': 'البريد الإلكتروني (اختياري)', 'he': 'אימייל (אופציונלי)', 'en': 'Email (optional)'},
    'deliveryZone':       {'ar': 'منطقة التوصيل',       'he': 'אזור משלוח',       'en': 'Delivery Zone'},
    'pickSavedAddress':   {'ar': 'اختر من عناويني المحفوظة', 'he': 'בחר מכתובת שמורה', 'en': 'Pick a saved address'},
    'city':               {'ar': 'المدينة',              'he': 'עיר',              'en': 'City'},
    'addressDetail':      {'ar': 'العنوان بالتفصيل',    'he': 'כתובת מפורטת',     'en': 'Full Address'},
    'neighborhood':       {'ar': 'الحي',                'he': 'שכונה',             'en': 'Neighborhood'},
    'building':           {'ar': 'المبنى/الشقة',        'he': 'בניין/דירה',        'en': 'Building/Apt'},
    'deliveryNotes':      {'ar': 'ملاحظات التوصيل',     'he': 'הערות למשלוח',      'en': 'Delivery Notes'},
    'couponAndPoints':    {'ar': '🎟️ الكوبون والنقاط',  'he': '🎟️ קופון ונקודות',  'en': '🎟️ Coupon & Points'},
    'loyaltyDiscount':    {'ar': '⭐ نقاط الولاء',       'he': '⭐ נקודות נאמנות',  'en': '⭐ Loyalty Points'},
    'selectZoneMsg':      {'ar': 'اختر منطقة التوصيل',  'he': 'בחר אזור משלוח',   'en': 'Select delivery zone'},
    'searchNeighborhood': {'ar': 'ابحث عن الحي',        'he': 'חפש שכונה',         'en': 'Search for your area'},
    'loyaltyMaxHintBase': {'ar': 'الحد الأقصى: %d نقطة','he': 'מקסימום: %d נקודות','en': 'Max: %d points'},

    // ── success ──────────────────────────────────────────────────
    'orderSuccessTitle':  {'ar': 'تم تأكيد طلبك بنجاح! 🎉', 'he': 'הזמנתך אושרה בהצלחה! 🎉', 'en': 'Order Confirmed! 🎉'},
    'orderSuccessSub':    {'ar': 'سنتواصل معك قريباً لتأكيد موعد التوصيل', 'he': 'ניצור קשר בקרוב לאישור מועד המשלוח', 'en': 'We\'ll contact you soon to confirm delivery'},
    'backToHome':         {'ar': 'العودة للرئيسية',      'he': 'חזרה לדף הבית',   'en': 'Back to Home'},

    // ── account ─────────────────────────────────────────────────
    'myAccount':          {'ar': 'حسابي',             'he': 'החשבון שלי',      'en': 'My Account'},
    'myOrders':           {'ar': 'طلباتي',            'he': 'ההזמנות שלי',    'en': 'My Orders'},
    'myAddresses':        {'ar': 'عناويني',           'he': 'הכתובות שלי',    'en': 'My Addresses'},
    'myWishlist':         {'ar': 'المفضلة',           'he': 'המועדפים שלי',   'en': 'My Wishlist'},
    'loyalty':            {'ar': 'نقاط الولاء',       'he': 'נקודות נאמנות',  'en': 'Loyalty Points'},
    'notifications':      {'ar': 'الإشعارات',         'he': 'התראות',         'en': 'Notifications'},
    'settings':           {'ar': 'الإعدادات',         'he': 'הגדרות',         'en': 'Settings'},
    'language':           {'ar': 'اللغة',             'he': 'שפה',            'en': 'Language'},
    'logout':             {'ar': 'تسجيل الخروج',      'he': 'התנתק',          'en': 'Log Out'},
    'login':              {'ar': 'تسجيل الدخول',      'he': 'התחבר',          'en': 'Log In'},
    'register':           {'ar': 'إنشاء حساب',        'he': 'הרשמה',          'en': 'Sign Up'},

    // ── orders ──────────────────────────────────────────────────
    'orderNumber':        {'ar': 'رقم الطلب',         'he': 'מספר הזמנה',     'en': 'Order #'},
    'orderStatus':        {'ar': 'حالة الطلب',        'he': 'סטטוס הזמנה',    'en': 'Order Status'},
    'requestReturn':      {'ar': 'طلب إرجاع',         'he': 'בקש החזרה',      'en': 'Request Return'},
    'noOrders':           {'ar': 'لا توجد طلبات بعد', 'he': 'אין הזמנות עדיין','en': 'No orders yet'},
    'trackOrder':         {'ar': 'تتبع الطلب',        'he': 'מעקב הזמנה',     'en': 'Track Order'},
    'orderConfirmed':     {'ar': 'تم تأكيد الطلب',   'he': 'ההזמנה אושרה',   'en': 'Order Confirmed'},
    'orderProcessing':    {'ar': 'جاري تجهيز الطلب', 'he': 'ההזמנה בהכנה',   'en': 'Order Processing'},
    'orderSentToDelivery':{'ar': 'مرسل للتوصيل',    'he': 'נשלח לשליחות',   'en': 'Sent to Courier'},
    'all':                {'ar': 'الكل',           'he': 'הכול',           'en': 'All'},
    'categoriesTitle':    {'ar': 'الأقسام',        'he': 'קטגוריות',       'en': 'Categories'},
    'storeTitle':       {'ar': 'المتجر', 'he': 'חנות', 'en': 'Store'},
    'noProducts':       {'ar': 'لا توجد منتجات', 'he': 'אין מוצרים', 'en': 'No products'},
    'sortNewest':       {'ar': 'الأحدث', 'he': 'החדש ביותר', 'en': 'Newest'},
    'sortPriceAsc':     {'ar': 'السعر ↑', 'he': 'מחיר ↑', 'en': 'Price ↑'},
    'sortPriceDesc':    {'ar': 'السعر ↓', 'he': 'מחיר ↓', 'en': 'Price ↓'},
    'sortRating':       {'ar': 'الأعلى تقييماً', 'he': 'הדירוג הגבוה', 'en': 'Top rated'},
    'sortBestseller':   {'ar': 'الأكثر مبيعاً', 'he': 'הנמכר ביותר', 'en': 'Best selling'},
    'loadPageError':    {'ar': 'تعذّر تحميل الصفحة', 'he': 'טעינת העמוד נכשלה', 'en': 'Failed to load page'},
    'checkInternet':    {'ar': 'تحقّق من اتصالك بالإنترنت وحاول مجدداً', 'he': 'בדוק את חיבור האינטרנט ונסה שוב', 'en': 'Check your connection and try again'},
    'newBadge':         {'ar': '✨ جديد', 'he': '✨ חדש', 'en': '✨ New'},
    'availableBrands':  {'ar': '🏪 الماركات المتوفرة', 'he': '🏪 מותגים זמינים', 'en': '🏪 Available brands'},
    'greetingHello':    {'ar': 'مرحباً', 'he': 'שלום', 'en': 'Hello'},
    'whatsapp':         {'ar': 'واتساب', 'he': 'וואטסאפ', 'en': 'WhatsApp'},
    'addedToCartShort': {'ar': 'تمت الإضافة ✓', 'he': 'נוסף ✓', 'en': 'Added ✓'},
    'orderInquiry':     {'ar': 'استفسار عن طلب', 'he': 'בירור לגבי הזמנה', 'en': 'Inquiry about order'},
    'trackIntro':         {'ar': 'أدخل رقم الطلب لعرض حالة الشحنة.', 'he': 'הזינו את מספר ההזמנה כדי לראות את סטטוס המשלוח.', 'en': 'Enter your order number to see the shipment status.'},
    'trackNumberLabel':   {'ar': 'رقم الطلب',       'he': 'מספר הזמנה',     'en': 'Order Number'},
    'trackSubmit':        {'ar': 'عرض حالة الطلب',  'he': 'הצג סטטוס',      'en': 'Show Status'},
    'trackEnterNumber':   {'ar': 'أدخل رقم الطلب',  'he': 'הזינו מספר הזמנה','en': 'Enter an order number'},
    'orderShipped':       {'ar': 'مع المندوب',       'he': 'עם השליח',       'en': 'With Courier'},
    'orderDelivered':     {'ar': 'تم التوصيل',        'he': 'נמסר',           'en': 'Delivered'},
    'orderCancelled':     {'ar': '❌ تم إلغاء هذا الطلب', 'he': '❌ ההזמנה בוטלה', 'en': '❌ This order was cancelled'},
    'subtotalLabel':      {'ar': 'المجموع الفرعي',   'he': 'סכום ביניים',    'en': 'Subtotal'},
    'discountLabel':      {'ar': 'الخصم',             'he': 'הנחה',           'en': 'Discount'},
    'loyaltyLabel':       {'ar': 'نقاط الولاء',       'he': 'נקודות נאמנות',  'en': 'Loyalty Points'},
    'freeDelivery':       {'ar': 'مجاني 🎉',          'he': 'חינם 🎉',        'en': 'Free 🎉'},
    'totalLabel':         {'ar': 'الإجمالي',          'he': 'סה"כ',           'en': 'Total'},
    'productsLabel':      {'ar': '🛍️ المنتجات',       'he': '🛍️ מוצרים',     'en': '🛍️ Products'},
    'orderNotFound':      {'ar': 'الطلب غير موجود',  'he': 'ההזמנה לא נמצאה','en': 'Order not found'},
    'returnRequested':    {'ar': 'تم إرسال طلب الإرجاع — قيد المراجعة', 'he': 'בקשת ההחזרה נשלחה — בבדיקה', 'en': 'Return request sent — under review'},
    'deliveryTeam':       {'ar': 'فريق التوصيل',      'he': 'צוות המשלוחים',  'en': 'Delivery Team'},
    'driver':             {'ar': 'المندوب',            'he': 'שליח',           'en': 'Courier'},
    'trackingNumberLabel':{'ar': 'رقم تتبّع الشحنة',    'he': 'מספר מעקב משלוח', 'en': 'Shipment tracking number'},
    'returnReason':       {'ar': 'يرجى ذكر سبب طلب الإرجاع:', 'he': 'נא לציין את סיבת הבקשה:', 'en': 'Please state the reason:'},
    'returnHint':         {'ar': 'مثال: المنتج لا يطابق الوصف...', 'he': 'לדוגמה: המוצר לא תואם לתיאור...', 'en': 'e.g. Product doesn\'t match description...'},
    'returnSend':         {'ar': 'إرسال الطلب',       'he': 'שלח בקשה',       'en': 'Submit Request'},
    'returnSuccess':      {'ar': 'تم إرسال طلب الإرجاع بنجاح', 'he': 'בקשת ההחזרה נשלחה בהצלחה', 'en': 'Return request submitted'},
    'returnReasonEmpty':  {'ar': 'يرجى ذكر سبب الإرجاع', 'he': 'נא לציין את הסיבה', 'en': 'Please provide a reason'},
    'orderRef':           {'ar': 'طلب',               'he': 'הזמנה',          'en': 'Order'},

    // ── notifications ────────────────────────────────────────────
    'notifPending':       {'ar': '🕐 طلب جديد بانتظار التأكيد', 'he': '🕐 הזמנה חדשה ממתינה לאישור', 'en': '🕐 New order awaiting confirmation'},
    'notifConfirmed':     {'ar': '✓ تم تأكيد طلبك',    'he': '✓ הזמנתך אושרה',     'en': '✓ Your order was confirmed'},
    'notifProcessing':    {'ar': '📦 جاري تجهيز طلبك', 'he': '📦 ההזמנה שלך בהכנה', 'en': '📦 Your order is being prepared'},
    'notifShipped':       {'ar': '🚚 طلبك في الطريق',  'he': '🚚 ההזמנה שלך בדרך',  'en': '🚚 Your order is on the way'},
    'notifDelivered':     {'ar': '🏠 تم تسليم طلبك',   'he': '🏠 ההזמנה שלך נמסרה', 'en': '🏠 Your order was delivered'},
    'notifCancelled':     {'ar': '❌ تم إلغاء طلبك',   'he': '❌ ההזמנה שלך בוטלה', 'en': '❌ Your order was cancelled'},
    'notifUpdate':        {'ar': 'تحديث على الطلب',    'he': 'עדכון על הזמנה',     'en': 'Order update'},
    'noNotifications':    {'ar': 'لا توجد إشعارات بعد','he': 'אין התראות עדיין',   'en': 'No notifications yet'},
    'noNotifSub':         {'ar': 'ستظهر هنا إشعارات الطلبات والعروض', 'he': 'התראות על הזמנות ומבצעים יופיעו כאן', 'en': 'Order and offer notifications will appear here'},

    // ── home — offers / promo / trust ───────────────────────────
    'sectionOffers':      {'ar': '🔥 عروض اليوم',      'he': '🔥 מבצעי היום',    'en': '🔥 Today\'s Offers'},
    'offersSubtitle':     {'ar': 'خصومات حقيقية على منتجات مختارة', 'he': 'הנחות אמיתיות על מוצרים נבחרים', 'en': 'Real discounts on selected products'},
    'promoCta1':          {'ar': 'احصل على 50 نقطة مجاناً! 🎁', 'he': 'קבל 50 נקודות חינם! 🎁', 'en': 'Get 50 points free! 🎁'},
    'promoCta2':          {'ar': 'ادعُ صديقاً ليسجّل بكودك واكسبا نقاطاً معاً', 'he': 'הזמן חבר להירשם עם הקוד שלך וצברו נקודות ביחד', 'en': 'Invite a friend to register with your code and earn points together'},
    'promoCtaBtn':        {'ar': 'شارك كودك ←',        'he': 'שתף את הקוד ←',   'en': 'Share your code ←'},
    'whyUs':              {'ar': '💎 لماذا تختار أبناء الفريد؟', 'he': '💎 למה לבחור בבני אל-פריד?', 'en': '💎 Why choose Alfared?'},
    'unitProduct':        {'ar': 'منتج',   'he': 'מוצר',    'en': 'Products'},
    'unitCustomer':       {'ar': 'عميل',   'he': 'לקוח',    'en': 'Customers'},
    'unitRating':         {'ar': 'تقييم',  'he': 'דירוג',   'en': 'Rating'},

    // ── loyalty screen ──────────────────────────────────────────
    'yourBalance':        {'ar': 'رصيدك من النقاط',    'he': 'יתרת הנקודות שלך',  'en': 'Your Points Balance'},
    'pointUnit':          {'ar': 'نقطة',               'he': 'נקודות',             'en': 'Points'},
    'howToEarn':          {'ar': 'كيف تكسب النقاط؟',   'he': 'כיצד לצבור נקודות?','en': 'How to earn points?'},
    'howToRedeem':        {'ar': 'كيف تستخدم النقاط؟', 'he': 'כיצד לממש נקודות?', 'en': 'How to redeem points?'},
    'pointsHistory':      {'ar': 'سجل النقاط',         'he': 'היסטוריית נקודות',   'en': 'Points History'},
    'earnExplainBase':    {'ar': 'اكسب نقطة عن كل %s ₪ — تُضاف عند تسليم الطلب.', 'he': 'צבר נקודה על כל %s ₪ — מתווספות עם מסירת ההזמנה.', 'en': 'Earn 1 point for every %s ₪ spent — added on delivery.'},
    'redeemExplainBase':  {'ar': 'كل %s نقطة = 1 ₪ خصم. الحد الأدنى: %s نقطة.', 'he': 'כל %s נקודות = 1 ₪ הנחה. מינימום: %s נקודות.', 'en': 'Every %s points = 1 ₪ discount. Minimum: %s points.'},
    'equivalentIlsBase':  {'ar': 'تساوي تقريباً %s ₪', 'he': 'שווה ערך לכ-%s ₪',  'en': 'Equivalent to approx. %s ₪'},

    // ── rewards screen ──────────────────────────────────────────
    'rewards':            {'ar': 'المكافآت',            'he': 'פרסים',             'en': 'Rewards'},
    'dailyRewards':       {'ar': 'المكافآت اليومية',    'he': 'פרסים יומיים',      'en': 'Daily Rewards'},
    'dailyCheckin':       {'ar': 'الحضور اليومي',       'he': 'כניסה יומית',       'en': 'Daily Check-in'},
    'checkinDone':        {'ar': 'لقد سجّلت حضورك اليوم ✓', 'he': 'כבר נכנסת היום ✓', 'en': 'You checked in today ✓'},
    'claimCheckin':       {'ar': '+ سجّل حضورك الآن',   'he': '+ הכנס עכשיו',      'en': '+ Check in now'},
    'claimedCheckin':     {'ar': '✓ تم التسجيل',        'he': '✓ בוצע',            'en': '✓ Checked in'},
    'inviteFriend':       {'ar': 'ادعُ صديق واكسب نقاطك', 'he': 'הזמן חבר וצבר נקודות', 'en': 'Invite a Friend & Earn'},
    'invitation':         {'ar': 'دعوة',                'he': 'הזמנה',             'en': 'Invite'},
    'codeCopied':         {'ar': 'تم نسخ الكود ✓',       'he': 'הקוד הועתק ✓',     'en': 'Code copied ✓'},
    'checkinMsgBase':     {'ar': 'احصل على %d نقطة كل يوم تفتح فيه التطبيق', 'he': 'קבל %d נקודות בכל יום שתפתח את האפליקציה', 'en': 'Earn %d points every day you open the app'},
    'inviteMsgBase':      {'ar': 'احصل على %d نقطة عن كل صديق يسجّل بكودك', 'he': 'קבל %d נקודות על כל חבר שנרשם עם הקוד שלך', 'en': 'Earn %d points for every friend who registers with your code'},
    'currentStreakBase':   {'ar': 'سلسلة الحضور الحالية: %d يوم', 'he': 'רצף נוכחי: %d ימים', 'en': 'Current streak: %d days'},

    // ── addresses ───────────────────────────────────────────────
    'newAddress':         {'ar': 'عنوان جديد',           'he': 'כתובת חדשה',        'en': 'New Address'},
    'noAddresses':        {'ar': 'لا توجد عناوين بعد',   'he': 'אין כתובות עדיין',  'en': 'No addresses yet'},
    'noAddressesSub':     {'ar': 'احفظ عناوينك لتوفير الوقت عند الطلب', 'he': 'שמור כתובות לחסוך זמן בהזמנה', 'en': 'Save addresses to speed up checkout'},
    'addNewAddress':      {'ar': '+ إضافة عنوان جديد',   'he': '+ הוסף כתובת חדשה', 'en': '+ Add New Address'},
    'defaultTag':         {'ar': 'افتراضي',              'he': 'ברירת מחדל',        'en': 'Default'},
    'deleteAddressTitle': {'ar': 'حذف العنوان؟',         'he': 'מחק כתובת?',        'en': 'Delete Address?'},
    'deleteAddressConfirm':{'ar': 'هل تريد حذف هذا العنوان نهائياً؟', 'he': 'למחוק כתובת זו לצמיתות?', 'en': 'Delete this address permanently?'},
    'addressLabel':       {'ar': 'اسم العنوان',          'he': 'שם הכתובת',         'en': 'Address name'},
    'addressNameHint':    {'ar': 'مثال: البيت',          'he': 'לדוגמה: הבית',      'en': 'e.g. Home'},
    'makeDefault':        {'ar': 'اجعله العنوان الافتراضي', 'he': 'הגדר כברירת מחדל', 'en': 'Set as default'},
    'saveAddress':        {'ar': 'حفظ العنوان',          'he': 'שמור כתובת',        'en': 'Save Address'},
    'saveAddressSuccess': {'ar': '✓ تم حفظ العنوان',     'he': '✓ הכתובת נשמרה',    'en': '✓ Address saved'},

    // ── wishlist ────────────────────────────────────────────────
    'noWishlistItems':    {'ar': 'لا توجد منتجات في المفضلة', 'he': 'אין מוצרים במועדפים', 'en': 'No items in wishlist'},

    // ── account screen ──────────────────────────────────────────
    'editProfile':        {'ar': 'تعديل الملف الشخصي',    'he': 'ערוך פרופיל',           'en': 'Edit Profile'},
    'fullName':           {'ar': 'الاسم الكامل',           'he': 'שם מלא',                'en': 'Full Name'},
    'emailField':         {'ar': 'البريد الإلكتروني',      'he': 'אימייל',                'en': 'Email'},
    'phoneOptional':      {'ar': 'رقم الهاتف (اختياري)',   'he': 'טלפון (אופציונלי)',     'en': 'Phone (optional)'},
    'saveChanges':        {'ar': 'حفظ التغييرات',          'he': 'שמור שינויים',          'en': 'Save Changes'},
    'changesSaved':       {'ar': '✓ تم حفظ التغييرات',     'he': '✓ השינויים נשמרו',      'en': '✓ Changes saved'},
    'saveFailed':         {'ar': 'حدث خطأ، حاول مجدداً',  'he': 'שגיאה, נסה שוב',        'en': 'Error, please try again'},
    'nameRequired':       {'ar': 'الاسم مطلوب',            'he': 'שם חובה',               'en': 'Name is required'},
    'emailInvalid':       {'ar': 'بريد غير صحيح',          'he': 'אימייל לא תקין',        'en': 'Invalid email'},
    'loginPrompt':        {'ar': 'سجّل دخولك للوصول لحسابك', 'he': 'התחבר כדי לגשת לחשבונך', 'en': 'Sign in to access your account'},
    'darkMode':           {'ar': 'الوضع الداكن',           'he': 'מצב כהה',               'en': 'Dark Mode'},
    'followUs':           {'ar': 'تابعنا على',             'he': 'עקוב אחרינו',           'en': 'Follow us on'},
    'contactWhatsapp':    {'ar': 'تواصل واتساب',           'he': 'צור קשר בוואטסאפ',      'en': 'Contact on WhatsApp'},
    'appVersion':         {'ar': 'الإصدار 1.0.0',          'he': 'גרסה 1.0.0',            'en': 'Version 1.0.0'},
    'currentLevel':       {'ar': 'مستواك الحالي',          'he': 'הרמה שלך',              'en': 'Your Level'},
    'spendingLabel':      {'ar': 'إنفاق',                  'he': 'הוצאות',                'en': 'spent'},
    'topTierMsg':         {'ar': 'أنت في أعلى مستوى!',    'he': 'אתה ברמה הגבוהה ביותר!', 'en': 'You\'re at the top tier!'},
    'ordersCountLabel':   {'ar': 'طلب',                    'he': 'הזמנות',                'en': 'orders'},
    'wishlistLabel':      {'ar': 'مفضلة',                  'he': 'מועדפים',               'en': 'saved'},
    'pointsShortLabel':   {'ar': 'نقطة',                   'he': 'נקודות',                'en': 'pts'},
    'loyaltyPointsCard':  {'ar': 'نقاط الولاء',            'he': 'נקודות נאמנות',         'en': 'Loyalty Points'},
    'toReachTierBase':    {'ar': 'للوصول لـ%s',  'he': 'להגיע ל-%s',     'en': 'to reach %s'},
    'remainingAmountBase':{'ar': 'يتبقى %s ₪',  'he': 'נותרו %s ₪',    'en': '%s ₪ remaining'},
    'newTierLabel':       {'ar': 'جديد',          'he': 'חדש',           'en': 'New'},
    'bronzeTierLabel':    {'ar': 'برونزي',         'he': 'ברונזה',        'en': 'Bronze'},
    'silverTierLabel':    {'ar': 'فضي',            'he': 'כסף',           'en': 'Silver'},
    'goldTierLabel':      {'ar': 'ذهبي',           'he': 'זהב',           'en': 'Gold'},

    // ── onboarding ──────────────────────────────────────────────
    'onboardTag1':    {'ar': '✦ متجر التجميل الأول',      'he': '✦ חנות היופי הראשונה',      'en': '✦ #1 Beauty Store'},
    'onboardTitle1':  {'ar': 'اكتشف عالم\nالجمال الفاخر', 'he': 'גלה את עולם\nהיופי היוקרתי', 'en': 'Discover the World\nof Luxury Beauty'},
    'onboardSub1':    {'ar': 'أكثر من 5000 منتج أصيل للمحلات والمتاجر وصفحات التسويق الإلكتروني', 'he': 'יותר מ-5000 מוצרים מקוריים לחנויות ועמודי שיווק', 'en': 'Over 5000 authentic products for shops and online stores'},
    'onboardTag2':    {'ar': '✦ توصيل سريع',              'he': '✦ משלוח מהיר',               'en': '✦ Fast Delivery'},
    'onboardTitle2':  {'ar': 'توصيل سريع\nلجميع فلسطين 🇵🇸','he': 'משלוח מהיר\nלכל פלסטין 🇵🇸', 'en': 'Fast Delivery\nAcross Palestine 🇵🇸'},
    'onboardSub2':    {'ar': 'نوصّل لجميع مناطق فلسطين والداخل في أسرع وقت وبأقل الأسعار', 'he': 'אנחנו מגיעים לכל אזורי פלסטין והפנים במהירות ובמחירים הטובים ביותר', 'en': 'We deliver to all areas of Palestine and inside at the fastest time and lowest prices'},
    'onboardTag3':    {'ar': '✦ برنامج الولاء',           'he': '✦ תוכנית נאמנות',            'en': '✦ Loyalty Program'},
    'onboardTitle3':  {'ar': 'نقاط ومكافآت\nحقيقية',      'he': 'נקודות ופרסים\nאמיתיים',      'en': 'Real Points\n& Rewards'},
    'onboardSub3':    {'ar': 'اجمع نقاطاً مع كل طلب وارتقِ لمستوى VIP واحصل على خصومات حصرية', 'he': 'צבור נקודות עם כל הזמנה, עלה לרמת VIP וקבל הנחות בלעדיות', 'en': 'Earn points with every order, reach VIP level and get exclusive discounts'},
    'onboardNext':    {'ar': 'التالي ←',                  'he': '← הבא',                      'en': 'Next →'},
    'onboardStart':   {'ar': 'ابدأ التسوق 🛍️',            'he': 'התחל לקנות 🛍️',              'en': 'Start Shopping 🛍️'},
    'onboardSkipOr':  {'ar': 'أو ',                       'he': 'או ',                        'en': 'or '},
    'onboardSkip':    {'ar': 'تخطى للمتجر',               'he': 'עבור לחנות',                 'en': 'Skip to store'},

    // ── login ────────────────────────────────────────────────────
    'loginWelcome':   {'ar': 'مرحباً بعودتك! 👋',         'he': 'ברוך שובך! 👋',              'en': 'Welcome back! 👋'},
    'loginSubtitle':  {'ar': 'سجّل دخولك برقم هاتفك',     'he': 'התחבר עם מספר הטלפון שלך',   'en': 'Sign in with your phone number'},
    'loginPhoneLabel':{'ar': 'رقم الهاتف',                 'he': 'מספר טלפון',                 'en': 'Phone Number'},
    'loginPhoneHint': {'ar': 'XXXXXXXXX',                  'he': 'XXXXXXXXX',                  'en': 'XXXXXXXXX'},
    'loginPhoneHelper':{'ar':'9 أرقام (بدون الصفر)',       'he': '9 ספרות (ללא אפס)',          'en': '9 digits (without 0)'},
    'loginPassLabel': {'ar': 'كلمة المرور',                'he': 'סיסמה',                      'en': 'Password'},
    'loginBtn':       {'ar': 'تسجيل الدخول',               'he': 'התחברות',                    'en': 'Sign In'},
    'loginNoAccount': {'ar': 'ليس لديك حساب؟ ',            'he': 'אין לך חשבון? ',             'en': 'Don\'t have an account? '},
    'loginCreate':    {'ar': 'إنشاء حساب',                 'he': 'הרשמה',                      'en': 'Create account'},
    'loginPhoneErr':  {'ar': 'أدخل 9 أرقام صحيحة',        'he': 'הזן 9 ספרות תקינות',         'en': 'Enter 9 valid digits'},
    'loginPassErr':   {'ar': '6 أحرف على الأقل',           'he': 'לפחות 6 תווים',              'en': 'At least 6 characters'},

    // ── register ─────────────────────────────────────────────────
    'registerTitle':  {'ar': 'إنشاء حساب جديد',           'he': 'יצירת חשבון חדש',            'en': 'Create New Account'},
    'registerWelcome':{'ar': 'انضم لعائلة أبناء الفريد 🎉','he': 'הצטרף למשפחת אבנא אלפריד 🎉','en': 'Join Alfared Family 🎉'},
    'registerNameLbl':{'ar': 'الاسم الكامل',               'he': 'שם מלא',                     'en': 'Full Name'},
    'registerNameHint':{'ar':'محمد أبو سالم',              'he': 'מוחמד אבו סאלם',             'en': 'John Smith'},
    'registerNameErr':{'ar': 'الرجاء إدخال الاسم',         'he': 'נא להזין שם',                'en': 'Please enter your name'},
    'registerEmailLbl':{'ar':'البريد الإلكتروني',          'he': 'דואר אלקטרוני',              'en': 'Email Address'},
    'registerEmailErr':{'ar':'بريد غير صحيح',              'he': 'דואר אלקטרוני לא תקין',      'en': 'Invalid email'},
    'registerPassLbl':{'ar': 'كلمة المرور',                'he': 'סיסמה',                      'en': 'Password'},
    'registerPassHelp':{'ar':'8 أحرف على الأقل',           'he': 'לפחות 8 תווים',              'en': 'At least 8 characters'},
    'registerPassErr':{'ar': '8 أحرف على الأقل',           'he': 'לפחות 8 תווים',              'en': 'At least 8 characters'},
    'registerBtn':    {'ar': 'إرسال رمز التأكيد',          'he': 'שלח קוד אימות',              'en': 'Send verification code'},
    'registerWaNote':     {'ar': 'سيتم إرسال رمز التأكيد إلى بريدك الإلكتروني', 'he': 'קוד האימות יישלח לדואר האלקטרוני שלך', 'en': 'A verification code will be sent to your email'},
    'registerDuplicate':  {'ar': 'هذا الرقم أو البريد مسجل مسبقاً',               'he': 'מספר זה או הדואר כבר רשומים',                'en': 'This phone or email is already registered'},
    'registerOtpFailed':  {'ar': 'فشل إرسال رمز التحقق',                          'he': 'שליחת קוד האימות נכשלה',                   'en': 'Failed to send OTP'},

    // ── account deletion ─────────────────────────────────────────
    'deleteAccount':        {'ar': 'حذف الحساب',                         'he': 'מחק חשבון',                          'en': 'Delete Account'},
    'deleteAccountTitle':   {'ar': 'حذف الحساب نهائياً',                 'he': 'מחיקת חשבון לצמיתות',               'en': 'Permanently Delete Account'},
    'deleteAccountMsg':     {'ar': 'سيتم حذف حسابك وجميع بياناتك نهائياً، ولا يمكن التراجع عن هذا الإجراء.', 'he': 'חשבונך וכל הנתונים שלך יימחקו לצמיתות ולא ניתן לבטל פעולה זו.', 'en': 'Your account and all data will be permanently deleted. This cannot be undone.'},
    'deleteAccountConfirm': {'ar': 'نعم، احذف حسابي',                   'he': 'כן, מחק את חשבוני',                 'en': 'Yes, Delete My Account'},
    'deleteAccountDone':    {'ar': '✓ تم حذف حسابك بنجاح',             'he': '✓ החשבון שלך נמחק בהצלחה',          'en': '✓ Your account has been deleted'},
    'deleteAccountFail':    {'ar': 'حدث خطأ، حاول مجدداً',             'he': 'שגיאה, נסה שוב',                    'en': 'An error occurred, please try again'},

    // ── language picker ──────────────────────────────────────────
    'chooseLanguage':       {'ar': 'اختر لغتك',                         'he': 'בחר שפה',                           'en': 'Choose Your Language'},

    // ── home marquee + flash sale + loyalty card ────────────────
    'marqueeText':        {
      'ar': '   🛵 توصيل لجميع مناطق فلسطين والداخل   •   🏆 أكثر من 10,000 عميل سعيد   •   🎁 اكسب نقاط مع كل طلب   •   💳 أسعار الجملة للمحلات والمتاجر   •   🗂️ +5000 منتج متوفر   •   ',
      'he': '   🛵 משלוח לכל אזורי פלסטין והפנים   •   🏆 יותר מ-10,000 לקוחות מרוצים   •   🎁 צבור נקודות עם כל הזמנה   •   💳 מחירי סיטונאי לחנויות   •   🗂️ +5000 מוצרים זמינים   •   ',
      'en': '   🛵 Delivery to all areas of Palestine   •   🏆 Over 10,000 happy customers   •   🎁 Earn points with every order   •   💳 Wholesale prices for shops   •   🗂️ +5000 products available   •   ',
    },
    'flashSaleTitle':     {'ar': 'عروض اليوم فقط!',                    'he': 'מבצעים להיום בלבד!',             'en': 'Today\'s Deals Only!'},
    'flashSaleSubtitle':  {'ar': 'لا تفوّت الفرصة — الأسعار تتغير غداً','he': 'אל תפספס — המחירים משתנים מחר', 'en': 'Don\'t miss out — prices change tomorrow'},
    'flashSaleEndsIn':    {'ar': 'ينتهي خلال',                          'he': 'מסתיים בעוד',                    'en': 'Ends in'},
    'timeUnitHour':       {'ar': 'س',   'he': 'ש',   'en': 'h'},
    'timeUnitMin':        {'ar': 'د',   'he': 'ד',   'en': 'm'},
    'timeUnitSec':        {'ar': 'ث',   'he': 'ש',   'en': 's'},
    'badgeOffer':         {'ar': '💥 عرض', 'he': '💥 מבצע', 'en': '💥 Deal'},
    'badgeTrending':      {'ar': '⭐ رائج', 'he': '⭐ פופולרי', 'en': '⭐ Trending'},
    'loyaltyCardTitle':   {'ar': 'نقاط الولاء الخاصة بك', 'he': 'נקודות הנאמנות שלך', 'en': 'Your Loyalty Points'},
    'loyaltyPointUnit':   {'ar': 'نقطة', 'he': 'נקודות', 'en': 'pts'},
    'loyaltyProgressBase':{'ar': '%d% نحو مستوى %s', 'he': '%d% לקראת רמה %s', 'en': '%d% toward %s tier'},

    // ── guest wall ──────────────────────────────────────────────
    'guestLoginTitle': {'ar': 'سجّل دخولك للمتابعة',                                                                   'he': 'התחבר להמשיך',                                        'en': 'Sign in to continue'},
    'guestLoginSub':   {'ar': 'سجّل الدخول أو أنشئ حساباً للوصول إلى السلة والمفضلة والطلبات',                       'he': 'התחבר או צור חשבון לגישה לעגלה, מועדפים והזמנות',     'en': 'Sign in or create an account to access cart, wishlist & orders'},
    'continueAsGuest': {'ar': 'تصفح بدون حساب',                                                                        'he': 'גלוש ללא חשבון',                                       'en': 'Browse without account'},

    // ── language names ──────────────────────────────────────────
    'lang_ar':            {'ar': 'العربية',    'he': 'ערבית',   'en': 'Arabic'},
    'lang_he':            {'ar': 'العبرية',    'he': 'עברית',   'en': 'Hebrew'},
    'lang_en':            {'ar': 'الإنجليزية', 'he': 'אנגלית',  'en': 'English'},
    'chooseLanguage':     {'ar': 'اختر اللغة', 'he': 'בחר שפה', 'en': 'Choose Language'},
  };
}
