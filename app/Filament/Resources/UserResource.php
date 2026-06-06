<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model              = User::class;
    protected static ?string $navigationIcon     = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel    = 'المستخدمون';
    protected static ?string $modelLabel         = 'مستخدم';
    protected static ?string $pluralModelLabel   = 'المستخدمون';
    protected static ?string $navigationGroup    = 'الإعدادات';
    protected static ?int    $navigationSort     = 1;

    // Only admins can manage users
    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermission('manage_users') ?? false;
    }

    // Scope to admin + staff only (customers have their own resource)
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('role', [User::ROLE_ADMIN, User::ROLE_STAFF]);
    }

    public static function form(Form $form): Form
    {
        $isAdmin = auth()->user()?->role === User::ROLE_ADMIN;

        return $form->schema([

            Forms\Components\Section::make('بيانات المستخدم')->schema([
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
                    Forms\Components\Select::make('role')
                        ->label('الدور')
                        ->options([
                            User::ROLE_ADMIN => '👑 مدير كامل',
                            User::ROLE_STAFF => '👤 موظف',
                        ])
                        ->default(User::ROLE_STAFF)
                        ->required()
                        ->live()
                        ->disabled(! $isAdmin),
                ]),
                Forms\Components\TextInput::make('password')
                    ->label('كلمة المرور')
                    ->password()
                    ->revealable()
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn($state) => filled($state))
                    ->nullable()
                    ->helperText('اتركها فارغة للإبقاء على الحالية'),
            ]),

            Forms\Components\Section::make('الصلاحيات')
                ->description('حدد ما يمكن لهذا الموظف الوصول إليه في لوحة التحكم')
                ->schema([
                    Forms\Components\Placeholder::make('admin_note')
                        ->label('')
                        ->content('المدير الكامل لديه وصول غير محدود لجميع الصفحات والعمليات.')
                        ->visible(fn(Forms\Get $get) => $get('role') === User::ROLE_ADMIN),

                    Forms\Components\CheckboxList::make('permissions')
                        ->label('الصلاحيات الممنوحة')
                        ->options(User::PERMISSIONS)
                        ->columns(2)
                        ->bulkToggleable()
                        ->default(User::$defaultStaffPermissions)
                        ->helperText('المحددة بشكل افتراضي هي الصلاحيات القياسية للموظفين — التقارير المالية وإدارة المستخدمين غير محددة بشكل افتراضي.')
                        ->dehydrated(fn(Forms\Get $get) => $get('role') === User::ROLE_STAFF)
                        ->visible(fn(Forms\Get $get) => $get('role') === User::ROLE_STAFF),
                ])
                ->visible($isAdmin),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('المستخدم')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn($record) => $record->email),

                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف')
                    ->searchable()
                    ->copyable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('role')
                    ->label('الدور')
                    ->badge()
                    ->formatStateUsing(fn($state) => match($state) {
                        User::ROLE_ADMIN => '👑 مدير',
                        User::ROLE_STAFF => '👤 موظف',
                        default          => $state,
                    })
                    ->color(fn($state) => match($state) {
                        User::ROLE_ADMIN => 'danger',
                        User::ROLE_STAFF => 'info',
                        default          => 'gray',
                    }),

                Tables\Columns\TextColumn::make('permissions')
                    ->label('الصلاحيات')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->role === User::ROLE_ADMIN) {
                            return 'جميع الصلاحيات';
                        }
                        // Normalize: may arrive as JSON string, array, or null
                        if (is_string($state) && $state !== '') {
                            $state = json_decode($state, true) ?? [];
                        }
                        $perms = (is_array($state) && count($state) > 0)
                            ? $state
                            : User::$defaultStaffPermissions;
                        return count($perms) . ' صلاحية';
                    })
                    ->badge()
                    ->color(fn($record) => $record->role === User::ROLE_ADMIN ? 'danger' : 'primary'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('الدور')
                    ->options([
                        User::ROLE_ADMIN => 'مدير',
                        User::ROLE_STAFF => 'موظف',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف')
                    ->visible(fn(User $record) => $record->id !== auth()->id()),
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
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
