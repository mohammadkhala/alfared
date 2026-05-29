@extends('layouts.legal')
@section('title', 'الأسئلة الشائعة')
@section('icon', '❓')
@section('description', 'الأسئلة الشائعة عن تطبيق وموقع شركة أبناء الفريد')

@section('page-content')

<style>
.faq-item { cursor: pointer; }
.faq-item .ls-head { justify-content: space-between; }
.faq-item .ls-head h2 { color: var(--text); font-size: 14px; font-weight: 700; }
.faq-arrow { font-size: 12px; color: var(--blue); transition: transform .2s; flex-shrink: 0; }
.faq-item.open .faq-arrow { transform: rotate(180deg); }
.faq-item .ls-body { display: none; }
.faq-item.open .ls-body { display: block; }
</style>

{{-- الطلبات --}}
<div class="ls"><div class="ls-head" style="background:var(--orange);"><span class="ls-head-icon">🛒</span><h2 style="color:white;">الطلبات</h2></div></div>

<div class="ls faq-item" onclick="this.classList.toggle('open')">
  <div class="ls-head"><span class="ls-head-icon">❓</span><h2>كيف أضع طلباً؟</h2><span class="faq-arrow">▼</span></div>
  <div class="ls-body"><p>تصفّح المنتجات وأضف ما تريده للسلة، ثم اضغط "إتمام الطلب" وأدخل عنوانك وأكّد الطلب. سيصلك إشعار بتأكيد الطلب فوراً.</p></div>
</div>

<div class="ls faq-item" onclick="this.classList.toggle('open')">
  <div class="ls-head"><span class="ls-head-icon">❓</span><h2>هل يمكنني تعديل طلبي بعد التأكيد؟</h2><span class="faq-arrow">▼</span></div>
  <div class="ls-body"><p>يمكن تعديل الطلب خلال ساعة من التأكيد فقط. تواصل معنا عبر واتساب في أقرب وقت ممكن.</p></div>
</div>

<div class="ls faq-item" onclick="this.classList.toggle('open')">
  <div class="ls-head"><span class="ls-head-icon">❓</span><h2>ما هي طرق الدفع المتاحة؟</h2><span class="faq-arrow">▼</span></div>
  <div class="ls-body"><p>الدفع عند الاستلام (كاش) هو الطريقة الأساسية حالياً. سنضيف طرق دفع إلكترونية قريباً.</p></div>
</div>

<div class="ls faq-item" onclick="this.classList.toggle('open')">
  <div class="ls-head"><span class="ls-head-icon">❓</span><h2>كيف أتابع حالة طلبي؟</h2><span class="faq-arrow">▼</span></div>
  <div class="ls-body"><p>من التطبيق اضغط على "طلباتي" لترى حالة كل طلب. ستصلك أيضاً إشعارات تلقائية عند كل تحديث.</p></div>
</div>

{{-- التوصيل --}}
<div class="ls" style="margin-top:8px;"><div class="ls-head" style="background:var(--blue);"><span class="ls-head-icon">🚚</span><h2 style="color:white;">التوصيل</h2></div></div>

<div class="ls faq-item" onclick="this.classList.toggle('open')">
  <div class="ls-head"><span class="ls-head-icon">❓</span><h2>كم تستغرق عملية التوصيل؟</h2><span class="faq-arrow">▼</span></div>
  <div class="ls-body"><p>الخليل 1-2 يوم، باقي المحافظات 2-5 أيام. راجع <a href="{{ route('shipping') }}" style="color:var(--orange);">صفحة الشحن والتوصيل</a> للتفاصيل الكاملة.</p></div>
</div>

<div class="ls faq-item" onclick="this.classList.toggle('open')">
  <div class="ls-head"><span class="ls-head-icon">❓</span><h2>هل التوصيل مجاني؟</h2><span class="faq-arrow">▼</span></div>
  <div class="ls-body"><p>التوصيل مجاني للطلبات فوق ₪200 داخل الخليل. تُحسب رسوم التوصيل لباقي المناطق عند إتمام الطلب.</p></div>
</div>

<div class="ls faq-item" onclick="this.classList.toggle('open')">
  <div class="ls-head"><span class="ls-head-icon">❓</span><h2>هل توصّلون لمنطقتي؟</h2><span class="faq-arrow">▼</span></div>
  <div class="ls-body"><p>نوصّل لجميع مناطق فلسطين. إذا لم تجد منطقتك عند الطلب، تواصل معنا مباشرة.</p></div>
</div>

{{-- الإرجاع --}}
<div class="ls" style="margin-top:8px;"><div class="ls-head" style="background:#10B981;"><span class="ls-head-icon">🔄</span><h2 style="color:white;">الإرجاع والاستبدال</h2></div></div>

<div class="ls faq-item" onclick="this.classList.toggle('open')">
  <div class="ls-head"><span class="ls-head-icon">❓</span><h2>كيف أرجع منتجاً؟</h2><span class="faq-arrow">▼</span></div>
  <div class="ls-body"><p>تواصل معنا خلال 7 أيام من الاستلام عبر واتساب مع صور للمنتج. راجع <a href="{{ route('returns') }}" style="color:var(--orange);">سياسة الإرجاع الكاملة</a>.</p></div>
</div>

<div class="ls faq-item" onclick="this.classList.toggle('open')">
  <div class="ls-head"><span class="ls-head-icon">❓</span><h2>وصلني منتج تالف، ماذا أفعل؟</h2><span class="faq-arrow">▼</span></div>
  <div class="ls-body"><p>التقط صوراً للمنتج فوراً وتواصل معنا خلال 24 ساعة. سنتحمل كامل تكلفة الاستبدال.</p></div>
</div>

{{-- الحساب --}}
<div class="ls" style="margin-top:8px;"><div class="ls-head" style="background:#6366F1;"><span class="ls-head-icon">👤</span><h2 style="color:white;">الحساب ونقاط الولاء</h2></div></div>

<div class="ls faq-item" onclick="this.classList.toggle('open')">
  <div class="ls-head"><span class="ls-head-icon">❓</span><h2>كيف تعمل نقاط الولاء؟</h2><span class="faq-arrow">▼</span></div>
  <div class="ls-body"><p>تكسب نقاطاً مع كل طلب تنجزه. يمكنك استبدال النقاط بخصومات على طلباتك القادمة من قسم "نقاطي" في التطبيق.</p></div>
</div>

<div class="ls faq-item" onclick="this.classList.toggle('open')">
  <div class="ls-head"><span class="ls-head-icon">❓</span><h2>كيف أحذف حسابي؟</h2><span class="faq-arrow">▼</span></div>
  <div class="ls-body"><p>من التطبيق: اذهب إلى <strong>حسابي ← إعدادات ← حذف الحساب</strong>. ستُحذف جميع بياناتك خلال 30 يوماً.</p></div>
</div>

<div class="ls faq-item" onclick="this.classList.toggle('open')">
  <div class="ls-head"><span class="ls-head-icon">❓</span><h2>نسيت كلمة المرور، ماذا أفعل؟</h2><span class="faq-arrow">▼</span></div>
  <div class="ls-body"><p>تواصل معنا عبر واتساب برقم هاتفك المسجّل وسنساعدك في استعادة حسابك.</p></div>
</div>

{{-- تواصل --}}
<div class="ls" style="margin-top:8px;">
  <div class="ls-head"><span class="ls-head-icon">💬</span><h2>لم تجد إجابة سؤالك؟</h2></div>
  <div class="ls-body">
    <p>تواصل معنا مباشرة وسنجيبك في أقرب وقت:</p>
    <div class="ls-contact-row">
      <a href="https://wa.me/970598191312" target="_blank" class="ls-chip">💬 واتساب</a>
      <a href="{{ route('contact') }}" class="ls-chip">📝 نموذج التواصل</a>
    </div>
  </div>
</div>

@endsection
