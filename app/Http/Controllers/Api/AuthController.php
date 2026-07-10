<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FailedLogin;
use App\Models\LoyaltyTransaction;
use App\Models\OtpCode;
use App\Models\Setting;
use App\Models\User;
use App\Services\LoyaltyService;
use App\Services\WaSenderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'email'         => 'required|email|unique:users',
            'phone'         => ['required', 'string', 'regex:/^\+(970|972)\d{8,10}$/'],
            'password'      => 'required|string|min:8|confirmed',
            'fcm_token'     => 'nullable|string|max:255',
            'device_type'   => 'nullable|in:ios,android',
            'referral_code' => 'nullable|string|max:16',
        ], [
            'phone.regex' => 'رقم الهاتف يجب أن يبدأ بـ +970 أو +972 ويتبعه 8 إلى 10 أرقام.',
        ]);

        // ─── Apply referral code if any ───
        $referredBy = null;
        $referrerBonus = (int) Setting::get('referral_bonus', 50);
        if (! empty($data['referral_code'])) {
            $referrer = User::where('referral_code', strtoupper($data['referral_code']))->first();
            if ($referrer && $referrer->role === User::ROLE_CUSTOMER) {
                $referredBy = $referrer->id;
            }
        }

        $user = User::create([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'phone'       => $data['phone'],
            'password'    => Hash::make($data['password']),
            'fcm_token'   => $data['fcm_token']   ?? null,
            'device_type' => $data['device_type'] ?? null,
            'role'        => User::ROLE_CUSTOMER,
            'referred_by' => $referredBy,
        ]);

        // ─── Reward the referrer too ───
        if ($referredBy && $referrerBonus > 0 && LoyaltyService::enabled()) {
            $referrerUser = User::find($referredBy);
            if ($referrerUser) {
                $referrerUser->increment('loyalty_points', $referrerBonus);
                LoyaltyTransaction::create([
                    'user_id' => $referrerUser->id,
                    'points'  => $referrerBonus,
                    'action'  => 'admin_add',
                    'note'    => "مكافأة دعوة: {$user->name}",
                ]);
            }
        }

        // Signup bonus
        $signupBonus = (int) Setting::get('signup_bonus', 0);
        if ($signupBonus > 0 && LoyaltyService::enabled()) {
            $user->increment('loyalty_points', $signupBonus);
            LoyaltyTransaction::create([
                'user_id' => $user->id,
                'points'  => $signupBonus,
                'action'  => 'admin_add',
                'note'    => 'مكافأة التسجيل من التطبيق',
            ]);
        }

        $token = $user->createToken('mobile-app', ['*'], now()->addMonths(6))->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $this->formatUser($user),
        ], 201);
    }

    // ── OTP via WhatsApp ──────────────────────────────────────────────────────

    public function sendOtp(Request $request): JsonResponse
    {
        $request->validate(['phone' => ['required', 'string', 'regex:/^\+(970|972)\d{8,10}$/']]);

        $phone = $request->phone;
        $code  = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Delete previous OTPs for this phone
        OtpCode::where('phone', $phone)->delete();

        OtpCode::create([
            'phone'      => $phone,
            'code'       => $code,
            'expires_at' => now()->addMinutes(5),
        ]);

        $sent = app(WaSenderService::class)->sendOtp($phone, $code);

        if (! $sent) {
            return response()->json(['message' => 'فشل إرسال رمز التحقق عبر واتساب، تأكد أن رقمك مسجل على واتساب'], 500);
        }

        return response()->json(['message' => 'تم إرسال رمز التحقق عبر واتساب']);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string',
            'code'  => 'required|string|size:6',
        ]);

        $otp = OtpCode::where('phone', $request->phone)
            ->where('code', $request->code)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (! $otp) {
            return response()->json(['message' => 'الرمز غير صحيح أو منتهي الصلاحية'], 422);
        }

        $otp->update(['used' => true]);

        return response()->json(['verified' => true]);
    }

    public function checkAvailability(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
        ]);

        $errors = [];

        if ($request->filled('phone') && User::where('phone', $request->phone)->exists()) {
            $errors['phone'] = 'رقم الهاتف مسجل مسبقاً، هل تريد تسجيل الدخول؟';
        }

        if ($request->filled('email') && User::where('email', $request->email)->exists()) {
            $errors['email'] = 'البريد الإلكتروني مسجل مسبقاً';
        }

        if (! empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }

        return response()->json(['available' => true]);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'phone'       => 'required|string',
            'password'    => 'required|string',
            'fcm_token'   => 'nullable|string|max:255',
            'device_type' => 'nullable|in:ios,android',
        ]);

        // Normalise: strip leading zero then prepend prefix if raw digits given
        $phone = $request->phone;
        $user  = User::where('phone', $phone)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            FailedLogin::record($request, $phone, 'app', 'invalid_credentials');
            throw ValidationException::withMessages([
                'phone' => ['رقم الهاتف أو كلمة المرور غير صحيحة.'],
            ]);
        }

        if ($user->role !== User::ROLE_CUSTOMER) {
            FailedLogin::record($request, $phone, 'app', 'non_customer_attempt');
            throw ValidationException::withMessages([
                'phone' => ['هذا الحساب غير مسموح له بدخول التطبيق.'],
            ]);
        }

        // Update FCM token if provided
        if ($request->filled('fcm_token')) {
            $user->update([
                'fcm_token'   => $request->fcm_token,
                'device_type' => $request->device_type,
            ]);
        }

        $token = $user->createToken('mobile-app', ['*'], now()->addMonths(6))->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $this->formatUser($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'تم تسجيل الخروج']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $this->formatUser($request->user()),
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'name'  => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone' => ['sometimes', 'string', 'regex:/^\+(970|972)\d{8,10}$/'],
        ]);
        $user->update($data);
        return response()->json(['user' => $this->formatUser($user)]);
    }

    /**
     * Permanently delete the authenticated user's account.
     * - Revokes all Sanctum tokens
     * - Clears FCM token (no more push)
     * - Anonymises orders (keeps order history for admin)
     * - Hard-deletes the user row
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $user = $request->user();

        // Revoke all tokens
        $user->tokens()->delete();

        // Anonymise orders so admin keeps the history
        $user->orders()->update([
            'customer_name'  => 'حساب محذوف',
            'customer_phone' => null,
            'customer_email' => null,
        ]);

        // Clear cart (Darryldecode cart uses session; wipe loyalty & address data)
        $user->addresses()->delete();

        // Delete user (cascades to loyalty_transactions, otp_codes, etc.)
        $user->delete();

        return response()->json(['message' => 'تم حذف الحساب نهائياً']);
    }

    /** Update FCM token from app (re-registered, refreshed, etc.). */
    public function updateFcmToken(Request $request): JsonResponse
    {
        $request->validate([
            'fcm_token'   => 'required|string|max:255',
            'device_type' => 'required|in:ios,android',
        ]);
        $request->user()->update([
            'fcm_token'   => $request->fcm_token,
            'device_type' => $request->device_type,
        ]);
        return response()->json(['ok' => true]);
    }

    protected function formatUser(User $user): array
    {
        try {
            $tier = \App\Services\TierService::summary($user);
        } catch (\Throwable $e) {
            report($e);
            $tier = [
                'total_spent'      => 0,
                'tier_key'         => 'new',
                'tier_label'       => 'جديد',
                'tier_emoji'       => '🎯',
                'next_tier_label'  => null,
                'next_tier_emoji'  => null,
                'next_tier_min'    => null,
                'amount_to_next'   => 200,
                'tier_progress'    => 0,
            ];
        }

        return [
            'id'             => $user->id,
            'name'           => $user->name,
            'email'          => $user->email,
            'phone'          => $user->phone,
            'loyalty_points' => (int) ($user->loyalty_points ?? 0),
            'created_at'     => $user->created_at,

            // ── Tier fields ──
            'total_spent'      => $tier['total_spent'],
            'tier_key'         => $tier['tier_key'],
            'tier_label'       => $tier['tier_label'],
            'tier_emoji'       => $tier['tier_emoji'],
            'next_tier_label'  => $tier['next_tier_label'],
            'next_tier_emoji'  => $tier['next_tier_emoji'],
            'next_tier_min'    => $tier['next_tier_min'],
            'amount_to_next'   => $tier['amount_to_next'],
            'tier_progress'    => $tier['tier_progress'],
        ];
    }
}
