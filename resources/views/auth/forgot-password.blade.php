@extends('layouts.app')
@section('title', 'استعادة كلمة المرور — أبناء الفريد')

@section('content')
<div style="min-height:calc(100vh - 320px);display:flex;align-items:center;justify-content:center;padding:48px 20px;background:#F9FAFB;">
  <div style="background:#fff;border-radius:24px;padding:48px;width:100%;max-width:440px;box-shadow:0 8px 40px rgba(27,59,140,.12);">

    <div style="text-align:center;margin-bottom:32px;">
      <div style="width:64px;height:64px;background:linear-gradient(135deg,#1B3B8C,#2d4fb8);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" width="28" height="28"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
      </div>
      <h1 style="font-size:1.6rem;font-weight:700;color:#1B3B8C;margin:0 0 6px;">استعادة كلمة المرور</h1>
      <p style="color:#6B7280;margin:0;font-size:14px;line-height:1.7;">أدخل رقم هاتفك وسنرسل لك رمز تحقق عبر واتساب</p>
    </div>

    @if($errors->any())
      <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:10px;padding:12px 16px;margin-bottom:20px;color:#DC2626;font-size:14px;">
        {{ $errors->first() }}
      </div>
    @endif

    <form action="{{ route('password.send') }}" method="POST">
      @csrf

      <div style="margin-bottom:24px;">
        <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">رقم الهاتف</label>
        <x-phone-input name="phone" :value="old('phone')" :required="true"/>
        <p style="color:#9CA3AF;font-size:12px;margin:6px 0 0;">الرقم المسجّل في حسابك</p>
      </div>

      <button type="submit"
        style="width:100%;padding:14px;background:linear-gradient(135deg,#E8711A,#C85E10);color:#fff;border:none;border-radius:12px;font-family:inherit;font-size:15px;font-weight:700;cursor:pointer;">
        إرسال رمز التحقق
      </button>
    </form>

    <p style="text-align:center;margin:24px 0 0;font-size:14px;color:#6B7280;">
      تذكّرت كلمة المرور؟
      <a href="{{ route('login') }}" style="color:#1B3B8C;font-weight:700;">تسجيل الدخول</a>
    </p>

  </div>
</div>
@endsection
