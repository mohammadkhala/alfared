@extends('layouts.app')

@section('title', 'إنشاء حساب — أبناء الفريد')

@section('content')
<div style="min-height:calc(100vh - 320px);display:flex;align-items:center;justify-content:center;padding:48px 20px;background:#F9FAFB;">
  <div style="background:#fff;border-radius:24px;padding:48px;width:100%;max-width:480px;box-shadow:0 8px 40px rgba(27,59,140,.12);">

    <div style="text-align:center;margin-bottom:32px;">
      <div style="width:64px;height:64px;background:linear-gradient(135deg,#E8711A,#d4610e);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" width="28" height="28"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/><path d="M16 3.13a4 4 0 010 7.75"/><path d="M21 21v-2a4 4 0 00-3-3.87"/></svg>
      </div>
      <h1 style="font-size:1.6rem;font-weight:700;color:#1B3B8C;margin:0 0 6px;">إنشاء حساب جديد</h1>
      <p style="color:#6B7280;margin:0;font-size:14px;">انضم إلى عائلة أبناء الفريد</p>
    </div>

    <form action="{{ route('register.submit') }}" method="POST">
      @csrf
      <div style="display:grid;gap:16px;">
        <div>
          <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">الاسم الكامل <span style="color:red;">*</span></label>
          <input type="text" name="name" value="{{ old('name') }}" required placeholder="أحمد محمود"
            style="width:100%;padding:13px 16px;border:1.5px solid {{ $errors->has('name') ? '#EF4444' : '#E5E7EB' }};border-radius:12px;font-family:inherit;font-size:15px;box-sizing:border-box;"/>
          @error('name')<p style="color:#EF4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
        </div>

        <div>
          <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">البريد الإلكتروني <span style="color:red;">*</span></label>
          <input type="email" name="email" value="{{ old('email') }}" required placeholder="email@example.com"
            style="width:100%;padding:13px 16px;border:1.5px solid {{ $errors->has('email') ? '#EF4444' : '#E5E7EB' }};border-radius:12px;font-family:inherit;font-size:15px;box-sizing:border-box;"/>
          @error('email')<p style="color:#EF4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
        </div>

        <div>
          <x-phone-input name="phone" :value="old('phone')" label="رقم الهاتف" :required="true"/>
          @error('phone')<p style="color:#EF4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
        </div>

        <div>
          <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">كلمة المرور <span style="color:red;">*</span></label>
          <input type="password" name="password" required placeholder="••••••••" minlength="8"
            style="width:100%;padding:13px 16px;border:1.5px solid {{ $errors->has('password') ? '#EF4444' : '#E5E7EB' }};border-radius:12px;font-family:inherit;font-size:15px;box-sizing:border-box;"/>
          @error('password')<p style="color:#EF4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
        </div>

        <div>
          <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">تأكيد كلمة المرور <span style="color:red;">*</span></label>
          <input type="password" name="password_confirmation" required placeholder="••••••••"
            style="width:100%;padding:13px 16px;border:1.5px solid #E5E7EB;border-radius:12px;font-family:inherit;font-size:15px;box-sizing:border-box;"/>
        </div>

        <button type="submit" style="padding:15px;font-size:16px;font-weight:700;border-radius:12px;border:none;cursor:pointer;background:linear-gradient(135deg,#E8711A,#d4610e);color:#fff;font-family:inherit;">
          إنشاء الحساب
        </button>
      </div>
    </form>

    <p style="text-align:center;margin-top:24px;font-size:14px;color:#6B7280;">
      لديك حساب بالفعل؟
      <a href="{{ route('login') }}" style="color:#1B3B8C;font-weight:700;text-decoration:none;">تسجيل الدخول</a>
    </p>
  </div>
</div>
@endsection
