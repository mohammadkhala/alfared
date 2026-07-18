@extends('layouts.app')

@section('title', __('track_page_title'))

@section('content')
<div class="container" style="padding:48px 16px;max-width:520px;margin:0 auto;">

  <div style="text-align:center;margin-bottom:28px;">
    <div style="width:68px;height:68px;border-radius:20px;background:#EFF6FF;display:flex;align-items:center;justify-content:center;font-size:32px;margin:0 auto 16px;">📦</div>
    <h1 style="font-size:1.6rem;font-weight:700;color:#1B3B8C;margin:0 0 8px;">{{ __('track_page_title') }}</h1>
    <p style="color:#6B7280;margin:0;font-size:14px;line-height:1.7;">{{ __('track_page_intro') }}</p>
  </div>

  <div style="background:#fff;border-radius:20px;padding:28px;box-shadow:0 4px 20px rgba(27,59,140,.08);">

    @if($errors->any())
      <div style="background:#FEE2E2;border:1px solid #FECACA;border-radius:12px;padding:14px;margin-bottom:20px;">
        <span style="color:#DC2626;font-weight:600;font-size:14px;">{{ $errors->first() }}</span>
      </div>
    @endif

    <form method="POST" action="{{ route('track.lookup') }}">
      @csrf

      <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">{{ __('track_order_number_label') }}</label>
      <input type="text" name="order_number" value="{{ old('order_number') }}" required
        placeholder="ORD-2026-0001" autocomplete="off"
        style="width:100%;padding:13px 16px;border:1px solid #E5E7EB;border-radius:12px;font-size:15px;margin-bottom:18px;font-family:inherit;"/>

      <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">{{ __('track_phone_label') }}</label>
      <input type="tel" name="phone" value="{{ old('phone') }}" required
        placeholder="0590000000" inputmode="tel" autocomplete="tel"
        style="width:100%;padding:13px 16px;border:1px solid #E5E7EB;border-radius:12px;font-size:15px;margin-bottom:8px;font-family:inherit;"/>
      <p style="font-size:12px;color:#9CA3AF;margin:0 0 22px;">{{ __('track_phone_hint') }}</p>

      <button type="submit" class="btn btn-blue"
        style="width:100%;padding:14px;border-radius:12px;font-weight:700;font-size:15px;border:none;cursor:pointer;">
        {{ __('track_submit') }}
      </button>
    </form>
  </div>

  @guest
    <p style="text-align:center;color:#6B7280;font-size:13px;margin-top:22px;">
      {{ __('track_login_hint') }}
      <a href="{{ route('login') }}" style="color:#1B3B8C;font-weight:600;">{{ __('track_login_link') }}</a>
    </p>
  @endguest

</div>
@endsection
