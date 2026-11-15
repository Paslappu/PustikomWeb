<?php

namespace App\Services;

use App\Models\DeviceReport;
use App\Models\DeviceReportImage;
use App\Models\Gedung;
use App\Models\User;
use App\Support\DeviceReportRules;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Dipakai bersama oleh controller web dan API.
 * Semua operasi file gambar (simpan/hapus) lewat sini agar DB dan disk tetap konsisten.
 */
class DeviceReportService
{
    private const DISK = 'public';

    private const FIELDS = ['uuid', 'device_id', 'gedung_id', 'captured_at', 'latitude', 'longitude', 'notes'];

    /**
     * Query daftar laporan yang sudah di-scope per role dan difilter.
     * Non-admin hanya melihat laporan miliknya; filter user_id hanya berlaku untuk admin.
     *
     * @param  array{device_id?: ?int, gedung_id?: ?int, user_id?: ?int, from?: ?string, to?: ?string, q?: ?string}  $filters
     */
    public function query(User $user, array $filters): Builder
    {
        $query = DeviceReport::query()->orderByDesc('captured_at')->orderByDesc('id');

        if (! $user->isAdmin()) {
            $query->where('user_id', $user->id);
        } elseif (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (! empty($filters['device_id'])) {
            $query->where('device_id', $filters['device_id']);
        }

        if (! empty($filters['gedung_id'])) {
            $query->whereIn('gedung_id', $this->gedungWithDescendantIds((int) $filters['gedung_id']));
        }

        if (! empty($filters['from'])) {
            $query->where('captured_at', '>=', Carbon::parse($filters['from'])->startOfDay());
        }

        if (! empty($filters['to'])) {
            $query->where('captured_at', '<=', Carbon::parse($filters['to'])->endOfDay());
        }

        if (! empty($filters['q'])) {
            $like = '%'.addcslashes($filters['q'], '%_\\').'%';
            $query->where(function (Builder $q) use ($like) {
                $q->where('notes', 'like', $like)
                    ->orWhereHas('device', fn (Builder $d) => $d->where('device_id', 'like', $like));
            });
        }

        return $query;
    }

    /**
     * Buat laporan beserta gambarnya.
     *
     * @param  array<int, UploadedFile>  $images
     */
    public function create(array $data, array $images, ?User $user): DeviceReport
    {
        $stored = [];

        try {
            return DB::transaction(function () use ($data, $images, $user, &$stored) {
                $report = new DeviceReport($this->fields($data));
                $report->user_id = $user?->id;
                $report->save();

                $sort = 0;
                foreach ($images as $file) {
                    $path = $this->storeFile($file);
                    $stored[] = $path;
                    $report->images()->create(['path' => $path, 'sort' => $sort++]);
                }

                return $report;
            });
        } catch (Throwable $e) {
            $this->deleteFiles($stored);
            throw $e;
        }
    }

    /**
     * Ubah field laporan, tambah gambar baru, dan/atau hapus gambar lama.
     * Total gambar setelah operasi harus 1-5. File lama dihapus setelah commit.
     *
     * @param  array<int, UploadedFile>  $newImages
     * @param  array<int, int|string>  $deleteImageIds
     */
    public function update(DeviceReport $report, array $data, array $newImages = [], array $deleteImageIds = []): DeviceReport
    {
        $stored = [];
        $obsolete = [];

        try {
            DB::transaction(function () use ($report, $data, $newImages, $deleteImageIds, &$stored, &$obsolete) {
                $locked = DeviceReport::whereKey($report->id)->lockForUpdate()->firstOrFail();

                $locked->fill($this->fields($data));
                $locked->save();

                if (! empty($deleteImageIds)) {
                    $locked->images()->whereIn('id', $deleteImageIds)->get()->each(function (DeviceReportImage $image) use (&$obsolete) {
                        $obsolete[] = $image->path;
                        $image->delete();
                    });
                }

                $total = $locked->images()->count() + count($newImages);

                if ($total < 1) {
                    throw ValidationException::withMessages(['images' => 'Laporan minimal harus memiliki 1 foto.']);
                }
                if ($total > DeviceReportRules::MAX_IMAGES) {
                    throw ValidationException::withMessages(['images' => 'Maksimal '.DeviceReportRules::MAX_IMAGES.' foto per laporan.']);
                }

                $this->attachFiles($locked, $newImages, $stored);
            });
        } catch (Throwable $e) {
            $this->deleteFiles($stored);
            throw $e;
        }

        // Commit sukses: baru hapus file lama dari disk.
        $this->deleteFiles($obsolete);

        return $report->refresh();
    }

    /**
     * Tambah gambar ke laporan (total maksimal 5).
     *
     * @param  array<int, UploadedFile>  $images
     * @return array<int, DeviceReportImage>
     */
    public function addImages(DeviceReport $report, array $images): array
    {
        $stored = [];

        try {
            return DB::transaction(function () use ($report, $images, &$stored) {
                $locked = DeviceReport::whereKey($report->id)->lockForUpdate()->firstOrFail();

                if ($locked->images()->count() + count($images) > DeviceReportRules::MAX_IMAGES) {
                    throw ValidationException::withMessages(['images' => 'Maksimal '.DeviceReportRules::MAX_IMAGES.' foto per laporan.']);
                }

                return $this->attachFiles($locked, $images, $stored);
            });
        } catch (Throwable $e) {
            $this->deleteFiles($stored);
            throw $e;
        }
    }

    /**
     * Hapus satu gambar. Gambar terakhir tidak boleh dihapus.
     */
    public function deleteImage(DeviceReport $report, DeviceReportImage $image): void
    {
        $path = DB::transaction(function () use ($report, $image) {
            $locked = DeviceReport::whereKey($report->id)->lockForUpdate()->firstOrFail();

            if ($locked->images()->count() <= 1) {
                throw ValidationException::withMessages(['image' => 'Laporan minimal harus memiliki 1 foto.']);
            }

            $image->delete();

            return $image->path;
        });

        $this->deleteFiles([$path]);
    }

    /**
     * Hapus laporan beserta seluruh gambarnya (DB lalu file).
     */
    public function delete(DeviceReport $report): void
    {
        $paths = DB::transaction(function () use ($report) {
            $paths = $report->images()->pluck('path')->all();
            $report->images()->delete();
            $report->delete();

            return $paths;
        });

        $this->deleteFiles($paths);
    }

    /**
     * ID gedung beserta seluruh turunannya (aman dari siklus parent_id).
     *
     * @return array<int, int>
     */
    public function gedungWithDescendantIds(int $gedungId): array
    {
        $ids = [$gedungId];
        $frontier = [$gedungId];

        while (! empty($frontier)) {
            $children = Gedung::whereIn('parent_id', $frontier)->pluck('id')->all();
            $frontier = array_values(array_diff($children, $ids));
            $ids = array_merge($ids, $frontier);
        }

        return $ids;
    }

    /**
     * Ambil field yang diizinkan dan samakan captured_at ke timezone aplikasi.
     */
    private function fields(array $data): array
    {
        $fields = Arr::only($data, self::FIELDS);

        if (isset($fields['captured_at'])) {
            $fields['captured_at'] = Carbon::parse($fields['captured_at'])->setTimezone(config('app.timezone'));
        }

        return $fields;
    }

    /**
     * @param  array<int, UploadedFile>  $files
     * @param  array<int, string>  $stored  diisi path file yang tersimpan (untuk cleanup bila gagal)
     * @return array<int, DeviceReportImage>
     */
    private function attachFiles(DeviceReport $report, array $files, array &$stored): array
    {
        $sort = (int) $report->images()->max('sort');
        $created = [];

        foreach ($files as $file) {
            $path = $this->storeFile($file);
            $stored[] = $path;
            $created[] = $report->images()->create(['path' => $path, 'sort' => ++$sort]);
        }

        return $created;
    }

    private function storeFile(UploadedFile $file): string
    {
        // Nama file di-hash oleh store(); nama dari klien tidak pernah dipakai.
        $path = $file->store('uploads/device-report/'.now()->format('Y/m'), self::DISK);

        if ($path === false) {
            throw new \RuntimeException('Gagal menyimpan file gambar.');
        }

        return $path;
    }

    /**
     * @param  array<int, string>  $paths
     */
    private function deleteFiles(array $paths): void
    {
        foreach ($paths as $path) {
            try {
                Storage::disk(self::DISK)->delete($path);
            } catch (Throwable $e) {
                Log::error('Gagal menghapus file device report: '.$path.' - '.$e->getMessage());
            }
        }
    }
}
