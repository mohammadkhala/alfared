<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopProductsWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 6;
    protected static ?string $heading = 'أكثر المنتجات مبيعاً';

    public function table(Table $table): Table
    {
        return $table
            ->query(Product::query()->where('is_active', true)->orderByDesc('sales_count')->limit(5))
            ->columns([
                Tables\Columns\ImageColumn::make('main_image')
                    ->label('')->circular()->size(36),
                Tables\Columns\TextColumn::make('name_ar')
                    ->label('المنتج')->limit(28)->weight('bold'),
                Tables\Columns\TextColumn::make('sales_count')
                    ->label('مبيعات')->sortable()
                    ->formatStateUsing(fn($state) => number_format($state)),
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('المخزون')
                    ->color(fn($record) => $record->stock_quantity <= ($record->low_stock_alert ?? 5) ? 'danger' : 'success')
                    ->formatStateUsing(fn($record) =>
                        $record->stock_quantity . ($record->stock_quantity <= ($record->low_stock_alert ?? 5) ? ' ⚠️' : ' ✓')
                    ),
            ])
            ->paginated(false);
    }
}
