@extends('layouts.app')
@section('title', 'إتمام إنشاء الحساب — أبناء الفريد')

@section('content')
<div style="min-height:calc(100vh - 320px);display:flex;align-items:center;justify-content:center;padding:48px 20px;background:#F9FAFB;">
  <div style="background:#fff;border-radius:24px;padding:48px;width:100%;max-width:440px;box-shadow:0 8px 40px rgba(27,59,140,.12);">

    {{-- Header --}}
    <div style="text-align:center;margin-bottom:28px;">
      <div style="width:64px;height:64px;background:linear-gradient(135deg,#E8711A,#d4610e);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;">
        🎉
      </div>
      <h1 style="font-size:1.5rem;font-weight:700;color:#1B3B8C;margin:0 0 8px;">مرحباً بك!</h1>
      <p style="color:#6B7280;margin:0;font-size:14px;">
        تم التحقق من رقمك <strong style="color:#25D366;">{{ $phone }}</strong><br/>
        أدخل اسمك لإتمام إنشاء الحساب
      </p>
    </div>

    {{-- Verified badge --}}
    <div style="background:#F0FDF4;border:1px solid #BBF7D0;border-radius:10px;padding:10px 16px;margin-bottom:24px;display:flex;align-items:center;gap:10px;font-size:14px;color:#166534;">
      <span>✅</span>
      <span>رقم الهاتف تم التحقق منه عبر واتساب</span>
    </div>

    {{-- Errors --}}
    @if($errors->any())
      <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:10px;padding:12px 16px;margin-bottom:20px;color:#DC2626;font-size:14px;">
        {{ $errors->first() }}
      </div>
    @endif

    <form action="{{ route('phone-login.register.submit') }}" method="POST">
      @csrf
      <div style="display:grid;gap:16px;">

        <div>
          <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">الاسم الكامل <span style="color:#EF4444;">*</span></label>
          <input type="text" name="name" value="{{ old('name') }}" required autofocus
            placeholder="أحمد محمود"
            style="width:100%;padding:13px 16px;border:1.5px solid {{ $errors->has('name') ? '#EF4444' : '#E5E7EB' }};border-radius:12px;font-family:inherit;font-size:15px;box-sizing:border-box;"
            onfocus="this.style.borderColor='#1B3B8C'" onblur="this.style.borderColor='#E5E7EB'"/>
          @error('name')<p style="color:#EF4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
        </div>

        <div>
          <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">البريد الإلكتروني <span style="color:#9CA3AF;font-weight:400;">(اختياري)</span></label>
          <input type="email" name="email" value="{{ old('email') }}"
            placeholder="email@example.com"
            style="width:100%;padding:13px 16px;border:1.5px solid {{ $errors->has('email') ? '#EF4444' : '#E5E7EB' }};border-radius:12px;font-family:inherit;font-size:15px;box-sizing:border-box;"
            onfocus="this.style.borderColor='#1B3B8C'" onblur="this.style.borderColor='#E5E7EB'"/>
          @error('email')<p style="color:#EF4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
        </div>

        {{-- Privacy Policy --}}
        <div>
          <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;">
            <input type="checkbox" name="privacy_policy" id="privacy_policy" value="1" required
              style="margin-top:3px;width:18px;height:18px;accent-color:#1B3B8C;flex-shrink:0;cursor:pointer;"/>
            <span style="font-size:13px;color:#374151;line-height:1.5;">
              أوافق على
              <a href="{{ route('privacy-policy') }}" target="_blank" style="color:#1B3B8C;font-weight:700;text-decoration:underline;">سياسة الخصوصية وشروط الاستخدام</a>
              الخاصة بمتجر أبناء الفريد
            </span>
          </label>
          @error('privacy_policy')
            <p style="color:#EF4444;font-size:12px;margin:6px 0 0;">{{ $message }}</p>
          @enderror
        </div>

        <button type="submit" style="padding:15px;font-size:16px;font-weight:700;border:none;border-radius:12px;cursor:pointer;background:linear-gradient(135deg,#E8711A,#d4610e);color:#fff;font-family:inherit;">
          إنشاء الحساب والدخول 🚀
        </button>
      </div>
    </form>

  </div>
</div>
@endsection
