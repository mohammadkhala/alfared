<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoyaltyTransactionResource\Pages;
use App\Models\LoyaltyTransaction;
use App\Models\User;
use App\Services\LoyaltyService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LoyaltyTransactionResource extends Resource
{
    protected static ?string $model = LoyaltyTransaction::class;
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'نقاط الولاء';
    protected static ?string $modelLabel = 'عملية نقاط';
    protected static ?string $pluralModelLabel = 'نقاط الولاء';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermission('manage_loyalty') ?? auth()->user()?->role === 'admin';
    }

    public static function form(Form $form): Form
    {
        // The form is used by manual-adjust headeraction. We override how data is saved on the page.
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->label('العميل')
                ->options(fn() => User::where('role', User::ROLE_CUSTOMER)
                    ->orderBy('name')
                    ->limit(200)
                    ->get()
                    ->mapWithKeys(fn($u) => [$u->id => "{$u->name} (#{$u->id}) — {$u->loyalty_points} نقطة"])
                    ->toArray())
                ->searchable()
                ->required(),
            Forms\Components\TextInput::make('points')
                ->label('عدد النقاط')
                ->helperText('قيمة موجبة لإضافة نقاط، قيمة سالبة للخصم')
                ->numeric()
                ->required(),
            Forms\Components\Textarea::make('note')
                ->label('ملاحظة')
                ->rows(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('التاريخ')
                    ->dateTime('d/m/Y h:i A')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('العميل')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('action')->label('الإجراء')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'earned'        => 'success',
                        'redeemed'      => 'warning',
                        'admin_add'     => 'info',
                        'admin_deduct'  => 'danger',
                        'expired'       => 'gray',
                        default         => 'gray',
                    })
                    ->formatStateUsing(fn($state) => LoyaltyTransaction::$actionLabels[$state] ?? $state),
                Tables\Columns\TextColumn::make('points')->label('النقاط')
                    ->sortable()
                    ->color(fn($state) => $state >= 0 ? 'success' : 'danger')
                    ->formatStateUsing(fn($state) => ($state > 0 ? '+' : '') . number_format($state)),
                Tables\Columns\TextColumn::make('value_ils')->label('قيمة (₪)')
                    ->money('ILS')->placeholder('—'),
                Tables\Columns\TextColumn::make('order.order_number')->label('رقم الطلب')
                    ->placeholder('—')
                    ->url(fn($record) => $record->order_id
                        ? route('filament.admin.resources.orders.view', ['record' => $record->order_id])
                        : null),
                Tables\Columns\TextColumn::make('admin.name')->label('بواسطة')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('note')->label('ملاحظة')
                    ->limit(40)->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action')->label('نوع العملية')
                    ->options(LoyaltyTransaction::$actionLabels),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoyaltyTransactions::route('/'),
        ];
    }
}
