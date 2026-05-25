@extends('layouts.app')

@section('title', 'المفضلة — أبناء الفريد')

@section('content')
<div class="page-header" style="background:linear-gradient(135deg,#1B3B8C 0%,#152d6e 100%);padding:32px 0;color:#fff;">
  <div class="container">
    <h1 style="font-size:1.6rem;font-weight:700;margin:0;">♡ المفضلة</h1>
  </div>
</div>

<div class="container" style="padding:40px 0;">
  <div class="account-layout">
    @include('account.partials.sidebar')
    <main class="account-main">
      @if($wishlist->count())
        <div class="products-grid">
          @foreach($wishlist as $item)
            <div style="position:relative;">
              <x-product-card :product="$item->product"/>
              <form action="{{ route('wishlist.toggle', $item->product) }}" method="POST" style="position:absolute;top:8px;inset-inline-start:8px;">
                @csrf
                <button type="submit" style="background:#EF4444;color:#fff;border:none;border-radius:8px;padding:6px 10px;cursor:pointer;font-size:12px;">إزالة</button>
              </form>
            </div>
          @endforeach
        </div>
      @else
        <div style="background:#fff;border-radius:16px;padding:60px;text-align:center;box-shadow:0 4px 20px rgba(27,59,140,.08);">
          <div style="font-size:64px;margin-bottom:16px;opacity:.3;">♡</div>
          <h4 style="color:#1B3B8C;margin-bottom:8px;">قائمة المفضلة فارغة</h4>
          <p style="color:#6B7280;margin-bottom:20px;">أضف المنتجات التي تعجبك لمتابعتها لاحقاً</p>
          <a href="{{ route('products.index') }}" class="btn btn-blue" style="padding:12px 28px;text-decoration:none;border-radius:10px;">تصفح المنتجات</a>
        </div>
      @endif
    </main>
  </div>
</div>
@endsection
