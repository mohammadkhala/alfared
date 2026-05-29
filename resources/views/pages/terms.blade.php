@extends('layouts.legal')
@section('title', 'شروط الاستخدام')
@section('description', 'شروط وأحكام استخدام تطبيق وموقع شركة أبناء الفريد')

@section('content')

<div class="page-header">
  <span class="page-icon">📜</span>
  <h1>شروط الاستخدام</h1>
  <p>آخر تحديث: يناير 2025 &nbsp;|&nbsp; شركة أبناء الفريد — فلسطين</p>
</div>

{{-- القبول --}}
<div class="section">
  <div class="section-header">
    <span class="section-icon">✍️</span>
    <h2>قبول الشروط</h2>
  </div>
  <div class="section-body">
    <p>
      باستخدامك لتطبيق أو موقع <strong>شركة أبناء الفريد</strong>، فإنك توافق على الالتزام
      بهذه الشروط والأحكام. إذا كنت لا توافق على أي بند من هذه الشروط،
      يرجى التوقف عن استخدام خدماتنا.
    </p>
    <p>
      نحتفظ بالحق في تعديل هذه الشروط في أي وقت، وستُعلَم بأي تغييرات جوهرية
      عبر التطبيق أو البريد الإلكتروني.
    </p>
  </div>
</div>

{{-- استخدام الخدمة --}}
<div class="section">
  <div class="section-header">
    <span class="section-icon">🛒</span>
    <h2>استخدام الخدمة</h2>
  </div>
  <div class="section-body">
    <p>يُسمح لك باستخدام خدماتنا وفق الشروط التالية:</p>
    <ul>
      <li>يجب أن يكون عمرك 16 سنة أو أكثر</li>
      <li>تقديم معلومات صحيحة ودقيقة عند التسجيل</li>
      <li>استخدام الخدمة للأغراض الشخصية المشروعة فقط</li>
      <li>الحفاظ على سرية كلمة المرور وعدم مشاركتها</li>
      <li>إخطارنا فوراً في حال اشتبهت باختراق حسابك</li>
    </ul>
  </div>
</div>

{{-- التسجيل والحساب --}}
<div class="section">
  <div class="section-header">
    <span class="section-icon">👤</span>
    <h2>التسجيل والحساب</h2>
  </div>
  <div class="section-body">
    <ul>
      <li>حساب واحد لكل مستخدم؛ إنشاء حسابات متعددة مخالف للشروط</li>
      <li>أنت مسؤول عن جميع الأنشطة التي تجري على حسابك</li>
      <li>يحق لنا تعليق أو إلغاء حسابك عند انتهاك هذه الشروط</li>
      <li>يمكنك حذف حسابك في أي وقت من إعدادات التطبيق</li>
    </ul>
  </div>
</div>

{{-- الطلبات والأسعار --}}
<div class="section">
  <div class="section-header">
    <span class="section-icon">💳</span>
    <h2>الطلبات والأسعار</h2>
  </div>
  <div class="section-body">
    <ul>
      <li>الأسعار المعروضة بالشيكل الإسرائيلي (₪) وقد تتغير دون إشعار مسبق</li>
      <li>تأكيد الطلب يعني موافقتك على السعر والشروط المعروضة</li>
      <li>نحتفظ بالحق في رفض أي طلب في حالات استثنائية (نفاد المخزون، خطأ في السعر)</li>
      <li>رسوم التوصيل تُحتسب حسب المنطقة وتُعرض قبل تأكيد الطلب</li>
      <li>الطلب المؤكد ملزم ولا يمكن إلغاؤه بعد الشحن</li>
    </ul>
    <div class="highlight-box">
      <strong>نقاط الولاء:</strong> لا يمكن استبدالها بالمال النقدي ولها صلاحية محددة.
      راجع شروط برنامج الولاء للتفاصيل.
    </div>
  </div>
</div>

{{-- المحتوى المحظور --}}
<div class="section">
  <div class="section-header">
    <span class="section-icon">🚫</span>
    <h2>الاستخدام المحظور</h2>
  </div>
  <div class="section-body">
    <p>يُحظر تماماً:</p>
    <ul>
      <li>استخدام الخدمة لأغراض غير قانونية أو مخالفة للنظام العام</li>
      <li>محاولة اختراق أو تعطيل الموقع أو التطبيق</li>
      <li>نشر محتوى مسيء أو كاذب في تقييمات المنتجات</li>
      <li>إساءة استخدام نظام الكوبونات أو نقاط الولاء بطرق احتيالية</li>
      <li>جمع أو استخراج بيانات مستخدمين آخرين</li>
      <li>انتحال شخصية موظفينا أو التصرف باسم الشركة</li>
    </ul>
  </div>
</div>

{{-- الملكية الفكرية --}}
<div class="section">
  <div class="section-header">
    <span class="section-icon">©️</span>
    <h2>الملكية الفكرية</h2>
  </div>
  <div class="section-body">
    <p>
      جميع محتويات الموقع والتطبيق (الشعار، الصور، النصوص، التصاميم) هي ملك
      حصري لشركة <strong>أبناء الفريد</strong> أو مرخّصة لها.
    </p>
    <ul>
      <li>لا يجوز نسخ أو إعادة نشر المحتوى دون إذن خطي مسبق</li>
      <li>استخدام شعار الشركة أو علامتها التجارية يتطلب موافقة صريحة</li>
    </ul>
  </div>
</div>

{{-- المسؤولية --}}
<div class="section">
  <div class="section-header">
    <span class="section-icon">⚖️</span>
    <h2>حدود المسؤولية</h2>
  </div>
  <div class="section-body">
    <p>
      نبذل قصارى جهدنا لتقديم خدمة موثوقة ومستمرة، غير أننا لا نضمن:
    </p>
    <ul>
      <li>عدم انقطاع الخدمة في أي وقت لأسباب فنية أو صيانة</li>
      <li>خلو الموقع والتطبيق من الأخطاء التقنية في جميع الأوقات</li>
    </ul>
    <p>
      مسؤوليتنا تقتصر على قيمة الطلب المتضرر وفق سياسة الإرجاع المعتمدة.
    </p>
  </div>
</div>

{{-- القانون المطبق --}}
<div class="section">
  <div class="section-header">
    <span class="section-icon">🏛️</span>
    <h2>القانون المطبق</h2>
  </div>
  <div class="section-body">
    <p>
      تخضع هذه الشروط وتُفسَّر وفقاً للقانون الفلسطيني المعمول به.
      أي نزاع ينشأ عن هذه الشروط يُحلّ بالتراضي أولاً،
      وإذا تعذّر ذلك يُحال للجهات المختصة في محافظة الخليل — فلسطين.
    </p>
  </div>
</div>

{{-- تواصل --}}
<div class="section">
  <div class="section-header">
    <span class="section-icon">💬</span>
    <h2>تواصل معنا</h2>
  </div>
  <div class="section-body">
    <p>لأي استفسار حول هذه الشروط:</p>
    <div class="contact-row">
      <a href="https://wa.me/970598191312" target="_blank" class="contact-chip">💬 واتساب</a>
      <a href="mailto:faredahmad615@gmail.com" class="contact-chip">📧 البريد الإلكتروني</a>
    </div>
  </div>
</div>

@endsection
