<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeliveryZoneResource\Pages;
use App\Models\DeliveryZone;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DeliveryZoneResource extends Resource
{
    protected static ?string $model = DeliveryZone::class;
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'مناطق التوصيل';
    protected static ?string $modelLabel = 'منطقة توصيل';
    protected static ?string $pluralModelLabel = 'مناطق التوصيل';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermission('manage_delivery_zones') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('الأسماء')->schema([
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\TextInput::make('name_ar')->label('الاسم (عربي)')->required()->maxLength(255),
                    Forms\Components\TextInput::make('name_he')->label('الاسم (عبري)')->maxLength(255),
                    Forms\Components\TextInput::make('name_en')->label('الاسم (انجليزي)')->maxLength(255),
                ]),
            ]),

            Forms\Components\Section::make('رسوم الشحن')
                ->description('السعر يُزامَن من RoadFN. "شحن مجاني فوق" عرض اختياري يضبطه المتجر.')
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('base_fee')->label('رسوم التوصيل (₪)')
                            ->numeric()->prefix('₪')->default(0),
                        Forms\Components\TextInput::make('free_above')->label('شحن مجاني فوق (₪)')
                            ->numeric()->nullable()->prefix('₪')
                            ->helperText('اتركه فارغاً إذا لا يوجد شحن مجاني'),
                        Forms\Components\TextInput::make('estimated_days')->label('أيام التوصيل المتوقعة')
                            ->numeric()->default(1)->suffix('يوم'),
                    ]),
                ]),

            Forms\Components\Section::make('الإعدادات')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Toggle::make('is_active')->label('فعال')->default(true)->inline(false),
                    Forms\Components\TextInput::make('sort_order')->label('الترتيب')->numeric()->default(0),
                ]),
            ]),

            Forms\Components\Section::make('ربط RoadFN')
                ->description('تُملأ تلقائياً بأمر "مزامنة من RoadFN". City ID = مدينة الوجهة، Area ID = المنطقة الافتراضية للشحنة.')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('roadfn_city_id')->label('RoadFN City ID'),
                        Forms\Components\TextInput::make('roadfn_area_id')->label('RoadFN Area ID'),
                    ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_ar')->label('المدينة')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('name_en')->label('EN')->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('base_fee')->label('رسوم التوصيل')
                    ->formatStateUsing(fn ($state) => $state . ' ₪')->sortable(),
                Tables\Columns\TextColumn::make('free_above')->label('شحن مجاني فوق')
                    ->formatStateUsing(fn ($state) => $state ? $state . ' ₪' : '—'),
                Tables\Columns\TextColumn::make('roadfn_city_id')->label('RoadFN City')
                    ->badge()->color('info')->placeholder('—'),
                Tables\Columns\TextColumn::make('roadfn_area_id')->label('RoadFN Area')
                    ->badge()->color('gray')->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('estimated_days')->label('أيام التوصيل')
                    ->formatStateUsing(fn ($state) => $state . ' يوم')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ToggleColumn::make('is_active')->label('فعال'),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('الفعالة'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('syncRoadFn')
                    ->label('مزامنة من RoadFN')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('مزامنة مناطق التوصيل من RoadFN')
                    ->modalDescription('سيتم بناء المناطق وأسعارها من قائمة أسعار RoadFN، وتعطيل ما لا يخدمه RoadFN.')
                    ->action(function () {
                        try {
                            \Illuminate\Support\Facades\Artisan::call('roadfn:sync-zones');
                            \Filament\Notifications\Notification::make()
                                ->title('تمت مزامنة المناطق من RoadFN')
                                ->body(trim(\Illuminate\Support\Facades\Artisan::output()))
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('فشلت المزامنة')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDeliveryZones::route('/'),
            'create' => Pages\CreateDeliveryZone::route('/create'),
            'edit'   => Pages\EditDeliveryZone::route('/{record}/edit'),
        ];
    }
}
