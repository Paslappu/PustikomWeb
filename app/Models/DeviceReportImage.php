<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DeviceReportImage extends Model
{
    protected $fillable = [
        'device_report_id',
        'path',
        'sort',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(DeviceReport::class, 'device_report_id');
    }

    /**
     * URL publik gambar (butuh `php artisan storage:link`).
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }
}
