<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\LoginAuth;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\BuildingController;
use App\Http\Controllers\Admin\FloorController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\PanaromaController;
use App\Http\Controllers\Admin\HotspotController;
use App\Http\Controllers\Admin\VideoController;

Route::group(['middleware' => [AdminAuth::class]], function () {
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin');
    Route::get('/admin/logout', [AdminController::class, 'logout'])->name('logout');
    Route::get('/admin/change-password', [AdminController::class, 'changePassword'])->name('change_password');
    Route::post('/admin/change-password', [AdminController::class, 'saveChangePassword'])->name('save_change_password');
    Route::get('/admin/company', [CompanyController::class, 'show'])->name('company');
    Route::post('/admin/company', [CompanyController::class, 'save'])->name('save_company');
    // Building
    Route::get('/admin/building', [BuildingController::class, 'show'])->name('list_building');
    Route::get('/admin/building/add', [BuildingController::class, 'add'])->name('add_building');
    Route::post('/admin/building/save', [BuildingController::class, 'save'])->name('save_building');
    Route::post('/admin/building/delete', [BuildingController::class, 'delete'])->name('delete_building');
    Route::get('/admin/building/edit/{id}', [BuildingController::class, 'edit'])->name('edit_building');
    // Floor
    Route::get('/admin/floor', [FloorController::class, 'show'])->name('list_floor');
    Route::get('/admin/floor/add', [FloorController::class, 'add'])->name('add_floor');
    Route::post('/admin/floor/save', [FloorController::class, 'save'])->name('save_floor');
    Route::post('/admin/floor/delete', [FloorController::class, 'delete'])->name('delete_floor');
    Route::get('/admin/floor/edit/{id}', [FloorController::class, 'edit'])->name('edit_floor');
    // Project
    Route::get('/admin/project', [ProjectController::class, 'show'])->name('list_project');
    //Route::get('/project/add', [ProjectController::class, 'add'])->name('add_project');
    Route::post('/admin/project/save', [ProjectController::class, 'save'])->name('save_project');
    //Route::post('/project/delete', [ProjectController::class, 'delete'])->name('delete_project');
    Route::get('/admin/project/edit/{id}', [ProjectController::class, 'edit'])->name('edit_project');
    Route::get('/admin/project/change-password', [ProjectController::class, 'changePassword'])->name('change_password_project');
    Route::get('/admin/project/map', [ProjectController::class, 'map'])->name('map_project');
    // Panaroma
    Route::get('/admin/panaroma', [PanaromaController::class, 'show'])->name('list_panaroma');
    Route::get('/admin/panaroma/add', [PanaromaController::class, 'add'])->name('add_panaroma');
    Route::post('/admin/panaroma/save', [PanaromaController::class, 'save'])->name('save_panaroma');
    Route::post('/admin/panaroma/delete', [PanaromaController::class, 'delete'])->name('delete_panaroma');
    Route::get('/admin/panaroma/edit/{id}', [PanaromaController::class, 'edit'])->name('edit_panaroma');
    Route::post('/admin/panaroma/delete-panaroma-image', [PanaromaController::class, 'deletePanaromaImage'])->name('delete_panaroma_image');
    // Hotspot
    Route::get('/admin/hotspot', [HotspotController::class, 'show'])->name('list_hotspot');
    Route::get('/admin/hotspot/add', [HotspotController::class, 'add'])->name('add_hotspot');
    Route::post('/admin/hotspot/save', [HotspotController::class, 'save'])->name('save_hotspot');
    Route::post('/admin/hotspot/delete', [HotspotController::class, 'delete'])->name('delete_hotspot');
    Route::get('/admin/hotspot/edit/{id}', [HotspotController::class, 'edit'])->name('edit_hotspot');
    // Video
    Route::get('/admin/video', [VideoController::class, 'show'])->name('list_video');
    Route::get('/admin/video/add', [VideoController::class, 'add'])->name('add_video');
    Route::post('/admin/video/save', [VideoController::class, 'save'])->name('save_video');
    Route::post('/admin/video/delete', [VideoController::class, 'delete'])->name('delete_video');
    Route::get('/admin/video/edit/{id}', [VideoController::class, 'edit'])->name('edit_video');
});
Route::group(['middleware' => [LoginAuth::class]], function () {
    Route::get('/admin/login', function () {return view('admin.login');})->name('login');
    Route::post('/admin/login', [AdminController::class, 'login'])->name('login');
});