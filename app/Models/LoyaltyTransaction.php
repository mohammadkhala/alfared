<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyTransaction extends Model
{
    protected $fillable = [
        'user_id', 'order_id', 'admin_id', 'points', 'action', 'value_ils', 'note',
    ];

    protected $casts = [
        'points'    => 'integer',
        'value_ils' => 'decimal:2',
    ];

    public static array $actionLabels = [
        'earned'        => 'كسب من طلب',
        'redeemed'      => 'استخدام في طلب',
        'admin_add'     => 'إضافة يدوية',
        'admin_deduct'  => 'خصم يدوي',
        'expired'       => 'انتهاء صلاحية',
    ];

    public static array $actionLocaleKeys = [
        'earned'        => 'loyalty_action_earned',
        'redeemed'      => 'loyalty_action_redeemed',
        'admin_add'     => 'loyalty_action_admin_add',
        'admin_deduct'  => 'loyalty_action_admin_deduct',
        'expired'       => 'loyalty_action_expired',
    ];

    public function user(): BelongsTo  { return $this->belongsTo(User::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function admin(): BelongsTo { return $this->belongsTo(User::class, 'admin_id'); }
}
