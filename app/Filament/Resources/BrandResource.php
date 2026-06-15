<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BrandResource\Pages;
use App\Models\Brand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'الماركات';
    protected static ?string $modelLabel = 'ماركة';
    protected static ?string $pluralModelLabel = 'الماركات';
    protected static ?string $navigationGroup = 'المتجر';
    protected static ?int $navigationSort = 4;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermission('manage_brands') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('name')->label('اسم الماركة')->required()->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn($state, Forms\Set $set) =>
                            $set('slug', Str::slug($state))),
                    Forms\Components\TextInput::make('slug')->label('الرابط')->required()
                        ->unique(ignoreRecord: true)->maxLength(255),
                ]),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\FileUpload::make('logo')
                        ->label('الشعار')
                        ->image()
                        ->directory('brands')
                        ->maxSize(3072)
                        ->acceptedFileTypes(['image/jpeg','image/png','image/webp'])
                        ->helperText('📐 400 × 400 بكسل (مربع) · 💡 PNG بخلفية شفافة مثالي · 📁 أقل من 1 MB'),
                    Forms\Components\TextInput::make('website')->label('الموقع الإلكتروني')
                        ->url()->maxLength(255),
                ]),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('sort_order')->label('الترتيب')->numeric()->default(0),
                    Forms\Components\Toggle::make('is_active')->label('فعال')->default(true)->inline(false),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')->label('الشعار')->circular(),
                Tables\Columns\TextColumn::make('name')->label('الاسم')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('products_count')->label('المنتجات')
                    ->counts('products')->sortable(),
                Tables\Columns\TextColumn::make('website')->label('الموقع')->limit(30),
                Tables\Columns\TextColumn::make('sort_order')->label('الترتيب')->sortable(),
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
            'index'  => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit'   => Pages\EditBrand::route('/{record}/edit'),
        ];
    }
}
