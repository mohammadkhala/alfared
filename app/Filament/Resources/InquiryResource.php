<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InquiryResource\Pages;
use App\Models\Inquiry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class InquiryResource extends Resource
{
    protected static ?string $model = Inquiry::class;
    protected static ?string $navigationIcon  = 'heroicon-o-envelope-open';
    protected static ?string $navigationLabel = 'الاستفسارات';
    protected static ?string $modelLabel      = 'استفسار';
    protected static ?string $pluralModelLabel= 'الاستفسارات';
    protected static ?string $navigationGroup = 'التواصل';
    protected static ?int    $navigationSort  = 1;

    /* Badge: count of new inquiries */
    public static function getNavigationBadge(): ?string
    {
        $count = Inquiry::where('status', 'new')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    /* ── FORM ── */
    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('بيانات المُرسِل')->schema([
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('الاسم')->disabled(),
                    Forms\Components\TextInput::make('phone')
                        ->label('الهاتف')->disabled()
                        ->suffixAction(
                            Forms\Components\Actions\Action::make('whatsapp')
                                ->label('واتساب')
                                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                                ->url(fn($record) => $record ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->phone) : '#')
                                ->openUrlInNewTab()
                        ),
                    Forms\Components\Select::make('status')
                        ->label('الحالة')
                        ->options([
                            'new'     => '🔴 جديد',
                            'read'    => '🟡 مقروء',
                            'replied' => '🟢 تم الرد',
                        ])
                        ->required(),
                ]),
            ]),

            Forms\Components\Section::make('الرسالة')->schema([
                Forms\Components\TextInput::make('subject')
                    ->label('الموضوع')->disabled()->columnSpanFull(),
                Forms\Components\Textarea::make('message')
                    ->label('نص الرسالة')->disabled()->rows(5)->columnSpanFull(),
            ]),

            Forms\Components\Section::make('رد الإدارة')->schema([
                Forms\Components\Textarea::make('admin_reply')
                    ->label('الرد (اختياري)')
                    ->rows(4)
                    ->placeholder('اكتب ردك هنا...')
                    ->columnSpanFull(),
            ]),

        ]);
    }

    /* ── TABLE ── */
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')->sortable()->width(60),

                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')->searchable()->weight('bold'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف')->searchable()->copyable()
                    ->url(fn($record) => 'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->phone))
                    ->openUrlInNewTab()
                    ->color('success'),

                Tables\Columns\TextColumn::make('subject')
                    ->label('الموضوع')->searchable()->limit(40),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn($state) => match($state) {
                        'new'     => 'جديد',
                        'read'    => 'مقروء',
                        'replied' => 'تم الرد',
                        default   => $state,
                    })
                    ->colors([
                        'danger'  => 'new',
                        'warning' => 'read',
                        'success' => 'replied',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('التاريخ')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'new'     => 'جديد',
                        'read'    => 'مقروء',
                        'replied' => 'تم الرد',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('عرض')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->action(function (Inquiry $record) {
                        if ($record->status === 'new') {
                            $record->update([
                                'status'  => 'read',
                                'read_at' => now(),
                            ]);
                        }
                    })
                    ->url(fn(Inquiry $record) => static::getUrl('edit', ['record' => $record])),

                Tables\Actions\Action::make('whatsapp')
                    ->label('رد عبر واتساب')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->url(fn(Inquiry $record) =>
                        'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->phone) .
                        '?text=' . urlencode("مرحباً {$record->name}، بخصوص استفسارك: {$record->subject}")
                    )
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('mark_replied')
                    ->label('تم الرد')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Inquiry $record) {
                        $record->update(['status' => 'replied']);
                        Notification::make()->title('تم تحديث الحالة')->success()->send();
                    })
                    ->visible(fn(Inquiry $record) => $record->status !== 'replied'),

                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_read')
                        ->label('تعليم كمقروء')
                        ->icon('heroicon-o-eye')
                        ->action(fn($records) => $records->each->update(['status' => 'read']))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('mark_replied')
                        ->label('تعليم كمُجاب')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn($records) => $records->each->update(['status' => 'replied']))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInquiries::route('/'),
            'edit'  => Pages\EditInquiry::route('/{record}/edit'),
        ];
    }
}
