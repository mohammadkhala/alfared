@extends('layouts.app')

@section('title', __('checkout_page_title'))

@section('content')
<div class="page-header" style="background:linear-gradient(135deg,#1B3B8C 0%,#152d6e 100%);padding:32px 0;text-align:center;color:#fff;">
  <div class="container">
    <h1 style="font-size:1.8rem;font-weight:700;margin:0 0 6px;">{{ __('checkout_heading') }}</h1>
    <p style="opacity:.8;margin:0;">{{ __('checkout_subheading') }}</p>
  </div>
</div>

<div class="container" style="padding:40px 0;">

  @if(session('error'))
  <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:12px;padding:14px 20px;margin-bottom:20px;color:#B91C1C;font-weight:600;display:flex;align-items:center;gap:10px;">
    ⚠️ {{ session('error') }}
  </div>
  @endif

  <form action="{{ route('checkout.place') }}" method="POST" id="checkoutForm" novalidate>
    @csrf
    <div class="checkout-layout">

      {{-- Left: Customer info --}}
      <div class="checkout-main">

        {{-- Step 1: Personal info --}}
        <div class="checkout-section" style="background:#fff;border-radius:16px;padding:28px;margin-bottom:20px;box-shadow:0 4px 20px rgba(27,59,140,.08);">
          <h3 style="font-size:1.1rem;font-weight:700;color:#1B3B8C;margin:0 0 20px;display:flex;align-items:center;gap:10px;">
            <span style="width:32px;height:32px;background:#1B3B8C;color:#fff;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:14px;">1</span>
            {{ __('checkout_step_personal') }}
          </h3>
          <div class="form-grid">
            <div class="form-group">
              <label>{{ __('checkout_first_name') }} <span style="color:red;">{{ __('checkout_required') }}</span></label>
              <input type="text" name="first_name" value="{{ old('first_name', auth()->user()->name ?? '') }}" required placeholder="{{ __('checkout_first_name_ph') }}"/>
              @error('first_name')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
              <label>{{ __('checkout_last_name') }} <span style="color:red;">{{ __('checkout_required') }}</span></label>
              <input type="text" name="last_name" value="{{ old('last_name') }}" required placeholder="{{ __('checkout_last_name_ph') }}"/>
              @error('last_name')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
              <x-phone-input
                name="phone"
                :value="old('phone', auth()->user()->phone ?? '')"
                :label="__('phone')"
                :required="true"
              />
              @error('phone')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
              <label>{{ __('checkout_email_optional') }}</label>
              <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" placeholder="{{ __('checkout_email_ph') }}"/>
            </div>
          </div>
        </div>

        {{-- Step 2: Address --}}
        <div class="checkout-section" style="background:#fff;border-radius:16px;padding:28px;margin-bottom:20px;box-shadow:0 4px 20px rgba(27,59,140,.08);">
          <h3 style="font-size:1.1rem;font-weight:700;color:#1B3B8C;margin:0 0 20px;display:flex;align-items:center;gap:10px;">
            <span style="width:32px;height:32px;background:#1B3B8C;color:#fff;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:14px;">2</span>
            {{ __('checkout_step_address') }}
          </h3>
          <div class="form-grid">
            <div class="form-group">
              <label>{{ __('checkout_delivery_zone') }} <span style="color:red;">{{ __('checkout_required') }}</span></label>
              {{-- Hidden input carries the actual value on submit --}}
              <input type="hidden" name="delivery_zone_id" id="deliveryZoneHidden" value="{{ old('delivery_zone_id', '') }}">
              <select id="deliveryZone" onchange="syncZone(this.value); updateDeliveryFee();">
                <option value="">{{ __('checkout_choose_zone') }}</option>
                @foreach($zones as $zone)
                  <option value="{{ $zone->id }}" {{ old('delivery_zone_id') == $zone->id ? 'selected' : '' }}>
                    {{ $zone->name_ar }} — {{ $zone->base_fee > 0 ? number_format($zone->base_fee, 2).' ₪' : __('free') }}
                  </option>
                @endforeach
              </select>
              @error('delivery_zone_id')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
              <label>{{ __('city') }} <span style="color:red;">{{ __('checkout_required') }}</span></label>
              <input type="text" name="city" value="{{ old('city', __('checkout_city_ph')) }}" required placeholder="{{ __('checkout_city_ph') }}"/>
              @error('city')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group" style="grid-column:1/-1;">
              <label>{{ __('checkout_address_line') }} <span style="color:red;">{{ __('checkout_required') }}</span></label>
              <input type="text" name="address_line" value="{{ old('address_line') }}" required placeholder="{{ __('checkout_address_line_ph') }}"/>
              @error('address_line')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
              <label>{{ __('checkout_area_neighborhood') }}</label>
              <input type="text" name="area" value="{{ old('area') }}" placeholder="{{ __('checkout_area_ph') }}"/>
            </div>
            <div class="form-group">
              <label>{{ __('checkout_building') }}</label>
              <input type="text" name="building" value="{{ old('building') }}" placeholder="{{ __('checkout_building_ph') }}"/>
            </div>
            <div class="form-group" style="grid-column:1/-1;">
              <label>{{ __('checkout_delivery_notes') }}</label>
              <textarea name="notes" rows="3" placeholder="{{ __('checkout_notes_ph') }}">{{ old('notes') }}</textarea>
            </div>
          </div>
        </div>

        {{-- Loyalty points redemption --}}
        @auth
          @if(($loyaltyCfg['enabled'] ?? false) && $loyaltyMax > 0)
            <div class="checkout-section" style="background:#fff;border-radius:16px;padding:28px;margin-bottom:20px;box-shadow:0 4px 20px rgba(27,59,140,.08);border:2px dashed #E8711A;">
              <h3 style="font-size:1.1rem;font-weight:700;color:#1B3B8C;margin:0 0 12px;display:flex;align-items:center;gap:10px;">
                <span style="font-size:24px;">⭐</span>
                {{ __('loyalty_use_at_checkout') }}
              </h3>
              <div style="font-size:13px;color:#475569;margin-bottom:14px;">
                {{ __('loyalty_available', ['n' => $loyaltyBalance]) }}
                — {{ __('loyalty_max_redeemable') }}: <strong style="color:#E8711A;">{{ $loyaltyMax }}</strong>
              </div>
              <div class="form-group">
                <label>{{ __('loyalty_redeem_label') }}</label>
                <div style="display:flex;gap:10px;align-items:center;">
                  <input
                    type="number"
                    name="loyalty_points"
                    id="loyaltyInput"
                    min="0"
                    max="{{ $loyaltyMax }}"
                    step="{{ $loyaltyCfg['points_per_redeem_ils'] }}"
                    value="0"
                    style="flex:1;"
                    oninput="updateLoyaltyDiscount()"
                  />
                  <button type="button" onclick="document.getElementById('loyaltyInput').value={{ $loyaltyMax }};updateLoyaltyDiscount();"
                    style="padding:11px 16px;background:#1B3B8C;color:#fff;border:none;border-radius:10px;cursor:pointer;font-weight:700;white-space:nowrap;">
                    {{ __('clear') }}
                  </button>
                </div>
                <div style="font-size:12px;color:#10B981;margin-top:8px;font-weight:600;">
                  {{ __('loyalty_redeem_value') }}: <span id="loyaltyDiscountDisplay">0.00</span> ₪
                </div>
              </div>
            </div>
          @endif
        @endauth

      </div>

      {{-- Right: Order summary --}}
      <div class="checkout-sidebar">
        <div style="background:#fff;border-radius:16px;padding:28px;box-shadow:0 4px 20px rgba(27,59,140,.08);position:sticky;top:100px;">
          <h3 style="font-size:1.1rem;font-weight:700;color:#1B3B8C;margin:0 0 20px;">{{ __('checkout_order_summary') }}</h3>

          {{-- Items --}}
          <div style="max-height:280px;overflow-y:auto;margin-bottom:20px;">
            @foreach($cart as $item)
              <div style="display:flex;gap:12px;align-items:center;padding:10px 0;border-bottom:1px solid #F3F4F6;">
                <img src="{{ $item['image'] ? asset('storage/'.$item['image']) : asset('images/placeholder.svg') }}"
                  style="width:50px;height:50px;object-fit:cover;border-radius:8px;flex-shrink:0;"/>
                <div style="flex:1;min-width:0;">
                  <div style="font-size:13px;font-weight:600;color:#374151;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $item['name'] }}</div>
                  @if($item['variant'])<div style="font-size:12px;color:#9CA3AF;">{{ $item['variant'] }}</div>@endif
                  <div style="font-size:13px;color:#6B7280;">× {{ $item['qty'] }}</div>
                </div>
                <div style="font-size:14px;font-weight:700;color:#E8711A;white-space:nowrap;">{{ number_format($item['price'] * $item['qty'], 2) }} ₪</div>
              </div>
            @endforeach
          </div>

          {{-- Totals --}}
          <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:20px;font-size:14px;">
            <div style="display:flex;justify-content:space-between;">
              <span style="color:#6B7280;">{{ __('subtotal') }}</span>
              <span style="font-weight:600;">{{ number_format($subtotal, 2) }} ₪</span>
            </div>
            @if($discount > 0)
              <div style="display:flex;justify-content:space-between;">
                <span style="color:#6B7280;">{{ __('discount') }}</span>
                <span style="color:#10B981;font-weight:600;">— {{ number_format($discount, 2) }} ₪</span>
              </div>
            @endif
            <div id="loyaltyRowDisplay" style="display:none;justify-content:space-between;">
              <span style="color:#6B7280;">⭐ {{ __('loyalty_title') }}</span>
              <span id="loyaltyDiscountAmount" style="color:#10B981;font-weight:600;">— 0.00 ₪</span>
            </div>
            <div style="display:flex;justify-content:space-between;">
              <span style="color:#6B7280;">{{ __('delivery_fee') }}</span>
              <span id="deliveryFeeDisplay" style="font-weight:600;color:#10B981;">{{ __('checkout_choose_zone_short') }}</span>
            </div>
            <div style="border-top:2px solid #E5E7EB;padding-top:12px;display:flex;justify-content:space-between;font-size:1.1rem;font-weight:700;">
              <span>{{ __('total') }}</span>
              <span id="totalDisplay" style="color:#E8711A;">{{ number_format($subtotal - $discount, 2) }} ₪</span>
            </div>
          </div>

          {{-- Payment method --}}
          <div style="margin-bottom:20px;">
            <div style="font-size:13px;font-weight:700;color:#1B3B8C;margin-bottom:10px;display:flex;align-items:center;gap:7px;">
              <span style="width:24px;height:24px;background:#1B3B8C;color:#fff;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:12px;">3</span>
              {{ __('checkout_step_payment') }}
            </div>

            {{-- COD --}}
            @if($codEnabled)
            <label for="cod" style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:10px;border:2px solid #E5E7EB;cursor:pointer;margin-bottom:8px;transition:border-color .2s,background .2s;" id="lbl-cod">
              <input type="radio" name="payment_method" value="cod" id="cod"
                {{ $codEnabled ? 'checked' : '' }}
                onchange="switchPayment(this.value)" style="width:16px;height:16px;accent-color:#1B3B8C;flex-shrink:0;"/>
              <div style="font-size:22px;flex-shrink:0;">💵</div>
              <div>
                <div style="font-size:13px;font-weight:700;color:#1B3B8C;">{{ __('checkout_cod_label') }}</div>
                <div style="font-size:11px;color:#6B7280;margin-top:1px;">{{ __('checkout_cod_desc') }}</div>
              </div>
            </label>
            @endif

            {{-- Online Card (Lahza) — controlled from admin settings --}}
            @if($onlinePaymentEnabled)
            <label for="lahza" style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:10px;border:2px solid #E5E7EB;cursor:pointer;margin-bottom:8px;transition:border-color .2s,background .2s;" id="lbl-lahza">
              <input type="radio" name="payment_method" value="lahza" id="lahza"
                {{ (!$codEnabled && $onlinePaymentEnabled) ? 'checked' : '' }}
                onchange="switchPayment(this.value)" style="width:16px;height:16px;accent-color:#1B3B8C;flex-shrink:0;"/>
              <div style="font-size:22px;flex-shrink:0;">💳</div>
              <div>
                <div style="font-size:13px;font-weight:700;color:#1B3B8C;">{{ __('checkout_card_label') }}</div>
                <div style="font-size:11px;color:#6B7280;margin-top:1px;">{{ __('checkout_card_desc') }}</div>
              </div>
            </label>
            @endif
          </div>

          <script>
            document.addEventListener('DOMContentLoaded', function() {
              // Highlight the checked payment option on load
              const checked = document.querySelector('input[name="payment_method"]:checked');
              if (checked) {
                const lbl = document.getElementById('lbl-' + checked.value);
                if (lbl) { lbl.style.borderColor = '#1B3B8C'; lbl.style.background = '#F0F4FF'; }
              }
            });
          </script>

          <button type="submit" class="btn btn-orange"
            style="width:100%;padding:16px;font-size:16px;font-weight:700;background:linear-gradient(135deg,#E8711A,#d4610e);border-radius:12px;box-shadow:0 4px 15px rgba(232,113,26,.3);">
            {{ __('checkout_confirm') }}
          </button>
          <p style="text-align:center;font-size:12px;color:#9CA3AF;margin:12px 0 0;">{{ __('checkout_safe_data') }}</p>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
const subtotal = {{ $subtotal }};
const discount = {{ $discount }};
const POINTS_PER_ILS = {{ $loyaltyCfg['points_per_redeem_ils'] ?? 20 }};
const FREE_LABEL  = @json(__('free'));
const ZONE_LABEL  = @json(__('checkout_choose_zone_short'));

let deliveryFee   = 0;
let loyaltyDiscount = 0;

// Sync select UI value → hidden input (called on change + on submit)
function syncZone(val) {
  document.getElementById('deliveryZoneHidden').value = val || '';
}

// On page load: sync hidden input if old() value pre-selected the select
document.addEventListener('DOMContentLoaded', function () {
  const sel = document.getElementById('deliveryZone');
  if (sel && sel.value) syncZone(sel.value);

  // Submit guard: validate zone selected
  document.getElementById('checkoutForm').addEventListener('submit', function (e) {
    syncZone(document.getElementById('deliveryZone').value);
    if (!document.getElementById('deliveryZoneHidden').value) {
      e.preventDefault();
      alert(@json(__('checkout_choose_zone')));
      document.getElementById('deliveryZone').focus();
    }
  });
});

function recalcTotal() {
  const total = Math.max(0, subtotal - discount - loyaltyDiscount + deliveryFee);
  document.getElementById('totalDisplay').textContent = total.toFixed(2) + ' ₪';
}

async function updateDeliveryFee() {
  const zoneId = document.getElementById('deliveryZone').value;
  if (!zoneId) return;

  const res = await fetch('{{ route("checkout.delivery-fee") }}?zone_id='+zoneId+'&subtotal='+(subtotal - discount - loyaltyDiscount));
  const data = await res.json();

  deliveryFee = parseFloat(data.fee) || 0;
  document.getElementById('deliveryFeeDisplay').textContent = deliveryFee > 0 ? data.formatted : FREE_LABEL + ' ✓';
  document.getElementById('deliveryFeeDisplay').style.color = deliveryFee > 0 ? '#374151' : '#10B981';

  recalcTotal();
}

function updateLoyaltyDiscount() {
  const input = document.getElementById('loyaltyInput');
  if (!input) return;
  let points = parseInt(input.value || 0, 10);
  if (isNaN(points) || points < 0) points = 0;
  loyaltyDiscount = +(points / POINTS_PER_ILS).toFixed(2);

  const display = document.getElementById('loyaltyDiscountDisplay');
  if (display) display.textContent = loyaltyDiscount.toFixed(2);

  const loyaltyRow = document.getElementById('loyaltyRowDisplay');
  if (loyaltyRow) {
    loyaltyRow.style.display = loyaltyDiscount > 0 ? '' : 'none';
    const amountEl = document.getElementById('loyaltyDiscountAmount');
    if (amountEl) amountEl.textContent = '— ' + loyaltyDiscount.toFixed(2) + ' ₪';
  }
  recalcTotal();
}
</script>
@endpush
