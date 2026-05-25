<div class="alfared-charts-grid" style="display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-bottom:0;">

  {{-- ── BAR CHART ── --}}
  <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,.06);">
    <h3 style="font-size:15px;font-weight:900;color:#1B3B8C;margin-bottom:16px;display:flex;justify-content:space-between;align-items:center;">
      📈 المبيعات اليومية (آخر 7 أيام)
    </h3>
    <div style="display:flex;align-items:flex-end;gap:8px;height:160px;">
      @foreach($daily as $i => $day)
        @php
          $barColors = ['#1B3B8C','rgba(27,59,140,.7)','#E8711A','rgba(27,59,140,.8)','#10B981','rgba(27,59,140,.6)','rgba(232,113,26,.8)'];
          $color = $barColors[$i % count($barColors)];
        @endphp
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:6px;height:100%;justify-content:flex-end;">
          <div style="font-size:10px;font-weight:700;color:#374151;">{{ $day['display'] }}</div>
          <div style="width:100%;border-radius:6px 6px 0 0;background:{{ $color }};height:{{ $day['pct'] }}%;min-height:4px;transition:height .3s;"></div>
          <div style="font-size:10px;color:#6B7280;">{{ $day['label'] }}</div>
        </div>
      @endforeach
    </div>
  </div>

  {{-- ── DONUT CHART ── --}}
  <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,.06);">
    <h3 style="font-size:15px;font-weight:900;color:#1B3B8C;margin-bottom:16px;">🏷️ المبيعات حسب القسم</h3>
    <div style="display:flex;flex-direction:column;align-items:center;">

      {{-- Donut --}}
      <div style="width:120px;height:120px;border-radius:50%;background:conic-gradient({{ $donutGradient }});position:relative;margin-bottom:16px;flex-shrink:0;">
        <div style="position:absolute;inset:24px;background:white;border-radius:50%;"></div>
      </div>

      {{-- Legend --}}
      <div style="width:100%;">
        @forelse($categories as $cat)
          <div style="display:flex;align-items:center;gap:8px;font-size:12px;margin-bottom:6px;">
            <div style="width:10px;height:10px;border-radius:50%;background:{{ $cat['color'] }};flex-shrink:0;"></div>
            <span style="flex:1;color:#374151;">{{ $cat['name'] }}</span>
            <span style="font-weight:700;color:#374151;">{{ $cat['pct'] }}%</span>
          </div>
        @empty
          <div style="text-align:center;color:#94A3B8;font-size:13px;">لا توجد مبيعات بعد</div>
        @endforelse
      </div>

    </div>
  </div>

</div>
