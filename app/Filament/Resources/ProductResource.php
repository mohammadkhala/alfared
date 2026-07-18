<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'المنتجات';
    protected static ?string $modelLabel = 'منتج';
    protected static ?string $pluralModelLabel = 'المنتجات';
    protected static ?string $navigationGroup = 'المتجر';
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermission('manage_products') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make()->tabs([

                Forms\Components\Tabs\Tab::make('المعلومات الأساسية')->schema([

                    // ── أسماء المنتج بالثلاث لغات ────────────────────────
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('name_ar')
                            ->label('اسم المنتج (عربي) *')
                            ->required()->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set, $record) {
                                if (!$record) {
                                    $base = \Illuminate\Support\Str::slug($state);
                                    $slug = $base . '-' . rand(100, 999);
                                    while (\App\Models\Product::where('slug', $slug)->exists()) {
                                        $slug = $base . '-' . rand(100, 999);
                                    }
                                    $set('slug', $slug);
                                }
                            }),
                        Forms\Components\TextInput::make('name_en')
                            ->label('اسم المنتج (إنجليزي)')
                            ->maxLength(255)
                            ->helperText('يُملأ تلقائياً عبر زر الترجمة'),
                        Forms\Components\TextInput::make('name_he')
                            ->label('اسم المنتج (عبري)')
                            ->maxLength(255)
                            ->helperText('يُملأ تلقائياً عبر زر الترجمة'),
                    ]),

                    Forms\Components\TextInput::make('slug')
                        ->label('الرابط (Slug)')
                        ->unique(ignoreRecord: true)->maxLength(255)
                        ->disabled()->dehydrated()
                        ->helperText('يتولّد تلقائياً من اسم المنتج'),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('القسم')
                            ->options(Category::where('is_active', true)->pluck('name_ar', 'id'))
                            ->searchable()->required(),
                        Forms\Components\Select::make('brand_id')
                            ->label('الماركة')
                            ->options(Brand::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()->nullable(),
                    ]),

                    Forms\Components\Textarea::make('short_description')
                        ->label('وصف قصير (عربي)')->rows(2)->maxLength(500),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Textarea::make('short_description_en')
                            ->label('وصف قصير (إنجليزي)')->rows(2)->maxLength(500)
                            ->helperText('يُملأ تلقائياً عبر زر الترجمة'),
                        Forms\Components\Textarea::make('short_description_he')
                            ->label('وصف قصير (عبري)')->rows(2)->maxLength(500)
                            ->helperText('يُملأ تلقائياً عبر زر الترجمة'),
                    ]),

                    Forms\Components\RichEditor::make('description_ar')
                        ->label('الوصف الكامل (عربي)')
                        ->toolbarButtons(['bold','italic','bulletList','orderedList','link'])
                        ->columnSpanFull(),

                    // ── زر الترجمة التلقائية ──────────────────────────────
                    Forms\Components\Actions::make([
                        Forms\Components\Actions\Action::make('auto_translate')
                            ->label('🌐 ترجم تلقائياً ← إنجليزي + عبري')
                            ->color('info')
                            ->icon('heroicon-o-language')
                            ->requiresConfirmation()
                            ->modalHeading('ترجمة تلقائية للمنتج')
                            ->modalDescription('سيتم ترجمة الاسم والوصف العربي إلى الإنجليزية والعبرية. أي نص مكتوب مسبقاً في حقول اللغتين سيُستبدل.')
                            ->modalSubmitActionLabel('ترجم الآن')
                            ->action(function (Forms\Get $get, Forms\Set $set) {
                                $translator = app(\App\Services\TranslationService::class);

                                $nameAr      = trim($get('name_ar') ?? '');
                                $descAr      = trim(strip_tags($get('description_ar') ?? ''));
                                $shortDescAr = trim($get('short_description') ?? '');
                                $errors = [];

                                if ($nameAr !== '') {
                                    $nameEn = $translator->translate($nameAr, 'ar', 'en');
                                    $nameHe = $translator->translate($nameAr, 'ar', 'he');
                                    if ($nameEn) $set('name_en', $nameEn);
                                    else         $errors[] = 'الاسم → إنجليزي';
                                    if ($nameHe) $set('name_he', $nameHe);
                                    else         $errors[] = 'الاسم → عبري';
                                }

                                if ($shortDescAr !== '') {
                                    $shortEn = $translator->translate($shortDescAr, 'ar', 'en');
                                    $shortHe = $translator->translate($shortDescAr, 'ar', 'he');
                                    if ($shortEn) $set('short_description_en', $shortEn);
                                    else          $errors[] = 'الوصف القصير → إنجليزي';
                                    if ($shortHe) $set('short_description_he', $shortHe);
                                    else          $errors[] = 'الوصف القصير → عبري';
                                }

                                if ($descAr !== '') {
                                    $descEn = $translator->translate($descAr, 'ar', 'en');
                                    $descHe = $translator->translate($descAr, 'ar', 'he');
                                    if ($descEn) $set('description_en', '<p>' . htmlspecialchars($descEn, ENT_QUOTES) . '</p>');
                                    else         $errors[] = 'الوصف → إنجليزي';
                                    if ($descHe) $set('description_he', '<p>' . htmlspecialchars($descHe, ENT_QUOTES) . '</p>');
                                    else         $errors[] = 'الوصف → عبري';
                                }

                                if (empty($errors)) {
                                    \Filament\Notifications\Notification::make()
                                        ->title('تمت الترجمة بنجاح ✅')
                                        ->body('تم ملء حقول الإنجليزية والعبرية. راجعها قبل الحفظ.')
                                        ->success()->duration(6000)->send();
                                } else {
                                    \Filament\Notifications\Notification::make()
                                        ->title('الترجمة اكتملت جزئياً ⚠️')
                                        ->body('فشل: ' . implode('، ', $errors) . '. تحقق من الاتصال.')
                                        ->warning()->duration(8000)->send();
                                }
                            })
                            ->visible(fn(Forms\Get $get) => filled(trim($get('name_ar') ?? ''))),
                    ])->columnSpanFull(),

                    // ── الوصف بلغات أخرى ─────────────────────────────────
                    Forms\Components\Section::make('الوصف بلغات أخرى')
                        ->description('تُملأ تلقائياً بالضغط على زر الترجمة أعلاه — يمكنك التعديل يدوياً')
                        ->collapsible()
                        ->collapsed(fn($record) => !$record?->description_en && !$record?->description_he)
                        ->schema([
                            Forms\Components\RichEditor::make('description_en')
                                ->label('الوصف الكامل (إنجليزي)')
                                ->toolbarButtons(['bold','italic','bulletList','orderedList','link'])
                                ->columnSpanFull(),
                            Forms\Components\RichEditor::make('description_he')
                                ->label('الوصف الكامل (عبري)')
                                ->toolbarButtons(['bold','italic','bulletList','orderedList','link'])
                                ->columnSpanFull(),
                        ]),
                ]),

                Forms\Components\Tabs\Tab::make('الأسعار والمخزون')->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('السعر (₪)')->required()->numeric()->prefix('₪'),
                        Forms\Components\TextInput::make('compare_price')
                            ->label('سعر قبل الخصم')->numeric()->prefix('₪'),
                        Forms\Components\TextInput::make('cost_price')
                            ->label('سعر التكلفة')->numeric()->prefix('₪'),
                    ]),

                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('sku')
                            ->label('رمز المنتج (SKU)')->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('stock_quantity')
                            ->label('الكمية')->numeric()->default(0),
                        Forms\Components\TextInput::make('low_stock_alert')
                            ->label('تنبيه مخزون منخفض')->numeric()->default(5),
                    ]),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Toggle::make('track_quantity')->label('تتبع الكمية')->default(true),
                        Forms\Components\Toggle::make('allow_backorder')->label('السماح بالطلب رغم نفاد المخزون')->default(false),
                    ]),
                ]),

                Forms\Components\Tabs\Tab::make('الصور')->schema([
                    Forms\Components\FileUpload::make('main_image')
                        ->label('الصورة الرئيسية')
                        ->image()
                        ->directory('products')
                        ->imageEditor()
                        ->maxSize(5120)
                        ->acceptedFileTypes(['image/jpeg','image/png','image/webp'])
                        ->saveUploadedFileUsing(fn ($file) => \App\Support\ImageOptimizer::store($file, 'products'))
                        ->helperText('📐 الأبعاد المثالية: 800 × 800 بكسل (مربع 1:1) · 🗜️ تُضغط تلقائياً وتُحوّل إلى WebP · 📁 الحد الأقصى: 5 MB · 💡 استخدم خلفية بيضاء أو شفافة')
                        ->columnSpanFull(),

                    Forms\Components\Repeater::make('images')
                        ->label('صور إضافية')->relationship('images')
                        ->schema([
                            Forms\Components\FileUpload::make('image')
                                ->label('الصورة')
                                ->image()
                                ->directory('products')
                                ->maxSize(5120)
                                ->acceptedFileTypes(['image/jpeg','image/png','image/webp'])
                                ->saveUploadedFileUsing(fn ($file) => \App\Support\ImageOptimizer::store($file, 'products'))
                                ->helperText('800 × 800 بكسل · تُضغط تلقائياً · أقل من 5 MB')
                                ->required(),
                            Forms\Components\TextInput::make('alt_text')->label('النص البديل'),
                            Forms\Components\TextInput::make('sort_order')->label('الترتيب')->numeric()->default(0),
                        ])->columns(3)->columnSpanFull(),
                ]),

                Forms\Components\Tabs\Tab::make('الألوان / المقاسات')->schema([
                    Forms\Components\Repeater::make('variants')
                        ->label('المتغيرات (اختياري)')
                        ->relationship('variants')
                        ->defaultItems(0)
                        ->addActionLabel('+ إضافة متغير')
                        ->helperText('اترك فارغاً إذا لم يكن للمنتج ألوان أو مقاسات مختلفة')
                        ->schema([
                            Forms\Components\Select::make('type')->label('النوع')
                                ->options(['color'=>'لون','size'=>'مقاس','volume'=>'حجم'])->required(),
                            Forms\Components\TextInput::make('value')->label('القيمة (عربي)')->required(),
                            Forms\Components\ColorPicker::make('color_code')->label('كود اللون'),
                            Forms\Components\TextInput::make('stock_quantity')->label('الكمية')->numeric()->default(0),
                            Forms\Components\TextInput::make('price_modifier')->label('إضافة للسعر')->numeric()->prefix('₪')->default(0),
                            Forms\Components\Toggle::make('is_active')->label('فعال')->default(true),
                        ])->columns(3)->columnSpanFull(),
                ]),

                Forms\Components\Tabs\Tab::make('الإعدادات والـ SEO')->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\Toggle::make('is_active')->label('منشور')->default(true),
                        Forms\Components\Toggle::make('is_featured')->label('منتج مميز')->default(false),
                        Forms\Components\Toggle::make('is_new')->label('منتج جديد')->default(false),
                    ]),
                    Forms\Components\TextInput::make('meta_title')->label('عنوان SEO')->maxLength(255),
                    Forms\Components\Textarea::make('meta_description')->label('وصف SEO')->rows(2)->maxLength(500),
                ]),

            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('main_image')->label('الصورة')->circular(),

                Tables\Columns\TextColumn::make('name_ar')->label('اسم المنتج')
                    ->searchable()->sortable()->weight('bold'),

                Tables\Columns\TextColumn::make('category.name_ar')->label('القسم')->badge(),

                Tables\Columns\TextColumn::make('price')->label('السعر')->money('ILS')->sortable(),

                Tables\Columns\TextColumn::make('stock_quantity')->label('المخزون')->sortable()
                    ->color(fn($record) => match(true) {
                        $record->stock_quantity === 0 => 'danger',
                        $record->stock_quantity <= $record->low_stock_alert => 'warning',
                        default => 'success',
                    }),

                Tables\Columns\TextColumn::make('sales_count')->label('مبيعات')->sortable(),

                Tables\Columns\IconColumn::make('is_featured')->label('مميز')->boolean(),

                Tables\Columns\ToggleColumn::make('is_active')->label('منشور'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')->label('القسم')
                    ->options(Category::pluck('name_ar', 'id')),
                Tables\Filters\SelectFilter::make('brand_id')->label('الماركة')
                    ->options(Brand::pluck('name', 'id')),
                Tables\Filters\TernaryFilter::make('is_active')->label('المنشورة'),
                Tables\Filters\TernaryFilter::make('is_featured')->label('المميزة'),
                Tables\Filters\Filter::make('low_stock')->label('مخزون منخفض')
                    ->query(fn(Builder $q) => $q->whereColumn('stock_quantity', '<=', 'low_stock_alert')->where('stock_quantity', '>', 0)),
                Tables\Filters\Filter::make('out_of_stock')->label('نفد المخزون')
                    ->query(fn(Builder $q) => $q->where('stock_quantity', 0)),
            ])
            // Actions first in the row so they stay visible without scrolling
            // the wide products table sideways.
            ->actions([
                Tables\Actions\Action::make('quick_price')
                    ->label('السعر')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('warning')
                    ->modalHeading(fn (Product $record) => 'تعديل سعر: '.$record->name_ar)
                    ->modalSubmitActionLabel('حفظ')
                    ->modalCancelActionLabel('إلغاء')
                    ->modalWidth('md')
                    ->fillForm(fn (Product $record) => [
                        'price'         => $record->price,
                        'compare_price' => $record->compare_price,
                    ])
                    ->form([
                        Forms\Components\TextInput::make('price')
                            ->label('السعر (₪)')->required()->numeric()->minValue(0)->prefix('₪'),
                        Forms\Components\TextInput::make('compare_price')
                            ->label('سعر قبل الخصم (اختياري)')->numeric()->minValue(0)->prefix('₪')
                            ->helperText('اتركه فارغاً لإلغاء الخصم. يجب أن يكون أكبر من السعر.')
                            ->gt('price'),
                    ])
                    ->action(function (Product $record, array $data): void {
                        $record->update([
                            'price'         => $data['price'],
                            'compare_price' => $data['compare_price'] ?: null,
                        ]);

                        // Home listings are cached — bust them so the new price
                        // shows immediately instead of after the TTL expires.
                        foreach (['ar', 'en', 'he'] as $loc) {
                            foreach (['featured', 'new', 'bestsellers', 'sale'] as $key) {
                                \Illuminate\Support\Facades\Cache::forget("home:{$loc}:{$key}");
                            }
                        }
                        \Illuminate\Support\Facades\Cache::forget('api:home');

                        \Filament\Notifications\Notification::make()
                            ->title('تم تحديث السعر ✓')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ], position: Tables\Enums\ActionsPosition::BeforeCells)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                    Tables\Actions\BulkAction::make('activate')->label('تفعيل')
                        ->icon('heroicon-o-check')
                        ->action(fn($records) => $records->each->update(['is_active' => true])),
                    Tables\Actions\BulkAction::make('deactivate')->label('إخفاء')
                        ->icon('heroicon-o-eye-slash')
                        ->action(fn($records) => $records->each->update(['is_active' => false])),
                    Tables\Actions\BulkAction::make('mark_featured')->label('تمييز ★')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('تمييز المنتجات المحددة')
                        ->modalDescription('سيتم إضافة المنتجات المحددة إلى قسم "المنتجات المميزة" في الصفحة الرئيسية.')
                        ->modalSubmitActionLabel('تمييز')
                        ->action(fn($records) => $records->each->update(['is_featured' => true])),
                    Tables\Actions\BulkAction::make('unmark_featured')->label('إلغاء التمييز')
                        ->icon('heroicon-o-no-symbol')
                        ->color('gray')
                        ->action(fn($records) => $records->each->update(['is_featured' => false])),
                    Tables\Actions\BulkAction::make('bulk_translate')
                        ->label('🌐 ترجمة تلقائية')
                        ->icon('heroicon-o-language')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('ترجمة المنتجات المحددة')
                        ->modalDescription('سيتم ترجمة (الاسم + الوصف القصير + الوصف) من العربية إلى الإنجليزية والعبرية لكل المنتجات المحددة. قد يستغرق ذلك بعض الوقت.')
                        ->modalSubmitActionLabel('ابدأ الترجمة')
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records) {
                            $translator = app(\App\Services\TranslationService::class);
                            $done = 0; $failed = 0; $apiError = false;

                            foreach ($records as $product) {
                                $updates = [];

                                // Name
                                $nameAr = trim($product->name_ar ?? '');
                                if ($nameAr) {
                                    $en = $translator->translate($nameAr, 'ar', 'en');
                                    $he = $translator->translate($nameAr, 'ar', 'he');
                                    if ($en) $updates['name_en'] = $en;
                                    if ($he) $updates['name_he'] = $he;
                                    if (!$en && !$he) { $apiError = true; break; }
                                }

                                // Short description
                                $shortAr = trim($product->short_description ?? '');
                                if ($shortAr) {
                                    $en = $translator->translate($shortAr, 'ar', 'en');
                                    $he = $translator->translate($shortAr, 'ar', 'he');
                                    if ($en) $updates['short_description_en'] = $en;
                                    if ($he) $updates['short_description_he'] = $he;
                                }

                                // Description (strip HTML)
                                $descAr = trim(strip_tags($product->description_ar ?? ''));
                                if ($descAr) {
                                    $en = $translator->translate($descAr, 'ar', 'en');
                                    $he = $translator->translate($descAr, 'ar', 'he');
                                    if ($en) $updates['description_en'] = '<p>' . htmlspecialchars($en, ENT_QUOTES) . '</p>';
                                    if ($he) $updates['description_he'] = '<p>' . htmlspecialchars($he, ENT_QUOTES) . '</p>';
                                }

                                if (!empty($updates)) {
                                    $product->updateQuietly($updates);
                                    $done++;
                                } else {
                                    $failed++;
                                }

                                // 0.5s between products to respect API rate limit
                                usleep(500_000);
                            }

                            if ($apiError) {
                                \Filament\Notifications\Notification::make()
                                    ->title('فشلت الترجمة — تجاوز الحد اليومي لـ MyMemory API')
                                    ->body("تمت ترجمة {$done} منتج. أضف MYMEMORY_EMAIL في ملف .env لرفع الحد إلى 50,000 حرف/يوم.")
                                    ->warning()->persistent()->send();
                            } else {
                                \Filament\Notifications\Notification::make()
                                    ->title("✅ تمت الترجمة: {$done} منتج" . ($failed ? " | تجاهل: {$failed}" : ''))
                                    ->success()->send();
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('stock_quantity', '<=', 5)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string { return 'warning'; }
}
