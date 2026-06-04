@extends('layouts.app')

@section('title', __('store_page_title'))

@section('content')
<div class="page-header" style="background:linear-gradient(135deg,#1B3B8C 0%,#152d6e 100%);padding:40px 0;text-align:center;color:#fff;">
  <div class="container">
    <h1 style="font-size:2rem;font-weight:700;margin:0 0 8px;">{{ __('store') }}</h1>
    <p style="opacity:.8;margin:0;">{{ __('store_tagline') }}</p>
  </div>
</div>

<div class="container" style="padding:40px 0;">
  <div class="shop-layout">

    {{-- SIDEBAR FILTERS --}}
    <aside class="shop-sidebar">
      <div class="filter-card">
        <h3 class="filter-title">{{ __('filter_results') }}</h3>

        <form id="filterForm" action="{{ route('products.index') }}" method="GET">
          @if(request('q'))
            <input type="hidden" name="q" value="{{ request('q') }}"/>
          @endif

          {{-- Category --}}
          <div class="filter-section">
            <h4>{{ __('filter_category') }}</h4>
            <div class="filter-options">
              @foreach($categories as $cat)
                <label class="filter-checkbox">
                  <input type="checkbox" name="category[]" value="{{ $cat->slug }}"
                    {{ in_array($cat->slug, (array)request('category')) ? 'checked' : '' }}
                    onchange="document.getElementById('filterForm').submit()"/>
                  <span>{{ $cat->name }}</span>
                  <span class="filter-count">{{ $cat->products_count ?? 0 }}</span>
                </label>
              @endforeach
            </div>
          </div>

          {{-- Brand --}}
          @if($brands->count())
          <div class="filter-section">
            <h4>{{ __('filter_brand') }}</h4>
            <div class="filter-options">
              @foreach($brands as $brand)
                <label class="filter-checkbox">
                  <input type="checkbox" name="brand[]" value="{{ $brand->id }}"
                    {{ in_array($brand->id, (array)request('brand')) ? 'checked' : '' }}
                    onchange="document.getElementById('filterForm').submit()"/>
                  <span>{{ $brand->name }}</span>
                </label>
              @endforeach
            </div>
          </div>
          @endif

          {{-- Price range --}}
          <div class="filter-section">
            <h4>{{ __('filter_price_range') }}</h4>
            <div style="display:flex;gap:8px;align-items:center;">
              <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="{{ __('filter_from') }}" class="price-input" min="0" step="0.5"/>
              <span style="color:#6B7280;">—</span>
              <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="{{ __('filter_to') }}" class="price-input" min="0" step="0.5"/>
            </div>
          </div>

          {{-- Rating --}}
          <div class="filter-section">
            <h4>{{ __('filter_rating') }}</h4>
            @for($r=5;$r>=1;$r--)
              <label class="filter-checkbox">
                <input type="radio" name="rating" value="{{ $r }}"
                  {{ request('rating') == $r ? 'checked' : '' }}
                  onchange="document.getElementById('filterForm').submit()"/>
                <span>
                  @for($s=1;$s<=5;$s++)
                    <span style="color:{{ $s<=$r ? '#F59E0B' : '#D1D5DB' }};">★</span>
                  @endfor
                  {{ __('filter_and_above') }}
                </span>
              </label>
            @endfor
          </div>

          {{-- Availability --}}
          <div class="filter-section">
            <label class="filter-checkbox">
              <input type="checkbox" name="in_stock" value="1"
                {{ request('in_stock') ? 'checked' : '' }}
                onchange="document.getElementById('filterForm').submit()"/>
              <span>{{ __('in_stock_only') }}</span>
            </label>
            <label class="filter-checkbox" style="margin-top:8px;">
              <input type="checkbox" name="on_sale" value="1"
                {{ request('on_sale') ? 'checked' : '' }}
                onchange="document.getElementById('filterForm').submit()"/>
              <span>{{ __('on_sale_only') }}</span>
            </label>
          </div>

          <div style="display:flex;gap:8px;margin-top:12px;">
            <button type="submit" class="btn btn-blue" style="flex:1;padding:10px;">{{ __('apply') }}</button>
            <a href="{{ route('products.index') }}" class="btn" style="flex:1;padding:10px;background:#F3F4F6;color:#374151;text-align:center;">{{ __('clear') }}</a>
          </div>
        </form>
      </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="shop-main">

      {{-- Sort bar --}}
      <div class="sort-bar">
        <div class="results-count">
          {{ __('showing') }} <strong>{{ $products->total() }}</strong> {{ __('products_unit') }}
          @if(request('q'))
            {{ __('for_search') }} "<strong>{{ request('q') }}</strong>"
          @endif
        </div>
        <div class="sort-controls">
          <label style="font-size:14px;color:#6B7280;">{{ __('sort_by') }}</label>
          <select name="sort" class="sort-select" onchange="location.href=updateQueryParam('sort',this.value)">
            <option value="newest"     {{ request('sort','newest')==='newest'    ?'selected':'' }}>{{ __('sort_newest') }}</option>
            <option value="price_asc"  {{ request('sort')==='price_asc'          ?'selected':'' }}>{{ __('sort_price_asc') }}</option>
            <option value="price_desc" {{ request('sort')==='price_desc'         ?'selected':'' }}>{{ __('sort_price_desc') }}</option>
            <option value="rating"     {{ request('sort')==='rating'             ?'selected':'' }}>{{ __('sort_rating') }}</option>
            <option value="bestseller" {{ request('sort')==='bestseller'         ?'selected':'' }}>{{ __('sort_bestseller') }}</option>
          </select>
        </div>
      </div>

      {{-- Products grid --}}
      @if($products->count())
        <div class="products-grid">
          @foreach($products as $product)
            <x-product-card :product="$product"/>
          @endforeach
        </div>
        <div class="pagination-wrap" style="margin-top:32px;">
          {{ $products->links() }}
        </div>
      @else
        <div class="empty-state" style="text-align:center;padding:80px 20px;">
          <div style="font-size:64px;margin-bottom:16px;">🔍</div>
          <h3 style="color:#1B3B8C;margin-bottom:8px;">{{ __('no_products') }}</h3>
          <p style="color:#6B7280;">{{ __('no_products_hint') }}</p>
          <a href="{{ route('products.index') }}" class="btn btn-blue" style="margin-top:20px;display:inline-block;padding:12px 28px;">{{ __('view_all_products') }}</a>
        </div>
      @endif
    </main>
  </div>
</div>
@endsection

@push('scripts')
<script>
function updateQueryParam(key, value) {
  const url = new URL(window.location.href);
  url.searchParams.set(key, value);
  url.searchParams.delete('page');
  return url.toString();
}
</script>
@endpush
