@extends('layouts.app')
@section('title', 'الدخول برقم الهاتف — أبناء الفريد')

@section('content')
<div style="min-height:calc(100vh - 320px);display:flex;align-items:center;justify-content:center;padding:48px 20px;background:#F9FAFB;">
  <div style="background:#fff;border-radius:24px;padding:48px;width:100%;max-width:440px;box-shadow:0 8px 40px rgba(27,59,140,.12);">

    {{-- Header --}}
    <div style="text-align:center;margin-bottom:32px;">
      <div style="width:64px;height:64px;background:linear-gradient(135deg,#25D366,#128C7E);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;">
        📱
      </div>
      <h1 style="font-size:1.5rem;font-weight:700;color:#1B3B8C;margin:0 0 6px;">الدخول برقم الهاتف</h1>
      <p style="color:#6B7280;margin:0;font-size:14px;">سنرسل رمز تحقق عبر <strong style="color:#25D366;">واتساب</strong></p>
    </div>

    {{-- Errors --}}
    @if($errors->any())
      <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:10px;padding:12px 16px;margin-bottom:20px;color:#DC2626;font-size:14px;">
        {{ $errors->first() }}
      </div>
    @endif

    {{-- Phone form --}}
    <form action="{{ route('phone-login.send') }}" method="POST">
      @csrf
      <div style="margin-bottom:24px;">
        <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:8px;">رقم الهاتف (واتساب)</label>
        <x-phone-input name="phone" :value="old('phone')" :required="true"/>
        <p style="color:#9CA3AF;font-size:12px;margin:6px 0 0;">مثال: +970 59 1234567</p>
      </div>

      <button type="submit" style="width:100%;padding:15px;font-size:16px;font-weight:700;border:none;border-radius:12px;cursor:pointer;background:linear-gradient(135deg,#25D366,#128C7E);color:#fff;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:10px;">
        <span>💬</span> إرسال رمز التحقق
      </button>
    </form>

    {{-- Divider --}}
    <div style="display:flex;align-items:center;gap:12px;margin:28px 0;">
      <div style="flex:1;height:1px;background:#E5E7EB;"></div>
      <span style="color:#9CA3AF;font-size:13px;">أو</span>
      <div style="flex:1;height:1px;background:#E5E7EB;"></div>
    </div>

    <a href="{{ route('login') }}" style="display:block;text-align:center;padding:13px;border:1.5px solid #1B3B8C;border-radius:12px;color:#1B3B8C;font-weight:600;text-decoration:none;font-size:15px;">
      الدخول بالبريد الإلكتروني
    </a>

    <p style="text-align:center;margin-top:20px;font-size:14px;color:#6B7280;">
      ليس لديك حساب؟
      <a href="{{ route('register') }}" style="color:#1B3B8C;font-weight:700;text-decoration:none;">إنشاء حساب</a>
    </p>
  </div>
</div>
@endsection
