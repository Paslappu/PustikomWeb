<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeviceReportResource;
use App\Models\DeviceReport;
use App\Services\DeviceReportService;
use App\Support\DeviceReportRules;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeviceReportController extends Controller
{
    private const RELATIONS = ['device', 'gedung', 'user', 'images'];

    public function __construct(private DeviceReportService $service)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', DeviceReport::class);

        $filters = $this->validate($request, [
            'device_id' => 'nullable|integer',
            'gedung_id' => 'nullable|integer',
            'user_id' => 'nullable|integer',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'q' => 'nullable|string|max:100',
            'per_page' => 'nullable|integer|min:1|max:100',
        ], [
            'from.date' => 'Tanggal awal tidak valid.',
            'to.date' => 'Tanggal akhir tidak valid.',
            'per_page.max' => 'per_page maksimal 100.',
        ]);

        $paginator = $this->service->query($request->user(), $filters)
            ->with(self::RELATIONS)
            ->paginate((int) ($filters['per_page'] ?? 15))
            ->withQueryString();

        return DeviceReportResource::collection($paginator);
    }

    /**
     * Idempotent pada `uuid`: pengulangan oleh user yang sama -> 200 (data yang ada),
     * oleh user lain -> 409. Aman untuk retry dari antrean offline di mobile.
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', DeviceReport::class);

        $uuid = $request->input('uuid');
        if (is_string($uuid) && Str::isUuid($uuid)) {
            $request->merge(['uuid' => Str::lower($uuid)]);

            if ($existing = DeviceReport::where('uuid', Str::lower($uuid))->first()) {
                return $this->existingResponse($existing, $request);
            }
        }

        $validated = $this->validate($request, DeviceReportRules::apiCreate(), DeviceReportRules::messages());

        try {
            $report = $this->service->create($validated, $request->file('images', []), $request->user());
        } catch (UniqueConstraintViolationException $e) {
            // Race: request lain dengan uuid yang sama menang lebih dulu
            $existing = DeviceReport::where('uuid', $validated['uuid'])->first();
            if ($existing) {
                return $this->existingResponse($existing, $request);
            }
            throw $e;
        }

        return (new DeviceReportResource($report->load(self::RELATIONS)))
            ->response()
            ->setStatusCode(201);
    }

    public function show(DeviceReport $deviceReport): DeviceReportResource
    {
        $this->authorize('view', $deviceReport);

        return new DeviceReportResource($deviceReport->load(self::RELATIONS));
    }

    /**
     * Update field saja (JSON). Gambar lewat endpoint /images.
     */
    public function update(Request $request, DeviceReport $deviceReport): DeviceReportResource
    {
        $this->authorize('update', $deviceReport);

        $validated = $this->validate($request, DeviceReportRules::base(true), DeviceReportRules::messages());

        $report = $this->service->update($deviceReport, $validated);

        return new DeviceReportResource($report->load(self::RELATIONS));
    }

    public function destroy(DeviceReport $deviceReport): JsonResponse
    {
        $this->authorize('delete', $deviceReport);

        $this->service->delete($deviceReport);

        return response()->json(null, 204);
    }

    private function existingResponse(DeviceReport $existing, Request $request): JsonResponse
    {
        if ((int) $existing->user_id !== (int) $request->user()->id) {
            return response()->json(['message' => 'UUID sudah digunakan.'], 409);
        }

        return (new DeviceReportResource($existing->load(self::RELATIONS)))
            ->response()
            ->setStatusCode(200);
    }
}
