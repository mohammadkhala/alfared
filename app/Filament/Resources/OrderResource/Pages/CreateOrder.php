<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Coupon;
use App\Models\DeliveryZone;
use App\Models\OrderAudit;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Server-side recalculation as a safety net.
        $subtotal = 0;
        foreach ($data['items'] ?? [] as $item) {
            $subtotal += (float)($item['price'] ?? 0) * (float)($item['quantity'] ?? 0);
        }

        $discount = 0;
        $couponId = null;
        if (!empty($data['coupon_code'])) {
            $coupon = Coupon::where('code', $data['coupon_code'])->where('is_active', true)->first();
            if ($coupon && $subtotal >= (float)($coupon->min_order_amount ?? 0)) {
                $discount = $coupon->type === 'percentage'
                    ? round($subtotal * ($coupon->value / 100), 2)
                    : (float)$coupon->value;
                if ($coupon->max_discount && $discount > $coupon->max_discount) {
                    $discount = (float)$coupon->max_discount;
                }
                $couponId = $coupon->id;
                $coupon->increment('used_count');
            }
        }

        $delivery = 0;
        if (!empty($data['delivery_zone_id'])) {
            if ($zone = DeliveryZone::find($data['delivery_zone_id'])) {
                $delivery = $zone->calculateFee(max(0, $subtotal - $discount));
            }
        }

        $data['subtotal']        = round($subtotal, 2);
        $data['discount_amount'] = round($discount, 2);
        $data['delivery_fee']    = round($delivery, 2);
        $data['total']           = round(max(0, $subtotal - $discount + $delivery), 2);
        $data['coupon_id']       = $couponId;
        $data['user_id']         = $data['user_id'] ?? auth()->id();

        // Normalize each item line total
        if (!empty($data['items'])) {
            foreach ($data['items'] as $k => $it) {
                $data['items'][$k]['total'] = round((float)$it['price'] * (float)$it['quantity'], 2);
            }
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        OrderAudit::log($this->record, 'create', null, 'تم إنشاء الطلب من لوحة التحكم');
    }
}
