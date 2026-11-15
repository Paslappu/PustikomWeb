<?php

namespace App\Http\Controllers;

use App\Models\Tipe;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.master-data.tipe.index', [
            'title' => 'Tipe',
            'subtitle' => '',
            'active' => 'tipe',
            'datas' => Tipe::latest()->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.master-data.tipe.create', [
            'title' => 'Tipe',
            'subtitle' => 'Add Tipe',
            'active' => 'tipe',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validasi input
            $this->validate($request, [
                'tipe_name' => 'required|unique:tipes,name',
                'icon' => 'nullable|string', // Validasi ikon
                'isRuckus' => 'required'
            ], [
                'tipe_name.required' => 'Tipe name is required!',
                'tipe_name.unique' => 'Tipe name already exists!',
            ]);

            // Menghandle upload file 3D jika ada
            $filePath = null;
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '-' . Str::slug($request->tipe_name) . '.' . $file->getClientOriginalExtension();

                // Store the file in 'public/models' directory inside storage
                $file->storeAs('public/models', $fileName);

                // Store the relative path for access
                $filePath = 'models/' . $fileName;
            }

            // Membuat tipe baru
            Tipe::create([
                'name' => $request->tipe_name,
                'slug' => Str::slug($request->tipe_name),
                'file' => $filePath, // Menyimpan path file 3D
                'icon' => $request->icon, // Menyimpan ikon
                'isRuckus' => $request->isRuckus
            ]);

            // Commit transaction
            DB::commit();

            return redirect()->route('admin.tipe')->with('success', 'Tipe has been added!');
        } catch (\Exception $e) {
            // Rollback transaction jika terjadi error
            DB::rollBack();

            // Menangani exception dan memberikan response error
            return redirect()->route('admin.tipe.create')->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $tipe = Tipe::findOrFail($id);
        return view('admin.master-data.tipe.edit', [
            'title' => 'Tipe',
            'subtitle' => 'Edit Tipe',
            'active' => 'tipe',
            'data' => $tipe,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        
        DB::beginTransaction();

        try {
            $this->validate($request, [
                'tipe_name' => 'required|unique:tipes,name,' . $id,
                'icon' => 'nullable|string',
                'isRuckus' => 'required',
            ], [
                'tipe_name.required' => 'Tipe name is required!',
                'tipe_name.unique' => 'Tipe name already exists!',
            ]);

            $tipe = Tipe::findOrFail($id);

            // Menghandle file 3D jika ada
            $filePath = $tipe->file; // Simpan path file lama
            if ($request->hasFile('file')) {
                // Hapus file lama jika ada
                if ($filePath) {
                    Storage::delete($filePath);
                }

                // Menyimpan file baru
                $file = $request->file('file');
                $fileName = time() . '-' . Str::slug($request->tipe_name) . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/models', $fileName);

                // Store the relative path for access
                $filePath = 'models/' . $fileName;
            }

            // Update data tipe
            $tipe->update([
                'name' => $request->tipe_name,
                'slug' => Str::slug($request->tipe_name),
                'file' => $filePath, // Update path file 3D
                'icon' => $request->icon, // Update ikon
                'isRuckus' => $request->isRuckus
            ]);

            // Commit transaction
            DB::commit();

            return redirect()->route('admin.tipe')->with('success', 'Tipe has been updated!');
        } catch (\Exception $e) {
            // Rollback transaction jika terjadi error
            DB::rollBack();

            // Menangani exception dan memberikan response error
            return redirect()->route('admin.tipe.edit', $id)->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $tipe = Tipe::findOrFail($id);
        return view('admin.master-data.tipe.show', [
            'title' => 'Tipe Detail',
            'subtitle' => 'Detail Tipe',
            'active' => 'tipe',
            'tipe' => $tipe,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $tipe = Tipe::findOrFail($id);

            // Hapus file 3D jika ada
            if ($tipe->file) {
                Storage::delete($tipe->file); // Menghapus file 3D dari storage
            }

            $tipe->delete();

            // Commit transaction
            DB::commit();

            return redirect()->route('admin.tipe')->with('success', 'Tipe has been deleted!');
        } catch (\Exception $e) {
            // Rollback transaction jika terjadi error
            DB::rollBack();

            // Menangani exception dan memberikan response error
            return redirect()->route('admin.tipe')->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }
}
