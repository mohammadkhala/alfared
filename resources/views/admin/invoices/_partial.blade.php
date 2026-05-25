{{-- Reusable single-invoice block. Variable: $order, optional: $lang --}}
@php
  $lang = $lang ?? app()->getLocale();
  $nameField = match($lang) {
      'he' => 'name_he',
      'en' => 'name_en',
      default => 'name_ar',
  };
@endphp
<div class="invoice-page">
  <header class="invoice-head">
    <div class="brand">
      <div class="logo">A</div>
      <div>
        <div class="brand-name">{{ __('company_name') }}</div>
        <div class="brand-meta">{{ __('invoice_company_address') }}</div>
      </div>
    </div>
    <div class="meta">
      <div class="meta-title">{{ __('invoice_title') }} #{{ $order->order_number }}</div>
      <div class="meta-date">{{ $order->created_at->format('Y-m-d • H:i') }}</div>
      <div class="meta-status">{{ __('order_status_' . ($order->status ?? 'pending')) }}</div>
    </div>
  </header>

  <section class="info-grid">
    <div class="card">
      <h4>{{ __('invoice_customer') }}</h4>
      <div><strong>{{ $order->customer_name }}</strong></div>
      <div>📞 {{ $order->customer_phone }}</div>
      @if($order->customer_email)<div>✉️ {{ $order->customer_email }}</div>@endif
    </div>
    <div class="card">
      <h4>{{ __('invoice_address') }}</h4>
      <div>{{ $order->city }}@if($order->area), {{ $order->area }}@endif</div>
      <div>{{ $order->address_line }}</div>
      @if($order->building)<div>{{ $order->building }}</div>@endif
      @if($order->deliveryZone)
        <div class="muted">{{ __('invoice_zone') }}: {{ $order->deliveryZone->{$nameField} ?? $order->deliveryZone->name_ar }}</div>
      @endif
    </div>
    <div class="card">
      <h4>{{ __('invoice_payment') }}</h4>
      <div>{{ match($order->payment_method) {
          'cod' => __('invoice_payment_cod'),
          'card' => __('invoice_payment_card'),
          'transfer' => __('invoice_payment_transfer'),
          default => $order->payment_method,
        } }}</div>
      <div class="muted">{{ __('invoice_payment_status') }}: {{ match($order->payment_status ?? 'pending') {
        'paid'    => __('invoice_payment_paid'),
        'failed'  => __('invoice_payment_failed'),
        default   => __('invoice_payment_pending'),
      } }}</div>
    </div>
  </section>

  <table class="items-tbl">
    <thead>
      <tr>
        <th>{{ __('invoice_col_index') }}</th>
        <th>{{ __('invoice_col_product') }}</th>
        <th>{{ __('invoice_col_qty') }}</th>
        <th>{{ __('invoice_col_unit_price') }}</th>
        <th>{{ __('invoice_col_total') }}</th>
      </tr>
    </thead>
    <tbody>
      @foreach($order->items as $i => $item)
        <tr>
          <td>{{ $i+1 }}</td>
          <td>
            <div>{{ $item->product_name }}</div>
            @if($item->variant_info)<div class="muted small">{{ $item->variant_info }}</div>@endif
          </td>
          <td>{{ $item->quantity }}</td>
          <td>{{ number_format($item->price, 2) }} ₪</td>
          <td><strong>{{ number_format($item->total, 2) }} ₪</strong></td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <section class="totals">
    <div></div>
    <table>
      <tr><td>{{ __('invoice_subtotal') }}</td><td>{{ number_format($order->subtotal, 2) }} ₪</td></tr>
      @if($order->discount_amount > 0)
        <tr class="discount"><td>{{ __('invoice_discount') }} @if($order->coupon_code) ({{ $order->coupon_code }})@endif</td>
          <td>− {{ number_format($order->discount_amount, 2) }} ₪</td></tr>
      @endif
      <tr><td>{{ __('invoice_delivery_fee') }}</td><td>{{ $order->delivery_fee > 0 ? number_format($order->delivery_fee, 2).' ₪' : __('invoice_delivery_free') }}</td></tr>
      <tr class="grand"><td>{{ __('invoice_grand_total') }}</td><td>{{ number_format($order->total, 2) }} ₪</td></tr>
    </table>
  </section>

  @if($order->admin_notes || $order->delivery_notes)
    <section class="notes">
      @if($order->delivery_notes)<div><strong>{{ __('invoice_delivery_notes') }}</strong> {{ $order->delivery_notes }}</div>@endif
      @if($order->admin_notes)<div><strong>{{ __('invoice_admin_notes') }}</strong> {{ $order->admin_notes }}</div>@endif
    </section>
  @endif

  <footer class="invoice-foot">
    <div>{{ __('invoice_thanks') }}</div>
    <div class="muted small">{{ __('invoice_printed_at') }} {{ now()->format('Y-m-d H:i') }} {{ __('invoice_printed_by') }} {{ auth()->user()->name ?? '—' }}</div>
  </footer>
</div>
