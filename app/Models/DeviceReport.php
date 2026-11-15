<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Laporan lapangan sebuah Device (foto, koordinat, catatan).
 *
 * Catatan: tidak ada model event di sini. Semua penghapusan harus lewat
 * DeviceReportService supaya file gambar ikut terhapus (cascade DB tidak
 * memicu event Eloquent).
 */
class DeviceReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'device_id',
        'user_id',
        'gedung_id',
        'captured_at',
        'latitude',
        'longitude',
        'notes',
    ];

    protected $casts = [
        'captured_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gedung(): BelongsTo
    {
        return $this->belongsTo(Gedung::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(DeviceReportImage::class)->orderBy('sort')->orderBy('id');
    }
}
