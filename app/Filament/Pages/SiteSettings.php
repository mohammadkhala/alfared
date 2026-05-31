<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SiteSettings extends Page implements Forms\Contracts\HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'إعدادات الموقع';
    protected static ?string $title           = 'إعدادات الموقع';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?int    $navigationSort  = 0;
    protected static string  $view            = 'filament.pages.site-settings';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasPermission('manage_settings') ?? false;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $s = Setting::all()->pluck('value', 'key')->toArray();

        $this->form->fill([
            // ── Store Info ──
            'store_name'     => $s['store_name']     ?? 'الفارد',
            'store_name_he'  => $s['store_name_he']  ?? 'אל-פארד',
            'store_tagline'  => $s['store_tagline']  ?? 'جمالك يبدأ من هنا',
            'store_phone'    => $s['store_phone']    ?? '+970598191312',
            'store_email'    => $s['store_email']    ?? 'info@alfared.ps',
            'store_address'  => $s['store_address']  ?? 'رام الله',
            'store_city'     => $s['store_city']     ?? 'رام الله',
            'store_country'  => $s['store_country']  ?? 'Palestine',
            'store_logo'     => $s['store_logo']     ?? null,
            'store_favicon'  => $s['store_favicon']  ?? null,

            // ── Social ──
            'social_whatsapp'   => $s['social_whatsapp']   ?? '+970598191312',
            'social_instagram'  => $s['social_instagram']  ?? 'alfared.store',
            'social_facebook'   => $s['social_facebook']   ?? '',
            'social_tiktok'     => $s['social_tiktok']     ?? '',
            'social_youtube'    => $s['social_youtube']    ?? '',

            // ── Shipping ──
            'min_order_amount'        => $s['min_order_amount']        ?? '0',
            'free_shipping_above'     => $s['free_shipping_above']     ?? '0',
            'default_delivery_fee'    => $s['default_delivery_fee']    ?? '15',
            'cod_enabled'             => isset($s['cod_enabled'])    ? ($s['cod_enabled'] === '1' || $s['cod_enabled'] === 'true') : true,
            'online_payment_enabled'  => isset($s['online_payment_enabled']) ? ($s['online_payment_enabled'] === '1' || $s['online_payment_enabled'] === 'true') : false,
            'payment_gateway_key'     => $s['payment_gateway_key']     ?? '',

            // ── SEO ──
            'meta_title'              => $s['meta_title']           ?? 'الفارد | متجر مستحضرات التجميل',
            'meta_description'        => $s['meta_description']     ?? 'تسوقي أفضل منتجات التجميل والعناية بالبشرة والشعر من متجر الفارد',
            'meta_keywords'           => isset($s['meta_keywords'])  ? json_decode($s['meta_keywords'], true) : ['تجميل','عطور','مكياج','عناية بالبشرة'],
            'google_analytics_id'     => $s['google_analytics_id']  ?? '',
            'google_tag_manager_id'   => $s['google_tag_manager_id'] ?? '',
            'facebook_pixel_id'       => $s['facebook_pixel_id']    ?? '',

            // ── Notifications ──
            'admin_email_notifications' => $s['admin_email_notifications'] ?? 'info@alfared.ps',
            'notify_new_order'          => isset($s['notify_new_order'])    ? ($s['notify_new_order'] === '1' || $s['notify_new_order'] === 'true') : true,
            'notify_low_stock'          => isset($s['notify_low_stock'])    ? ($s['notify_low_stock'] === '1' || $s['notify_low_stock'] === 'true') : true,
            'notify_new_review'         => isset($s['notify_new_review'])   ? ($s['notify_new_review'] === '1' || $s['notify_new_review'] === 'true') : false,
            'notify_cancelled_order'    => isset($s['notify_cancelled_order']) ? ($s['notify_cancelled_order'] === '1' || $s['notify_cancelled_order'] === 'true') : true,

            // ── Loyalty ──
            'loyalty_enabled'          => isset($s['loyalty_enabled'])    ? ($s['loyalty_enabled'] === '1' || $s['loyalty_enabled'] === 'true') : true,
            'earn_amount_per_point'    => $s['earn_amount_per_point']    ?? '10',
            'earn_on_status'           => $s['earn_on_status']           ?? 'delivered',
            'earn_multiplier'          => $s['earn_multiplier']          ?? '1.0',
            'include_discount_in_earn' => isset($s['include_discount_in_earn']) ? ($s['include_discount_in_earn'] === '1' || $s['include_discount_in_earn'] === 'true') : false,
            'include_delivery_in_earn' => isset($s['include_delivery_in_earn']) ? ($s['include_delivery_in_earn'] === '1' || $s['include_delivery_in_earn'] === 'true') : false,
            'first_order_bonus'        => $s['first_order_bonus']        ?? '0',
            'signup_bonus'             => $s['signup_bonus']             ?? '0',
            'points_per_redeem_ils'    => $s['points_per_redeem_ils']    ?? '20',
            'min_redeem_points'        => $s['min_redeem_points']        ?? '100',
            'max_redeem_percent'       => $s['max_redeem_percent']       ?? '50',
            'points_expiry_days'       => $s['points_expiry_days']       ?? '0',
            'daily_checkin_points'     => $s['daily_checkin_points']     ?? '5',
            'referral_bonus'           => $s['referral_bonus']           ?? '50',

            // ── Tiers ──
            'tier_min_bronze' => $s['tier_min_bronze'] ?? '100',
            'tier_min_silver' => $s['tier_min_silver'] ?? '500',
            'tier_min_gold'   => $s['tier_min_gold']   ?? '1500',
            'tier_min_vip'    => $s['tier_min_vip']    ?? '3000',

            // ── Maintenance ──
            'maintenance_mode'         => isset($s['maintenance_mode'])  ? ($s['maintenance_mode'] === '1') : false,
            'maintenance_type'         => $s['maintenance_type']         ?? 'maintenance',
            'maintenance_title'        => $s['maintenance_title']        ?? 'نحن تحت الصيانة',
            'maintenance_message'      => $s['maintenance_message']      ?? 'نعمل على تحسين الموقع لنقدم لك تجربة أفضل — سنعود قريباً!',
            'maintenance_launch_date'  => $s['maintenance_launch_date']  ?? '',
            'maintenance_whatsapp'     => $s['maintenance_whatsapp']     ?? ($s['store_phone'] ?? '+970598191312'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()->tabs([

                    // ── Tab 1: Store Info ──────────────────────────────────
                    Forms\Components\Tabs\Tab::make('معلومات المتجر')
                        ->icon('heroicon-o-building-storefront')
                        ->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('store_name')
                                    ->label('اسم المتجر (عربي)')
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('store_name_he')
                                    ->label('اسم المتجر (عبري)')
                                    ->maxLength(100),
                            ]),
                            Forms\Components\TextInput::make('store_tagline')
                                ->label('الشعار / الوصف المختصر')
                                ->maxLength(200)
                                ->columnSpanFull(),
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('store_phone')
                                    ->label('رقم الهاتف / الواتساب')
                                    ->tel(),
                                Forms\Components\TextInput::make('store_email')
                                    ->label('البريد الإلكتروني الرسمي')
                                    ->email(),
                            ]),
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('store_address')
                                    ->label('العنوان'),
                                Forms\Components\TextInput::make('store_city')
                                    ->label('المدينة'),
                            ]),
                            Forms\Components\TextInput::make('store_country')
                                ->label('الدولة')
                                ->default('Palestine'),
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\FileUpload::make('store_logo')
                                    ->label('شعار المتجر (Logo)')
                                    ->image()
                                    ->directory('settings')
                                    ->imageEditor(),
                                Forms\Components\FileUpload::make('store_favicon')
                                    ->label('Favicon (16×16 أو 32×32)')
                                    ->image()
                                    ->directory('settings'),
                            ]),
                        ]),

                    // ── Tab 2: Social ──────────────────────────────────────
                    Forms\Components\Tabs\Tab::make('التواصل الاجتماعي')
                        ->icon('heroicon-o-share')
                        ->schema([
                            Forms\Components\TextInput::make('social_whatsapp')
                                ->label('رقم الواتساب')
                                ->placeholder('+972501234567')
                                ->helperText('يُستخدم في رابط التواصل السريع'),
                            Forms\Components\TextInput::make('social_instagram')
                                ->label('Instagram')
                                ->placeholder('@alfared.store')
                                ->prefix('instagram.com/'),
                            Forms\Components\TextInput::make('social_facebook')
                                ->label('Facebook')
                                ->placeholder('رابط الصفحة الكاملة'),
                            Forms\Components\TextInput::make('social_tiktok')
                                ->label('TikTok')
                                ->placeholder('@alfared'),
                            Forms\Components\TextInput::make('social_youtube')
                                ->label('YouTube')
                                ->placeholder('رابط القناة'),
                        ]),

                    // ── Tab 3: Shipping & Payment ──────────────────────────
                    Forms\Components\Tabs\Tab::make('الشحن والدفع')
                        ->icon('heroicon-o-truck')
                        ->schema([
                            Forms\Components\Grid::make(3)->schema([
                                Forms\Components\TextInput::make('min_order_amount')
                                    ->label('الحد الأدنى للطلب')
                                    ->numeric()
                                    ->suffix('₪')
                                    ->default(0),
                                Forms\Components\TextInput::make('free_shipping_above')
                                    ->label('شحن مجاني فوق')
                                    ->numeric()
                                    ->suffix('₪')
                                    ->helperText('0 = دائماً مدفوع'),
                                Forms\Components\TextInput::make('default_delivery_fee')
                                    ->label('رسوم الشحن الافتراضية')
                                    ->numeric()
                                    ->suffix('₪'),
                            ]),
                            Forms\Components\Section::make('طرق الدفع')->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\Toggle::make('cod_enabled')
                                        ->label('الدفع عند الاستلام (COD)')
                                        ->default(true),
                                    Forms\Components\Toggle::make('online_payment_enabled')
                                        ->label('الدفع الإلكتروني')
                                        ->default(false),
                                ]),
                                Forms\Components\TextInput::make('payment_gateway_key')
                                    ->label('مفتاح بوابة الدفع (API Key)')
                                    ->password()
                                    ->revealable()
                                    ->helperText('يُخزَّن بشكل آمن'),
                            ]),
                        ]),

                    // ── Tab 4: SEO ─────────────────────────────────────────
                    Forms\Components\Tabs\Tab::make('SEO')
                        ->icon('heroicon-o-magnifying-glass')
                        ->schema([
                            Forms\Components\TextInput::make('meta_title')
                                ->label('عنوان الموقع (Meta Title)')
                                ->maxLength(70)
                                ->helperText('الحد الأقصى المُوصى به: 70 حرفاً'),
                            Forms\Components\Textarea::make('meta_description')
                                ->label('وصف الموقع (Meta Description)')
                                ->rows(3)
                                ->maxLength(160)
                                ->helperText('الحد الأقصى المُوصى به: 160 حرفاً'),
                            Forms\Components\TagsInput::make('meta_keywords')
                                ->label('الكلمات المفتاحية')
                                ->separator(','),
                            Forms\Components\Section::make('أدوات القياس والتتبع')->schema([
                                Forms\Components\Grid::make(3)->schema([
                                    Forms\Components\TextInput::make('google_analytics_id')
                                        ->label('Google Analytics ID')
                                        ->placeholder('G-XXXXXXXXXX'),
                                    Forms\Components\TextInput::make('google_tag_manager_id')
                                        ->label('Google Tag Manager ID')
                                        ->placeholder('GTM-XXXXXXX'),
                                    Forms\Components\TextInput::make('facebook_pixel_id')
                                        ->label('Facebook Pixel ID')
                                        ->placeholder('XXXXXXXXXXXXXXX'),
                                ]),
                            ]),
                        ]),

                    // ── Tab: Loyalty ─────────────────────────────────────
                    Forms\Components\Tabs\Tab::make('نقاط الولاء')
                        ->icon('heroicon-o-star')
                        ->schema([
                            Forms\Components\Toggle::make('loyalty_enabled')
                                ->label('تفعيل نظام نقاط الولاء')
                                ->helperText('عند الإيقاف، لن يكسب العملاء نقاطاً جديدة ولن يستطيعوا استخدام نقاطهم.')
                                ->default(true),

                            Forms\Components\Section::make('🎯 الكسب — كيف يحصل العميل على النقاط')
                                ->schema([
                                    Forms\Components\Grid::make(2)->schema([
                                        Forms\Components\TextInput::make('earn_amount_per_point')
                                            ->label('يكسب العميل نقطة واحدة عن كل')
                                            ->numeric()->suffix('₪')->default(10)
                                            ->helperText('مثال: 10 ₪ = نقطة واحدة'),
                                        Forms\Components\Select::make('earn_on_status')
                                            ->label('متى تُضاف النقاط؟')
                                            ->options([
                                                'delivered' => '✓ عند تسليم الطلب (الأكثر أماناً)',
                                                'confirmed' => '⚡ عند تأكيد الطلب',
                                                'shipped'   => '🚚 عند شحن الطلب',
                                            ])
                                            ->default('delivered'),
                                    ]),
                                    Forms\Components\Grid::make(3)->schema([
                                        Forms\Components\TextInput::make('earn_multiplier')
                                            ->label('مضاعف النقاط (ترويج)')
                                            ->numeric()->step('0.1')->default(1.0)->minValue(0.1)
                                            ->helperText('×1.0 = افتراضي، ×2.0 = ضعف النقاط (للعروض)'),
                                        Forms\Components\Toggle::make('include_discount_in_earn')
                                            ->label('احسب النقاط بعد الخصم')
                                            ->helperText('عند التفعيل، تُحتسب النقاط على المبلغ الفعلي بعد خصم الكوبون')
                                            ->inline(false),
                                        Forms\Components\Toggle::make('include_delivery_in_earn')
                                            ->label('أضف رسوم التوصيل')
                                            ->helperText('احتساب النقاط على رسوم التوصيل أيضاً')
                                            ->inline(false),
                                    ]),
                                    Forms\Components\Grid::make(2)->schema([
                                        Forms\Components\TextInput::make('first_order_bonus')
                                            ->label('🎁 مكافأة أول طلب')
                                            ->numeric()->default(0)->suffix('نقطة')
                                            ->helperText('نقاط إضافية يحصل عليها العميل في أول طلب مكتمل (0 لإيقافها)'),
                                        Forms\Components\TextInput::make('signup_bonus')
                                            ->label('🎉 مكافأة التسجيل')
                                            ->numeric()->default(0)->suffix('نقطة')
                                            ->helperText('نقاط تُعطى للعميل عند إنشاء حساب جديد'),
                                    ]),
                                ]),

                            Forms\Components\Section::make('🎮 ميزات المشاركة (Engagement)')
                                ->schema([
                                    Forms\Components\Grid::make(2)->schema([
                                        Forms\Components\TextInput::make('daily_checkin_points')
                                            ->label('🌟 نقاط الحضور اليومي')
                                            ->numeric()->default(5)->suffix('نقطة')
                                            ->helperText('يحصل العميل عليها كل يوم يفتح فيه التطبيق ويضغط حضور (0 لإيقافها)'),
                                        Forms\Components\TextInput::make('referral_bonus')
                                            ->label('🎁 مكافأة الإحالة')
                                            ->numeric()->default(50)->suffix('نقطة')
                                            ->helperText('نقاط تُعطى للعميل الذي يدعو صديقاً عند تسجيل صديقه'),
                                    ]),
                                ]),

                            Forms\Components\Section::make('💸 الاستخدام — كيف يستفيد العميل من النقاط')
                                ->schema([
                                    Forms\Components\Grid::make(3)->schema([
                                        Forms\Components\TextInput::make('points_per_redeem_ils')
                                            ->label('عدد النقاط = 1 ₪ خصم')
                                            ->numeric()->default(20)
                                            ->helperText('مثال: 20 نقطة = 1 ₪'),
                                        Forms\Components\TextInput::make('min_redeem_points')
                                            ->label('الحد الأدنى للاستخدام')
                                            ->numeric()->default(100)
                                            ->suffix('نقطة'),
                                        Forms\Components\TextInput::make('max_redeem_percent')
                                            ->label('أقصى نسبة خصم من الطلب')
                                            ->numeric()->minValue(0)->maxValue(100)->default(50)
                                            ->suffix('%')
                                            ->helperText('50% = لا يمكن تخفيض أكثر من نصف قيمة الطلب'),
                                    ]),
                                    Forms\Components\TextInput::make('points_expiry_days')
                                        ->label('⏰ صلاحية النقاط')
                                        ->numeric()->default(0)->suffix('يوم')
                                        ->helperText('عدد الأيام قبل انتهاء صلاحية النقاط (0 = لا تنتهي صلاحيتها أبداً)'),
                                ]),
                        ]),

                    // ── Tab 5: Notifications ──────────────────────────────
                    Forms\Components\Tabs\Tab::make('الإشعارات')
                        ->icon('heroicon-o-bell')
                        ->schema([
                            Forms\Components\TextInput::make('admin_email_notifications')
                                ->label('البريد الإلكتروني لاستقبال الإشعارات')
                                ->email()
                                ->helperText('ستُرسَل إليه الإشعارات الإدارية'),
                            Forms\Components\Section::make('إشعارات البريد الإلكتروني')->schema([
                                Forms\Components\Toggle::make('notify_new_order')
                                    ->label('طلب جديد')
                                    ->helperText('إشعار فوري عند وصول طلب جديد')
                                    ->default(true),
                                Forms\Components\Toggle::make('notify_low_stock')
                                    ->label('مخزون منخفض')
                                    ->helperText('إشعار عند نزول مخزون منتج عن الحد الأدنى')
                                    ->default(true),
                                Forms\Components\Toggle::make('notify_new_review')
                                    ->label('تقييم جديد')
                                    ->helperText('إشعار عند إضافة تقييم للمنتجات')
                                    ->default(false),
                                Forms\Components\Toggle::make('notify_cancelled_order')
                                    ->label('إلغاء طلب')
                                    ->helperText('إشعار عند إلغاء أي طلب')
                                    ->default(true),
                            ]),
                        ]),

                    // ── Tab: Customer Tiers ─────────────────────────────
                    Forms\Components\Tabs\Tab::make('🏅 تصنيفات العملاء')
                        ->schema([
                            Forms\Components\Section::make('حدود الإنفاق لكل مستوى')
                                ->description('يُصنَّف العميل تلقائياً بناءً على مجموع مشترياته المكتملة')
                                ->schema([
                                    Forms\Components\Grid::make(2)->schema([
                                        Forms\Components\TextInput::make('tier_min_bronze')
                                            ->label('🥉 برونزي — الحد الأدنى (₪)')
                                            ->numeric()
                                            ->default(\App\Services\TierService::DEFAULT_BRONZE)
                                            ->minValue(1)
                                            ->suffix('₪')
                                            ->helperText('من هذا المبلغ يصبح العميل برونزياً'),

                                        Forms\Components\TextInput::make('tier_min_silver')
                                            ->label('🥈 فضي — الحد الأدنى (₪)')
                                            ->numeric()
                                            ->default(\App\Services\TierService::DEFAULT_SILVER)
                                            ->minValue(1)
                                            ->suffix('₪')
                                            ->helperText('من هذا المبلغ يصبح العميل فضياً'),

                                        Forms\Components\TextInput::make('tier_min_gold')
                                            ->label('🥇 ذهبي — الحد الأدنى (₪)')
                                            ->numeric()
                                            ->default(\App\Services\TierService::DEFAULT_GOLD)
                                            ->minValue(1)
                                            ->suffix('₪')
                                            ->helperText('من هذا المبلغ يصبح العميل ذهبياً'),

                                        Forms\Components\TextInput::make('tier_min_vip')
                                            ->label('💎 VIP — الحد الأدنى (₪)')
                                            ->numeric()
                                            ->default(\App\Services\TierService::DEFAULT_VIP)
                                            ->minValue(1)
                                            ->suffix('₪')
                                            ->helperText('من هذا المبلغ يصبح العميل VIP'),
                                    ]),
                                ]),

                            Forms\Components\Section::make('المستويات الحالية')
                                ->description('نظرة عامة — التصنيف يُحسب لحظياً من مجموع طلبات العميل المكتملة')
                                ->schema([
                                    Forms\Components\Placeholder::make('tier_preview')
                                        ->label('')
                                        ->content(function () {
                                            $tiers = \App\Services\TierService::tiers();
                                            $rows  = '';
                                            foreach ($tiers as $i => $t) {
                                                $next = $tiers[$i + 1] ?? null;
                                                $range = $next
                                                    ? $t['min'] . ' – ' . ($next['min'] - 1) . ' ₪'
                                                    : $t['min'] . ' ₪ فأكثر';
                                                $rows .= "<tr><td style='padding:6px 12px;font-size:18px'>{$t['emoji']}</td>"
                                                    . "<td style='padding:6px 12px;font-weight:700'>{$t['label']}</td>"
                                                    . "<td style='padding:6px 12px;color:#888'>{$range}</td></tr>";
                                            }
                                            return new \Illuminate\Support\HtmlString(
                                                "<table style='border-collapse:collapse;width:100%'>{$rows}</table>"
                                            );
                                        }),
                                ]),
                        ]),

                    // ── Tab: Maintenance ──────────────────────────────────
                    Forms\Components\Tabs\Tab::make('🚧 وضع الصيانة')
                        ->icon('heroicon-o-wrench-screwdriver')
                        ->schema([
                            Forms\Components\Section::make('تفعيل وضع الصيانة / قريباً')
                                ->description('عند التفعيل، سيرى الزوار صفحة "تحت الصيانة" بدلاً من الموقع. لوحة التحكم تبقى تعمل.')
                                ->schema([
                                    Forms\Components\Toggle::make('maintenance_mode')
                                        ->label('تفعيل وضع الصيانة')
                                        ->helperText('عند التشغيل → يُوجَّه كل الزوار لصفحة الصيانة فوراً')
                                        ->onColor('danger')
                                        ->offColor('success')
                                        ->live()
                                        ->afterStateUpdated(function ($state) {
                                            \App\Models\Setting::set('maintenance_mode', $state ? '1' : '0', 'maintenance');
                                        }),

                                    Forms\Components\Select::make('maintenance_type')
                                        ->label('نوع الصفحة')
                                        ->options([
                                            'maintenance' => '🔧 تحت الصيانة',
                                            'coming_soon' => '🚀 قريباً',
                                        ])
                                        ->default('maintenance')
                                        ->helperText('اختر التصميم المناسب للصفحة'),

                                    Forms\Components\TextInput::make('maintenance_title')
                                        ->label('العنوان الرئيسي')
                                        ->placeholder('نحن نُحسّن تجربتك')
                                        ->helperText('يظهر بخط كبير في منتصف الصفحة'),

                                    Forms\Components\Textarea::make('maintenance_message')
                                        ->label('الرسالة للزوار')
                                        ->rows(3)
                                        ->placeholder('نعمل على تحسين الموقع وسنعود قريباً...'),

                                    Forms\Components\TextInput::make('maintenance_launch_date')
                                        ->label('تاريخ الإطلاق (اختياري)')
                                        ->placeholder('2026-06-15')
                                        ->helperText('يظهر عداد تنازلي في صفحة "قريباً"'),

                                    Forms\Components\TextInput::make('maintenance_whatsapp')
                                        ->label('رقم واتساب للتواصل')
                                        ->placeholder('+970598191312')
                                        ->helperText('يظهر في صفحة الصيانة / قريباً'),
                                ]),
                        ]),

                ])->columnSpanFull(),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('حفظ الإعدادات')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $groups = [
            'store'         => ['store_name','store_name_he','store_tagline','store_phone','store_email','store_address','store_city','store_country','store_logo','store_favicon'],
            'social'        => ['social_whatsapp','social_instagram','social_facebook','social_tiktok','social_youtube'],
            'shipping'      => ['min_order_amount','free_shipping_above','default_delivery_fee','cod_enabled','online_payment_enabled','payment_gateway_key'],
            'seo'           => ['meta_title','meta_description','meta_keywords','google_analytics_id','google_tag_manager_id','facebook_pixel_id'],
            'notifications' => ['admin_email_notifications','notify_new_order','notify_low_stock','notify_new_review','notify_cancelled_order'],
            'loyalty'       => [
                'loyalty_enabled','earn_amount_per_point','earn_on_status',
                'points_per_redeem_ils','min_redeem_points','max_redeem_percent',
                'earn_multiplier','include_discount_in_earn','include_delivery_in_earn',
                'first_order_bonus','signup_bonus','points_expiry_days',
                'daily_checkin_points','referral_bonus',
            ],
            'tiers'         => ['tier_min_bronze', 'tier_min_silver', 'tier_min_gold', 'tier_min_vip'],
            'maintenance'   => ['maintenance_mode','maintenance_type','maintenance_title','maintenance_message','maintenance_launch_date','maintenance_whatsapp'],
        ];

        foreach ($groups as $group => $keys) {
            foreach ($keys as $key) {
                if (array_key_exists($key, $data)) {
                    $raw = $data[$key];
                    if (is_array($raw)) {
                        $value = json_encode($raw);
                    } elseif (is_bool($raw)) {
                        $value = $raw ? '1' : '0';
                    } else {
                        $value = (string) ($raw ?? '');
                    }
                    Setting::set($key, $value, $group);
                }
            }
        }

        Notification::make()
            ->title('تم حفظ الإعدادات بنجاح')
            ->success()
            ->send();
    }
}
