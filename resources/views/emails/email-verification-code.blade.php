<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>تأكيد البريد الإلكتروني</title>
</head>
<body style="margin:0;padding:0;background:#F5F6FA;font-family:Tahoma,Arial,sans-serif;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F5F6FA;padding:32px 16px;">
    <tr>
      <td align="center">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
               style="max-width:520px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(27,59,140,.08);">

          <tr>
            <td style="background:#1B3B8C;padding:28px 24px;text-align:center;">
              <h1 style="margin:0;color:#ffffff;font-size:20px;font-weight:bold;">شركة أبناء الفريد</h1>
              <p style="margin:6px 0 0;color:#C7D3F0;font-size:13px;">تأكيد البريد الإلكتروني</p>
            </td>
          </tr>

          <tr>
            <td style="padding:32px 24px;">
              <p style="margin:0 0 8px;color:#1A1A2E;font-size:15px;">
                @if($name) أهلاً {{ $name }}، @else أهلاً بك، @endif
              </p>
              <p style="margin:0 0 24px;color:#4B5563;font-size:14px;line-height:1.8;">
                شكراً لإنشاء حسابك في متجرنا. استخدم الرمز التالي لتأكيد بريدك وإتمام التسجيل:
              </p>

              <div style="background:#F0F4FF;border:1px dashed #1B3B8C;border-radius:12px;padding:20px;text-align:center;margin-bottom:24px;">
                <div style="font-size:32px;font-weight:bold;color:#1B3B8C;letter-spacing:10px;direction:ltr;">
                  {{ $code }}
                </div>
              </div>

              <p style="margin:0 0 8px;color:#4B5563;font-size:13px;line-height:1.8;">
                ⏱ الرمز صالح لمدة <strong>10 دقائق</strong>.
              </p>
              <p style="margin:0;color:#6B7280;font-size:13px;line-height:1.8;">
                إذا لم تُنشئ هذا الحساب، تجاهل الرسالة ولن يُنشأ أي حساب ببريدك.
              </p>
            </td>
          </tr>

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
