<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\RoadFnService;
use Illuminate\Console\Command;

/**
 * Shows exactly what would be sent to RoadFN for an order, without creating
 * anything. Creating a shipment is billable and can't be undone from our side,
 * so this exists to catch a bad payload — unmapped zone, malformed phone,
 * empty address — before the real call is made for the first time.
 */
class PreviewRoadFnShipment extends Command
{
    protected $signature = 'roadfn:preview {order : رقم الطلب أو order_number} {--reserve : احجز رقم تتبّع فعلي لاختبار الاتصال}';

    protected $description = 'معاينة حمولة شحنة رودفنتي دون إنشائها';

    public function handle(RoadFnService $roadFn): int
    {
        $key = $this->argument('order');

        $order = Order::with(['items', 'deliveryZone.parent'])
            ->where('id', $key)->orWhere('order_number', $key)->first();

        if (! $order) {
            $this->error("لا يوجد طلب بالرقم {$key}.");
            return self::FAILURE;
        }

        $zone = $order->deliveryZone;

        $this->newLine();
        $this->line("الطلب      : {$order->order_number} (id={$order->id})");
        $this->line('المنطقة    : ' . ($zone?->full_name ?? '✗ لا توجد منطقة توصيل'));
        $this->newLine();

        if (! $zone) {
            $this->error('✗ الطلب بلا منطقة توصيل — لا يمكن إرساله.');
            return self::FAILURE;
        }

        $this->line('ربط رودفنتي : city=' . ($zone->roadfn_city_id ?: '✗') . '  area=' . ($zone->roadfn_area_id ?: '✗'));

        if (! $zone->roadfn_city_id || ! $zone->roadfn_area_id) {
            $this->error('✗ المنطقة غير مربوطة بـ رودفنتي. شغّل roadfn:sync-zones أولاً.');
            return self::FAILURE;
        }

        // Mirrors RoadFnService::createShipment so the preview can't drift from
        // what actually gets sent.
        $phone = '0' . substr(preg_replace('/\D+/', '', (string) $order->customer_phone), -9);

        $payload = [
            'ShipmentTrackingNo' => '(يُحجز عند الإرسال)',
            'clientName'         => $order->customer_name,
            'clientPhone'        => $phone,
            'clientCityId'       => $zone->roadfn_city_id,
            'clientAreaId'       => $zone->roadfn_area_id,
            'clientAddress'      => trim($order->address_line . ' ' . $order->building),
            'shipmentTotal'      => (float) $order->total,
            'ShipmentTypeID'     => config('services.roadfn.default_shipment_type'),
            'Remarks'            => $order->delivery_notes,
            'shipmentContains'   => $order->items->pluck('product_name')->implode(', '),
            'ShipmentQuantity'   => (int) $order->items->sum('quantity') ?: 1,
        ];

        $this->newLine();
        $this->info('الحمولة التي ستُرسل:');
        foreach ($payload as $k => $v) {
            $this->line(sprintf('  %-20s %s', $k, $v === null || $v === '' ? '(فارغ)' : $v));
        }

        // Anything RoadFN is likely to reject, or that would reach the courier
        // as an undeliverable shipment.
        $this->newLine();
        $problems = [];
        if (strlen($phone) !== 10 || ! str_starts_with($phone, '0')) {
            $problems[] = "رقم الهاتف غير سليم بعد التحويل: {$phone}";
        }
        if (trim((string) $payload['clientAddress']) === '') {
            $problems[] = 'العنوان فارغ — لن يجد المندوب الزبون.';
        }
        if ($payload['shipmentTotal'] <= 0) {
            $problems[] = 'قيمة الشحنة صفر — سيُحصّل المندوب صفر شيكل.';
        }
        if (trim((string) $payload['clientName']) === '') {
            $problems[] = 'اسم الزبون فارغ.';
        }
        if ($order->roadfn_tracking_number) {
            $problems[] = "الطلب مُرسل مسبقاً برقم تتبّع {$order->roadfn_tracking_number} — الإرسال ثانيةً يُنشئ شحنة مكرّرة.";
        }

        if ($problems) {
            $this->error('مشاكل يجب حلّها قبل الإرسال:');
            foreach ($problems as $p) {
                $this->line('  ✗ ' . $p);
            }
            return self::FAILURE;
        }

        $this->info('✓ الحمولة سليمة. لم يُرسل أي شيء.');

        if ($this->option('reserve')) {
            $this->newLine();
            $this->line('حجز رقم تتبّع (يثبت أن الاتصال والصلاحيات تعمل، ولا يُنشئ شحنة)...');
            try {
                $this->info('✓ رقم تتبّع محجوز: ' . $roadFn->reserveTrackingNumber());
                $this->line('  لم تُنشأ شحنة — الرقم يبقى غير مستخدم.');
            } catch (\Throwable $e) {
                $this->error('✗ فشل الحجز: ' . $e->getMessage());
                return self::FAILURE;
            }
        }

        return self::SUCCESS;
    }
}
