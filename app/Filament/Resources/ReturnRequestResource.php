<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReturnRequestResource\Pages;
use App\Models\Order;
use App\Services\FcmService;
use App\Services\WaSenderService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;

class ReturnRequestResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon  = 'heroicon-o-arrow-uturn-left';
    protected static ?string $navigationLabel = 'طلبات الإرجاع';
    protected static ?string $modelLabel      = 'طلب إرجاع';
    protected static ?string $pluralModelLabel= 'طلبات الإرجاع';
    protected static ?string $navigationGroup = 'المتجر';
    protected static ?int    $navigationSort  = 4;
    protected static ?string $slug            = 'return-requests';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermission('manage_orders') ?? false;
    }

    // ── فلترة: فقط الطلبات التي فيها طلب إرجاع ──────────────────────────────
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereNotNull('return_requested_at')
            ->with(['user', 'deliveryZone', 'items.product'])
            ->latest('return_requested_at');
    }

    // ── Badge عدد الطلبات المعلّقة ───────────────────────────────────────────
    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()
            ->where('return_status', 'pending')
            ->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    // ── الجدول ───────────────────────────────────────────────────────────────
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('return_requested_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('رقم الطلب')
                    ->searchable()
                    ->weight('bold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('العميل')
                    ->searchable(),

                Tables\Columns\TextColumn::make('customer_phone')
                    ->label('الهاتف')
                    ->searchable(),

                Tables\Columns\TextColumn::make('total')
                    ->label('قيمة الطلب')
                    ->money('ILS')
                    ->sortable(),

                Tables\Columns\TextColumn::make('return_reason')
                    ->label('سبب الإرجاع')
                    ->limit(50)
                    ->tooltip(fn($record) => $record->return_reason)
                    ->wrap(),

                Tables\Columns\BadgeColumn::make('return_status')
                    ->label('الحالة')
                    ->formatStateUsing(fn($state) => match($state) {
                        'pending'  => '⏳ معلّق',
                        'approved' => '✅ مقبول',
                        'rejected' => '❌ مرفوض',
                        default    => $state,
                    })
                    ->color(fn($state) => match($state) {
                        'pending'  => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'gray',
                    }),

                Tables\Columns\TextColumn::make('return_reject_reason')
                    ->label('سبب الرفض')
                    ->limit(40)
                    ->tooltip(fn($record) => $record->return_reject_reason)
                    ->placeholder('—')
                    ->visible(fn() => true)
                    ->color('danger'),

                Tables\Columns\TextColumn::make('return_requested_at')
                    ->label('تاريخ الطلب')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('return_status')
                    ->label('الحالة')
                    ->options([
                        'pending'  => '⏳ معلّق',
                        'approved' => '✅ مقبول',
                        'rejected' => '❌ مرفوض',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label(''),

                // ── قبول الإرجاع ──
                Tables\Actions\Action::make('approve')
                    ->label('قبول')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(Order $record) => $record->return_status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('قبول طلب الإرجاع')
                    ->modalDescription('هل أنت متأكد من قبول هذا الطلب؟ سيتم إشعار العميل عبر واتساب.')
                    ->action(function (Order $record) {
                        // تغيير حالة الإرجاع + حالة الطلب → مُرجَّع
                        // (تغيير status يُطلق FCM تلقائياً عبر Order::boot)
                        $record->update([
                            'return_status' => 'approved',
                            'status'        => 'returned',
                        ]);

                        // إشعار واتساب
                        $phone = $record->customer_phone ?? $record->user?->phone;
                        if ($phone) {
                            app(WaSenderService::class)->send(
                                $phone,
                                "✅ *تم قبول طلب الإرجاع*\n\nعزيزي {$record->customer_name}،\nتم قبول طلب إرجاع الطلب رقم *{$record->order_number}*.\nسيتواصل معك فريقنا قريباً لإتمام عملية الإرجاع.\n\n🛍️ *أبناء الفريد التجارية*"
                            );
                        }

                        Notification::make()
                            ->title('✅ تم قبول الإرجاع')
                            ->body("طلب {$record->order_number} — تم تغيير الحالة وإشعار العميل")
                            ->success()
                            ->send();
                    }),

                // ── رفض الإرجاع ──
                Tables\Actions\Action::make('reject')
                    ->label('رفض')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(Order $record) => $record->return_status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('reject_reason')
                            ->label('سبب الرفض')
                            ->placeholder('أدخل سبب رفض الإرجاع...')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Order $record, array $data) {
                        // حفظ سبب الرفض في قاعدة البيانات
                        $record->update([
                            'return_status'        => 'rejected',
                            'return_reject_reason' => $data['reject_reason'],
                        ]);

                        // إشعار Push للتطبيق
                        if ($record->user) {
                            FcmService::sendToUser(
                                $record->user,
                                '❌ طلب الإرجاع مرفوض',
                                "طلبك رقم {$record->order_number} — {$data['reject_reason']}",
                                ['type' => 'return_rejected', 'order_number' => $record->order_number],
                            );
                        }

                        // إشعار واتساب
                        $phone = $record->customer_phone ?? $record->user?->phone;
                        if ($phone) {
                            app(WaSenderService::class)->send(
                                $phone,
                                "❌ *بشأن طلب الإرجاع*\n\nعزيزي {$record->customer_name}،\nنأسف لإبلاغك بأنه تم رفض طلب إرجاع الطلب رقم *{$record->order_number}*.\n\n📝 *السبب:* {$data['reject_reason']}\n\nللاستفسار تواصل معنا عبر واتساب.\n\n🛍️ *أبناء الفريد التجارية*"
                            );
                        }

                        Notification::make()
                            ->title('❌ تم رفض الإرجاع')
                            ->body("طلب {$record->order_number} — تم إشعار العميل")
                            ->warning()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('approveAll')
                    ->label('قبول المحدد')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            if ($record->return_status === 'pending') {
                                // تغيير الحالتين — FCM يُطلق تلقائياً عند تغيير status
                                $record->update([
                                    'return_status' => 'approved',
                                    'status'        => 'returned',
                                ]);
                            }
                        }
                        Notification::make()->title('✅ تم قبول الطلبات المحددة')->success()->send();
                    }),
            ])
            ->emptyStateIcon('heroicon-o-arrow-uturn-left')
            ->emptyStateHeading('لا توجد طلبات إرجاع')
            ->emptyStateDescription('لم يطلب أي عميل إرجاع بعد.');
    }

    // ── صفحة التفاصيل ────────────────────────────────────────────────────────
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            Infolists\Components\Section::make('📦 معلومات الطلب')
                ->columns(3)
                ->schema([
                    Infolists\Components\TextEntry::make('order_number')
                        ->label('رقم الطلب')->weight('bold')->color('primary'),
                    Infolists\Components\TextEntry::make('customer_name')
                        ->label('اسم العميل'),
                    Infolists\Components\TextEntry::make('customer_phone')
                        ->label('رقم الهاتف'),
                    Infolists\Components\TextEntry::make('total')
                        ->label('قيمة الطلب')->money('ILS'),
                    Infolists\Components\TextEntry::make('status')
                        ->label('حالة الطلب')
                        ->badge()
                        ->color(fn($state) => match($state) {
                            'delivered' => 'success',
                            'cancelled' => 'danger',
                            default     => 'warning',
                        }),
                    Infolists\Components\TextEntry::make('created_at')
                        ->label('تاريخ الطلب')->dateTime('d/m/Y'),
                ]),

            Infolists\Components\Section::make('↩️ بيانات الإرجاع')
                ->columns(3)
                ->schema([
                    Infolists\Components\TextEntry::make('return_requested_at')
                        ->label('تاريخ طلب الإرجاع')->dateTime('d/m/Y H:i'),
                    Infolists\Components\TextEntry::make('return_status')
                        ->label('حالة الإرجاع')
                        ->badge()
                        ->formatStateUsing(fn($state) => match($state) {
                            'pending'  => '⏳ معلّق',
                            'approved' => '✅ مقبول',
                            'rejected' => '❌ مرفوض',
                            default    => $state,
                        })
                        ->color(fn($state) => match($state) {
                            'pending'  => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger',
                            default    => 'gray',
                        }),
                    Infolists\Components\TextEntry::make('return_reason')
                        ->label('سبب الإرجاع من العميل')
                        ->columnSpanFull()
                        ->extraAttributes(['class' => 'bg-red-50 rounded p-3']),

                    Infolists\Components\TextEntry::make('return_reject_reason')
                        ->label('سبب الرفض من الإدارة')
                        ->columnSpanFull()
                        ->visible(fn($record) => filled($record->return_reject_reason))
                        ->extraAttributes(['class' => 'bg-orange-50 rounded p-3'])
                        ->icon('heroicon-o-x-circle')
                        ->iconColor('danger'),
                ]),

            Infolists\Components\Section::make('🛍️ المنتجات')
                ->schema([
                    Infolists\Components\RepeatableEntry::make('items')
                        ->label('')
                        ->schema([
                            Infolists\Components\TextEntry::make('product.name_ar')
                                ->label('المنتج'),
                            Infolists\Components\TextEntry::make('quantity')
                                ->label('الكمية'),
                            Infolists\Components\TextEntry::make('unit_price')
                                ->label('السعر')->money('ILS'),
                            Infolists\Components\TextEntry::make('subtotal')
                                ->label('المجموع')->money('ILS'),
                        ])
                        ->columns(4),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReturnRequests::route('/'),
            'view'  => Pages\ViewReturnRequest::route('/{record}'),
        ];
    }
}
