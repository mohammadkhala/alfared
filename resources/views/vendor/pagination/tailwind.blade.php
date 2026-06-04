@if ($paginator->hasPages())
<nav dir="rtl" aria-label="pagination" style="display:flex;flex-direction:column;align-items:center;gap:12px;margin-top:32px;">

  {{-- Mobile: prev / next only --}}
  <div style="display:flex;gap:8px;" class="pagination-mobile">
    @if ($paginator->onFirstPage())
      <span style="padding:10px 18px;background:#F3F4F6;color:#9CA3AF;border-radius:10px;font-size:14px;cursor:not-allowed;font-family:Cairo,sans-serif;">
        ➔ السابق
      </span>
    @else
      <a href="{{ $paginator->previousPageUrl() }}"
         style="padding:10px 18px;background:#fff;color:#1B3B8C;border:2px solid #1B3B8C;border-radius:10px;font-size:14px;font-weight:700;font-family:Cairo,sans-serif;text-decoration:none;transition:background .2s;"
         onmouseover="this.style.background='#1B3B8C';this.style.color='#fff'"
         onmouseout="this.style.background='#fff';this.style.color='#1B3B8C'">
        ➔ السابق
      </a>
    @endif

    @if ($paginator->hasMorePages())
      <a href="{{ $paginator->nextPageUrl() }}"
         style="padding:10px 18px;background:#fff;color:#1B3B8C;border:2px solid #1B3B8C;border-radius:10px;font-size:14px;font-weight:700;font-family:Cairo,sans-serif;text-decoration:none;transition:background .2s;"
         onmouseover="this.style.background='#1B3B8C';this.style.color='#fff'"
         onmouseout="this.style.background='#fff';this.style.color='#1B3B8C'">
        التالي ←
      </a>
    @else
      <span style="padding:10px 18px;background:#F3F4F6;color:#9CA3AF;border-radius:10px;font-size:14px;cursor:not-allowed;font-family:Cairo,sans-serif;">
        التالي ←
      </span>
    @endif
  </div>

  {{-- Desktop: numbered pages --}}
  <div style="display:flex;flex-wrap:wrap;gap:6px;justify-content:center;" class="pagination-desktop">

    {{-- Previous button --}}
    @if ($paginator->onFirstPage())
      <span style="padding:8px 14px;background:#F3F4F6;color:#9CA3AF;border-radius:8px;font-size:14px;cursor:not-allowed;">➔</span>
    @else
      <a href="{{ $paginator->previousPageUrl() }}"
         style="padding:8px 14px;background:#fff;color:#1B3B8C;border:1.5px solid #E5E7EB;border-radius:8px;font-size:14px;font-weight:600;text-decoration:none;"
         onmouseover="this.style.borderColor='#1B3B8C'"
         onmouseout="this.style.borderColor='#E5E7EB'">➔</a>
    @endif

    {{-- Page numbers --}}
    @foreach ($elements as $element)
      @if (is_string($element))
        <span style="padding:8px 14px;color:#9CA3AF;font-size:14px;">…</span>
      @endif

      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <span style="padding:8px 14px;background:#1B3B8C;color:#fff;border-radius:8px;font-size:14px;font-weight:700;min-width:36px;text-align:center;">{{ $page }}</span>
          @else
            <a href="{{ $url }}"
               style="padding:8px 14px;background:#fff;color:#374151;border:1.5px solid #E5E7EB;border-radius:8px;font-size:14px;text-decoration:none;min-width:36px;text-align:center;"
               onmouseover="this.style.borderColor='#1B3B8C';this.style.color='#1B3B8C'"
               onmouseout="this.style.borderColor='#E5E7EB';this.style.color='#374151'">{{ $page }}</a>
          @endif
        @endforeach
      @endif
    @endforeach

    {{-- Next button --}}
    @if ($paginator->hasMorePages())
      <a href="{{ $paginator->nextPageUrl() }}"
         style="padding:8px 14px;background:#fff;color:#1B3B8C;border:1.5px solid #E5E7EB;border-radius:8px;font-size:14px;font-weight:600;text-decoration:none;"
         onmouseover="this.style.borderColor='#1B3B8C'"
         onmouseout="this.style.borderColor='#E5E7EB'">←</a>
    @else
      <span style="padding:8px 14px;background:#F3F4F6;color:#9CA3AF;border-radius:8px;font-size:14px;cursor:not-allowed;">←</span>
    @endif

  </div>

  {{-- Count info --}}
  <p style="font-size:13px;color:#9CA3AF;font-family:Cairo,sans-serif;margin:0;">
    عرض {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} من {{ $paginator->total() }} منتج
  </p>

</nav>
@endif
