<?php

namespace App\Http\Controllers;

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
        $data = $request->validate([
            'name'    => 'required|string|max:100',
            'phone'   => 'required|string|max:30',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:2000',
        ]);

        // Build WhatsApp message and redirect
        $text = "رسالة جديدة من الموقع\n"
              . "الاسم: {$data['name']}\n"
              . "الهاتف: {$data['phone']}\n"
              . "الموضوع: {$data['subject']}\n"
              . "الرسالة: {$data['message']}";

        $url = 'https://wa.me/970598191312?text=' . urlencode($text);

        return redirect()->away($url);
    }
}
