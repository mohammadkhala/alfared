@props([
    'name'        => 'phone',
    'value'       => '',
    'required'    => false,
    'label'       => null,
    'placeholder' => '5XX XXX XXX',
])

@php
    // Split incoming value into prefix + local number
    $rawValue = old($name, $value ?? '');
    $detectedPrefix = '+970';
    $localNumber    = $rawValue;
    if (is_string($rawValue) && str_starts_with($rawValue, '+972')) {
        $detectedPrefix = '+972';
        $localNumber    = substr($rawValue, 4);
    } elseif (is_string($rawValue) && str_starts_with($rawValue, '+970')) {
        $detectedPrefix = '+970';
        $localNumber    = substr($rawValue, 4);
    }
    // strip leading 0 if user pasted local format
    $localNumber = ltrim($localNumber ?? '', '0');
    $instanceId = 'phone_' . substr(md5($name . uniqid()), 0, 8);
@endphp

<div class="phone-input-wrap" data-phone-instance="{{ $instanceId }}">
    @if($label)
        <label style="display:block;margin-bottom:6px;font-size:13px;color:#374151;font-weight:600;">
            {{ $label }} @if($required)<span style="color:#EF4444;">*</span>@endif
        </label>
    @endif

    <div style="display:flex;gap:8px;align-items:stretch;">
        <select
            id="{{ $instanceId }}_prefix"
            class="phone-prefix-select"
            style="flex:0 0 auto;min-width:120px;padding:11px 10px;border:1px solid #E5E7EB;border-radius:10px;background:#fff;font-family:inherit;font-size:14px;color:#1B3B8C;font-weight:700;cursor:pointer;direction:ltr;text-align:center;"
            onchange="phoneCombine_{{ $instanceId }}()"
        >
            <option value="+970" {{ $detectedPrefix === '+970' ? 'selected' : '' }}>🇵🇸 +970</option>
            <option value="+972" {{ $detectedPrefix === '+972' ? 'selected' : '' }}>🇮🇱 +972</option>
        </select>

        <input
            type="tel"
            id="{{ $instanceId }}_local"
            inputmode="numeric"
            pattern="[0-9]{8,10}"
            placeholder="{{ $placeholder }}"
            value="{{ $localNumber }}"
            {{ $required ? 'required' : '' }}
            style="flex:1;padding:11px 14px;border:1px solid #E5E7EB;border-radius:10px;font-family:inherit;font-size:14px;direction:ltr;text-align:start;"
            oninput="phoneCombine_{{ $instanceId }}()"
            onblur="phoneCombine_{{ $instanceId }}()"
        />
    </div>

    {{-- The actual value submitted to the server --}}
    <input type="hidden" name="{{ $name }}" id="{{ $instanceId }}_value" value="{{ $rawValue }}"/>

    <div id="{{ $instanceId }}_error" style="display:none;color:#EF4444;font-size:12px;margin-top:6px;">
        رقم الهاتف غير صحيح. يجب أن يبدأ بـ +970 أو +972 ويحتوي على 8 إلى 10 أرقام.
    </div>
</div>

<script>
function phoneCombine_{{ $instanceId }}() {
    const prefix = document.getElementById('{{ $instanceId }}_prefix').value;
    const localEl = document.getElementById('{{ $instanceId }}_local');
    const errorEl = document.getElementById('{{ $instanceId }}_error');
    // strip non-digits + leading zeros
    let digits = (localEl.value || '').replace(/\D/g, '').replace(/^0+/, '');
    localEl.value = digits;

    const valid = digits.length >= 8 && digits.length <= 10;
    if (errorEl) errorEl.style.display = (digits.length > 0 && !valid) ? '' : 'none';

    document.getElementById('{{ $instanceId }}_value').value = digits ? (prefix + digits) : '';
}
document.addEventListener('DOMContentLoaded', phoneCombine_{{ $instanceId }});
</script>
