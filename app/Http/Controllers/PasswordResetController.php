<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetCodeMail;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\WaSenderService;
use App\Support\OtpThrottle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

/**
 * Password recovery via a WhatsApp one-time code.
 */
class PasswordResetController extends Controller
{
    private const PHONE_RULE = ['required', 'string', 'regex:/^\+(970|972)\d{8,10}$/'];

    public function requestForm()
    {
        return view('auth.forgot-password');
    }

    /** Step 1 — send the code over WhatsApp or e-mail. */
    public function sendCode(Request $request)
    {
        $method = $request->input('method') === 'email' ? 'email' : 'whatsapp';

        if ($method === 'email') {
            $request->validate(['email' => ['required', 'email', 'max:191']], [
                'email.required' => 'البريد الإلكتروني مطلوب',
                'email.email'    => 'صيغة البريد الإلكتروني غير صحيحة',
            ]);
            $identifier = $request->email;
            $user       = User::where('email', $identifier)->first();
        } else {
            $request->validate(['phone' => self::PHONE_RULE], [
                'phone.required' => 'رقم الهاتف مطلوب',
                'phone.regex'    => 'رقم الهاتف يجب أن يبدأ بـ +970 أو +972',
            ]);
            $identifier = $request->phone;
            $user       = User::where('phone', $identifier)->first();
        }

        // Same response whether or not the account exists, so this page can't
        // be used to discover which phones/e-mails are registered. No code is
        // generated for an unknown account.
        if (! $user) {
            session(['reset_phone' => null, 'reset_label' => $identifier, 'reset_method' => $method]);
            return redirect()->route('password.verify');
        }

        // OTPs are always keyed by the account's phone, whichever channel
        // delivers them, so verification stays identical for both.
        $key = $user->phone;

        if ($wait = OtpThrottle::retryAfter($key, $method)) {
            $field = $method === 'email' ? 'email' : 'phone';
            return back()->withErrors([$field => OtpThrottle::message($wait)])->withInput();
        }

        OtpCode::where('phone', $key)->delete();
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        OtpCode::create(['phone' => $key, 'code' => $code, 'expires_at' => now()->addMinutes(5)]);

        OtpThrottle::record($key, $method);

        if ($method === 'email') {
            try {
                Mail::to($user->email)->send(new PasswordResetCodeMail($code, $user->name ?? ''));
            } catch (\Throwable $e) {
                report($e);
                return back()->withErrors([
                    'email' => 'تعذّر إرسال البريد الآن. جرّب الاستعادة عبر واتساب.',
                ])->withInput();
            }
        } elseif (! app(WaSenderService::class)->sendOtp($key, $code)) {
            return back()->withErrors([
                'phone' => 'تعذّر إرسال الرمز عبر واتساب. تأكد أن رقمك مسجّل على واتساب وحاول مجدداً.',
            ])->withInput();
        }

        session([
            'reset_phone'  => $key,
            'reset_label'  => $identifier,
            'reset_method' => $method,
        ]);

        return redirect()->route('password.verify');
    }

    public function verifyForm()
    {
        // reset_phone is null for unknown accounts — the form still shows so
        // we don't leak whether the identifier exists.
        if (! session()->has('reset_label')) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password', [
            'label'  => session('reset_label'),
            'method' => session('reset_method', 'whatsapp'),
        ]);
    }

    /** Step 2 — check the code and set the new password. */
    public function reset(Request $request)
    {
        $phone = session('reset_phone');
        if (! $phone) {
            // Unknown account, or the session expired — same generic error.
            return back()->withErrors(['code' => 'الرمز غير صحيح أو منتهي الصلاحية.']);
        }

        $request->validate([
            'code'     => ['required', 'string', 'size:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'code.required'      => 'أدخل رمز التحقق',
            'code.size'          => 'الرمز يجب أن يكون 6 أرقام',
            'password.required'  => 'كلمة المرور مطلوبة',
            'password.min'       => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'كلمة المرور وتأكيدها غير متطابقتين',
        ]);

        $otp = OtpCode::where('phone', $phone)
            ->where('code', $request->code)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (! $otp) {
            return back()->withErrors(['code' => 'الرمز غير صحيح أو منتهي الصلاحية.']);
        }

        $user = User::where('phone', $phone)->first();
        if (! $user) {
            return redirect()->route('password.request')
                ->withErrors(['phone' => 'لا يوجد حساب بهذا الرقم.']);
        }

        $otp->update(['used' => true]);

        $user->forceFill(['password' => Hash::make($request->password)])->save();

        // Old sessions/tokens should not survive a password reset.
        $user->tokens()->delete();

        session()->forget(['reset_phone', 'reset_label', 'reset_method']);
        OtpThrottle::clear($phone);

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route('account.index')
            ->with('success', 'تم تغيير كلمة المرور بنجاح ✓');
    }
}
