@extends('layouts.app')
@section('title', __('company_name') . ' — ' . __('tagline'))
@section('description', __('products_count'))

@section('content')

{{-- ═══════════════════ HERO ═══════════════════ --}}
<div class="hero">
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
  <div class="particle" style="top:20%;right:52%;"></div>
  <div class="particle" style="top:65%;right:44%;width:6px;height:6px;animation-delay:1s;"></div>
  <div class="particle" style="top:40%;right:36%;width:3px;height:3px;animation-delay:2s;"></div>

  {{-- LEFT: text content --}}
  <div class="hero-left">
    <div class="hero-content">
      <div class="hero-pill">{{ __('hero_pill') }}</div>
      <h2>{{ __('hero_line1') }}<br/>{{ __('hero_line2') }}<br/><em>{{ __('hero_brand') }}</em></h2>
      <p class="hero-desc">
        {{ __('hero_desc_1') }}<br/>
        {{ __('hero_desc_2') }}<br/>
        {{ __('hero_desc_3') }}
      </p>
      <div class="hero-btns">
        <a href="{{ route('products.index') }}" class="btn-orange">{{ __('btn_shop_now') }}</a>
        <a href="https://wa.me/970598191312" target="_blank" class="btn-ghost">{{ __('btn_contact') }}</a>
      </div>
      <div class="hero-kpis">
        <div class="kpi-item">
          <div class="kpi-num"><b>+</b><span class="kpi-counter" data-target="5000" data-format="comma">0</span></div>
          <div class="kpi-label">{{ __('kpi_products') }}</div>
        </div>
        <div class="kpi-item">
          <div class="kpi-num"><b>+</b><span class="kpi-counter" data-target="50">0</span></div>
          <div class="kpi-label">{{ __('kpi_brands') }}</div>
        </div>
        <div class="kpi-item">
          <div class="kpi-num"><b>+</b><span class="kpi-counter" data-target="10000" data-format="comma">0</span></div>
          <div class="kpi-label">{{ __('kpi_customers') }}</div>
        </div>
        <div class="kpi-item">
          <div class="kpi-num"><span class="kpi-counter" data-target="4.9" data-format="decimal">0</span><b>★</b></div>
          <div class="kpi-label">{{ __('kpi_rating') }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- RIGHT: full-bleed image --}}
  <div class="hero-right">
    <img src="https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?w=700&q=85" alt="{{ __('company_name') }}"/>
    <div class="hero-img-overlay"></div>
    <div class="float-card fc-top">
      <div class="fc-icon">💄</div>
      <div class="fc-name">Charlotte Tilbury</div>
      <div class="fc-price">89 ₪</div>
    </div>
    <div class="float-card fc-mid">
      <div class="fc-icon">🧴</div>
      <div class="fc-name">La Roche-Posay</div>
      <div class="fc-price">65 ₪</div>
    </div>
    <div class="float-card fc-bot">
      <div class="fc-icon">🌸</div>
      <div class="fc-name">Dior Perfume</div>
      <div class="fc-price">195 ₪</div>
    </div>
    <div class="sale-ribbon">{{ __('sale_ribbon') }}</div>
  </div>
</div>

{{-- ═══════════════════ MARQUEE ═══════════════════ --}}
<div class="marquee-wrap">
  <div class="marquee">
    @for($i=0;$i<2;$i++)
      <span>{{ __('feat_authentic_title') }}</span>
      <span>{{ __('feat_delivery_title') }}</span>
      <span>{{ __('feat_cod_title') }}</span>
      <span>{{ __('products_count') }}</span>
      <span>{{ __('location') }}</span>
      <span>{{ __('section_brands_title') }}</span>
      <span>{{ __('free_shipping_msg') }}</span>
    @endfor
  </div>
</div>

{{-- ═══════════════════ CATEGORIES ═══════════════════ --}}
<section style="background:var(--gray-bg);">
  <div class="container">
    <div class="section-head">
      <div class="section-title">
        <h2>{{ __('section_categories_title') }}</h2>
        <p>{{ __('section_categories_sub') }}</p>
        <div class="title-line"></div>
      </div>
      <a href="{{ route('products.index') }}" class="see-all">{{ __('see_all') }}</a>
    </div>
    @php
      $catFallbackImages = [
        'makeup'   => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400&q=80',
        'skincare' => 'https://images.unsplash.com/photo-1556228578-8c89e6adf883?w=400&q=80',
        'hair'     => 'https://images.unsplash.com/photo-1527799820374-dcf8d9d4a388?w=400&q=80',
        'perfume'  => 'https://images.unsplash.com/photo-1541643600914-78b084683702?w=400&q=80',
        'nails'    => 'https://images.unsplash.com/photo-1604654894610-df63bc536371?w=400&q=80',
        'devices'  => 'https://images.unsplash.com/photo-1522338242992-e1a54906a8da?w=400&q=80',
      ];
      $defaultCatImage = 'https://images.unsplash.com/photo-1512207736890-6ffed8a84e8d?w=400&q=80';
    @endphp
    <div class="categories-grid">
      @forelse($categories as $cat)
        <a href="{{ route('products.category', $cat->slug) }}" class="cat-card">
          @if($cat->image)
            <img src="{{ asset('storage/'.$cat->image) }}" alt="{{ $cat->name }}" loading="lazy"/>
          @else
            <img src="{{ $catFallbackImages[$cat->slug] ?? $defaultCatImage }}" alt="{{ $cat->name }}" loading="lazy"/>
          @endif
          <div class="cat-overlay"></div>
          <div class="cat-info">
            <h3>{{ $cat->name }}</h3>
            @if($cat->products_count ?? 0)<span>+{{ $cat->products_count }} {{ __('products_unit') }}</span>@endif
          </div>
        </a>
      @empty
        @php
          $emptyCats = [
            ['makeup',  '💄', 'مكياج',  $catFallbackImages['makeup']],
            ['skincare','🧴', 'عناية بالبشرة', $catFallbackImages['skincare']],
            ['hair',    '💆', 'شعر',    $catFallbackImages['hair']],
            ['perfume', '🌹', 'عطور',   $catFallbackImages['perfume']],
            ['nails',   '💅', 'أظافر',  $catFallbackImages['nails']],
            ['devices', '⚡', 'أجهزة',  $catFallbackImages['devices']],
          ];
        @endphp
        @foreach($emptyCats as [$slug, $icon, $label, $img])
          <a href="{{ route('products.category', $slug) }}" class="cat-card">
            <img src="{{ $img }}" alt="{{ $label }}" loading="lazy"/>
            <div class="cat-overlay"></div>
            <div class="cat-info"><h3>{{ $label }}</h3></div>
          </a>
        @endforeach
      @endforelse
    </div>
  </div>
</section>

{{-- ═══════════════════ FEATURED PRODUCTS ═══════════════════ --}}
@if(isset($featuredProducts) && $featuredProducts->count())
<section>
  <div class="container">
    <div class="section-head">
      <div class="section-title">
        <h2>{{ __('section_featured_title') }}</h2>
        <p>{{ __('section_featured_sub') }}</p>
        <div class="title-line"></div>
      </div>
      <a href="{{ route('products.index') }}" class="see-all">{{ __('see_all') }}</a>
    </div>
    <div class="products-grid">
      @foreach($featuredProducts as $product)
        <x-product-card :product="$product"/>
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- ═══════════════════ DUAL BANNERS ═══════════════════ --}}
<div style="padding:0 24px 56px;max-width:1280px;margin:0 auto;">
  <div class="dual-banners">
    <div class="d-banner">
      <img src="https://images.unsplash.com/photo-1571781926291-c477ebfd024b?w=700&q=80" alt="{{ __('skincare') }}"/>
      <div class="d-banner-overlay blue"></div>
      <div class="d-banner-content">
        <span class="tag">{{ __('banner_skincare_tag') }}</span>
        <h3>{{ __('banner_skincare_title') }}</h3>
        <p>{{ __('banner_skincare_desc') }}</p>
        <a href="{{ route('products.category','skincare') }}">{{ __('banner_skincare_btn') }}</a>
      </div>
    </div>
    <div class="d-banner">
      <img src="https://images.unsplash.com/photo-1512207736890-6ffed8a84e8d?w=700&q=80" alt="{{ __('perfume') }}"/>
      <div class="d-banner-overlay orange"></div>
      <div class="d-banner-content">
        <span class="tag">{{ __('banner_perfume_tag') }}</span>
        <h3>{{ __('banner_perfume_title') }}</h3>
        <p>{{ __('banner_perfume_desc') }}</p>
        <a href="{{ route('products.category','perfume') }}">{{ __('banner_perfume_btn') }}</a>
      </div>
    </div>
  </div>
</div>

{{-- ═══════════════════ OFFERS / DEALS ═══════════════════ --}}
@if((isset($offerProducts) && $offerProducts->count()) || (isset($offerBanners) && $offerBanners->count()))
<section style="background:linear-gradient(180deg,#FFF7F0 0%,var(--gray-bg) 100%);">
  <div class="container">
    @if(isset($offerBanners) && $offerBanners->count())
      <div style="display:grid;grid-template-columns:1fr;gap:20px;margin-bottom:24px;">
        @foreach($offerBanners as $banner)
          <a href="{{ $banner->link ?? '#' }}" style="display:block;text-decoration:none;border-radius:18px;overflow:hidden;background:linear-gradient(135deg,#E8711A,#C85E10);position:relative;padding:24px 28px;color:white;box-shadow:0 8px 24px rgba(232,113,26,0.25);">
            <div style="position:absolute;top:-30px;left:-30px;width:140px;height:140px;background:rgba(255,255,255,0.07);border-radius:50%;"></div>
            <div style="display:flex;align-items:center;gap:18px;position:relative;z-index:1;flex-wrap:wrap;">
              <div style="font-size:48px;">⚡</div>
              <div style="flex:1;min-width:200px;">
                @if($banner->badge_text)
                  <span style="display:inline-block;background:rgba(0,0,0,0.2);color:white;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;margin-bottom:8px;">{{ $banner->badge_text }}</span>
                @endif
                <h2 style="font-size:1.5rem;font-weight:900;margin:0 0 4px;">{{ $banner->title_ar ?? 'عروض حصرية' }}</h2>
                <p style="opacity:.85;font-size:14px;margin:0;">{{ $banner->subtitle_ar ?? 'خصومات حقيقية على أفضل المنتجات' }}</p>
              </div>
              @if($banner->button_text_ar)
                <div style="background:white;color:var(--orange);padding:10px 22px;border-radius:12px;font-weight:800;font-size:14px;">{{ $banner->button_text_ar }} ←</div>
              @endif
            </div>
          </a>
        @endforeach
      </div>
    @endif

    @if(isset($offerProducts) && $offerProducts->count())
      <div class="section-head">
        <div class="section-title">
          @if(isset($flashSale) && $flashSale)
            <h2>⚡ {{ $flashSale->name_ar }}</h2>
            @if($flashSale->ends_at)
              <p class="flash-countdown-wrap">
                ينتهي العرض خلال:
                <span class="flash-countdown" data-ends="{{ $flashSale->ends_at->toIso8601String() }}"></span>
              </p>
            @else
              <p>خصومات حقيقية على منتجات مختارة — لفترة محدودة</p>
            @endif
          @else
            <h2>🔥 عروض اليوم</h2>
            <p>خصومات حقيقية على منتجات مختارة — لفترة محدودة</p>
          @endif
          <div class="title-line"></div>
        </div>
        <a href="{{ route('products.index', ['on_sale' => 1]) }}" class="see-all">عرض كل العروض ←</a>
      </div>
      <div class="products-grid">
        @foreach($offerProducts as $product)
          <x-product-card :product="$product"/>
        @endforeach
      </div>
    @endif
  </div>
</section>
@endif

{{-- ═══════════════════ BEST SELLERS ═══════════════════ --}}
@if(isset($bestSellers) && $bestSellers->count())
<section style="background:var(--gray-bg);">
  <div class="container">
    <div class="section-head">
      <div class="section-title">
        <h2>{{ __('section_bestsellers_title') }}</h2>
        <p>{{ __('section_bestsellers_sub') }}</p>
        <div class="title-line"></div>
      </div>
      <a href="{{ route('products.index') }}" class="see-all">{{ __('see_all') }}</a>
    </div>
    <div class="products-grid">
      @foreach($bestSellers as $product)
        <x-product-card :product="$product"/>
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- ═══════════════════ FLASH OFFER ═══════════════════ --}}
<section style="padding:48px 0;">
  <div class="container">
    <div class="flash-banner">
      <div class="flash-text">
        <p>{{ __('flash_tag') }}</p>
        <h2>{{ __('flash_title') }}</h2>
        <div class="flash-timer">
          <div class="timer-item"><span id="h">02</span><p>{{ __('flash_hour') }}</p></div>
          <span class="timer-sep">:</span>
          <div class="timer-item"><span id="m">45</span><p>{{ __('flash_minute') }}</p></div>
          <span class="timer-sep">:</span>
          <div class="timer-item"><span id="s">30</span><p>{{ __('flash_second') }}</p></div>
        </div>
        <a href="{{ route('products.index') }}" class="btn-orange" style="margin-top:20px;">{{ __('btn_shop_now') }}</a>
      </div>
      <div class="flash-img">💄</div>
    </div>
  </div>
</section>

{{-- ═══════════════════ NEW ARRIVALS ═══════════════════ --}}
@if(isset($newProducts) && $newProducts->count())
<section>
  <div class="container">
    <div class="section-head">
      <div class="section-title">
        <h2>{{ __('section_new_title') }}</h2>
        <p>{{ __('section_new_sub') }}</p>
        <div class="title-line"></div>
      </div>
      <a href="{{ route('products.index') }}" class="see-all">{{ __('see_all') }}</a>
    </div>
    <div class="products-grid">
      @foreach($newProducts as $product)
        <x-product-card :product="$product"/>
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- ═══════════════════ BRANDS ═══════════════════ --}}
<section style="background:var(--gray-bg);padding:56px 0;">
  <div class="container">
    <div class="section-head">
      <div class="section-title">
        <h2>{{ __('section_brands_title') }}</h2>
        <p>{{ __('section_brands_sub') }}</p>
        <div class="title-line"></div>
      </div>
    </div>
    <div class="brands-track">
      @if(isset($brands) && $brands->count())
        @foreach($brands as $brand)
          <a href="{{ route('products.index','?brand='.$brand->id) }}" class="brand-pill">{{ $brand->name }}</a>
        @endforeach
        @foreach($brands as $brand)
          <a href="{{ route('products.index','?brand='.$brand->id) }}" class="brand-pill">{{ $brand->name }}</a>
        @endforeach
      @else
        @foreach(['Charlotte Tilbury','La Roche-Posay','Maybelline','L\'Oréal','MAC','NYX','Olaplex','Dyson','Fenty Beauty','NARS','Urban Decay','Cetaphil'] as $b)
          <span class="brand-pill">{{ $b }}</span>
        @endforeach
      @endif
    </div>
  </div>
</section>

{{-- ═══════════════════ TESTIMONIALS ═══════════════════ --}}
<section>
  <div class="container">
    <div class="section-head">
      <div class="section-title">
        <h2>{{ __('section_testimonials_title') }}</h2>
        <p>{{ __('section_testimonials_sub') }}</p>
        <div class="title-line"></div>
      </div>
      <div style="display:flex;align-items:center;gap:8px;">
        <span style="font-size:28px;font-weight:900;color:var(--blue);">4.9</span>
        <div>
          <div style="color:#FFC107;font-size:18px;">★★★★★</div>
          <div style="font-size:12px;color:var(--gray);">{{ __('rating_label') }}</div>
        </div>
      </div>
    </div>
    @php
      $locale = session('locale','ar');
      $reviews = [
        'ar' => [
          ['أحمد أبو سالم','رام الله','منتجات أصيلة 100% والتوصيل سريع جداً! وصل طلبي في نفس اليوم. تعامل راقٍ وأسعار ممتازة.','👨'],
          ['خالد الخليل','نابلس','أفضل متجر تجميل في فلسطين! الأسعار معقولة جداً مقارنة بالمتاجر الأخرى. الدفع عند الاستلام ميزة رائعة.','👨‍🦱'],
          ['محمد الحمدان','الخليل','صاحب محل — أتعامل مع أبناء الفريد منذ سنتين. دائماً منتجات طازجة ووفيرة وبأسعار الجملة.','🧔'],
        ],
        'he' => [
          ['רחל כהן','חיפה','מוצרים מקוריים ומשלוח מהיר! ההזמנה הגיעה באותו יום. שירות מצוין ומחירים נהדרים.','💁‍♀️'],
          ['מרים לוי','ירושלים','החנות הטובה ביותר לקוסמטיקה! המחירים סבירים מאוד. תשלום עם קבלה — פיצ\'ר מדהים!','👩‍🦱'],
          ['שרה אבו עלי','חברון','בעלת חנות — עובדת עם אבנאא אלפריד כבר שנתיים. מוצרים טריים תמיד ובמחירי סיטונאות.','👩'],
        ],
        'en' => [
          ['Sara Ahmed','Ramallah','100% authentic products and super fast delivery! My order arrived the same day. Excellent service and great prices.','💁‍♀️'],
          ['Reem Al-Khalil','Nablus','Best beauty store in Palestine! Prices are very reasonable. Cash on delivery is a fantastic feature!','👩‍🦱'],
          ['Mona Hamdan','Hebron','Beauty shop owner — working with Alfared Sons for 2 years. Always fresh products in abundance at wholesale prices.','👩'],
        ],
      ];
      $currentReviews = $reviews[$locale] ?? $reviews['ar'];
    @endphp
    <div class="test-grid">
      @foreach($currentReviews as $review)
        <div class="test-card">
          <div class="test-header">
            <div class="test-avatar">{{ $review[3] }}</div>
            <div>
              <div class="test-name">{{ $review[0] }}</div>
              <div class="test-loc">📍 {{ $review[1] }}</div>
            </div>
          </div>
          <div class="test-stars">★★★★★</div>
          <div class="test-text">"{{ $review[2] }}"</div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ═══════════════════ FEATURES ═══════════════════ --}}
<section class="features">
  <div class="container">
    <div class="features-grid">
      <div class="feat-card">
        <div class="feat-icon fi-blue">🚚</div>
        <h3>{{ __('feat_delivery_title') }}</h3>
        <p>{{ __('feat_delivery_desc') }}</p>
      </div>
      <div class="feat-card">
        <div class="feat-icon fi-orange">✅</div>
        <h3>{{ __('feat_authentic_title') }}</h3>
        <p>{{ __('feat_authentic_desc') }}</p>
      </div>
      <div class="feat-card">
        <div class="feat-icon fi-green">💰</div>
        <h3>{{ __('feat_cod_title') }}</h3>
        <p>{{ __('feat_cod_desc') }}</p>
      </div>
    </div>
  </div>
</section>

{{-- ═══════════════════ LOCATION MAP ═══════════════════ --}}
<section class="location-section">
  <div class="container">
    <div class="section-head">
      <div class="section-title">
        <h2>📍 موقعنا</h2>
        <p>{{ __('location') }}</p>
        <div class="title-line"></div>
      </div>
    </div>
    <div class="location-layout">
      <div class="location-info">
        <div class="loc-item">
          <div class="loc-icon">🏪</div>
          <div>
            <h4>{{ __('company_name') }}</h4>
            <p>{{ __('location') }}</p>
          </div>
        </div>
        <div class="loc-item">
          <div class="loc-icon">📞</div>
          <div>
            <h4>{{ __('contact_us') }}</h4>
            <a href="tel:+970598191312" dir="ltr" class="loc-link">+970 598 191 312</a>
          </div>
        </div>
        <div class="loc-item">
          <div class="loc-icon">📧</div>
          <div>
            <h4>{{ __('email') ?? 'البريد الإلكتروني' }}</h4>
            <a href="mailto:faredahmad615@gmail.com" dir="ltr" class="loc-link">faredahmad615@gmail.com</a>
          </div>
        </div>
        <div class="loc-item">
          <div class="loc-icon">🕐</div>
          <div>
            <h4>ساعات العمل</h4>
            <p>السبت – الخميس: 9 ص – 9 م</p>
          </div>
        </div>
        <a href="https://wa.me/970598191312" target="_blank" class="btn-orange" style="display:inline-flex;align-items:center;gap:8px;margin-top:8px;text-decoration:none;">
          💬 تواصل معنا عبر واتساب
        </a>
      </div>
      <div class="location-map">
        <iframe
          src="https://maps.google.com/maps?q=31.553731,35.084007&z=17&output=embed"
          width="100%" height="100%"
          style="border:0; border-radius:18px;"
          allowfullscreen=""
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
          title="موقع المسطبة الأهلية"
        ></iframe>
      </div>
    </div>
  </div>
</section>

{{-- ═══════════════════ NEWSLETTER / CTA ═══════════════════ --}}
<section class="newsletter">
  <div class="newsletter-overlay"></div>
  <div class="newsletter-content">
    <h2>{{ __('section_newsletter_title') }}</h2>
    <p>{{ __('section_newsletter_sub') }}</p>
    <div class="newsletter-inner">
      <a href="https://wa.me/970598191312" target="_blank" class="btn-orange" style="text-decoration:none;">
        {{ __('btn_whatsapp') }}
      </a>
      <a href="https://www.tiktok.com/@huda.center" target="_blank" class="btn-ghost" style="text-decoration:none;">
        {{ __('btn_tiktok') }}
      </a>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
// KPI Counter Animation — runs on page load, no scroll needed
(function() {
  function animateCounter(el) {
    const target  = parseFloat(el.dataset.target);
    const fmt     = el.dataset.format;
    const isDecimal = fmt === 'decimal';
    const duration  = 1400;
    const steps     = 60;
    const interval  = duration / steps;
    let current     = 0;
    const increment = target / steps;

    const timer = setInterval(() => {
      current += increment;
      if (current >= target) {
        current = target;
        clearInterval(timer);
      }
      if (isDecimal) {
        el.textContent = current.toFixed(1);
      } else if (fmt === 'comma') {
        el.textContent = Math.floor(current).toLocaleString();
      } else {
        el.textContent = Math.floor(current);
      }
    }, interval);
  }

  // Start immediately when DOM is ready — hero is always in the viewport on load
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.kpi-counter').forEach(function(el) {
      animateCounter(el);
    });
  });
})();
</script>
<script>
// Countdown Timer
function updateTimer() {
  const now = new Date();
  const end = new Date(now);
  end.setHours(23,59,59,0);
  const diff = Math.max(0, end - now);
  const h = Math.floor(diff/3600000);
  const m = Math.floor((diff%3600000)/60000);
  const s = Math.floor((diff%60000)/1000);
  const pad = n => String(n).padStart(2,'0');
  ['h','m','s'].forEach((id,i) => {
    const el = document.getElementById(id);
    if(el) el.textContent = pad([h,m,s][i]);
  });
}
updateTimer();
setInterval(updateTimer, 1000);
</script>

<script>
// Flash-sale section countdown
(function() {
  const el = document.querySelector('.flash-countdown');
  if (!el) return;
  const ends = new Date(el.dataset.ends);
  function pad(n){ return String(n).padStart(2,'0'); }
  function render() {
    const diff = Math.max(0, ends - new Date());
    if (diff === 0) { el.textContent = 'انتهى العرض'; return; }
    const d = Math.floor(diff/86400000);
    const h = Math.floor((diff%86400000)/3600000);
    const m = Math.floor((diff%3600000)/60000);
    const s = Math.floor((diff%60000)/1000);
    let html = '';
    if (d > 0) html += `<span class="fc-block">${d}</span><span class="fc-sep">يوم</span>`;
    html += `<span class="fc-block">${pad(h)}</span><span class="fc-sep">:</span>`;
    html += `<span class="fc-block">${pad(m)}</span><span class="fc-sep">:</span>`;
    html += `<span class="fc-block">${pad(s)}</span>`;
    el.innerHTML = html;
  }
  render();
  setInterval(render, 1000);
})();
</script>
@endpush
