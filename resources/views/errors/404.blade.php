@extends('layouts.app')

@section('title', 'الصفحة غير موجودة — ' . __('company_name'))
@section('description', 'الصفحة التي تبحث عنها غير موجودة أو تم نقلها.')

@section('content')
<div class="container" style="padding-top:56px;padding-bottom:72px;">
  <div class="err-wrap">

    {{-- Illustration --}}
    <div class="err-art" aria-hidden="true">
      <span class="err-code">404</span>
      <div class="err-bag">🛍️</div>
    </div>

    <h1 class="err-title">عذراً، الصفحة غير موجودة</h1>
    <p class="err-sub">
      الرابط الذي فتحته قد يكون قديماً أو تم نقل الصفحة.
      لا تقلق — يمكنك العودة للمتجر ومتابعة التسوّق.
    </p>

    {{-- Search --}}
    <form action="{{ route('products.index') }}" method="GET" class="err-search">
      <input type="text" name="q" placeholder="ابحث عن منتج..." aria-label="ابحث عن منتج"/>
      <button type="submit">بحث</button>
    </form>

    {{-- Actions --}}
    <div class="err-actions">
      <a href="{{ route('home') }}" class="btn btn-blue err-btn">🏠 الصفحة الرئيسية</a>
      <a href="{{ route('products.index') }}" class="btn err-btn err-btn-orange">🛒 تصفّح المتجر</a>
      <a href="{{ route('contact') }}" class="btn err-btn err-btn-ghost">💬 تواصل معنا</a>
    </div>

    {{-- Popular categories --}}
    <div class="err-links">
      <span class="err-links-label">أقسام شائعة</span>
      <div class="err-chips">
        <a href="{{ route('products.category','skincare') }}" class="err-chip">{{ __('skincare') }}</a>
        <a href="{{ route('products.category','makeup') }}" class="err-chip">{{ __('makeup') }}</a>
        <a href="{{ route('products.category','perfume') }}" class="err-chip">{{ __('perfume') }}</a>
        <a href="{{ route('products.category','hair') }}" class="err-chip">{{ __('hair') }}</a>
      </div>
    </div>

  </div>
</div>

@push('styles')
<style>
  .err-wrap { max-width:640px; margin:0 auto; text-align:center; }

  .err-art { position:relative; display:inline-block; margin-bottom:8px; }
  .err-code {
    font-size:clamp(88px,20vw,150px); font-weight:900; line-height:1;
    background:linear-gradient(135deg,var(--blue),var(--orange));
    -webkit-background-clip:text; background-clip:text; color:transparent;
    letter-spacing:2px;
  }
  .err-bag {
    position:absolute; top:-6px; inset-inline-end:-26px;
    font-size:38px; transform:rotate(12deg);
  }

  .err-title { font-size:clamp(20px,4vw,28px); font-weight:900; color:var(--blue); margin:8px 0 10px; }
  .err-sub   { font-size:15px; color:var(--gray); line-height:1.9; margin-bottom:28px; }

  .err-search { display:flex; gap:8px; max-width:420px; margin:0 auto 24px; }
  .err-search input {
    flex:1; padding:13px 16px; border:1.5px solid #E5E7EB; border-radius:12px;
    font-family:'Cairo',sans-serif; font-size:14px; outline:none; transition:border-color .2s;
  }
  .err-search input:focus { border-color:var(--orange); }
  .err-search button {
    padding:13px 24px; border:none; border-radius:12px; cursor:pointer;
    background:var(--blue); color:#fff; font-family:'Cairo',sans-serif;
    font-size:14px; font-weight:800; transition:background .2s;
  }
  .err-search button:hover { background:var(--blue-dk); }

  .err-actions { display:flex; flex-wrap:wrap; gap:10px; justify-content:center; margin-bottom:34px; }
  .err-btn { padding:12px 22px; border-radius:12px; font-size:14px; }
  .err-btn-orange { background:var(--orange); color:#fff; }
  .err-btn-orange:hover { background:#d4610e; color:#fff; }
  .err-btn-ghost { background:var(--gray-bg); color:var(--text); border:1.5px solid #E5E7EB; }
  .err-btn-ghost:hover { background:#EDEFF5; }

  .err-links-label { display:block; font-size:12px; font-weight:800; color:var(--gray); margin-bottom:12px; }
  .err-chips { display:flex; flex-wrap:wrap; gap:8px; justify-content:center; }
  .err-chip {
    padding:8px 16px; border-radius:20px; background:var(--blue-lt);
    color:var(--blue); font-size:13px; font-weight:700; transition:all .2s;
  }
  .err-chip:hover { background:var(--orange-lt); color:var(--orange); }

  @media(max-width:480px) {
    .err-search { flex-direction:column; }
    .err-actions .err-btn { flex:1 1 100%; justify-content:center; }
  }
</style>
@endpush
@endsection
