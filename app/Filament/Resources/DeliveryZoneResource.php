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

            Forms\Components\Section::make('رسوم الشحن')->schema([
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\TextInput::make('base_fee')->label('رسوم التوصيل (₪)')
                        ->numeric()->required()->prefix('₪')->default(0),
                    Forms\Components\TextInput::make('free_above')->label('شحن مجاني فوق (₪)')
                        ->numeric()->nullable()->prefix('₪')
                        ->helperText('اتركه فارغاً إذا لا يوجد شحن مجاني'),
                    Forms\Components\TextInput::make('estimated_days')->label('أيام التوصيل المتوقعة')
                        ->numeric()->default(1)->suffix('يوم'),
                ]),
                Forms\Components\Toggle::make('is_active')->label('فعال')->default(true)->inline(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_ar')->label('المنطقة')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('name_en')->label('EN')->searchable(),
                Tables\Columns\TextColumn::make('base_fee')->label('رسوم التوصيل')
                    ->formatStateUsing(fn($state) => $state . ' ₪')->sortable(),
                Tables\Columns\TextColumn::make('free_above')->label('شحن مجاني فوق')
                    ->formatStateUsing(fn($state) => $state ? $state . ' ₪' : '—'),
                Tables\Columns\TextColumn::make('estimated_days')->label('أيام التوصيل')
                    ->suffix(' يوم'),
                Tables\Columns\ToggleColumn::make('is_active')->label('فعال'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('الفعالة'),
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
