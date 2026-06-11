<?php

namespace App\Observers;

use App\Models\Banner;
use Illuminate\Support\Facades\Cache;

class BannerObserver
{
    public function saved(Banner $banner): void
    {
        $this->clearBannerCache();
    }

    public function deleted(Banner $banner): void
    {
        $this->clearBannerCache();
    }

    private function clearBannerCache(): void
    {
        // Web cache (per locale)
        foreach (['ar', 'en', 'he'] as $loc) {
            Cache::forget("home:{$loc}:hero");
            Cache::forget("home:{$loc}:offer_banners");
        }

        // API cache
        Cache::forget('api:home');
        Cache::forget('api:offers:banners');
        foreach (['hero', 'offers', 'popup'] as $pos) {
            Cache::forget("api:banners:{$pos}");
        }
    }
}
