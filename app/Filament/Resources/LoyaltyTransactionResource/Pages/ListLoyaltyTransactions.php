<?php

namespace App\Filament\Resources\LoyaltyTransactionResource\Pages;

use App\Filament\Resources\LoyaltyTransactionResource;
use App\Models\User;
use App\Services\LoyaltyService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListLoyaltyTransactions extends ListRecords
{
    protected static string $resource = LoyaltyTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('manual_adjust')
                ->label('+ تعديل يدوي للنقاط')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->form([
                    Forms\Components\Select::make('user_id')
                        ->label('العميل')
                        ->options(fn() => User::where('role', User::ROLE_CUSTOMER)
                            ->orderBy('name')->limit(500)->get()
                            ->mapWithKeys(fn($u) => [$u->id => "{$u->name} — {$u->loyalty_points} نقطة"])
                            ->toArray())
                        ->searchable()
                        ->required(),
                    Forms\Components\TextInput::make('points')
                        ->label('عدد النقاط')
                        ->helperText('قيمة موجبة للإضافة، قيمة سالبة للخصم')
                        ->numeric()
                        ->required(),
                    Forms\Components\Textarea::make('note')
                        ->label('سبب التعديل (اختياري)')
                        ->rows(2),
                ])
                ->action(function (array $data) {
                    $user = User::find($data['user_id']);
                    if (! $user) return;

                    $tx = LoyaltyService::adminAdjust($user, (int) $data['points'], $data['note'] ?? null);

                    Notification::make()
                        ->title('✓ تم تعديل النقاط بنجاح')
                        ->body("{$user->name} • رصيده الآن: {$user->fresh()->loyalty_points} نقطة")
                        ->success()
                        ->send();
                }),
        ];
    }
}
