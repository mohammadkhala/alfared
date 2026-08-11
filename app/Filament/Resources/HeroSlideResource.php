<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeroSlideResource\Pages;
use App\Models\HeroSlide;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HeroSlideResource extends Resource
{
    protected static ?string $model = HeroSlide::class;
    protected static ?string $navigationIcon  = 'heroicon-o-rectangle-group';
    protected static ?string $navigationLabel = 'سلايدر الرئيسية';
    protected static ?string $modelLabel      = 'شريحة';
    protected static ?string $pluralModelLabel = 'شرائح السلايدر';
    protected static ?string $navigationGroup = 'التسويق';
    protected static ?int    $navigationSort  = 0;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('المحتوى')->schema([
                Forms\Components\Tabs::make()->tabs([
                    Forms\Components\Tabs\Tab::make('🇸🇦 العربية')->schema(self::langFields('ar')),
                    Forms\Components\Tabs\Tab::make('🇮🇱 العبرية')->schema(self::langFields('he')),
                    Forms\Components\Tabs\Tab::make('🇬🇧 الإنجليزية')->schema(self::langFields('en')),
                ])->columnSpanFull(),
            ]),

            Forms\Components\Section::make('الصورة والأزرار')->schema([
                Forms\Components\FileUpload::make('image')
                    ->label('صورة الخلفية')
                    ->image()
                    ->directory('hero')
                    ->imageEditor()
                    ->helperText('الأمثل: صورة أفقية بجودة عالية. تُترك فارغة → صورة افتراضية.')
                    ->columnSpanFull(),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('btn1_url')->label('رابط الزر الأول')
                        ->placeholder('/products')->helperText('اتركه فارغاً لإخفاء الزر.'),
                    Forms\Components\TextInput::make('btn2_url')->label('رابط الزر الثاني')
                        ->placeholder('https://wa.me/...'),
                ]),
            ]),

            Forms\Components\Section::make('الإعدادات')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Toggle::make('is_active')->label('مفعّلة')->default(true),
                    Forms\Components\TextInput::make('sort_order')->label('الترتيب')
                        ->numeric()->default(0)->helperText('الأصغر يظهر أولاً.'),
                ]),
            ]),
        ]);
    }

    /** The per-language text inputs, reused across the three tabs. */
    private static function langFields(string $lang): array
    {
        return [
            Forms\Components\TextInput::make("badge_{$lang}")->label('الشارة العلوية')
                ->placeholder('✦ مجموعة 2026'),
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make("title_{$lang}")->label('العنوان الرئيسي'),
                Forms\Components\TextInput::make("highlight_{$lang}")->label('الكلمة المميّزة (بلون مختلف)'),
            ]),
            Forms\Components\Textarea::make("subtitle_{$lang}")->label('الوصف')->rows(3),
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make("btn1_text_{$lang}")->label('نص الزر الأول')
                    ->placeholder('تسوّق الآن'),
                Forms\Components\TextInput::make("btn2_text_{$lang}")->label('نص الزر الثاني')
                    ->placeholder('تواصل معنا'),
            ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('الصورة')
                    ->defaultImageUrl(asset('images/banner.png'))->height(48),
                Tables\Columns\TextColumn::make('title_ar')->label('العنوان')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('badge_ar')->label('الشارة')->limit(24)->toggleable(),
                Tables\Columns\IconColumn::make('is_active')->label('مفعّلة')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->label('الترتيب')->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListHeroSlides::route('/'),
            'create' => Pages\CreateHeroSlide::route('/create'),
            'edit'   => Pages\EditHeroSlide::route('/{record}/edit'),
        ];
    }
}
