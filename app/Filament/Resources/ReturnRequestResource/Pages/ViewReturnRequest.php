<?php

namespace App\Filament\Resources\ReturnRequestResource\Pages;

use App\Filament\Resources\ReturnRequestResource;
use App\Models\Order;
use App\Services\FcmService;
use App\Services\WaSenderService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewReturnRequest extends ViewRecord
{
    protected static string $resource = ReturnRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // ── قبول ──
            Actions\Action::make('approve')
                ->label('قبول الإرجاع')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn() => $this->record->return_status === 'pending')
                ->requiresConfirmation()
                ->modalHeading('تأكيد قبول الإرجاع')
                ->modalDescription('سيتم إشعار العميل عبر واتساب بقبول طلبه.')
                ->action(function () {
                    // تغيير الحالتين — FCM يُطلق تلقائياً عند تغيير status
                    $this->record->update([
                        'return_status' => 'approved',
                        'status'        => 'returned',
                    ]);

                    // إشعار واتساب
                    $phone = $this->record->customer_phone ?? $this->record->user?->phone;
                    if ($phone) {
                        app(WaSenderService::class)->send(
                            $phone,
                            "✅ *تم قبول طلب الإرجاع*\n\nعزيزي {$this->record->customer_name}،\nتم قبول طلب إرجاع الطلب رقم *{$this->record->order_number}*.\nسيتواصل معك فريقنا قريباً لإتمام عملية الإرجاع.\n\n🛍️ *أبناء الفريد التجارية*"
                        );
                    }

                    Notification::make()->title('✅ تم قبول الإرجاع وتغيير الحالة')->success()->send();
                    $this->refreshFormData(['return_status', 'status']);
                }),

            // ── رفض ──
            Actions\Action::make('reject')
                ->label('رفض الإرجاع')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn() => $this->record->return_status === 'pending')
                ->form([
                    Forms\Components\Textarea::make('reject_reason')
                        ->label('سبب الرفض')
                        ->placeholder('اكتب سبب رفض طلب الإرجاع...')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $this->record->update(['return_status' => 'rejected']);

                    // إشعار Push للتطبيق
                    if ($this->record->user) {
                        FcmService::sendToUser(
                            $this->record->user,
                            '❌ طلب الإرجاع مرفوض',
                            "طلبك رقم {$this->record->order_number} — {$data['reject_reason']}",
                            ['type' => 'return_rejected', 'order_number' => $this->record->order_number],
                        );
                    }

                    // إشعار واتساب
                    $phone = $this->record->customer_phone ?? $this->record->user?->phone;
                    if ($phone) {
                        app(WaSenderService::class)->send(
                            $phone,
                            "❌ *بشأن طلب الإرجاع*\n\nعزيزي {$this->record->customer_name}،\nنأسف لإبلاغك بأنه تم رفض طلب إرجاع الطلب رقم *{$this->record->order_number}*.\n\n📝 *السبب:* {$data['reject_reason']}\n\nللاستفسار تواصل معنا عبر واتساب.\n\n🛍️ *أبناء الفريد التجارية*"
                        );
                    }

                    Notification::make()->title('❌ تم رفض الإرجاع')->warning()->send();
                    $this->refreshFormData(['return_status']);
                }),

            Actions\Action::make('back')
                ->label('رجوع للقائمة')
                ->icon('heroicon-o-arrow-right')
                ->color('gray')
                ->url(ReturnRequestResource::getUrl()),
        ];
    }
}
