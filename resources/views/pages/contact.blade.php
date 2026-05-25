@extends('layouts.app')
@section('title', 'اتصل بنا — ' . __('company_name'))
@section('description', 'تواصل مع شركة ابناء الفريد — نحن هنا لمساعدتك في أي استفسار')

@push('styles')
<style>
.page-hero {
  background: linear-gradient(135deg, var(--blue-dk) 0%, var(--blue) 60%, #2448A8 100%);
  padding: 64px 24px 80px;
  text-align: center;
  position: relative;
  overflow: hidden;
}
.page-hero::before {
  content: '';
  position: absolute;
  width: 340px; height: 340px;
  border-radius: 50%;
  background: rgba(232,113,26,0.10);
  top: -120px; right: -80px;
}
.page-hero::after {
  content: '';
  position: absolute;
  width: 200px; height: 200px;
  border-radius: 50%;
  background: rgba(255,255,255,0.04);
  bottom: -60px; left: -40px;
}
.page-hero h1 { font-size: 36px; font-weight: 900; color: white; margin-bottom: 12px; }
.page-hero p  { font-size: 15px; color: rgba(255,255,255,0.65); max-width: 500px; margin: 0 auto; line-height: 1.7; }

.contact-grid {
  display: grid;
  grid-template-columns: 1fr 1.4fr;
  gap: 32px;
  padding: 60px 0;
}
@media(max-width:768px) {
  .contact-grid { grid-template-columns: 1fr; padding: 32px 0; }
}

.contact-info-card {
  background: white;
  border-radius: 20px;
  padding: 32px;
  box-shadow: 0 4px 24px rgba(27,59,140,0.08);
  display: flex;
  flex-direction: column;
  gap: 24px;
}
.contact-info-card h2 {
  font-size: 20px; font-weight: 900; color: var(--blue); margin-bottom: 4px;
}
.contact-info-card .subtitle {
  font-size: 13px; color: var(--gray); line-height: 1.7;
}
.contact-item {
  display: flex;
  align-items: flex-start;
  gap: 14px;
}
.contact-icon {
  width: 44px; height: 44px;
  border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
  font-size: 20px;
  flex-shrink: 0;
}
.contact-icon.blue   { background: var(--blue-lt); }
.contact-icon.orange { background: var(--orange-lt); }
.contact-icon.green  { background: #ECFDF5; }
.contact-icon.purple { background: #F5F3FF; }
.contact-item-body strong {
  display: block; font-size: 13px; font-weight: 800; color: var(--text); margin-bottom: 3px;
}
.contact-item-body a, .contact-item-body span {
  font-size: 13px; color: var(--gray); line-height: 1.6;
}
.contact-item-body a:hover { color: var(--orange); }

.social-row {
  display: flex; gap: 10px; flex-wrap: wrap;
}
.social-chip {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 8px 14px;
  border-radius: 12px;
  font-size: 12px; font-weight: 700;
  transition: all .2s;
}
.social-chip.whatsapp { background: #ECFDF5; color: #059669; }
.social-chip.facebook { background: #EFF6FF; color: #1D4ED8; }
.social-chip.tiktok   { background: #F9FAFB; color: #111; border: 1px solid #E5E7EB; }
.social-chip:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.10); }

/* Contact Form */
.contact-form-card {
  background: white;
  border-radius: 20px;
  padding: 36px;
  box-shadow: 0 4px 24px rgba(27,59,140,0.08);
}
.contact-form-card h2 {
  font-size: 20px; font-weight: 900; color: var(--blue); margin-bottom: 6px;
}
.contact-form-card .subtitle {
  font-size: 13px; color: var(--gray); margin-bottom: 24px; line-height: 1.6;
}
.form-group { margin-bottom: 16px; }
.form-group label {
  display: block; font-size: 12px; font-weight: 800; color: var(--text); margin-bottom: 6px;
}
.form-group input, .form-group textarea {
  width: 100%;
  padding: 12px 16px;
  border: 1.5px solid #E5E7EB;
  border-radius: 12px;
  font-family: 'Cairo', sans-serif;
  font-size: 13px;
  color: var(--text);
  background: var(--gray-bg);
  outline: none;
  transition: border-color .2s, background .2s;
  resize: vertical;
}
.form-group input:focus, .form-group textarea:focus {
  border-color: var(--blue); background: white;
}
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
@media(max-width:500px) { .form-row { grid-template-columns: 1fr; } }

.btn-submit {
  width: 100%;
  height: 52px;
  background: linear-gradient(90deg, var(--orange), #C85E10);
  color: white;
  border: none;
  border-radius: 14px;
  font-family: 'Cairo', sans-serif;
  font-size: 15px;
  font-weight: 800;
  cursor: pointer;
  transition: opacity .2s, transform .2s;
  display: flex; align-items: center; justify-content: center; gap: 8px;
}
.btn-submit:hover { opacity: .92; transform: translateY(-1px); }

/* Map section */
.map-section { padding: 0 0 60px; }
.map-section h2 {
  font-size: 22px; font-weight: 900; color: var(--text); margin-bottom: 20px;
}
.map-frame {
  border-radius: 20px; overflow: hidden;
  box-shadow: 0 4px 24px rgba(27,59,140,0.10);
  border: none; width: 100%; height: 380px; display: block;
}
</style>
@endpush

@section('content')

{{-- Hero --}}
<div class="page-hero">
  <h1>📞 اتصل بنا</h1>
  <p>نحن هنا لمساعدتك في أي استفسار أو طلب — تواصل معنا الآن</p>
</div>

<div class="container">

  {{-- Contact grid --}}
  <div class="contact-grid">

    {{-- Left: Info --}}
    <div class="contact-info-card">
      <div>
        <h2>معلومات التواصل</h2>
        <p class="subtitle">نسعد بتواصلك معنا في أي وقت وسنرد عليك في أقرب وقت ممكن</p>
      </div>

      <div class="contact-item">
        <div class="contact-icon orange">📞</div>
        <div class="contact-item-body">
          <strong>رقم الهاتف</strong>
          <a href="tel:+970598191312" dir="ltr">+970 598 191 312</a>
        </div>
      </div>

      <div class="contact-item">
        <div class="contact-icon blue">📧</div>
        <div class="contact-item-body">
          <strong>البريد الإلكتروني</strong>
          <a href="mailto:faredahmad615@gmail.com">faredahmad615@gmail.com</a>
        </div>
      </div>

      <div class="contact-item">
        <div class="contact-icon green">💬</div>
        <div class="contact-item-body">
          <strong>واتساب</strong>
          <a href="https://wa.me/970598191312" target="_blank">+970 598 191 312</a>
        </div>
      </div>

      <div class="contact-item">
        <div class="contact-icon purple">📍</div>
        <div class="contact-item-body">
          <strong>الموقع</strong>
          <span>فلسطين — الخليل</span>
        </div>
      </div>

      <div class="contact-item">
        <div class="contact-icon orange">🕐</div>
        <div class="contact-item-body">
          <strong>أوقات العمل</strong>
          <span>السبت — الخميس: 9ص — 6م</span>
        </div>
      </div>

      <div>
        <p style="font-size:12px;font-weight:800;color:var(--text);margin-bottom:10px;">تابعنا على</p>
        <div class="social-row">
          <a href="https://wa.me/970598191312" target="_blank" class="social-chip whatsapp">💬 واتساب</a>
          <a href="https://www.facebook.com/HUDACENTERHEBRON" target="_blank" class="social-chip facebook">📘 فيسبوك</a>
          <a href="https://www.tiktok.com/@huda.center" target="_blank" class="social-chip tiktok">🎵 تيك توك</a>
        </div>
      </div>
    </div>

    {{-- Right: Form --}}
    <div class="contact-form-card">
      <h2>أرسل لنا رسالة</h2>
      <p class="subtitle">املأ النموذج وسيتم إرسال رسالتك مباشرة عبر واتساب</p>

      <form action="{{ route('contact.send') }}" method="POST">
        @csrf
        <div class="form-row">
          <div class="form-group">
            <label>الاسم الكامل *</label>
            <input type="text" name="name" placeholder="محمد أحمد" required value="{{ old('name') }}"/>
            @error('name')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
          </div>
          <div class="form-group">
            <label>رقم الهاتف *</label>
            <input type="tel" name="phone" placeholder="+970 5XX XXX XXX" required dir="ltr" value="{{ old('phone') }}"/>
            @error('phone')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
          </div>
        </div>

        <div class="form-group">
          <label>الموضوع *</label>
          <input type="text" name="subject" placeholder="استفسار عن منتج / طلب / شكوى..." required value="{{ old('subject') }}"/>
          @error('subject')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label>الرسالة *</label>
          <textarea name="message" rows="5" placeholder="اكتب رسالتك هنا..." required>{{ old('message') }}</textarea>
          @error('message')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
        </div>

        <button type="submit" class="btn-submit">
          💬 إرسال عبر واتساب
        </button>
      </form>
    </div>

  </div>

  {{-- Map --}}
  <div class="map-section">
    <h2>📍 موقعنا على الخريطة</h2>
    <iframe
      class="map-frame"
      src="https://maps.google.com/maps?q=31.553731,35.084007&z=17&output=embed"
      allowfullscreen
      loading="lazy"
      referrerpolicy="no-referrer-when-downgrade">
    </iframe>
  </div>

</div>
@endsection
