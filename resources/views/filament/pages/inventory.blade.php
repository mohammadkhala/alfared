<x-filament-panels::page>
<div style="direction:rtl;font-family:'Cairo',sans-serif;">

  {{-- ── STATS ROW ── --}}
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px;">

    <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,.05);position:relative;overflow:hidden;">
      <div style="position:absolute;top:0;right:0;width:4px;height:100%;background:#1B3B8C;"></div>
      <div style="font-size:11px;color:#6B7280;font-weight:700;margin-bottom:6px;">إجمالي المنتجات</div>
      <div style="font-size:28px;font-weight:900;color:#1B3B8C;">{{ number_format($total) }}</div>
      <div style="font-size:11px;color:#9CA3AF;margin-top:4px;">منتج في المتجر</div>
    </div>

    <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,.05);position:relative;overflow:hidden;">
      <div style="position:absolute;top:0;right:0;width:4px;height:100%;background:#10B981;"></div>
      <div style="font-size:11px;color:#6B7280;font-weight:700;margin-bottom:6px;">مخزون جيد</div>
      <div style="font-size:28px;font-weight:900;color:#10B981;">{{ number_format($inStock) }}</div>
      <div style="font-size:11px;color:#9CA3AF;margin-top:4px;">فوق حد التنبيه</div>
    </div>

    <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,.05);position:relative;overflow:hidden;">
      <div style="position:absolute;top:0;right:0;width:4px;height:100%;background:#F59E0B;"></div>
      <div style="font-size:11px;color:#6B7280;font-weight:700;margin-bottom:6px;">مخزون منخفض</div>
      <div style="font-size:28px;font-weight:900;color:#F59E0B;">{{ number_format($lowStock) }}</div>
      <div style="font-size:11px;color:#9CA3AF;margin-top:4px;">⚠ تحتاج إعادة طلب</div>
    </div>

    <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,.05);position:relative;overflow:hidden;">
      <div style="position:absolute;top:0;right:0;width:4px;height:100%;background:#EF4444;"></div>
      <div style="font-size:11px;color:#6B7280;font-weight:700;margin-bottom:6px;">نفد المخزون</div>
      <div style="font-size:28px;font-weight:900;color:#EF4444;">{{ number_format($outOfStock) }}</div>
      <div style="font-size:11px;color:#9CA3AF;margin-top:4px;">✗ غير متوفر</div>
    </div>

  </div>

  {{-- ── PRODUCT STOCK TABLE ── --}}
  <div style="background:white;border-radius:14px;box-shadow:0 2px 8px rgba(0,0,0,.05);overflow:hidden;">
    <div style="padding:16px 20px;border-bottom:1px solid #F3F4F6;display:flex;justify-content:space-between;align-items:center;">
      <h3 style="font-size:14px;font-weight:900;color:#1B3B8C;margin:0;">📋 جرد المخزون</h3>
      <a href="{{ route('filament.admin.resources.products.index') }}" style="font-size:12px;color:#E8711A;font-weight:700;text-decoration:none;">إدارة المنتجات ←</a>
    </div>
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr style="background:#F9FAFB;">
          <th style="padding:11px 16px;font-size:11px;color:#6B7280;font-weight:700;text-align:right;border-bottom:1px solid #F3F4F6;">المنتج</th>
          <th style="padding:11px 16px;font-size:11px;color:#6B7280;font-weight:700;text-align:right;border-bottom:1px solid #F3F4F6;">القسم</th>
          <th style="padding:11px 16px;font-size:11px;color:#6B7280;font-weight:700;text-align:right;border-bottom:1px solid #F3F4F6;">SKU</th>
          <th style="padding:11px 16px;font-size:11px;color:#6B7280;font-weight:700;text-align:right;border-bottom:1px solid #F3F4F6;">الكمية</th>
          <th style="padding:11px 16px;font-size:11px;color:#6B7280;font-weight:700;text-align:right;border-bottom:1px solid #F3F4F6;">حد التنبيه</th>
          <th style="padding:11px 16px;font-size:11px;color:#6B7280;font-weight:700;text-align:right;border-bottom:1px solid #F3F4F6;">مستوى المخزون</th>
          <th style="padding:11px 16px;font-size:11px;color:#6B7280;font-weight:700;text-align:right;border-bottom:1px solid #F3F4F6;">الحالة</th>
        </tr>
      </thead>
      <tbody>
        @forelse($products as $p)
          @php
            $statusColor = match($p['status']) {
              'out' => ['bg'=>'#FEE2E2','text'=>'#991B1B','label'=>'✗ نفد'],
              'low' => ['bg'=>'#FEF3C7','text'=>'#92400E','label'=>'⚠ منخفض'],
              default => ['bg'=>'#D1FAE5','text'=>'#065F46','label'=>'✓ متوفر'],
            };
            $barColor = match($p['status']) {
              'out' => '#EF4444',
              'low' => '#F59E0B',
              default => '#10B981',
            };
          @endphp
          <tr style="border-bottom:1px solid #F3F4F6;" onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='white'">
            <td style="padding:13px 16px;">
              <div style="display:flex;align-items:center;gap:10px;">
                @if($p['image'])
                  <img src="{{ asset('storage/'.$p['image']) }}" style="width:38px;height:38px;border-radius:10px;object-fit:cover;flex-shrink:0;" alt="">
                @else
                  <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#EEF2FF,#C7D2FE);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">📦</div>
                @endif
                <span style="font-size:13px;font-weight:700;color:#374151;">{{ \Illuminate\Support\Str::limit($p['name'], 28) }}</span>
              </div>
            </td>
            <td style="padding:13px 16px;font-size:12px;color:#6B7280;">{{ $p['category'] }}</td>
            <td style="padding:13px 16px;font-size:11px;color:#9CA3AF;font-family:monospace;">{{ $p['sku'] ?: '—' }}</td>
            <td style="padding:13px 16px;">
              <span style="font-size:16px;font-weight:900;color:{{ $p['qty'] == 0 ? '#EF4444' : ($p['status'] === 'low' ? '#F59E0B' : '#374151') }};">{{ $p['qty'] }}</span>
              <span style="font-size:10px;color:#9CA3AF;"> قطعة</span>
            </td>
            <td style="padding:13px 16px;font-size:12px;color:#9CA3AF;">{{ $p['low_alert'] }}</td>
            <td style="padding:13px 16px;min-width:120px;">
              <div style="background:#F3F4F6;border-radius:4px;height:6px;overflow:hidden;">
                <div style="height:100%;border-radius:4px;background:{{ $barColor }};width:{{ $p['bar_pct'] }}%;"></div>
              </div>
            </td>
            <td style="padding:13px 16px;">
              <span style="background:{{ $statusColor['bg'] }};color:{{ $statusColor['text'] }};padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;white-space:nowrap;">{{ $statusColor['label'] }}</span>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" style="text-align:center;padding:40px;color:#9CA3AF;font-size:13px;">لا توجد منتجات</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

</div>
</x-filament-panels::page>
