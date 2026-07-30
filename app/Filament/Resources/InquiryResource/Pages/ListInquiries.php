<?php

namespace App\Filament\Resources\InquiryResource\Pages;

use App\Filament\Resources\InquiryResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListInquiries extends ListRecords
{
    protected static string $resource = InquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('الكل'),
            'new' => Tab::make('جديد 🔴')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'new'))
                ->badge(\App\Models\Inquiry::where('status', 'new')->count()),
            'read' => Tab::make('مقروء')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'read')),
            'replied' => Tab::make('تم الرد ✓')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'replied')),
        ];
    }
}
