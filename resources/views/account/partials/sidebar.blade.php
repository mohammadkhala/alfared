<aside class="account-sidebar">
  <div style="background:#fff;border-radius:16px;padding:24px;box-shadow:0 4px 20px rgba(27,59,140,.08);">
    <nav style="display:flex;flex-direction:column;gap:4px;">
      <a href="{{ route('account.index') }}"
        style="display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;text-decoration:none;font-size:14px;font-weight:500;
        {{ request()->routeIs('account.index') ? 'background:#EFF6FF;color:#1B3B8C;font-weight:700;' : 'color:#4B5563;' }}">
        <span>🏠</span> لوحة الحساب
      </a>
      <a href="{{ route('account.orders') }}"
        style="display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;text-decoration:none;font-size:14px;font-weight:500;
        {{ request()->routeIs('account.orders') ? 'background:#EFF6FF;color:#1B3B8C;font-weight:700;' : 'color:#4B5563;' }}">
        <span>📦</span> طلباتي
      </a>
      <a href="{{ route('account.wishlist') }}"
        style="display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;text-decoration:none;font-size:14px;font-weight:500;
        {{ request()->routeIs('account.wishlist') ? 'background:#EFF6FF;color:#1B3B8C;font-weight:700;' : 'color:#4B5563;' }}">
        <span>♡</span> المفضلة
      </a>
      <a href="{{ route('account.points') }}"
        style="display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;text-decoration:none;font-size:14px;font-weight:500;
        {{ request()->routeIs('account.points') ? 'background:#EFF6FF;color:#1B3B8C;font-weight:700;' : 'color:#4B5563;' }}">
        <span>⭐</span> نقاط الولاء
      </a>
      <a href="{{ route('account.profile') }}"
        style="display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;text-decoration:none;font-size:14px;font-weight:500;
        {{ request()->routeIs('account.profile') ? 'background:#EFF6FF;color:#1B3B8C;font-weight:700;' : 'color:#4B5563;' }}">
        <span>✏️</span> تعديل البيانات
      </a>
      <div style="border-top:1px solid #E5E7EB;margin:8px 0;"></div>
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" style="width:100%;display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;border:none;background:none;cursor:pointer;font-size:14px;color:#EF4444;font-family:inherit;font-weight:500;">
          <span>🚪</span> تسجيل الخروج
        </button>
      </form>
    </nav>
  </div>
</aside>
