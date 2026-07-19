<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderCancellation;
use App\Models\LoyaltyTransaction;
use App\Models\OtpCode;
use App\Models\Wishlist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AccountController extends Controller
{
    public function index()
    {
        $user         = auth()->user();
        $ordersCount  = Order::where('user_id', $user->id)->count();
        $totalSpent   = Order::where('user_id', $user->id)->whereIn('status', ['delivered', 'shipped'])->sum('total');
        $loyaltyPoints= $user->loyalty_points ?? 0;
        $wishlistCount= Wishlist::where('user_id', $user->id)->count();
        $recentOrders = Order::where('user_id', $user->id)->latest()->take(5)->get();

        return view('account.index', compact('ordersCount', 'totalSpent', 'loyaltyPoints', 'wishlistCount', 'recentOrders'));
    }

    public function orders()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('account.orders', compact('orders'));
    }

    /** Customer cancelling their own order from the account page. */
    public function cancelOrder(Request $request, string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (! OrderCancellation::customerMayCancel($order)) {
            return back()->with('error', OrderCancellation::blockedReason($order));
        }

        OrderCancellation::cancel(
            $order,
            OrderCancellation::BY_CUSTOMER,
            $request->input('reason'),
        );

        return back()->with('success', "تم إلغاء الطلب #{$order->order_number}.");
    }

    public function wishlist()
    {
        $wishlist = Wishlist::where('user_id', auth()->id())
            ->with('product.brand', 'product.category')
            ->latest()
            ->paginate(12);

        return view('account.wishlist', compact('wishlist'));
    }

    public function points()
    {
        $user    = auth()->user();
        $points  = $user->loyalty_points ?? 0;
        $history = LoyaltyTransaction::where('user_id', $user->id)->latest()->paginate(20);

        return view('account.points', compact('points', 'history'));
    }

    public function profile()
    {
        return view('account.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => ['required', 'string', 'regex:/^\+(970|972)\d{8,10}$/'],
        ], [
            'phone.regex' => 'رقم الهاتف يجب أن يبدأ بـ +970 أو +972 ويتبعه 8 إلى 10 أرقام.',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->filled('email') ? $request->email : null,
            'phone' => $request->phone,
        ];

        // Password change
        if ($request->filled('current_password')) {
            $request->validate([
                'current_password' => 'required',
                'password'         => 'required|string|min:8|confirmed',
            ]);

            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة']);
            }

            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'تم تحديث بياناتك بنجاح ✓');
    }

    // ── Auth ──────────────────────────────────────────────

    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone'    => ['required', 'string', 'regex:/^\+(970|972)\d{8,10}$/'],
            'password' => 'required',
        ], [
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.regex'    => 'رقم الهاتف يجب أن يبدأ بـ +970 أو +972',
            'password.required' => 'كلمة المرور مطلوبة',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            \App\Models\FailedLogin::record($request, $request->phone, 'storefront', 'invalid_credentials');
            return back()->withErrors([
                'phone' => 'رقم الهاتف أو كلمة المرور غير صحيحة.',
            ])->withInput($request->except('password'));
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('account.index'))->with('success', 'أهلاً بعودتك ' . $user->name . '! 👋');
    }

    // ── Register: Step 1 — validate form + send OTP ───────────────────────

    public function registerForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:100',
            'phone'          => ['required', 'string', 'regex:/^\+(970|972)\d{8,10}$/', 'unique:users,phone'],
            'email'          => 'required|email|max:191|unique:users,email',
            'password'       => 'required|string|min:8|confirmed',
            'privacy_policy' => 'accepted',
        ], [
            'name.required'           => 'الاسم مطلوب',
            'phone.required'          => 'رقم الهاتف مطلوب',
            'phone.regex'             => 'رقم الهاتف يجب أن يبدأ بـ +970 أو +972',
            'phone.unique'            => 'رقم الهاتف مسجل مسبقاً — هل تريد تسجيل الدخول؟',
            'email.required'          => 'البريد الإلكتروني مطلوب لتأكيد حسابك',
            'email.email'             => 'صيغة البريد الإلكتروني غير صحيحة',
            'email.unique'            => 'البريد الإلكتروني مسجل مسبقاً',
            'password.min'            => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed'      => 'كلمة المرور وتأكيدها غير متطابقتين',
            'privacy_policy.accepted' => 'يجب الموافقة على سياسة الخصوصية وشروط الاستخدام للمتابعة.',
        ]);

        // Hold the details in the session — the account is only created once
        // the e-mailed code is confirmed, so an unverified address never
        // results in an account.
        session([
            'reg_name'     => $request->name,
            'reg_phone'    => $request->phone,
            'reg_email'    => $request->email,
            'reg_password' => bcrypt($request->password),
            'reg_sent_at'  => now()->timestamp,
        ]);

        // Codes are keyed by phone (the account's future identifier).
        OtpCode::where('phone', $request->phone)->delete();
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        OtpCode::create([
            'phone'      => $request->phone,
            'code'       => $code,
            'expires_at' => now()->addMinutes(10),
        ]);

        try {
            Mail::to($request->email)->send(
                new \App\Mail\EmailVerificationCodeMail($code, $request->name)
            );
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors([
                'email' => 'تعذّر إرسال رمز التأكيد إلى بريدك. تأكد من صحة البريد وحاول مجدداً.',
            ])->withInput();
        }

        return redirect()->route('register.verify');
    }

    // ── Register: Step 2 — OTP verification ─────────────────────────────

    public function registerVerifyForm()
    {
        if (! session('reg_phone')) {
            return redirect()->route('register')->withErrors(['phone' => 'ابدأ التسجيل من جديد.']);
        }
        return view('auth.register-verify', [
            'phone'  => session('reg_phone'),
            'sentAt' => session('reg_sent_at'),
        ]);
    }

    public function registerVerify(Request $request)
    {
        $phone = session('reg_phone');
        if (! $phone) {
            return redirect()->route('register')->withErrors(['phone' => 'انتهت الجلسة، ابدأ التسجيل من جديد.']);
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

        // createVerifiedUser() re-checks the phone in case it was taken
        // while the OTP was pending.
        return $this->createVerifiedUser(
            $request,
            session('reg_name'),
            $phone,
            session('reg_email'),
            session('reg_password'),
        );
    }

    /**
     * Creates the customer, grants the signup bonus, logs them in.
     * Shared by the OTP flow and the direct flow (verification disabled).
     *
     * @param string $hashedPassword already bcrypt-hashed
     */
    private function createVerifiedUser(
        Request $request,
        string $name,
        string $phone,
        ?string $email,
        string $hashedPassword,
    ) {
        // Guard against the phone being taken while the OTP was pending.
        if (User::where('phone', $phone)->exists()) {
            session()->forget(['reg_name','reg_phone','reg_email','reg_password','reg_sent_at']);
            return redirect()->route('login')->withErrors(['phone' => 'رقم الهاتف مسجل مسبقاً، سجّل دخولك.']);
        }

        $user = new User([
            'name'  => $name,
            'phone' => $phone,
            'email' => $email ?: null,
            'role'  => 'customer',
        ]);
        // Reaching here means the e-mailed code was confirmed.
        if ($email) {
            $user->email_verified_at = now();
        }
        // Write hashed password directly to bypass the 'hashed' cast re-hashing
        $user->setRawAttributes(array_merge($user->getAttributes(), [
            'password' => $hashedPassword,
        ]));
        $user->save();

        // Signup bonus
        $signupBonus = (int) \App\Models\Setting::get('signup_bonus', 0);
        if ($signupBonus > 0 && \App\Services\LoyaltyService::enabled()) {
            $user->increment('loyalty_points', $signupBonus);
            \App\Models\LoyaltyTransaction::create([
                'user_id' => $user->id, 'points' => $signupBonus,
                'action'  => 'admin_add', 'note' => 'مكافأة التسجيل (تلقائية)',
            ]);
        }

        session()->forget(['reg_name','reg_phone','reg_email','reg_password','reg_sent_at']);

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route('account.index')->with('success', 'مرحباً بك في أبناء الفريد! 🎉');
    }

    public function registerResendOtp(Request $request)
    {
        $phone = session('reg_phone');
        $email = session('reg_email');
        if (! $phone || ! $email) {
            return response()->json(['error' => 'انتهت الجلسة'], 422);
        }

        OtpCode::where('phone', $phone)->delete();
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        OtpCode::create(['phone' => $phone, 'code' => $code, 'expires_at' => now()->addMinutes(10)]);

        try {
            Mail::to($email)->send(
                new \App\Mail\EmailVerificationCodeMail($code, session('reg_name', ''))
            );
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'تعذّر إرسال البريد، حاول مجدداً'], 500);
        }

        session(['reg_sent_at' => now()->timestamp]);
        return response()->json(['message' => 'تم إعادة الإرسال ✅']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('success', 'تم تسجيل الخروج بنجاح');
    }
}
