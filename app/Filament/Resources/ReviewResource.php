<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'التقييمات';
    protected static ?string $modelLabel = 'تقييم';
    protected static ?string $pluralModelLabel = 'التقييمات';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermission('manage_reviews') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('reviewer_name')->label('اسم المراجع')->maxLength(255),
                    Forms\Components\Select::make('rating')->label('التقييم')
                        ->options([1=>'★', 2=>'★★', 3=>'★★★', 4=>'★★★★', 5=>'★★★★★'])
                        ->required(),
                ]),
                Forms\Components\Textarea::make('comment')->label('التعليق')->rows(3)->columnSpanFull(),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Toggle::make('is_approved')->label('معتمد')->inline(false),
                    Forms\Components\Toggle::make('is_verified')->label('شراء موثق')->inline(false),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name_ar')->label('المنتج')
                    ->searchable()->sortable()->limit(30),
                Tables\Columns\TextColumn::make('reviewer_name')->label('اسم العميل')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rating')->label('التقييم')
                    ->formatStateUsing(fn($state) => str_repeat('★', $state) . str_repeat('☆', 5 - $state))
                    ->color(fn($state) => $state >= 4 ? 'success' : ($state >= 3 ? 'warning' : 'danger')),
                Tables\Columns\TextColumn::make('comment')->label('التعليق')
                    ->limit(50)->searchable(),
                Tables\Columns\IconColumn::make('is_verified')->label('موثق')->boolean(),
                Tables\Columns\IconColumn::make('is_approved')->label('معتمد')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('التاريخ')
                    ->dateTime('d/m/Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')->label('المعتمدة'),
                Tables\Filters\TernaryFilter::make('is_verified')->label('الموثقة'),
                Tables\Filters\SelectFilter::make('rating')->label('التقييم')
                    ->options([1=>'1 نجمة', 2=>'2 نجوم', 3=>'3 نجوم', 4=>'4 نجوم', 5=>'5 نجوم']),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('اعتماد')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(Review $record) => !$record->is_approved)
                    ->action(fn(Review $record) => $record->update(['is_approved' => true])),
                Tables\Actions\Action::make('reject')
                    ->label('رفض')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(Review $record) => $record->is_approved)
                    ->action(fn(Review $record) => $record->update(['is_approved' => false])),
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve_all')
                        ->label('اعتماد المحدد')
                        ->icon('heroicon-o-check')
                        ->action(fn($records) => $records->each->update(['is_approved' => true])),
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit'   => Pages\EditReview::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::where('is_approved', false)->count();
        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string { return 'warning'; }
}
