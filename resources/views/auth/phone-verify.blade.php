@extends('layouts.app')
@section('title', 'التحقق من الهاتف — أبناء الفريد')

@section('content')
<div style="min-height:calc(100vh - 320px);display:flex;align-items:center;justify-content:center;padding:48px 20px;background:#F9FAFB;">
  <div style="background:#fff;border-radius:24px;padding:48px;width:100%;max-width:440px;box-shadow:0 8px 40px rgba(27,59,140,.12);">

    {{-- Header --}}
    <div style="text-align:center;margin-bottom:32px;">
      <div style="width:64px;height:64px;background:linear-gradient(135deg,#1B3B8C,#2d4fb8);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;">
        🔐
      </div>
      <h1 style="font-size:1.5rem;font-weight:700;color:#1B3B8C;margin:0 0 8px;">أدخل رمز التحقق</h1>
      <p style="color:#6B7280;margin:0;font-size:14px;">
        أرسلنا رمزاً مكوناً من 6 أرقام عبر واتساب إلى<br/>
        <strong style="color:#1B3B8C;">{{ $phone }}</strong>
      </p>
    </div>

    {{-- Errors --}}
    @if($errors->any())
      <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:10px;padding:12px 16px;margin-bottom:20px;color:#DC2626;font-size:14px;text-align:center;">
        {{ $errors->first() }}
      </div>
    @endif

    {{-- OTP form --}}
    <form action="{{ route('phone-login.verify.submit') }}" method="POST" id="otpForm">
      @csrf
      <input type="hidden" name="code" id="otpValue"/>

      {{-- 6-box OTP input --}}
      <div style="display:flex;gap:10px;justify-content:center;margin-bottom:28px;direction:ltr;">
        @for($i = 0; $i < 6; $i++)
          <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
            class="otp-box"
            style="width:48px;height:58px;text-align:center;font-size:22px;font-weight:700;color:#1B3B8C;border:2px solid #E5E7EB;border-radius:12px;font-family:inherit;outline:none;transition:border-color .2s;"
            onfocus="this.style.borderColor='#1B3B8C'"
            onblur="this.style.borderColor='#E5E7EB'"/>
        @endfor
      </div>

      {{-- Countdown --}}
      <div style="text-align:center;margin-bottom:20px;font-size:14px;color:#6B7280;">
        <span id="timerWrap">صالح لمدة: <strong id="timerDisplay" style="color:#E8711A;">05:00</strong></span>
        <span id="expiredMsg" style="display:none;color:#EF4444;">انتهت صلاحية الرمز — اضغط إعادة الإرسال</span>
      </div>

      <button type="submit" id="submitBtn" style="width:100%;padding:15px;font-size:16px;font-weight:700;border:none;border-radius:12px;cursor:pointer;background:linear-gradient(135deg,#1B3B8C,#2d4fb8);color:#fff;font-family:inherit;margin-bottom:12px;">
        تحقق من الرمز ✓
      </button>
    </form>

    {{-- Resend --}}
    <button id="resendBtn" onclick="resendOtp()" disabled
      style="width:100%;padding:13px;border:1.5px solid #E5E7EB;border-radius:12px;background:none;font-size:14px;color:#9CA3AF;font-family:inherit;cursor:not-allowed;">
      إعادة الإرسال (<span id="resendCountdown">60</span>ث)
    </button>

    <div style="text-align:center;margin-top:20px;">
      <a href="{{ route('phone-login.index') }}" style="color:#6B7280;font-size:13px;text-decoration:none;">← تغيير رقم الهاتف</a>
    </div>
  </div>
</div>

@push('scripts')
<script>
// ── OTP boxes auto-advance ───────────────────────────────────────────────
const boxes = document.querySelectorAll('.otp-box');
const otpValue = document.getElementById('otpValue');

boxes.forEach((box, idx) => {
  box.addEventListener('input', e => {
    const v = e.target.value.replace(/[^0-9]/g, '');
    e.target.value = v;
    if (v && idx < boxes.length - 1) boxes[idx + 1].focus();
    updateOtpValue();
  });

  box.addEventListener('keydown', e => {
    if (e.key === 'Backspace' && !e.target.value && idx > 0) {
      boxes[idx - 1].focus();
    }
  });

  box.addEventListener('paste', e => {
    e.preventDefault();
    const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
    [...pasted].forEach((ch, i) => { if (boxes[idx + i]) boxes[idx + i].value = ch; });
    updateOtpValue();
    const next = Math.min(idx + pasted.length, boxes.length - 1);
    boxes[next].focus();
  });
});

function updateOtpValue() {
  otpValue.value = [...boxes].map(b => b.value).join('');
}

// Auto-submit when all 6 filled
document.getElementById('otpForm').addEventListener('input', () => {
  if ([...boxes].every(b => b.value)) {
    updateOtpValue();
    setTimeout(() => document.getElementById('otpForm').submit(), 300);
  }
});

// ── Countdown timer ──────────────────────────────────────────────────────
const sentAt   = {{ $sentAt ?? 'Math.floor(Date.now()/1000)' }};
const expiresAt = sentAt + 300; // 5 minutes

function updateTimer() {
  const now  = Math.floor(Date.now() / 1000);
  const left = expiresAt - now;
  if (left <= 0) {
    document.getElementById('timerWrap').style.display  = 'none';
    document.getElementById('expiredMsg').style.display = 'block';
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').style.opacity = '.5';
    return;
  }
  const m = String(Math.floor(left / 60)).padStart(2, '0');
  const s = String(left % 60).padStart(2, '0');
  document.getElementById('timerDisplay').textContent = m + ':' + s;
  setTimeout(updateTimer, 1000);
}
updateTimer();

// ── Resend countdown (60s cooldown) ─────────────────────────────────────
let resendLeft = 60;
const resendBtn = document.getElementById('resendBtn');
const resendCnt = document.getElementById('resendCountdown');

function tickResend() {
  resendLeft--;
  resendCnt.textContent = resendLeft;
  if (resendLeft <= 0) {
    resendBtn.disabled = false;
    resendBtn.style.cursor = 'pointer';
    resendBtn.style.color = '#1B3B8C';
    resendBtn.style.borderColor = '#1B3B8C';
    resendBtn.textContent = 'إعادة إرسال الرمز';
  } else {
    setTimeout(tickResend, 1000);
  }
}
setTimeout(tickResend, 1000);

// ── Resend AJAX ──────────────────────────────────────────────────────────
function resendOtp() {
  resendBtn.disabled = true;
  resendBtn.textContent = 'جاري الإرسال...';

  fetch('{{ route("phone-login.resend") }}', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
  })
  .then(r => r.json())
  .then(data => {
    if (data.message) {
      resendBtn.textContent = '✅ تم الإرسال!';
      // Reset timers
      location.reload();
    } else {
      resendBtn.textContent = data.error || 'خطأ في الإرسال';
      resendBtn.disabled = false;
    }
  })
  .catch(() => {
    resendBtn.textContent = 'خطأ — حاول مجدداً';
    resendBtn.disabled = false;
  });
}

// Focus first box
boxes[0].focus();
</script>
@endpush
@endsection
