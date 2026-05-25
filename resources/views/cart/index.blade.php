@extends('layouts.app')

@section('title', __('cart_page_title'))

@section('content')
<div class="page-header" style="background:linear-gradient(135deg,#1B3B8C 0%,#152d6e 100%);padding:40px 0;text-align:center;color:#fff;">
  <div class="container">
    <h1 style="font-size:2rem;font-weight:700;margin:0 0 8px;">🛒 {{ __('cart_title') }}</h1>
    <p style="opacity:.8;margin:0;">{{ count($cart) }} {{ count($cart) === 1 ? __('cart_items_count_one') : __('cart_items_count_many') }} {{ __('cart_items_in_cart') }}</p>
  </div>
</div>

<div class="container" style="padding:40px 0;">

  @if(count($cart))
    <div class="cart-layout">

      {{-- Cart items --}}
      <div class="cart-items">
        @foreach($cart as $key => $item)
          <div class="cart-item">
            <img src="{{ $item['image'] ? asset('storage/'.$item['image']) : asset('images/placeholder.svg') }}"
              alt="{{ $item['name'] }}" style="width:90px;height:90px;object-fit:cover;border-radius:12px;flex-shrink:0;"/>

            <div style="flex:1;min-width:0;">
              <h4 style="margin:0 0 4px;color:#1B3B8C;font-size:15px;font-weight:600;">{{ $item['name'] }}</h4>
              @if($item['variant'])
                <p style="margin:0 0 8px;color:#6B7280;font-size:13px;">{{ $item['variant'] }}</p>
              @endif
              <div style="font-size:16px;font-weight:700;color:#E8711A;">{{ number_format($item['price'], 2) }} ₪</div>
            </div>

            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:12px;">
              {{-- Qty update --}}
              <form action="{{ route('cart.update', $key) }}" method="POST" style="display:flex;align-items:center;gap:0;border:1px solid #E5E7EB;border-radius:8px;overflow:hidden;">
                @csrf
                @method('PATCH')
                <button type="submit" name="qty" value="{{ max(1, $item['qty']-1) }}"
                  style="padding:6px 12px;background:#F3F4F6;border:none;cursor:pointer;font-size:16px;">−</button>
                <span style="padding:6px 14px;font-weight:600;font-size:15px;color:#1B3B8C;background:#fff;">{{ $item['qty'] }}</span>
                <button type="submit" name="qty" value="{{ $item['qty']+1 }}"
                  style="padding:6px 12px;background:#F3F4F6;border:none;cursor:pointer;font-size:16px;">+</button>
              </form>

              <div style="font-weight:700;color:#1B3B8C;font-size:15px;">{{ number_format($item['price'] * $item['qty'], 2) }} ₪</div>

              <form action="{{ route('cart.remove', $key) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" style="background:none;border:none;color:#EF4444;cursor:pointer;font-size:13px;display:flex;align-items:center;gap:4px;">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                  {{ __('cart_remove') }}
                </button>
              </form>
            </div>
          </div>
        @endforeach

        {{-- Continue shopping --}}
        <div style="padding:16px 0;border-top:1px solid #E5E7EB;margin-top:8px;">
          <a href="{{ route('products.index') }}" style="color:#1B3B8C;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:6px;">
            {{ __('cart_continue_shopping') }}
          </a>
        </div>
      </div>

      {{-- Order summary --}}
      <div class="cart-summary">
        <div class="summary-card" style="background:#fff;border-radius:16px;padding:28px;box-shadow:0 4px 20px rgba(27,59,140,.08);position:sticky;top:100px;">
          <h3 style="font-size:1.2rem;font-weight:700;color:#1B3B8C;margin:0 0 24px;">{{ __('cart_summary') }}</h3>

          <div style="display:flex;flex-direction:column;gap:14px;margin-bottom:20px;">
            <div style="display:flex;justify-content:space-between;font-size:15px;">
              <span style="color:#6B7280;">{{ __('subtotal') }}</span>
              <span style="font-weight:600;">{{ number_format($subtotal, 2) }} ₪</span>
            </div>
            @if($discount > 0)
              <div style="display:flex;justify-content:space-between;font-size:15px;">
                <span style="color:#6B7280;">{{ __('discount') }} ({{ $coupon['label'] ?? '' }})</span>
                <span style="font-weight:600;color:#10B981;">— {{ number_format($discount, 2) }} ₪</span>
              </div>
            @endif
            <div style="display:flex;justify-content:space-between;font-size:15px;">
              <span style="color:#6B7280;">{{ __('delivery_fee') }}</span>
              <span style="font-weight:600;color:#10B981;">{{ $delivery > 0 ? number_format($delivery, 2).' ₪' : __('cart_delivery_tbd') }}</span>
            </div>
            <div style="border-top:2px solid #E5E7EB;padding-top:14px;display:flex;justify-content:space-between;font-size:1.1rem;font-weight:700;">
              <span>{{ __('total') }}</span>
              <span style="color:#E8711A;">{{ number_format($total, 2) }} ₪</span>
            </div>
          </div>

          {{-- Coupon code --}}
          @if($coupon)
            <div style="background:#ECFDF5;border:1px solid #6EE7B7;border-radius:10px;padding:12px 16px;margin-bottom:16px;display:flex;justify-content:space-between;align-items:center;">
              <span style="color:#065F46;font-size:14px;font-weight:600;">{{ __('cart_coupon_active') }}: {{ $coupon['code'] }} ({{ $coupon['label'] }})</span>
              <form action="{{ route('cart.coupon.remove') }}" method="POST">
                @csrf
                <button type="submit" style="background:none;border:none;color:#EF4444;cursor:pointer;font-size:12px;">{{ __('cart_coupon_remove') }}</button>
              </form>
            </div>
          @else
            <form action="{{ route('cart.coupon.apply') }}" method="POST" style="margin-bottom:16px;">
              @csrf
              <div style="display:flex;gap:8px;">
                <input type="text" name="code" placeholder="{{ __('cart_coupon_placeholder') }}" required
                  style="flex:1;padding:11px 14px;border:1px solid #E5E7EB;border-radius:10px;font-family:inherit;font-size:14px;"/>
                <button type="submit" class="btn btn-blue" style="padding:11px 16px;white-space:nowrap;">{{ __('cart_apply_coupon') }}</button>
              </div>
            </form>
          @endif

          <a href="{{ route('checkout.index') }}" class="btn btn-orange"
            style="display:block;text-align:center;padding:16px;font-size:16px;font-weight:700;text-decoration:none;border-radius:12px;background:linear-gradient(135deg,#E8711A,#d4610e);color:#fff;box-shadow:0 4px 15px rgba(232,113,26,.3);">
            {{ __('cart_proceed') }}
          </a>

          <div style="display:flex;justify-content:center;gap:16px;margin-top:16px;">
            <span style="font-size:12px;color:#9CA3AF;display:flex;align-items:center;gap:4px;">{{ __('cart_safe_payment') }}</span>
            <span style="font-size:12px;color:#9CA3AF;display:flex;align-items:center;gap:4px;">{{ __('cart_cod_short') }}</span>
          </div>
        </div>
      </div>
    </div>

  @else
    <div style="text-align:center;padding:100px 20px;">
      <div style="font-size:80px;margin-bottom:20px;opacity:.3;">🛒</div>
      <h3 style="color:#1B3B8C;font-size:1.5rem;margin-bottom:12px;">{{ __('cart_empty_title') }}</h3>
      <p style="color:#6B7280;margin-bottom:28px;">{{ __('cart_empty_desc') }}</p>
      <a href="{{ route('products.index') }}" class="btn btn-blue" style="padding:14px 36px;font-size:16px;">{{ __('cart_browse_products') }}</a>
    </div>
  @endif

</div>
@endsection
