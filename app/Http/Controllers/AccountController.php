<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\LoyaltyTransaction;
use App\Models\Wishlist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => ['required', 'string', 'regex:/^\+(970|972)\d{8,10}$/'],
        ], [
            'phone.regex' => 'رقم الهاتف يجب أن يبدأ بـ +970 أو +972 ويتبعه 8 إلى 10 أرقام.',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
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
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('account.index'))->with('success', 'أهلاً بعودتك! 👋');
        }

        // Log failed attempt for security audit
        \App\Models\FailedLogin::record($request, $request->input('email'), 'storefront', 'invalid_credentials');

        // Unified error message (don't reveal whether email exists)
        return back()->withErrors([
            'email' => 'البيانات المُدخلة غير صحيحة. حاول مرة أخرى.',
        ])->withInput($request->except('password'));
    }

    public function registerForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users',
            'phone'    => ['required', 'string', 'regex:/^\+(970|972)\d{8,10}$/'],
            'password' => 'required|string|min:8|confirmed',
        ], [
            'phone.regex' => 'رقم الهاتف يجب أن يبدأ بـ +970 أو +972 ويتبعه 8 إلى 10 أرقام.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        // ── Signup loyalty bonus ──
        $signupBonus = (int) \App\Models\Setting::get('signup_bonus', 0);
        if ($signupBonus > 0 && \App\Services\LoyaltyService::enabled()) {
            $user->increment('loyalty_points', $signupBonus);
            \App\Models\LoyaltyTransaction::create([
                'user_id' => $user->id,
                'points'  => $signupBonus,
                'action'  => 'admin_add',
                'note'    => 'مكافأة التسجيل (تلقائية)',
            ]);
        }

        Auth::login($user);

        return redirect()->route('account.index')->with('success', 'مرحباً بك في أبناء الفريد! 🎉');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('success', 'تم تسجيل الخروج بنجاح');
    }
}
