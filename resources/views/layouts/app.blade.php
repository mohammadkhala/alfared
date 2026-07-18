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
  <meta property="og:image"       content="{{ \App\Support\SiteBranding::logo() }}"/>
  <meta property="og:locale"      content="ar_PS"/>
  <meta property="og:site_name"   content="شركة أبناء الفريد"/>

  {{-- Twitter Card --}}
  <meta name="twitter:card"        content="summary"/>
  <meta name="twitter:title"       content="@yield('title', __('company_name'))"/>
  <meta name="twitter:description" content="@yield('description', __('tagline'))"/>
  <meta name="twitter:image"       content="{{ \App\Support\SiteBranding::logo() }}"/>

  {{-- Robots --}}
  <meta name="robots" content="index, follow"/>

  <link rel="icon" href="{{ \App\Support\SiteBranding::favicon() }}"/>
  <link rel="shortcut icon" href="{{ \App\Support\SiteBranding::favicon() }}"/>
  <link rel="sitemap" type="application/xml" href="{{ url('/sitemap.xml') }}"/>

  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="{{ asset('css/store.css') }}?v={{ @filemtime(public_path('css/store.css')) ?: '1' }}"/>
  @stack('styles')

  <!-- TikTok Pixel Code Start -->
  <script>
  !function (w, d, t) {
    w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie","holdConsent","revokeConsent","grantConsent"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(
  var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var r="https://analytics.tiktok.com/i18n/pixel/events.js",o=n&&n.partner;ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=r,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};n=document.createElement("script")
  ;n.type="text/javascript",n.async=!0,n.src=r+"?sdkid="+e+"&lib="+t;e=document.getElementsByTagName("script")[0];e.parentNode.insertBefore(n,e)};
    ttq.load('D8HV653C77UFK9KE0MB0');
    ttq.page();
  }(window, document, 'ttq');
  </script>
  <!-- TikTok Pixel Code End -->
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
      <a href="https://www.facebook.com/HUDACENTERHEBRON" target="_blank" class="topbar-tiktok" style="display:inline-flex;align-items:center;gap:4px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.884v2.25h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/></svg>
        Facebook
      </a>
      <span class="topbar-sep">|</span>
      <a href="https://www.tiktok.com/@huda.center" target="_blank" class="topbar-tiktok" style="display:inline-flex;align-items:center;gap:4px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.78 1.52V6.76a4.85 4.85 0 01-1.01-.07z"/></svg>
        TikTok
      </a>
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
        <img src="{{ \App\Support\SiteBranding::logo() }}" alt="{{ __('company_name') }}" class="logo-img"/>
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
          <a href="https://www.facebook.com/HUDACENTERHEBRON" target="_blank" rel="noopener" class="social-btn sb-facebook" title="Facebook" aria-label="Facebook"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.412c0-3.025 1.792-4.697 4.533-4.697 1.313 0 2.686.236 2.686.236v2.971H15.83c-1.491 0-1.956.931-1.956 1.886v2.265h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/></svg></a>
          <a href="https://www.tiktok.com/@huda.center" target="_blank" rel="noopener" class="social-btn sb-tiktok" title="TikTok" aria-label="TikTok"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg></a>
          <a href="https://wa.me/970598191312" target="_blank" rel="noopener" class="social-btn sb-whatsapp" title="WhatsApp" aria-label="WhatsApp"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg></a>
          <a href="mailto:faredahmad615@gmail.com" class="social-btn sb-email" title="Email" aria-label="Email"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2zm0 4.24l-8 4.99-8-4.99V6l8 4.99L20 6v2.24z"/></svg></a>
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
          <li><a href="{{ route('track.form') }}">📦 {{ __('track_page_title') }}</a></li>
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
        <a href="https://play.google.com/store/apps/details?id=pss.alfared.shop&pcampaignid=web_share" target="_blank" rel="noopener" class="app-store-btn android" title="Google Play">
          <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M3.18 23.75c.3.17.65.18.96.04l12.47-7.21-2.79-2.79-10.64 9.96zm-1.43-20.5C1.44 3.58 1.25 4 1.25 4.5v15c0 .5.19.92.5 1.25l.07.07 8.4-8.4v-.2L1.75 3.25zm14.63 8.56l-2.49-2.49-2.49 2.49 2.49 2.49 2.49-2.49zm1.24-.71l-2.12 2.12 2.12 2.12 3.26-1.88c.93-.54.93-1.41 0-1.95l-3.26-1.88 0 1.47z"/></svg>
          <div>
            <span>{{ __('download_from') }}</span>
            <strong>Google Play</strong>
          </div>
        </a>
        <a href="https://apps.apple.com/ru/app/%D8%B4%D8%B1%D9%83%D8%A9-%D8%A7%D8%A8%D9%86%D8%A7%D8%A1-%D8%A7%D9%84%D9%81%D8%B1%D9%8A%D8%AF/id6776633367?l=en-GB" target="_blank" rel="noopener" class="app-store-btn ios" title="App Store">
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
        <span class="made-with-love">{{ __('made_with_love') }} <a href="https://wa.me/972594513978" target="_blank" rel="noopener">mohammad khallaf</a></span>
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
    <a href="https://play.google.com/store/apps/details?id=pss.alfared.shop&pcampaignid=web_share" class="float-app-btn android" target="_blank" rel="noopener">
      <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M3.18 23.75c.3.17.65.18.96.04l12.47-7.21-2.79-2.79-10.64 9.96zm-1.43-20.5C1.44 3.58 1.25 4 1.25 4.5v15c0 .5.19.92.5 1.25l.07.07 8.4-8.4v-.2L1.75 3.25zm14.63 8.56l-2.49-2.49-2.49 2.49 2.49 2.49 2.49-2.49zm1.24-.71l-2.12 2.12 2.12 2.12 3.26-1.88c.93-.54.93-1.41 0-1.95l-3.26-1.88 0 1.47z"/></svg>
      Google Play
    </a>
    <a href="https://apps.apple.com/ru/app/%D8%B4%D8%B1%D9%83%D8%A9-%D8%A7%D8%A8%D9%86%D8%A7%D8%A1-%D8%A7%D9%84%D9%81%D8%B1%D9%8A%D8%AF/id6776633367?l=en-GB" class="float-app-btn ios" target="_blank" rel="noopener">
      <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
      App Store
    </a>
  </div>
</div>

{{-- ════ FLOATING SOCIAL WIDGET ════ --}}
<div class="float-social" id="floatSocial">
  <div class="float-social-items">
    <a href="https://www.facebook.com/HUDACENTERHEBRON" target="_blank" class="float-social-btn facebook" title="Facebook">
      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="#fff"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.884v2.25h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/></svg>
      <span class="float-social-label">Facebook</span>
    </a>
    <a href="https://www.tiktok.com/@huda.center" target="_blank" class="float-social-btn tiktok" title="TikTok">
      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="#fff"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.78 1.52V6.76a4.85 4.85 0 01-1.01-.07z"/></svg>
      <span class="float-social-label">TikTok</span>
    </a>
    <a href="tel:+970598191312" class="float-social-btn phone" title="{{ __('contact_us') }}">
      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.09 9.81a19.79 19.79 0 01-3.07-8.67A2 2 0 012 .99h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 8.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
      <span class="float-social-label">+970 598 191 312</span>
    </a>
  </div>
  <a href="https://wa.me/970598191312" target="_blank" class="float-wa-btn" title="WhatsApp">
    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="#fff"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
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
