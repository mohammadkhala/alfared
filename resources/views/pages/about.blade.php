@extends('layouts.app')
@section('title', 'من نحن — ' . __('company_name'))
@section('description', 'تعرّف على شركة ابناء الفريد التجارية — قصتنا، قيمنا، ومنتجاتنا')

@push('styles')
<style>
/* ── Hero ── */
.about-hero {
  background: linear-gradient(135deg, var(--blue-dk) 0%, var(--blue) 55%, #2448A8 100%);
  padding: 72px 24px 100px;
  text-align: center;
  position: relative;
  overflow: hidden;
}
.about-hero-bg { display:none; }
.about-hero::before {
  content:'';position:absolute;width:380px;height:380px;border-radius:50%;
  background:rgba(232,113,26,0.10);top:-140px;right:-100px;z-index:1;
}
.about-hero::after {
  content:'';position:absolute;width:240px;height:240px;border-radius:50%;
  background:rgba(255,255,255,0.04);bottom:-80px;left:-60px;z-index:1;
}
.about-hero .pill,
.about-hero h1,
.about-hero p { position:relative;z-index:2; }
.about-hero h1 { font-size:38px;font-weight:900;color:white;margin-bottom:14px; }
.about-hero p  { font-size:16px;color:rgba(255,255,255,0.65);max-width:560px;margin:0 auto;line-height:1.8; }
.about-hero .pill {
  display:inline-block;padding:5px 18px;
  background:rgba(232,113,26,0.2);border:1px solid rgba(232,113,26,0.4);
  border-radius:30px;color:#FFA76B;font-size:11px;font-weight:800;
  letter-spacing:1px;margin-bottom:20px;
}

/* ── Stats bar ── */
.stats-bar {
  background:white;
  box-shadow:0 4px 24px rgba(27,59,140,0.10);
  border-radius:20px;
  margin:-40px 0 0;
  display:grid;
  grid-template-columns:repeat(4,1fr);
  overflow:hidden;
  position:relative;z-index:10;
}
@media(max-width:640px){.stats-bar{grid-template-columns:repeat(2,1fr);}}
.stat-item {
  padding:24px 16px;
  text-align:center;
  border-left:1px solid #F1F5F9;
}
.stat-item:last-child { border:none; }
.stat-num { font-size:28px;font-weight:900;color:var(--blue); }
.stat-lbl { font-size:12px;color:var(--gray);font-weight:700;margin-top:2px; }

/* ── Story ── */
.about-section { padding:64px 0; }
.about-section h2 {
  font-size:26px;font-weight:900;color:var(--text);
  margin-bottom:10px;
  position:relative;
  padding-bottom:12px;
}
.about-section h2::after {
  content:'';position:absolute;bottom:0;right:0;
  width:48px;height:3px;background:var(--orange);border-radius:2px;
}
.about-section p { font-size:14px;color:var(--gray);line-height:1.9;margin-top:14px; }

.story-grid {
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:48px;
  align-items:center;
}
@media(max-width:768px){.story-grid{grid-template-columns:1fr;gap:28px;}}
.story-img {
  border-radius:24px;
  overflow:hidden;
  box-shadow:0 8px 40px rgba(27,59,140,0.15);
}
.story-img img {
  width:100%;height:auto;display:block;border-radius:24px;
}

/* ── Feature banner ── */
.feature-banner {
  border-radius:24px;
  overflow:hidden;
  box-shadow:0 8px 40px rgba(27,59,140,0.15);
  margin:0 0 64px;
}
.feature-banner img {
  width:100%;height:auto;display:block;
}

/* ── Values ── */
.values-grid {
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:20px;
  margin-top:36px;
}
@media(max-width:768px){.values-grid{grid-template-columns:1fr 1fr;}}
@media(max-width:480px){.values-grid{grid-template-columns:1fr;}}
.value-card {
  background:white;
  border-radius:18px;
  padding:28px 20px;
  box-shadow:0 4px 20px rgba(27,59,140,0.07);
  text-align:center;
  transition:transform .25s,box-shadow .25s;
}
.value-card:hover { transform:translateY(-4px);box-shadow:0 12px 32px rgba(27,59,140,0.12); }
.value-emoji { font-size:36px;margin-bottom:12px; }
.value-card h3 { font-size:14px;font-weight:900;color:var(--blue);margin-bottom:6px; }
.value-card p  { font-size:12px;color:var(--gray);line-height:1.7; }

/* ── Why us ── */
.why-section {
  background:linear-gradient(135deg,var(--blue-dk),var(--blue));
  border-radius:24px;
  padding:48px 40px;
  margin-bottom:64px;
  position:relative;overflow:hidden;
}
@media(max-width:640px){.why-section{padding:32px 20px;}}
.why-section::before {
  content:'';position:absolute;width:260px;height:260px;border-radius:50%;
  background:rgba(232,113,26,0.10);top:-80px;left:-60px;
}
.why-section h2 { font-size:24px;font-weight:900;color:white;margin-bottom:8px; }
.why-section > p { font-size:13px;color:rgba(255,255,255,0.6);margin-bottom:32px; }
.why-grid {
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:20px;
}
@media(max-width:640px){.why-grid{grid-template-columns:1fr;}}
.why-item {
  background:rgba(255,255,255,0.08);
  border:1px solid rgba(255,255,255,0.12);
  border-radius:16px;
  padding:20px 16px;
}
.why-item .icon { font-size:28px;margin-bottom:10px; }
.why-item h4 { font-size:13px;font-weight:800;color:white;margin-bottom:6px; }
.why-item p  { font-size:12px;color:rgba(255,255,255,0.60);line-height:1.7; }

/* ── CTA ── */
.about-cta {
  text-align:center;
  padding:0 0 72px;
}
.about-cta h2 { font-size:26px;font-weight:900;color:var(--text);margin-bottom:10px; }
.about-cta p  { font-size:14px;color:var(--gray);margin-bottom:28px;line-height:1.7; }
.cta-btns { display:flex;gap:14px;justify-content:center;flex-wrap:wrap; }
.cta-btns .btn-orange {
  background:linear-gradient(90deg,var(--orange),#C85E10);color:white;
  padding:14px 28px;border-radius:14px;font-weight:800;font-size:14px;
  transition:opacity .2s,transform .2s;
}
.cta-btns .btn-orange:hover{opacity:.9;transform:translateY(-2px);}
.cta-btns .btn-blue {
  background:var(--blue);color:white;
  padding:14px 28px;border-radius:14px;font-weight:800;font-size:14px;
  transition:opacity .2s,transform .2s;
}
.cta-btns .btn-blue:hover{opacity:.9;transform:translateY(-2px);}
</style>
@endpush

@section('content')

{{-- Hero --}}
<div class="about-hero">
  <div class="about-hero-bg"></div>
  <div class="pill">✦ شركة ابناء الفريد التجارية</div>
  <h1>من نحن</h1>
  <p>الصين بين يديك — أضخم معرض للمنتجات في فلسطين، نجمع لك آلاف المنتجات في مكان واحد</p>
</div>

<div class="container">

  {{-- Stats bar --}}
  <div class="stats-bar">
    <div class="stat-item">
      <div class="stat-num">+5,000</div>
      <div class="stat-lbl">منتج متوفر</div>
    </div>
    <div class="stat-item">
      <div class="stat-num">+10K</div>
      <div class="stat-lbl">عميل راضٍ</div>
    </div>
    <div class="stat-item">
      <div class="stat-num">4.9 ⭐</div>
      <div class="stat-lbl">تقييم العملاء</div>
    </div>
    <div class="stat-item">
      <div class="stat-num">فلسطين 🇵🇸</div>
      <div class="stat-lbl">نوصّل لكل المناطق</div>
    </div>
  </div>

  {{-- Story --}}
  <div class="about-section" style="padding-top:80px;">
    <div class="story-grid">
      <div>
        <h2>قصتنا</h2>
        <p>
          شركة ابناء الفريد التجارية — واحدة من أبرز شركات الاستيراد والتجزئة في فلسطين،
          متخصصون في جلب أفضل المنتجات مباشرةً من الصين وتوفيرها للمحلات التجارية،
          المتاجر الإلكترونية، وصفحات التسويق بأسعار منافسة وجودة مضمونة.
        </p>
        <p>
          بدأنا رحلتنا برؤية واضحة: أن نجعل المنتجات العالمية في متناول الجميع،
          وأن نكون الشريك الموثوق لكل من يريد البناء على تجارة ناجحة ومستدامة.
        </p>
        <p>
          اليوم، نفخر بخدمة آلاف العملاء في مختلف مناطق فلسطين والداخل،
          مع توصيل سريع وخدمة عملاء تضع رضاك فوق كل اعتبار.
        </p>
      </div>
      <div class="story-img">
        <img src="{{ asset('images/banner.png') }}" alt="شركة ابناء الفريد التجارية" loading="lazy"/>
      </div>
    </div>
  </div>

  {{-- Values --}}
  <div class="about-section" style="padding-top:8px;">
    <h2>قيمنا</h2>
    <div class="values-grid">
      <div class="value-card">
        <div class="value-emoji">🏆</div>
        <h3>الجودة أولاً</h3>
        <p>نختار كل منتج بعناية لنضمن لك جودة تليق بثقتك بنا</p>
      </div>
      <div class="value-card">
        <div class="value-emoji">🤝</div>
        <h3>الأمانة والشفافية</h3>
        <p>نؤمن بالعلاقات طويلة الأمد المبنية على الصدق مع كل عميل</p>
      </div>
      <div class="value-card">
        <div class="value-emoji">🚀</div>
        <h3>السرعة في التوصيل</h3>
        <p>نلتزم بتوصيل طلباتك في أسرع وقت لجميع مناطق فلسطين</p>
      </div>
      <div class="value-card">
        <div class="value-emoji">💰</div>
        <h3>أسعار تنافسية</h3>
        <p>نوفر أفضل الأسعار لأن نجاح عملك هو نجاحنا</p>
      </div>
      <div class="value-card">
        <div class="value-emoji">🌍</div>
        <h3>تنوع المنتجات</h3>
        <p>أكثر من 5000 منتج في فئات متعددة تلبي كل احتياجاتك</p>
      </div>
      <div class="value-card">
        <div class="value-emoji">📞</div>
        <h3>دعم دائم</h3>
        <p>فريق خدمة عملاء متاح للإجابة على كل استفساراتك</p>
      </div>
    </div>
  </div>

</div>

{{-- Why us ── full width --}}
<div class="container">
  <div class="why-section">
    <h2>لماذا تختار ابناء الفريد؟</h2>
    <p>نقدم لك تجربة تسوق متكاملة من أول نقرة حتى وصول المنتج لبابك</p>
    <div class="why-grid">
      <div class="why-item">
        <div class="icon">🏭</div>
        <h4>مباشرة من المصدر</h4>
        <p>منتجات مستوردة مباشرة من الصين تضمن لك أفضل سعر وأعلى جودة</p>
      </div>
      <div class="why-item">
        <div class="icon">🇵🇸</div>
        <h4>توصيل لكل فلسطين</h4>
        <p>نوصّل لجميع مناطق الضفة الغربية والداخل الفلسطيني بسرعة وأمان</p>
      </div>
      <div class="why-item">
        <div class="icon">📦</div>
        <h4>مخزون ضخم دائماً</h4>
        <p>نحتفظ بمخزون كافٍ لضمان توفر المنتجات وتسليمها في أقصر وقت</p>
      </div>
      <div class="why-item">
        <div class="icon">💳</div>
        <h4>دفع آمن ومريح</h4>
        <p>ادفع عند الاستلام أو عبر الدفع الإلكتروني بأمان تام</p>
      </div>
      <div class="why-item">
        <div class="icon">🔄</div>
        <h4>سياسة استرجاع مرنة</h4>
        <p>نضمن حقك في استبدال أو إرجاع المنتج في حال وجود أي مشكلة</p>
      </div>
      <div class="why-item">
        <div class="icon">🌟</div>
        <h4>ثقة آلاف العملاء</h4>
        <p>أكثر من 10,000 عميل راضٍ يثقون في شركة ابناء الفريد يومياً</p>
      </div>
    </div>
  </div>
</div>

<div class="container">
  {{-- CTA --}}
  <div class="about-cta">
    <h2>مستعد تبدأ التسوق؟</h2>
    <p>تصفح آلاف المنتجات الآن واستمتع بتجربة تسوق لا مثيل لها</p>
    <div class="cta-btns">
      <a href="{{ route('products.index') }}" class="btn-orange">🛍️ تصفح المنتجات</a>
      <a href="{{ route('contact') }}" class="btn-blue">📞 اتصل بنا</a>
    </div>
  </div>
</div>

@endsection
