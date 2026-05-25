<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderAudit extends Model
{
    protected $fillable = ['order_id', 'user_id', 'user_name', 'action', 'changes', 'note'];

    protected $casts = [
        'changes' => 'array',
    ];

    public static array $actionLabels = [
        'create'        => '✨ إنشاء الطلب',
        'status_change' => '🔄 تغيير الحالة',
        'edit'          => '✏️ تعديل بيانات',
        'add_item'      => '➕ إضافة منتج',
        'remove_item'   => '➖ حذف منتج',
        'cancel'        => '❌ إلغاء الطلب',
    ];

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function user(): BelongsTo  { return $this->belongsTo(User::class); }

    /** Quick helper to log an action. */
    public static function log(Order $order, string $action, ?array $changes = null, ?string $note = null): self
    {
        $user = auth()->user();
        return static::create([
            'order_id'  => $order->id,
            'user_id'   => $user?->id,
            'user_name' => $user?->name,
            'action'    => $action,
            'changes'   => $changes,
            'note'      => $note,
        ]);
    }
}
