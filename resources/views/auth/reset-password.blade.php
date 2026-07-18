@extends('layouts.app')
@section('title', 'تعيين كلمة مرور جديدة — أبناء الفريد')

@section('content')
<div style="min-height:calc(100vh - 320px);display:flex;align-items:center;justify-content:center;padding:48px 20px;background:#F9FAFB;">
  <div style="background:#fff;border-radius:24px;padding:48px;width:100%;max-width:440px;box-shadow:0 8px 40px rgba(27,59,140,.12);">

    <div style="text-align:center;margin-bottom:32px;">
      @if($method === 'email')
        <div style="width:64px;height:64px;background:linear-gradient(135deg,#1B3B8C,#2d4fb8);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
          <svg viewBox="0 0 24 24" fill="#fff" width="28" height="28"><path d="M20 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2zm0 4.24l-8 4.99-8-4.99V6l8 4.99L20 6v2.24z"/></svg>
        </div>
      @else
        <div style="width:64px;height:64px;background:linear-gradient(135deg,#25D366,#128C7E);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
          <svg viewBox="0 0 24 24" fill="#fff" width="28" height="28"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        </div>
      @endif
      <h1 style="font-size:1.6rem;font-weight:700;color:#1B3B8C;margin:0 0 6px;">أدخل الرمز</h1>
      <p style="color:#6B7280;margin:0;font-size:14px;line-height:1.7;">
        @if($method === 'email')
          إذا كان البريد مسجّلاً فستصلك رسالة على<br/>
        @else
          إذا كان الرقم مسجّلاً فستصلك رسالة واتساب على<br/>
        @endif
        <strong dir="ltr" style="color:#1B3B8C;">{{ $label }}</strong>
      </p>
    </div>

    @if($errors->any())
      <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:10px;padding:12px 16px;margin-bottom:20px;color:#DC2626;font-size:14px;">
        {{ $errors->first() }}
      </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST">
      @csrf

      <div style="margin-bottom:16px;">
        <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">رمز التحقق</label>
        <input type="text" name="code" required inputmode="numeric" maxlength="6" autocomplete="one-time-code"
          placeholder="——————" dir="ltr"
          style="width:100%;padding:13px 16px;border:1.5px solid #E5E7EB;border-radius:12px;font-family:inherit;font-size:22px;font-weight:700;letter-spacing:8px;text-align:center;box-sizing:border-box;"
          onfocus="this.style.borderColor='#1B3B8C'" onblur="this.style.borderColor='#E5E7EB'"/>
      </div>

      <div style="margin-bottom:16px;">
        <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">كلمة المرور الجديدة</label>
        <input type="password" name="password" required minlength="8" placeholder="8 أحرف على الأقل"
          style="width:100%;padding:13px 16px;border:1.5px solid #E5E7EB;border-radius:12px;font-family:inherit;font-size:15px;box-sizing:border-box;"
          onfocus="this.style.borderColor='#1B3B8C'" onblur="this.style.borderColor='#E5E7EB'"/>
      </div>

      <div style="margin-bottom:24px;">
        <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">تأكيد كلمة المرور</label>
        <input type="password" name="password_confirmation" required minlength="8" placeholder="أعد كتابتها"
          style="width:100%;padding:13px 16px;border:1.5px solid #E5E7EB;border-radius:12px;font-family:inherit;font-size:15px;box-sizing:border-box;"
          onfocus="this.style.borderColor='#1B3B8C'" onblur="this.style.borderColor='#E5E7EB'"/>
      </div>

      <button type="submit"
        style="width:100%;padding:14px;background:linear-gradient(135deg,#E8711A,#C85E10);color:#fff;border:none;border-radius:12px;font-family:inherit;font-size:15px;font-weight:700;cursor:pointer;">
        تغيير كلمة المرور
      </button>
    </form>

    <p style="text-align:center;margin:24px 0 0;font-size:14px;color:#6B7280;">
      لم يصلك الرمز؟
      <a href="{{ route('password.request') }}" style="color:#1B3B8C;font-weight:700;">إعادة المحاولة</a>
    </p>

  </div>
</div>
@endsection
