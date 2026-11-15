<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeviceResource;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    private const RELATIONS = ['tipe', 'categoryDana', 'latestLocation.gedung'];

    public function index(Request $request)
    {
        $filters = $this->validate($request, [
            'search' => 'nullable|string|max:100',
            'tipe_id' => 'nullable|integer',
            'category_dana_id' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'per_page' => 'nullable|integer|min:1|max:100',
        ], [
            'is_active.boolean' => 'is_active harus berupa true/false.',
            'per_page.max' => 'per_page maksimal 100.',
        ]);

        $query = Device::with(self::RELATIONS)->orderBy('device_id');

        if (! empty($filters['search'])) {
            $query->where('device_id', 'like', '%'.addcslashes($filters['search'], '%_\\').'%');
        }
        if (! empty($filters['tipe_id'])) {
            $query->where('tipe_id', $filters['tipe_id']);
        }
        if (! empty($filters['category_dana_id'])) {
            $query->where('category_dana_id', $filters['category_dana_id']);
        }
        if (array_key_exists('is_active', $filters) && $filters['is_active'] !== null) {
            $query->where('isActive', $filters['is_active']);
        }

        return DeviceResource::collection(
            $query->paginate((int) ($filters['per_page'] ?? 15))->withQueryString()
        );
    }

    /**
     * Resolusi hasil scan QR. Menerima kode mentah, URL `.../device/ABC`, atau `.../deviceABC`
     * (QR lama tidak memakai slash, lihat Device::boot).
     */
    public function lookup(Request $request): DeviceResource
    {
        $validated = $this->validate($request, [
            'code' => 'required|string|max:500',
        ], [
            'code.required' => 'Kode device wajib diisi.',
        ]);

        $raw = trim($validated['code']);
        $candidates = [$raw];

        if (preg_match('#/device/?([^/?\#]+)#i', $raw, $m)) {
            $candidates[] = urldecode($m[1]);
        }

        foreach ($candidates as $code) {
            $device = Device::with(self::RELATIONS)->where('device_id', $code)->first();
            if ($device) {
                return new DeviceResource($device);
            }
        }

        abort(404);
    }

    public function show(Device $device): DeviceResource
    {
        return new DeviceResource($device->load(self::RELATIONS));
    }
}
