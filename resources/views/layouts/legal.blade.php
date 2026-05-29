<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>@yield('title') — شركة أبناء الفريد</title>
  <meta name="description" content="@yield('description','شركة أبناء الفريد — فلسطين')"/>
  <link rel="icon" href="/images/logo.png"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet"/>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --orange: #F97316;
      --blue:   #3B82F6;
    }

    body {
      font-family: 'Cairo', sans-serif;
      background: #080810;
      color: rgba(255,255,255,.82);
      min-height: 100vh;
      line-height: 1.8;
    }

    /* Grid background */
    body::before {
      content: '';
      position: fixed; inset: 0; z-index: 0;
      background-image:
        linear-gradient(rgba(255,255,255,.02) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,.02) 1px, transparent 1px);
      background-size: 55px 55px;
      pointer-events: none;
    }

    /* Glows */
    body::after {
      content: '';
      position: fixed;
      width: 600px; height: 600px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(249,115,22,.1) 0%, transparent 68%);
      top: -150px; right: -150px;
      pointer-events: none; z-index: 0;
    }

    /* ── Topbar ── */
    .topbar {
      position: sticky; top: 0; z-index: 100;
      background: rgba(8,8,16,.85);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border-bottom: 1px solid rgba(255,255,255,.07);
      padding: 0 24px;
    }

    .topbar-inner {
      max-width: 900px; margin: 0 auto;
      display: flex; align-items: center;
      justify-content: space-between;
      height: 60px; gap: 16px;
    }

    .brand {
      display: flex; align-items: center; gap: 10px;
      text-decoration: none;
    }

    .brand img {
      width: 34px; height: 34px;
      object-fit: contain; border-radius: 8px;
    }

    .brand-name {
      font-size: 15px; font-weight: 700;
      color: #fff;
    }

    .brand-name span { color: var(--orange); }

    .back-btn {
      display: flex; align-items: center; gap: 6px;
      font-size: 13px; font-weight: 600;
      color: rgba(255,255,255,.5);
      text-decoration: none;
      padding: 6px 14px; border-radius: 8px;
      border: 1px solid rgba(255,255,255,.08);
      transition: color .2s, border-color .2s, background .2s;
    }

    .back-btn:hover {
      color: #fff;
      border-color: rgba(249,115,22,.3);
      background: rgba(249,115,22,.07);
    }

    /* ── Main content ── */
    .container {
      position: relative; z-index: 1;
      max-width: 780px; margin: 0 auto;
      padding: 48px 24px 80px;
    }

    /* Page header */
    .page-header {
      text-align: center;
      margin-bottom: 48px;
      padding-bottom: 36px;
      border-bottom: 1px solid rgba(255,255,255,.06);
    }

    .page-icon {
      font-size: 48px; margin-bottom: 16px;
      display: block;
      filter: drop-shadow(0 0 20px rgba(249,115,22,.3));
    }

    .page-header h1 {
      font-size: 32px; font-weight: 900;
      color: #fff; margin-bottom: 8px;
    }

    .page-header p {
      font-size: 13px; color: rgba(255,255,255,.3);
    }

    /* Sections */
    .section {
      margin-bottom: 36px;
      background: rgba(255,255,255,.03);
      border: 1px solid rgba(255,255,255,.06);
      border-radius: 16px;
      overflow: hidden;
    }

    .section-header {
      padding: 16px 24px;
      background: rgba(249,115,22,.07);
      border-bottom: 1px solid rgba(255,255,255,.05);
      display: flex; align-items: center; gap: 10px;
    }

    .section-header h2 {
      font-size: 15px; font-weight: 700;
      color: var(--orange);
    }

    .section-icon { font-size: 18px; }

    .section-body {
      padding: 20px 24px;
    }

    .section-body p {
      font-size: 14px;
      color: rgba(255,255,255,.65);
      margin-bottom: 12px;
    }

    .section-body p:last-child { margin-bottom: 0; }

    .section-body ul {
      list-style: none;
      padding: 0;
    }

    .section-body ul li {
      font-size: 14px;
      color: rgba(255,255,255,.65);
      padding: 6px 0;
      padding-right: 20px;
      position: relative;
      border-bottom: 1px solid rgba(255,255,255,.04);
    }

    .section-body ul li:last-child { border-bottom: none; }

    .section-body ul li::before {
      content: '›';
      position: absolute; right: 0;
      color: var(--orange);
      font-weight: 900;
    }

    .highlight-box {
      background: rgba(249,115,22,.08);
      border: 1px solid rgba(249,115,22,.2);
      border-radius: 10px;
      padding: 14px 18px;
      font-size: 13px;
      color: rgba(255,255,255,.7);
      margin-top: 12px;
    }

    .highlight-box strong { color: var(--orange); }

    /* Contact section */
    .contact-row {
      display: flex; gap: 10px; flex-wrap: wrap;
      margin-top: 8px;
    }

    .contact-chip {
      display: inline-flex; align-items: center; gap: 7px;
      padding: 8px 16px;
      background: rgba(255,255,255,.04);
      border: 1px solid rgba(255,255,255,.08);
      border-radius: 8px;
      font-size: 13px; color: rgba(255,255,255,.7);
      text-decoration: none;
      transition: background .2s, border-color .2s;
    }

    .contact-chip:hover {
      background: rgba(249,115,22,.1);
      border-color: rgba(249,115,22,.3);
      color: #fff;
    }

    /* Footer */
    .legal-footer {
      text-align: center;
      padding-top: 36px;
      border-top: 1px solid rgba(255,255,255,.05);
      font-size: 12px;
      color: rgba(255,255,255,.2);
    }

    .legal-footer a {
      color: var(--orange); text-decoration: none; opacity: .75;
      margin: 0 8px;
    }
    .legal-footer a:hover { opacity: 1; }

    @media (max-width: 600px) {
      .topbar-inner { height: 54px; }
      .page-header h1 { font-size: 26px; }
      .section-body { padding: 16px; }
      .section-header { padding: 14px 16px; }
    }
  </style>
</head>
<body>

  <nav class="topbar">
    <div class="topbar-inner">
      <a href="/" class="brand">
        <img src="/images/logo.png" alt="أبناء الفريد"
             onerror="this.style.display='none'"/>
        <span class="brand-name">أبناء <span>الفريد</span></span>
      </a>
      <a href="/" class="back-btn">← العودة للرئيسية</a>
    </div>
  </nav>

  <div class="container">
    @yield('content')

    <footer class="legal-footer">
      <p>© 2025 شركة أبناء الفريد — فلسطين</p>
      <p style="margin-top:8px;">
        <a href="/privacy-policy">سياسة الخصوصية</a>
        <a href="/returns">سياسة الإرجاع</a>
        <a href="/terms">شروط الاستخدام</a>
      </p>
    </footer>
  </div>

</body>
</html>
