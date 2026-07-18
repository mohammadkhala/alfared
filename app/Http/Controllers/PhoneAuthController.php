<?php

namespace App\Http\Controllers;

use App\Models\OtpCode;
use App\Models\User;
use App\Services\WaSenderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PhoneAuthController extends Controller
{
    // ── Step 1: Show phone input form ────────────────────────────────────────

    public function showPhoneForm()
    {
        return view('auth.phone-login');
    }

    // ── Step 2: Send OTP ─────────────────────────────────────────────────────

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/^\+(970|972)\d{8,10}$/'],
        ], [
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.regex'    => 'رقم الهاتف يجب أن يبدأ بـ +970 أو +972',
        ]);

        $phone = $request->phone;

        // Per-phone limit — protects the sender WhatsApp number from spam bans.
        if ($wait = \App\Support\OtpThrottle::retryAfter($phone)) {
            return back()->withErrors(['phone' => \App\Support\OtpThrottle::message($wait)])->withInput();
        }

        // Delete old OTPs for this phone
        OtpCode::where('phone', $phone)->delete();

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'phone'      => $phone,
            'code'       => $code,
            'expires_at' => now()->addMinutes(5),
        ]);

        \App\Support\OtpThrottle::record($phone);

        $sent = app(WaSenderService::class)->sendOtp($phone, $code);

        if (! $sent) {
            return back()->withErrors(['phone' => 'فشل إرسال رمز التحقق عبر واتساب. تأكد أن رقمك مسجل على واتساب وحاول مجدداً.'])->withInput();
        }

        // Store phone in session for the verify step
        session(['otp_phone' => $phone, 'otp_sent_at' => now()->timestamp]);

        return redirect()->route('phone-login.verify');
    }

    // ── Step 3: Show OTP verification form ───────────────────────────────────

    public function showVerifyForm()
    {
        if (! session('otp_phone')) {
            return redirect()->route('phone-login')->withErrors(['phone' => 'ابدأ من جديد بإدخال رقم هاتفك.']);
        }

        return view('auth.phone-verify', [
            'phone'     => session('otp_phone'),
            'sentAt'    => session('otp_sent_at'),
        ]);
    }

    // ── Step 4: Verify OTP → login or register ───────────────────────────────

    public function verifyOtp(Request $request)
    {
        $phone = session('otp_phone');

        if (! $phone) {
            return redirect()->route('phone-login')->withErrors(['phone' => 'انتهت الجلسة، ابدأ من جديد.']);
        }

        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ], [
            'code.required' => 'أدخل رمز التحقق',
            'code.size'     => 'الرمز يجب أن يكون 6 أرقام',
        ]);

        $otp = OtpCode::where('phone', $phone)
            ->where('code', $request->code)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (! $otp) {
            return back()->withErrors(['code' => 'الرمز غير صحيح أو منتهي الصلاحية.']);
        }

        $otp->update(['used' => true]);

        // Find existing user by phone
        $user = User::where('phone', $phone)->first();

        if ($user) {
            // Existing user → login directly
            Auth::login($user, true);
            session()->forget(['otp_phone', 'otp_sent_at']);

            return redirect()->intended(route('account.index'))->with('success', 'مرحباً بعودتك ' . $user->name . ' 👋');
        }

        // New user → store phone_verified in session, show quick register form
        session(['otp_phone_verified' => $phone]);
        session()->forget('otp_sent_at');

        return redirect()->route('phone-login.register');
    }

    // ── Step 5 (new user): Quick registration form ───────────────────────────

    public function showRegisterForm()
    {
        $phone = session('otp_phone_verified');

        if (! $phone) {
            return redirect()->route('phone-login')->withErrors(['phone' => 'يجب التحقق من رقم هاتفك أولاً.']);
        }

        return view('auth.phone-register', compact('phone'));
    }

    public function completeRegistration(Request $request)
    {
        $phone = session('otp_phone_verified');

        if (! $phone) {
            return redirect()->route('phone-login');
        }

        $request->validate([
            'name'           => ['required', 'string', 'max:100'],
            'email'          => ['nullable', 'email', 'max:191', 'unique:users,email'],
            'privacy_policy' => ['accepted'],
        ], [
            'name.required'           => 'الاسم مطلوب',
            'email.email'             => 'البريد الإلكتروني غير صحيح',
            'email.unique'            => 'هذا البريد مسجل مسبقاً',
            'privacy_policy.accepted' => 'يجب الموافقة على سياسة الخصوصية وشروط الاستخدام للمتابعة.',
        ]);

        // Prevent duplicate phone
        if (User::where('phone', $phone)->exists()) {
            session()->forget(['otp_phone', 'otp_phone_verified']);
            return redirect()->route('phone-login')->withErrors(['phone' => 'رقم الهاتف مسجل مسبقاً. سجل الدخول.']);
        }

        $user = User::create([
            'name'     => $request->name,
            'phone'    => $phone,
            'email'    => $request->email ?: null,
            'password' => Hash::make(Str::random(16)), // random password (phone login only)
            'role'     => 'customer',
        ]);

        Auth::login($user, true);
        session()->forget(['otp_phone', 'otp_phone_verified', 'otp_sent_at']);

        return redirect()->route('account.index')->with('success', 'أهلاً وسهلاً ' . $user->name . ' 🎉');
    }

    // ── Resend OTP (AJAX / POST) ──────────────────────────────────────────────

    public function resendOtp(Request $request)
    {
        $phone = session('otp_phone');

        if (! $phone) {
            return response()->json(['error' => 'انتهت الجلسة'], 422);
        }

        if ($wait = \App\Support\OtpThrottle::retryAfter($phone)) {
            return response()->json([
                'error'       => \App\Support\OtpThrottle::message($wait),
                'retry_after' => $wait,
            ], 429);
        }

        OtpCode::where('phone', $phone)->delete();
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        OtpCode::create(['phone' => $phone, 'code' => $code, 'expires_at' => now()->addMinutes(5)]);

        \App\Support\OtpThrottle::record($phone);

        $sent = app(WaSenderService::class)->sendOtp($phone, $code);

        if (! $sent) {
            return response()->json(['error' => 'فشل الإرسال، حاول مجدداً'], 500);
        }

        session(['otp_sent_at' => now()->timestamp]);

        return response()->json(['message' => 'تم إعادة الإرسال ✅']);
    }
}
