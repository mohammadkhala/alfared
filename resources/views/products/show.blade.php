@extends('layouts.app')

@section('title', $product->name_ar . ' — أبناء الفريد')
@section('description', $product->description_ar ?? $product->name_ar)

@section('content')
<div class="container" style="padding:32px 0;">

  {{-- Breadcrumb --}}
  <nav class="breadcrumb" style="margin-bottom:24px;font-size:14px;color:#6B7280;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
    <a href="{{ route('home') }}" style="color:#1B3B8C;">الرئيسية</a>
    <span>›</span>
    <a href="{{ route('products.index') }}" style="color:#1B3B8C;">المتجر</a>
    @if($product->category)
      <span>›</span>
      <a href="{{ route('products.category', $product->category->slug) }}" style="color:#1B3B8C;">{{ $product->category->name_ar }}</a>
    @endif
    <span>›</span>
    <span>{{ $product->name_ar }}</span>
  </nav>

  {{-- Product main section --}}
  <div class="product-detail-layout">

    {{-- Gallery --}}
    <div class="product-gallery">
      <div class="gallery-main" id="mainImage">
        <img id="mainImg"
          src="{{ $product->main_image ? asset('storage/'.$product->main_image) : asset('images/placeholder.svg') }}"
          alt="{{ $product->name_ar }}" style="width:100%;height:460px;object-fit:contain;border-radius:16px;background:#F9FAFB;"/>
        @if($product->is_on_sale)
          <div class="product-badge sale" style="position:absolute;top:16px;inset-inline-start:16px;">-{{ $product->discount_percent }}%</div>
        @endif
      </div>
      @if($product->images && count($product->images) > 1)
        <div class="gallery-thumbs">
          @foreach($product->images as $img)
            <img src="{{ asset('storage/'.$img->image_path) }}"
              alt="{{ $product->name_ar }}"
              class="gallery-thumb"
              onclick="document.getElementById('mainImg').src=this.src;document.querySelectorAll('.gallery-thumb').forEach(t=>t.classList.remove('active'));this.classList.add('active')"/>
          @endforeach
        </div>
      @endif
    </div>

    {{-- Product info --}}
    <div class="product-detail-info">
      @if($product->brand)
        <div class="product-brand" style="font-size:13px;color:#E8711A;font-weight:600;margin-bottom:8px;">{{ $product->brand->name }}</div>
      @endif

      <h1 style="font-size:1.8rem;font-weight:700;color:#1B3B8C;line-height:1.3;margin-bottom:12px;">{{ $product->name_ar }}</h1>

      {{-- Rating --}}
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
        <div style="display:flex;gap:2px;">
          @for($i=1;$i<=5;$i++)
            <span style="color:{{ $i<=round($product->rating_avg??0) ? '#F59E0B' : '#D1D5DB' }};font-size:18px;">★</span>
          @endfor
        </div>
        <span style="color:#6B7280;font-size:14px;">{{ number_format($product->rating_avg??0,1) }} ({{ $product->reviews_count??0 }} تقييم)</span>
        <span style="color:#6B7280;font-size:14px;">{{ number_format($product->views_count??0) }} مشاهدة</span>
      </div>

      {{-- Price --}}
      <div style="display:flex;align-items:baseline;gap:16px;margin-bottom:24px;">
        <span style="font-size:2.2rem;font-weight:900;color:#E8711A;">{{ number_format($product->price, 2) }} ₪</span>
        @if($product->is_on_sale && $product->compare_price)
          <span style="font-size:1.2rem;text-decoration:line-through;color:#9CA3AF;">{{ number_format($product->compare_price, 2) }} ₪</span>
          <span style="background:#FEF3C7;color:#D97706;padding:4px 10px;border-radius:20px;font-size:13px;font-weight:600;">وفر {{ number_format($product->compare_price - $product->price, 2) }} ₪</span>
        @endif
      </div>

      {{-- Stock status --}}
      <div style="margin-bottom:20px;">
        @if($product->is_in_stock)
          @if($product->is_low_stock)
            <span style="color:#D97706;font-size:14px;font-weight:600;">⚠️ كمية محدودة — {{ $product->stock_quantity }} متبقية</span>
          @else
            <span style="color:#10B981;font-size:14px;font-weight:600;">✓ متوفر في المخزون</span>
          @endif
        @else
          <span style="color:#EF4444;font-size:14px;font-weight:600;">✗ نفد المخزون</span>
        @endif
      </div>

      {{-- Short description --}}
      @if($product->description_ar)
        <p style="color:#4B5563;line-height:1.7;margin-bottom:24px;font-size:15px;">{{ Str::limit($product->description_ar, 200) }}</p>
      @endif

      {{-- Add to cart form --}}
      @if($product->is_in_stock)
        <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form" id="addToCartForm">
          @csrf
          <input type="hidden" name="product_id" value="{{ $product->id }}"/>

          {{-- Variants --}}
          @if($product->variants && $product->variants->count())
            @php $variantGroups = $product->variants->groupBy('type'); @endphp
            @foreach($variantGroups as $type => $variants)
              <div class="variant-group" style="margin-bottom:16px;">
                <label style="font-size:14px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">
                  {{ $type === 'color' ? 'اللون' : ($type === 'size' ? 'الحجم' : $type) }}:
                  <span id="selected_{{ $type }}" style="color:#E8711A;"></span>
                </label>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                  @foreach($variants as $variant)
                    <button type="button"
                      class="variant-btn {{ $type === 'color' ? 'color-btn' : 'size-btn' }}"
                      data-variant-id="{{ $variant->id }}"
                      data-variant-info="{{ $variant->value }}"
                      data-type="{{ $type }}"
                      data-modifier="{{ $variant->price_modifier }}"
                      onclick="selectVariant(this,'{{ $type }}')"
                      style="@if($type==='color')width:32px;height:32px;border-radius:50%;background:{{ $variant->value }};@else padding:8px 16px;border-radius:8px;background:#F3F4F6;font-size:13px;@endif border:2px solid transparent;cursor:pointer;">
                      @if($type !== 'color'){{ $variant->value }}@endif
                    </button>
                  @endforeach
                </div>
              </div>
            @endforeach
            <input type="hidden" name="variant_id" id="selectedVariantId"/>
            <input type="hidden" name="variant_info" id="selectedVariantInfo"/>
          @endif

          {{-- Quantity --}}
          <div style="margin-bottom:20px;">
            <label style="font-size:14px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">الكمية:</label>
            <div class="qty-selector" style="display:flex;align-items:center;gap:0;border:1px solid #E5E7EB;border-radius:10px;width:fit-content;">
              <button type="button" onclick="changeQty(-1)" style="padding:10px 16px;background:none;border:none;font-size:18px;cursor:pointer;color:#374151;">−</button>
              <input type="number" name="qty" id="qtyInput" value="1" min="1" max="{{ $product->stock_quantity }}"
                style="width:50px;text-align:center;border:none;font-size:16px;font-weight:600;color:#1B3B8C;background:none;"/>
              <button type="button" onclick="changeQty(1)" style="padding:10px 16px;background:none;border:none;font-size:18px;cursor:pointer;color:#374151;">+</button>
            </div>
          </div>

          <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:10px;">
            <button type="submit" class="add-to-cart-btn btn btn-orange" style="flex:1;padding:16px;font-size:16px;min-width:160px;">
              🛒 {{ __('add_to_cart') }}
            </button>
            <form action="{{ route('wishlist.toggle', $product) }}" method="POST" style="display:inline;">
              @csrf
              <button type="submit" style="padding:16px;background:#F3F4F6;border:none;border-radius:12px;cursor:pointer;font-size:20px;" title="أضف للمفضلة">♡</button>
            </form>
          </div>

          {{-- Buy Now: type=button + direct form.submit() bypasses AJAX interceptor --}}
          <input type="hidden" name="buy_now" value="0" id="buyNowFlag"/>
          <button type="button"
            onclick="document.getElementById('buyNowFlag').value='1'; document.getElementById('addToCartForm').submit();"
            style="width:100%;padding:15px;font-size:16px;font-weight:700;background:#1B3B8C;color:#fff;border:none;border-radius:12px;cursor:pointer;letter-spacing:0.3px;">
            ⚡ {{ __('buy_now') }}
          </button>
        </form>
      @else
        <button class="btn" style="background:#9CA3AF;color:#fff;padding:16px;width:100%;border-radius:12px;cursor:not-allowed;">نفد المخزون</button>
      @endif

      {{-- Features --}}
      <div style="margin-top:24px;padding:16px;background:#F0F4FF;border-radius:12px;display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#374151;">
          <span>🚚</span><span>شحن لجميع مناطق فلسطين</span>
        </div>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#374151;">
          <span>✓</span><span>منتجات أصلية 100%</span>
        </div>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#374151;">
          <span>💳</span><span>دفع عند الاستلام</span>
        </div>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#374151;">
          <span>↩️</span><span>سياسة إرجاع مرنة</span>
        </div>
      </div>

      {{-- Share --}}
      <div style="margin-top:16px;display:flex;align-items:center;gap:12px;font-size:13px;color:#6B7280;">
        <span>مشاركة:</span>
        <a href="https://wa.me/?text={{ urlencode($product->name_ar.' '.url()->current()) }}" target="_blank" style="color:#25D366;text-decoration:none;">💬 واتساب</a>
      </div>
    </div>
  </div>

  {{-- Tabs: Description | Specs | Reviews --}}
  <div class="product-tabs" style="margin-top:48px;">
    <div style="display:flex;gap:0;border-bottom:2px solid #E5E7EB;margin-bottom:32px;">
      <button class="tab-btn active" onclick="showTab('desc',this)" style="padding:12px 28px;background:none;border:none;border-bottom:3px solid #1B3B8C;font-size:15px;font-weight:600;color:#1B3B8C;cursor:pointer;margin-bottom:-2px;">الوصف</button>
      <button class="tab-btn" onclick="showTab('specs',this)" style="padding:12px 28px;background:none;border:none;border-bottom:3px solid transparent;font-size:15px;color:#6B7280;cursor:pointer;margin-bottom:-2px;">المواصفات</button>
      <button class="tab-btn" onclick="showTab('reviews',this)" style="padding:12px 28px;background:none;border:none;border-bottom:3px solid transparent;font-size:15px;color:#6B7280;cursor:pointer;margin-bottom:-2px;">التقييمات ({{ $product->reviews_count ?? 0 }})</button>
    </div>

    {{-- Description tab --}}
    <div id="tab-desc" class="tab-content">
      @if($product->description_ar)
        <div style="color:#4B5563;line-height:1.9;font-size:15px;">
          {!! nl2br(e($product->description_ar)) !!}
        </div>
      @else
        <p style="color:#9CA3AF;">لا يوجد وصف لهذا المنتج.</p>
      @endif
    </div>

    {{-- Specs tab --}}
    <div id="tab-specs" class="tab-content" style="display:none;">
      <table style="width:100%;border-collapse:collapse;font-size:14px;">
        @if($product->sku)<tr style="border-bottom:1px solid #E5E7EB;"><td style="padding:12px;color:#6B7280;width:200px;">الرمز</td><td style="padding:12px;font-weight:500;">{{ $product->sku }}</td></tr>@endif
        @if($product->brand)<tr style="border-bottom:1px solid #E5E7EB;"><td style="padding:12px;color:#6B7280;">الماركة</td><td style="padding:12px;font-weight:500;">{{ $product->brand->name }}</td></tr>@endif
        @if($product->category)<tr style="border-bottom:1px solid #E5E7EB;"><td style="padding:12px;color:#6B7280;">الفئة</td><td style="padding:12px;font-weight:500;">{{ $product->category->name_ar }}</td></tr>@endif
        <tr style="border-bottom:1px solid #E5E7EB;"><td style="padding:12px;color:#6B7280;">الحالة</td><td style="padding:12px;font-weight:500;">{{ $product->is_in_stock ? 'متوفر' : 'نفد المخزون' }}</td></tr>
      </table>
    </div>

    {{-- Reviews tab --}}
    <div id="tab-reviews" class="tab-content" style="display:none;">
      @if($product->reviews && $product->reviews->count())
        <div style="display:grid;gap:20px;">
          @foreach($product->reviews->take(10) as $review)
            <div style="background:#F9FAFB;border-radius:12px;padding:20px;">
              <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">
                <div>
                  <span style="font-weight:600;color:#1B3B8C;">{{ $review->reviewer_name ?? ($review->user->name ?? 'عميل') }}</span>
                  <div style="margin-top:4px;">
                    @for($i=1;$i<=5;$i++)
                      <span style="color:{{ $i<=$review->rating ? '#F59E0B' : '#D1D5DB' }};">★</span>
                    @endfor
                  </div>
                </div>
                <span style="font-size:12px;color:#9CA3AF;">{{ $review->created_at->format('d/m/Y') }}</span>
              </div>
              @if($review->comment)
                <p style="color:#4B5563;margin:0;line-height:1.6;">{{ $review->comment }}</p>
              @endif
            </div>
          @endforeach
        </div>
      @else
        <p style="color:#9CA3AF;text-align:center;padding:40px 0;">لا توجد تقييمات بعد. كن أول من يقيّم هذا المنتج!</p>
      @endif

      {{-- Add review form --}}
      @auth
        <div style="margin-top:32px;background:#F9FAFB;border-radius:16px;padding:24px;">
          <h4 style="color:#1B3B8C;margin:0 0 20px;">أضف تقييمك</h4>
          <form action="{{ route('products.review', $product->slug) }}" method="POST">
            @csrf
            <div style="margin-bottom:16px;">
              <label style="font-size:14px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">التقييم</label>
              <div class="star-rating-input" style="display:flex;gap:8px;font-size:28px;">
                @for($i=1;$i<=5;$i++)
                  <label style="cursor:pointer;">
                    <input type="radio" name="rating" value="{{ $i }}" style="display:none;" required/>
                    <span class="star-input" data-val="{{ $i }}" style="color:#D1D5DB;" onmouseover="hoverStar({{$i}})" onmouseout="resetStars()" onclick="selectStar({{$i}})">★</span>
                  </label>
                @endfor
              </div>
            </div>
            <div style="margin-bottom:16px;">
              <label style="font-size:14px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">التعليق (اختياري)</label>
              <textarea name="comment" rows="4" style="width:100%;padding:12px;border:1px solid #E5E7EB;border-radius:10px;font-family:inherit;font-size:14px;resize:vertical;" placeholder="اكتب تجربتك مع المنتج..."></textarea>
            </div>
            <button type="submit" class="btn btn-blue" style="padding:12px 28px;">إرسال التقييم</button>
          </form>
        </div>
      @else
        <div style="text-align:center;padding:24px;background:#F9FAFB;border-radius:12px;margin-top:20px;">
          <a href="{{ route('login') }}" class="btn btn-blue" style="padding:12px 28px;">سجل دخول لإضافة تقييم</a>
        </div>
      @endauth
    </div>
  </div>

  {{-- Related products --}}
  @if($relatedProducts && $relatedProducts->count())
    <div style="margin-top:56px;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <h2 style="font-size:1.5rem;font-weight:700;color:#1B3B8C;margin:0;">منتجات مشابهة</h2>
        @if($product->category)
          <a href="{{ route('products.category', $product->category->slug) }}" style="color:#E8711A;font-weight:600;text-decoration:none;">عرض الكل ›</a>
        @endif
      </div>
      <div class="products-grid">
        @foreach($relatedProducts as $related)
          <x-product-card :product="$related"/>
        @endforeach
      </div>
    </div>
  @endif

</div>
@endsection

@push('scripts')
<script>
function changeQty(delta) {
  const input = document.getElementById('qtyInput');
  const max = parseInt(input.max) || 99;
  input.value = Math.max(1, Math.min(max, parseInt(input.value||1) + delta));
}

function selectVariant(btn, type) {
  document.querySelectorAll('.variant-btn[data-type="'+type+'"]').forEach(b => {
    b.style.borderColor = 'transparent';
  });
  btn.style.borderColor = '#1B3B8C';
  document.getElementById('selectedVariantId').value = btn.dataset.variantId;
  document.getElementById('selectedVariantInfo').value = btn.dataset.variantInfo;
  const sel = document.getElementById('selected_'+type);
  if(sel) sel.textContent = btn.dataset.variantInfo;

  // Update price
  const modifier = parseFloat(btn.dataset.modifier||0);
  const base = {{ $product->price }};
  document.querySelector('.price-display')?.setAttribute('data-total', base+modifier);
}

function showTab(id, btn) {
  document.querySelectorAll('.tab-content').forEach(t => t.style.display='none');
  document.getElementById('tab-'+id).style.display='block';
  document.querySelectorAll('.tab-btn').forEach(b => {
    b.style.borderBottomColor='transparent';
    b.style.color='#6B7280';
  });
  btn.style.borderBottomColor='#1B3B8C';
  btn.style.color='#1B3B8C';
}

let selectedRating = 0;
function selectStar(val) {
  selectedRating = val;
  document.querySelector('input[name="rating"][value="'+val+'"]').checked = true;
  document.querySelectorAll('.star-input').forEach(s => {
    s.style.color = parseInt(s.dataset.val) <= val ? '#F59E0B' : '#D1D5DB';
  });
}
function hoverStar(val) {
  document.querySelectorAll('.star-input').forEach(s => {
    s.style.color = parseInt(s.dataset.val) <= val ? '#F59E0B' : '#D1D5DB';
  });
}
function resetStars() {
  document.querySelectorAll('.star-input').forEach(s => {
    s.style.color = parseInt(s.dataset.val) <= selectedRating ? '#F59E0B' : '#D1D5DB';
  });
}
</script>
@endpush
