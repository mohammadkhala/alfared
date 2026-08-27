<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * "بدون فيديو" must actually clear the column. The radio is dehydrated, so
     * without this a cleared selection would leave the old value in place.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($this->data['video_source'] ?? null) === 'none') {
            $data['video'] = null;
        }
        return $data;
    }
}
