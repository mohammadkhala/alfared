<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'الأقسام';
    protected static ?string $modelLabel = 'قسم';
    protected static ?string $pluralModelLabel = 'الأقسام';
    protected static ?string $navigationGroup = 'المتجر';
    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermission('manage_categories') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('الأسماء')->schema([
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\TextInput::make('name_ar')
                        ->label('الاسم (عربي)')->required()->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn($state, Forms\Set $set) =>
                            $set('slug', Str::slug($state))),
                    Forms\Components\TextInput::make('name_he')->label('الاسم (عبري)')->maxLength(255),
                    Forms\Components\TextInput::make('name_en')->label('الاسم (انجليزي)')->maxLength(255),
                ]),
                Forms\Components\TextInput::make('slug')->label('الرابط')->required()
                    ->unique(ignoreRecord: true)->maxLength(255),
            ]),

            Forms\Components\Section::make('الشكل والترتيب')->schema([
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\FileUpload::make('image')
                        ->label('الصورة')
                        ->image()
                        ->directory('categories')
                        ->maxSize(1024)
                        ->acceptedFileTypes(['image/jpeg','image/png','image/webp'])
                        ->helperText('📐 400 × 400 بكسل (مربع) · 📁 أقل من 1 MB'),
                    Forms\Components\TextInput::make('icon')->label('أيقونة (emoji أو اسم)')->maxLength(50),
                    Forms\Components\ColorPicker::make('color')->label('لون الخلفية'),
                ]),
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\Select::make('parent_id')->label('القسم الرئيسي')
                        ->options(Category::whereNull('parent_id')->pluck('name_ar', 'id'))
                        ->nullable()->searchable(),
                    Forms\Components\TextInput::make('sort_order')->label('الترتيب')->numeric()->default(0),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Toggle::make('is_active')->label('فعال')->default(true)->inline(false),
                        Forms\Components\Toggle::make('show_in_menu')->label('في القائمة')->default(true)->inline(false),
                    ]),
                ]),
                Forms\Components\Textarea::make('description_ar')->label('الوصف')->rows(2)->maxLength(500)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('الصورة')->circular(),
                Tables\Columns\TextColumn::make('name_ar')->label('الاسم')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('name_en')->label('EN')->searchable(),
                Tables\Columns\TextColumn::make('slug')->label('الرابط')->searchable(),
                Tables\Columns\TextColumn::make('products_count')->label('المنتجات')
                    ->counts('products')->sortable(),
                Tables\Columns\TextColumn::make('sort_order')->label('الترتيب')->sortable(),
                Tables\Columns\IconColumn::make('show_in_menu')->label('في القائمة')->boolean(),
                Tables\Columns\ToggleColumn::make('is_active')->label('فعال'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('الفعالة'),
                Tables\Filters\TernaryFilter::make('show_in_menu')->label('في القائمة'),
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
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit'   => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
