<?php

namespace App\Filament\Resources\FailedLoginResource\Pages;

use App\Filament\Resources\FailedLoginResource;
use App\Models\FailedLogin;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListFailedLogins extends ListRecords
{
    protected static string $resource = FailedLoginResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('clear_old')
                ->label('🗑️ حذف الأقدم من 30 يوم')
                ->color('gray')
                ->requiresConfirmation()
                ->action(function () {
                    $deleted = FailedLogin::where('attempted_at', '<', now()->subDays(30))->delete();
                    Notification::make()
                        ->title('تم الحذف')
                        ->body("حُذف {$deleted} سجل قديم")
                        ->success()
                        ->send();
                }),
        ];
    }
}
