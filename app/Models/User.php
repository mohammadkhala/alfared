<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasApiTokens;

    // ── Role constants ───────────────────────────────────────────────
    const ROLE_ADMIN    = 'admin';
    const ROLE_STAFF    = 'staff';
    const ROLE_CUSTOMER = 'customer';

    // ── All available permissions ─────────────────────────────────────
    public const PERMISSIONS = [
        'view_dashboard'         => 'لوحة التحكم الرئيسية',
        'manage_orders'          => 'إدارة الطلبات',
        'manage_products'        => 'إدارة المنتجات',
        'manage_inventory'       => 'إدارة المخزون',
        'manage_categories'      => 'إدارة الأقسام',
        'manage_brands'          => 'إدارة الماركات',
        'manage_coupons'         => 'إدارة الكوبونات',
        'manage_banners'         => 'إدارة البانرات',
        'view_customers'         => 'عرض العملاء وتعديلهم',
        'manage_users'           => 'إدارة المستخدمين والصلاحيات',
        'view_sales_reports'     => 'تقارير المبيعات',
        'view_financial_reports' => 'التقارير المالية 💰',
        'manage_settings'        => 'إعدادات الموقع',
        'manage_loyalty'         => 'نقاط الولاء',
        'manage_reviews'         => 'إدارة التقييمات',
        'manage_delivery_zones'  => 'مناطق التوصيل',
        'manage_suppliers'       => 'إدارة الموردين',
    ];

    // ── Default permissions for role=staff ───────────────────────────
    // Financial reports and user management are OFF by default
    public static array $defaultStaffPermissions = [
        'view_dashboard',
        'manage_orders',
        'manage_products',
        'manage_inventory',
        'manage_categories',
        'manage_brands',
        'manage_coupons',
        'manage_banners',
        'view_customers',
        'manage_reviews',
        'manage_delivery_zones',
        'manage_suppliers',
    ];

    protected $fillable = [
        'name', 'email', 'phone', 'password',
        'role', 'permissions', 'loyalty_points',
        'fcm_token', 'device_type',
        'referral_code', 'referred_by', 'last_checkin_at', 'checkin_streak',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'permissions'       => 'array',
            'last_checkin_at'   => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->referral_code)) {
                $user->referral_code = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
            }
        });
    }

    public function addresses() { return $this->hasMany(UserAddress::class); }
    public function referrer()  { return $this->belongsTo(User::class, 'referred_by'); }
    public function referrals() { return $this->hasMany(User::class, 'referred_by'); }

    // ── Filament panel access ────────────────────────────────────────
    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_STAFF]);
    }

    // ── Permission check ─────────────────────────────────────────────
    public function hasPermission(string $permission): bool
    {
        // Admin has unrestricted access
        if ($this->role === self::ROLE_ADMIN) {
            return true;
        }

        // Customers have NO admin panel access
        if ($this->role === self::ROLE_CUSTOMER) {
            return false;
        }

        // Staff: use custom permissions if set, otherwise fall back to defaults
        $custom = $this->permissions;

        if (! empty($custom)) {
            return in_array($permission, $custom);
        }

        return in_array($permission, static::$defaultStaffPermissions);
    }

    // ── Relationships ─────────────────────────────────────────────────
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // ── Computed: customer tier ───────────────────────────────────────
    public function getTierKeyAttribute(): string
    {
        return \App\Services\TierService::tierForAmount(
            \App\Services\TierService::totalSpent($this)
        )['key'];
    }
}
