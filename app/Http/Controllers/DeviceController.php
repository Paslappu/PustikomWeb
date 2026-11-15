<?php

namespace App\Http\Controllers;

use App\Models\CategoryDana;
use App\Models\Device;
use App\Models\Gedung;
use App\Models\Location;
use App\Models\Tipe;
use App\Services\RuckusService;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch devices with their latest location and type
        $devices = Device::with([
            'tipe',
            'location' => function ($query) {
                $query->orderBy('created_at', 'desc'); // Get locations ordered by latest first
            },
            'location.gedung', // Eager load gedung from location
        ])->get();

        // Fetch all types and buildings for filtering purposes
        $tipe = Tipe::orderBy('name', 'ASC')->get();
        $gedungs = Gedung::orderBy('name', 'ASC')->get();

        return view('admin.master-data.device.index', [
            'title' => 'Device',
            'subtitle' => '',
            'active' => 'device',
            'devices' => $devices,
            'gedungs' => $gedungs,
            'tipe' => $tipe,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.master-data.device.create', [
            'title' => 'Device',
            'subtitle' => 'Add Device',
            'active' => 'device',
            'tipers' => Tipe::orderBy('name', 'ASC')->get(), // Menyediakan tipe perangkat
            'gedungs' => Gedung::orderBy('name', 'ASC')->get(), // Menyediakan gedung\
            'category' => CategoryDana::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                'device_id' => 'required|unique:devices,device_id|max:255',
                'tipe_id' => 'required',
                'gedung_id' => 'required',
                'isActive' => 'required|boolean',
                'location' => 'nullable|string', // Validate location
                'category_dana_id' => 'required',
                'sticker' => 'required',
            ],
            [
                'device_id.required' => 'Device ID is required!',
                'device_id.unique' => 'Device ID already exists!',
                'tipe_id.required' => 'Device type is required!',
                'gedung_id.required' => 'Building is required!',
                'isActive.required' => 'Device status is required!',
            ]
        );

        if ($request->hasFile('sticker')) {
            $file = $request->file('sticker');
            $fileName = time().'-'.$file->getClientOriginalName();
            $file->storeAs('public/uploads/sticker/', $fileName);
        }
        // Membuat perangkat baru dan otomatis menghasilkan QR Code
        $device = Device::create([
            'device_id' => $request->device_id,
            'tipe_id' => $request->tipe_id,
            'isActive' => $request->isActive,
            'category_dana_id' => $request->category_dana_id,
            'sticker' => $fileName,
        ]);

        Location::create([
            'gedung_id' => $request->gedung_id,
            'device_id' => $device->id,
            'location' => $request->location, // Save location
        ]);

        return redirect()->route('admin.device')->with('success', 'Device has been added!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Mengambil data perangkat beserta lokasi dan tipe
        $device = Device::with([
            'location' => function ($query) {
                $query->orderBy('created_at', 'desc'); // Urutkan berdasarkan tanggal terbaru
            },
            'tipe',
        ])->findOrFail($id);

        // Mendapatkan lokasi terakhir
        $latestLocation = $device->location->first();
        $x = $y = null;
        $gedung = null;

        // Jika ada lokasi terakhir, ambil koordinat dan gedung terkait
        if ($latestLocation) {
            $gedung = $latestLocation->gedung; // Mengambil gedung dari relasi Location
            if ($latestLocation->location) {
                [$x, $y] = explode(',', $latestLocation->location); // Memisahkan x dan y
            }
        }

        // Mengambil riwayat lokasi dari perangkat
        $locationHistory = $device->location()->orderBy('created_at', 'desc')->get();

        // Check if device type is Ruckus and get AP summary if it is
        $apSummary = null;
        if ($device->tipe->isRuckus) {
            $ruckusService = app(RuckusService::class);
            try {
                $apSummary = $ruckusService->getApSummary($device->device_id);
            } catch (\Exception $e) {
                // Log the error but continue without AP data
                \Illuminate\Support\Facades\Log::error('Error getting Ruckus AP data: '.$e->getMessage());
            }
        }

        $gedungs = Gedung::orderBy('name', 'ASC')->get();

        return view('admin.master-data.device.show', [
            'title' => 'Device',
            'subtitle' => 'Device Detail',
            'active' => 'device',
            'device' => $device,
            'gedung' => $gedung,
            'gedungs' => $gedungs,
            'latestLocation' => $latestLocation,
            'x' => $x,
            'y' => $y,
            'locationHistory' => $locationHistory, // Mengirimkan riwayat lokasi
            'apSummary' => $apSummary, // Add AP summary data
        ]);
    }

    public function addLocation($id, Request $request)
    {
        $device = Device::findOrFail($id);

        // Validate the incoming data
        $request->validate([
            'gedung_id' => 'required|exists:gedungs,id',
            'location' => 'required|string|max:255',
        ]);

        // Create a new location for the device
        Location::create([
            'gedung_id' => $request->gedung_id,
            'device_id' => $device->id,
            'location' => $request->location,
        ]);

        return redirect()->route('admin.device.show', $id)->with('success', 'Location has been updated!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $device = Device::findOrFail($id);

        return view('admin.master-data.device.edit', [
            'title' => 'Device',
            'subtitle' => 'Edit Device',
            'active' => 'device',
            'device' => $device,
            'data' => $device,
            'types' => Tipe::orderBy('name', 'ASC')->get(),
            'buildings' => Gedung::orderBy('name', 'ASC')->get(),
            'categories' => CategoryDana::all(),
            'tipers' => Tipe::orderBy('name', 'ASC')->get(), // Menyediakan tipe perangkat
            'gedungs' => Gedung::orderBy('name', 'ASC')->get(), // Menyediakan gedung
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the request
        $this->validate(
            $request,
            [
                'device_id' => 'required|max:255|unique:devices,device_id,'.$id,
                'tipe_id' => 'required',
                'isActive' => 'required|boolean',
                'category_dana_id' => 'required',
            ],
            [
                'device_id.required' => 'Device ID is required!',
                'device_id.unique' => 'Device ID already exists!',
                'tipe_id.required' => 'Device type is required!',
                'isActive.required' => 'Device status is required!',
            ]
        );

        if ($request->hasFile('sticker')) {
            $file = $request->file('sticker');
            $fileName = time().'-'.$file->getClientOriginalName();
            $file->storeAs('public/uploads/sticker/', $fileName);
        }

        // Find the device or fail
        $device = Device::findOrFail($id);

        // Update the device
        $device->update([
            'device_id' => $request->device_id,
            'tipe_id' => $request->tipe_id,
            'isActive' => $request->isActive,
            'category_dana_id' => $request->category_dana_id,
        ]);

        // Return success response
        return redirect()->route('admin.device')->with('success', 'Device has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $device = Device::findOrFail($id);

        // Device yang sudah punya laporan tidak boleh dihapus (hindari file laporan yatim)
        if ($device->reports()->exists()) {
            return redirect()->route('admin.device')->with('error', 'Device cannot be deleted because it has device reports. Delete the reports first.');
        }

        $device->delete();

        return redirect()->route('admin.device')->with('success', 'Device has been deleted!');
    }
}
