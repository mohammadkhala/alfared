<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Coupon;
use App\Models\DeliveryZone;
use App\Models\OrderAudit;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    /** Snapshot of original values, captured before save. */
    protected array $beforeSnapshot = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('invoice')
                ->label('🖨️ فاتورة')
                ->color('gray')
                ->url(fn() => route('orders.invoice', $this->record))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make()->label('حذف'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // store original snapshot for diffing on save
        $this->beforeSnapshot = $this->record->only([
            'customer_name','customer_phone','customer_email','city','area',
            'address_line','building','delivery_notes','delivery_zone_id',
            'subtotal','delivery_fee','discount_amount','total',
            'coupon_code','status','payment_method','payment_status','admin_notes',
        ]);
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Recalculate totals server-side
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
        $data['last_edited_by']  = auth()->id();
        $data['last_edited_at']  = now();

        if (!empty($data['items'])) {
            foreach ($data['items'] as $k => $it) {
                $data['items'][$k]['total'] = round((float)$it['price'] * (float)$it['quantity'], 2);
            }
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // Build a diff of changed fields
        $changes = [];
        $after = $this->record->fresh()->only(array_keys($this->beforeSnapshot));

        foreach ($this->beforeSnapshot as $key => $before) {
            $now = $after[$key] ?? null;
            if ((string)$before !== (string)$now) {
                $changes[$key] = ['before' => $before, 'after' => $now];
            }
        }

        if (empty($changes)) {
            return;
        }

        // Pick the action label: if only `status` changed → "status_change"
        $action = (count($changes) === 1 && isset($changes['status']))
            ? 'status_change'
            : 'edit';

        OrderAudit::log($this->record, $action, $changes);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
