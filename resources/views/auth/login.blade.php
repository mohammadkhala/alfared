@extends('layouts.app')

@section('title', 'تسجيل الدخول — أبناء الفريد')

@section('content')
<div style="min-height:calc(100vh - 320px);display:flex;align-items:center;justify-content:center;padding:48px 20px;background:#F9FAFB;">
  <div style="background:#fff;border-radius:24px;padding:48px;width:100%;max-width:440px;box-shadow:0 8px 40px rgba(27,59,140,.12);">

    <div style="text-align:center;margin-bottom:32px;">
      <div style="width:64px;height:64px;background:linear-gradient(135deg,#1B3B8C,#2d4fb8);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" width="28" height="28"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </div>
      <h1 style="font-size:1.6rem;font-weight:700;color:#1B3B8C;margin:0 0 6px;">تسجيل الدخول</h1>
      <p style="color:#6B7280;margin:0;font-size:14px;">أهلاً بك في شركة أبناء الفريد</p>
    </div>

    <form action="{{ route('login.submit') }}" method="POST">
      @csrf
      <div class="form-group" style="margin-bottom:16px;">
        <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">البريد الإلكتروني</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus
          placeholder="email@example.com"
          style="width:100%;padding:13px 16px;border:1.5px solid {{ $errors->has('email') ? '#EF4444' : '#E5E7EB' }};border-radius:12px;font-family:inherit;font-size:15px;box-sizing:border-box;transition:border-color .2s;"
          onfocus="this.style.borderColor='#1B3B8C'" onblur="this.style.borderColor='#E5E7EB'"/>
        @error('email')<p style="color:#EF4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
      </div>

      <div class="form-group" style="margin-bottom:24px;">
        <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">كلمة المرور</label>
        <input type="password" name="password" required
          placeholder="••••••••"
          style="width:100%;padding:13px 16px;border:1.5px solid #E5E7EB;border-radius:12px;font-family:inherit;font-size:15px;box-sizing:border-box;transition:border-color .2s;"
          onfocus="this.style.borderColor='#1B3B8C'" onblur="this.style.borderColor='#E5E7EB'"/>
      </div>

      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;color:#4B5563;">
          <input type="checkbox" name="remember" style="accent-color:#1B3B8C;width:16px;height:16px;"/>
          تذكرني
        </label>
      </div>

      <button type="submit" class="btn btn-blue" style="width:100%;padding:15px;font-size:16px;font-weight:700;border-radius:12px;">
        تسجيل الدخول
      </button>
    </form>

    {{-- Divider --}}
    <div style="display:flex;align-items:center;gap:12px;margin:24px 0 20px;">
      <div style="flex:1;height:1px;background:#E5E7EB;"></div>
      <span style="color:#9CA3AF;font-size:13px;">أو</span>
      <div style="flex:1;height:1px;background:#E5E7EB;"></div>
    </div>

    <a href="{{ route('phone-login.index') }}" style="display:flex;align-items:center;justify-content:center;gap:10px;padding:13px;border:1.5px solid #25D366;border-radius:12px;color:#128C7E;font-weight:600;text-decoration:none;font-size:15px;background:#F0FFF4;">
      <span style="font-size:18px;">💬</span> الدخول برقم الهاتف (واتساب)
    </a>

    <p style="text-align:center;margin-top:20px;font-size:14px;color:#6B7280;">
      ليس لديك حساب؟
      <a href="{{ route('register') }}" style="color:#1B3B8C;font-weight:700;text-decoration:none;">إنشاء حساب جديد</a>
    </p>
  </div>
</div>
@endsection
