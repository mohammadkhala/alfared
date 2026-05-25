<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlashSaleResource\Pages;
use App\Filament\Resources\FlashSaleResource\RelationManagers;
use App\Models\FlashSale;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FlashSaleResource extends Resource
{
    protected static ?string $model = FlashSale::class;
    protected static ?string $navigationIcon  = 'heroicon-o-bolt';
    protected static ?string $navigationLabel = 'العروض الخاصة';
    protected static ?string $modelLabel      = 'عرض';
    protected static ?string $pluralModelLabel = 'العروض الخاصة';
    protected static ?string $navigationGroup = 'المتجر';
    protected static ?int    $navigationSort  = 6;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermission('manage_products') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات العرض')->schema([
                Forms\Components\TextInput::make('name_ar')
                    ->label('اسم العرض (عربي)')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('مثال: عروض رمضان 2026'),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\DateTimePicker::make('starts_at')
                        ->label('تاريخ البدء')
                        ->nullable()
                        ->helperText('اتركه فارغاً لبدء العرض فوراً'),
                    Forms\Components\DateTimePicker::make('ends_at')
                        ->label('تاريخ الانتهاء')
                        ->nullable()
                        ->helperText('اتركه فارغاً لعرض مفتوح بلا نهاية')
                        ->after('starts_at'),
                ]),

                Forms\Components\Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true)
                    ->inline(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_ar')
                    ->label('اسم العرض')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('saleProducts_count')
                    ->label('عدد المنتجات')
                    ->counts('saleProducts')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('starts_at')
                    ->label('يبدأ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('فوري'),

                Tables\Columns\TextColumn::make('ends_at')
                    ->label('ينتهي')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('مفتوح'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('أُنشئ')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelationManagers(): array
    {
        return [
            RelationManagers\SaleProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFlashSales::route('/'),
            'create' => Pages\CreateFlashSale::route('/create'),
            'edit'   => Pages\EditFlashSale::route('/{record}/edit'),
        ];
    }
}
