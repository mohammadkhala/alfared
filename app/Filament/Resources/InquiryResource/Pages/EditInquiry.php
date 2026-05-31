<?php

namespace App\Filament\Resources\InquiryResource\Pages;

use App\Filament\Resources\InquiryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInquiry extends EditRecord
{
    protected static string $resource = InquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('حذف'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Auto-mark as replied if admin wrote a reply
        if (!empty($data['admin_reply']) && $data['status'] === 'read') {
            $data['status'] = 'replied';
        }
        return $data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'تم حفظ التغييرات';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
