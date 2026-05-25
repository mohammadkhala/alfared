@extends('layouts.app')

@section('title', __('success_page_title'))

@section('content')
<div class="container" style="padding:60px 0;max-width:700px;margin:0 auto;">

  {{-- Success card --}}
  <div style="background:#fff;border-radius:24px;padding:48px;text-align:center;box-shadow:0 8px 40px rgba(27,59,140,.12);">

    <div style="width:80px;height:80px;background:linear-gradient(135deg,#10B981,#059669);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;box-shadow:0 8px 24px rgba(16,185,129,.3);">
      <svg viewBox="0 0 24 24" fill="none" stroke="white" width="40" height="40"><polyline points="20 6 9 17 4 12"/></svg>
    </div>

    <h1 style="font-size:1.8rem;font-weight:700;color:#1B3B8C;margin:0 0 12px;">{{ __('success_title') }}</h1>
    <p style="color:#6B7280;margin:0 0 8px;font-size:15px;">{{ __('success_thanks') }}</p>
    <p style="color:#6B7280;margin:0 0 28px;font-size:15px;">{{ __('success_will_contact') }}</p>

    {{-- Order number badge --}}
    <div style="background:linear-gradient(135deg,#EFF6FF,#DBEAFE);border:2px solid #BFDBFE;border-radius:16px;padding:20px;margin-bottom:32px;">
      <p style="color:#6B7280;font-size:13px;margin:0 0 4px;">{{ __('success_order_number') }}</p>
      <div style="font-size:1.8rem;font-weight:900;color:#1B3B8C;letter-spacing:2px;">{{ $order->order_number }}</div>
      <p style="color:#9CA3AF;font-size:12px;margin:8px 0 0;">{{ __('success_keep_number') }}</p>
    </div>

    {{-- Order details --}}
    <div style="background:#F9FAFB;border-radius:16px;padding:24px;margin-bottom:32px;text-align:start;">
      <h3 style="color:#1B3B8C;font-size:1rem;font-weight:700;margin:0 0 16px;">{{ __('success_order_details') }}</h3>

      <div style="display:grid;gap:10px;">
        <div style="display:flex;justify-content:space-between;font-size:14px;">
          <span style="color:#6B7280;">{{ __('success_total_amount') }}</span>
          <span style="font-weight:700;color:#E8711A;font-size:16px;">{{ number_format($order->total, 2) }} ₪</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:14px;">
          <span style="color:#6B7280;">{{ __('success_payment_method') }}</span>
          <span style="font-weight:600;">{{ __('payment_cod') }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:14px;">
          <span style="color:#6B7280;">{{ __('address') }}</span>
          <span style="font-weight:600;text-align:end;">{{ $order->city }}، {{ $order->address_line }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:14px;">
          <span style="color:#6B7280;">{{ __('success_status') }}</span>
          <span style="background:#FEF3C7;color:#D97706;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">{{ __('success_status_pending') }}</span>
        </div>
      </div>

      {{-- Items summary --}}
      <div style="border-top:1px solid #E5E7EB;margin-top:16px;padding-top:16px;">
        @foreach($order->items as $item)
          <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:8px;">
            <span style="color:#4B5563;">{{ $item->product_name }} @if($item->variant_info)<small style="color:#9CA3AF;">({{ $item->variant_info }})</small>@endif × {{ $item->quantity }}</span>
            <span style="font-weight:600;">{{ number_format($item->total, 2) }} ₪</span>
          </div>
        @endforeach
      </div>
    </div>

    {{-- Action buttons --}}
    <div style="display:flex;gap:12px;flex-wrap:wrap;justify-content:center;">
      <a href="{{ route('checkout.tracking', $order->order_number) }}"
        class="btn btn-blue" style="padding:14px 24px;text-decoration:none;border-radius:12px;">
        {{ __('success_track_order') }}
      </a>
      <a href="https://wa.me/970598191312?text={{ urlencode(__('success_wa_message').$order->order_number) }}"
        target="_blank"
        style="padding:14px 24px;background:#25D366;color:#fff;border-radius:12px;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:8px;">
        {{ __('success_whatsapp_contact') }}
      </a>
      <a href="{{ route('home') }}" style="padding:14px 24px;background:#F3F4F6;color:#374151;border-radius:12px;text-decoration:none;font-weight:600;">
        {{ __('success_back_home') }}
      </a>
    </div>

    <p style="color:#9CA3AF;font-size:12px;margin-top:24px;line-height:1.6;">
      📞 +970 598 191 312<br/>
      📧 faredahmad615@gmail.com
    </p>
  </div>

</div>
@endsection
