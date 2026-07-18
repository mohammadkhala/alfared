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

    @php $method = old('method', 'whatsapp'); @endphp

    {{-- Delivery method --}}
    <div style="display:flex;gap:8px;background:#F3F4F6;padding:5px;border-radius:12px;margin-bottom:20px;">
      <button type="button" id="tabWa" onclick="pickMethod('whatsapp')"
        style="flex:1;padding:10px;border:none;border-radius:9px;cursor:pointer;font-family:inherit;font-size:13px;font-weight:700;">
        واتساب
      </button>
      <button type="button" id="tabEmail" onclick="pickMethod('email')"
        style="flex:1;padding:10px;border:none;border-radius:9px;cursor:pointer;font-family:inherit;font-size:13px;font-weight:700;">
        البريد الإلكتروني
      </button>
    </div>

    <form action="{{ route('password.send') }}" method="POST">
      @csrf
      <input type="hidden" name="method" id="methodField" value="{{ $method }}"/>

      <div id="waBlock" style="margin-bottom:24px;">
        <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">رقم الهاتف</label>
        <x-phone-input name="phone" :value="old('phone')"/>
        <p style="color:#9CA3AF;font-size:12px;margin:6px 0 0;">الرقم المسجّل في حسابك</p>
      </div>

      <div id="emailBlock" style="margin-bottom:24px;display:none;">
        <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">البريد الإلكتروني</label>
        <input type="email" name="email" value="{{ old('email') }}" dir="ltr" placeholder="name@example.com"
          style="width:100%;padding:13px 16px;border:1.5px solid #E5E7EB;border-radius:12px;font-family:inherit;font-size:15px;box-sizing:border-box;"
          onfocus="this.style.borderColor='#1B3B8C'" onblur="this.style.borderColor='#E5E7EB'"/>
        <p style="color:#9CA3AF;font-size:12px;margin:6px 0 0;">البريد المسجّل في حسابك</p>
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

@push('scripts')
<script>
function pickMethod(m) {
  var isWa = m === 'whatsapp';
  document.getElementById('methodField').value = m;
  document.getElementById('waBlock').style.display    = isWa ? 'block' : 'none';
  document.getElementById('emailBlock').style.display = isWa ? 'none'  : 'block';

  var on  = 'background:#fff;color:#1B3B8C;box-shadow:0 1px 4px rgba(0,0,0,.08);';
  var off = 'background:transparent;color:#6B7280;';
  document.getElementById('tabWa').style.cssText    = document.getElementById('tabWa').style.cssText.replace(/background:[^;]*;|color:[^;]*;|box-shadow:[^;]*;/g,'') + (isWa ? on : off);
  document.getElementById('tabEmail').style.cssText = document.getElementById('tabEmail').style.cssText.replace(/background:[^;]*;|color:[^;]*;|box-shadow:[^;]*;/g,'') + (isWa ? off : on);
}
pickMethod(document.getElementById('methodField').value || 'whatsapp');
</script>
@endpush
@endsection
