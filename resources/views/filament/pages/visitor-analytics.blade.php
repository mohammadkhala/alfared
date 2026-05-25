<x-filament-panels::page>

  {{-- Period filter --}}
  <div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
    @foreach(['today' => 'اليوم', 'week' => 'هذا الأسبوع', 'month' => 'هذا الشهر', 'all' => 'الكل'] as $key => $label)
      <button
        type="button"
        wire:click="setPeriod('{{ $key }}')"
        style="padding:9px 18px;border-radius:10px;border:none;cursor:pointer;font-family:'Cairo',sans-serif;font-size:13px;font-weight:700;
               background:{{ $period === $key ? '#122870' : '#fff' }};
               color:{{ $period === $key ? '#fff' : '#475569' }};
               box-shadow:0 2px 6px rgba(0,0,0,0.06);">
        {{ $label }}
      </button>
    @endforeach
    <span style="margin-inline-start:auto;font-size:12px;color:#64748B;align-self:center;">
      {{ $from->format('d/m/Y') }} → {{ $to->format('d/m/Y') }}
    </span>
  </div>

  {{-- KPI cards --}}
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:24px;">
    <div style="background:#fff;border-radius:14px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,0.05);border-top:4px solid #122870;">
      <div style="font-size:12px;color:#64748B;font-weight:700;">إجمالي الزيارات</div>
      <div style="font-size:30px;font-weight:900;color:#122870;margin-top:8px;">{{ number_format($totalVisits) }}</div>
    </div>
    <div style="background:#fff;border-radius:14px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,0.05);border-top:4px solid #E8711A;">
      <div style="font-size:12px;color:#64748B;font-weight:700;">زوار فريدون</div>
      <div style="font-size:30px;font-weight:900;color:#E8711A;margin-top:8px;">{{ number_format($uniqueVisits) }}</div>
    </div>
    <div style="background:#fff;border-radius:14px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,0.05);border-top:4px solid #10B981;">
      <div style="font-size:12px;color:#64748B;font-weight:700;">مستخدمون مسجَّلون</div>
      <div style="font-size:30px;font-weight:900;color:#10B981;margin-top:8px;">{{ number_format($loggedIn) }}</div>
    </div>
  </div>

  {{-- Device breakdown --}}
  <div style="background:#fff;border-radius:14px;padding:24px;box-shadow:0 2px 10px rgba(0,0,0,0.05);margin-bottom:24px;">
    <h3 style="font-size:14px;font-weight:900;color:#122870;margin:0 0 16px;">📱 توزيع الزوار حسب الجهاز</h3>
    @if($byDevice->isEmpty())
      <div style="color:#94A3B8;text-align:center;padding:20px;">لا توجد بيانات للفترة المحددة</div>
    @else
      <div style="display:grid;grid-template-columns:repeat({{ min(6, $byDevice->count()) }},1fr);gap:14px;">
        @foreach($byDevice as $type => $row)
          @php
            $label = \App\Models\SiteVisit::$deviceLabels[$type] ?? $type;
            $color = \App\Models\SiteVisit::$deviceColors[$type] ?? '#94A3B8';
            $pct   = $totalVisits > 0 ? round(($row->total / $totalVisits) * 100) : 0;
          @endphp
          <div style="text-align:center;padding:16px;background:#F8FAFC;border-radius:12px;border-bottom:3px solid {{ $color }};">
            <div style="font-size:28px;font-weight:900;color:{{ $color }};">{{ number_format($row->total) }}</div>
            <div style="font-size:11px;color:#64748B;margin-top:6px;font-weight:700;">{{ $label }}</div>
            <div style="font-size:10px;color:#94A3B8;margin-top:2px;">{{ $pct }}% • {{ $row->uniques }} فريد</div>
          </div>
        @endforeach
      </div>

      {{-- Bar visualization --}}
      <div style="margin-top:20px;display:flex;height:24px;border-radius:12px;overflow:hidden;background:#F1F5F9;">
        @foreach($byDevice as $type => $row)
          @php
            $color = \App\Models\SiteVisit::$deviceColors[$type] ?? '#94A3B8';
            $w     = $totalVisits > 0 ? ($row->total / $totalVisits) * 100 : 0;
          @endphp
          <div style="background:{{ $color }};width:{{ $w }}%;" title="{{ $type }} {{ round($w) }}%"></div>
        @endforeach
      </div>
    @endif
  </div>

  {{-- Visits-over-time chart --}}
  <div style="background:#fff;border-radius:14px;padding:24px;box-shadow:0 2px 10px rgba(0,0,0,0.05);margin-bottom:24px;">
    <h3 style="font-size:14px;font-weight:900;color:#122870;margin:0 0 16px;">
      📈 الزيارات عبر {{ $period === 'today' ? 'الساعات' : 'الأيام' }}
    </h3>
    @if($byTime->isEmpty())
      <div style="color:#94A3B8;text-align:center;padding:20px;">لا توجد بيانات</div>
    @else
      @php $max = $byTime->max('total') ?: 1; @endphp
      <div style="display:flex;gap:6px;align-items:flex-end;height:160px;padding-top:10px;">
        @foreach($byTime as $bucket)
          @php $h = ($bucket->total / $max) * 140; @endphp
          <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;min-width:0;">
            <div style="font-size:10px;color:#475569;font-weight:700;">{{ $bucket->total }}</div>
            <div style="width:100%;height:{{ $h }}px;background:linear-gradient(180deg,#E8711A,#d4610e);border-radius:6px 6px 0 0;min-height:2px;"></div>
            <div style="font-size:9px;color:#94A3B8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100%;">{{ $bucket->bucket }}</div>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  {{-- 2-col: top pages + browsers/OS --}}
  <div style="display:grid;grid-template-columns:2fr 1fr;gap:18px;margin-bottom:24px;">

    <div style="background:#fff;border-radius:14px;padding:24px;box-shadow:0 2px 10px rgba(0,0,0,0.05);">
      <h3 style="font-size:14px;font-weight:900;color:#122870;margin:0 0 16px;">🔝 أكثر الصفحات زيارة</h3>
      @if($topPages->isEmpty())
        <div style="color:#94A3B8;text-align:center;padding:20px;">لا توجد بيانات</div>
      @else
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
          <thead>
            <tr style="background:#F8FAFC;border-bottom:1px solid #E5E7EB;">
              <th style="text-align:start;padding:10px;color:#64748B;font-weight:700;">المسار</th>
              <th style="text-align:end;padding:10px;color:#64748B;font-weight:700;">إجمالي</th>
              <th style="text-align:end;padding:10px;color:#64748B;font-weight:700;">فريدون</th>
            </tr>
          </thead>
          <tbody>
            @foreach($topPages as $page)
              <tr style="border-bottom:1px solid #F3F4F6;">
                <td style="padding:8px 10px;font-family:monospace;font-size:12px;color:#0F172A;direction:ltr;">{{ $page->path }}</td>
                <td style="padding:8px 10px;text-align:end;font-weight:700;color:#122870;">{{ number_format($page->total) }}</td>
                <td style="padding:8px 10px;text-align:end;color:#64748B;">{{ number_format($page->uniques) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    </div>

    <div style="display:flex;flex-direction:column;gap:18px;">
      <div style="background:#fff;border-radius:14px;padding:24px;box-shadow:0 2px 10px rgba(0,0,0,0.05);">
        <h3 style="font-size:14px;font-weight:900;color:#122870;margin:0 0 12px;">🌐 المتصفحات</h3>
        @forelse($topBrowsers as $b)
          @php $w = $totalVisits ? ($b->total / $totalVisits) * 100 : 0; @endphp
          <div style="margin-bottom:8px;">
            <div style="display:flex;justify-content:space-between;font-size:12px;color:#475569;margin-bottom:3px;">
              <span>{{ $b->browser }}</span><span>{{ $b->total }} • {{ round($w) }}%</span>
            </div>
            <div style="height:6px;background:#F1F5F9;border-radius:3px;overflow:hidden;">
              <div style="height:100%;background:#3B82F6;width:{{ $w }}%;"></div>
            </div>
          </div>
        @empty
          <div style="color:#94A3B8;font-size:13px;">—</div>
        @endforelse
      </div>

      <div style="background:#fff;border-radius:14px;padding:24px;box-shadow:0 2px 10px rgba(0,0,0,0.05);">
        <h3 style="font-size:14px;font-weight:900;color:#122870;margin:0 0 12px;">💾 أنظمة التشغيل</h3>
        @forelse($topOS as $o)
          @php $w = $totalVisits ? ($o->total / $totalVisits) * 100 : 0; @endphp
          <div style="margin-bottom:8px;">
            <div style="display:flex;justify-content:space-between;font-size:12px;color:#475569;margin-bottom:3px;">
              <span>{{ $o->os }}</span><span>{{ $o->total }} • {{ round($w) }}%</span>
            </div>
            <div style="height:6px;background:#F1F5F9;border-radius:3px;overflow:hidden;">
              <div style="height:100%;background:#10B981;width:{{ $w }}%;"></div>
            </div>
          </div>
        @empty
          <div style="color:#94A3B8;font-size:13px;">—</div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- Recent visits --}}
  <div style="background:#fff;border-radius:14px;padding:24px;box-shadow:0 2px 10px rgba(0,0,0,0.05);">
    <h3 style="font-size:14px;font-weight:900;color:#122870;margin:0 0 16px;">🕒 آخر الزيارات</h3>
    @if($recent->isEmpty())
      <div style="color:#94A3B8;text-align:center;padding:20px;">لا توجد بيانات</div>
    @else
      <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
          <tr style="background:#F8FAFC;border-bottom:1px solid #E5E7EB;">
            <th style="text-align:start;padding:10px;color:#64748B;font-weight:700;">الوقت</th>
            <th style="text-align:start;padding:10px;color:#64748B;font-weight:700;">الجهاز</th>
            <th style="text-align:start;padding:10px;color:#64748B;font-weight:700;">المتصفح / النظام</th>
            <th style="text-align:start;padding:10px;color:#64748B;font-weight:700;">الصفحة</th>
            <th style="text-align:start;padding:10px;color:#64748B;font-weight:700;">IP</th>
          </tr>
        </thead>
        <tbody>
          @foreach($recent as $v)
            @php $color = \App\Models\SiteVisit::$deviceColors[$v->device_type] ?? '#94A3B8'; @endphp
            <tr style="border-bottom:1px solid #F3F4F6;">
              <td style="padding:8px 10px;color:#64748B;white-space:nowrap;">{{ $v->visited_at?->diffForHumans() }}</td>
              <td style="padding:8px 10px;">
                <span style="background:{{ $color }};color:#fff;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:700;">
                  {{ \App\Models\SiteVisit::$deviceLabels[$v->device_type] ?? $v->device_type }}
                </span>
              </td>
              <td style="padding:8px 10px;color:#475569;font-size:12px;">{{ $v->browser }} • {{ $v->os }}</td>
              <td style="padding:8px 10px;font-family:monospace;font-size:11px;color:#0F172A;direction:ltr;">{{ Str::limit($v->path, 40) }}</td>
              <td style="padding:8px 10px;font-family:monospace;font-size:11px;color:#94A3B8;direction:ltr;">{{ $v->ip }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>

</x-filament-panels::page>
