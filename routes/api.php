<?php

use App\Http\Controllers\Api\V1\AuthController as V1AuthController;
use App\Http\Controllers\Api\V1\DeviceController as V1DeviceController;
use App\Http\Controllers\Api\V1\DeviceReportController as V1DeviceReportController;
use App\Http\Controllers\Api\V1\DeviceReportImageController as V1DeviceReportImageController;
use App\Http\Controllers\Api\V1\ReferenceController as V1ReferenceController;
use App\Http\Controllers\AuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('/login', [AuthenticationController::class, 'login']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| API v1 (mobile app) - /api/v1/*
|--------------------------------------------------------------------------
| Selalu membalas JSON (force-json). Login memakai model User (semua role);
| akses data laporan diatur DeviceReportPolicy (pemilik atau admin).
*/
Route::prefix('v1')->name('api.v1.')->middleware('force-json')->group(function () {
    Route::post('/auth/login', [V1AuthController::class, 'login'])
        ->middleware('throttle:api-login')
        ->name('auth.login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [V1AuthController::class, 'logout'])->name('auth.logout');
        Route::get('/auth/me', [V1AuthController::class, 'me'])->name('auth.me');

        // "lookup" harus didaftarkan sebelum "{device}"
        Route::get('/devices', [V1DeviceController::class, 'index'])->name('devices.index');
        Route::get('/devices/lookup', [V1DeviceController::class, 'lookup'])->name('devices.lookup');
        Route::get('/devices/{device}', [V1DeviceController::class, 'show'])->name('devices.show');

        Route::get('/gedung', [V1ReferenceController::class, 'gedung'])->name('gedung.index');
        Route::get('/tipe', [V1ReferenceController::class, 'tipe'])->name('tipe.index');
        Route::get('/category-dana', [V1ReferenceController::class, 'categoryDana'])->name('category-dana.index');

        Route::get('/device-reports', [V1DeviceReportController::class, 'index'])->name('device-reports.index');
        Route::post('/device-reports', [V1DeviceReportController::class, 'store'])->name('device-reports.store');
        Route::get('/device-reports/{device_report}', [V1DeviceReportController::class, 'show'])->name('device-reports.show');
        Route::match(['put', 'patch'], '/device-reports/{device_report}', [V1DeviceReportController::class, 'update'])->name('device-reports.update');
        Route::delete('/device-reports/{device_report}', [V1DeviceReportController::class, 'destroy'])->name('device-reports.destroy');

        Route::post('/device-reports/{device_report}/images', [V1DeviceReportImageController::class, 'store'])->name('device-reports.images.store');
        Route::delete('/device-reports/{device_report}/images/{image}', [V1DeviceReportImageController::class, 'destroy'])
            ->scopeBindings()
            ->name('device-reports.images.destroy');
    });
});
