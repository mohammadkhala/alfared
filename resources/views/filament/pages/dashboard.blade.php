<x-filament-panels::page>
@php
  $statusColors = ['pending'=>'#F59E0B','confirmed'=>'#3B82F6','processing'=>'#3B82F6','sent_to_delivery'=>'#1B3B8C','shipped'=>'#8B5CF6','delivered'=>'#10B981','cancelled'=>'#EF4444','returned'=>'#EF4444'];
  $statusLabels = ['pending'=>'بانتظار التأكيد','confirmed'=>'تم التأكيد','processing'=>'قيد التجهيز','sent_to_delivery'=>'مرسل للتوصيل','shipped'=>'في الطريق','delivered'=>'تم التوصيل','cancelled'=>'ملغي','returned'=>'مُرجَّع'];
@endphp

<style>
/* ── Dashboard Responsive Grid ────────────────────────────── */
.dash-kpi-row    { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:20px; }
.dash-charts-row { display:grid; grid-template-columns:2fr 1fr;       gap:16px; margin-bottom:20px; }
.dash-tables-row { display:grid; grid-template-columns:1fr 1fr;       gap:16px; }
.dash-table-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }

/* KPI value numbers shrink gracefully */
.dash-kpi-val { font-size:28px; font-weight:900; line-height:1; margin-bottom:6px; }
.dash-kpi-lbl { font-size:11px; color:#6B7280; font-weight:700; margin-bottom:8px; }

/* ── Tablet (≤ 900px) ──────────────────────────────────────── */
@media (max-width: 900px) {
  .dash-kpi-row    { grid-template-columns: repeat(2,1fr); }
  .dash-charts-row { grid-template-columns: 1fr; }
  .dash-tables-row { grid-template-columns: 1fr; }
  .dash-kpi-val    { font-size: 22px; }
  .dash-bar-wrap   { height: 120px !important; }
}

/* ── Mobile (≤ 600px) ──────────────────────────────────────── */
@media (max-width: 600px) {
  .dash-kpi-row    { grid-template-columns: repeat(2,1fr); gap:10px; margin-bottom:14px; }
  .dash-charts-row { gap:10px; margin-bottom:14px; }
  .dash-tables-row { gap:10px; }
  .dash-kpi-val    { font-size: 20px; }
  .dash-kpi-lbl    { font-size: 10px; }
  .dash-card       { padding: 14px !important; }
  .dash-bar-wrap   { height: 100px !important; }
  .dash-card h3    { font-size: 13px !important; }
  .dash-table-wrap table { min-width: 420px; }
}

/* ── Small Mobile (≤ 400px) ───────────────────────────────── */
@media (max-width: 400px) {
  .dash-kpi-row    { grid-template-columns: 1fr 1fr; gap:8px; }
  .dash-kpi-val    { font-size: 18px; }
  .dash-card       { padding: 12px !important; }
}
</style>

<div style="direction:rtl;font-family:'Cairo',sans-serif;">

  {{-- ══════════ KPI ROW ══════════ --}}
  <div class="dash-kpi-row">

    {{-- Revenue --}}
    <div class="dash-card" style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,.06);position:relative;overflow:hidden;">
      <div style="position:absolute;top:0;right:0;width:4px;height:100%;background:#1B3B8C;"></div>
      <div style="position:absolute;top:14px;left:14px;font-size:30px;opacity:.1;line-height:1;">💰</div>
      <div class="dash-kpi-lbl">إجمالي المبيعات (هذا الشهر)</div>
      <div class="dash-kpi-val" style="color:#1B3B8C;">{{ number_format($monthRevenue, 0) }} ₪</div>
      @if($revenueChange != 0)
        <div style="font-size:11px;color:{{ $revenueChange >= 0 ? '#10B981' : '#EF4444' }};">{{ $revenueChange >= 0 ? '↑' : '↓' }} +{{ abs($revenueChange) }}% مقارنة بالشهر الماضي</div>
      @else
        <div style="font-size:11px;color:#9CA3AF;">— لا توجد بيانات سابقة</div>
      @endif
    </div>

    {{-- Today Orders --}}
    <div class="dash-card" style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,.06);position:relative;overflow:hidden;">
      <div style="position:absolute;top:0;right:0;width:4px;height:100%;background:#E8711A;"></div>
      <div style="position:absolute;top:14px;left:14px;font-size:30px;opacity:.1;line-height:1;">🛒</div>
      <div class="dash-kpi-lbl">الطلبات الجديدة (اليوم)</div>
      <div class="dash-kpi-val" style="color:#E8711A;">{{ $todayOrders }}</div>
      @if($ordersChange != 0)
        <div style="font-size:11px;color:{{ $ordersChange >= 0 ? '#10B981' : '#EF4444' }};">{{ $ordersChange >= 0 ? '↑' : '↓' }} {{ abs($ordersChange) }}% مقارنة بالأمس</div>
      @else
        <div style="font-size:11px;color:#9CA3AF;">{{ $todayOrders == 0 ? 'لا يوجد طلبات اليوم' : 'نفس أمس' }}</div>
      @endif
    </div>

    {{-- New Customers --}}
    <div class="dash-card" style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,.06);position:relative;overflow:hidden;">
      <div style="position:absolute;top:0;right:0;width:4px;height:100%;background:#10B981;"></div>
      <div style="position:absolute;top:14px;left:14px;font-size:30px;opacity:.1;line-height:1;">👥</div>
      <div class="dash-kpi-lbl">العملاء الجدد (هذا الأسبوع)</div>
      <div class="dash-kpi-val" style="color:#10B981;">{{ $weekCustomers }}</div>
      @if($customersChange != 0)
        <div style="font-size:11px;color:{{ $customersChange >= 0 ? '#10B981' : '#EF4444' }};">{{ $customersChange >= 0 ? '↑' : '↓' }} {{ abs($customersChange) }}% مقارنة بالأسبوع الماضي</div>
      @else
        <div style="font-size:11px;color:#9CA3AF;">هذا الأسبوع</div>
      @endif
    </div>

    {{-- Low Stock --}}
    <div class="dash-card" style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,.06);position:relative;overflow:hidden;">
      <div style="position:absolute;top:0;right:0;width:4px;height:100%;background:#8B5CF6;"></div>
      <div style="position:absolute;top:14px;left:14px;font-size:30px;opacity:.1;line-height:1;">📦</div>
      <div class="dash-kpi-lbl">منتجات مخزون منخفض</div>
      <div class="dash-kpi-val" style="color:#8B5CF6;">{{ $lowStock }}</div>
      <div style="font-size:11px;color:{{ $lowStock > 0 ? '#EF4444' : '#10B981' }};">{{ $lowStock > 0 ? '⚠️ تحتاج إعادة طلب' : '✓ المخزون كافٍ' }}</div>
    </div>

  </div>

  {{-- ══════════ CHARTS ROW ══════════ --}}
  <div class="dash-charts-row">

    {{-- Bar Chart --}}
    <div class="dash-card" style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,.06);">
      <h3 style="font-size:14px;font-weight:900;color:#1B3B8C;margin:0 0 16px;">📈 المبيعات اليومية (آخر 7 أيام)</h3>
      <div class="dash-bar-wrap" style="display:flex;align-items:flex-end;gap:8px;height:160px;">
        @php $barColors = ['#1B3B8C','rgba(27,59,140,.65)','#E8711A','rgba(27,59,140,.8)','#10B981','rgba(27,59,140,.6)','rgba(232,113,26,.75)']; @endphp
        @foreach($daily as $i => $day)
          <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:6px;height:100%;justify-content:flex-end;">
            <div style="font-size:10px;font-weight:700;color:#374151;">{{ $day['display'] }}</div>
            <div style="width:100%;border-radius:6px 6px 0 0;background:{{ $barColors[$i % count($barColors)] }};height:{{ $day['pct'] }}%;transition:height .3s;"></div>
            <div style="font-size:10px;color:#6B7280;white-space:nowrap;">{{ $day['label'] }}</div>
          </div>
        @endforeach
      </div>
    </div>

    {{-- Donut Chart --}}
    <div class="dash-card" style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,.06);">
      <h3 style="font-size:14px;font-weight:900;color:#1B3B8C;margin:0 0 16px;">🏷️ المبيعات حسب القسم</h3>
      <div style="display:flex;flex-direction:column;align-items:center;">
        <div style="width:120px;height:120px;border-radius:50%;background:conic-gradient({{ $donutGradient }});position:relative;margin-bottom:14px;flex-shrink:0;">
          <div style="position:absolute;inset:24px;background:white;border-radius:50%;"></div>
        </div>
        <div style="width:100%;">
          @forelse($categories as $cat)
            <div style="display:flex;align-items:center;gap:8px;font-size:12px;margin-bottom:6px;">
              <div style="width:10px;height:10px;border-radius:50%;background:{{ $cat['color'] }};flex-shrink:0;"></div>
              <span style="flex:1;color:#374151;">{{ $cat['name'] }}</span>
              <span style="font-weight:700;color:#1B3B8C;">{{ $cat['pct'] }}%</span>
            </div>
          @empty
            <div style="text-align:center;color:#9CA3AF;font-size:12px;padding:8px 0;">لا توجد مبيعات بعد</div>
          @endforelse
        </div>
      </div>
    </div>

  </div>

  {{-- ══════════ TABLES ROW ══════════ --}}
  <div class="dash-tables-row">

    {{-- Recent Orders --}}
    <div class="dash-card" style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,.06);">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
        <h3 style="font-size:14px;font-weight:900;color:#1B3B8C;margin:0;">آخر الطلبات</h3>
        <a href="{{ route('filament.admin.resources.orders.index') }}" style="font-size:12px;color:#E8711A;text-decoration:none;font-weight:700;">عرض الكل ←</a>
      </div>
      <div class="dash-table-wrap">
      <table style="width:100%;border-collapse:collapse;">
        <thead>
          <tr style="background:#F9FAFB;">
            <th style="padding:8px;font-size:11px;color:#6B7280;font-weight:700;text-align:right;border-radius:6px 0 0 6px;">رقم الطلب</th>
            <th style="padding:8px;font-size:11px;color:#6B7280;font-weight:700;text-align:right;">العميل</th>
            <th style="padding:8px;font-size:11px;color:#6B7280;font-weight:700;text-align:right;">الإجمالي</th>
            <th style="padding:8px;font-size:11px;color:#6B7280;font-weight:700;text-align:right;border-radius:0 6px 6px 0;">الحالة</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentOrders as $order)
          <tr style="border-bottom:1px solid #F3F4F6;">
            <td style="padding:10px 8px;font-size:12px;font-weight:700;color:#1B3B8C;">#{{ $order->order_number }}</td>
            <td style="padding:10px 8px;font-size:12px;color:#374151;">{{ $order->customer_name }}</td>
            <td style="padding:10px 8px;font-size:12px;font-weight:700;">{{ number_format($order->total, 0) }} ₪</td>
            <td style="padding:10px 8px;">
              <span style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:700;color:{{ $statusColors[$order->status] ?? '#6B7280' }};">
                <span style="width:7px;height:7px;border-radius:50%;background:{{ $statusColors[$order->status] ?? '#6B7280' }};display:inline-block;"></span>
                {{ $statusLabels[$order->status] ?? $order->status }}
              </span>
            </td>
          </tr>
          @empty
          <tr><td colspan="4" style="text-align:center;padding:24px;color:#9CA3AF;font-size:13px;">لا توجد طلبات بعد</td></tr>
          @endforelse
        </tbody>
      </table>
      </div>{{-- /.dash-table-wrap --}}
    </div>

    {{-- Top Products --}}
    <div class="dash-card" style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,.06);">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
        <h3 style="font-size:14px;font-weight:900;color:#1B3B8C;margin:0;">أكثر المنتجات مبيعاً</h3>
        <a href="{{ route('filament.admin.resources.products.index') }}" style="font-size:12px;color:#E8711A;text-decoration:none;font-weight:700;">عرض الكل ←</a>
      </div>
      <div class="dash-table-wrap">
      <table style="width:100%;border-collapse:collapse;">
        <thead>
          <tr style="background:#F9FAFB;">
            <th style="padding:8px;font-size:11px;color:#6B7280;font-weight:700;text-align:right;">المنتج</th>
            <th style="padding:8px;font-size:11px;color:#6B7280;font-weight:700;text-align:right;">مبيعات</th>
            <th style="padding:8px;font-size:11px;color:#6B7280;font-weight:700;text-align:right;">المخزون</th>
          </tr>
        </thead>
        <tbody>
          @forelse($topProducts as $product)
          @php $stockOk = $product->stock_quantity > ($product->low_stock_alert ?? 5); @endphp
          <tr style="border-bottom:1px solid #F3F4F6;">
            <td style="padding:10px 8px;">
              <div style="display:flex;align-items:center;gap:8px;">
                @if($product->main_image)
                  <img src="{{ asset('storage/' . $product->main_image) }}" style="width:32px;height:32px;border-radius:8px;object-fit:cover;"/>
                @else
                  <div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#EEF2FF,#C7D2FE);display:flex;align-items:center;justify-content:center;font-size:14px;">📦</div>
                @endif
                <span style="font-size:12px;font-weight:600;color:#374151;">{{ Str::limit($product->name_ar, 28) }}</span>
              </div>
            </td>
            <td style="padding:10px 8px;font-size:12px;font-weight:700;color:#1B3B8C;">{{ number_format($product->sales_count) }}</td>
            <td style="padding:10px 8px;font-size:12px;font-weight:700;color:{{ $stockOk ? '#10B981' : '#EF4444' }};">
              {{ $product->stock_quantity }} {{ $stockOk ? '✓' : '⚠️' }}
            </td>
          </tr>
          @empty
          <tr><td colspan="3" style="text-align:center;padding:24px;color:#9CA3AF;font-size:13px;">لا توجد منتجات بعد</td></tr>
          @endforelse
        </tbody>
      </table>
      </div>{{-- /.dash-table-wrap --}}
    </div>

  </div>

</div>
</x-filament-panels::page>
