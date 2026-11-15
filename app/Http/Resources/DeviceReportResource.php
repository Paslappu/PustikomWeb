<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\DeviceReport */
class DeviceReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'device_id' => $this->device_id,
            'gedung_id' => $this->gedung_id,
            'captured_at' => $this->captured_at?->toIso8601String(),
            'latitude' => $this->latitude !== null ? (float) $this->latitude : null,
            'longitude' => $this->longitude !== null ? (float) $this->longitude : null,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'device' => $this->whenLoaded('device', fn () => [
                'id' => $this->device->id,
                'code' => $this->device->device_id,
            ]),
            'gedung' => $this->whenLoaded('gedung', fn () => $this->gedung ? [
                'id' => $this->gedung->id,
                'name' => $this->gedung->name,
                'label' => $this->gedung->display_label,
            ] : null),
            'user' => $this->whenLoaded('user', fn () => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ] : null),
            'images' => DeviceReportImageResource::collection($this->whenLoaded('images')),
        ];
    }
}
