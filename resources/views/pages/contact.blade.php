@extends('layouts.app')
@section('title', __('contact_meta_title') . ' — ' . __('company_name'))
@section('description', __('contact_meta_desc'))

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
  <h1>{{ __('contact_hero_title') }}</h1>
  <p>{{ __('contact_hero_sub') }}</p>
</div>

<div class="container">

  {{-- Contact grid --}}
  <div class="contact-grid">

    {{-- Left: Info --}}
    <div class="contact-info-card">
      <div>
        <h2>{{ __('contact_info_title') }}</h2>
        <p class="subtitle">{{ __('contact_info_sub') }}</p>
      </div>

      <div class="contact-item">
        <div class="contact-icon orange">📞</div>
        <div class="contact-item-body">
          <strong>{{ __('contact_phone') }}</strong>
          <a href="tel:+970598191312" dir="ltr">+970 598 191 312</a>
        </div>
      </div>

      <div class="contact-item">
        <div class="contact-icon blue">📧</div>
        <div class="contact-item-body">
          <strong>{{ __('contact_email') }}</strong>
          <a href="mailto:faredahmad615@gmail.com">faredahmad615@gmail.com</a>
        </div>
      </div>

      <div class="contact-item">
        <div class="contact-icon green"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="#fff"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg></div>
        <div class="contact-item-body">
          <strong>{{ __('contact_whatsapp') }}</strong>
          <a href="https://wa.me/970598191312" target="_blank">+970 598 191 312</a>
        </div>
      </div>

      <div class="contact-item">
        <div class="contact-icon purple">📍</div>
        <div class="contact-item-body">
          <strong>{{ __('contact_location') }}</strong>
          <span>{{ __('contact_location_val') }}</span>
        </div>
      </div>

      <div class="contact-item">
        <div class="contact-icon orange">🕐</div>
        <div class="contact-item-body">
          <strong>{{ __('contact_hours') }}</strong>
          <span>{{ __('contact_hours_val') }}</span>
        </div>
      </div>

      <div>
        <p style="font-size:12px;font-weight:800;color:var(--text);margin-bottom:10px;">{{ __('contact_follow') }}</p>
        <div class="social-row">
          <a href="https://wa.me/970598191312" target="_blank" class="social-chip whatsapp"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="currentColor" style="vertical-align:-2px;margin-inline-end:6px;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>{{ __('contact_whatsapp') }}</a>
          <a href="https://www.facebook.com/HUDACENTERHEBRON" target="_blank" class="social-chip facebook">{{ __('contact_facebook') }}</a>
          <a href="https://www.tiktok.com/@huda.center" target="_blank" class="social-chip tiktok">{{ __('contact_tiktok') }}</a>
        </div>
      </div>
    </div>

    {{-- Right: Form --}}
    <div class="contact-form-card">
      <h2>{{ __('contact_form_title') }}</h2>
      <p class="subtitle">{{ __('contact_form_sub') }}</p>

      <form action="{{ route('contact.send') }}" method="POST">
        @csrf
        {{-- Honeypot: hidden from people, tempting to bots. A real user never
             fills it, so any value here means the submission is automated. --}}
        <input type="text" name="website" tabindex="-1" autocomplete="off"
               style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;" aria-hidden="true"/>
        {{-- When the page was rendered — a form submitted in under 2s was filled
             by a script, not a human. --}}
        <input type="hidden" name="loaded_at" value="{{ now()->timestamp }}"/>
        <div class="form-row">
          <div class="form-group">
            <label>{{ __('contact_f_name') }}</label>
            <input type="text" name="name" placeholder="{{ __('contact_f_name_ph') }}" required value="{{ old('name') }}"/>
            @error('name')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
          </div>
          <div class="form-group">
            <label>{{ __('contact_f_phone') }}</label>
            <input type="tel" name="phone" placeholder="+970 5XX XXX XXX" required dir="ltr" value="{{ old('phone') }}"/>
            @error('phone')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
          </div>
        </div>

        <div class="form-group">
          <label>{{ __('contact_f_subject') }}</label>
          <input type="text" name="subject" placeholder="{{ __('contact_f_subject_ph') }}" required value="{{ old('subject') }}"/>
          @error('subject')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label>{{ __('contact_f_message') }}</label>
          <textarea name="message" rows="5" placeholder="{{ __('contact_f_message_ph') }}" required>{{ old('message') }}</textarea>
          @error('message')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
        </div>

        <button type="submit" class="btn-submit">
          {{ __('contact_f_send') }}
        </button>
      </form>
    </div>

  </div>

  {{-- Map --}}
  <div class="map-section">
    <h2>{{ __('contact_map_title') }}</h2>
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
