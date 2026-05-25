<?php

namespace App\Filament\Resources\FlashSaleResource\RelationManagers;

use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SaleProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'saleProducts';
    protected static ?string $title = 'منتجات العرض';
    protected static ?string $modelLabel = 'منتج';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('product_id')
                ->label('المنتج')
                ->options(
                    Product::where('is_active', true)
                        ->orderBy('name_ar')
                        ->pluck('name_ar', 'id')
                )
                ->searchable()
                ->required()
                ->preload(),

            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('sale_price')
                    ->label('سعر العرض (₪)')
                    ->numeric()
                    ->required()
                    ->prefix('₪')
                    ->minValue(0),

                Forms\Components\TextInput::make('quantity_limit')
                    ->label('الحد الأقصى للكمية')
                    ->numeric()
                    ->nullable()
                    ->minValue(1)
                    ->helperText('اتركه فارغاً لكمية غير محدودة'),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_id')
            ->columns([
                Tables\Columns\TextColumn::make('product.name_ar')
                    ->label('المنتج')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product.price')
                    ->label('السعر الأصلي')
                    ->formatStateUsing(fn($state) => number_format($state, 0) . ' ₪')
                    ->color('gray'),

                Tables\Columns\TextColumn::make('sale_price')
                    ->label('سعر العرض')
                    ->formatStateUsing(fn($state) => number_format($state, 0) . ' ₪')
                    ->color('success')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('discount_percent')
                    ->label('نسبة التوفير')
                    ->state(function ($record): string {
                        $original = $record->product?->price ?? 0;
                        if ($original <= 0) return '—';
                        $pct = round((($original - $record->sale_price) / $original) * 100);
                        return $pct . '%';
                    })
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('sold_count')
                    ->label('مُباع')
                    ->sortable(),

                Tables\Columns\TextColumn::make('quantity_limit')
                    ->label('الحد')
                    ->placeholder('غير محدود'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('إضافة منتج'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ]);
    }
}
