<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Sends a test message so SMTP settings can be verified on hosts where
 * `tinker` is unavailable (shared hosting often disables shell_exec).
 */
class TestMail extends Command
{
    protected $signature = 'mail:test {email : المستلم}';

    protected $description = 'إرسال رسالة اختبار للتأكد من إعدادات البريد';

    public function handle(): int
    {
        $to = $this->argument('email');

        $this->line('المرسِل  : '.config('mail.from.address').' ('.config('mail.from.name').')');
        $this->line('المزوّد  : '.config('mail.default'));
        $this->line('الخادم   : '.config('mail.mailers.smtp.host').':'.config('mail.mailers.smtp.port'));
        $this->line('المستخدم : '.config('mail.mailers.smtp.username'));
        $this->newLine();

        if (config('mail.default') === 'log') {
            $this->warn('MAIL_MAILER=log — الرسائل تُكتب في storage/logs ولا تُرسل فعلياً.');
        }

        $this->info("جارٍ الإرسال إلى {$to} ...");

        try {
            Mail::raw(
                "رسالة اختبار من متجر أبناء الفريد.\n\nإذا وصلتك هذه الرسالة فإعدادات SMTP تعمل بنجاح.",
                fn ($m) => $m->to($to)->subject('اختبار إعدادات البريد — أبناء الفريد')
            );

            $this->newLine();
            $this->info('✓ تم الإرسال بلا أخطاء. افحص صندوق الوارد ومجلد السبام.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->newLine();
            $this->error('✗ فشل الإرسال:');
            $this->line($e->getMessage());

            return self::FAILURE;
        }
    }
}
