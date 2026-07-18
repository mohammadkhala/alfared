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
            Forms\Components\Section::make('النوع')->schema([
                Forms\Components\Select::make('parent_id')
                    ->label('تابعة لمنطقة رئيسية')
                    ->placeholder('— منطقة رئيسية (تحدّد سعر التوصيل) —')
                    ->options(fn () => \App\Models\DeliveryZone::main()->pluck('name_ar', 'id'))
                    ->searchable()
                    ->live()
                    ->helperText('اتركه فارغاً لإنشاء منطقة رئيسية مثل: الضفة / القدس / الداخل. أو اختر منطقة رئيسية لإضافة مدينة تابعة لها.'),
            ]),

            Forms\Components\Section::make('الأسماء')->schema([
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\TextInput::make('name_ar')->label('الاسم (عربي)')->required()->maxLength(255),
                    Forms\Components\TextInput::make('name_he')->label('الاسم (عبري)')->maxLength(255),
                    Forms\Components\TextInput::make('name_en')->label('الاسم (انجليزي)')->maxLength(255),
                ]),
            ]),

            // Only main zones price anything — a sub zone inherits its parent's
            // fee, so showing these fields on a city would be misleading.
            Forms\Components\Section::make('رسوم الشحن')
                ->description('تُطبَّق على كل المدن التابعة لهذه المنطقة')
                ->visible(fn (Forms\Get $get) => blank($get('parent_id')))
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
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_ar')->label('المنطقة')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('parent.name_ar')->label('تابعة لـ')
                    ->placeholder('— رئيسية —')->badge()->color('primary')->searchable(),
                Tables\Columns\TextColumn::make('children_count')->label('المدن')
                    ->counts('children')->badge()->color('gray')
                    ->formatStateUsing(fn ($state) => $state > 0 ? $state : '—'),
                Tables\Columns\TextColumn::make('name_en')->label('EN')->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // Sub zones inherit their parent's pricing, so show a dash
                // instead of a misleading 0 ₪.
                Tables\Columns\TextColumn::make('base_fee')->label('رسوم التوصيل')
                    ->formatStateUsing(fn ($state, $record) => $record->parent_id ? 'حسب الرئيسية' : $state . ' ₪')
                    ->sortable(),
                Tables\Columns\TextColumn::make('free_above')->label('شحن مجاني فوق')
                    ->formatStateUsing(fn ($state, $record) => $record->parent_id ? '—' : ($state ? $state . ' ₪' : '—')),
                Tables\Columns\TextColumn::make('estimated_days')->label('أيام التوصيل')
                    ->formatStateUsing(fn ($state, $record) => $record->parent_id ? '—' : $state . ' يوم')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ToggleColumn::make('is_active')->label('فعال'),
            ])
            ->defaultSort('parent_id')
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
