<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\GedungResource;
use App\Models\CategoryDana;
use App\Models\Gedung;
use App\Models\Tipe;
use Illuminate\Http\JsonResponse;

/**
 * Daftar referensi (tanpa paginasi) untuk dropdown di aplikasi mobile.
 */
class ReferenceController extends Controller
{
    public function gedung()
    {
        return GedungResource::collection(Gedung::orderBy('name')->get());
    }

    public function tipe(): JsonResponse
    {
        return response()->json([
            'data' => Tipe::orderBy('name')->get(['id', 'name', 'slug']),
        ]);
    }

    public function categoryDana(): JsonResponse
    {
        return response()->json([
            'data' => CategoryDana::orderBy('name')->get(['id', 'name', 'slug']),
        ]);
    }
}
