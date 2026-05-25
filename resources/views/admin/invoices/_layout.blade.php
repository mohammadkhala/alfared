@php
  $lang = $lang ?? app()->getLocale();
  $dir  = $lang === 'en' ? 'ltr' : 'rtl';
@endphp
<!DOCTYPE html>
<html lang="{{ $lang }}" dir="{{ $dir }}">
<head>
<meta charset="UTF-8"/>
<title>{{ $title ?? __('invoice_title') }} — {{ __('company_name') }}</title>
<style>
  @page { size: A4; margin: 12mm; }
  * { box-sizing: border-box; }
  body { font-family: 'Cairo','Segoe UI',Tahoma,sans-serif; margin:0; padding:0; color:#0F172A; background:#F8FAFC; }

  .toolbar {
    position: sticky; top:0; z-index:10;
    background:#122870; color:#fff; padding:14px 20px;
    display:flex; justify-content:space-between; align-items:center;
    box-shadow:0 2px 8px rgba(0,0,0,.1);
  }
  .toolbar button, .toolbar a {
    background:#E8711A; color:#fff; border:none; border-radius:8px;
    padding:9px 18px; font-family:inherit; font-size:14px; font-weight:700;
    cursor:pointer; text-decoration:none;
  }
  .toolbar a.back { background:rgba(255,255,255,.18); }

  .sheet { max-width:880px; margin:24px auto; }
  .invoice-page {
    background:#fff; border-radius:8px; padding:36px;
    box-shadow:0 6px 24px rgba(0,0,0,.06);
    margin-bottom:24px;
    page-break-after: always;
  }
  .invoice-page:last-child { page-break-after: auto; }

  .invoice-head {
    display:flex; justify-content:space-between; align-items:flex-start;
    border-bottom:2px solid #E8711A; padding-bottom:18px; margin-bottom:24px;
  }
  .brand { display:flex; gap:12px; align-items:center; }
  .logo {
    width:54px; height:54px; border-radius:12px;
    background:linear-gradient(135deg,#122870,#1B3B8C);
    color:#E8711A; font-weight:900; font-size:32px;
    display:flex; align-items:center; justify-content:center;
  }
  .brand-name { font-size:18px; font-weight:900; color:#122870; }
  .brand-meta { font-size:12px; color:#64748B; }

  .meta { text-align:left; }
  .meta-title { font-size:20px; font-weight:900; color:#E8711A; }
  .meta-date { font-size:12px; color:#64748B; margin-top:4px; }
  .meta-status {
    margin-top:8px; display:inline-block;
    background:#FEF3C7; color:#92400E;
    padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700;
  }

  .info-grid {
    display:grid; grid-template-columns:repeat(3,1fr); gap:14px;
    margin-bottom:22px;
  }
  .card {
    background:#F8FAFC; border:1px solid #E5E7EB; border-radius:8px;
    padding:14px; font-size:13px; line-height:1.7;
  }
  .card h4 {
    font-size:12px; color:#122870; margin:0 0 8px;
    border-bottom:1px solid #E5E7EB; padding-bottom:6px;
  }
  .muted { color:#64748B; }
  .small { font-size:11px; }

  table.items-tbl {
    width:100%; border-collapse:collapse; margin-bottom:18px;
    font-size:13px;
  }
  table.items-tbl th {
    background:#122870; color:#fff;
    padding:10px 12px; text-align:right; font-weight:700; font-size:12px;
  }
  table.items-tbl td {
    padding:10px 12px; border-bottom:1px solid #E5E7EB; text-align:right;
  }
  table.items-tbl tbody tr:nth-child(even) td { background:#FAFBFC; }

  .totals {
    display:grid; grid-template-columns:1fr 1fr; gap:20px;
    margin-bottom:18px;
  }
  .totals table {
    width:100%; border-collapse:collapse; font-size:13px;
  }
  .totals td { padding:8px 12px; }
  .totals tr td:first-child { color:#64748B; }
  .totals tr td:last-child { text-align:left; font-weight:700; }
  .totals tr.discount td { color:#10B981; }
  .totals tr.grand td {
    font-size:16px; font-weight:900;
    border-top:2px solid #122870; padding-top:12px;
    color:#122870;
  }
  .totals tr.grand td:last-child { color:#E8711A; }

  .notes {
    background:#FFF9F0; border-inline-start:4px solid #E8711A;
    border-radius:6px; padding:12px 16px; font-size:12px;
    line-height:1.7; margin-bottom:18px;
  }
  .notes div { margin-bottom:4px; }

  .invoice-foot {
    text-align:center; padding-top:18px;
    border-top:1px dashed #E5E7EB;
    font-size:13px; color:#475569;
  }
  .invoice-foot .muted { margin-top:6px; }

  @media print {
    body { background:#fff; }
    .toolbar { display:none; }
    .sheet { margin:0; max-width:none; }
    .invoice-page { box-shadow:none; padding:8mm 6mm; margin:0; border-radius:0; }
  }
</style>
</head>
<body>

<div class="toolbar">
  <a href="{{ url()->previous() }}" class="back">{{ __('invoice_back') }}</a>
  <div style="display:flex;align-items:center;gap:12px;">
    <span style="font-weight:700;">{{ $title ?? __('invoice_title') }}</span>
    <select id="invLangSelect" onchange="changeInvLang(this.value)"
      style="background:rgba(255,255,255,.18);color:#fff;border:1px solid rgba(255,255,255,.35);border-radius:8px;padding:6px 10px;font-family:inherit;font-size:13px;cursor:pointer;">
      <option value="ar" {{ $lang === 'ar' ? 'selected' : '' }}>🇸🇦 العربية</option>
      <option value="he" {{ $lang === 'he' ? 'selected' : '' }}>🇮🇱 עברית</option>
      <option value="en" {{ $lang === 'en' ? 'selected' : '' }}>🇬🇧 English</option>
    </select>
  </div>
  <button onclick="window.print()">{{ __('invoice_print') }}</button>
</div>

<script>
function changeInvLang(lang) {
  const url = new URL(window.location.href);
  url.searchParams.set('lang', lang);
  // remove autoprint flag so the page doesn't print again on reload
  url.searchParams.set('autoprint', '0');
  window.location.href = url.toString();
}
</script>

<div class="sheet">
  {{ $slot ?? '' }}
  @yield('content')
</div>

<script>
  // Auto-open print dialog after page loads (unless user just switched language)
  (function () {
    const params = new URLSearchParams(window.location.search);
    if (params.get('autoprint') !== '0') {
      window.addEventListener('load', () => setTimeout(() => window.print(), 350));
    }
  })();
</script>

</body>
</html>
