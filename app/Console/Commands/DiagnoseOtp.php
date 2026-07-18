<?php

namespace App\Console\Commands;

use App\Models\OtpCode;
use App\Models\User;
use App\Services\WaSenderService;
use App\Support\OtpThrottle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

/**
 * Explains why a WhatsApp code did or did not go out for a given phone —
 * the send path is deliberately silent about unknown accounts, which makes
 * it hard to tell a missing account from a delivery failure.
 */
class DiagnoseOtp extends Command
{
    protected $signature = 'otp:diagnose {phone : الرقم بصيغة +970... } {--send : محاولة إرسال فعلية}';

    protected $description = 'تشخيص سبب عدم وصول رمز واتساب';

    public function handle(): int
    {
        $phone = $this->argument('phone');
        $this->newLine();

        // 1) Account
        $user = User::where('phone', $phone)->first();
        if ($user) {
            $this->info("✓ الحساب موجود: {$user->name} (#{$user->id})");
        } else {
            $this->error("✗ لا يوجد حساب بالرقم {$phone}");
            $this->warn('  الاستعادة لا ترسل رمزاً لرقم غير مسجّل (إخفاء الأرقام المسجّلة).');
            $this->line('  الأرقام المسجّلة (آخر 5):');
            User::whereNotNull('phone')->latest('id')->limit(5)->pluck('phone')
                ->each(fn ($p) => $this->line("    {$p}"));
        }

        // 2) WaSender config
        $key = config('services.wasender.api_key');
        $this->newLine();
        $this->line($key ? '✓ مفتاح WaSender مضبوط ('.strlen($key).' حرف)' : '✗ مفتاح WaSender غير مضبوط في .env');

        // 3) Daily cap
        $sentToday = (int) Cache::get('wa:otp:sent:'.now()->toDateString(), 0);
        $this->line("• أُرسل اليوم: {$sentToday} / 200");
        if ($sentToday >= 200) {
            $this->error('  ✗ بُلغ السقف اليومي — الإرسال متوقّف حتى الغد.');
        }

        // 4) Per-phone throttle
        $wait = OtpThrottle::retryAfter($phone);
        $this->line($wait > 0
            ? "✗ محظور مؤقتاً لهذا الرقم — انتظر {$wait} ثانية"
            : '✓ لا حظر زمني على هذا الرقم');

        // 5) Latest code
        $otp = OtpCode::where('phone', $phone)->latest('id')->first();
        $this->newLine();
        if ($otp) {
            $state = $otp->used ? 'مستخدم' : ($otp->expires_at->isPast() ? 'منتهي' : 'صالح');
            $this->line("• آخر رمز: {$otp->code} · {$state} · أُنشئ {$otp->created_at->diffForHumans()}");
            $this->warn('  وجود الرمز يعني أن النظام أنشأه — فالمشكلة في تسليم واتساب.');
        } else {
            $this->line('• لا يوجد أي رمز مُنشأ لهذا الرقم.');
        }

        // 6) Optional live send
        if ($this->option('send')) {
            if (! $user) {
                $this->error('لن أرسل: لا حساب بهذا الرقم.');
                return self::FAILURE;
            }
            $this->newLine();
            $this->info('جارٍ إرسال رمز اختباري عبر واتساب...');
            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $ok = app(WaSenderService::class)->sendOtp($phone, $code);
            $ok
                ? $this->info("✓ قبِل المزوّد الإرسال (الرمز {$code}). إن لم تصلك الرسالة فالرقم المرسِل محظور غالباً.")
                : $this->error('✗ فشل الإرسال — راجع storage/logs/laravel.log بحثاً عن WaSender.');
        } else {
            $this->newLine();
            $this->line('لمحاولة إرسال فعلية: php artisan otp:diagnose '.$phone.' --send');
        }

        $this->newLine();
        return self::SUCCESS;
    }
}
