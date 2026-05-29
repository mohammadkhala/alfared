<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\User;
use App\Services\TierService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    protected static ?string $model            = User::class;
    protected static ?string $navigationIcon   = 'heroicon-o-users';
    protected static ?string $navigationLabel  = 'العملاء';
    protected static ?string $modelLabel       = 'عميل';
    protected static ?string $pluralModelLabel = 'العملاء';
    protected static ?string $navigationGroup  = 'المتجر';
    protected static ?int    $navigationSort   = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermission('view_customers') ?? false;
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('role', User::ROLE_CUSTOMER);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('role', User::ROLE_CUSTOMER)
            ->where('created_at', '>=', now()->startOfWeek())
            ->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات العميل')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('الاسم الكامل')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->label('رقم الهاتف')
                        ->tel()
                        ->maxLength(20),
                ]),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('email')
                        ->label('البريد الإلكتروني')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Forms\Components\TextInput::make('loyalty_points')
                        ->label('نقاط الولاء')
                        ->numeric()
                        ->default(0),
                ]),
                // Hidden: always customer
                Forms\Components\Hidden::make('role')->default(User::ROLE_CUSTOMER),
            ]),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            Infolists\Components\Section::make('بيانات العميل')
                ->columns(3)
                ->schema([
                    Infolists\Components\TextEntry::make('name')
                        ->label('الاسم')
                        ->weight('bold')
                        ->size('lg'),
                    Infolists\Components\TextEntry::make('email')
                        ->label('البريد')
                        ->copyable(),
                    Infolists\Components\TextEntry::make('phone')
                        ->label('الهاتف')
                        ->copyable(),
                    Infolists\Components\TextEntry::make('customer_tier')
                        ->label('التصنيف')
                        ->state(function ($record) {
                            $spent = TierService::totalSpent($record);
                            $tier  = TierService::tierForAmount($spent);
                            $next  = TierService::nextTierForAmount($spent);
                            $label = $tier['emoji'] . ' ' . $tier['label'];
                            if ($next) {
                                $remaining = number_format($next['min'] - $spent, 0);
                                $label .= " (يحتاج {$remaining} ₪ للـ{$next['label']})";
                            } else {
                                $label .= ' — أعلى مستوى!';
                            }
                            return $label;
                        })
                        ->badge()
                        ->color(fn($record) => TierService::tierForAmount(TierService::totalSpent($record))['color']),
                    Infolists\Components\TextEntry::make('loyalty_points')
                        ->label('نقاط الولاء')
                        ->badge()
                        ->color('warning'),
                    Infolists\Components\TextEntry::make('created_at')
                        ->label('تاريخ التسجيل')
                        ->dateTime('d/m/Y'),
                ]),

            Infolists\Components\Section::make('ملخص الإنفاق')
                ->columns(4)
                ->schema([
                    Infolists\Components\TextEntry::make('orders_count')
                        ->label('إجمالي الطلبات')
                        ->state(fn($record) => $record->orders()->count())
                        ->badge()->color('primary'),
                    Infolists\Components\TextEntry::make('delivered_orders')
                        ->label('طلبات مكتملة')
                        ->state(fn($record) => $record->orders()->where('status', 'delivered')->count())
                        ->badge()->color('success'),
                    Infolists\Components\TextEntry::make('total_spent')
                        ->label('إجمالي الإنفاق')
                        ->state(fn($record) =>
                            number_format($record->orders()->where('status', '!=', 'cancelled')->sum('total'), 0) . ' ₪'
                        )
                        ->weight('bold'),
                    Infolists\Components\TextEntry::make('avg_order')
                        ->label('متوسط الطلب')
                        ->state(function ($record) {
                            $count = $record->orders()->where('status', '!=', 'cancelled')->count();
                            $total = $record->orders()->where('status', '!=', 'cancelled')->sum('total');
                            return $count > 0 ? number_format($total / $count, 0) . ' ₪' : '—';
                        }),
                ]),

            Infolists\Components\Section::make('آخر الطلبات')->schema([
                Infolists\Components\RepeatableEntry::make('orders')
                    ->label('')
                    ->schema([
                        Infolists\Components\TextEntry::make('order_number')
                            ->label('رقم الطلب')->weight('bold'),
                        Infolists\Components\TextEntry::make('status')
                            ->label('الحالة')
                            ->badge()
                            ->formatStateUsing(fn($state) => \App\Models\Order::$statusLabels[$state] ?? $state)
                            ->color(fn($state) => match($state) {
                                'delivered'             => 'success',
                                'cancelled','returned'  => 'danger',
                                'pending'               => 'warning',
                                default                 => 'info',
                            }),
                        Infolists\Components\TextEntry::make('total')
                            ->label('الإجمالي')->money('ILS'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('التاريخ')->dateTime('d/m/Y'),
                    ])
                    ->columns(4),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('العميل')
                    ->searchable()->sortable()->weight('bold')
                    ->description(fn($record) => $record->email),

                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف')
                    ->searchable()->copyable()
                    ->icon('heroicon-o-phone')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('orders_count')
                    ->label('الطلبات')
                    ->counts('orders')
                    ->sortable()->badge()->color('primary'),

                Tables\Columns\TextColumn::make('tier')
                    ->label('التصنيف')
                    ->getStateUsing(function ($record) {
                        $spent = TierService::totalSpent($record);
                        $tier  = TierService::tierForAmount($spent);
                        return $tier['emoji'] . ' ' . $tier['label'];
                    })
                    ->badge()
                    ->color(function ($record) {
                        $spent = TierService::totalSpent($record);
                        return TierService::tierForAmount($spent)['color'];
                    }),

                Tables\Columns\TextColumn::make('total_spent')
                    ->label('إجمالي الإنفاق')
                    ->getStateUsing(fn($record) => TierService::totalSpent($record))
                    ->money('ILS')
                    ->weight('bold')->color('success'),

                Tables\Columns\TextColumn::make('loyalty_points')
                    ->label('نقاط الولاء')
                    ->sortable()->badge()->color('warning'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('d/m/Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('has_orders')
                    ->label('لديهم طلبات')
                    ->query(fn($query) => $query->has('orders')),
                Tables\Filters\Filter::make('new_this_week')
                    ->label('مسجلون هذا الأسبوع')
                    ->query(fn($query) => $query->where('created_at', '>=', now()->startOfWeek())),
                Tables\Filters\SelectFilter::make('tier')
                    ->label('التصنيف')
                    ->options([
                        'new'    => '🎯 جديد',
                        'bronze' => '🥉 برونزي',
                        'silver' => '🥈 فضي',
                        'gold'   => '🥇 ذهبي',
                        'vip'    => '💎 VIP',
                    ])
                    ->query(function ($query, array $data) {
                        if (empty($data['value'])) return $query;
                        $tiers = TierService::tiers();
                        $idx   = array_search($data['value'], array_column($tiers, 'key'));
                        if ($idx === false) return $query;
                        $min = $tiers[$idx]['min'];
                        $max = isset($tiers[$idx + 1]) ? $tiers[$idx + 1]['min'] : PHP_INT_MAX;
                        return $query->whereHas('orders', function ($q) use ($min, $max) {
                            $q->whereNotIn('status', ['cancelled', 'returned'])
                              ->havingRaw('SUM(total) >= ? AND SUM(total) < ?', [$min, $max]);
                        }, '>=', 1)->orWhere(function ($q) use ($min) {
                            // handle edge case: users with no orders in new tier
                            if ($min === 0) {
                                $q->whereDoesntHave('orders', fn($oq) =>
                                    $oq->whereNotIn('status', ['cancelled', 'returned'])
                                );
                            }
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('الملف'),
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\Action::make('whatsapp')
                    ->label('واتساب')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(fn(User $record) => $record->phone
                        ? 'https://wa.me/' . preg_replace('/\D/', '', $record->phone)
                        : null)
                    ->openUrlInNewTab()
                    ->visible(fn(User $record) => (bool) $record->phone),
                Tables\Actions\Action::make('delete_account')
                    ->label('حذف الحساب')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('حذف حساب العميل نهائياً')
                    ->modalDescription(fn(User $record) =>
                        "هل تريد حذف حساب {$record->name} ({$record->email}) نهائياً؟ سيتم الاحتفاظ بسجل الطلبات."
                    )
                    ->modalSubmitActionLabel('نعم، احذف الحساب')
                    ->action(function (User $record) {
                        // Keep orders anonymised
                        $record->orders()->update([
                            'customer_name'  => 'حساب محذوف',
                            'customer_phone' => null,
                            'customer_email' => null,
                        ]);
                        $record->addresses()->delete();
                        $record->tokens()->delete();
                        $record->delete();

                        \Filament\Notifications\Notification::make()
                            ->title('تم حذف الحساب بنجاح')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view'   => Pages\ViewCustomer::route('/{record}'),
            'edit'   => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
