@extends('layouts.app')

@section('title', __('my_orders_title'))

@php
  $statusLocaleKey = [
    'pending'    => 'order_status_pending',
    'confirmed'  => 'order_status_confirmed',
    'processing' => 'order_status_processing',
    'sent_to_delivery' => 'order_status_sent_to_delivery',
    'shipped'    => 'order_status_shipped',
    'delivered'  => 'order_status_delivered',
    'cancelled'  => 'order_status_cancelled',
    'returned'   => 'order_status_returned',
  ];
@endphp

@section('content')
<div class="page-header" style="background:linear-gradient(135deg,#1B3B8C 0%,#152d6e 100%);padding:32px 0;color:#fff;">
  <div class="container">
    <h1 style="font-size:1.6rem;font-weight:700;margin:0;">{{ __('my_orders_heading') }}</h1>
  </div>
</div>

<div class="container" style="padding:40px 0;">
  <div class="account-layout">
    @include('account.partials.sidebar')
    <main class="account-main">
      <div style="background:#fff;border-radius:16px;padding:28px;box-shadow:0 4px 20px rgba(27,59,140,.08);">
        <h3 style="color:#1B3B8C;font-size:1.1rem;font-weight:700;margin:0 0 20px;">{{ __('my_orders_history') }}</h3>

        @if($orders->count())
          <div style="display:flex;flex-direction:column;gap:16px;">
            @foreach($orders as $order)
              @php
                $colors = ['pending'=>'#FEF3C7|#D97706','confirmed'=>'#DBEAFE|#1D4ED8','processing'=>'#E0E7FF|#7C3AED','sent_to_delivery'=>'#DBEAFE|#1B3B8C','shipped'=>'#D1FAE5|#059669','delivered'=>'#D1FAE5|#059669','cancelled'=>'#FEE2E2|#DC2626'];
                [$bg,$tc] = explode('|', $colors[$order->status] ?? '#F3F4F6|#374151');
              @endphp
              <div style="border:1px solid #E5E7EB;border-radius:14px;padding:20px;transition:box-shadow .2s;" onmouseenter="this.style.boxShadow='0 4px 20px rgba(27,59,140,.12)'" onmouseleave="this.style.boxShadow='none'">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
                  <div>
                    <div style="font-size:15px;font-weight:700;color:#1B3B8C;">{{ $order->order_number }}</div>
                    <div style="font-size:13px;color:#9CA3AF;margin-top:2px;">{{ $order->created_at->format('d/m/Y — H:i') }}</div>
                  </div>
                  <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                    <span style="background:{{ $bg }};color:{{ $tc }};padding:5px 14px;border-radius:20px;font-size:12px;font-weight:600;">
                      {{ __($statusLocaleKey[$order->status] ?? 'order_status_pending') }}
                    </span>
                    <span style="font-size:16px;font-weight:900;color:#E8711A;">{{ number_format($order->total, 2) }} ₪</span>
                  </div>
                </div>

                {{-- Items preview --}}
                <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px;">
                  @foreach($order->items->take(4) as $item)
                    <div style="display:flex;align-items:center;gap:8px;background:#F9FAFB;border-radius:8px;padding:8px 12px;">
                      <img src="{{ $item->product_image ? asset('storage/'.$item->product_image) : asset('images/placeholder.svg') }}"
                        style="width:36px;height:36px;object-fit:cover;border-radius:6px;"/>
                      <span style="font-size:12px;color:#374151;">{{ Str::limit($item->product_name, 25) }} × {{ $item->quantity }}</span>
                    </div>
                  @endforeach
                  @if($order->items->count() > 4)
                    <div style="background:#F9FAFB;border-radius:8px;padding:8px 12px;font-size:12px;color:#6B7280;display:flex;align-items:center;">
                      +{{ $order->items->count()-4 }} {{ __('my_orders_more_items') }}
                    </div>
                  @endif
                </div>

                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                  <a href="{{ route('checkout.tracking', $order->order_number) }}" class="btn btn-blue" style="padding:9px 18px;font-size:13px;text-decoration:none;border-radius:8px;">{{ __('my_orders_track') }}</a>
                  <a href="https://wa.me/970598191312?text={{ urlencode(__('my_orders_inquire_wa').$order->order_number) }}"
                    target="_blank"
                    style="padding:9px 18px;font-size:13px;background:#25D366;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;">
                    {{ __('my_orders_inquire') }}
                  </a>

                  {{-- Only while the order is still ours to stop: once a
                       shipment exists at the courier a parcel is physically
                       moving, and cancelling here would desync the two. --}}
                  @if(\App\Services\OrderCancellation::customerMayCancel($order))
                    <form method="POST" action="{{ route('account.orders.cancel', $order->order_number) }}"
                          onsubmit="return confirm(@js(__('my_orders_cancel_confirm')))"
                          style="display:inline-flex;gap:8px;align-items:center;">
                      @csrf
                      <input type="text" name="reason" maxlength="500"
                             placeholder="{{ __('my_orders_cancel_reason') }}"
                             style="padding:8px 12px;font-size:12px;border:1px solid #E5E7EB;border-radius:8px;width:170px;font-family:inherit;"/>
                      <button type="submit"
                              style="padding:9px 18px;font-size:13px;background:#fff;color:#DC2626;border:1px solid #FCA5A5;border-radius:8px;font-weight:600;cursor:pointer;font-family:inherit;">
                        {{ __('my_orders_cancel') }}
                      </button>
                    </form>
                  @endif
                </div>
              </div>
            @endforeach
          </div>

          <div style="margin-top:24px;">
            {{ $orders->links() }}
          </div>
        @else
          <div style="text-align:center;padding:60px 20px;color:#9CA3AF;">
            <div style="font-size:64px;margin-bottom:16px;">📦</div>
            <h4 style="color:#374151;margin-bottom:8px;">{{ __('my_orders_empty_title') }}</h4>
            <p style="margin-bottom:20px;">{{ __('my_orders_empty_desc') }}</p>
            <a href="{{ route('products.index') }}" class="btn btn-blue" style="padding:12px 28px;text-decoration:none;border-radius:10px;">{{ __('my_orders_browse') }}</a>
          </div>
        @endif
      </div>
    </main>
  </div>
</div>
@endsection
