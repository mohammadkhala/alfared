<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('invoice')
                ->label('🖨️ فاتورة')
                ->color('warning')
                ->icon('heroicon-o-printer')
                ->url(fn() => route('orders.invoice', $this->record))
                ->openUrlInNewTab(),
            Actions\EditAction::make()->label('تعديل')->icon('heroicon-o-pencil-square'),
        ];
    }
}
