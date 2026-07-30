<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function contact()
    {
        return view('pages.contact');
    }

    public function about()
    {
        return view('pages.about');
    }

    public function privacyPolicy()
    {
        return view('pages.privacy-policy');
    }

    public function returns()
    {
        return view('pages.returns');
    }

    public function terms()
    {
        return view('pages.terms');
    }

    public function shipping()
    {
        return view('pages.shipping');
    }

    public function faq()
    {
        return view('pages.faq');
    }

    public function sendContact(Request $request)
    {
        // ── Bot traps ──
        // A filled honeypot, or a form returned in under two seconds, is a
        // script. Pretend it worked so the bot doesn't learn it was caught, but
        // save nothing.
        $tooFast = (int) $request->input('loaded_at', 0) > (time() - 2);
        if (filled($request->input('website')) || $tooFast) {
            return redirect()->route('contact')->with('success', 'تم إرسال رسالتك بنجاح.');
        }

        $data = $request->validate([
            'name'    => 'required|string|max:100',
            // Digits, spaces and + only — a real phone, not a link or a script.
            'phone'   => ['required', 'string', 'max:30', 'regex:/^[0-9+\s()\-]{6,30}$/'],
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:2000',
        ], [
            'phone.regex' => 'يرجى إدخال رقم هاتف صحيح.',
        ]);

        // Only ever the four validated fields — status/admin_reply/read_at in
        // the model's fillable are never taken from the request.
        Inquiry::create($data);

        // Open WhatsApp for a quick reply. urlencode() neutralises any newline
        // or control character in the user's text, so it can't forge extra
        // parameters onto the wa.me URL.
        $text = "رسالة جديدة من الموقع\n"
              . "الاسم: {$data['name']}\n"
              . "الهاتف: {$data['phone']}\n"
              . "الموضوع: {$data['subject']}\n"
              . "الرسالة: {$data['message']}";

        $url = 'https://wa.me/970598191312?text=' . urlencode($text);

        return redirect()->away($url);
    }
}
