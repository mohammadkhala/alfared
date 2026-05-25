<x-filament-panels::page>
@php
  $statusColors = ['pending'=>'#F59E0B','confirmed'=>'#3B82F6','processing'=>'#8B5CF6','shipped'=>'#E8711A','delivered'=>'#10B981','cancelled'=>'#EF4444','returned'=>'#EF4444'];
  $statusLabels = ['pending'=>'بانتظار التأكيد','confirmed'=>'تم التأكيد','processing'=>'قيد التجهيز','shipped'=>'في الطريق','delivered'=>'تم التوصيل','cancelled'=>'ملغي','returned'=>'مُرجَّع'];
  $catColors = ['#E8711A','#1B3B8C','#10B981','#8B5CF6','#94A3B8','#F59E0B','#EC4899','#06B6D4'];
@endphp

<div style="direction:rtl;font-family:'Cairo',sans-serif;">

  {{-- ── PERIOD FILTER ── --}}
  <div style="background:white;border-radius:14px;padding:16px 20px;box-shadow:0 2px 8px rgba(0,0,0,.05);margin-bottom:20px;display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
    <span style="font-size:13px;font-weight:700;color:#374151;">الفترة الزمنية:</span>
    @foreach(['today'=>'اليوم','week'=>'هذا الأسبوع','month'=>'هذا الشهر','year'=>'هذا العام'] as $val => $label)
      <button
        wire:click="$set('period','{{ $val }}')"
        style="padding:7px 18px;border-radius:8px;font-size:13px;font-weight:700;font-family:'Cairo',sans-serif;cursor:pointer;border:2px solid {{ $period === $val ? '#1B3B8C' : '#E5E7EB' }};background:{{ $period === $val ? '#1B3B8C' : 'white' }};color:{{ $period === $val ? 'white' : '#6B7280' }};transition:all .2s;"
      >{{ $label }}</button>
    @endforeach
    <span style="margin-right:auto;font-size:12px;color:#9CA3AF;">{{ $periodLabel }}: {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}</span>
  </div>

  {{-- ── KPI ROW ── --}}
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px;">

    <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,.05);position:relative;overflow:hidden;">
      <div style="position:absolute;top:0;right:0;width:4px;height:100%;background:#1B3B8C;"></div>
      <div style="font-size:11px;color:#6B7280;font-weight:700;margin-bottom:8px;">إجمالي المبيعات</div>
      <div style="font-size:26px;font-weight:900;color:#1B3B8C;margin-bottom:4px;">{{ number_format($revenue, 2) }} ₪</div>
      @if($revenueChange != 0)
        <div style="font-size:11px;color:{{ $revenueChange >= 0 ? '#10B981' : '#EF4444' }};">{{ $revenueChange >= 0 ? '↑' : '↓' }} {{ abs($revenueChange) }}% مقارنة بالفترة السابقة</div>
      @else
        <div style="font-size:11px;color:#9CA3AF;">الفترة السابقة: {{ number_format($prevRevenue, 2) }} ₪</div>
      @endif
    </div>

    <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,.05);position:relative;overflow:hidden;">
      <div style="position:absolute;top:0;right:0;width:4px;height:100%;background:#E8711A;"></div>
      <div style="font-size:11px;color:#6B7280;font-weight:700;margin-bottom:8px;">عدد الطلبات</div>
      <div style="font-size:26px;font-weight:900;color:#E8711A;margin-bottom:4px;">{{ number_format($ordersCount) }}</div>
      <div style="font-size:11px;color:#9CA3AF;">طلب مكتمل</div>
    </div>

    <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,.05);position:relative;overflow:hidden;">
      <div style="position:absolute;top:0;right:0;width:4px;height:100%;background:#10B981;"></div>
      <div style="font-size:11px;color:#6B7280;font-weight:700;margin-bottom:8px;">متوسط قيمة الطلب</div>
      <div style="font-size:26px;font-weight:900;color:#10B981;margin-bottom:4px;">{{ number_format($avgOrder, 2) }} ₪</div>
      <div style="font-size:11px;color:#9CA3AF;">لكل طلب</div>
    </div>

    <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,.05);position:relative;overflow:hidden;">
      <div style="position:absolute;top:0;right:0;width:4px;height:100%;background:#8B5CF6;"></div>
      <div style="font-size:11px;color:#6B7280;font-weight:700;margin-bottom:8px;">قطع مبيعة</div>
      <div style="font-size:26px;font-weight:900;color:#8B5CF6;margin-bottom:4px;">{{ number_format($itemsSold) }}</div>
      <div style="font-size:11px;color:#9CA3AF;">إجمالي الكميات</div>
    </div>

  </div>

  {{-- ── SALES CHART ── --}}
  <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,.05);margin-bottom:20px;">
    <h3 style="font-size:14px;font-weight:900;color:#1B3B8C;margin:0 0 16px;">📈 مخطط المبيعات — {{ $periodLabel }}</h3>
    @php $barColors = ['#1B3B8C','rgba(27,59,140,.65)','#E8711A','rgba(27,59,140,.8)','#10B981','rgba(27,59,140,.6)','rgba(232,113,26,.75)']; @endphp
    <div style="display:flex;align-items:flex-end;gap:{{ count($chartData) > 15 ? '3' : '8' }}px;height:180px;overflow-x:auto;">
      @foreach($chartData as $i => $bar)
        <div style="flex-shrink:0;min-width:{{ count($chartData) > 15 ? '24px' : '32px' }};display:flex;flex-direction:column;align-items:center;gap:4px;height:100%;justify-content:flex-end;">
          @if($bar['display'])
            <div style="font-size:9px;font-weight:700;color:#374151;white-space:nowrap;">{{ $bar['display'] }}</div>
          @endif
          <div style="width:100%;border-radius:4px 4px 0 0;background:{{ $barColors[$i % count($barColors)] }};height:{{ $bar['pct'] }}%;min-height:2px;"></div>
          <div style="font-size:9px;color:#6B7280;white-space:nowrap;transform:rotate({{ count($chartData) > 15 ? '-45deg' : '0' }});">{{ $bar['label'] }}</div>
        </div>
      @endforeach
    </div>
  </div>

  {{-- ── TABLES ROW ── --}}
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">

    {{-- Top Products --}}
    <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,.05);">
      <h3 style="font-size:14px;font-weight:900;color:#1B3B8C;margin:0 0 14px;">🏆 أعلى المنتجات مبيعاً</h3>
      <table style="width:100%;border-collapse:collapse;">
        <thead>
          <tr style="background:#F9FAFB;">
            <th style="padding:8px;font-size:11px;color:#6B7280;font-weight:700;text-align:right;">#</th>
            <th style="padding:8px;font-size:11px;color:#6B7280;font-weight:700;text-align:right;">المنتج</th>
            <th style="padding:8px;font-size:11px;color:#6B7280;font-weight:700;text-align:right;">الكمية</th>
            <th style="padding:8px;font-size:11px;color:#6B7280;font-weight:700;text-align:right;">الإيراد</th>
          </tr>
        </thead>
        <tbody>
          @forelse($topProducts as $i => $p)
          <tr style="border-bottom:1px solid #F3F4F6;">
            <td style="padding:9px 8px;font-size:12px;font-weight:700;color:#9CA3AF;">{{ $i+1 }}</td>
            <td style="padding:9px 8px;font-size:12px;color:#374151;font-weight:600;">{{ \Illuminate\Support\Str::limit($p->name_ar, 22) }}</td>
            <td style="padding:9px 8px;font-size:12px;font-weight:700;color:#1B3B8C;">{{ number_format($p->qty) }}</td>
            <td style="padding:9px 8px;font-size:12px;font-weight:700;color:#10B981;">{{ number_format($p->revenue, 0) }} ₪</td>
          </tr>
          @empty
          <tr><td colspan="4" style="text-align:center;padding:20px;color:#9CA3AF;font-size:13px;">لا توجد بيانات</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Sales by Category --}}
    <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,.05);">
      <h3 style="font-size:14px;font-weight:900;color:#1B3B8C;margin:0 0 14px;">🏷️ المبيعات حسب القسم</h3>
      @php $catTotal = $byCategory->sum('total') ?: 1; @endphp
      @forelse($byCategory as $i => $cat)
        @php $pct = round(($cat->total / $catTotal) * 100); $color = $catColors[$i % count($catColors)]; @endphp
        <div style="margin-bottom:12px;">
          <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px;">
            <span style="font-weight:700;color:#374151;">{{ $cat->name_ar }}</span>
            <span style="color:#6B7280;">{{ number_format($cat->total, 0) }} ₪ <span style="color:#9CA3AF;">({{ $pct }}%)</span></span>
          </div>
          <div style="background:#F3F4F6;border-radius:4px;height:8px;overflow:hidden;">
            <div style="height:100%;border-radius:4px;background:{{ $color }};width:{{ $pct }}%;transition:width .5s;"></div>
          </div>
        </div>
      @empty
        <div style="text-align:center;color:#9CA3AF;font-size:13px;padding:20px 0;">لا توجد بيانات</div>
      @endforelse
    </div>

  </div>

  {{-- ── STATUS + CITIES ROW ── --}}
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

    {{-- Order Status Breakdown --}}
    <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,.05);">
      <h3 style="font-size:14px;font-weight:900;color:#1B3B8C;margin:0 0 14px;">📋 توزيع حالات الطلبات</h3>
      @php $totalOrds = $statusBreakdown->sum('count') ?: 1; @endphp
      @forelse($statusBreakdown as $row)
        @php $pct = round(($row->count / $totalOrds) * 100); $color = $statusColors[$row->status] ?? '#9CA3AF'; @endphp
        <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #F3F4F6;">
          <div style="width:10px;height:10px;border-radius:50%;background:{{ $color }};flex-shrink:0;"></div>
          <span style="flex:1;font-size:12px;font-weight:600;color:#374151;">{{ $statusLabels[$row->status] ?? $row->status }}</span>
          <span style="font-size:12px;font-weight:700;color:#1B3B8C;">{{ $row->count }}</span>
          <span style="font-size:11px;color:#9CA3AF;width:36px;text-align:left;">{{ $pct }}%</span>
          <span style="font-size:11px;font-weight:600;color:#10B981;">{{ number_format($row->total, 0) }} ₪</span>
        </div>
      @empty
        <div style="text-align:center;color:#9CA3AF;font-size:13px;padding:20px 0;">لا توجد طلبات في هذه الفترة</div>
      @endforelse
    </div>

    {{-- Top Cities --}}
    <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,.05);">
      <h3 style="font-size:14px;font-weight:900;color:#1B3B8C;margin:0 0 14px;">📍 أعلى المدن مبيعاً</h3>
      @php $cityMax = $topCities->max('revenue') ?: 1; @endphp
      @forelse($topCities as $i => $city)
        @php $pct = round(($city->revenue / $cityMax) * 100); @endphp
        <div style="margin-bottom:10px;">
          <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:3px;">
            <span style="font-weight:700;color:#374151;">{{ $city->city ?: '—' }}</span>
            <span style="color:#6B7280;">{{ $city->count }} طلب &nbsp;·&nbsp; <strong>{{ number_format($city->revenue, 0) }} ₪</strong></span>
          </div>
          <div style="background:#F3F4F6;border-radius:4px;height:6px;overflow:hidden;">
            <div style="height:100%;border-radius:4px;background:#1B3B8C;width:{{ $pct }}%;"></div>
          </div>
        </div>
      @empty
        <div style="text-align:center;color:#9CA3AF;font-size:13px;padding:20px 0;">لا توجد بيانات</div>
      @endforelse
    </div>

  </div>

</div>
</x-filament-panels::page>
