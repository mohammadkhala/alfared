<div style="width:100%;margin-bottom:8px;">
    <div style="display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:14px;width:100%;">
        <div style="background:white;border-radius:14px;padding:18px 16px;box-shadow:0 2px 10px rgba(0,0,0,0.05);text-align:center;border-top:4px solid #5B21B6;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:108px;">
            <div style="font-size:28px;font-weight:900;color:#5B21B6;line-height:1;">{{ $new }}</div>
            <div style="font-size:12px;color:#475569;margin-top:8px;font-weight:700;white-space:nowrap;">🆕 جديد</div>
        </div>
        <div style="background:white;border-radius:14px;padding:18px 16px;box-shadow:0 2px 10px rgba(0,0,0,0.05);text-align:center;border-top:4px solid #E8711A;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:108px;">
            <div style="font-size:28px;font-weight:900;color:#E8711A;line-height:1;">{{ $processing }}</div>
            <div style="font-size:12px;color:#475569;margin-top:8px;font-weight:700;white-space:nowrap;">⚙️ قيد التجهيز</div>
        </div>
        <div style="background:white;border-radius:14px;padding:18px 16px;box-shadow:0 2px 10px rgba(0,0,0,0.05);text-align:center;border-top:4px solid #10B981;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:108px;">
            <div style="font-size:28px;font-weight:900;color:#10B981;line-height:1;">{{ $shipped }}</div>
            <div style="font-size:12px;color:#475569;margin-top:8px;font-weight:700;white-space:nowrap;">🚚 في الطريق</div>
        </div>
        <div style="background:white;border-radius:14px;padding:18px 16px;box-shadow:0 2px 10px rgba(0,0,0,0.05);text-align:center;border-top:4px solid #065F46;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:108px;">
            <div style="font-size:28px;font-weight:900;color:#065F46;line-height:1;">{{ $delivered }}</div>
            <div style="font-size:12px;color:#475569;margin-top:8px;font-weight:700;white-space:nowrap;">✓ مُسلَّم</div>
        </div>
        <div style="background:white;border-radius:14px;padding:18px 16px;box-shadow:0 2px 10px rgba(0,0,0,0.05);text-align:center;border-top:4px solid #DC2626;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:108px;">
            <div style="font-size:28px;font-weight:900;color:#DC2626;line-height:1;">{{ $cancelled }}</div>
            <div style="font-size:12px;color:#475569;margin-top:8px;font-weight:700;white-space:nowrap;">✕ ملغي</div>
        </div>
    </div>
</div>
