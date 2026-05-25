<div class="alfared-kpi-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:0;">

  {{-- Revenue --}}
  <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,.06);display:flex;flex-direction:column;gap:8px;position:relative;overflow:hidden;">
    <div style="position:absolute;top:0;right:0;width:4px;height:100%;background:#1B3B8C;"></div>
    <div style="position:absolute;top:16px;left:16px;font-size:32px;opacity:.12;line-height:1;">💰</div>
    <div style="font-size:12px;color:#6B7280;font-weight:600;">إجمالي المبيعات (هذا الشهر)</div>
    <div style="font-size:28px;font-weight:900;color:#1B3B8C;">{{ number_format($monthRevenue, 0) }} ₪</div>
    @if($monthChange != 0)
      <div style="font-size:12px;color:{{ $monthChange >= 0 ? '#10B981' : '#EF4444' }};">
        {{ $monthChange >= 0 ? '↑' : '↓' }} {{ abs($monthChange) }}% مقارنة بالشهر الماضي
      </div>
    @else
      <div style="font-size:12px;color:#6B7280;">— لا توجد بيانات سابقة</div>
    @endif
  </div>

  {{-- Today orders --}}
  <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,.06);display:flex;flex-direction:column;gap:8px;position:relative;overflow:hidden;">
    <div style="position:absolute;top:0;right:0;width:4px;height:100%;background:#E8711A;"></div>
    <div style="position:absolute;top:16px;left:16px;font-size:32px;opacity:.12;line-height:1;">🛒</div>
    <div style="font-size:12px;color:#6B7280;font-weight:600;">الطلبات الجديدة (اليوم)</div>
    <div style="font-size:28px;font-weight:900;color:#E8711A;">{{ $todayOrders }}</div>
    @if($ordersChange != 0)
      <div style="font-size:12px;color:{{ $ordersChange >= 0 ? '#10B981' : '#EF4444' }};">
        {{ $ordersChange >= 0 ? '↑' : '↓' }} {{ abs($ordersChange) }}% مقارنة بالأمس
      </div>
    @else
      <div style="font-size:12px;color:#6B7280;">{{ $yesterdayOrders ?? 0 == 0 ? 'لا يوجد طلبات أمس' : 'بدون تغيير' }}</div>
    @endif
  </div>

  {{-- New customers --}}
  <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,.06);display:flex;flex-direction:column;gap:8px;position:relative;overflow:hidden;">
    <div style="position:absolute;top:0;right:0;width:4px;height:100%;background:#10B981;"></div>
    <div style="position:absolute;top:16px;left:16px;font-size:32px;opacity:.12;line-height:1;">👥</div>
    <div style="font-size:12px;color:#6B7280;font-weight:600;">العملاء الجدد (هذا الأسبوع)</div>
    <div style="font-size:28px;font-weight:900;color:#10B981;">{{ $weekCustomers }}</div>
    @if($customersChange != 0)
      <div style="font-size:12px;color:{{ $customersChange >= 0 ? '#10B981' : '#EF4444' }};">
        {{ $customersChange >= 0 ? '↑' : '↓' }} {{ abs($customersChange) }}% مقارنة بالأسبوع الماضي
      </div>
    @else
      <div style="font-size:12px;color:#6B7280;">هذا الأسبوع</div>
    @endif
  </div>

  {{-- Low stock --}}
  <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,.06);display:flex;flex-direction:column;gap:8px;position:relative;overflow:hidden;">
    <div style="position:absolute;top:0;right:0;width:4px;height:100%;background:#8B5CF6;"></div>
    <div style="position:absolute;top:16px;left:16px;font-size:32px;opacity:.12;line-height:1;">📦</div>
    <div style="font-size:12px;color:#6B7280;font-weight:600;">منتجات مخزون منخفض</div>
    <div style="font-size:28px;font-weight:900;color:#8B5CF6;">{{ $lowStock }}</div>
    <div style="font-size:12px;color:{{ $lowStock > 0 ? '#EF4444' : '#10B981' }};">
      {{ $lowStock > 0 ? '⚠️ تحتاج إعادة طلب' : '✓ المخزون كافٍ' }}
    </div>
  </div>

</div>
