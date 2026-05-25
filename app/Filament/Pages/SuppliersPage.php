<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class SuppliersPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'الموردون';
    protected static ?string $title           = 'الموردون';
    protected static ?string $navigationGroup = 'المخزون';
    protected static ?int    $navigationSort  = 2;
    protected static string  $view            = 'filament.pages.suppliers';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasPermission('manage_suppliers') ?? false;
    }
}
