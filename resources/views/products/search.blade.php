@extends('layouts.app')

@section('title', 'نتائج البحث: ' . request('q') . ' — أبناء الفريد')

@section('content')
<div class="page-header" style="background:linear-gradient(135deg,#1B3B8C 0%,#152d6e 100%);padding:40px 0;text-align:center;color:#fff;">
  <div class="container">
    <h1 style="font-size:2rem;font-weight:700;margin:0 0 8px;">نتائج البحث</h1>
    <p style="opacity:.8;margin:0;">بحثت عن: <strong>"{{ request('q') }}"</strong> — {{ $products->total() }} نتيجة</p>
  </div>
</div>

<div class="container" style="padding:40px 0;">
  @if($products->count())
    <div class="products-grid">
      @foreach($products as $product)
        <x-product-card :product="$product"/>
      @endforeach
    </div>
    <div style="margin-top:32px;">
      {{ $products->links() }}
    </div>
  @else
    <div style="text-align:center;padding:80px 20px;">
      <div style="font-size:64px;margin-bottom:16px;">🔍</div>
      <h3 style="color:#1B3B8C;margin-bottom:8px;">لم نجد نتائج لـ "{{ request('q') }}"</h3>
      <p style="color:#6B7280;margin-bottom:24px;">تأكد من كتابة الكلمة بشكل صحيح أو جرب كلمة أخرى</p>
      <a href="{{ route('products.index') }}" class="btn btn-blue" style="padding:12px 28px;">تصفح جميع المنتجات</a>
    </div>
  @endif
</div>
@endsection
