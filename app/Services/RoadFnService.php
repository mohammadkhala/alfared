<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RoadFnService
{
    private string $baseUrl;
    private string $username;
    private string $password;
    private string $deviceToken;

    public function __construct()
    {
        $this->baseUrl     = rtrim(config('services.roadfn.base_url'), '/') . '/';
        $this->username    = config('services.roadfn.username') ?? '';
        $this->password    = config('services.roadfn.password') ?? '';
        $this->deviceToken = config('services.roadfn.device_token') ?? '';
    }

    /** Logs in against RoadFN and returns a fresh JWT (not cached). */
    public function login(): string
    {
        $response = Http::post("{$this->baseUrl}api/Login", [
            'userName'    => $this->username,
            'password'    => $this->password,
            'deviceToken' => $this->deviceToken,
        ]);

        if (! $response->successful()) {
            Log::error('RoadFN login failed', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \RuntimeException('فشل تسجيل الدخول إلى RoadFN.');
        }

        $token = $response->json('Token');
        if (! $token) {
            Log::error('RoadFN login: no token in response', ['body' => $response->json()]);
            throw new \RuntimeException('استجابة RoadFN لا تحتوي على token.');
        }

        return $token;
    }

    /** Token is valid ~1 day server-side; cache for less than that. */
    private function token(): string
    {
        return Cache::remember('roadfn_token', now()->addHours(23), fn () => $this->login());
    }

    private function get(string $uri, array $query = []): array
    {
        return $this->send(fn (string $token) => Http::withToken($token)->get("{$this->baseUrl}{$uri}", $query));
    }

    private function post(string $uri, array $data = []): array
    {
        return $this->send(fn (string $token) => Http::withToken($token)->post("{$this->baseUrl}{$uri}", $data));
    }

    /** Runs the request, retrying once with a fresh token on 401. */
    private function send(\Closure $call): array
    {
        $response = $call($this->token());

        if ($response->status() === 401) {
            Cache::forget('roadfn_token');
            $response = $call($this->token());
        }

        if (! $response->successful()) {
            Log::error('RoadFN request failed', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \RuntimeException('فشل الاتصال بـ RoadFN: ' . ($response->json('message') ?? "HTTP {$response->status()}"));
        }

        return $response->json() ?? [];
    }

    /** One-off lookup used by `roadfn:list-locations` to map our zones to RoadFN's IDs. */
    public function getCities(): array
    {
        return $this->get('api/Business/Cities');
    }

    /** Param name is a best guess (docs didn't spell it out) — verify against a real response. */
    public function getAreas(string $cityId): array
    {
        return $this->get('api/Business/Areas', ['CityId' => $cityId]);
    }

    public function getShipmentFee(string $cityId, string $areaId): array
    {
        return $this->get('api/Business/GetShipmentFee', ['ToCityId' => $cityId, 'ToAreaId' => $areaId]);
    }

    /** This merchant's per-destination price list (FromCity is fixed to the account branch). */
    public function getShippingFees(): array
    {
        return $this->get('api/Business/ListOfShippingFees');
    }

    /**
     * A sensible default area to ship a whole city to: RoadFN gives each city a
     * generic "الرجاء إدخال عنوان واضح" catch-all area; where none exists we fall
     * back to the first area. The customer's typed address still guides the driver.
     */
    public function resolveDefaultArea(string $cityId): ?string
    {
        return $this->pickDefaultAreaId($this->getAreas($cityId));
    }

    /** Prefer the generic "الرجاء إدخال عنوان واضح" catch-all area, else the first. */
    public function pickDefaultAreaId(array $areas): ?string
    {
        $catchAll = collect($areas)->first(function ($a) {
            $name = $a['AreaName'] ?? '';
            return str_contains($name, 'الرجاء') || str_contains($name, 'عنوان واضح') || str_contains($name, 'ادخال عنوان');
        });

        $pick = $catchAll ?? ($areas[0] ?? null);
        return $pick ? (string) $pick['Id'] : null;
    }

    /**
     * Creates a real RoadFN shipment for the order and records it on the order
     * (tracking number, internal id, RoadFN status). The order's delivery zone
     * must already have roadfn_city_id/roadfn_area_id mapped.
     */
    public function createShipment(Order $order): void
    {
        $zone = $order->deliveryZone;
        if (! $zone || ! $zone->roadfn_city_id || ! $zone->roadfn_area_id) {
            throw new \RuntimeException('منطقة التوصيل لهذا الطلب غير مربوطة بمدينة/منطقة RoadFN بعد.');
        }

        $order->loadMissing('items');

        // RoadFN requires the merchant to reserve the tracking number up front
        // and pass it into the create call.
        $tracking = $this->reserveTrackingNumber();

        // Card orders are already settled through Lahza, so the driver must not
        // collect anything — send 0 as the collectible and say why on the note.
        $prepaid = in_array($order->payment_method, ['lahza', 'card'], true)
            && $order->payment_status === 'paid';

        $remarks = array_filter([
            $order->delivery_notes,
            $prepaid ? 'مدفوع مسبقاً بالبطاقة — لا يوجد مبلغ للتحصيل' : null,
        ]);

        $payload = [
            'ShipmentTrackingNo' => $tracking,
            // Our own order number, so the same reference identifies the order
            // on both sides.
            'ShipmentReference'  => $order->order_number,
            'clientName'         => $order->customer_name,
            // RoadFN wants a 10-digit local number (digits only), not +970…
            'clientPhone'        => $this->localPhone($order->customer_phone),
            'clientCityId'       => $zone->roadfn_city_id,
            // The neighbourhood the customer picked, else the city's default.
            'clientAreaId'       => $order->roadfn_area_id ?: $zone->roadfn_area_id,
            'clientAddress'      => trim($order->address_line . ' ' . $order->building),
            'shipmentTotal'      => $prepaid ? 0 : (float) $order->total,
            'ShipmentTypeID'     => config('services.roadfn.default_shipment_type'),
            'Remarks'            => implode(' — ', $remarks) ?: null,
            'shipmentContains'   => $order->items->pluck('product_name')->implode(', '),
            'ShipmentQuantity'   => (int) $order->items->sum('quantity') ?: 1,
        ];

        $this->post('api/Business/CreateShipmentConfirm', $payload);

        // We already hold the tracking number; look the created shipment up by it
        // to capture the internal id + current status for future polling.
        $record = $this->findShipmentByTracking($tracking) ?? ['ShipmentTrackingNo' => $tracking];

        $update = [
            'roadfn_tracking_number' => $tracking,
            'roadfn_shipment_id'     => $record['ID'] ?? null,
            'roadfn_sent_at'         => now(),
        ];

        // Move the order ourselves rather than waiting for RoadFN to report a
        // status. A shipment RoadFN hasn't indexed yet comes back with no
        // StatusId, so applyShipmentRecord leaves the status alone and the
        // customer keeps seeing "بانتظار التأكيد" for an order already handed
        // to the courier. Only advance — never drag a further-along order back.
        if (in_array($order->status, ['pending', 'confirmed', 'processing'], true)) {
            $update['status'] = 'sent_to_delivery';
        }

        $order->update($update);

        // RoadFN's own status wins from here; it can only move the order forward
        // through the mapped states.
        $this->applyShipmentRecord($order, $record);
    }

    /** Reserves a new tracking number (RoadFN requires it before creating). */
    public function reserveTrackingNumber(): string
    {
        $code = $this->post('api/Business/GetShipmentsTrackingGeneratedCode')['GeneratedCode'] ?? null;
        if (! $code) {
            throw new \RuntimeException('تعذّر حجز رقم تتبّع من RoadFN.');
        }
        return $code;
    }

    private function localPhone(string $phone): string
    {
        return '0' . substr(preg_replace('/\D+/', '', $phone), -9);
    }

    /** GET /ShipmentList — the recent shipments for this merchant. */
    public function listShipments(): array
    {
        return $this->get('api/Business/ShipmentList');
    }

    /** POST /ShipmentListWithIds — exactly these shipments, regardless of date. */
    public function shipmentsByIds(array $ids): array
    {
        if (empty($ids)) return [];
        return $this->send(fn (string $token) => Http::withToken($token)
            ->post("{$this->baseUrl}api/Business/ShipmentListWithIds", array_values(array_map('intval', $ids))));
    }

    /** A shipment found by the tracking number we reserved. */
    public function findShipmentByTracking(string $tracking): ?array
    {
        return collect($this->listShipments())->firstWhere('ShipmentTrackingNo', $tracking);
    }

    /**
     * Pulls one order's current state from RoadFN, on demand.
     *
     * The scheduled sync only runs if cron is actually calling schedule:run,
     * which isn't a given on shared hosting — and until it does, a shipment
     * cancelled at RoadFN keeps showing as on its way to both the admin and the
     * customer. This gives the admin a button that doesn't depend on cron.
     */
    public function refreshOrder(Order $order): bool
    {
        if (blank($order->roadfn_tracking_number)) {
            return false;
        }

        $record = null;

        if (filled($order->roadfn_shipment_id)) {
            $record = collect($this->shipmentsByIds([$order->roadfn_shipment_id]))->first();
        }

        // Falls back to the tracking number for shipments whose internal id was
        // never captured at creation time.
        $record ??= $this->findShipmentByTracking($order->roadfn_tracking_number);

        if (! $record) {
            return false;
        }

        if (blank($order->roadfn_shipment_id) && isset($record['ID'])) {
            $order->update(['roadfn_shipment_id' => $record['ID']]);
        }

        $this->applyShipmentRecord($order, $record);

        return true;
    }

    /** RoadFN StatusId => our order status, or null when unmapped. */
    public function mapStatusId(?int $statusId): ?string
    {
        if ($statusId === null) return null;
        return config('services.roadfn.status_map')[$statusId] ?? null;
    }

    /**
     * Reflects a RoadFN shipment record onto the order: always stores the raw
     * RoadFN status (id + Arabic text), and moves our own status only when the
     * StatusId maps to one of ours. Unmapped statuses are logged, never guessed.
     * The status change flows through Order::update, firing the existing
     * loyalty + push-notification hooks in Order::boot.
     */
    public function applyShipmentRecord(Order $order, array $record): void
    {
        $statusId = isset($record['StatusId']) ? (int) $record['StatusId'] : null;

        $update = [
            'roadfn_status_id' => $statusId,
            'roadfn_status'    => $record['StatusAr'] ?? $record['StatusEn'] ?? $order->roadfn_status,
        ];

        $mapped = $this->mapStatusId($statusId);
        if ($mapped === null) {
            Log::warning('RoadFN sync: unmapped StatusId — add it to services.roadfn.status_map', [
                'order'    => $order->order_number,
                'statusId' => $statusId,
                'statusAr' => $record['StatusAr'] ?? null,
            ]);
        } elseif ($mapped !== $order->status) {
            $update['status'] = $mapped;
            // Stamp the matching timestamp on first transition, if not already set.
            $stamp = ['confirmed' => 'confirmed_at', 'shipped' => 'shipped_at', 'delivered' => 'delivered_at'][$mapped] ?? null;
            if ($stamp && ! $order->{$stamp}) {
                $update[$stamp] = now();
            }
        }

        $order->update($update);
    }
}
