<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Gedung */
class GedungResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'lokasi' => $this->lokasi,
            'label' => $this->display_label,
            'parent_id' => $this->parent_id,
            // Foto gedung disimpan di public/uploads/gedung/photo (lihat GedungController)
            'photo_url' => $this->photo ? asset('uploads/gedung/photo/'.$this->photo) : null,
        ];
    }
}
