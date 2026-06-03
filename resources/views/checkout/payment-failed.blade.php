@extends('layouts.app')
@section('title', 'فشل الدفع')
@section('content')

<div style="min-height:60vh;display:flex;align-items:center;justify-content:center;padding:40px 20px;">
  <div style="text-align:center;max-width:480px;width:100%;">
    <div style="font-size:72px;margin-bottom:20px;animation:shake .5s ease;">❌</div>
    <h1 style="font-size:1.8rem;font-weight:900;color:#DC2626;margin-bottom:12px;">فشل عملية الدفع</h1>
    <p style="color:#6B7280;line-height:1.8;margin-bottom:32px;">
      لم يتم إتمام الدفع للطلب <strong>{{ $order->order_number }}</strong>.<br/>
      يمكنك المحاولة مجدداً أو اختيار الدفع عند الاستلام.
    </p>

    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
      <a href="{{ route('checkout.index') }}"
         style="padding:14px 28px;background:#1B3B8C;color:#fff;border-radius:12px;font-weight:700;text-decoration:none;font-size:15px;">
        🔄 حاول مجدداً
      </a>
      <a href="{{ route('home') }}"
         style="padding:14px 28px;background:#F3F4F6;color:#374151;border-radius:12px;font-weight:700;text-decoration:none;font-size:15px;">
        🏠 الرئيسية
      </a>
    </div>

    <p style="margin-top:24px;font-size:13px;color:#9CA3AF;">
      هل تحتاج مساعدة؟
      <a href="https://wa.me/970598191312" target="_blank" style="color:#25D366;font-weight:700;">تواصل معنا عبر واتساب</a>
    </p>
  </div>
</div>

<style>
@keyframes shake {
  0%,100%{transform:translateX(0)}
  25%{transform:translateX(-8px)}
  75%{transform:translateX(8px)}
}
</style>
@endsection
