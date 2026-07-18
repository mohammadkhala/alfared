@extends('layouts.app')
@section('title', 'التحقق من رقم الهاتف — أبناء الفريد')

@section('content')
<div style="min-height:calc(100vh - 320px);display:flex;align-items:center;justify-content:center;padding:48px 20px;background:#F9FAFB;">
  <div style="background:#fff;border-radius:24px;padding:48px;width:100%;max-width:440px;box-shadow:0 8px 40px rgba(27,59,140,.12);">

    {{-- Steps indicator --}}
    <div style="display:flex;align-items:center;gap:0;margin-bottom:32px;">
      <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:6px;">
        <div style="width:32px;height:32px;border-radius:50%;background:#10B981;color:#fff;display:flex;align-items:center;justify-content:center;font-size:16px;">✓</div>
        <span style="font-size:11px;color:#10B981;font-weight:600;">بياناتك</span>
      </div>
      <div style="flex:1;height:2px;background:#10B981;margin-bottom:18px;"></div>
      <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:6px;">
        <div style="width:32px;height:32px;border-radius:50%;background:#1B3B8C;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;">2</div>
        <span style="font-size:11px;color:#1B3B8C;font-weight:600;">التحقق</span>
      </div>
    </div>

    <div style="text-align:center;margin-bottom:28px;">
      <div style="font-size:48px;margin-bottom:12px;">📧</div>
      <h1 style="font-size:1.4rem;font-weight:700;color:#1B3B8C;margin:0 0 8px;">أكّد بريدك الإلكتروني</h1>
      <p style="color:#6B7280;margin:0;font-size:14px;line-height:1.8;">
        أرسلنا رمزاً مكوناً من 6 أرقام إلى<br/>
        <strong dir="ltr" style="color:#1B3B8C;">{{ session('reg_email') }}</strong><br/>
        <span style="font-size:12px;color:#9CA3AF;">لم تجده؟ تفقّد مجلد الرسائل غير المرغوبة (Spam)</span>
      </p>
    </div>

    @if($errors->any())
      <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:10px;padding:12px 16px;margin-bottom:20px;color:#DC2626;font-size:14px;text-align:center;">
        {{ $errors->first() }}
      </div>
    @endif

    <form action="{{ route('register.verify.submit') }}" method="POST" id="otpForm">
      @csrf
      <input type="hidden" name="code" id="otpValue"/>

      {{-- 6 OTP boxes --}}
      <div style="display:flex;gap:10px;justify-content:center;margin-bottom:28px;direction:ltr;">
        @for($i = 0; $i < 6; $i++)
          <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
            class="otp-box"
            style="width:48px;height:58px;text-align:center;font-size:22px;font-weight:700;color:#1B3B8C;border:2px solid #E5E7EB;border-radius:12px;font-family:inherit;outline:none;transition:border-color .2s;"
            onfocus="this.style.borderColor='#1B3B8C'"
            onblur="this.style.borderColor='#E5E7EB'"/>
        @endfor
      </div>

      {{-- Timer --}}
      <div style="text-align:center;margin-bottom:20px;font-size:14px;color:#6B7280;">
        <span id="timerWrap">صالح لمدة: <strong id="timerDisplay" style="color:#E8711A;">05:00</strong></span>
        <span id="expiredMsg" style="display:none;color:#EF4444;">انتهت صلاحية الرمز — اضغط إعادة الإرسال</span>
      </div>

      <button type="submit" id="submitBtn"
        style="width:100%;padding:15px;font-size:16px;font-weight:700;border:none;border-radius:12px;cursor:pointer;background:linear-gradient(135deg,#1B3B8C,#2d4fb8);color:#fff;font-family:inherit;margin-bottom:12px;">
        تأكيد وإنشاء الحساب ✓
      </button>
    </form>

    <button id="resendBtn" onclick="resendOtp()" disabled
      style="width:100%;padding:13px;border:1.5px solid #E5E7EB;border-radius:12px;background:none;font-size:14px;color:#9CA3AF;font-family:inherit;cursor:not-allowed;">
      إعادة الإرسال (<span id="resendCountdown">60</span>ث)
    </button>

    <div style="text-align:center;margin-top:20px;">
      <a href="{{ route('register') }}" style="color:#6B7280;font-size:13px;text-decoration:none;">← تعديل البيانات</a>
    </div>
  </div>
</div>

@push('scripts')
<script>
const boxes    = document.querySelectorAll('.otp-box');
const otpValue = document.getElementById('otpValue');

boxes.forEach((box, idx) => {
  box.addEventListener('input', e => {
    e.target.value = e.target.value.replace(/[^0-9]/g, '');
    if (e.target.value && idx < boxes.length - 1) boxes[idx + 1].focus();
    sync();
  });
  box.addEventListener('keydown', e => {
    if (e.key === 'Backspace' && !e.target.value && idx > 0) boxes[idx - 1].focus();
  });
  box.addEventListener('paste', e => {
    e.preventDefault();
    const p = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
    [...p].forEach((ch, i) => { if (boxes[idx + i]) boxes[idx + i].value = ch; });
    sync();
    boxes[Math.min(idx + p.length, boxes.length - 1)].focus();
  });
});

function sync() { otpValue.value = [...boxes].map(b => b.value).join(''); }

document.getElementById('otpForm').addEventListener('input', () => {
  if ([...boxes].every(b => b.value)) { sync(); setTimeout(() => document.getElementById('otpForm').submit(), 300); }
});

// Timer
const expiresAt = {{ $sentAt ?? 'Math.floor(Date.now()/1000)' }} + 300;
function tick() {
  const left = expiresAt - Math.floor(Date.now() / 1000);
  if (left <= 0) {
    document.getElementById('timerWrap').style.display  = 'none';
    document.getElementById('expiredMsg').style.display = 'block';
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').style.opacity = '.5';
    return;
  }
  document.getElementById('timerDisplay').textContent =
    String(Math.floor(left / 60)).padStart(2,'0') + ':' + String(left % 60).padStart(2,'0');
  setTimeout(tick, 1000);
}
tick();

// Resend cooldown
let left2 = 60;
const resendBtn = document.getElementById('resendBtn');
const resendCnt = document.getElementById('resendCountdown');
function tickResend() {
  left2--;
  resendCnt.textContent = left2;
  if (left2 <= 0) {
    resendBtn.disabled = false;
    resendBtn.style.cursor = 'pointer';
    resendBtn.style.color = '#1B3B8C';
    resendBtn.style.borderColor = '#1B3B8C';
    resendBtn.textContent = 'إعادة إرسال الرمز';
  } else setTimeout(tickResend, 1000);
}
setTimeout(tickResend, 1000);

function resendOtp() {
  resendBtn.disabled = true;
  resendBtn.textContent = 'جاري الإرسال...';
  fetch('{{ route("register.resend") }}', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
  })
  .then(r => r.json())
  .then(d => { if (d.message) location.reload(); else { resendBtn.textContent = d.error || 'خطأ'; resendBtn.disabled = false; } })
  .catch(() => { resendBtn.textContent = 'خطأ — حاول مجدداً'; resendBtn.disabled = false; });
}

boxes[0].focus();
</script>
@endpush
@endsection
