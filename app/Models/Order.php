<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'customer_name', 'customer_phone', 'customer_email',
        'city', 'area', 'address_line', 'building', 'delivery_notes', 'delivery_zone_id',
        'subtotal', 'delivery_fee', 'discount_amount', 'total',
        'coupon_code', 'coupon_id', 'status', 'payment_method', 'payment_status',
        'admin_notes', 'confirmed_at', 'shipped_at', 'delivered_at',
        'last_edited_by', 'last_edited_at',
        'loyalty_points_redeemed', 'loyalty_discount',
        'return_requested_at', 'return_reason', 'return_status', 'return_reject_reason',
    ];

    protected $casts = [
        'subtotal'        => 'decimal:2',
        'delivery_fee'    => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total'           => 'decimal:2',
        'confirmed_at'         => 'datetime',
        'shipped_at'           => 'datetime',
        'delivered_at'         => 'datetime',
        'last_edited_at'       => 'datetime',
        'return_requested_at'  => 'datetime',
    ];

    public static $statusLabels = [
        'pending'    => 'بانتظار التأكيد',
        'confirmed'  => 'تم التأكيد',
        'processing' => 'قيد التجهيز',
        'shipped'    => 'في الطريق',
        'delivered'  => 'تم التوصيل',
        'cancelled'  => 'ملغي',
        'returned'   => 'مُرجَّع',
    ];

    public static $statusColors = [
        'pending'    => 'warning',
        'confirmed'  => 'info',
        'processing' => 'info',
        'shipped'    => 'primary',
        'delivered'  => 'success',
        'cancelled'  => 'danger',
        'returned'   => 'danger',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . date('Y') . '-' . str_pad(
                    (Order::whereYear('created_at', date('Y'))->count() + 1),
                    4, '0', STR_PAD_LEFT
                );
            }
        });

        // ── Audit: log inline status changes (and any other model updates) ──
        static::updating(function (Order $order) {
            if (! auth()->check()) return;

            $original = $order->getOriginal();
            $changes  = [];
            foreach ($order->getDirty() as $field => $newVal) {
                // skip noise + skip fields that EditOrder page already logs explicitly
                if (in_array($field, ['updated_at', 'last_edited_at', 'last_edited_by'])) continue;
                $changes[$field] = ['before' => $original[$field] ?? null, 'after' => $newVal];
            }
            if (empty($changes)) return;

            // Skip if this update is from the EditOrder page (it sets last_edited_at)
            if ($order->isDirty('last_edited_at')) return;

            $action = (count($changes) === 1 && isset($changes['status'])) ? 'status_change' : 'edit';

            // Defer creation of audit until AFTER the update finished
            $order->_pendingAudit = ['action' => $action, 'changes' => $changes];
        });

        static::updated(function (Order $order) {
            if (! empty($order->_pendingAudit)) {
                OrderAudit::log($order, $order->_pendingAudit['action'], $order->_pendingAudit['changes']);
                $order->_pendingAudit = null;
            }

            // ── Award loyalty points when order reaches the configured status ──
            $earnStatus = (string) \App\Models\Setting::get('earn_on_status', 'delivered');
            if (
                $order->wasChanged('status')
                && $order->status === $earnStatus
                && \App\Services\LoyaltyService::enabled()
                && $order->user_id
            ) {
                \App\Services\LoyaltyService::awardForOrder($order);
            }

            // ── Push notification to customer on status change ──
            if ($order->wasChanged('status') && $order->user_id) {
                $customer = User::find($order->user_id);
                if ($customer && $customer->fcm_token) {
                    $statusLabel = self::$statusLabels[$order->status] ?? $order->status;
                    \App\Services\FcmService::sendToUser(
                        $customer,
                        '📦 تحديث على طلبك',
                        "طلب #{$order->order_number} • الحالة: {$statusLabel}",
                        [
                            'order_number' => $order->order_number,
                            'status'       => $order->status,
                            'type'         => 'order_status_change',
                        ]
                    );
                }
            }
        });
    }

    /** Transient property to defer audit logging until after update. */
    public ?array $_pendingAudit = null;

    public function user(): BelongsTo         { return $this->belongsTo(User::class); }
    public function deliveryZone(): BelongsTo { return $this->belongsTo(DeliveryZone::class); }
    public function coupon(): BelongsTo       { return $this->belongsTo(Coupon::class); }
    public function items(): HasMany          { return $this->hasMany(OrderItem::class); }
    public function audits(): HasMany         { return $this->hasMany(OrderAudit::class)->latest(); }
    public function lastEditor(): BelongsTo   { return $this->belongsTo(User::class, 'last_edited_by'); }

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabels[$this->status] ?? $this->status;
    }

    public function getWhatsappMessageAttribute(): string
    {
        $msg = "مرحباً {$this->customer_name} 👋\n\n";
        $msg .= "تم استلام طلبك بنجاح! 🎉\n";
        $msg .= "رقم الطلب: #{$this->order_number}\n";
        $msg .= "الإجمالي: {$this->total} ₪\n\n";
        $msg .= "سنتواصل معك قريباً لتأكيد موعد التوصيل.\n";
        $msg .= "شكراً لثقتك بأبناء الفريد 🛍️";
        return urlencode($msg);
    }
}
