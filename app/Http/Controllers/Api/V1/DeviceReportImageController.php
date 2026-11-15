<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeviceReportImageResource;
use App\Models\DeviceReport;
use App\Models\DeviceReportImage;
use App\Services\DeviceReportService;
use App\Support\DeviceReportRules;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceReportImageController extends Controller
{
    public function __construct(private DeviceReportService $service)
    {
    }

    /**
     * Tambah gambar (multipart `images[]`), total per laporan maksimal 5.
     */
    public function store(Request $request, DeviceReport $deviceReport): JsonResponse
    {
        $this->authorize('update', $deviceReport);

        $this->validate($request, DeviceReportRules::images(true), DeviceReportRules::messages());

        $created = $this->service->addImages($deviceReport, $request->file('images', []));

        return DeviceReportImageResource::collection(collect($created))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Hapus satu gambar (422 bila itu gambar terakhir). Binding di-scope ke laporan.
     */
    public function destroy(DeviceReport $deviceReport, DeviceReportImage $image): JsonResponse
    {
        $this->authorize('update', $deviceReport);

        $this->service->deleteImage($deviceReport, $image);

        return response()->json(null, 204);
    }
}
