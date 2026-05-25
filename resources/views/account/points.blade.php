@extends('layouts.app')

@section('title', __('loyalty_title') . ' — ' . __('company_name'))

@php
  $cfg = \App\Services\LoyaltyService::config();
  $ilsValue = \App\Services\LoyaltyService::pointsToIls((int) $points);
@endphp

@section('content')
<div class="page-header" style="background:linear-gradient(135deg,#1B3B8C 0%,#152d6e 100%);padding:32px 0;color:#fff;">
  <div class="container">
    <h1 style="font-size:1.6rem;font-weight:700;margin:0;">⭐ {{ __('loyalty_title') }}</h1>
  </div>
</div>

<div class="container" style="padding:40px 0;">
  <div class="account-layout">
    @include('account.partials.sidebar')
    <main class="account-main">

      {{-- Points balance card --}}
      <div style="background:linear-gradient(135deg,#1B3B8C,#2d4fb8);border-radius:20px;padding:36px;color:#fff;margin-bottom:24px;text-align:center;">
        <div style="font-size:14px;opacity:.85;margin-bottom:4px;">{{ __('loyalty_balance') }}</div>
        <div style="font-size:56px;font-weight:900;margin-bottom:8px;">{{ number_format($points) }}</div>
        <div style="font-size:18px;opacity:.9;margin-bottom:16px;">{{ __('loyalty_points_word') }}</div>
        <div style="background:rgba(255,255,255,.15);border-radius:12px;padding:12px;font-size:14px;">
          {{ __('loyalty_value') }} <strong style="color:#FCD34D;">{{ number_format($ilsValue, 2) }} ₪</strong>
        </div>
      </div>

      {{-- How it works --}}
      <div style="background:#fff;border-radius:16px;padding:28px;box-shadow:0 4px 20px rgba(27,59,140,.08);margin-bottom:24px;">
        <h3 style="color:#1B3B8C;font-size:1.1rem;font-weight:700;margin:0 0 20px;">{{ __('loyalty_how_earn') }}</h3>
        <p style="color:#475569;font-size:14px;line-height:1.7;margin-bottom:16px;">
          {{ __('loyalty_how_earn_desc', ['n' => 1, 'amount' => $cfg['earn_amount_per_point']]) }}
        </p>
        <h3 style="color:#1B3B8C;font-size:1.1rem;font-weight:700;margin:20px 0 12px;">{{ __('loyalty_how_redeem') }}</h3>
        <p style="color:#475569;font-size:14px;line-height:1.7;">
          {{ __('loyalty_how_redeem_desc', ['n' => $cfg['points_per_redeem_ils'], 'min' => $cfg['min_redeem_points']]) }}
        </p>
      </div>

      {{-- Points history --}}
      <div style="background:#fff;border-radius:16px;padding:28px;box-shadow:0 4px 20px rgba(27,59,140,.08);">
        <h3 style="color:#1B3B8C;font-size:1.1rem;font-weight:700;margin:0 0 20px;">{{ __('loyalty_transactions') }}</h3>
        @if($history->count())
          <table style="width:100%;border-collapse:collapse;font-size:14px;">
            <thead>
              <tr style="border-bottom:2px solid #E5E7EB;">
                <th style="padding:12px;text-align:start;color:#6B7280;font-weight:600;">{{ __('tracking_order_date') }}</th>
                <th style="padding:12px;text-align:start;color:#6B7280;font-weight:600;">{{ __('checkout_step_payment') }}</th>
                <th style="padding:12px;text-align:start;color:#6B7280;font-weight:600;">{{ __('loyalty_points_word') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($history as $tx)
                <tr style="border-bottom:1px solid #F3F4F6;">
                  <td style="padding:12px;color:#6B7280;">{{ $tx->created_at->format('d/m/Y') }}</td>
                  <td style="padding:12px;">
                    {{ __(\App\Models\LoyaltyTransaction::$actionLocaleKeys[$tx->action] ?? 'loyalty_action_earned') }}
                    @if($tx->note)<small style="display:block;color:#9CA3AF;font-size:11px;margin-top:2px;">{{ $tx->note }}</small>@endif
                  </td>
                  <td style="padding:12px;font-weight:700;color:{{ $tx->points > 0 ? '#10B981' : '#EF4444' }};">
                    {{ $tx->points > 0 ? '+' : '' }}{{ $tx->points }}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @else
          <div style="text-align:center;padding:40px;color:#9CA3AF;">
            <div style="font-size:48px;margin-bottom:12px;">⭐</div>
            <p>{{ __('loyalty_no_transactions') }}</p>
          </div>
        @endif
      </div>

    </main>
  </div>
</div>
@endsection
