<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Filament\Widgets\OrderStatsWidget;
use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    public function mount(): void
    {
        parent::mount();

        // Pulls anything RoadFN has changed since the last check, so the list is
        // current without the admin pressing anything. Rate-limited internally
        // and swallows its own errors — see RoadFnAutoSync.
        \App\Support\RoadFnAutoSync::runIfDue();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('+ طلب جديد')
                ->icon('heroicon-o-plus')
                ->color('primary'),
            Actions\Action::make('print_all')
                ->label('🖨️ طباعة كل الفواتير')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\Select::make('lang')
                        ->label('لغة الفاتورة')
                        ->options([
                            'ar' => '🇸🇦 العربية',
                            'he' => '🇮🇱 עברית',
                            'en' => '🇬🇧 English',
                        ])
                        ->default('ar')
                        ->required(),
                ])
                ->action(fn(array $data) => redirect()->away(
                    route('orders.invoices.bulk', ['ids' => 'all', 'lang' => $data['lang']])
                )),
            Actions\Action::make('export_all')
                ->label('📥 تصدير Excel')
                ->color('gray')
                ->url(fn() => route('orders.export', ['ids' => 'all'])),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OrderStatsWidget::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('الكل')
                ->badge(Order::count()),
            'new' => Tab::make('🆕 جديد')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending'))
                ->badge(Order::where('status', 'pending')->count())
                ->badgeColor('warning'),
            'processing' => Tab::make('⚙️ تجهيز')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereIn('status', ['confirmed', 'processing']))
                ->badge(Order::whereIn('status', ['confirmed', 'processing'])->count())
                ->badgeColor('info'),
            'shipped' => Tab::make('🚚 شحن')
                // Includes sent_to_delivery: an order handed to RoadFN but not
                // yet picked up by a driver belongs in this tab, and was
                // appearing in none of them.
                ->modifyQueryUsing(fn(Builder $query) => $query->whereIn('status', ['sent_to_delivery', 'shipped']))
                ->badge(Order::whereIn('status', ['sent_to_delivery', 'shipped'])->count())
                ->badgeColor('primary'),
            'delivered' => Tab::make('✓ مُسلَّم')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'delivered'))
                ->badge(Order::where('status', 'delivered')->count())
                ->badgeColor('success'),
            'cancelled' => Tab::make('✕ ملغي')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereIn('status', ['cancelled', 'returned']))
                ->badge(Order::whereIn('status', ['cancelled', 'returned'])->count())
                ->badgeColor('danger'),
        ];
    }
}
