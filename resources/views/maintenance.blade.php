<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ $type === 'coming_soon' ? 'قريباً' : 'تحت الصيانة' }} — شركة أبناء الفريد</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet"/>

@if($type === 'coming_soon')
  {{-- ══════════════════════════════════════
       COMING SOON  —  full design
  ══════════════════════════════════════ --}}
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root { --orange: #F97316; --blue: #3B82F6; }

    html, body { height: 100%; }

    body {
      font-family: 'Cairo', sans-serif;
      background: #080810;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      position: relative;
    }

    canvas { position: fixed; inset: 0; z-index: 0; pointer-events: none; }

    .grid-bg {
      position: fixed; inset: 0;
      background-image:
        linear-gradient(rgba(255,255,255,.022) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,.022) 1px, transparent 1px);
      background-size: 55px 55px;
      pointer-events: none; z-index: 0;
    }

    .glow-tr {
      position: fixed; width: 650px; height: 650px; border-radius: 50%;
      background: radial-gradient(circle, rgba(249,115,22,.16) 0%, transparent 68%);
      top: -200px; right: -150px; pointer-events: none;
      animation: pulseG 7s ease-in-out infinite alternate;
    }
    .glow-bl {
      position: fixed; width: 550px; height: 550px; border-radius: 50%;
      background: radial-gradient(circle, rgba(59,130,246,.14) 0%, transparent 68%);
      bottom: -150px; left: -120px; pointer-events: none;
      animation: pulseG 9s ease-in-out infinite alternate-reverse;
    }
    @keyframes pulseG {
      from { transform: scale(1);    opacity: .7; }
      to   { transform: scale(1.18); opacity: 1;  }
    }

    /* ── Wrapper ── */
    .wrapper {
      position: relative; z-index: 2;
      width: 95%; max-width: 1100px;
      display: grid; grid-template-columns: 1fr 1fr; gap: 0;
      background: rgba(255,255,255,.03);
      border: 1px solid rgba(255,255,255,.08);
      border-radius: 28px; overflow: hidden;
      box-shadow: 0 40px 100px rgba(0,0,0,.7), inset 0 1px 0 rgba(255,255,255,.05);
      animation: fadeUp .7s cubic-bezier(.16,1,.3,1) both;
    }
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(36px) scale(.97); }
      to   { opacity: 1; transform: translateY(0)    scale(1);   }
    }

    /* ── LEFT PANEL ── */
    .panel-left {
      padding: 52px 44px;
      display: flex; flex-direction: column; justify-content: center; align-items: flex-start;
      text-align: right;
      border-left: 1px solid rgba(255,255,255,.06);
      position: relative;
      background: linear-gradient(160deg, rgba(249,115,22,.06) 0%, transparent 60%);
    }
    .panel-left::after {
      content: ''; position: absolute; top: 0; left: 0;
      width: 100%; height: 3px;
      background: linear-gradient(90deg, transparent, var(--orange), transparent);
    }

    /* Logo */
    .logo-ring { position: relative; width: 88px; height: 88px; margin-bottom: 28px; }
    .logo-ring svg.ring {
      position: absolute; inset: 0; width: 100%; height: 100%;
      animation: spin 10s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    .logo-inner {
      position: absolute; inset: 10px; border-radius: 50%;
      background: linear-gradient(135deg, #1a1a2e, #16213e);
      border: 1px solid rgba(249,115,22,.25);
      display: flex; align-items: center; justify-content: center;
    }
    .logo-inner img { width: 42px; height: 42px; object-fit: contain; border-radius: 6px; }

    .badge {
      display: inline-flex; align-items: center; gap: 6px;
      background: rgba(249,115,22,.12); border: 1px solid rgba(249,115,22,.28);
      color: var(--orange); font-size: 11px; font-weight: 700;
      padding: 4px 14px; border-radius: 50px; margin-bottom: 16px; letter-spacing: .5px;
    }
    .badge-dot {
      width: 6px; height: 6px; border-radius: 50%; background: var(--orange);
      animation: blink 1.4s ease-in-out infinite;
    }
    @keyframes blink {
      0%,100% { opacity:1; box-shadow: 0 0 5px var(--orange); }
      50%      { opacity:.2; box-shadow: none; }
    }

    h1 { font-size: 46px; font-weight: 900; color: #fff; line-height: 1.15; margin-bottom: 12px; }
    h1 .hl {
      background: linear-gradient(90deg, var(--orange), #fb923c);
      -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
    }

    .subtitle { font-size: 14px; color: rgba(255,255,255,.4); line-height: 1.9; margin-bottom: 32px; }

    /* Features */
    .features { display: flex; flex-direction: column; gap: 10px; width: 100%; }
    .feature {
      display: flex; align-items: center; gap: 10px;
      padding: 11px 14px;
      background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.05);
      border-radius: 10px; font-size: 13px; color: rgba(255,255,255,.7); font-weight: 600;
      transition: background .2s, border-color .2s;
    }
    .feature:hover { background: rgba(249,115,22,.07); border-color: rgba(249,115,22,.2); }
    .feature-icon { font-size: 17px; filter: drop-shadow(0 0 5px rgba(249,115,22,.4)); }

    /* ── RIGHT PANEL ── */
    .panel-right { padding: 52px 44px; display: flex; flex-direction: column; justify-content: center; }

    .coming-soon-badge {
      display: flex; align-items: center; justify-content: center; gap: 10px;
      background: rgba(249,115,22,.1); border: 1px solid rgba(249,115,22,.3);
      border-radius: 14px; padding: 18px 24px; margin-bottom: 32px;
      font-size: 18px; font-weight: 900; color: #fff; letter-spacing: .5px;
    }
    .cs-dot {
      width: 10px; height: 10px; border-radius: 50%;
      background: var(--orange); box-shadow: 0 0 10px var(--orange);
      animation: blink 1.4s ease-in-out infinite; flex-shrink: 0;
    }

    .divider {
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,.07), transparent);
      margin-bottom: 28px;
    }

    /* Countdown */
    .countdown-section { margin-bottom: 28px; }
    .countdown-label {
      font-size: 11px; font-weight: 700; color: rgba(255,255,255,.35);
      letter-spacing: 1px; margin-bottom: 14px; text-align: center;
    }
    .countdown {
      display: flex; gap: 12px; justify-content: center;
    }
    .count-item {
      background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.08);
      border-radius: 14px; padding: 14px 16px; min-width: 68px; text-align: center;
    }
    .count-num { font-size: 30px; font-weight: 900; color: #FFA76B; display: block; line-height: 1; }
    .count-lbl { font-size: 10px; color: rgba(255,255,255,.4); margin-top: 4px; display: block; }

    /* WhatsApp */
    .btn-wa {
      display: inline-flex; align-items: center; justify-content: center; gap: 10px;
      width: 100%; padding: 13px 24px;
      background: linear-gradient(90deg, #25D366, #1da851);
      color: white; border-radius: 12px; font-size: 15px; font-weight: 800;
      text-decoration: none; margin-bottom: 20px;
      box-shadow: 0 4px 20px rgba(37,211,102,.3);
      transition: transform .2s, opacity .2s;
    }
    .btn-wa:hover { transform: translateY(-2px); opacity: .92; }

    /* Store buttons */
    .stores { display: flex; gap: 8px; margin-bottom: 24px; flex-wrap: wrap; }
    .store-btn {
      flex: 1; min-width: 120px;
      display: flex; align-items: center; gap: 8px;
      background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.09);
      color: #fff; padding: 10px 14px; border-radius: 10px; text-decoration: none;
      font-size: 12px; font-weight: 700;
      transition: background .2s, border-color .2s, transform .15s;
    }
    .store-btn:hover { background: rgba(255,255,255,.09); border-color: rgba(255,255,255,.18); transform: translateY(-2px); }
    .store-btn .store-icon { font-size: 20px; }
    .store-btn small { font-size: 9px; font-weight: 400; opacity: .5; display: block; }

    /* Bottom row */
    .bottom-row {
      display: flex; align-items: center; justify-content: space-between;
      flex-wrap: wrap; gap: 12px;
    }
    .social { display: flex; gap: 8px; }
    .social a {
      width: 36px; height: 36px; border-radius: 9px;
      background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.07);
      display: flex; align-items: center; justify-content: center;
      text-decoration: none; font-size: 15px;
      transition: background .2s, border-color .2s, transform .15s;
    }
    .social a:hover { background: rgba(249,115,22,.1); border-color: rgba(249,115,22,.28); transform: translateY(-2px); }
    .footer-text { font-size: 10px; color: rgba(255,255,255,.18); }

    /* Mobile */
    @media (max-width: 720px) {
      .wrapper { grid-template-columns: 1fr; max-width: 480px; }
      .panel-left {
        align-items: center; text-align: center;
        border-left: none; border-bottom: 1px solid rgba(255,255,255,.06);
        padding: 40px 28px 32px;
      }
      .panel-right { padding: 32px 28px 40px; }
      h1 { font-size: 36px; }
      .features { display: grid; grid-template-columns: 1fr 1fr; }
      .stores { justify-content: center; }
      .bottom-row { justify-content: center; flex-direction: column; align-items: center; }
    }
  </style>
@else
  {{-- ══════════════════════════════════════
       MAINTENANCE  —  simple design
  ══════════════════════════════════════ --}}
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Cairo', sans-serif;
      background: #0B1A3B;
      min-height: 100vh;
      display: flex; align-items: center; justify-content: center;
      overflow: hidden; position: relative;
    }

    .blob {
      position: absolute; border-radius: 50%; filter: blur(80px);
      opacity: 0.25; animation: float 8s ease-in-out infinite;
    }
    .blob-1 { width:500px;height:500px;background:#E8711A;top:-200px;right:-100px;animation-delay:0s; }
    .blob-2 { width:400px;height:400px;background:#1B3B8C;bottom:-150px;left:-100px;animation-delay:3s; }
    .blob-3 { width:250px;height:250px;background:#E8711A;bottom:100px;right:80px;animation-delay:5s; }
    @keyframes float {
      0%,100% { transform: translateY(0) scale(1); }
      50%      { transform: translateY(-30px) scale(1.05); }
    }

    body::before {
      content:''; position:absolute; inset:0;
      background-image:
        linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
      background-size: 48px 48px;
    }

    .card {
      position: relative; z-index: 10; text-align: center;
      padding: 60px 48px; max-width: 580px; width: 90%;
      background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.10);
      border-radius: 32px; backdrop-filter: blur(20px);
    }

    .logo-wrap { margin-bottom: 32px; }
    .logo-icon {
      width: 90px; height: 90px; border-radius: 24px;
      background: linear-gradient(135deg, #1B3B8C, #2448A8);
      border: 2px solid rgba(232,113,26,0.4);
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 16px; font-size: 40px;
      box-shadow: 0 8px 32px rgba(232,113,26,0.25);
    }
    .logo-name { font-size: 18px; font-weight: 900; color: white; }

    .spinner-wrap { margin-bottom: 32px; }
    .spinner {
      width: 56px; height: 56px;
      border: 4px solid rgba(255,255,255,0.1);
      border-top-color: #E8711A;
      border-radius: 50%; margin: 0 auto;
      animation: spin 1.2s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    .title { font-size: 32px; font-weight: 900; color: white; margin-bottom: 14px; line-height: 1.3; }
    .subtitle { font-size: 15px; color: rgba(255,255,255,0.60); line-height: 1.8; margin-bottom: 36px; }

    .btn-wa {
      display: inline-flex; align-items: center; gap: 10px;
      padding: 14px 28px;
      background: linear-gradient(90deg, #25D366, #1da851);
      color: white; border-radius: 16px; font-size: 15px; font-weight: 800;
      text-decoration: none; transition: transform .2s, opacity .2s;
      box-shadow: 0 4px 20px rgba(37,211,102,0.3);
    }
    .btn-wa:hover { transform: translateY(-2px); opacity: .92; }

    .status-bar {
      display: flex; align-items: center; gap: 8px; justify-content: center;
      margin-top: 28px; font-size: 12px; color: rgba(255,255,255,0.4);
    }
    .status-dot {
      width: 8px; height: 8px; background: #25D366;
      border-radius: 50%; animation: blink 2s ease-in-out infinite;
    }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }
  </style>
@endif
</head>
<body>

@if($type === 'coming_soon')
  {{-- ══ COMING SOON BODY ══ --}}
  <canvas id="canvas"></canvas>
  <div class="grid-bg"></div>
  <div class="glow-tr"></div>
  <div class="glow-bl"></div>

  <div class="wrapper">

    {{-- LEFT: Brand --}}
    <div class="panel-left">
      <div class="logo-ring">
        <svg class="ring" viewBox="0 0 88 88" fill="none">
          <circle cx="44" cy="44" r="40" stroke="url(#rg)" stroke-width="1.4" stroke-dasharray="5 4"/>
          <defs>
            <linearGradient id="rg" x1="0" y1="0" x2="88" y2="88" gradientUnits="userSpaceOnUse">
              <stop stop-color="#F97316"/>
              <stop offset="1" stop-color="#3B82F6"/>
            </linearGradient>
          </defs>
        </svg>
        <div class="logo-inner">
          <img src="{{ \App\Support\SiteBranding::logo() }}" alt="أبناء الفريد"
               onerror="this.outerHTML='<span style=\'font-size:26px\'>🛍️</span>'" />
        </div>
      </div>

      <div class="badge"><span class="badge-dot"></span> جاري التطوير</div>

      <h1>شركة<br /><span class="hl">أبناء الفريد</span></h1>

      <p class="subtitle">
        {{ $message ?: 'تطبيقنا الجديد في الطريق إليك' }}<br />
        تسوّق من أكبر معرض منتجات في فلسطين
      </p>

      <div class="features">
        <div class="feature"><span class="feature-icon">🛒</span> آلاف المنتجات</div>
        <div class="feature"><span class="feature-icon">🚚</span> توصيل لكل فلسطين</div>
        <div class="feature"><span class="feature-icon">⭐</span> نقاط ولاء حصرية</div>
        <div class="feature"><span class="feature-icon">🔔</span> إشعارات فورية</div>
      </div>
    </div>

    {{-- RIGHT: Countdown + Actions --}}
    <div class="panel-right">

      <div class="coming-soon-badge">
        <span class="cs-dot"></span>
        🚀 {{ $title ?: 'قريباً — ترقّب الإطلاق!' }}
      </div>

      <div class="divider"></div>

      @if($launch)
        <div class="countdown-section">
          <div class="countdown-label">⏱ العد التنازلي للإطلاق</div>
          <div class="countdown" id="countdown">
            <div class="count-item"><span class="count-num" id="cd-d">00</span><span class="count-lbl">يوم</span></div>
            <div class="count-item"><span class="count-num" id="cd-h">00</span><span class="count-lbl">ساعة</span></div>
            <div class="count-item"><span class="count-num" id="cd-m">00</span><span class="count-lbl">دقيقة</span></div>
            <div class="count-item"><span class="count-num" id="cd-s">00</span><span class="count-lbl">ثانية</span></div>
          </div>
        </div>
      @endif

      @if($wa)
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $wa) }}" class="btn-wa" target="_blank">
          💬 تواصل معنا عبر واتساب
        </a>
      @endif

      <div class="stores">
        <a href="#" class="store-btn">
          <span class="store-icon">▶️</span>
          <div><small>متاح قريباً على</small>Google Play</div>
        </a>
        <a href="#" class="store-btn">
          <span class="store-icon">🍎</span>
          <div><small>متاح قريباً على</small>App Store</div>
        </a>
      </div>

      <div class="bottom-row">
        <div class="social">
          <a href="https://www.tiktok.com/@alfaredsons" target="_blank" title="TikTok">🎵</a>
          <a href="https://www.facebook.com/alfaredsons" target="_blank" title="Facebook">📘</a>
          @if($wa)
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $wa) }}" target="_blank" title="WhatsApp">💬</a>
          @endif
        </div>
        <p class="footer-text">© {{ date('Y') }} أبناء الفريد</p>
      </div>

    </div>
  </div>

@else
  {{-- ══ MAINTENANCE BODY ══ --}}
  <div class="blob blob-1"></div>
  <div class="blob blob-2"></div>
  <div class="blob blob-3"></div>

  <div class="card">
    <div class="logo-wrap">
      <div class="logo-icon">🛍️</div>
      <div class="logo-name">شركة أبناء الفريد</div>
    </div>

    <div class="spinner-wrap"><div class="spinner"></div></div>
    <h1 class="title">{{ $title ?: 'نحن تحت الصيانة' }}</h1>
    <p class="subtitle">{{ $message ?: 'نعمل على تحسين تجربتك — سنعود قريباً!' }}</p>

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
@endif

{{-- Countdown script (coming_soon only) --}}
@if($type === 'coming_soon' && $launch)
<script>
  const target = new Date("{{ $launch }}T00:00:00");
  function update() {
    const diff = Math.max(0, target - new Date());
    const d = Math.floor(diff / 86400000);
    const h = Math.floor((diff % 86400000) / 3600000);
    const m = Math.floor((diff % 3600000)  / 60000);
    const s = Math.floor((diff % 60000)    / 1000);
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

{{-- Particles animation (coming_soon only) --}}
@if($type === 'coming_soon')
<script>
  const canvas = document.getElementById('canvas');
  const ctx = canvas.getContext('2d');
  let P = [];

  function resize() { canvas.width = innerWidth; canvas.height = innerHeight; }
  resize();
  window.addEventListener('resize', () => { resize(); init(); });

  function init() {
    P = [];
    const n = Math.floor((canvas.width * canvas.height) / 13000);
    for (let i = 0; i < n; i++) P.push({
      x: Math.random() * canvas.width,
      y: Math.random() * canvas.height,
      r: Math.random() * 1.4 + .3,
      dx: (Math.random() - .5) * .3,
      dy: (Math.random() - .5) * .3,
      o: Math.random() * .45 + .1,
      c: Math.random() > .5 ? '249,115,22' : '59,130,246'
    });
  }
  init();

  function draw() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    P.forEach(p => {
      ctx.beginPath();
      ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
      ctx.fillStyle = `rgba(${p.c},${p.o})`;
      ctx.fill();
      p.x += p.dx; p.y += p.dy;
      if (p.x < 0 || p.x > canvas.width)  p.dx *= -1;
      if (p.y < 0 || p.y > canvas.height) p.dy *= -1;
    });
    for (let i = 0; i < P.length; i++)
      for (let j = i + 1; j < P.length; j++) {
        const d = Math.hypot(P[i].x - P[j].x, P[i].y - P[j].y);
        if (d < 95) {
          ctx.beginPath();
          ctx.moveTo(P[i].x, P[i].y);
          ctx.lineTo(P[j].x, P[j].y);
          ctx.strokeStyle = `rgba(249,115,22,${(.09*(1-d/95)).toFixed(3)})`;
          ctx.lineWidth = .35;
          ctx.stroke();
        }
      }
    requestAnimationFrame(draw);
  }
  draw();
</script>
@endif

</body>
</html>
