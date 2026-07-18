@extends('layouts.app')

@section('title', __('tracking_page_prefix') . ' ' . $order->order_number)

@section('content')
<div class="container" style="padding:40px 0;max-width:800px;margin:0 auto;">

  <h1 style="font-size:1.8rem;font-weight:700;color:#1B3B8C;margin:0 0 8px;text-align:center;">{{ __('tracking_heading') }}</h1>
  <p style="text-align:center;color:#6B7280;margin:0 0 32px;">{{ __('tracking_order_number') }}: <strong>{{ $order->order_number }}</strong></p>

  {{-- Status timeline --}}
  @php
    // sent_to_delivery has to be here: it's the state an order sits in from the
    // moment it's handed to RoadFN until a driver picks it up. Leaving it out
    // made array_search return false, so the whole timeline went blank for the
    // customer exactly when they were most likely to check it.
    $statuses = ['pending','confirmed','processing','sent_to_delivery','shipped','delivered'];
    $statusLabels = [
      'pending'          => __('tracking_status_pending'),
      'confirmed'        => __('tracking_status_confirmed'),
      'processing'       => __('tracking_status_processing'),
      'sent_to_delivery' => __('tracking_status_sent_to_delivery'),
      'shipped'          => __('tracking_status_shipped'),
      'delivered'        => __('tracking_status_delivered'),
    ];
    $statusIcons = ['pending'=>'🕐','confirmed'=>'✓','processing'=>'📦','sent_to_delivery'=>'📤','shipped'=>'🚚','delivered'=>'🏠'];
    $currentIndex = array_search($order->status, $statuses);

    // cancelled/returned aren't points on this line. Keep the bar filled to the
    // last step the order actually reached instead of blanking it out.
    if($currentIndex === false) {
      $currentIndex = in_array($order->status, ['cancelled','returned'], true)
        ? array_search('processing', $statuses)
        : -1;
    }
  @endphp

  <div style="background:#fff;border-radius:20px;padding:36px;box-shadow:0 4px 20px rgba(27,59,140,.08);margin-bottom:24px;">
    <div class="tracking-timeline">
      @foreach($statuses as $i => $status)
        @php
          $isDone = $currentIndex >= $i;
          $isCurrent = $currentIndex === $i;
          $isCancelled = $order->status === 'cancelled';
        @endphp
        <div class="timeline-step {{ $isDone && !$isCancelled ? 'done' : '' }} {{ $isCurrent && !$isCancelled ? 'current' : '' }}">
          <div class="timeline-icon" style="width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;margin:0 auto 8px;
            background:{{ ($isDone && !$isCancelled) ? 'linear-gradient(135deg,#1B3B8C,#2d4fb8)' : '#F3F4F6' }};
            border:3px solid {{ $isCurrent && !$isCancelled ? '#E8711A' : ($isDone && !$isCancelled ? '#1B3B8C' : '#E5E7EB') }};
            color:{{ ($isDone && !$isCancelled) ? '#fff' : '#9CA3AF' }};
            box-shadow:{{ $isCurrent && !$isCancelled ? '0 0 0 6px rgba(232,113,26,.15)' : 'none' }};">
            {{ $statusIcons[$status] }}
          </div>
          <div style="font-size:12px;font-weight:{{ $isCurrent ? '700' : '500' }};color:{{ ($isDone && !$isCancelled) ? '#1B3B8C' : '#9CA3AF' }};text-align:center;">
            {{ $statusLabels[$status] }}
          </div>
        </div>
        @if($i < count($statuses)-1)
          <div style="flex:1;height:3px;background:{{ ($currentIndex > $i && !$isCancelled) ? 'linear-gradient(90deg,#1B3B8C,#2d4fb8)' : '#E5E7EB' }};margin-top:22px;border-radius:2px;"></div>
        @endif
      @endforeach
    </div>

    @if($order->status === 'cancelled')
      <div style="background:#FEE2E2;border:1px solid #FECACA;border-radius:12px;padding:16px;text-align:center;margin-top:24px;">
        <span style="color:#DC2626;font-weight:600;">{{ __('tracking_cancelled_msg') }}</span>
      </div>
    @elseif($order->status === 'delivered')
      <div style="background:#ECFDF5;border:1px solid #6EE7B7;border-radius:12px;padding:16px;text-align:center;margin-top:24px;">
        <span style="color:#059669;font-weight:600;">{{ __('tracking_delivered_msg') }}</span>
      </div>
    @elseif($order->status === 'sent_to_delivery')
      <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:12px;padding:16px;text-align:center;margin-top:24px;">
        <span style="color:#1B3B8C;font-weight:600;">{{ __('tracking_sent_to_delivery_msg') }}</span>
      </div>
    @elseif($order->status === 'shipped')
      <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:12px;padding:16px;text-align:center;margin-top:24px;">
        <span style="color:#1B3B8C;font-weight:600;">{{ __('tracking_shipped_msg') }}</span>
      </div>
    @endif
  </div>

  {{-- Courier (RoadFN) card — shown once the shipment has been dispatched --}}
  @if($order->roadfn_tracking_number)
    <div style="background:#fff;border-radius:16px;padding:20px 24px;box-shadow:0 4px 20px rgba(27,59,140,.08);margin-bottom:24px;display:flex;align-items:center;gap:16px;">
      <div style="width:52px;height:52px;border-radius:14px;background:#EFF6FF;display:flex;align-items:center;justify-content:center;font-size:26px;flex-shrink:0;">🚚</div>
      <div style="flex:1;min-width:0;">
        <div style="font-size:15px;font-weight:700;color:#1B3B8C;">{{ __('tracking_courier_title') }}</div>
        <div style="font-size:13px;color:#6B7280;margin-top:3px;">{{ __('tracking_number_label') }}: <strong style="color:#374151;">{{ $order->roadfn_tracking_number }}</strong></div>
        @if($order->roadfn_status)
          <div style="font-size:13px;color:#1B3B8C;margin-top:2px;font-weight:600;">{{ $order->roadfn_status }}</div>
        @endif
      </div>
    </div>
  @endif

  {{-- Order info cards --}}
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
    <div style="background:#fff;border-radius:16px;padding:24px;box-shadow:0 4px 20px rgba(27,59,140,.08);">
      <h4 style="color:#1B3B8C;margin:0 0 16px;font-size:1rem;">{{ __('tracking_order_info') }}</h4>
      <div style="font-size:14px;display:grid;gap:8px;">
        <div style="display:flex;justify-content:space-between;">
          <span style="color:#6B7280;">{{ __('tracking_order_number') }}</span>
          <span style="font-weight:600;">{{ $order->order_number }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;">
          <span style="color:#6B7280;">{{ __('tracking_order_date') }}</span>
          <span style="font-weight:600;">{{ $order->created_at->format('d/m/Y') }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;">
          <span style="color:#6B7280;">{{ __('total') }}</span>
          <span style="font-weight:700;color:#E8711A;">{{ number_format($order->total, 2) }} ₪</span>
        </div>
      </div>
    </div>
    <div style="background:#fff;border-radius:16px;padding:24px;box-shadow:0 4px 20px rgba(27,59,140,.08);">
      <h4 style="color:#1B3B8C;margin:0 0 16px;font-size:1rem;">{{ __('tracking_delivery_address') }}</h4>
      <div style="font-size:14px;color:#4B5563;line-height:1.7;">
        <strong>{{ $order->customer_name }}</strong><br/>
        {{ $order->city }}، {{ $order->address_line }}<br/>
        @if($order->area){{ $order->area }}<br/>@endif
        📞 {{ $order->customer_phone }}
      </div>
    </div>
  </div>

  {{-- Items --}}
  <div style="background:#fff;border-radius:16px;padding:24px;box-shadow:0 4px 20px rgba(27,59,140,.08);margin-bottom:24px;">
    <h4 style="color:#1B3B8C;margin:0 0 20px;font-size:1rem;">{{ __('tracking_items') }}</h4>
    @foreach($order->items as $item)
      <div style="display:flex;gap:16px;align-items:center;padding:12px 0;{{ !$loop->last ? 'border-bottom:1px solid #F3F4F6;' : '' }}">
        <img src="{{ $item->product_image ? asset('storage/'.$item->product_image) : asset('images/placeholder.svg') }}"
          style="width:56px;height:56px;object-fit:cover;border-radius:10px;flex-shrink:0;"/>
        <div style="flex:1;min-width:0;">
          <div style="font-size:14px;font-weight:600;color:#374151;">{{ $item->product_name }}</div>
          @if($item->variant_info)<div style="font-size:12px;color:#9CA3AF;">{{ $item->variant_info }}</div>@endif
          <div style="font-size:13px;color:#6B7280;">{{ number_format($item->price, 2) }} ₪ × {{ $item->quantity }}</div>
        </div>
        <div style="font-size:14px;font-weight:700;color:#E8711A;">{{ number_format($item->total, 2) }} ₪</div>
      </div>
    @endforeach
  </div>

  {{-- CTA --}}
  <div style="text-align:center;display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
    <a href="https://wa.me/970598191312?text={{ urlencode(__('tracking_wa_message').$order->order_number) }}"
      target="_blank"
      style="padding:14px 24px;background:#25D366;color:#fff;border-radius:12px;text-decoration:none;font-weight:600;">
      {{ __('tracking_wa_btn') }}
    </a>
    <a href="{{ route('home') }}" class="btn btn-blue" style="padding:14px 24px;text-decoration:none;border-radius:12px;">
      {{ __('tracking_continue_shopping') }}
    </a>
  </div>

</div>

@push('styles')
<style>
.tracking-timeline {
  display: flex;
  align-items: flex-start;
}
</style>
@endpush
@endsection
