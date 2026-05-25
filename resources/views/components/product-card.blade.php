@props(['product'])

@php
  $saveAmount = ($product->is_on_sale && $product->compare_price)
    ? number_format($product->compare_price - $product->price, 0)
    : null;
  $rating = round($product->rating_avg ?? 0);
@endphp

<div class="product-card">

  {{-- IMAGE AREA --}}
  <div class="product-img">
    <a href="{{ route('products.show', $product->slug) }}" class="product-img-link">
      <img
        src="{{ $product->main_image ? asset('storage/'.$product->main_image) : asset('images/placeholder.svg') }}"
        alt="{{ $product->name }}"
        loading="lazy"
      />
    </a>

    {{-- BADGES --}}
    <div class="card-badges">
      @if(!$product->is_in_stock)
        <span class="badge-out">{{ __('out_of_stock') }}</span>
      @elseif($product->is_on_sale)
        <span class="badge-sale">{{ $product->discount_percent }}%−</span>
      @elseif($product->is_new ?? false)
        <span class="badge-new">جديد</span>
      @endif

      @if($product->is_in_stock && ($product->is_low_stock ?? false))
        <span class="badge-low">كمية محدودة</span>
      @endif
    </div>

    {{-- WISHLIST --}}
    @auth
      <form action="{{ route('wishlist.toggle', $product) }}" method="POST" class="wish-form">
        @csrf
        <button type="submit" class="wish-btn" title="{{ __('wishlist') }}">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        </button>
      </form>
    @else
      <a href="{{ route('login') }}" class="wish-btn" title="{{ __('wishlist') }}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
      </a>
    @endauth

    {{-- BUY NOW (hover shortcut → product page) --}}
    @if($product->is_in_stock)
      <div class="quick-add-wrap">
        <a href="{{ route('products.show', $product->slug) }}" class="quick-add quick-buy">
          <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          اشتري الآن
        </a>
      </div>
    @endif
  </div>

  {{-- INFO AREA --}}
  <div class="product-info">

    {{-- Brand --}}
    @if($product->brand)
      <div class="product-brand">{{ $product->brand->name }}</div>
    @endif

    {{-- Name --}}
    <div class="product-name">
      <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
    </div>

    {{-- Stars --}}
    <div class="stars">
      @for($i=1;$i<=5;$i++)
        <span class="{{ $i <= $rating ? 'star-fill' : 'star-empty' }}">★</span>
      @endfor
      @if(($product->reviews_count ?? 0) > 0)
        <span class="reviews-count">({{ $product->reviews_count }})</span>
      @endif
    </div>

    {{-- Price row --}}
    <div class="product-price-row">
      <div class="price-group">
        <span class="price-main">{{ number_format($product->price, 0) }} ₪</span>
        @if($product->is_on_sale && $product->compare_price)
          <span class="price-old">{{ number_format($product->compare_price, 0) }} ₪</span>
        @endif
      </div>
      @if($saveAmount)
        <span class="price-save">وفّر {{ $saveAmount }} ₪</span>
      @endif
    </div>

    {{-- Add to cart --}}
    @if($product->is_in_stock)
      <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}"/>
        <input type="hidden" name="qty" value="1"/>
        <button type="submit" class="add-btn">
          <span class="add-btn-icon">+</span>
          {{ __('add_to_cart') }}
        </button>
      </form>
    @else
      <button class="add-btn add-btn-disabled" disabled>{{ __('out_of_stock') }}</button>
    @endif

  </div>
</div>
