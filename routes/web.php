<?php

use App\Http\Controllers\AboutUsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlatController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\FrontPageController;
use App\Http\Controllers\GedungController;
use App\Http\Controllers\InformationController;
use App\Http\Controllers\MainSliderController;
use App\Http\Controllers\ReviewSliderController;
use App\Http\Controllers\TipeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use App\Models\Device;
use Illuminate\Support\Facades\Route;
use App\Exports\DeviceExport;
use Maatwebsite\Excel\Facades\Excel;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// Debug route - Check document root and CSS
Route::get('/debug-info', function () {
    return response()->json([
        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
        'public_path' => public_path(),
        'base_path' => base_path(),
        'css_file_exists' => file_exists(public_path('css/login.css')),
        'css_path' => public_path('css/login.css'),
        'app_url' => config('app.url'),
        'asset_css' => asset('css/login.css'),
    ]);
});

// File route/web.php atau file route yang sesuai

Route::get('/device/{id}', function ($id) {
    $device = Device::with(['location' => function ($query) {
        $query->orderBy('created_at', 'desc'); // Order by the latest location
    }, 'tipe'])->where('device_id', $id)->firstOrFail();

    $x = $y = null;
    $gedung = null;

    $latestLocation = $device->location->first();

    if ($latestLocation) {
        $gedung = $latestLocation->gedung;
        if ($latestLocation->location) {
            list($x, $y) = explode(',', $latestLocation->location);
        }
    }

    // Get the location history
    $locationHistory = $device->location()->orderBy('created_at', 'desc')->get();
    
    // Check if device type is Ruckus and get AP summary if it is
    $apSummary = null;
    if ($device->tipe->isRuckus) {
        $ruckusService = app(App\Services\RuckusService::class);
        try {
            $apSummary = $ruckusService->getApSummary($device->device_id);
        } catch (\Exception $e) {
            // Log the error but continue without AP data
            \Illuminate\Support\Facades\Log::error('Error getting Ruckus AP data: ' . $e->getMessage());
        }
    }

    return view('front.device', compact('device', 'gedung', 'latestLocation', 'x', 'y', 'locationHistory', 'apSummary'));
})->name('device');



Route::get('/catalog/product/{slug}', [FrontPageController::class, 'productDetail'])->name('product.detail');

/* Authentication Routes... */
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

Auth::routes([
    'register' => false, // Register Routes...
    'reset' => false, // Reset Password Routes...
    'verify' => false, // Email Verification Routes...
]);

Route::middleware(['auth', 'login-check'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');


    Route::get('/admin/gedung', [GedungController::class, 'index'])->name('admin.gedung');
    Route::get('/admin/gedung/add', [GedungController::class, 'create'])->name('admin.gedung.create');
    Route::post('/admin/gedung/store', [GedungController::class, 'store'])->name('admin.gedung.store');
    Route::get('/admin/gedung/{id}/show', [GedungController::class, 'show'])->name('admin.gedung.show');
    Route::get('/admin/gedung/{id}/edit', [GedungController::class, 'edit'])->name('admin.gedung.edit');
    Route::put('/admin/gedung/{id}/update', [GedungController::class, 'update'])->name('admin.gedung.update');
    Route::delete('/admin/gedung/{id}/delete', [GedungController::class, 'destroy'])->name('admin.gedung.delete');

    Route::get('/admin/tipe', [TipeController::class, 'index'])->name('admin.tipe');
    Route::get('/admin/tipe/add', [TipeController::class, 'create'])->name('admin.tipe.create');
    Route::post('/admin/tipe/store', [TipeController::class, 'store'])->name('admin.tipe.store');
    Route::get('/admin/tipe/{id}/edit', [TipeController::class, 'edit'])->name('admin.tipe.edit');
    Route::put('/admin/tipe/{id}/update', [TipeController::class, 'update'])->name('admin.tipe.update');
    Route::delete('/admin/tipe/{id}/delete', [TipeController::class, 'destroy'])->name('admin.tipe.delete');
    Route::get('/admin/tipe/{id}', [TipeController::class, 'show'])->name('admin.tipe.show');

    Route::get('/admin/user', [UserController::class, 'index'])->name('admin.user');
    Route::get('/admin/user/add', [UserController::class, 'create'])->name('admin.user.create');
    Route::post('/admin/user/store', [UserController::class, 'store'])->name('admin.user.store');
    Route::get('/admin/user/{id}/show', [UserController::class, 'show'])->name('admin.user.show');
    Route::get('/admin/user/{id}/edit', [UserController::class, 'edit'])->name('admin.user.edit');
    Route::put('/admin/user/{id}/update', [UserController::class, 'update'])->name('admin.user.update');
    Route::delete('/admin/user/{id}/delete', [UserController::class, 'destroy'])->name('admin.user.delete');


    Route::get('/admin/device', [DeviceController::class, 'index'])->name('admin.device');
    Route::get('/admin/device/add', [DeviceController::class, 'create'])->name('admin.device.create');
    Route::post('/admin/device/store', [DeviceController::class, 'store'])->name('admin.device.store');
    Route::get('/admin/device/{id}/show', [DeviceController::class, 'show'])->name('admin.device.show');
    Route::get('/admin/device/{id}/edit', [DeviceController::class, 'edit'])->name('admin.device.edit');
    Route::put('/admin/device/{id}/update', [DeviceController::class, 'update'])->name('admin.device.update');
    Route::delete('/admin/device/{id}/delete', [DeviceController::class, 'destroy'])->name('admin.device.delete');
    Route::post('/admin/device/{id}/location', [DeviceController::class, 'addLocation'])->name('admin.device.addLocation');
    Route::get('admin/device/export', function () {
        return Excel::download(new DeviceExport, 'devices.xlsx');
    })->name('admin.device.export');


    Route::get('/admin/category', [CategoryController::class, 'index'])->name('admin.category');
    Route::get('/admin/category/add', [CategoryController::class, 'create'])->name('admin.category.create');
    Route::post('/admin/category/store', [CategoryController::class, 'store'])->name('admin.category.store');
    Route::get('/admin/category/{id}/edit', [CategoryController::class, 'edit'])->name('admin.category.edit');
    Route::put('/admin/category/{id}/update', [CategoryController::class, 'update'])->name('admin.category.update');
    Route::delete('/admin/category/{id}/delete', [CategoryController::class, 'destroy'])->name('admin.category.delete');

    Route::get('/admin/alat', [AlatController::class, 'index'])->name('admin.alat');
    Route::get('/admin/alat/add', [AlatController::class, 'create'])->name('admin.alat.create');
    Route::post('/admin/alat/store', [AlatController::class, 'store'])->name('admin.alat.store');
    Route::get('/admin/alat/{id}/edit', [AlatController::class, 'edit'])->name('admin.alat.edit');
    Route::put('/admin/alat/{id}/update', [AlatController::class, 'update'])->name('admin.alat.update');
    Route::delete('/admin/alat/{id}/delete', [AlatController::class, 'destroy'])->name('admin.alat.delete');


    Route::get('/admin/catalog', [CatalogController::class, 'index'])->name('admin.catalog');
    Route::get('/admin/catalog/add', [CatalogController::class, 'create'])->name('admin.catalog.create');
    Route::post('/admin/catalog/store', [CatalogController::class, 'store'])->name('admin.catalog.store');
    Route::get('/admin/catalog/{id}/show', [CatalogController::class, 'show'])->name('admin.catalog.show');
    Route::get('/admin/catalog/{id}/edit', [CatalogController::class, 'edit'])->name('admin.catalog.edit');
    Route::put('/admin/catalog/{id}/update', [CatalogController::class, 'update'])->name('admin.catalog.update');
    Route::delete('/admin/catalog/{id}/delete', [CatalogController::class, 'destroy'])->name('admin.catalog.delete');
    Route::delete('/admin/catalog/{id}/delete-image', [CatalogController::class, 'destroyImage'])->name('admin.catalog.delete-image');

    Route::get('/admin/gallery', [CatalogController::class, 'gallery'])->name('admin.gallery');


    Route::get('/admin/video', [VideoController::class, 'index'])->name('admin.video');
    Route::get('/admin/video/add', [VideoController::class, 'create'])->name('admin.video.create');
    Route::post('/admin/video/store', [VideoController::class, 'store'])->name('admin.video.store');
    Route::delete('/admin/video/{id}/delete', [VideoController::class, 'destroy'])->name('admin.video.delete');

    Route::get('/admin/information', [InformationController::class, 'index'])->name('admin.information');
    Route::get('/admin/information/add', [InformationController::class, 'create'])->name('admin.information.create');
    Route::post('/admin/information/store', [InformationController::class, 'store'])->name('admin.information.store');
    Route::delete('/admin/information/{id}/delete', [InformationController::class, 'destroy'])->name('admin.information.delete');

    Route::get('/admin/main-slider', [MainSliderController::class, 'index'])->name('admin.main-slider');
    Route::get('/admin/main-slider/add', [MainSliderController::class, 'create'])->name('admin.main-slider.create');
    Route::post('/admin/main-slider/store', [MainSliderController::class, 'store'])->name('admin.main-slider.store');
    Route::get('/admin/main-slider/{id}/edit', [MainSliderController::class, 'edit'])->name('admin.main-slider.edit');
    Route::put('/admin/main-slider/{id}/update', [MainSliderController::class, 'update'])->name('admin.main-slider.update');
    Route::delete('/admin/main-slider/{id}/delete', [MainSliderController::class, 'destroy'])->name('admin.main-slider.delete');

    Route::get('/admin/review-slider', [ReviewSliderController::class, 'index'])->name('admin.review-slider');
    Route::get('/admin/review-slider/add', [ReviewSliderController::class, 'create'])->name('admin.review-slider.create');
    Route::post('/admin/review-slider/store', [ReviewSliderController::class, 'store'])->name('admin.review-slider.store');
    Route::get('/admin/review-slider/{id}/edit', [ReviewSliderController::class, 'edit'])->name('admin.review-slider.edit');
    Route::put('/admin/review-slider/{id}/update', [ReviewSliderController::class, 'update'])->name('admin.review-slider.update');
    Route::delete('/admin/review-slider/{id}/delete', [ReviewSliderController::class, 'destroy'])->name('admin.review-slider.delete');

    Route::get('/admin/about-us', [AboutUsController::class, 'index'])->name('admin.about-us');
    Route::put('/admin/about-us/{id}/update', [AboutUsController::class, 'update'])->name('admin.about-us.update');

    Route::get('/admin/account-setting', [AdminController::class, 'accountSetting'])->name('admin.account-setting');
    Route::put('/admin/change-password/{id}', [AdminController::class, 'changePassword'])->name('admin.change-password');
    Route::put('/admin/change-information/{id}', [AdminController::class, 'changeInformation'])->name('admin.change-information');
});