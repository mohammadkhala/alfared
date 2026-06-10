@extends('layouts.app')
@section('title', 'إنشاء حساب — أبناء الفريد')

@section('content')
<div style="min-height:calc(100vh - 320px);display:flex;align-items:center;justify-content:center;padding:48px 20px;background:#F9FAFB;">
  <div style="background:#fff;border-radius:24px;padding:48px;width:100%;max-width:480px;box-shadow:0 8px 40px rgba(27,59,140,.12);">

    <div style="text-align:center;margin-bottom:28px;">
      <div style="width:64px;height:64px;background:linear-gradient(135deg,#E8711A,#d4610e);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;">
        📱
      </div>
      <h1 style="font-size:1.6rem;font-weight:700;color:#1B3B8C;margin:0 0 6px;">إنشاء حساب جديد</h1>
      <p style="color:#6B7280;margin:0;font-size:14px;">سنرسل رمز تحقق لرقم واتساب الخاص بك</p>
    </div>

    {{-- Steps indicator --}}
    <div style="display:flex;align-items:center;gap:0;margin-bottom:32px;">
      <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:6px;">
        <div style="width:32px;height:32px;border-radius:50%;background:#1B3B8C;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;">1</div>
        <span style="font-size:11px;color:#1B3B8C;font-weight:600;">بياناتك</span>
      </div>
      <div style="flex:1;height:2px;background:#E5E7EB;margin-bottom:18px;"></div>
      <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:6px;">
        <div style="width:32px;height:32px;border-radius:50%;background:#E5E7EB;color:#9CA3AF;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;">2</div>
        <span style="font-size:11px;color:#9CA3AF;">التحقق</span>
      </div>
    </div>

    @if($errors->any())
      <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:10px;padding:12px 16px;margin-bottom:20px;color:#DC2626;font-size:14px;">
        {{ $errors->first() }}
      </div>
    @endif

    <form action="{{ route('register.submit') }}" method="POST">
      @csrf
      <div style="display:grid;gap:16px;">

        <div>
          <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">الاسم الكامل <span style="color:#EF4444;">*</span></label>
          <input type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="أحمد محمود"
            style="width:100%;padding:13px 16px;border:1.5px solid {{ $errors->has('name') ? '#EF4444' : '#E5E7EB' }};border-radius:12px;font-family:inherit;font-size:15px;box-sizing:border-box;"
            onfocus="this.style.borderColor='#1B3B8C'" onblur="this.style.borderColor='#E5E7EB'"/>
          @error('name')<p style="color:#EF4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
        </div>

        <div>
          <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">رقم الهاتف (واتساب) <span style="color:#EF4444;">*</span></label>
          <x-phone-input name="phone" :value="old('phone')" :required="true"/>
          <p style="color:#9CA3AF;font-size:12px;margin:5px 0 0;">📱 سيُرسل رمز تحقق لهذا الرقم عبر واتساب</p>
          @error('phone')<p style="color:#EF4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
        </div>

        <div>
          <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">
            البريد الإلكتروني <span style="color:#9CA3AF;font-weight:400;">(اختياري)</span>
          </label>
          <input type="email" name="email" value="{{ old('email') }}" placeholder="email@example.com"
            style="width:100%;padding:13px 16px;border:1.5px solid {{ $errors->has('email') ? '#EF4444' : '#E5E7EB' }};border-radius:12px;font-family:inherit;font-size:15px;box-sizing:border-box;"
            onfocus="this.style.borderColor='#1B3B8C'" onblur="this.style.borderColor='#E5E7EB'"/>
          @error('email')<p style="color:#EF4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
        </div>

        <div>
          <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">كلمة المرور <span style="color:#EF4444;">*</span></label>
          <div style="position:relative;">
            <input type="password" name="password" id="pwd1" required placeholder="••••••••" minlength="8"
              style="width:100%;padding:13px 48px 13px 16px;border:1.5px solid {{ $errors->has('password') ? '#EF4444' : '#E5E7EB' }};border-radius:12px;font-family:inherit;font-size:15px;box-sizing:border-box;"
              onfocus="this.style.borderColor='#1B3B8C'" onblur="this.style.borderColor='#E5E7EB'"/>
            <button type="button" onclick="togglePwd('pwd1')" tabindex="-1"
              style="position:absolute;inset-inline-end:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9CA3AF;font-size:18px;">👁</button>
          </div>
          <p style="color:#9CA3AF;font-size:12px;margin:5px 0 0;">8 أحرف على الأقل</p>
          @error('password')<p style="color:#EF4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
        </div>

        <div>
          <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">تأكيد كلمة المرور <span style="color:#EF4444;">*</span></label>
          <div style="position:relative;">
            <input type="password" name="password_confirmation" id="pwd2" required placeholder="••••••••"
              style="width:100%;padding:13px 48px 13px 16px;border:1.5px solid #E5E7EB;border-radius:12px;font-family:inherit;font-size:15px;box-sizing:border-box;"
              onfocus="this.style.borderColor='#1B3B8C'" onblur="this.style.borderColor='#E5E7EB'"/>
            <button type="button" onclick="togglePwd('pwd2')" tabindex="-1"
              style="position:absolute;inset-inline-end:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9CA3AF;font-size:18px;">👁</button>
          </div>
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

        <button type="submit" style="padding:15px;font-size:16px;font-weight:700;border:none;border-radius:12px;cursor:pointer;background:linear-gradient(135deg,#E8711A,#d4610e);color:#fff;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:8px;">
          <span>💬</span> إرسال رمز التحقق
        </button>
      </div>
    </form>

    <p style="text-align:center;margin-top:24px;font-size:14px;color:#6B7280;">
      لديك حساب بالفعل؟
      <a href="{{ route('login') }}" style="color:#1B3B8C;font-weight:700;text-decoration:none;">تسجيل الدخول</a>
    </p>
  </div>
</div>

@push('scripts')
<script>
function togglePwd(id) {
  const f = document.getElementById(id);
  f.type = f.type === 'password' ? 'text' : 'password';
}
</script>
@endpush
@endsection
