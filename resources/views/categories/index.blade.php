@extends('layouts.app')

@section('title', __('section_categories_title') . ' — ' . __('company_name'))
@section('description', __('section_categories_sub'))

@section('content')
<div class="container" style="padding:32px 0 56px;">

  {{-- Breadcrumb --}}
  <nav style="margin-bottom:28px;font-size:14px;color:#6B7280;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
    <a href="{{ route('home') }}" style="color:#1B3B8C;">{{ __('home') }}</a>
    <span>›</span>
    <span>{{ __('section_categories_title') }}</span>
  </nav>

  {{-- Header --}}
  <div style="text-align:center;margin-bottom:40px;">
    <h1 style="font-size:2rem;font-weight:800;color:#1B3B8C;margin:0 0 10px;">{{ __('section_categories_title') }}</h1>
    <p style="color:#6B7280;font-size:15px;margin:0;">{{ __('section_categories_sub') }}</p>
    <div style="width:60px;height:4px;background:var(--orange,#E8711A);border-radius:2px;margin:16px auto 0;"></div>
  </div>

  @php
  $catFallbackImages = [
    'makeup'   => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=600&q=80',
    'skincare' => 'https://images.unsplash.com/photo-1556228578-8c89e6adf883?w=600&q=80',
    'hair'     => 'https://images.unsplash.com/photo-1527799820374-dcf8d9d4a388?w=600&q=80',
    'perfume'  => 'https://images.unsplash.com/photo-1541643600914-78b084683702?w=600&q=80',
    'nails'    => 'https://images.unsplash.com/photo-1604654894610-df63bc536371?w=600&q=80',
    'devices'  => 'https://images.unsplash.com/photo-1522338242992-e1a54906a8da?w=600&q=80',
  ];
  $defaultCatImage = 'https://images.unsplash.com/photo-1512207736890-6ffed8a84e8d?w=600&q=80';
  @endphp

  {{-- Categories Grid --}}
  <div class="all-cats-grid">
    @forelse($categories as $cat)
      @php
        $catImg = $cat->image
          ? (str_starts_with($cat->image, 'http') ? $cat->image : asset('storage/'.$cat->image))
          : ($catFallbackImages[$cat->slug] ?? $defaultCatImage);
        $count = $cat->products_count ?? 0;
      @endphp
      <a href="{{ route('products.category', $cat->slug) }}" class="all-cat-card">
        <div class="all-cat-img-wrap">
          <img src="{{ $catImg }}" alt="{{ $cat->name }}" loading="lazy"/>
          <div class="all-cat-overlay"></div>
        </div>
        <div class="all-cat-body">
          <h2 class="all-cat-name">{{ $cat->name }}</h2>
          @if($count > 0)
            <span class="all-cat-count">{{ $count }}+ {{ __('products_unit') }}</span>
          @endif
          <span class="all-cat-cta">{{ __('see_all') }}</span>
        </div>
      </a>
    @empty
      <p style="color:#9CA3AF;grid-column:1/-1;text-align:center;padding:40px 0;">{{ __('no_products') }}</p>
    @endforelse
  </div>

</div>

<style>
.all-cats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: 24px;
}

.all-cat-card {
  display: flex;
  flex-direction: column;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0,0,0,.08);
  text-decoration: none;
  transition: transform .25s, box-shadow .25s;
  background: #fff;
}
.all-cat-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 32px rgba(27,59,140,.15);
}

.all-cat-img-wrap {
  position: relative;
  height: 200px;
  overflow: hidden;
}
.all-cat-img-wrap img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform .4s;
}
.all-cat-card:hover .all-cat-img-wrap img {
  transform: scale(1.07);
}
.all-cat-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to bottom, transparent 40%, rgba(27,59,140,.55) 100%);
}

.all-cat-body {
  padding: 18px 20px 22px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.all-cat-name {
  font-size: 1.15rem;
  font-weight: 700;
  color: #1B3B8C;
  margin: 0;
}
.all-cat-count {
  font-size: 13px;
  color: #6B7280;
}
.all-cat-cta {
  display: inline-block;
  margin-top: 8px;
  font-size: 13px;
  font-weight: 600;
  color: #E8711A;
  border: 1.5px solid #E8711A;
  border-radius: 20px;
  padding: 5px 16px;
  width: fit-content;
  transition: background .2s, color .2s;
}
.all-cat-card:hover .all-cat-cta {
  background: #E8711A;
  color: #fff;
}

@media (max-width: 600px) {
  .all-cats-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; }
  .all-cat-img-wrap { height: 150px; }
}
@media (max-width: 380px) {
  .all-cats-grid { grid-template-columns: 1fr; }
}
</style>
@endsection
