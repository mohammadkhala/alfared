@extends('layouts.legal')
@section('title', 'سياسة الخصوصية')
@section('icon', '🔒')
@section('description', 'سياسة خصوصية شركة أبناء الفريد')

@section('page-content')

<div class="ls">
  <div class="ls-head"><span class="ls-head-icon">📋</span><h2>مقدمة</h2></div>
  <div class="ls-body">
    <p>تلتزم شركة <strong>أبناء الفريد</strong> بحماية خصوصية مستخدمي تطبيقها وموقعها الإلكتروني. تصف هذه السياسة كيفية جمع معلوماتك الشخصية واستخدامها وحمايتها عند استخدامك لخدماتنا.</p>
    <p>باستخدامك للتطبيق أو الموقع فإنك توافق على الشروط الواردة في هذه السياسة.</p>
  </div>
</div>

<div class="ls">
  <div class="ls-head"><span class="ls-head-icon">📊</span><h2>البيانات التي نجمعها</h2></div>
  <div class="ls-body">
    <ul>
      <li><strong>بيانات التسجيل:</strong> الاسم، رقم الهاتف، البريد الإلكتروني عند إنشاء الحساب</li>
      <li><strong>بيانات الطلبات:</strong> عناوين التوصيل، تفاصيل المنتجات المطلوبة، تاريخ الطلبات</li>
      <li><strong>بيانات الاستخدام:</strong> الصفحات التي تزورها، المنتجات التي تتصفحها، مدة الجلسة</li>
      <li><strong>بيانات الجهاز:</strong> نوع الجهاز، نظام التشغيل، معرّف الجهاز لأغراض الإشعارات</li>
      <li><strong>بيانات الموقع:</strong> المنطقة الجغرافية لحساب رسوم التوصيل (بموافقتك فقط)</li>
    </ul>
    <div class="ls-highlight">
      <strong>ملاحظة:</strong> لا نجمع بيانات بطاقات الائتمان أو الحسابات البنكية. تتم عمليات الدفع بالكاش عند الاستلام أو عبر وسائل دفع آمنة خارجية.
    </div>
  </div>
</div>

<div class="ls">
  <div class="ls-head"><span class="ls-head-icon">⚙️</span><h2>كيف نستخدم بياناتك</h2></div>
  <div class="ls-body">
    <ul>
      <li>معالجة طلباتك وتتبّع حالة التوصيل</li>
      <li>إرسال إشعارات حول حالة طلبك (اختياري)</li>
      <li>تحسين تجربة التسوق وتخصيص العروض</li>
      <li>التواصل معك للرد على استفساراتك وشكاواك</li>
      <li>احتساب نقاط الولاء والمكافآت</li>
      <li>تحليل أداء الموقع والتطبيق لتطويرهما</li>
    </ul>
  </div>
</div>

<div class="ls">
  <div class="ls-head"><span class="ls-head-icon">🤝</span><h2>مشاركة البيانات مع أطراف ثالثة</h2></div>
  <div class="ls-body">
    <p><strong>لا نبيع بياناتك لأي طرف ثالث.</strong> قد نشارك بعض البيانات فقط في:</p>
    <ul>
      <li>مزودو خدمة التوصيل لتنفيذ طلباتك</li>
      <li>خدمة Firebase (Google) لإرسال الإشعارات الفورية</li>
      <li>إذا طلبت السلطات القانونية ذلك وفق القانون الفلسطيني</li>
    </ul>
  </div>
</div>

<div class="ls">
  <div class="ls-head"><span class="ls-head-icon">🔔</span><h2>الإشعارات الفورية</h2></div>
  <div class="ls-body">
    <p>يستخدم تطبيقنا خدمة <strong>Firebase Cloud Messaging</strong> من Google لإرسال إشعارات حول حالة طلباتك والعروض الخاصة.</p>
    <p>يمكنك إيقاف تشغيل الإشعارات في أي وقت من إعدادات هاتفك أو من داخل التطبيق دون أي تأثير على خدمة التسوق.</p>
  </div>
</div>

<div class="ls">
  <div class="ls-head"><span class="ls-head-icon">🗄️</span><h2>الاحتفاظ بالبيانات وحذفها</h2></div>
  <div class="ls-body">
    <ul>
      <li>بيانات الطلبات تُحفظ لمدة 5 سنوات لأغراض ضريبية ومحاسبية</li>
      <li>بيانات الحساب تُحذف خلال 30 يوماً من طلب الحذف</li>
      <li>السجلات التقنية تُحذف خلال 90 يوماً</li>
    </ul>
    <div class="ls-highlight">
      لحذف حسابك وجميع بياناتك: <strong>التطبيق ← حسابي ← حذف الحساب</strong> أو تواصل معنا مباشرة.
    </div>
  </div>
</div>

<div class="ls">
  <div class="ls-head"><span class="ls-head-icon">⚖️</span><h2>حقوقك</h2></div>
  <div class="ls-body">
    <ul>
      <li>الاطلاع على البيانات التي نحتفظ بها عنك</li>
      <li>تصحيح أي بيانات غير دقيقة</li>
      <li>طلب حذف حسابك وبياناتك الشخصية</li>
      <li>إيقاف تلقّي الإشعارات التسويقية</li>
      <li>تقديم شكوى بشأن معالجة بياناتك</li>
    </ul>
  </div>
</div>

<div class="ls">
  <div class="ls-head"><span class="ls-head-icon">🛡️</span><h2>أمان البيانات</h2></div>
  <div class="ls-body">
    <p>نستخدم تشفير HTTPS لجميع الاتصالات. كلمات المرور مشفّرة ولا يمكن لأي موظف الاطلاع عليها.</p>
    <p>نحثّك على استخدام كلمة مرور قوية وعدم مشاركتها مع أي شخص.</p>
  </div>
</div>

<div class="ls">
  <div class="ls-head"><span class="ls-head-icon">💬</span><h2>تواصل معنا</h2></div>
  <div class="ls-body">
    <p>لأي استفسار يتعلق بسياسة الخصوصية:</p>
    <div class="ls-contact-row">
      <a href="https://wa.me/970598191312" target="_blank" class="ls-chip">💬 واتساب</a>
      <a href="mailto:faredahmad615@gmail.com" class="ls-chip">📧 البريد الإلكتروني</a>
      <a href="https://www.facebook.com/alfaredsons" target="_blank" class="ls-chip">📘 فيسبوك</a>
    </div>
  </div>
</div>

@endsection
