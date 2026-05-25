<?php

namespace App\Filament\Resources\ReturnRequestResource\Pages;

use App\Filament\Resources\ReturnRequestResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListReturnRequests extends ListRecords
{
    protected static string $resource = ReturnRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('الكل')
                ->icon('heroicon-m-list-bullet')
                ->badge(fn() => static::getResource()::getEloquentQuery()->count()),

            'pending' => Tab::make('⏳ معلّقة')
                ->modifyQueryUsing(fn(Builder $q) => $q->where('return_status', 'pending'))
                ->badge(fn() => static::getResource()::getEloquentQuery()->where('return_status', 'pending')->count())
                ->badgeColor('warning'),

            'approved' => Tab::make('✅ مقبولة')
                ->modifyQueryUsing(fn(Builder $q) => $q->where('return_status', 'approved'))
                ->badge(fn() => static::getResource()::getEloquentQuery()->where('return_status', 'approved')->count())
                ->badgeColor('success'),

            'rejected' => Tab::make('❌ مرفوضة')
                ->modifyQueryUsing(fn(Builder $q) => $q->where('return_status', 'rejected'))
                ->badge(fn() => static::getResource()::getEloquentQuery()->where('return_status', 'rejected')->count())
                ->badgeColor('danger'),
        ];
    }
}
