<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class MaintenancePage extends Page implements Forms\Contracts\HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = 'وضع الصيانة';
    protected static ?string $title           = '🚧 وضع الصيانة / قريباً';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?int    $navigationSort  = 5;
    protected static string  $view            = 'filament.pages.maintenance-page';

    public ?array $data = [];

    /* Badge: يظهر "مفعّل" باللون الأحمر إذا كان الوضع شغّالاً */
    public static function getNavigationBadge(): ?string
    {
        try {
            return Setting::get('maintenance_mode') === '1' ? 'مفعّل' : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public function mount(): void
    {
        $phone = Setting::get('store_phone') ?: Setting::get('social_whatsapp') ?: '+970598191312';

        $this->form->fill([
            'maintenance_mode'        => Setting::get('maintenance_mode', '0') === '1',
            'maintenance_type'        => Setting::get('maintenance_type', 'maintenance'),
            'maintenance_title'       => Setting::get('maintenance_title')    ?: 'نحن تحت الصيانة',
            'maintenance_message'     => Setting::get('maintenance_message')  ?: 'نعمل على تحسين الموقع لنقدم لك تجربة أفضل — سنعود قريباً بكل جديد!',
            'maintenance_launch_date' => Setting::get('maintenance_launch_date', ''),
            'maintenance_whatsapp'    => Setting::get('maintenance_whatsapp') ?: $phone,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make()->schema([
                    Forms\Components\Toggle::make('maintenance_mode')
                        ->label('تفعيل وضع الصيانة')
                        ->helperText('عند التشغيل → يُوجَّه كل الزوار لصفحة الصيانة فوراً. لوحة التحكم تبقى تعمل.')
                        ->onColor('danger')
                        ->offColor('success')
                        ->onIcon('heroicon-m-lock-closed')
                        ->offIcon('heroicon-m-lock-open')
                        ->columnSpanFull(),
                ]),

                Forms\Components\Section::make('إعدادات الصفحة')->schema([
                    Forms\Components\Select::make('maintenance_type')
                        ->label('نوع الصفحة')
                        ->options([
                            'maintenance' => '🔧 تحت الصيانة',
                            'coming_soon' => '🚀 قريباً',
                        ])
                        ->default('maintenance')
                        ->required(),

                    Forms\Components\TextInput::make('maintenance_title')
                        ->label('العنوان الرئيسي')
                        ->placeholder('نحن تحت الصيانة')
                        ->maxLength(100)
                        ->helperText('يظهر بخط كبير في منتصف الصفحة'),

                    Forms\Components\Textarea::make('maintenance_message')
                        ->label('رسالة الزوار')
                        ->placeholder('نعمل على تحسين الموقع لنقدم لك تجربة أفضل — سنعود قريباً!')
                        ->rows(3)
                        ->helperText('وصف مختصر يظهر تحت العنوان'),

                    Forms\Components\TextInput::make('maintenance_launch_date')
                        ->label('تاريخ الإطلاق (اختياري)')
                        ->type('date')
                        ->helperText('يظهر عداد تنازلي في صفحة "قريباً" — اتركه فارغاً إن لم تحتج'),

                    Forms\Components\TextInput::make('maintenance_whatsapp')
                        ->label('رقم واتساب للتواصل')
                        ->placeholder('+970598191312')
                        ->helperText('يظهر زر واتساب في الصفحة للزوار'),
                ]),

            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('💾 حفظ وتطبيق')
                ->color('primary')
                ->action('save'),

            Action::make('preview')
                ->label('👁 معاينة الصفحة')
                ->color('gray')
                ->url(route('maintenance.preview'))
                ->openUrlInNewTab(),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::set('maintenance_mode',     $data['maintenance_mode'] ? '1' : '0', 'maintenance');
        Setting::set('maintenance_type',     $data['maintenance_type'] ?? 'maintenance', 'maintenance');
        Setting::set('maintenance_title',    $data['maintenance_title'] ?? '', 'maintenance');
        Setting::set('maintenance_message',  $data['maintenance_message'] ?? '', 'maintenance');
        Setting::set('maintenance_launch_date', $data['maintenance_launch_date'] ?? '', 'maintenance');
        Setting::set('maintenance_whatsapp', $data['maintenance_whatsapp'] ?? '', 'maintenance');

        $isOn = $data['maintenance_mode'] ?? false;

        Notification::make()
            ->title($isOn ? '🔴 وضع الصيانة مفعّل — الموقع محجوب الآن' : '🟢 الموقع يعمل بشكل طبيعي')
            ->color($isOn ? 'danger' : 'success')
            ->send();
    }
}
