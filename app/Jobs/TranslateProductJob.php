<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\TranslationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TranslateProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 60;

    public function __construct(public int $productId) {}

    public function handle(TranslationService $translator): void
    {
        $product = Product::find($this->productId);
        if (! $product) return;

        $updates = [];

        // ── Name ─────────────────────────────────────────────────────────
        $nameAr = trim($product->name_ar ?? '');
        if ($nameAr !== '') {
            $en = $translator->translate($nameAr, 'ar', 'en');
            $he = $translator->translate($nameAr, 'ar', 'he');
            if ($en) $updates['name_en'] = $en;
            if ($he) $updates['name_he'] = $he;
        }

        // ── Full description ──────────────────────────────────────────────
        $descAr = trim(strip_tags($product->description_ar ?? ''));
        if ($descAr !== '') {
            $en = $translator->translate($descAr, 'ar', 'en');
            $he = $translator->translate($descAr, 'ar', 'he');
            if ($en) $updates['description_en'] = '<p>' . htmlspecialchars($en, ENT_QUOTES) . '</p>';
            if ($he) $updates['description_he'] = '<p>' . htmlspecialchars($he, ENT_QUOTES) . '</p>';
        }

        if (empty($updates)) return;

        // Update without triggering model events (to avoid infinite loop)
        Product::withoutEvents(fn() =>
            Product::where('id', $this->productId)->update($updates)
        );

        Log::info("TranslateProductJob done", ['product_id' => $this->productId, 'fields' => array_keys($updates)]);
    }

    public function failed(\Throwable $e): void
    {
        Log::warning("TranslateProductJob failed for product #{$this->productId}: " . $e->getMessage());
    }
}
