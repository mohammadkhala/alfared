<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Compresses uploaded images and converts them to WebP to keep product/banner
 * images light (faster loading in the app & site, less bandwidth).
 *
 * Infographic product images contain fine Arabic text, so we cap the longest
 * side at a generous size and use a fairly high WebP quality to stay readable.
 */
class ImageOptimizer
{
    /** Max longest-side in pixels (never upscales). */
    public const MAX_SIDE = 1400;

    /** WebP quality (0-100). High enough to keep infographic text crisp. */
    public const QUALITY = 82;

    /**
     * Filament FileUpload `saveUploadedFileUsing` callback.
     * Optimizes the temp upload and stores it as .webp on the public disk.
     * Returns the stored path (relative to the disk root).
     */
    public static function store(TemporaryUploadedFile $file, string $directory): string
    {
        $manager = new ImageManager(new Driver());
        $image   = $manager->read($file->getRealPath());

        // Shrink only if larger than MAX_SIDE (keeps aspect ratio, no upscale).
        $image->scaleDown(width: self::MAX_SIDE, height: self::MAX_SIDE);

        $encoded  = $image->toWebp(self::QUALITY);
        $filename = trim($directory, '/').'/'.Str::uuid()->toString().'.webp';

        Storage::disk('public')->put($filename, (string) $encoded);

        return $filename;
    }

    /**
     * Optimize an already-stored image in place (used by the batch command).
     * Re-encodes to WebP alongside the original and returns the new path,
     * or null if it could not be processed. The caller updates the DB path.
     */
    public static function optimizeStored(string $path): ?string
    {
        $disk = Storage::disk('public');

        if (! $disk->exists($path)) {
            return null;
        }

        // Already a webp? still worth recompressing if oversized, but skip to be safe.
        try {
            $manager = new ImageManager(new Driver());
            $image   = $manager->read($disk->path($path));
            $image->scaleDown(width: self::MAX_SIDE, height: self::MAX_SIDE);
            $encoded = $image->toWebp(self::QUALITY);

            $newPath = preg_replace('/\.[^.]+$/', '.webp', $path);
            // Avoid clobbering a different file that happens to share the name.
            if ($newPath !== $path && $disk->exists($newPath)) {
                $newPath = dirname($path).'/'.Str::uuid()->toString().'.webp';
            }

            $disk->put($newPath, (string) $encoded);

            // Remove the old file if the extension changed.
            if ($newPath !== $path) {
                $disk->delete($path);
            }

            return $newPath;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
