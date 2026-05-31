<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ $type === 'coming_soon' ? 'قريباً' : 'تحت الصيانة' }} — ابناء الفريد</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet"/>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Cairo', sans-serif;
      background: #0B1A3B;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      position: relative;
    }

    /* ── Background blobs ── */
    .blob {
      position: absolute;
      border-radius: 50%;
      filter: blur(80px);
      opacity: 0.25;
      animation: float 8s ease-in-out infinite;
    }
    .blob-1 { width:500px;height:500px;background:#E8711A;top:-200px;right:-100px;animation-delay:0s; }
    .blob-2 { width:400px;height:400px;background:#1B3B8C;bottom:-150px;left:-100px;animation-delay:3s; }
    .blob-3 { width:250px;height:250px;background:#E8711A;bottom:100px;right:80px;animation-delay:5s; }
    @keyframes float {
      0%,100% { transform: translateY(0) scale(1); }
      50%      { transform: translateY(-30px) scale(1.05); }
    }

    /* ── Grid overlay ── */
    body::before {
      content:'';position:absolute;inset:0;
      background-image:
        linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
      background-size: 48px 48px;
    }

    /* ── Card ── */
    .card {
      position: relative;
      z-index: 10;
      text-align: center;
      padding: 60px 48px;
      max-width: 580px;
      width: 90%;
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(255,255,255,0.10);
      border-radius: 32px;
      backdrop-filter: blur(20px);
    }

    /* ── Logo ── */
    .logo-wrap { margin-bottom: 32px; }
    .logo-icon {
      width: 90px; height: 90px;
      border-radius: 24px;
      background: linear-gradient(135deg, #1B3B8C, #2448A8);
      border: 2px solid rgba(232,113,26,0.4);
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 16px;
      font-size: 40px;
      box-shadow: 0 8px 32px rgba(232,113,26,0.25);
    }
    .logo-name { font-size: 18px; font-weight: 900; color: white; }
    .logo-sub  { font-size: 12px; color: rgba(255,255,255,0.5); margin-top: 2px; }

    /* ── Icon ── */
    .main-icon {
      font-size: 72px;
      margin-bottom: 20px;
      display: block;
      animation: pulse 2s ease-in-out infinite;
    }
    @keyframes pulse {
      0%,100% { transform: scale(1); }
      50%      { transform: scale(1.08); }
    }

    /* ── Text ── */
    .title {
      font-size: 32px;
      font-weight: 900;
      color: white;
      margin-bottom: 14px;
      line-height: 1.3;
    }
    .subtitle {
      font-size: 15px;
      color: rgba(255,255,255,0.60);
      line-height: 1.8;
      margin-bottom: 36px;
    }

    /* ── Countdown ── */
    .countdown-wrap { margin-bottom: 36px; }
    .countdown-label {
      font-size: 12px; font-weight: 700;
      color: rgba(255,255,255,0.5);
      letter-spacing: 1px;
      margin-bottom: 16px;
    }
    .countdown {
      display: flex;
      gap: 16px;
      justify-content: center;
    }
    .count-item {
      background: rgba(255,255,255,0.08);
      border: 1px solid rgba(255,255,255,0.12);
      border-radius: 16px;
      padding: 16px 20px;
      min-width: 72px;
    }
    .count-num {
      font-size: 32px;
      font-weight: 900;
      color: #FFA76B;
      display: block;
      line-height: 1;
    }
    .count-lbl {
      font-size: 11px;
      color: rgba(255,255,255,0.5);
      margin-top: 4px;
      display: block;
    }

    /* ── Maintenance spinner ── */
    .spinner-wrap { margin-bottom: 32px; }
    .spinner {
      width: 56px; height: 56px;
      border: 4px solid rgba(255,255,255,0.1);
      border-top-color: #E8711A;
      border-radius: 50%;
      margin: 0 auto;
      animation: spin 1.2s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── WhatsApp button ── */
    .btn-wa {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 14px 28px;
      background: linear-gradient(90deg, #25D366, #1da851);
      color: white;
      border-radius: 16px;
      font-size: 15px;
      font-weight: 800;
      text-decoration: none;
      transition: transform .2s, opacity .2s;
      box-shadow: 0 4px 20px rgba(37,211,102,0.3);
    }
    .btn-wa:hover { transform: translateY(-2px); opacity: .92; }

    /* ── Status bar ── */
    .status-bar {
      display: flex;
      align-items: center;
      gap: 8px;
      justify-content: center;
      margin-top: 28px;
      font-size: 12px;
      color: rgba(255,255,255,0.4);
    }
    .status-dot {
      width: 8px; height: 8px;
      background: #25D366;
      border-radius: 50%;
      animation: blink 2s ease-in-out infinite;
    }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }
  </style>
</head>
<body>
  <div class="blob blob-1"></div>
  <div class="blob blob-2"></div>
  <div class="blob blob-3"></div>

  <div class="card">

    {{-- Logo --}}
    <div class="logo-wrap">
      <div class="logo-icon">🛍️</div>
      <div class="logo-name">شركة أبناء الفريد</div>
    </div>

    @if($type === 'coming_soon')
      {{-- COMING SOON --}}
      <span class="main-icon">🚀</span>
      <h1 class="title">{{ $title ?: 'قريباً جداً...' }}</h1>
      <p class="subtitle">{{ $message ?: 'نعمل على شيء رائع — ابقَ على تواصل!' }}</p>

      @if($launch)
        <div class="countdown-wrap">
          <div class="countdown-label">⏱ العد التنازلي للإطلاق</div>
          <div class="countdown" id="countdown">
            <div class="count-item"><span class="count-num" id="cd-d">00</span><span class="count-lbl">يوم</span></div>
            <div class="count-item"><span class="count-num" id="cd-h">00</span><span class="count-lbl">ساعة</span></div>
            <div class="count-item"><span class="count-num" id="cd-m">00</span><span class="count-lbl">دقيقة</span></div>
            <div class="count-item"><span class="count-num" id="cd-s">00</span><span class="count-lbl">ثانية</span></div>
          </div>
        </div>
      @endif

    @else
      {{-- MAINTENANCE --}}
      <div class="spinner-wrap"><div class="spinner"></div></div>
      <h1 class="title">{{ $title ?: 'نحن تحت الصيانة' }}</h1>
      <p class="subtitle">{{ $message ?: 'نعمل على تحسين تجربتك — سنعود قريباً!' }}</p>
    @endif

    @if($wa)
      <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $wa) }}" class="btn-wa" target="_blank">
        💬 تواصل معنا عبر واتساب
      </a>
    @endif

    <div class="status-bar">
      <span class="status-dot"></span>
      <span>الفريق يعمل الآن على الموقع</span>
    </div>

  </div>

  @if($launch)
  <script>
    const target = new Date("{{ $launch }}T00:00:00");
    function update() {
      const now  = new Date();
      const diff = Math.max(0, target - now);
      const d = Math.floor(diff / 86400000);
      const h = Math.floor((diff % 86400000) / 3600000);
      const m = Math.floor((diff % 3600000) / 60000);
      const s = Math.floor((diff % 60000) / 1000);
      const pad = n => String(n).padStart(2,'0');
      document.getElementById('cd-d').textContent = pad(d);
      document.getElementById('cd-h').textContent = pad(h);
      document.getElementById('cd-m').textContent = pad(m);
      document.getElementById('cd-s').textContent = pad(s);
    }
    update();
    setInterval(update, 1000);
  </script>
  @endif
</body>
</html>
