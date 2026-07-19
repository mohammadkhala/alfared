@php
  $rtl = $lang !== 'en';
  $t = [
    'ar' => [
      'brand' => 'أبناء الفريد', 'title' => 'فاتورة', 'thanks' => 'تمّ استلام طلبك بنجاح — شكراً لثقتك',
      'no' => 'رقم الفاتورة', 'date' => 'التاريخ', 'items' => 'البنود', 'qty' => 'الكمية',
      'subtotal' => 'المجموع الفرعي', 'discount' => 'الخصم', 'loyalty' => 'خصم النقاط',
      'delivery' => 'التوصيل', 'total' => 'الإجمالي المدفوع', 'address' => 'عنوان التوصيل',
      'payment' => 'طريقة الدفع', 'view' => 'عرض الفاتورة أو طباعتها',
      'hint' => 'من الصفحة يمكنك حفظها PDF عبر خيار الطباعة في متصفحك.',
      'cod' => 'نقداً عند الاستلام', 'card' => 'بطاقة', 'footer' => 'لأي استفسار تواصل معنا',
    ],
    'he' => [
      'brand' => 'אבנא אלפריד', 'title' => 'חשבונית', 'thanks' => 'ההזמנה התקבלה — תודה על האמון',
      'no' => 'מספר חשבונית', 'date' => 'תאריך', 'items' => 'פריטים', 'qty' => 'כמות',
      'subtotal' => 'סכום ביניים', 'discount' => 'הנחה', 'loyalty' => 'הנחת נקודות',
      'delivery' => 'משלוח', 'total' => 'סה״כ לתשלום', 'address' => 'כתובת למשלוח',
      'payment' => 'אמצעי תשלום', 'view' => 'הצגה או הדפסה של החשבונית',
      'hint' => 'מהעמוד ניתן לשמור כ־PDF דרך אפשרות ההדפסה בדפדפן.',
      'cod' => 'מזומן במסירה', 'card' => 'כרטיס', 'footer' => 'לכל שאלה צרו קשר',
    ],
    'en' => [
      'brand' => 'Alfared', 'title' => 'Invoice', 'thanks' => 'Your order is complete — thank you',
      'no' => 'Invoice no.', 'date' => 'Date', 'items' => 'Items', 'qty' => 'Qty',
      'subtotal' => 'Subtotal', 'discount' => 'Discount', 'loyalty' => 'Points discount',
      'delivery' => 'Delivery', 'total' => 'Total paid', 'address' => 'Delivery address',
      'payment' => 'Payment method', 'view' => 'View or print invoice',
      'hint' => 'From that page you can save it as PDF using your browser print dialog.',
      'cod' => 'Cash on delivery', 'card' => 'Card', 'footer' => 'Questions? Get in touch',
    ],
  ][$lang] ?? [];
  $money = fn ($v) => number_format((float) $v, 2) . ' ₪';
@endphp
<!DOCTYPE html>
<html lang="{{ $lang }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $t['title'] }} #{{ $order->order_number }}</title>
<style>
  body { font-family: Arial, sans-serif; background:#f4f6fb; margin:0; padding:0; direction: {{ $rtl ? 'rtl' : 'ltr' }}; }
  .wrap { max-width:600px; margin:32px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.08); }
  .header { background:linear-gradient(135deg,#0F2660,#1B3B8C); padding:30px 24px; text-align:center; color:#fff; }
  .header h1 { margin:0; font-size:22px; }
  .header p { margin:8px 0 0; opacity:.85; font-size:14px; }
  .body { padding:26px 24px; }
  .meta { background:#f8fafc; border-radius:10px; padding:14px 16px; margin-bottom:20px; font-size:13px; color:#475569; }
  .meta strong { color:#1B3B8C; }
  .section-title { font-size:14px; font-weight:bold; color:#1B3B8C; margin:22px 0 10px; border-bottom:2px solid #f0f0f0; padding-bottom:6px; }
  table { width:100%; border-collapse:collapse; }
  td { padding:9px 4px; font-size:13px; color:#444; border-bottom:1px solid #f0f0f0; }
  td.num { text-align:{{ $rtl ? 'left' : 'right' }}; font-weight:bold; color:#1B3B8C; white-space:nowrap; }
  .total-row td { font-size:17px; color:#E8711A; border-top:2px solid #f0f0f0; border-bottom:none; padding-top:13px; font-weight:bold; }
  .btn-wrap { text-align:center; margin:26px 0 6px; }
  .btn { display:inline-block; background:#E8711A; color:#fff !important; text-decoration:none; padding:13px 30px; border-radius:10px; font-weight:bold; font-size:15px; }
  .hint { text-align:center; font-size:12px; color:#94a3b8; margin:10px 0 0; }
  .footer { background:#f4f6fb; padding:20px 24px; text-align:center; font-size:12px; color:#999; }
  .footer a { color:#1B3B8C; text-decoration:none; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>🧾 {{ $t['brand'] }}</h1>
    <p>{{ $t['thanks'] }}</p>
  </div>

  <div class="body">
    <div class="meta">
      <div><strong>{{ $t['no'] }}:</strong> #{{ $order->order_number }}</div>
      <div><strong>{{ $t['date'] }}:</strong> {{ ($order->delivered_at ?? $order->created_at)->format('d/m/Y') }}</div>
      <div><strong>{{ $t['payment'] }}:</strong>
        {{ in_array($order->payment_method, ['lahza','card']) ? $t['card'] : $t['cod'] }}
      </div>
    </div>

    <div class="section-title">{{ $t['items'] }}</div>
    <table>
      @foreach($order->items as $item)
        <tr>
          <td>{{ $item->product_name }}<br><span style="color:#94a3b8;font-size:12px;">{{ $t['qty'] }}: {{ $item->quantity }}</span></td>
          <td class="num">{{ $money($item->total) }}</td>
        </tr>
      @endforeach

      <tr><td>{{ $t['subtotal'] }}</td><td class="num">{{ $money($order->subtotal) }}</td></tr>

      @if($order->discount_amount > 0)
        <tr><td>{{ $t['discount'] }}</td><td class="num">− {{ $money($order->discount_amount) }}</td></tr>
      @endif
      @if($order->loyalty_discount > 0)
        <tr><td>{{ $t['loyalty'] }}</td><td class="num">− {{ $money($order->loyalty_discount) }}</td></tr>
      @endif

      <tr><td>{{ $t['delivery'] }}</td><td class="num">{{ $money($order->delivery_fee) }}</td></tr>
      <tr class="total-row"><td>{{ $t['total'] }}</td><td class="num">{{ $money($order->total) }}</td></tr>
    </table>

    <div class="section-title">{{ $t['address'] }}</div>
    <div style="font-size:13px;color:#475569;line-height:1.8;">
      {{ $order->customer_name }}<br>
      {{ $order->deliveryZone?->full_name ?? $order->city }}<br>
      {{ $order->address_line }}{{ $order->building ? ' — ' . $order->building : '' }}<br>
      {{ $order->customer_phone }}
    </div>

    {{-- Links to the printable page instead of attaching a PDF: browsers render
         Arabic correctly, and print-to-PDF gives a better document than any
         library that runs on this host. --}}
    <div class="btn-wrap">
      <a href="{{ route('orders.receipt', ['orderNumber' => $order->order_number, 'lang' => $lang]) }}" class="btn">
        {{ $t['view'] }}
      </a>
    </div>
    <p class="hint">{{ $t['hint'] }}</p>
  </div>

  <div class="footer">
    {{ $t['footer'] }} — <a href="https://wa.me/970598191312">WhatsApp</a>
  </div>
</div>
</body>
</html>
