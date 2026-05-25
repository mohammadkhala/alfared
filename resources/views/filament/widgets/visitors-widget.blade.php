<div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,0.05);margin-bottom:14px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
        <h3 style="font-size:14px;font-weight:900;color:#122870;margin:0;">👥 زوار اليوم</h3>
        <a href="{{ url('/admin/visitor-analytics') }}" style="font-size:12px;color:#E8711A;text-decoration:none;font-weight:700;">عرض التفاصيل ←</a>
    </div>
    <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:12px;align-items:end;">
        <div>
            <div style="font-size:11px;color:#64748B;font-weight:700;">إجمالي اليوم</div>
            <div style="font-size:24px;font-weight:900;color:#122870;">{{ number_format($today) }}</div>
        </div>
        <div>
            <div style="font-size:11px;color:#64748B;font-weight:700;">زوار فريدون</div>
            <div style="font-size:24px;font-weight:900;color:#E8711A;">{{ number_format($todayUnique) }}</div>
        </div>
        @foreach(\App\Models\SiteVisit::$deviceLabels as $type => $label)
            @continue($type === 'other')
            @php
                $count = $byDevice[$type] ?? 0;
                $color = \App\Models\SiteVisit::$deviceColors[$type] ?? '#94A3B8';
            @endphp
            <div>
                <div style="font-size:11px;color:#64748B;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $label }}</div>
                <div style="font-size:20px;font-weight:900;color:{{ $color }};">{{ number_format($count) }}</div>
            </div>
        @endforeach
    </div>
</div>
