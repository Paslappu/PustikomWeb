<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceReport;
use App\Models\Gedung;
use App\Models\User;
use App\Services\DeviceReportService;
use App\Support\DeviceReportRules;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeviceReportController extends Controller
{
    /**
     * Titik tengah default peta (kira-kira Gorontalo) saat laporan belum punya koordinat.
     */
    private const MAP_CENTER = ['lat' => 0.5412, 'lng' => 123.0595];

    public function __construct(private DeviceReportService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
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
        ], [
            'from.date' => 'Tanggal awal tidak valid.',
            'to.date' => 'Tanggal akhir tidak valid.',
        ]);

        $user = auth()->user();
        $isAdmin = $user->isAdmin();

        $query = $this->service->query($user, $filters)
            ->with(['device.tipe', 'gedung', 'user', 'images']);

        return view('admin.master-data.device-report.index', [
            'title' => 'Device Report',
            'subtitle' => '',
            'active' => 'device-report',
            'reports' => $query->paginate(15)->withQueryString(),
            'devices' => Device::orderBy('device_id', 'ASC')->get(['id', 'device_id']),
            'gedungs' => Gedung::orderBy('name', 'ASC')->get(),
            'users' => $isAdmin ? User::orderBy('name', 'ASC')->get(['id', 'name']) : collect(),
            'isAdmin' => $isAdmin,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', DeviceReport::class);

        return view('admin.master-data.device-report.create', [
            'title' => 'Device Report',
            'subtitle' => 'Add Device Report',
            'active' => 'device-report',
            'devices' => Device::with('tipe')->orderBy('device_id', 'ASC')->get(),
            'gedungs' => Gedung::orderBy('name', 'ASC')->get(),
            'mapCenter' => self::MAP_CENTER,
            'maxImages' => DeviceReportRules::MAX_IMAGES,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', DeviceReport::class);

        $validated = $this->validate(
            $request,
            array_merge(DeviceReportRules::base(), DeviceReportRules::images(true)),
            DeviceReportRules::messages()
        );

        // UUID dibuat server untuk laporan dari web
        $validated['uuid'] = (string) Str::uuid();

        $this->service->create($validated, $request->file('images', []), $request->user());

        return redirect()->route('admin.device-report')->with('success', 'Device report has been added!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $report = DeviceReport::with(['device.tipe', 'gedung', 'user', 'images'])->findOrFail($id);
        $this->authorize('view', $report);

        return view('admin.master-data.device-report.show', [
            'title' => 'Device Report',
            'subtitle' => 'Device Report Detail',
            'active' => 'device-report',
            'report' => $report,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $report = DeviceReport::with('images')->findOrFail($id);
        $this->authorize('update', $report);

        return view('admin.master-data.device-report.edit', [
            'title' => 'Device Report',
            'subtitle' => 'Edit Device Report',
            'active' => 'device-report',
            'report' => $report,
            'devices' => Device::with('tipe')->orderBy('device_id', 'ASC')->get(),
            'gedungs' => Gedung::orderBy('name', 'ASC')->get(),
            'mapCenter' => self::MAP_CENTER,
            'maxImages' => DeviceReportRules::MAX_IMAGES,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $report = DeviceReport::findOrFail($id);
        $this->authorize('update', $report);

        $validated = $this->validate($request, DeviceReportRules::webUpdate($report), DeviceReportRules::messages());

        $this->service->update(
            $report,
            $validated,
            $request->file('images', []),
            $validated['delete_image_ids'] ?? []
        );

        return redirect()->route('admin.device-report')->with('success', 'Device report has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $report = DeviceReport::findOrFail($id);
        $this->authorize('delete', $report);

        $this->service->delete($report);

        return redirect()->route('admin.device-report')->with('success', 'Device report has been deleted!');
    }
}
