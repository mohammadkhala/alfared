<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Services\LahzaService;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LahzaWebhookController extends Controller
{
    public function handle(Request $request, LahzaService $lahza)
    {
        // 1. Verify signature
        $signature = $request->header('x-lahza-signature', '');
        $payload   = $request->getContent();

        if (! $lahza->validateWebhookSignature($payload, $signature)) {
            Log::warning('Lahza webhook: invalid signature');
            return response('Unauthorized', 401);
        }

        $event = $request->input('event');
        $data  = $request->input('data', []);

        Log::info('Lahza webhook received', ['event' => $event, 'ref' => $data['reference'] ?? null]);

        if ($event === 'charge.success') {
            $this->handleChargeSuccess($data);
        }

        return response('OK', 200);
    }

    private function handleChargeSuccess(array $data): void
    {
        $reference = $data['reference'] ?? null;
        if (! $reference) return;

        // Find order by order_number (we pass it as ref)
        $order = Order::where('order_number', $reference)
                      ->orWhere('payment_ref', $reference)
                      ->first();

        if (! $order) {
            Log::warning('Lahza webhook: order not found', ['reference' => $reference]);
            return;
        }

        // Idempotency: already paid
        if ($order->payment_status === 'paid') {
            Log::info('Lahza webhook: order already paid', ['order' => $order->order_number]);
            return;
        }

        // Verify amount matches (amount comes in smallest unit)
        $paidAmount  = ($data['amount'] ?? 0) / 100;
        $orderAmount = (float) $order->total;

        if (abs($paidAmount - $orderAmount) > 0.5) {
            Log::error('Lahza webhook: amount mismatch', [
                'order'  => $order->order_number,
                'paid'   => $paidAmount,
                'expected' => $orderAmount,
            ]);
            return;
        }

        // Update order
        $order->update([
            'payment_status' => 'paid',
            'payment_ref'    => $reference,
            'status'         => 'confirmed',
            'confirmed_at'   => now(),
        ]);

        Log::info('Lahza webhook: order confirmed', ['order' => $order->order_number]);

        // Notify admins
        $recipients = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_STAFF])->get()
            ->filter(fn ($u) => $u->hasPermission('manage_orders'));

        if ($recipients->isNotEmpty()) {
            FilamentNotification::make()
                ->title('💳 دفع مكتمل — ' . $order->order_number)
                ->body($order->customer_name . ' • ' . number_format($order->total, 2) . ' ₪ — تم الدفع عبر لحظة')
                ->icon('heroicon-o-credit-card')
                ->iconColor('success')
                ->actions([
                    NotificationAction::make('view')
                        ->label('عرض الطلب')
                        ->url(url('/admin/orders/' . $order->id))
                        ->markAsRead(),
                ])
                ->sendToDatabase($recipients);
        }
    }
}
