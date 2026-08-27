<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;


    /**
     * The two video inputs are separate fields (a shared name made FileUpload
     * choke on the URL string), so fold the chosen one back into `video` and
     * let "بدون فيديو" clear it.
     */
    private function applyVideo(array $data): array
    {
        $source = $this->data['video_source'] ?? null;

        $data['video'] = match ($source) {
            'link'   => $this->data['video_link'] ?: null,
            'upload' => is_array($f = ($this->data['video_file'] ?? null))
                            ? (reset($f) ?: null)
                            : ($f ?: null),
            'none'   => null,
            default  => $data['video'] ?? null,   // untouched when the tab wasn't used
        };

        return $data;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->applyVideo($data);
    }
}
