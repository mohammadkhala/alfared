<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }


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

    /** Splits the stored `video` back into the right input for editing. */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $video = $data['video'] ?? null;
        $isLink = $video && str_starts_with($video, 'http');

        $data['video_source'] = match (true) {
            blank($video) => 'none',
            $isLink       => 'link',
            default       => 'upload',
        };
        $data['video_link'] = $isLink ? $video : null;
        $data['video_file'] = (! $isLink && $video) ? [$video] : [];

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->applyVideo($data);
    }
}
