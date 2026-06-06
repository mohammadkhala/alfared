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

                    // ── الوصف الكامل عربي ─────────────────────────────────
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

                                $nameAr = trim($get('name_ar') ?? '');
                                $descAr = trim(strip_tags($get('description_ar') ?? ''));

                                $errors = [];

                                // ترجمة الاسم
                                if ($nameAr !== '') {
                                    $nameEn = $translator->translate($nameAr, 'ar', 'en');
                                    $nameHe = $translator->translate($nameAr, 'ar', 'he');
                                    if ($nameEn) $set('name_en', $nameEn);
                                    else         $errors[] = 'الاسم → إنجليزي';
                                    if ($nameHe) $set('name_he', $nameHe);
                                    else         $errors[] = 'الاسم → عبري';
                                }

                                // ترجمة الوصف الكامل
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
                                        ->success()
                                        ->duration(6000)
                                        ->send();
                                } else {
                                    \Filament\Notifications\Notification::make()
                                        ->title('الترجمة اكتملت جزئياً ⚠️')
                                        ->body('فشل: ' . implode('، ', $errors) . '. تحقق من الاتصال.')
                                        ->warning()
                                        ->duration(8000)
                                        ->send();
                                }
                            })
                            ->visible(fn(Forms\Get $get) => filled(trim($get('name_ar') ?? ''))),
                    ])->columnSpanFull(),

                    // ── الوصف الكامل بلغات أخرى (تُملأ تلقائياً) ──────────
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
                        ->maxSize(2048)
                        ->acceptedFileTypes(['image/jpeg','image/png','image/webp'])
                        ->helperText('📐 الأبعاد المثالية: 800 × 800 بكسل (مربع 1:1) · 📁 الحجم الأقصى: 2 MB · ✅ الصيغ المقبولة: JPG، PNG، WebP · 💡 استخدم خلفية بيضاء أو شفافة للحصول على أفضل نتيجة')
                        ->columnSpanFull(),

                    Forms\Components\Repeater::make('images')
                        ->label('صور إضافية')->relationship('images')
                        ->schema([
                            Forms\Components\FileUpload::make('image')
                                ->label('الصورة')
                                ->image()
                                ->directory('products')
                                ->maxSize(2048)
                                ->acceptedFileTypes(['image/jpeg','image/png','image/webp'])
                                ->helperText('800 × 800 بكسل · أقل من 2 MB')
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
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                    Tables\Actions\BulkAction::make('activate')->label('تفعيل')
                        ->icon('heroicon-o-check')
                        ->action(fn($records) => $records->each->update(['is_active' => true])),
                    Tables\Actions\BulkAction::make('deactivate')->label('إخفاء')
                        ->icon('heroicon-o-eye-slash')
                        ->action(fn($records) => $records->each->update(['is_active' => false])),
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
