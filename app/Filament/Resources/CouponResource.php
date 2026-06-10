<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationLabel = 'كوبونات الخصم';
    protected static ?string $modelLabel = 'كوبون';
    protected static ?string $pluralModelLabel = 'كوبونات الخصم';
    protected static ?string $navigationGroup = 'التسويق';
    protected static ?int    $navigationSort  = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermission('manage_coupons') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات الكوبون')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('code')->label('كود الكوبون')
                        ->required()->maxLength(50)->unique(ignoreRecord: true)
                        ->helperText('سيكون حساساً لحالة الأحرف'),
                    Forms\Components\TextInput::make('name_ar')->label('اسم الكوبون')->maxLength(255),
                ]),
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\Select::make('type')->label('نوع الخصم')
                        ->options(['percentage' => 'نسبة مئوية %', 'fixed' => 'مبلغ ثابت ₪'])
                        ->required()->default('percentage'),
                    Forms\Components\TextInput::make('value')->label('قيمة الخصم')
                        ->numeric()->required()->prefix(''),
                    Forms\Components\TextInput::make('max_discount')->label('أقصى خصم (₪)')
                        ->numeric()->nullable()->prefix('₪'),
                ]),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('min_order_amount')->label('أدنى مبلغ للطلب (₪)')
                        ->numeric()->default(0)->prefix('₪'),
                    Forms\Components\TextInput::make('usage_limit')->label('حد الاستخدام الكلي')
                        ->numeric()->nullable(),
                ]),
            ]),

            Forms\Components\Section::make('التاريخ والحالة')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\DateTimePicker::make('starts_at')->label('تاريخ البدء')->nullable(),
                    Forms\Components\DateTimePicker::make('expires_at')->label('تاريخ الانتهاء')->nullable(),
                ]),
                Forms\Components\Toggle::make('is_active')->label('فعال')->default(true)->inline(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label('الكود')
                    ->searchable()->sortable()->weight('bold')->copyable(),
                Tables\Columns\TextColumn::make('name_ar')->label('الاسم')->searchable(),
                Tables\Columns\TextColumn::make('type')->label('النوع')
                    ->formatStateUsing(fn($state) => $state === 'percentage' ? 'نسبة %' : 'مبلغ ₪')
                    ->badge()->color(fn($state) => $state === 'percentage' ? 'info' : 'warning'),
                Tables\Columns\TextColumn::make('value')->label('الخصم')
                    ->formatStateUsing(fn($record) =>
                        $record->type === 'percentage' ? $record->value . '%' : $record->value . ' ₪'
                    ),
                Tables\Columns\TextColumn::make('used_count')->label('استُخدم')->sortable(),
                Tables\Columns\TextColumn::make('usage_limit')->label('الحد')->default('—'),
                Tables\Columns\TextColumn::make('expires_at')->label('ينتهي')->dateTime('d/m/Y')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('فعال')->boolean(),
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
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit'   => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
