<x-filament-panels::page>
<div style="direction:rtl;font-family:'Cairo',sans-serif;">

  {{-- ── YEAR SELECTOR ── --}}
  <div style="background:white;border-radius:14px;padding:16px 20px;box-shadow:0 2px 8px rgba(0,0,0,.05);margin-bottom:20px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
    <span style="font-size:13px;font-weight:700;color:#374151;">السنة المالية:</span>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
      @foreach($availableYears as $y)
        <button
          wire:click="$set('year','{{ $y }}')"
          style="padding:7px 20px;border-radius:8px;font-size:13px;font-weight:700;font-family:'Cairo',sans-serif;cursor:pointer;border:2px solid {{ (string)$year === (string)$y ? '#1B3B8C' : '#E5E7EB' }};background:{{ (string)$year === (string)$y ? '#1B3B8C' : 'white' }};color:{{ (string)$year === (string)$y ? 'white' : '#6B7280' }};transition:all .2s;"
        >{{ $y }}</button>
      @endforeach
    </div>
    <span style="margin-right:auto;font-size:12px;color:#9CA3AF;">التقرير المالي السنوي — {{ $year }}</span>
  </div>

  {{-- ── KPI ROW ── --}}
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px;">

    <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,.05);position:relative;overflow:hidden;">
      <div style="position:absolute;top:0;right:0;width:4px;height:100%;background:#1B3B8C;"></div>
      <div style="font-size:11px;color:#6B7280;font-weight:700;margin-bottom:8px;">إجمالي الإيرادات</div>
      <div style="font-size:26px;font-weight:900;color:#1B3B8C;margin-bottom:4px;">{{ number_format($yearRevenue, 0) }} ₪</div>
      <div style="font-size:11px;color:#9CA3AF;">{{ number_format($yearOrders) }} طلب مكتمل</div>
    </div>

    <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,.05);position:relative;overflow:hidden;">
      <div style="position:absolute;top:0;right:0;width:4px;height:100%;background:#10B981;"></div>
      <div style="font-size:11px;color:#6B7280;font-weight:700;margin-bottom:8px;">إجمالي الربح</div>
      <div style="font-size:26px;font-weight:900;color:{{ $grossProfit >= 0 ? '#10B981' : '#EF4444' }};margin-bottom:4px;">{{ number_format($grossProfit, 0) }} ₪</div>
      <div style="font-size:11px;color:#9CA3AF;">هامش الربح: {{ $margin }}%</div>
    </div>

    <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,.05);position:relative;overflow:hidden;">
      <div style="position:absolute;top:0;right:0;width:4px;height:100%;background:#F59E0B;"></div>
      <div style="font-size:11px;color:#6B7280;font-weight:700;margin-bottom:8px;">إجمالي الخصومات</div>
      <div style="font-size:26px;font-weight:900;color:#F59E0B;margin-bottom:4px;">{{ number_format($yearDiscounts, 0) }} ₪</div>
      <div style="font-size:11px;color:#9CA3AF;">تكلفة رسوم التوصيل: {{ number_format($yearDelivery, 0) }} ₪</div>
    </div>

    <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,.05);position:relative;overflow:hidden;">
      <div style="position:absolute;top:0;right:0;width:4px;height:100%;background:#EF4444;"></div>
      <div style="font-size:11px;color:#6B7280;font-weight:700;margin-bottom:8px;">طلبات ملغاة</div>
      <div style="font-size:26px;font-weight:900;color:#EF4444;margin-bottom:4px;">{{ number_format($yearCancelled, 0) }} ₪</div>
      <div style="font-size:11px;color:#9CA3AF;">قيمة الطلبات الملغاة</div>
    </div>

  </div>

  {{-- ── MONTHLY BAR CHART ── --}}
  <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,.05);margin-bottom:20px;">
    <h3 style="font-size:14px;font-weight:900;color:#1B3B8C;margin:0 0 16px;">📈 الإيرادات الشهرية — {{ $year }}</h3>
    <div style="display:flex;align-items:flex-end;gap:8px;height:180px;">
      @foreach($monthly as $i => $m)
        @php $barColor = $m['revenue'] > 0 ? '#1B3B8C' : '#E5E7EB'; @endphp
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;height:100%;justify-content:flex-end;">
          @if($m['revenue'] > 0)
            <div style="font-size:9px;font-weight:700;color:#374151;white-space:nowrap;">
              {{ $m['revenue'] >= 1000 ? round($m['revenue']/1000,1).'k' : (int)$m['revenue'] }}
            </div>
          @endif
          <div style="width:100%;border-radius:4px 4px 0 0;background:{{ $barColor }};height:{{ $m['pct'] }}%;min-height:2px;"></div>
          <div style="font-size:9px;color:#6B7280;white-space:nowrap;">{{ mb_substr($m['month'], 0, 4) }}</div>
        </div>
      @endforeach
    </div>
  </div>

  {{-- ── MONTHLY BREAKDOWN TABLE ── --}}
  <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,.05);margin-bottom:20px;">
    <h3 style="font-size:14px;font-weight:900;color:#1B3B8C;margin:0 0 14px;">📊 التفصيل الشهري — {{ $year }}</h3>
    <div style="overflow-x:auto;">
      <table style="width:100%;border-collapse:collapse;min-width:700px;">
        <thead>
          <tr style="background:#F9FAFB;">
            <th style="padding:10px 12px;font-size:11px;color:#6B7280;font-weight:700;text-align:right;border-bottom:2px solid #E5E7EB;">الشهر</th>
            <th style="padding:10px 12px;font-size:11px;color:#6B7280;font-weight:700;text-align:right;border-bottom:2px solid #E5E7EB;">الإيرادات</th>
            <th style="padding:10px 12px;font-size:11px;color:#6B7280;font-weight:700;text-align:right;border-bottom:2px solid #E5E7EB;">الطلبات</th>
            <th style="padding:10px 12px;font-size:11px;color:#6B7280;font-weight:700;text-align:right;border-bottom:2px solid #E5E7EB;">متوسط الطلب</th>
            <th style="padding:10px 12px;font-size:11px;color:#6B7280;font-weight:700;text-align:right;border-bottom:2px solid #E5E7EB;">الخصومات</th>
            <th style="padding:10px 12px;font-size:11px;color:#6B7280;font-weight:700;text-align:right;border-bottom:2px solid #E5E7EB;">التوصيل</th>
          </tr>
        </thead>
        <tbody>
          @php $totalRevenue = 0; $totalOrders = 0; $totalDisc = 0; $totalDel = 0; @endphp
          @foreach($monthly as $m)
            @php
              $totalRevenue += $m['revenue'];
              $totalOrders  += $m['orders'];
              $totalDisc    += $m['discount'];
              $totalDel     += $m['delivery'];
            @endphp
            <tr style="border-bottom:1px solid #F3F4F6;{{ $m['revenue'] > 0 ? '' : 'opacity:.45;' }}">
              <td style="padding:10px 12px;font-size:12px;font-weight:700;color:#374151;">{{ $m['month'] }}</td>
              <td style="padding:10px 12px;font-size:13px;font-weight:900;color:#1B3B8C;">
                {{ $m['revenue'] > 0 ? number_format($m['revenue'], 0).' ₪' : '—' }}
              </td>
              <td style="padding:10px 12px;font-size:12px;color:#374151;">{{ $m['orders'] > 0 ? number_format($m['orders']) : '—' }}</td>
              <td style="padding:10px 12px;font-size:12px;color:#6B7280;">{{ $m['avg'] > 0 ? number_format($m['avg'], 0).' ₪' : '—' }}</td>
              <td style="padding:10px 12px;font-size:12px;color:#F59E0B;">{{ $m['discount'] > 0 ? number_format($m['discount'], 0).' ₪' : '—' }}</td>
              <td style="padding:10px 12px;font-size:12px;color:#6B7280;">{{ $m['delivery'] > 0 ? number_format($m['delivery'], 0).' ₪' : '—' }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr style="background:#F9FAFB;border-top:2px solid #E5E7EB;">
            <td style="padding:10px 12px;font-size:12px;font-weight:900;color:#374151;">الإجمالي</td>
            <td style="padding:10px 12px;font-size:13px;font-weight:900;color:#1B3B8C;">{{ number_format($totalRevenue, 0) }} ₪</td>
            <td style="padding:10px 12px;font-size:12px;font-weight:700;color:#374151;">{{ number_format($totalOrders) }}</td>
            <td style="padding:10px 12px;font-size:12px;color:#6B7280;">{{ $totalOrders > 0 ? number_format($totalRevenue/$totalOrders, 0).' ₪' : '—' }}</td>
            <td style="padding:10px 12px;font-size:12px;font-weight:700;color:#F59E0B;">{{ number_format($totalDisc, 0) }} ₪</td>
            <td style="padding:10px 12px;font-size:12px;color:#6B7280;">{{ number_format($totalDel, 0) }} ₪</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  {{-- ── COUPON + DELIVERY ROW ── --}}
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

    {{-- Coupon Usage --}}
    <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,.05);">
      <h3 style="font-size:14px;font-weight:900;color:#1B3B8C;margin:0 0 14px;">🎫 استخدام الكوبونات — {{ $year }}</h3>
      @if($couponUsage->isEmpty())
        <div style="text-align:center;color:#9CA3AF;font-size:13px;padding:20px 0;">لم يُستخدم أي كوبون في هذه الفترة</div>
      @else
        <table style="width:100%;border-collapse:collapse;">
          <thead>
            <tr style="background:#F9FAFB;">
              <th style="padding:8px;font-size:11px;color:#6B7280;font-weight:700;text-align:right;">الكود</th>
              <th style="padding:8px;font-size:11px;color:#6B7280;font-weight:700;text-align:right;">مرات الاستخدام</th>
              <th style="padding:8px;font-size:11px;color:#6B7280;font-weight:700;text-align:right;">إجمالي الخصم</th>
            </tr>
          </thead>
          <tbody>
            @foreach($couponUsage as $c)
              <tr style="border-bottom:1px solid #F3F4F6;">
                <td style="padding:9px 8px;">
                  <span style="background:#EEF2FF;color:#1B3B8C;padding:3px 10px;border-radius:6px;font-size:12px;font-weight:700;font-family:monospace;">{{ $c->coupon_code }}</span>
                </td>
                <td style="padding:9px 8px;font-size:12px;font-weight:700;color:#374151;text-align:center;">{{ $c->uses }}</td>
                <td style="padding:9px 8px;font-size:12px;font-weight:700;color:#F59E0B;">{{ number_format($c->saved, 0) }} ₪</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    </div>

    {{-- Delivery Stats --}}
    <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,.05);">
      <h3 style="font-size:14px;font-weight:900;color:#1B3B8C;margin:0 0 14px;">🚚 إحصائيات التوصيل — {{ $year }}</h3>
      @php
        $deliveryTotal = $paidDelivery + $freeDelivery ?: 1;
        $paidPct = round(($paidDelivery / $deliveryTotal) * 100);
        $freePct = round(($freeDelivery / $deliveryTotal) * 100);
      @endphp

      <div style="margin-bottom:20px;">
        <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:6px;">
          <span style="font-weight:700;color:#374151;">توصيل مدفوع</span>
          <span style="color:#6B7280;">{{ number_format($paidDelivery) }} طلب <span style="color:#9CA3AF;">({{ $paidPct }}%)</span></span>
        </div>
        <div style="background:#F3F4F6;border-radius:6px;height:12px;overflow:hidden;">
          <div style="height:100%;border-radius:6px;background:#E8711A;width:{{ $paidPct }}%;transition:width .5s;"></div>
        </div>
      </div>

      <div style="margin-bottom:24px;">
        <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:6px;">
          <span style="font-weight:700;color:#374151;">توصيل مجاني</span>
          <span style="color:#6B7280;">{{ number_format($freeDelivery) }} طلب <span style="color:#9CA3AF;">({{ $freePct }}%)</span></span>
        </div>
        <div style="background:#F3F4F6;border-radius:6px;height:12px;overflow:hidden;">
          <div style="height:100%;border-radius:6px;background:#10B981;width:{{ $freePct }}%;transition:width .5s;"></div>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div style="background:#FFF7ED;border-radius:10px;padding:14px;text-align:center;">
          <div style="font-size:22px;font-weight:900;color:#E8711A;">{{ number_format($paidDelivery) }}</div>
          <div style="font-size:11px;color:#9CA3AF;margin-top:2px;">طلب بتوصيل مدفوع</div>
          <div style="font-size:12px;font-weight:700;color:#E8711A;margin-top:4px;">{{ number_format($yearDelivery, 0) }} ₪</div>
        </div>
        <div style="background:#F0FDF4;border-radius:10px;padding:14px;text-align:center;">
          <div style="font-size:22px;font-weight:900;color:#10B981;">{{ number_format($freeDelivery) }}</div>
          <div style="font-size:11px;color:#9CA3AF;margin-top:2px;">طلب بتوصيل مجاني</div>
          <div style="font-size:12px;font-weight:700;color:#10B981;margin-top:4px;">{{ $paidDelivery + $freeDelivery }} إجمالي</div>
        </div>
      </div>
    </div>

  </div>

</div>
</x-filament-panels::page>
