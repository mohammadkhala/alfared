<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Explains why RoadFN authentication fails. The service only reports
 * "فشل تسجيل الدخول" and writes the real cause to the log, which is easy to
 * miss on shared hosting.
 */
class DiagnoseRoadFn extends Command
{
    protected $signature = 'roadfn:diagnose {--fresh : تجاهل التوكن المخزّن}';

    protected $description = 'تشخيص فشل الاتصال بـ RoadFN';

    public function handle(): int
    {
        $base   = rtrim((string) config('services.roadfn.base_url'), '/');
        $user   = (string) config('services.roadfn.username');
        $pass   = (string) config('services.roadfn.password');
        $device = (string) config('services.roadfn.device_token');

        $this->newLine();
        $this->line('الإعدادات المقروءة من .env:');
        $this->line('  base_url     : '.($base ?: '(فارغ)'));
        $this->line('  username     : '.($user !== '' ? $user : '✗ فارغ'));
        // Never print the password — length is enough to spot a missing value.
        $this->line('  password     : '.($pass !== '' ? '✓ مضبوطة ('.mb_strlen($pass).' حرف)' : '✗ فارغة'));
        $this->line('  device_token : '.($device !== '' ? '✓ مضبوط' : '⚠️ فارغ'));
        $this->newLine();

        if ($user === '' || $pass === '') {
            $this->error('✗ بيانات الدخول غير مضبوطة في .env على هذا الخادم.');
            $this->line('   أضف ROADFN_USERNAME و ROADFN_PASSWORD ثم: php artisan config:clear');
            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            Cache::forget('roadfn_token');
            $this->line('حُذف التوكن المخزّن.');
        }

        $this->info('محاولة تسجيل الدخول ...');

        try {
            $res = Http::timeout(20)->post("{$base}/api/Login", [
                'userName'    => $user,
                'password'    => $pass,
                'deviceToken' => $device,
            ]);
        } catch (\Throwable $e) {
            $this->error('✗ تعذّر الوصول للخادم: '.$e->getMessage());
            $this->line('   تحقّق أن الاستضافة تسمح بالاتصالات الصادرة (outbound).');
            return self::FAILURE;
        }

        $this->line('رمز الاستجابة: '.$res->status());

        if (! $res->successful()) {
            $this->error('✗ رفض الخادم الطلب.');
            $this->line('الرد: '.mb_substr($res->body(), 0, 400));
            $this->newLine();

            // Read the actual validation errors instead of guessing — a 400
            // here is usually a missing field, not wrong credentials.
            $fields = array_keys((array) $res->json('errors', []));
            if ($fields) {
                $this->error('حقول ناقصة أو غير صالحة: '.implode('، ', $fields));
                $this->line('اضبط ما يقابلها في .env ثم: php artisan config:clear');
            } elseif ($res->status() === 401) {
                $this->line('اسم المستخدم أو كلمة المرور غير صحيحة.');
            } else {
                $this->line('راجع العنوان، أو احتمال حجب الاتصالات الصادرة من الاستضافة.');
            }

            return self::FAILURE;
        }

        $token = $res->json('Token');
        if (! $token) {
            $this->error('✗ نجح الطلب لكن بلا token.');
            $this->line('الرد: '.mb_substr($res->body(), 0, 400));
            return self::FAILURE;
        }

        $this->info('✓ تسجيل الدخول ناجح (طول التوكن: '.mb_strlen($token).').');
        $this->newLine();

        // Prove the token actually works on a real endpoint.
        $cities = Http::timeout(20)
            ->withToken($token)
            ->get("{$base}/api/Business/Cities");

        $this->line('جلب المدن: '.$cities->status());
        if ($cities->successful()) {
            $data = $cities->json();
            $count = is_array($data) ? count($data) : 0;
            $this->info("✓ الاتصال سليم تماماً ({$count} مدينة).");
            return self::SUCCESS;
        }

        $this->error('✗ التوكن لا يعمل على نقاط RoadFN.');
        $this->line('الرد: '.mb_substr($cities->body(), 0, 300));

        return self::FAILURE;
    }
}
