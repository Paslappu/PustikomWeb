<?php

namespace App\Support;

use App\Models\DeviceReport;
use Illuminate\Validation\Rule;

/**
 * Aturan validasi bersama untuk web dan API. Controller tetap memanggil
 * $this->validate() sendiri; kelas ini hanya menyimpan daftar aturan/pesan.
 */
class DeviceReportRules
{
    public const MAX_IMAGES = 5;

    public const MAX_IMAGE_KB = 5120;

    /**
     * Field utama laporan. $partial = true untuk update parsial (PUT/PATCH API).
     */
    public static function base(bool $partial = false): array
    {
        $sometimes = $partial ? ['sometimes'] : [];

        return [
            'device_id' => array_merge($sometimes, ['required', 'integer', 'exists:devices,id']),
            'gedung_id' => ['nullable', 'integer', 'exists:gedungs,id'],
            'captured_at' => array_merge($sometimes, ['required', 'date', 'before_or_equal:now']),
            'latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitude'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Aturan file gambar. $required = true saat membuat laporan (minimal 1 foto).
     */
    public static function images(bool $required): array
    {
        return [
            'images' => $required
                ? ['required', 'array', 'min:1', 'max:'.self::MAX_IMAGES]
                : ['nullable', 'array', 'max:'.self::MAX_IMAGES],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:'.self::MAX_IMAGE_KB],
        ];
    }

    /**
     * Aturan tambahan khusus API saat membuat laporan.
     */
    public static function apiCreate(): array
    {
        return array_merge(
            ['uuid' => ['required', 'uuid', 'unique:device_reports,uuid']],
            self::base(),
            self::images(true),
        );
    }

    /**
     * Aturan web update: field utama + gambar baru (opsional) + id gambar yang dihapus
     * (harus milik laporan ini).
     */
    public static function webUpdate(DeviceReport $report): array
    {
        return array_merge(
            self::base(),
            self::images(false),
            [
                'delete_image_ids' => ['nullable', 'array'],
                'delete_image_ids.*' => [
                    'integer',
                    Rule::exists('device_report_images', 'id')->where('device_report_id', $report->id),
                ],
            ],
        );
    }

    public static function messages(): array
    {
        return [
            'uuid.required' => 'UUID wajib diisi.',
            'uuid.uuid' => 'UUID tidak valid.',
            'uuid.unique' => 'UUID sudah digunakan.',
            'device_id.required' => 'Device wajib dipilih.',
            'device_id.integer' => 'Device tidak valid.',
            'device_id.exists' => 'Device tidak ditemukan.',
            'gedung_id.integer' => 'Gedung tidak valid.',
            'gedung_id.exists' => 'Gedung tidak ditemukan.',
            'captured_at.required' => 'Waktu pengambilan wajib diisi.',
            'captured_at.date' => 'Waktu pengambilan tidak valid.',
            'captured_at.before_or_equal' => 'Waktu pengambilan tidak boleh di masa depan.',
            'latitude.numeric' => 'Latitude harus berupa angka.',
            'latitude.between' => 'Latitude harus di antara -90 dan 90.',
            'latitude.required_with' => 'Latitude wajib diisi jika longitude diisi.',
            'longitude.numeric' => 'Longitude harus berupa angka.',
            'longitude.between' => 'Longitude harus di antara -180 dan 180.',
            'longitude.required_with' => 'Longitude wajib diisi jika latitude diisi.',
            'notes.string' => 'Catatan harus berupa teks.',
            'notes.max' => 'Catatan maksimal 2000 karakter.',
            'images.required' => 'Minimal 1 foto wajib diunggah.',
            'images.array' => 'Format foto tidak valid.',
            'images.min' => 'Minimal 1 foto wajib diunggah.',
            'images.max' => 'Maksimal '.self::MAX_IMAGES.' foto.',
            'images.*.image' => 'File harus berupa gambar.',
            'images.*.mimes' => 'Foto harus berformat jpg, jpeg, png, atau webp.',
            'images.*.max' => 'Ukuran tiap foto maksimal 5 MB.',
            'images.*.uploaded' => 'Foto gagal diunggah (periksa ukuran file dan batas upload server).',
            'delete_image_ids.array' => 'Format data hapus foto tidak valid.',
            'delete_image_ids.*.integer' => 'Foto yang dihapus tidak valid.',
            'delete_image_ids.*.exists' => 'Foto yang dihapus tidak ditemukan pada laporan ini.',
        ];
    }
}
