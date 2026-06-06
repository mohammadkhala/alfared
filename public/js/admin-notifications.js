/**
 * Admin Notifications — new-order sound + side popup + browser notification
 *
 * Watches the Filament bell-icon badge for an increase in unread count.
 * On increase:
 *  - Plays a two-tone "ding" via Web Audio (no MP3 needed)
 *  - Fetches the latest unread notification and shows a slide-in popup
 *  - Shows a desktop notification if the user granted permission
 */
(function () {
  'use strict';

  // ════════ Sound ════════════════════════════════════════════════════════
  function playBeep() {
    try {
      const ctx = new (window.AudioContext || window.webkitAudioContext)();
      const now = ctx.currentTime;
      [880, 1320].forEach((freq, i) => {
        const osc  = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type            = 'sine';
        osc.frequency.value = freq;
        gain.gain.setValueAtTime(0.0001, now + i * 0.18);
        gain.gain.exponentialRampToValueAtTime(0.28, now + i * 0.18 + 0.02);
        gain.gain.exponentialRampToValueAtTime(0.0001, now + i * 0.18 + 0.30);
        osc.connect(gain).connect(ctx.destination);
        osc.start(now + i * 0.18);
        osc.stop(now + i * 0.18 + 0.32);
      });
      setTimeout(() => { try { ctx.close(); } catch (_) {} }, 800);
    } catch (_) { /* ignore */ }
  }

  // ════════ Side popup container ═════════════════════════════════════════
  function ensurePopupHost() {
    let host = document.getElementById('aOrderPopupHost');
    if (host) return host;
    host = document.createElement('div');
    host.id = 'aOrderPopupHost';
    host.style.cssText = `
      position:fixed; top:80px; left:20px; z-index:99999;
      display:flex; flex-direction:column; gap:10px;
      max-width:360px; pointer-events:none;
    `;
    document.body.appendChild(host);

    // inject keyframes once
    if (!document.getElementById('aOrderPopupStyle')) {
      const s = document.createElement('style');
      s.id = 'aOrderPopupStyle';
      s.textContent = `
        @keyframes aOrderIn  { from { transform:translateX(-120%); opacity:0; } to { transform:translateX(0); opacity:1; } }
        @keyframes aOrderOut { from { transform:translateX(0); opacity:1; } to { transform:translateX(-120%); opacity:0; } }
        @keyframes aOrderPulse { 0%,100% { box-shadow:0 12px 32px rgba(232,113,26,.35); } 50% { box-shadow:0 12px 32px rgba(232,113,26,.65); } }
        .a-order-popup {
          background:#fff; border-radius:14px; padding:16px;
          box-shadow:0 12px 32px rgba(0,0,0,.18);
          border-inline-start:4px solid #E8711A;
          animation: aOrderIn .3s ease-out, aOrderPulse 1.4s ease-in-out 2;
          pointer-events:auto; cursor:pointer;
          font-family:'Cairo',sans-serif;
        }
        .a-order-popup.closing { animation: aOrderOut .3s ease-in forwards; }
        .a-order-popup .a-row { display:flex; align-items:flex-start; gap:12px; }
        .a-order-popup .a-icon {
          width:42px; height:42px; border-radius:50%;
          background:linear-gradient(135deg,#E8711A,#d4610e);
          color:#fff; font-size:20px;
          display:flex; align-items:center; justify-content:center; flex-shrink:0;
        }
        .a-order-popup .a-title { font-size:14px; font-weight:800; color:#122870; margin-bottom:2px; }
        .a-order-popup .a-body  { font-size:12px; color:#475569; line-height:1.5; }
        .a-order-popup .a-time  { font-size:10px; color:#94A3B8; margin-top:6px; }
        .a-order-popup .a-close {
          background:none; border:none; color:#94A3B8;
          cursor:pointer; font-size:16px; padding:0; line-height:1;
          margin-inline-start:auto; flex-shrink:0;
        }
        .a-order-popup .a-close:hover { color:#EF4444; }
      `;
      document.head.appendChild(s);
    }
    return host;
  }

  function showPopup({ title, body, time, url }) {
    const host = ensurePopupHost();
    const card = document.createElement('div');
    card.className = 'a-order-popup';
    card.innerHTML = `
      <div class="a-row">
        <div class="a-icon">🛒</div>
        <div style="flex:1; min-width:0;">
          <div class="a-title"></div>
          <div class="a-body"></div>
          <div class="a-time"></div>
        </div>
        <button class="a-close" type="button" aria-label="إغلاق">✕</button>
      </div>
    `;
    card.querySelector('.a-title').textContent = title || 'إشعار جديد';
    card.querySelector('.a-body').textContent  = body  || '';
    card.querySelector('.a-time').textContent  = time  || '';

    const remove = () => {
      card.classList.add('closing');
      setTimeout(() => card.remove(), 300);
    };

    card.querySelector('.a-close').addEventListener('click', (e) => {
      e.stopPropagation();
      remove();
    });

    if (url) {
      card.addEventListener('click', () => { window.location.href = url; });
    }

    host.appendChild(card);
    setTimeout(remove, 8000);
  }

  // ════════ Browser desktop notification ═════════════════════════════════
  function showDesktopNotification(title, body) {
    if (!('Notification' in window)) return;
    if (Notification.permission === 'granted') {
      try { new Notification(title, { body, icon: '/images/logo.png' }); }
      catch (_) {}
    }
  }
  function requestPermissionOnce() {
    if ('Notification' in window && Notification.permission === 'default') {
      Notification.requestPermission().catch(() => {});
    }
  }
  document.addEventListener('click',  requestPermissionOnce, { once: true });
  document.addEventListener('keydown', requestPermissionOnce, { once: true });

  // ════════ Fetch latest notification details ═════════════════════════════
  let lastNotificationId = null;
  async function fetchLatestAndShow() {
    try {
      const res = await fetch('/admin-api/latest-notification', {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      });
      if (!res.ok) return;
      const json = await res.json();
      const n = json.notification;
      if (!n || n.id === lastNotificationId) return;
      lastNotificationId = n.id;
      showPopup({ title: n.title, body: n.body, time: n.time, url: n.url });
      showDesktopNotification(n.title || 'إشعار جديد', n.body || '');
    } catch (_) { /* ignore */ }
  }

  // ════════ Watch the Filament bell badge for increases ══════════════════
  let lastCount = null;
  function getUnreadCount() {
    const badge = document.querySelector(
      '[x-data*="databaseNotifications"] [data-badge], ' +
      '.fi-topbar-database-notifications-btn .fi-badge, ' +
      '.fi-topbar-database-notifications [aria-label*="unread" i], ' +
      'button[aria-label*="إشعار"] .fi-badge'
    );
    if (!badge) return 0;
    const text = (badge.textContent || '').trim();
    if (!text) return 0;
    const n = parseInt(text.replace(/[^\d]/g, ''), 10);
    return isNaN(n) ? 0 : n;
  }

  function tick() {
    const count = getUnreadCount();
    if (lastCount === null) {
      lastCount = count;
      // also seed lastNotificationId so we don't pop on initial load
      fetch('/admin-api/latest-notification', {
        headers: { 'Accept': 'application/json' },
        credentials: 'same-origin',
      }).then(r => r.ok ? r.json() : null)
        .then(j => { if (j && j.notification) lastNotificationId = j.notification.id; })
        .catch(() => {});
      return;
    }
    if (count > lastCount) {
      playBeep();
      fetchLatestAndShow();
    }
    lastCount = count;
  }

  // First tick after DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', tick);
  } else {
    setTimeout(tick, 500);
  }

  // Poll every 3s
  setInterval(tick, 3000);

  // Also fire on Livewire morph updates (faster)
  document.addEventListener('livewire:initialized', () => {
    if (window.Livewire) {
      window.Livewire.hook('morph.updated', tick);
    }
  });

  // ════════ Make Filament bell-dropdown notifications clickable ══════════
  // The user can click anywhere on a notification card to open the order.
  document.addEventListener('click', function (e) {
    // Find the parent notification card
    const card = e.target.closest('.fi-no-notification');
    if (!card) return;

    // Ignore clicks on the close button, action buttons, or existing links
    if (e.target.closest('.fi-no-notification-close-btn')) return;
    if (e.target.closest('.fi-no-notification-actions a, .fi-no-notification-actions button')) return;
    if (e.target.closest('a, button')) return;

    // Find an action link inside this card
    const link = card.querySelector('.fi-no-notification-actions a[href]');
    if (!link) return;

    // Trigger the action (marks as read + navigates)
    e.preventDefault();
    link.click();
  }, true);

  // Add hover styles + cursor to notifications dynamically
  const cursorStyle = document.createElement('style');
  cursorStyle.textContent = `
    .fi-no-notification:has(.fi-no-notification-actions a[href]) {
      cursor: pointer;
      transition: background .15s ease;
    }
    .fi-no-notification:has(.fi-no-notification-actions a[href]):hover {
      background: rgba(232, 113, 26, 0.04);
    }
  `;
  document.head.appendChild(cursorStyle);
})();

// ════════════════════════════════════════════════════════════════════════════
//  Image Zoom Lightbox — click any product image in the admin to enlarge it
// ════════════════════════════════════════════════════════════════════════════
(function () {
  'use strict';

  // ── inject CSS once ───────────────────────────────────────────────────────
  const style = document.createElement('style');
  style.textContent = `
    #aImgOverlay {
      position: fixed; inset: 0; z-index: 99999;
      background: rgba(0, 0, 0, .82);
      display: flex; align-items: center; justify-content: center;
      cursor: zoom-out;
      animation: aOverlayIn .18s ease-out;
    }
    @keyframes aOverlayIn {
      from { opacity: 0; }
      to   { opacity: 1; }
    }
    #aImgOverlay img {
      max-width: 92vw; max-height: 88vh;
      border-radius: 14px;
      box-shadow: 0 30px 80px rgba(0, 0, 0, .7);
      animation: aImgPop .22s cubic-bezier(.34,1.56,.64,1);
      pointer-events: none;
      user-select: none;
    }
    @keyframes aImgPop {
      from { transform: scale(.7); opacity: 0; }
      to   { transform: scale(1);  opacity: 1; }
    }
    #aImgOverlay .a-zoom-close {
      position: absolute; top: 18px; left: 18px;
      width: 38px; height: 38px; border-radius: 50%;
      background: rgba(255,255,255,.15);
      border: none; color: #fff; font-size: 20px; line-height: 1;
      cursor: pointer; display: flex; align-items: center; justify-content: center;
      transition: background .15s;
      pointer-events: auto;
    }
    #aImgOverlay .a-zoom-close:hover { background: rgba(255,255,255,.28); }

    /* make Filament image entries look clickable */
    .fi-in-image-entry img,
    .fi-in-image img {
      cursor: zoom-in !important;
      transition: transform .18s, box-shadow .18s;
    }
    .fi-in-image-entry img:hover,
    .fi-in-image img:hover {
      transform: scale(1.06);
      box-shadow: 0 6px 24px rgba(0,0,0,.22);
    }
  `;
  document.head.appendChild(style);

  // ── open lightbox ─────────────────────────────────────────────────────────
  function openLightbox(src, alt) {
    if (document.getElementById('aImgOverlay')) return;   // already open

    const overlay = document.createElement('div');
    overlay.id = 'aImgOverlay';

    const img = document.createElement('img');
    img.src = src;
    img.alt = alt || '';

    const closeBtn = document.createElement('button');
    closeBtn.className = 'a-zoom-close';
    closeBtn.innerHTML = '✕';
    closeBtn.setAttribute('aria-label', 'إغلاق');

    overlay.appendChild(img);
    overlay.appendChild(closeBtn);
    document.body.appendChild(overlay);

    function close() {
      overlay.style.animation = 'aOverlayIn .15s ease-out reverse forwards';
      setTimeout(() => overlay.remove(), 150);
    }

    overlay.addEventListener('click', close);
    closeBtn.addEventListener('click', (e) => { e.stopPropagation(); close(); });
    document.addEventListener('keydown', function esc(e) {
      if (e.key === 'Escape') { close(); document.removeEventListener('keydown', esc); }
    });
  }

  // ── event delegation: catch clicks on any fi-image-entry img ─────────────
  document.addEventListener('click', function (e) {
    const img = e.target.closest(
      '.fi-in-image-entry img, .fi-in-image img'
    );
    if (!img) return;
    e.preventDefault();
    e.stopPropagation();
    openLightbox(img.src, img.alt);
  });
})();
