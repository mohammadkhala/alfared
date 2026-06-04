<!DOCTYPE html>
@php $locale = session('locale','ar'); $isRtl = in_array($locale,['ar','he']); @endphp
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}"/>
  <title>@yield('title', __('company_name'))</title>
  <meta name="description" content="@yield('description', __('tagline'))"/>

  {{-- SEO: Canonical URL — tells Google the authoritative domain is alfared.ps --}}
  <link rel="canonical" href="{{ url()->current() }}"/>

  {{-- Open Graph --}}
  <meta property="og:type"        content="website"/>
  <meta property="og:url"         content="{{ url()->current() }}"/>
  <meta property="og:title"       content="@yield('title', __('company_name'))"/>
  <meta property="og:description" content="@yield('description', __('tagline'))"/>
  <meta property="og:image"       content="{{ asset('images/logo.png') }}"/>
  <meta property="og:locale"      content="ar_PS"/>
  <meta property="og:site_name"   content="شركة أبناء الفريد"/>

  {{-- Twitter Card --}}
  <meta name="twitter:card"        content="summary"/>
  <meta name="twitter:title"       content="@yield('title', __('company_name'))"/>
  <meta name="twitter:description" content="@yield('description', __('tagline'))"/>
  <meta name="twitter:image"       content="{{ asset('images/logo.png') }}"/>

  {{-- Robots --}}
  <meta name="robots" content="index, follow"/>

  <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}"/>
  <link rel="shortcut icon" href="{{ asset('images/logo.png') }}"/>
  <link rel="sitemap" type="application/xml" href="{{ url('/sitemap.xml') }}"/>

  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="{{ asset('css/store.css') }}"/>
  @stack('styles')
</head>
<body>

{{-- ════ TOP BAR ════ --}}
<div class="topbar">
  <div class="topbar-inner">
    {{-- Right side: contact info --}}
    <div class="topbar-contact">
      <a href="tel:+970598191312" dir="ltr">📞 +970 598 191 312</a>
      <span class="topbar-sep">|</span>
      <a href="mailto:faredahmad615@gmail.com" dir="ltr">📧 faredahmad615@gmail.com</a>
    </div>

    {{-- Left side: language + TikTok --}}
    <div class="topbar-end">
      <a href="https://www.facebook.com/HUDACENTERHEBRON" target="_blank" class="topbar-tiktok">📘 Facebook</a>
      <span class="topbar-sep">|</span>
      <a href="https://www.tiktok.com/@huda.center" target="_blank" class="topbar-tiktok">🎵 TikTok</a>
      <span class="topbar-sep">|</span>
      <div class="lang-switch">
        <a href="{{ route('lang.switch','ar') }}"
           class="{{ $locale==='ar'?'active':'' }}"
           title="العربية">عربي</a>
        <a href="{{ route('lang.switch','he') }}"
           class="{{ $locale==='he'?'active':'' }}"
           title="עברית">עברית</a>
        <a href="{{ route('lang.switch','en') }}"
           class="{{ $locale==='en'?'active':'' }}"
           title="English">EN</a>
      </div>
    </div>
  </div>
</div>

{{-- ════ NAVBAR ════ --}}
<nav class="navbar">
  <div class="container">
    <div class="inner">

      {{-- Logo --}}
      <a href="{{ route('home') }}" class="logo">
        <img src="{{ asset('images/logo.png') }}" alt="{{ __('company_name') }}" class="logo-img"/>
        <div class="logo-text">
          <h1>{{ __('company_name') }}</h1>
          <span>ALFARED SONS COMPANY</span>
        </div>
      </a>

      {{-- Nav Links --}}
      <ul class="nav-links">
        <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">{{ __('nav_home') }}</a></li>
        <li><a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">{{ __('nav_store') }}</a></li>
        <li><a href="{{ route('products.index') }}?on_sale=1" class="{{ request()->is('*/products*') && request('on_sale') ? 'active' : '' }}">{{ __('nav_offers') }}</a></li>
        <li><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">{{ __('nav_about') }}</a></li>
        <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">{{ __('nav_contact') }}</a></li>
      </ul>

      {{-- Actions --}}
      <div class="nav-actions">
        <form action="{{ route('products.search') }}" method="GET" class="search-bar">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          <input type="text" name="q" placeholder="{{ __('search_placeholder') }}" value="{{ request('q') }}"/>
        </form>

        <a href="{{ route('account.wishlist') }}" class="icon-btn" title="{{ __('wishlist') }}">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
        </a>

        <a href="{{ route('cart.index') }}" class="icon-btn" title="{{ __('cart') }}" id="cartIconLink">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>
          <span class="cart-badge" id="cartBadge" style="{{ count(session('cart',[])) > 0 ? '' : 'display:none;' }}">{{ count(session('cart',[])) }}</span>
        </a>

        @auth
          <a href="{{ route('account.index') }}" class="icon-btn" title="{{ __('my_account') }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </a>
        @else
          <a href="{{ route('login') }}" class="btn btn-blue" style="padding:9px 18px;font-size:13px;">{{ __('login') }}</a>
        @endauth

        {{-- Hamburger button — visible only on mobile --}}
        <button type="button" class="mobile-menu-toggle" onclick="document.body.classList.toggle('mobile-drawer-open')" aria-label="Menu"
          style="display:none;background:none;border:none;cursor:pointer;color:#1B3B8C;width:42px;height:42px;border-radius:12px;align-items:center;justify-content:center;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px;height:24px;"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
      </div>

    </div>
  </div>
</nav>

{{-- Mobile drawer --}}
<aside class="mobile-drawer">
  <div class="mobile-drawer-header">
    <strong style="color:#1B3B8C;font-size:15px;">{{ __('company_name') }}</strong>
    <button type="button" onclick="document.body.classList.remove('mobile-drawer-open')" aria-label="Close"
      style="background:none;border:none;font-size:24px;color:#1B3B8C;cursor:pointer;line-height:1;">✕</button>
  </div>
  <ul class="mobile-drawer-links">
    <li><a href="{{ route('home') }}">🏠 {{ __('nav_home') }}</a></li>
    <li><a href="{{ route('products.index') }}">🛍️ {{ __('nav_store') }}</a></li>
    <li><a href="{{ route('products.index') }}?on_sale=1">🔥 {{ __('nav_offers') }}</a></li>
    <li><a href="{{ route('about') }}" onclick="document.body.classList.remove('mobile-drawer-open')">🏢 {{ __('nav_about') }}</a></li>
    <li><a href="{{ route('contact') }}" onclick="document.body.classList.remove('mobile-drawer-open')">📞 {{ __('nav_contact') }}</a></li>
    <li class="separator"></li>
    @auth
      <li><a href="{{ route('account.index') }}">👤 {{ __('my_account') }}</a></li>
      <li><a href="{{ route('account.orders') }}">📦 {{ __('footer_orders') }}</a></li>
      <li><a href="{{ route('account.wishlist') }}">❤️ {{ __('wishlist') }}</a></li>
      <li><a href="{{ route('account.points') }}">⭐ {{ __('loyalty_title') }}</a></li>
    @else
      <li><a href="{{ route('login') }}">🔐 {{ __('login') }}</a></li>
      <li><a href="{{ route('register') }}">✨ {{ __('register') }}</a></li>
    @endauth
  </ul>
</aside>
<div class="mobile-drawer-backdrop" onclick="document.body.classList.remove('mobile-drawer-open')"></div>

{{-- ALERTS --}}
@if(session('success'))
  <div class="container" style="padding-top:12px;">
    <div class="alert alert-success">✓ {{ session('success') }}</div>
  </div>
@endif
@if(session('error'))
  <div class="container" style="padding-top:12px;">
    <div class="alert alert-error">✗ {{ session('error') }}</div>
  </div>
@endif

{{-- PAGE CONTENT --}}
@yield('content')

{{-- ════ FOOTER ════ --}}
<footer id="contact">
  <div class="container">
    <div class="footer-grid">

      <div class="footer-brand">
        <h3>{{ __('company_name') }}</h3>
        <p>{{ __('footer_about_text') }}</p>
        <div class="footer-contact">
          <a href="tel:+970598191312" dir="ltr">📞 +970 598 191 312</a>
          <a href="mailto:faredahmad615@gmail.com" dir="ltr">📧 faredahmad615@gmail.com</a>
          <a href="#">📍 {{ __('location') }}</a>
        </div>
        <div class="footer-social">
          <a href="https://www.facebook.com/HUDACENTERHEBRON" target="_blank" class="social-btn" title="Facebook">📘</a>
          <a href="https://www.tiktok.com/@huda.center" target="_blank" class="social-btn" title="TikTok">🎵</a>
          <a href="https://wa.me/970598191312" target="_blank" class="social-btn" title="WhatsApp">💬</a>
          <a href="mailto:faredahmad615@gmail.com" class="social-btn" title="Email">📧</a>
        </div>
      </div>

      <div class="footer-col">
        <h4>{{ __('footer_sections') }}</h4>
        <ul>
          <li><a href="{{ route('products.category','makeup') }}">{{ __('makeup') }}</a></li>
          <li><a href="{{ route('products.category','skincare') }}">{{ __('skincare') }}</a></li>
          <li><a href="{{ route('products.category','hair') }}">{{ __('hair') }}</a></li>
          <li><a href="{{ route('products.category','perfume') }}">{{ __('perfume') }}</a></li>
          <li><a href="{{ route('products.category','nails') }}">{{ __('nails') }}</a></li>
          <li><a href="{{ route('products.category','devices') }}">{{ __('devices') }}</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4>{{ __('footer_account') }}</h4>
        <ul>
          <li><a href="{{ route('account.index') }}">{{ __('my_account') }}</a></li>
          <li><a href="{{ route('account.orders') }}">{{ __('footer_orders') }}</a></li>
          <li><a href="{{ route('account.wishlist') }}">{{ __('wishlist') }}</a></li>
          <li><a href="{{ route('account.points') }}">{{ __('footer_points') }}</a></li>
          <li><a href="{{ route('cart.index') }}">{{ __('cart') }}</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4>{{ __('footer_support') }}</h4>
        <ul>
          <li><a href="https://wa.me/970598191312" target="_blank">{{ __('whatsapp') }}</a></li>
          <li><a href="{{ route('contact') }}">{{ __('contact_us') }}</a></li>
          <li><a href="{{ route('returns') }}">{{ __('footer_return_policy') }}</a></li>
          <li><a href="{{ route('shipping') }}">{{ __('footer_shipping') }}</a></li>
          <li><a href="{{ route('faq') }}">{{ __('footer_faq') }}</a></li>
          <li><a href="{{ route('privacy-policy') }}">🔒 {{ __('privacy_policy') }}</a></li>
          <li><a href="{{ route('terms') }}">📜 {{ __('terms_of_use') }}</a></li>
        </ul>
      </div>
    </div>

    {{-- App download strip --}}
    <div class="app-download-strip">
      <div class="app-download-text">
        <span class="app-download-icon">📱</span>
        <div>
          <strong>{{ __('download_app_title') }}</strong>
          <p>{{ __('download_app_sub') }}</p>
        </div>
      </div>
      <div class="app-download-btns">
        <a href="#" class="app-store-btn android" title="Google Play">
          <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M3.18 23.75c.3.17.65.18.96.04l12.47-7.21-2.79-2.79-10.64 9.96zm-1.43-20.5C1.44 3.58 1.25 4 1.25 4.5v15c0 .5.19.92.5 1.25l.07.07 8.4-8.4v-.2L1.75 3.25zm14.63 8.56l-2.49-2.49-2.49 2.49 2.49 2.49 2.49-2.49zm1.24-.71l-2.12 2.12 2.12 2.12 3.26-1.88c.93-.54.93-1.41 0-1.95l-3.26-1.88 0 1.47z"/></svg>
          <div>
            <span>{{ __('download_from') }}</span>
            <strong>Google Play</strong>
          </div>
        </a>
        <a href="#" class="app-store-btn ios" title="App Store">
          <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
          <div>
            <span>{{ __('download_from') }}</span>
            <strong>App Store</strong>
          </div>
        </a>
      </div>
    </div>

    <div class="footer-bottom">
      <div class="footer-bottom-right">
        <span>© {{ date('Y') }} {{ __('footer_copyright') }}</span>
        <span class="footer-sep">·</span>
        <span class="made-with-love">{{ __('made_with_love') }} <a href="https://mohammad-khallaf.vercel.app/" target="_blank" rel="noopener">mohammad khallaf</a></span>
      </div>
      <div class="payment-icons">
        <span class="pay-icon">{{ __('payment_cod') }}</span>
        <span class="pay-icon">{{ __('payment_secure') }}</span>
      </div>
    </div>
  </div>
</footer>

{{-- ════ FLOATING APP DOWNLOAD ════ --}}
<div class="float-app" id="floatApp">
  <button class="float-app-toggle" onclick="document.getElementById('floatApp').classList.toggle('open')" title="{{ __('float_app_toggle_title') }}">
    📱
  </button>
  <div class="float-app-panel">
    <p class="float-app-title">{{ __('float_app_title') }}</p>
    <a href="#" class="float-app-btn android" target="_blank">
      <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M3.18 23.75c.3.17.65.18.96.04l12.47-7.21-2.79-2.79-10.64 9.96zm-1.43-20.5C1.44 3.58 1.25 4 1.25 4.5v15c0 .5.19.92.5 1.25l.07.07 8.4-8.4v-.2L1.75 3.25zm14.63 8.56l-2.49-2.49-2.49 2.49 2.49 2.49 2.49-2.49zm1.24-.71l-2.12 2.12 2.12 2.12 3.26-1.88c.93-.54.93-1.41 0-1.95l-3.26-1.88 0 1.47z"/></svg>
      Google Play
    </a>
    <a href="#" class="float-app-btn ios" target="_blank">
      <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
      App Store
    </a>
  </div>
</div>

{{-- ════ FLOATING SOCIAL WIDGET ════ --}}
<div class="float-social" id="floatSocial">
  <div class="float-social-items">
    <a href="https://www.facebook.com/HUDACENTERHEBRON" target="_blank" class="float-social-btn facebook" title="Facebook">
      📘
      <span class="float-social-label">Facebook</span>
    </a>
    <a href="https://www.tiktok.com/@huda.center" target="_blank" class="float-social-btn tiktok" title="TikTok">
      🎵
      <span class="float-social-label">TikTok</span>
    </a>
    <a href="tel:+970598191312" class="float-social-btn phone" title="{{ __('contact_us') }}">
      📞
      <span class="float-social-label">+970 598 191 312</span>
    </a>
  </div>
  <a href="https://wa.me/970598191312" target="_blank" class="float-wa-btn" title="WhatsApp">
    💬
    <span class="float-social-label">WhatsApp</span>
  </a>
</div>

<script>
// Floating social widget toggle on mobile tap
document.addEventListener('DOMContentLoaded', function() {
  const widget = document.getElementById('floatSocial');
  if (widget) {
    widget.querySelector('.float-wa-btn').addEventListener('click', function(e) {
      // On desktop this opens WhatsApp; on mobile toggle the menu first
      if (window.innerWidth < 768) {
        e.preventDefault();
        widget.classList.toggle('open');
      }
    });
  }
});
</script>

<script>
// Cart AJAX — intercept quick-add and add-btn forms
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.add-to-cart-form').forEach(form => {
    form.addEventListener('submit', async (e) => {
      if (e.submitter && e.submitter.name === 'buy_now') return;
      e.preventDefault();
      const btn = form.querySelector('.quick-add, .add-btn');
      if (!btn) return;
      const orig = btn.innerHTML;
      btn.innerHTML = '⏳';
      btn.disabled = true;

      try {
        const res = await fetch('{{ route("cart.add") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
          },
          body: JSON.stringify(Object.fromEntries(new FormData(form)))
        });
        const data = await res.json();
        if (data.success) {
          btn.innerHTML = '✓';
          btn.style.background = '#10B981';
          document.querySelectorAll('.cart-badge').forEach(b => {
            b.textContent = data.cartCount;
            b.style.display = 'flex';
          });
        }
      } catch(err) { /* silent */ }

      setTimeout(() => {
        btn.innerHTML = orig;
        btn.disabled = false;
        btn.style.background = '';
      }, 1800);
    });
  });
});
</script>

{{-- ── AJAX Add-to-Cart + live badge + toast ── --}}
<div id="cartToast" style="position:fixed;top:90px;left:50%;transform:translateX(-50%) translateY(-20px);background:linear-gradient(135deg,#10B981,#059669);color:#fff;padding:14px 24px;border-radius:12px;font-weight:700;font-size:14px;box-shadow:0 8px 24px rgba(16,185,129,.35);z-index:9999;opacity:0;pointer-events:none;transition:all .3s ease;display:flex;align-items:center;gap:8px;">
  <span id="cartToastIcon">✓</span><span id="cartToastMsg"></span>
</div>
<script>
(function(){
  const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            || document.querySelector('input[name="_token"]')?.value || '';
  const badge = document.getElementById('cartBadge');
  const toast = document.getElementById('cartToast');
  const toastMsg = document.getElementById('cartToastMsg');
  const toastIcon = document.getElementById('cartToastIcon');

  function setBadge(n) {
    if (!badge) return;
    badge.textContent = n;
    badge.style.display = n > 0 ? '' : 'none';
    // pop animation
    badge.style.transform = 'scale(1.4)';
    setTimeout(() => badge.style.transform = 'scale(1)', 200);
  }

  function showToast(msg, ok=true) {
    toastMsg.textContent = msg;
    toastIcon.textContent = ok ? '✓' : '⚠';
    toast.style.background = ok
      ? 'linear-gradient(135deg,#10B981,#059669)'
      : 'linear-gradient(135deg,#EF4444,#DC2626)';
    toast.style.opacity = '1';
    toast.style.transform = 'translateX(-50%) translateY(0)';
    clearTimeout(showToast._t);
    showToast._t = setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(-50%) translateY(-20px)';
    }, 2000);
  }

  document.addEventListener('submit', async function(e){
    const form = e.target.closest('.add-to-cart-form');
    if (!form) return;

    // Buy Now: let the form submit normally so the server can redirect to checkout
    if (e.submitter && e.submitter.name === 'buy_now') return;

    e.preventDefault();

    const btn = form.querySelector('button[type="submit"]');
    const originalHTML = btn ? btn.innerHTML : '';
    if (btn) { btn.disabled = true; btn.innerHTML = '⏳'; }

    try {
      const fd = new FormData(form);
      const res = await fetch(form.action, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': CSRF,
        },
        body: fd,
        credentials: 'same-origin',
      });

      if (!res.ok) throw new Error('HTTP ' + res.status);
      const data = await res.json();

      if (data.success) {
        setBadge(data.cartCount);
        showToast(data.message || @json(__('add_to_cart')) + ' ✓', true);
      } else {
        showToast(data.message || 'Error', false);
      }
    } catch (err) {
      showToast('⚠ ' + err.message, false);
    } finally {
      if (btn) { btn.disabled = false; btn.innerHTML = originalHTML; }
    }
  });
})();
</script>

@stack('scripts')
</body>
</html>
