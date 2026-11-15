<?php

namespace App\Http\Controllers;

use App\Models\Gedung;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class GedungController extends Controller
{
    /**
     * Display a listing of the resources.
     */
    public function index()
    {
        return view('admin.master-data.gedung.index', [
            'title' => 'Gedung',
            'subtitle' => '',
            'active' => 'gedung',
            'datas' => Gedung::orderBy('name', 'ASC')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.master-data.gedung.create', [
            'title' => 'Gedung',
            'subtitle' => 'Add Building',
            'active' => 'gedung',
            'gedungs' => Gedung::orderBy('name', 'ASC')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction(); // Start the transaction

        try {
            // Validasi input
            $this->validate($request, [
                'name' => 'required|max:255',
                'lokasi' => 'required|max:255',
                'parent_id' => 'nullable|exists:gedungs,id',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ], [
                'name.required' => 'Building name is required!',
                'lokasi.required' => 'Location is required!',
                'photo.image' => 'Photo must be an image!',
                'photo.mimes' => 'Photo must be a jpeg, png, jpg, or gif!',
            ]);

            $slug = Str::slug($request->input('name'));

            // Menangani upload foto gedung
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $photoPath = time() . '-' . rand(1, 100) . '-' . $slug . '.' . $photo->extension();
                $photo->move(public_path('uploads/gedung/photo'), $photoPath);
            }

            // Menyimpan data gedung
            Gedung::create([
                'name' => $request->input('name'),
                'slug' => $slug,
                'lokasi' => $request->input('lokasi'),
                'parent_id' => $request->input('parent_id'),
                'photo' => $photoPath,
            ]);

            DB::commit(); // Commit the transaction

            return redirect()->route('admin.gedung')->with('success', 'Building has been added!');
        } catch (\Exception $e) {
            DB::rollback(); // Rollback the transaction if something goes wrong

            // Log error or handle exception
            return back()->withErrors(['error' => 'An error occurred while saving the building.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Ambil data gedung berdasarkan ID
        $gedung = Gedung::findOrFail($id);

        // Ambil lokasi perangkat berdasarkan gedung_id dan device_id yang tidak sama, ambil yang terbaru
        $devicesWithLastLocation = Location::where('gedung_id', $gedung->id)
            ->whereHas('device')  // pastikan ada device terkait
            ->latest('created_at') // ambil lokasi terbaru
            ->get();

        return view('admin.master-data.gedung.show', [
            'title' => 'Gedung',
            'subtitle' => 'Building Detail',
            'active' => 'gedung',
            'data' => $gedung,
            'devicesWithLastLocation' => $devicesWithLastLocation,
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $gedung = Gedung::findOrFail($id);
        $gedungs = Gedung::where('id', '!=', $id)->orderBy('name', 'ASC')->get();
    // ---------------------------

        return view('admin.master-data.gedung.edit', [
            'title' => 'Gedung',
            'subtitle' => 'Edit Gedung',
            'active' => 'gedung',
            'data' => $gedung,
            'gedungs' => $gedungs, // <--- JANGAN LUPA TAMBAHKAN INI KE ARRAY
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction(); // Start the transaction

        try {
            // Validasi input
            $this->validate($request, [
                'name' => 'required|max:255',
                'lokasi' => 'required|max:255',
                'parent_id' => 'nullable|exists:gedungs,id',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ], [
                'name.required' => 'Building name is required!',
                'lokasi.required' => 'Location is required!',
                'photo.image' => 'Photo must be an image!',
                'photo.mimes' => 'Photo must be a jpeg, png, jpg, or gif!',
            ]);

            $gedung = Gedung::findOrFail($id);
            $slug = Str::slug($request->input('name'));

            // Menangani upload foto gedung jika ada
            $photoPath = $gedung->photo;
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $photoPath = time() . '-' . rand(1, 100) . '-' . $slug . '.' . $photo->extension();
                $photo->move(public_path('uploads/gedung/photo'), $photoPath);

                // Menghapus foto lama jika ada
                if ($gedung->photo && file_exists(public_path('uploads/gedung/photo/' . $gedung->photo))) {
                    unlink(public_path('uploads/gedung/photo/' . $gedung->photo));
                }
            }

            // Memperbarui data gedung
            $gedung->update([
                'name' => $request->input('name'),
                'slug' => $slug,
                'lokasi' => $request->input('lokasi'),
                'parent_id' => $request->input('parent_id'),
                'photo' => $photoPath,
            ]);

            DB::commit(); // Commit the transaction

            return redirect()->route('admin.gedung')->with('success', 'Building has been updated!');
        } catch (\Exception $e) {
            DB::rollback(); // Rollback the transaction if something goes wrong

            // Log error or handle exception
            return back()->withErrors(['error' => 'An error occurred while updating the building.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction(); // Start the transaction

        try {
            $gedung = Gedung::findOrFail($id);
            $photo = $gedung->photo;

            // Menghapus foto jika ada
            if ($photo && file_exists(public_path('uploads/gedung/photo/' . $photo))) {
                unlink(public_path('uploads/gedung/photo/' . $photo));
            }

            $gedung->delete();

            DB::commit(); // Commit the transaction

            return redirect()->route('admin.gedung')->with('success', 'Building has been deleted!');
        } catch (\Exception $e) {
            DB::rollback(); // Rollback the transaction if something goes wrong

            // Log error or handle exception
            return back()->withErrors(['error' => 'An error occurred while deleting the building.']);
        }
    }
}
