@extends('layouts.legal')
@section('title', 'الشحن والتوصيل')
@section('icon', '🚚')
@section('description', 'سياسة الشحن والتوصيل لشركة أبناء الفريد')

@section('page-content')

<div class="ls">
  <div class="ls-head"><span class="ls-head-icon">📍</span><h2>مناطق التوصيل</h2></div>
  <div class="ls-body">
    <p>نوصّل لجميع مناطق فلسطين بما فيها:</p>
    <ul>
      <li>الخليل وجميع قراها ومدنها</li>
      <li>بيت لحم ومحيطها</li>
      <li>القدس والمناطق المحيطة</li>
      <li>رام الله والبيرة ونابلس</li>
      <li>جنين وطولكرم وقلقيلية</li>
      <li>أريحا وسائر المحافظات الفلسطينية</li>
    </ul>
    <div class="ls-highlight">
      <strong>ملاحظة:</strong> بعض المناطق النائية قد تستغرق وقتاً إضافياً للتوصيل.
    </div>
  </div>
</div>

<div class="ls">
  <div class="ls-head"><span class="ls-head-icon">⏱️</span><h2>مدة التوصيل</h2></div>
  <div class="ls-body">
    <ul>
      <li><strong>الخليل وضواحيها:</strong> 1–2 يوم عمل</li>
      <li><strong>المحافظات القريبة (بيت لحم، القدس):</strong> 2–3 أيام عمل</li>
      <li><strong>باقي المحافظات:</strong> 3–5 أيام عمل</li>
    </ul>
    <div class="ls-highlight">
      تُحسب أيام العمل من الأحد إلى الخميس، ولا تشمل أيام الجمعة والسبت والأعياد الرسمية.
    </div>
  </div>
</div>

<div class="ls">
  <div class="ls-head"><span class="ls-head-icon">💰</span><h2>رسوم التوصيل</h2></div>
  <div class="ls-body">
    <ul>
      <li><strong>الخليل المدينة:</strong> توصيل مجاني للطلبات فوق ₪200</li>
      <li><strong>المناطق الأخرى:</strong> رسوم تُحدَّد عند إتمام الطلب حسب المنطقة</li>
      <li><strong>الطلبات الكبيرة والثقيلة:</strong> قد تُطبَّق رسوم إضافية</li>
    </ul>
  </div>
</div>

<div class="ls">
  <div class="ls-head"><span class="ls-head-icon">📦</span><h2>تتبّع الطلب</h2></div>
  <div class="ls-body">
    <p>بعد تأكيد طلبك ستصلك إشعارات فورية على التطبيق لكل مرحلة:</p>
    <ul>
      <li>✅ تأكيد الطلب</li>
      <li>📦 الطلب قيد التجهيز</li>
      <li>🚚 الطلب في الطريق إليك</li>
      <li>🎉 تم التوصيل</li>
    </ul>
    <p>يمكنك أيضاً متابعة حالة طلبك في أي وقت من <strong>التطبيق ← طلباتي</strong>.</p>
  </div>
</div>

<div class="ls">
  <div class="ls-head"><span class="ls-head-icon">💬</span><h2>استفسارات التوصيل</h2></div>
  <div class="ls-body">
    <p>للاستفسار عن طلبك أو موعد التوصيل:</p>
    <div class="ls-contact-row">
      <a href="https://wa.me/970598191312" target="_blank" class="ls-chip">💬 واتساب</a>
      <a href="tel:+970598191312" class="ls-chip">📞 اتصال مباشر</a>
    </div>
  </div>
</div>

@endsection
