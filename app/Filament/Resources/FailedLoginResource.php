<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FailedLoginResource\Pages;
use App\Models\FailedLogin;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FailedLoginResource extends Resource
{
    protected static ?string $model = FailedLogin::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';
    protected static ?string $navigationLabel = 'محاولات الدخول الفاشلة';
    protected static ?string $modelLabel = 'محاولة';
    protected static ?string $pluralModelLabel = 'محاولات الدخول الفاشلة';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?int $navigationSort = 9;

    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('attempted_at')->label('الوقت')
                    ->dateTime('d/m/Y h:i A')->sortable(),

                Tables\Columns\TextColumn::make('email')->label('البريد المُحاوَل')
                    ->searchable()->copyable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('ip')->label('IP')
                    ->searchable()->copyable()
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('source')->label('من')
                    ->badge()
                    ->color(fn($state) => $state === 'admin' ? 'danger' : 'warning')
                    ->formatStateUsing(fn($state) => $state === 'admin' ? '🛡️ لوحة التحكم' : '🛒 المتجر'),

                Tables\Columns\TextColumn::make('reason')->label('السبب')
                    ->badge()
                    ->color(fn($state) => $state === 'rate_limited' ? 'danger' : 'gray')
                    ->formatStateUsing(fn($state) => match($state) {
                        'invalid_credentials' => 'بيانات خاطئة',
                        'rate_limited'        => '🚫 تم الحظر مؤقتاً',
                        default               => $state,
                    }),

                Tables\Columns\TextColumn::make('user_agent')->label('المتصفح')
                    ->limit(50)->tooltip(fn($record) => $record->user_agent),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('source')->label('المصدر')
                    ->options(['admin' => 'لوحة التحكم', 'storefront' => 'المتجر']),
                Tables\Filters\Filter::make('today')
                    ->label('اليوم فقط')
                    ->toggle()
                    ->query(fn(\Illuminate\Database\Eloquent\Builder $query) =>
                        $query->whereDate('attempted_at', today())),
            ])
            ->actions([])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدَّد'),
                ]),
            ])
            ->defaultSort('attempted_at', 'desc')
            ->poll('30s'); // refresh live every 30s
    }

    public static function getNavigationBadge(): ?string
    {
        $recent = FailedLogin::where('attempted_at', '>=', now()->subHour())->count();
        return $recent > 0 ? (string) $recent : null;
    }

    public static function getNavigationBadgeColor(): ?string { return 'danger'; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFailedLogins::route('/'),
        ];
    }
}
