<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تأكيد الطلب</title>
<style>
  body { font-family: Arial, sans-serif; background: #f4f6fb; margin: 0; padding: 0; direction: rtl; }
  .wrap { max-width: 600px; margin: 32px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
  .header { background: linear-gradient(135deg, #0F2660, #1B3B8C); padding: 32px 24px; text-align: center; color: #fff; }
  .header h1 { margin: 0; font-size: 22px; }
  .header p { margin: 8px 0 0; opacity: 0.8; font-size: 14px; }
  .body { padding: 28px 24px; }
  .badge { display: inline-block; background: #E8711A; color: #fff; border-radius: 8px; padding: 4px 14px; font-size: 14px; font-weight: bold; margin-bottom: 16px; }
  .section-title { font-size: 14px; font-weight: bold; color: #1B3B8C; margin: 20px 0 10px; border-bottom: 2px solid #f0f0f0; padding-bottom: 6px; }
  table { width: 100%; border-collapse: collapse; }
  td { padding: 8px 4px; font-size: 13px; color: #444; border-bottom: 1px solid #f0f0f0; }
  td:last-child { text-align: left; font-weight: bold; color: #1B3B8C; }
  .total-row td { font-size: 16px; color: #E8711A; border-top: 2px solid #f0f0f0; padding-top: 12px; }
  .item-row td:first-child { color: #333; }
  .footer { background: #f4f6fb; padding: 20px 24px; text-align: center; font-size: 12px; color: #999; }
  .footer a { color: #1B3B8C; text-decoration: none; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>🛍️ أبناء الفريد</h1>
    <p>شكراً لطلبك! سنتواصل معك قريباً</p>
  </div>
  <div class="body">
    <div class="badge">✓ تم استلام طلبك</div>
    <p style="font-size:15px; color:#333;">مرحباً <strong>{{ $order->customer_name }}</strong>،<br>
    تم تأكيد طلبك رقم <strong>#{{ $order->order_number }}</strong> بنجاح.</p>

    <div class="section-title">📦 المنتجات</div>
    <table>
      @foreach($order->items as $item)
      <tr class="item-row">
        <td>{{ $item->product_name }} × {{ $item->quantity }}</td>
        <td>{{ number_format($item->total, 2) }} ₪</td>
      </tr>
      @endforeach
    </table>

    <div class="section-title">💰 ملخص الطلب</div>
    <table>
      <tr><td>المجموع الفرعي</td><td>{{ number_format($order->subtotal, 2) }} ₪</td></tr>
      @if($order->discount_amount > 0)
      <tr><td>الخصم</td><td>− {{ number_format($order->discount_amount, 2) }} ₪</td></tr>
      @endif
      @if($order->loyalty_discount > 0)
      <tr><td>نقاط الولاء</td><td>− {{ number_format($order->loyalty_discount, 2) }} ₪</td></tr>
      @endif
      <tr><td>رسوم التوصيل</td><td>{{ $order->delivery_fee > 0 ? number_format($order->delivery_fee, 2).' ₪' : 'مجاني 🎉' }}</td></tr>
      <tr class="total-row"><td>الإجمالي</td><td>{{ number_format($order->total, 2) }} ₪</td></tr>
    </table>

    <div class="section-title">📍 عنوان التوصيل</div>
    <p style="font-size:13px; color:#555; margin:0;">
      {{ $order->city }}{{ $order->area ? '، '.$order->area : '' }}<br>
      {{ $order->address_line }}{{ $order->building ? '، '.$order->building : '' }}<br>
      <strong>هاتف:</strong> {{ $order->customer_phone }}
    </p>
  </div>
  <div class="footer">
    <p>للاستفسار تواصل معنا عبر واتساب أو تواصل مع المتجر.<br>
    <strong>أبناء الفريد للتسوق الإلكتروني</strong></p>
  </div>
</div>
</body>
</html>
