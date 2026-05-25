<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 6;
    protected static ?string $heading = 'آخر الطلبات';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->latest()->limit(10))
            ->columns([
                Tables\Columns\TextColumn::make('order_number')->label('رقم الطلب')->weight('bold'),
                Tables\Columns\TextColumn::make('customer_name')->label('العميل'),
                Tables\Columns\TextColumn::make('customer_phone')->label('الهاتف'),
                Tables\Columns\TextColumn::make('city')->label('المدينة'),
                Tables\Columns\TextColumn::make('total')->label('الإجمالي')->money('ILS'),
                Tables\Columns\SelectColumn::make('status')->label('الحالة')->options(Order::$statusLabels),
                Tables\Columns\TextColumn::make('created_at')->label('التاريخ')->dateTime('d/m/Y h:i A'),
            ]);
    }
}
