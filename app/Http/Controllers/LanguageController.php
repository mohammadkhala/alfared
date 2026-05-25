<?php

namespace App\Http\Controllers;

class LanguageController extends Controller
{
    public function switch(string $locale)
    {
        if (in_array($locale, ['ar', 'he', 'en'])) {
            session(['locale' => $locale]);
        }
        return redirect()->back();
    }
}
