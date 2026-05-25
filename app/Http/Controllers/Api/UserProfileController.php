<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyTransaction;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserAddress;
use App\Services\LoyaltyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    /* ─────────────────── ADDRESSES ─────────────────── */

    public function addresses(Request $request): JsonResponse
    {
        $items = UserAddress::where('user_id', $request->user()->id)
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();
        return response()->json(['addresses' => $items]);
    }

    public function addAddress(Request $request): JsonResponse
    {
        $data = $request->validate([
            'label'            => 'nullable|string|max:60',
            'delivery_zone_id' => 'required|exists:delivery_zones,id',
            'city'             => 'required|string|max:100',
            'area'             => 'nullable|string|max:100',
            'address_line'     => 'required|string|max:500',
            'building'         => 'nullable|string|max:100',
            'phone'            => ['required','string','regex:/^\+(970|972)\d{8,10}$/'],
            'notes'            => 'nullable|string|max:1000',
            'is_default'       => 'nullable|boolean',
        ]);

        $data['user_id'] = $request->user()->id;

        // If this becomes default, unset others
        if (! empty($data['is_default'])) {
            UserAddress::where('user_id', $data['user_id'])->update(['is_default' => false]);
        } else {
            // If it's the user's first address, set default
            $count = UserAddress::where('user_id', $data['user_id'])->count();
            if ($count === 0) $data['is_default'] = true;
        }

        $address = UserAddress::create($data);
        return response()->json(['address' => $address], 201);
    }

    public function updateAddress(Request $request, int $id): JsonResponse
    {
        $address = UserAddress::where('user_id', $request->user()->id)->findOrFail($id);
        $data = $request->validate([
            'label'            => 'nullable|string|max:60',
            'delivery_zone_id' => 'sometimes|exists:delivery_zones,id',
            'city'             => 'sometimes|string|max:100',
            'area'             => 'nullable|string|max:100',
            'address_line'     => 'sometimes|string|max:500',
            'building'         => 'nullable|string|max:100',
            'phone'            => ['sometimes','string','regex:/^\+(970|972)\d{8,10}$/'],
            'notes'            => 'nullable|string|max:1000',
            'is_default'       => 'nullable|boolean',
        ]);
        if (! empty($data['is_default'])) {
            UserAddress::where('user_id', $request->user()->id)->update(['is_default' => false]);
        }
        $address->update($data);
        return response()->json(['address' => $address]);
    }

    public function deleteAddress(Request $request, int $id): JsonResponse
    {
        UserAddress::where('user_id', $request->user()->id)->findOrFail($id)->delete();
        return response()->json(['ok' => true]);
    }

    /* ─────────────────── DAILY CHECK-IN ─────────────────── */

    public function checkinStatus(Request $request): JsonResponse
    {
        $u = $request->user();
        $today = now()->toDateString();
        $alreadyDone = $u->last_checkin_at?->toDateString() === $today;
        $points = (int) Setting::get('daily_checkin_points', 5);

        return response()->json([
            'already_done'   => $alreadyDone,
            'streak'         => (int) $u->checkin_streak,
            'reward_points'  => $points,
            'last_checkin_at'=> $u->last_checkin_at?->toDateString(),
        ]);
    }

    public function checkin(Request $request): JsonResponse
    {
        $u = $request->user();
        $today = now()->toDateString();

        // Already checked in today?
        if ($u->last_checkin_at?->toDateString() === $today) {
            return response()->json(['error' => 'لقد سجّلت الحضور اليوم بالفعل'], 422);
        }

        // Streak logic: if last check-in was yesterday → increment; else reset to 1
        $newStreak = 1;
        if ($u->last_checkin_at?->toDateString() === now()->subDay()->toDateString()) {
            $newStreak = (int) $u->checkin_streak + 1;
        }

        $basePoints  = (int) Setting::get('daily_checkin_points', 5);
        // Bonus every 7 days: +20 points
        $bonus = ($newStreak > 0 && $newStreak % 7 === 0) ? 20 : 0;
        $totalPoints = $basePoints + $bonus;

        $u->update([
            'last_checkin_at' => $today,
            'checkin_streak'  => $newStreak,
        ]);

        if ($totalPoints > 0 && LoyaltyService::enabled()) {
            $u->increment('loyalty_points', $totalPoints);
            LoyaltyTransaction::create([
                'user_id' => $u->id,
                'points'  => $totalPoints,
                'action'  => 'admin_add',
                'note'    => $bonus > 0
                    ? "حضور يومي (يوم {$newStreak}) + مكافأة أسبوع"
                    : "حضور يومي (يوم {$newStreak})",
            ]);
        }

        return response()->json([
            'streak'        => $newStreak,
            'reward_points' => $totalPoints,
            'bonus_points'  => $bonus,
            'balance'       => (int) $u->fresh()->loyalty_points,
        ]);
    }

    /* ─────────────────── REFERRAL ─────────────────── */

    public function referralInfo(Request $request): JsonResponse
    {
        $u = $request->user();

        // Defensive: backfill code if missing (for legacy users created before this feature)
        if (empty($u->referral_code)) {
            do {
                $code = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
            } while (User::where('referral_code', $code)->exists());
            $u->update(['referral_code' => $code]);
            $u->refresh();
        }

        $referralBonus = (int) Setting::get('referral_bonus', 50);
        $count = User::where('referred_by', $u->id)->count();
        return response()->json([
            'referral_code'  => $u->referral_code,
            'bonus_points'   => $referralBonus,
            'invited_count'  => $count,
            'share_message'  => "🎉 انضم لتطبيق أبناء الفريد واحصل على نقاط ولاء مجانية! استخدم كودي: {$u->referral_code}",
        ]);
    }
}
