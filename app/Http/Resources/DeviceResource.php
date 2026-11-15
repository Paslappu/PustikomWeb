<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * Kolom `qr` sengaja tidak diekspos.
 *
 * @mixin \App\Models\Device
 */
class DeviceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->device_id,
            'is_active' => (bool) $this->isActive,
            'tipe' => $this->whenLoaded('tipe', fn () => $this->tipe ? [
                'id' => $this->tipe->id,
                'name' => $this->tipe->name,
                'slug' => $this->tipe->slug,
            ] : null),
            'category_dana' => $this->whenLoaded('categoryDana', fn () => $this->categoryDana ? [
                'id' => $this->categoryDana->id,
                'name' => $this->categoryDana->name,
            ] : null),
            'sticker_url' => $this->sticker ? Storage::disk('public')->url('uploads/sticker/'.$this->sticker) : null,
            'current_location' => $this->whenLoaded('latestLocation', fn () => $this->latestLocation ? [
                'gedung' => $this->latestLocation->gedung ? new GedungResource($this->latestLocation->gedung) : null,
                'location' => $this->latestLocation->location,
                'since' => $this->latestLocation->created_at?->toIso8601String(),
            ] : null),
        ];
    }
}
