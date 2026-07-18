@extends('layouts.app')

@section('title', 'حسابي — أبناء الفريد')

@section('content')
<div class="page-header" style="background:linear-gradient(135deg,#1B3B8C 0%,#152d6e 100%);padding:40px 0;color:#fff;">
  <div class="container">
    <div style="display:flex;align-items:center;gap:20px;">
      <div style="width:64px;height:64px;background:rgba(255,255,255,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:28px;">
        👤
      </div>
      <div>
        <h1 style="font-size:1.6rem;font-weight:700;margin:0 0 4px;">مرحباً، {{ auth()->user()->name }}</h1>
        <p style="opacity:.7;margin:0;font-size:14px;">{{ auth()->user()->email }}</p>
      </div>
    </div>
  </div>
</div>

<div class="container" style="padding:40px 0;">
  <div class="account-layout">

    {{-- Sidebar --}}
    @include('account.partials.sidebar')

    {{-- Main: Dashboard --}}
    <main class="account-main">

      {{-- Stats --}}
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;margin-bottom:32px;">
        <div style="background:#fff;border-radius:16px;padding:24px;text-align:center;box-shadow:0 4px 20px rgba(27,59,140,.08);">
          <div style="font-size:2rem;font-weight:900;color:#1B3B8C;">{{ $ordersCount }}</div>
          <div style="color:#6B7280;font-size:13px;margin-top:4px;">إجمالي الطلبات</div>
        </div>
        <div style="background:#fff;border-radius:16px;padding:24px;text-align:center;box-shadow:0 4px 20px rgba(27,59,140,.08);">
          <div style="font-size:2rem;font-weight:900;color:#E8711A;">{{ number_format($totalSpent, 2) }}</div>
          <div style="color:#6B7280;font-size:13px;margin-top:4px;">إجمالي المشتريات ₪</div>
        </div>
        <div style="background:#fff;border-radius:16px;padding:24px;text-align:center;box-shadow:0 4px 20px rgba(27,59,140,.08);">
          <div style="font-size:2rem;font-weight:900;color:#10B981;">{{ $loyaltyPoints }}</div>
          <div style="color:#6B7280;font-size:13px;margin-top:4px;">نقاط الولاء</div>
        </div>
        <div style="background:#fff;border-radius:16px;padding:24px;text-align:center;box-shadow:0 4px 20px rgba(27,59,140,.08);">
          <div style="font-size:2rem;font-weight:900;color:#8B5CF6;">{{ $wishlistCount }}</div>
          <div style="color:#6B7280;font-size:13px;margin-top:4px;">المفضلة</div>
        </div>
      </div>

      {{-- Recent orders --}}
      <div style="background:#fff;border-radius:16px;padding:28px;box-shadow:0 4px 20px rgba(27,59,140,.08);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
          <h3 style="color:#1B3B8C;font-size:1.1rem;font-weight:700;margin:0;">آخر الطلبات</h3>
          <a href="{{ route('account.orders') }}" style="color:#E8711A;font-size:13px;font-weight:600;text-decoration:none;">عرض الكل ›</a>
        </div>

        @if($recentOrders->count())
          <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:14px;">
              <thead>
                <tr style="border-bottom:2px solid #E5E7EB;">
                  <th style="padding:12px;text-align:start;color:#6B7280;font-weight:600;">رقم الطلب</th>
                  <th style="padding:12px;text-align:start;color:#6B7280;font-weight:600;">التاريخ</th>
                  <th style="padding:12px;text-align:start;color:#6B7280;font-weight:600;">المبلغ</th>
                  <th style="padding:12px;text-align:start;color:#6B7280;font-weight:600;">الحالة</th>
                  <th style="padding:12px;text-align:start;color:#6B7280;font-weight:600;"></th>
                </tr>
              </thead>
              <tbody>
                @foreach($recentOrders as $order)
                  @php
                    $colors = ['pending'=>'#FEF3C7|#D97706','confirmed'=>'#DBEAFE|#1D4ED8','processing'=>'#E0E7FF|#7C3AED','sent_to_delivery'=>'#DBEAFE|#1B3B8C','shipped'=>'#D1FAE5|#059669','delivered'=>'#D1FAE5|#059669','cancelled'=>'#FEE2E2|#DC2626'];
                    [$bg,$tc] = explode('|', $colors[$order->status] ?? '#F3F4F6|#374151');
                  @endphp
                  <tr style="border-bottom:1px solid #F3F4F6;">
                    <td style="padding:12px;font-weight:600;color:#1B3B8C;">{{ $order->order_number }}</td>
                    <td style="padding:12px;color:#6B7280;">{{ $order->created_at->format('d/m/Y') }}</td>
                    <td style="padding:12px;font-weight:700;color:#E8711A;">{{ number_format($order->total, 2) }} ₪</td>
                    <td style="padding:12px;">
                      <span style="background:{{ $bg }};color:{{ $tc }};padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600;white-space:nowrap;">
                        {{ \App\Models\Order::$statusLabels[$order->status] ?? $order->status }}
                      </span>
                    </td>
                    <td style="padding:12px;">
                      <a href="{{ route('checkout.tracking', $order->order_number) }}" style="color:#1B3B8C;font-size:12px;text-decoration:none;">تتبع</a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div style="text-align:center;padding:40px;color:#9CA3AF;">
            <div style="font-size:48px;margin-bottom:12px;">📦</div>
            <p>لا توجد طلبات بعد</p>
            <a href="{{ route('products.index') }}" class="btn btn-blue" style="padding:10px 24px;display:inline-block;margin-top:12px;">تصفح المنتجات</a>
          </div>
        @endif
      </div>

    </main>
  </div>
</div>
@endsection
