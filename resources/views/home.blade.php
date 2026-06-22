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

{{-- ═══════════════════ PROMO POPUP BANNER ═══════════════════ --}}
@if(isset($heroBanners) && $heroBanners->count())
@php
  $popupBanners = $heroBanners;
  $popupKey     = 'promo_popup_' . $popupBanners->pluck('id')->sort()->implode('_');
@endphp

{{-- Overlay --}}
<div id="promoPopupOverlay"
     style="display:none;position:fixed;inset:0;background:rgba(10,18,40,.65);backdrop-filter:blur(4px);z-index:9998;align-items:center;justify-content:center;padding:20px;">

  {{-- Modal box --}}
  <div id="promoPopupBox"
       style="position:relative;max-width:680px;width:100%;border-radius:24px;overflow:hidden;box-shadow:0 24px 80px rgba(0,0,0,.45);transform:scale(.9) translateY(20px);opacity:0;transition:transform .4s cubic-bezier(.34,1.56,.64,1),opacity .4s ease;">

    {{-- Close button --}}
    <button onclick="closePromoPopup()"
            style="position:absolute;top:14px;left:14px;z-index:10;width:36px;height:36px;border-radius:50%;background:rgba(0,0,0,.5);border:none;cursor:pointer;color:#fff;font-size:20px;line-height:1;display:flex;align-items:center;justify-content:center;transition:background .2s;"
            onmouseover="this.style.background='rgba(0,0,0,.8)'"
            onmouseout="this.style.background='rgba(0,0,0,.5)'">✕</button>

    @if($popupBanners->count() === 1)
      {{-- ── Single banner ── --}}
      @php $b = $popupBanners->first(); @endphp
      <a href="{{ $b->link ?? '#' }}" onclick="closePromoPopup()" style="display:block;text-decoration:none;position:relative;">
        <img src="{{ $b->image ? Storage::url($b->image) : '' }}" alt="{{ $b->title_ar }}"
             style="width:100%;height:360px;object-fit:cover;display:block;"/>
        <div style="position:absolute;inset:0;background:linear-gradient(to left,transparent 35%,rgba(10,20,60,.8));"></div>
        <div style="position:absolute;top:50%;right:40px;transform:translateY(-50%);color:#fff;max-width:55%;">
          @if($b->badge_text)
            <span style="display:inline-block;background:#E8711A;color:#fff;font-size:12px;font-weight:700;padding:5px 14px;border-radius:20px;margin-bottom:12px;">{{ $b->badge_text }}</span>
          @endif
          @if($b->title_ar)
            <h2 style="font-size:clamp(20px,3.5vw,34px);font-weight:900;margin:0 0 8px;line-height:1.25;">{{ $b->title_ar }}</h2>
          @endif
          @if($b->subtitle_ar)
            <p style="font-size:14px;opacity:.88;margin:0 0 18px;line-height:1.5;">{{ $b->subtitle_ar }}</p>
          @endif
          @if($b->button_text_ar)
            <span style="display:inline-block;background:#E8711A;color:#fff;padding:12px 26px;border-radius:12px;font-weight:700;font-size:14px;">{{ $b->button_text_ar }} ←</span>
          @endif
        </div>
      </a>

    @else
      {{-- ── Multi-banner slider ── --}}
      <div style="position:relative;overflow:hidden;">
        <div id="popupSlides" style="display:flex;transition:transform .5s cubic-bezier(.4,0,.2,1);">
          @foreach($popupBanners as $i => $b)
          <div style="min-width:100%;position:relative;flex-shrink:0;">
            <a href="{{ $b->link ?? '#' }}" onclick="closePromoPopup()" style="display:block;text-decoration:none;">
              <img src="{{ $b->image ? Storage::url($b->image) : '' }}" alt="{{ $b->title_ar }}"
                   style="width:100%;height:360px;object-fit:cover;display:block;"/>
              <div style="position:absolute;inset:0;background:linear-gradient(to left,transparent 35%,rgba(10,20,60,.8));"></div>
              <div style="position:absolute;top:50%;right:40px;transform:translateY(-50%);color:#fff;max-width:55%;">
                @if($b->badge_text)
                  <span style="display:inline-block;background:#E8711A;color:#fff;font-size:12px;font-weight:700;padding:5px 14px;border-radius:20px;margin-bottom:12px;">{{ $b->badge_text }}</span>
                @endif
                @if($b->title_ar)
                  <h2 style="font-size:clamp(20px,3.5vw,34px);font-weight:900;margin:0 0 8px;line-height:1.25;">{{ $b->title_ar }}</h2>
                @endif
                @if($b->subtitle_ar)
                  <p style="font-size:14px;opacity:.88;margin:0 0 18px;line-height:1.5;">{{ $b->subtitle_ar }}</p>
                @endif
                @if($b->button_text_ar)
                  <span style="display:inline-block;background:#E8711A;color:#fff;padding:12px 26px;border-radius:12px;font-weight:700;font-size:14px;">{{ $b->button_text_ar }} ←</span>
                @endif
              </div>
            </a>
          </div>
          @endforeach
        </div>
        {{-- Arrows --}}
        <button onclick="popupSlide(-1)" style="position:absolute;top:50%;right:12px;transform:translateY(-50%);width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,.88);border:none;cursor:pointer;font-size:18px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.2);">›</button>
        <button onclick="popupSlide(1)"  style="position:absolute;top:50%;left:12px; transform:translateY(-50%);width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,.88);border:none;cursor:pointer;font-size:18px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.2);">‹</button>
        {{-- Dots --}}
        <div id="popupDots" style="position:absolute;bottom:12px;left:50%;transform:translateX(-50%);display:flex;gap:7px;">
          @foreach($popupBanners as $i => $b)
            <button onclick="popupGoTo({{ $i }})" style="width:8px;height:8px;border-radius:50%;border:none;cursor:pointer;background:{{ $i===0?'#fff':'rgba(255,255,255,.45)' }};padding:0;transition:.3s;"></button>
          @endforeach
        </div>
      </div>
    @endif

    {{-- Footer strip --}}
    <div style="background:#fff;padding:14px 24px;display:flex;align-items:center;justify-content:space-between;gap:12px;">
      <span style="font-size:13px;color:#6B7280;">لا تفوّتي عروضنا المميزة 🎁</span>
      <button onclick="closePromoPopup()" style="background:none;border:1px solid #D1D5DB;border-radius:8px;padding:7px 18px;font-size:13px;color:#6B7280;cursor:pointer;">إغلاق</button>
    </div>

  </div>
</div>

@push('scripts')
<script>
(function(){
  const STORAGE_KEY = '{{ $popupKey }}';
  const overlay = document.getElementById('promoPopupOverlay');
  const box     = document.getElementById('promoPopupBox');

  // Show only once per session
  if (!sessionStorage.getItem(STORAGE_KEY)) {
    setTimeout(function(){
      overlay.style.display = 'flex';
      requestAnimationFrame(function(){
        requestAnimationFrame(function(){
          box.style.transform = 'scale(1) translateY(0)';
          box.style.opacity   = '1';
        });
      });
    }, 800);
  }

  window.closePromoPopup = function(){
    box.style.transform = 'scale(.9) translateY(20px)';
    box.style.opacity   = '0';
    setTimeout(function(){ overlay.style.display = 'none'; }, 350);
    sessionStorage.setItem(STORAGE_KEY, '1');
  };

  // Close on overlay click
  overlay.addEventListener('click', function(e){
    if (e.target === overlay) closePromoPopup();
  });

  // Close on Escape
  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') closePromoPopup();
  });

  @if($popupBanners->count() > 1)
  // Slider for multi-banner popup
  let cur = 0;
  const total  = {{ $popupBanners->count() }};
  const track  = document.getElementById('popupSlides');
  const dotsEl = document.querySelectorAll('#popupDots button');
  function popupGo(n){
    cur = (n + total) % total;
    track.style.transform = 'translateX(' + (cur * 100) + '%)';
    dotsEl.forEach(function(d,i){ d.style.background = i===cur ? '#fff' : 'rgba(255,255,255,.45)'; });
  }
  window.popupSlide = function(d){ popupGo(cur + d); };
  window.popupGoTo  = function(n){ popupGo(n); };
  setInterval(function(){ popupGo(cur + 1); }, 5000);
  @endif
})();
</script>
@endpush

@endif

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
      <a href="{{ route('categories.index') }}" class="see-all">{{ __('see_all') }}</a>
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
    {{-- Categories Slider --}}
    <div style="position:relative;padding:0 32px;">

      <button onclick="catMove(1)" aria-label="التالي"
        style="position:absolute;top:50%;right:0;transform:translateY(-50%);z-index:10;width:40px;height:40px;border-radius:50%;background:#fff;border:2px solid #E5E7EB;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 10px rgba(0,0,0,.12);">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1B3B8C" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
      </button>

      <div id="catTrack" style="display:flex;gap:16px;overflow-x:auto;scroll-behavior:smooth;scrollbar-width:none;-ms-overflow-style:none;padding:4px 0 8px;">
        <style>#catTrack::-webkit-scrollbar{display:none}</style>

        @forelse($categories as $cat)
          <a href="{{ route('products.category', $cat->slug) }}" class="cat-card" style="flex:0 0 200px;min-width:200px;">
            @php $catImg = $cat->image ? (str_starts_with($cat->image,'http') ? $cat->image : asset('storage/'.$cat->image)) : ($catFallbackImages[$cat->slug] ?? $defaultCatImage); @endphp
            <img src="{{ $catImg }}" alt="{{ $cat->name }}" loading="lazy"/>
            <div class="cat-overlay"></div>
            <div class="cat-info">
              <h3>{{ $cat->name }}</h3>
              @if($cat->products_count ?? 0)<span>+{{ $cat->products_count }} {{ __('products_unit') }}</span>@endif
            </div>
          </a>
        @empty
          @foreach([['makeup','مكياج',$catFallbackImages['makeup']],['skincare','عناية بالبشرة',$catFallbackImages['skincare']],['hair','شعر',$catFallbackImages['hair']],['perfume','عطور',$catFallbackImages['perfume']],['nails','أظافر',$catFallbackImages['nails']],['devices','أجهزة',$catFallbackImages['devices']]] as [$slug,$label,$img])
            <a href="{{ route('products.category', $slug) }}" class="cat-card" style="flex:0 0 200px;min-width:200px;">
              <img src="{{ $img }}" alt="{{ $label }}" loading="lazy"/>
              <div class="cat-overlay"></div>
              <div class="cat-info"><h3>{{ $label }}</h3></div>
            </a>
          @endforeach
        @endforelse
      </div>

      <button onclick="catMove(-1)" aria-label="السابق"
        style="position:absolute;top:50%;left:0;transform:translateY(-50%);z-index:10;width:40px;height:40px;border-radius:50%;background:#fff;border:2px solid #E5E7EB;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 10px rgba(0,0,0,.12);">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1B3B8C" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
      </button>
    </div>

    <script>
    var _catTimer = null;
    function catMove(dir) {
      var t = document.getElementById('catTrack');
      if (!t) return;
      t.scrollBy({ left: dir * -220, behavior: 'smooth' });
    }
    function _catAuto() {
      var t = document.getElementById('catTrack');
      if (!t) return;
      var atEnd = t.scrollLeft <= -(t.scrollWidth - t.clientWidth - 5);
      t.scrollBy({ left: atEnd ? t.scrollWidth : -220, behavior: 'smooth' });
    }
    function _catReset() {
      clearInterval(_catTimer);
      _catTimer = setInterval(_catAuto, 3500);
    }
    window.addEventListener('load', function() {
      var t = document.getElementById('catTrack');
      if (!t) return;
      t.addEventListener('mouseenter', function() { clearInterval(_catTimer); });
      t.addEventListener('mouseleave', _catReset);
      _catReset();
    });
    </script>
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
                {{ __('flash_ends_in') }}
                <span class="flash-countdown" data-ends="{{ $flashSale->ends_at->toIso8601String() }}"></span>
              </p>
            @else
              <p>{{ __('offers_today_sub') }}</p>
            @endif
          @else
            <h2>🔥 {{ __('offers_today_title') }}</h2>
            <p>{{ __('offers_today_sub') }}</p>
          @endif
          <div class="title-line"></div>
        </div>
        <a href="{{ route('products.index', ['on_sale' => 1]) }}" class="see-all">{{ __('see_all_offers') }}</a>
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
        <h2>📍 {{ __('location_title') }}</h2>
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
            <h4>{{ __('working_hours_title') }}</h4>
            <p>{{ __('working_hours_value') }}</p>
          </div>
        </div>
        <a href="https://wa.me/970598191312" target="_blank" class="btn-orange" style="display:inline-flex;align-items:center;gap:8px;margin-top:8px;text-decoration:none;">
          {{ __('contact_whatsapp_btn') }}
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
