<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>رمز استعادة كلمة المرور</title>
</head>
<body style="margin:0;padding:0;background:#F5F6FA;font-family:Tahoma,Arial,sans-serif;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F5F6FA;padding:32px 16px;">
    <tr>
      <td align="center">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
               style="max-width:520px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(27,59,140,.08);">

          {{-- Header --}}
          <tr>
            <td style="background:#1B3B8C;padding:28px 24px;text-align:center;">
              <h1 style="margin:0;color:#ffffff;font-size:20px;font-weight:bold;">شركة أبناء الفريد</h1>
              <p style="margin:6px 0 0;color:#C7D3F0;font-size:13px;">استعادة كلمة المرور</p>
            </td>
          </tr>

          {{-- Body --}}
          <tr>
            <td style="padding:32px 24px;">
              <p style="margin:0 0 8px;color:#1A1A2E;font-size:15px;">
                @if($name) مرحباً {{ $name }}، @else مرحباً، @endif
              </p>
              <p style="margin:0 0 24px;color:#4B5563;font-size:14px;line-height:1.8;">
                تلقّينا طلباً لاستعادة كلمة مرور حسابك. استخدم الرمز التالي لإتمام العملية:
              </p>

              {{-- Code --}}
              <div style="background:#F0F4FF;border:1px dashed #1B3B8C;border-radius:12px;padding:20px;text-align:center;margin-bottom:24px;">
                <div style="font-size:32px;font-weight:bold;color:#1B3B8C;letter-spacing:10px;direction:ltr;">
                  {{ $code }}
                </div>
              </div>

              <p style="margin:0 0 8px;color:#4B5563;font-size:13px;line-height:1.8;">
                ⏱ الرمز صالح لمدة <strong>5 دقائق</strong> فقط.
              </p>
              <p style="margin:0;color:#6B7280;font-size:13px;line-height:1.8;">
                إذا لم تطلب استعادة كلمة المرور، تجاهل هذه الرسالة — لن يتغيّر شيء في حسابك.
              </p>

              <div style="margin-top:24px;padding:14px 16px;background:#FFF7ED;border-radius:10px;">
                <p style="margin:0;color:#9A3412;font-size:12px;line-height:1.7;">
                  🔒 لا تشارك هذا الرمز مع أي شخص. فريقنا لن يطلبه منك أبداً.
                </p>
              </div>
            </td>
          </tr>

          {{-- Footer --}}
          <tr>
            <td style="background:#F9FAFB;padding:20px 24px;text-align:center;border-top:1px solid #E5E7EB;">
              <p style="margin:0;color:#9CA3AF;font-size:12px;">
                © {{ date('Y') }} شركة أبناء الفريد التجارية
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
