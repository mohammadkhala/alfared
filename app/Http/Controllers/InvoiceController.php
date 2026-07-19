<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceController extends Controller
{
    /** Supported invoice languages. */
    private const LANGS = ['ar', 'he', 'en'];

    /** Print a single invoice. ?lang=ar|he|en */
    public function single(Request $request, Order $order)
    {
        abort_unless(auth()->user()?->hasPermission('manage_orders'), 403);

        $lang = $this->resolveLang($request);
        $order->load('items', 'deliveryZone');

        return $this->renderInLang($lang, fn() =>
            view('admin.invoices.single', ['order' => $order, 'lang' => $lang])->render()
        );
    }

    /**
     * The same invoice, for the customer who bought it.
     *
     * single() is admin-only; this is the copy linked from the invoice email,
     * so it goes through OrderAccess — the signed-in owner, staff, or a guest
     * session that proved it knows the order. The order number alone runs in
     * sequence and would otherwise expose every customer's basket.
     */
    public function receipt(Request $request, string $orderNumber)
    {
        $order = Order::with('items', 'deliveryZone.parent')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        \App\Support\OrderAccess::authorize($order);

        $lang = $this->resolveLang($request);

        return $this->renderInLang($lang, fn () =>
            view('admin.invoices.single', ['order' => $order, 'lang' => $lang])->render()
        );
    }

    /** Print many invoices at once. ?ids=1,2,3 OR ?ids=all  &  ?lang=ar|he|en */
    public function bulk(Request $request)
    {
        abort_unless(auth()->user()?->hasPermission('manage_orders'), 403);

        $idsParam = $request->query('ids');
        $lang = $this->resolveLang($request);

        $query = Order::with('items', 'deliveryZone')->orderBy('created_at', 'desc');

        if ($idsParam && $idsParam !== 'all') {
            $ids = collect(explode(',', $idsParam))->filter()->map(fn($i) => (int)$i)->all();
            $query->whereIn('id', $ids);
        }

        $orders = $query->get();
        abort_if($orders->isEmpty(), 404, __('admin_invoice_none'));

        return $this->renderInLang($lang, fn() =>
            view('admin.invoices.bulk', ['orders' => $orders, 'lang' => $lang])->render()
        );
    }

    /** Pick a safe language code from the request, fallback to current locale. */
    private function resolveLang(Request $request): string
    {
        $lang = (string) $request->query('lang', app()->getLocale());
        return in_array($lang, self::LANGS, true) ? $lang : 'ar';
    }

    /** Render a view inside a temporary locale switch. */
    private function renderInLang(string $lang, \Closure $renderer): \Illuminate\Http\Response
    {
        $previous = app()->getLocale();
        app()->setLocale($lang);
        try {
            $html = $renderer();
        } finally {
            app()->setLocale($previous);
        }
        return response($html);
    }

    /** Export orders to Excel-compatible CSV (UTF-8 with BOM). */
    public function export(Request $request): StreamedResponse
    {
        abort_unless(auth()->user()?->hasPermission('manage_orders'), 403);

        $idsParam = $request->query('ids');
        $query = Order::with('items', 'deliveryZone')->orderBy('created_at', 'desc');

        if ($idsParam && $idsParam !== 'all') {
            $ids = collect(explode(',', $idsParam))->filter()->map(fn($i) => (int)$i)->all();
            $query->whereIn('id', $ids);
        }

        $filename = 'orders-' . now()->format('Y-m-d-Hi') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM so Excel detects Arabic correctly
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'رقم الطلب', 'التاريخ', 'العميل', 'الهاتف', 'البريد',
                'المدينة', 'المنطقة', 'العنوان', 'المنطقة الجغرافية',
                'عدد المنتجات', 'المنتجات',
                'المجموع الفرعي', 'الخصم', 'كود الكوبون', 'رسوم التوصيل', 'الإجمالي',
                'طريقة الدفع', 'حالة الدفع', 'الحالة',
                'ملاحظات الإدارة', 'ملاحظات التوصيل',
                'آخر تعديل بواسطة', 'تاريخ آخر تعديل',
            ]);

            $query->chunk(200, function ($orders) use ($out) {
                foreach ($orders as $order) {
                    $itemsText = $order->items->map(fn($it) =>
                        $it->product_name . ' × ' . $it->quantity
                    )->implode(' | ');

                    fputcsv($out, [
                        $order->order_number,
                        $order->created_at?->format('Y-m-d H:i'),
                        $order->customer_name,
                        $order->customer_phone,
                        $order->customer_email,
                        $order->city,
                        $order->area,
                        $order->address_line,
                        optional($order->deliveryZone)->name_ar,
                        $order->items->count(),
                        $itemsText,
                        number_format($order->subtotal, 2, '.', ''),
                        number_format($order->discount_amount, 2, '.', ''),
                        $order->coupon_code,
                        number_format($order->delivery_fee, 2, '.', ''),
                        number_format($order->total, 2, '.', ''),
                        $order->payment_method,
                        $order->payment_status,
                        \App\Models\Order::$statusLabels[$order->status] ?? $order->status,
                        $order->admin_notes,
                        $order->delivery_notes,
                        optional($order->lastEditor)->name,
                        $order->last_edited_at?->format('Y-m-d H:i'),
                    ]);
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
