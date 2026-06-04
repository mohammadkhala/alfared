@extends('layouts.app')

@section('content')
<div class="legal-page">
  <div class="container">

    {{-- Breadcrumb --}}
    <nav class="legal-breadcrumb">
      <a href="{{ route('home') }}">🏠 الرئيسية</a>
      <span>›</span>
      <span>@yield('title')</span>
    </nav>

    {{-- Page Header --}}
    <div class="legal-header">
      <div class="legal-header-icon">@yield('icon', '📄')</div>
      <div>
        <h1>@yield('title')</h1>
        <p class="legal-date">آخر تحديث: يونيو 2026 &nbsp;·&nbsp; شركة أبناء الفريد — فلسطين</p>
      </div>
    </div>

    {{-- Content --}}
    <div class="legal-content">
      @yield('page-content')
    </div>

    {{-- Footer links --}}
    <div class="legal-footer-links">
      <a href="{{ route('privacy-policy') }}" class="{{ request()->routeIs('privacy-policy') ? 'active' : '' }}">🔒 سياسة الخصوصية</a>
      <a href="{{ route('returns') }}"        class="{{ request()->routeIs('returns')        ? 'active' : '' }}">🔄 سياسة الإرجاع</a>
      <a href="{{ route('shipping') }}"       class="{{ request()->routeIs('shipping')       ? 'active' : '' }}">🚚 الشحن والتوصيل</a>
      <a href="{{ route('terms') }}"          class="{{ request()->routeIs('terms')          ? 'active' : '' }}">📜 شروط الاستخدام</a>
      <a href="{{ route('faq') }}"            class="{{ request()->routeIs('faq')            ? 'active' : '' }}">❓ الأسئلة الشائعة</a>
    </div>

  </div>
</div>

<style>
/* ── Legal Page Styles ── */
.legal-page {
  background: var(--gray-bg);
  min-height: 60vh;
  padding: 40px 0 64px;
}

.legal-breadcrumb {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: var(--gray);
  margin-bottom: 28px;
}
.legal-breadcrumb a {
  color: var(--blue);
  font-weight: 600;
  transition: color .2s;
}
.legal-breadcrumb a:hover { color: var(--orange); }
.legal-breadcrumb span { color: #CBD5E1; }

/* ── Header ── */
.legal-header {
  display: flex;
  align-items: center;
  gap: 20px;
  background: white;
  border-radius: 16px;
  padding: 28px 32px;
  margin-bottom: 28px;
  box-shadow: 0 2px 12px rgba(27,59,140,.06);
  border-right: 4px solid var(--orange);
}
.legal-header-icon {
  font-size: 44px;
  line-height: 1;
  flex-shrink: 0;
}
.legal-header h1 {
  font-size: 26px;
  font-weight: 900;
  color: var(--blue);
  margin-bottom: 4px;
}
.legal-date {
  font-size: 12px;
  color: var(--gray);
}

/* ── Content area ── */
.legal-content {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

/* ── Section card ── */
.ls {
  background: white;
  border-radius: 14px;
  box-shadow: 0 2px 10px rgba(27,59,140,.05);
  overflow: hidden;
}

.ls-head {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px 24px;
  background: var(--blue-lt);
  border-bottom: 1px solid rgba(27,59,140,.08);
}
.ls-head h2 {
  font-size: 14px;
  font-weight: 800;
  color: var(--blue);
}
.ls-head-icon { font-size: 18px; }

.ls-body {
  padding: 20px 24px;
}
.ls-body p {
  font-size: 14px;
  color: #374151;
  line-height: 1.9;
  margin-bottom: 10px;
}
.ls-body p:last-child { margin-bottom: 0; }

.ls-body ul {
  list-style: none;
  padding: 0;
  margin: 0;
}
.ls-body ul li {
  font-size: 14px;
  color: #374151;
  padding: 7px 20px 7px 0;
  border-bottom: 1px solid #F3F4F6;
  position: relative;
  line-height: 1.7;
}
.ls-body ul li:last-child { border-bottom: none; }
.ls-body ul li::before {
  content: '›';
  position: absolute;
  right: 0;
  color: var(--orange);
  font-weight: 900;
  font-size: 16px;
}

.ls-highlight {
  background: var(--orange-lt);
  border: 1px solid rgba(232,113,26,.2);
  border-radius: 10px;
  padding: 12px 16px;
  font-size: 13px;
  color: #374151;
  margin-top: 12px;
  line-height: 1.8;
}
.ls-highlight strong { color: var(--orange); }

/* ── Contact chips ── */
.ls-contact-row {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  margin-top: 10px;
}
.ls-chip {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 8px 16px;
  background: var(--blue-lt);
  border: 1px solid rgba(27,59,140,.12);
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  color: var(--blue);
  text-decoration: none;
  transition: all .2s;
}
.ls-chip:hover {
  background: var(--orange-lt);
  border-color: rgba(232,113,26,.3);
  color: var(--orange);
}

/* ── Footer links ── */
.legal-footer-links {
  display: flex;
  justify-content: center;
  gap: 8px;
  flex-wrap: wrap;
  margin-top: 32px;
  padding-top: 24px;
  border-top: 1px solid #E5E7EB;
}
.legal-footer-links a {
  padding: 8px 18px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  color: var(--gray);
  background: white;
  border: 1.5px solid #E5E7EB;
  transition: all .2s;
  text-decoration: none;
}
.legal-footer-links a:hover,
.legal-footer-links a.active {
  color: var(--orange);
  border-color: var(--orange);
  background: var(--orange-lt);
}

@media (max-width: 640px) {
  .legal-header { padding: 20px; gap: 14px; }
  .legal-header h1 { font-size: 20px; }
  .legal-header-icon { font-size: 34px; }
  .ls-body { padding: 16px; }
  .ls-head { padding: 12px 16px; }
}
</style>
@endsection
