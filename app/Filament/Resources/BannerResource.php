<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = 'لوحة الإعلانات';
    protected static ?string $modelLabel = 'إعلان';
    protected static ?string $pluralModelLabel = 'لوحة الإعلانات';
    protected static ?string $navigationGroup = 'التسويق';
    protected static ?int    $navigationSort  = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermission('manage_banners') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('محتوى البانر')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('title_ar')->label('العنوان (عربي)')->maxLength(255),
                    Forms\Components\TextInput::make('subtitle_ar')->label('العنوان الفرعي')->maxLength(255),
                ]),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('badge_text')->label('نص الشارة (مثل: مبصع محدود)')->maxLength(100),
                    Forms\Components\TextInput::make('button_text_ar')->label('نص الزر')->maxLength(100),
                ]),
                Forms\Components\TextInput::make('link')->label('الرابط عند النقر')->url()->maxLength(255),
                Forms\Components\FileUpload::make('image')
                    ->label('صورة البانر')
                    ->image()
                    ->directory('banners')
                    ->required()
                    ->maxSize(5120)
                    ->acceptedFileTypes(['image/jpeg','image/png','image/webp'])
                    ->saveUploadedFileUsing(fn ($file) => \App\Support\ImageOptimizer::store($file, 'banners'))
                    ->helperText('📐 الأبعاد المثالية: 1200 × 500 بكسل · 📱 اجعل المحتوى في المنتصف · 🗜️ تُضغط تلقائياً وتُحوّل إلى WebP · 📁 الحد الأقصى: 5 MB')
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make('الإعدادات')->schema([
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\Select::make('position')->label('الموضع')
                        ->options([
                            'hero'     => '🦸 قسم البطل (Hero)',
                            'offers'   => '🔥 العروض والتخفيضات',
                            'promo'    => '📢 بانر ترويجي',
                            'sidebar'  => '⏸️ الجانب',
                        ])->default('promo'),
                    Forms\Components\TextInput::make('sort_order')->label('الترتيب')->numeric()->default(0),
                    Forms\Components\Toggle::make('is_active')->label('فعال')->default(true)->inline(false),
                ]),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\DateTimePicker::make('starts_at')->label('تاريخ البدء')->nullable(),
                    Forms\Components\DateTimePicker::make('ends_at')->label('تاريخ الانتهاء')->nullable(),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('الصورة')->width(80)->height(50),
                Tables\Columns\TextColumn::make('title_ar')->label('العنوان')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('position')->label('الموضع')
                    ->formatStateUsing(fn($state) => match($state) {
                        'hero'    => 'البطل',
                        'offers'  => '🔥 العروض',
                        'promo'   => 'ترويجي',
                        'sidebar' => 'جانب',
                        default   => $state,
                    })
                    ->color(fn($state) => $state === 'offers' ? 'warning' : 'gray')
                    ->badge(),
                Tables\Columns\TextColumn::make('sort_order')->label('الترتيب')->sortable(),
                Tables\Columns\TextColumn::make('ends_at')->label('ينتهي')->dateTime('d/m/Y')->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')->label('فعال'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('الفعالة'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit'   => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
