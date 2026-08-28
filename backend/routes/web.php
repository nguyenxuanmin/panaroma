<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SystemAuth;
use App\Http\Middleware\CheckSystemAuth;
use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\LoginAuth;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\FloorController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\PanoramaController;
use App\Http\Controllers\Admin\HotspotController;

Route::group(['middleware' => [SystemAuth::class]], function () {
    Route::group(['middleware' => [AdminAuth::class]], function () {
        Route::get('/admin', [DashboardController::class, 'index'])->name('admin');
        Route::get('/admin/logout', [AdminController::class, 'logout'])->name('logout');
        Route::get('/admin/change-password', [AdminController::class, 'changePassword'])->name('change_password');
        Route::post('/admin/change-password', [AdminController::class, 'saveChangePassword'])->name('save_change_password');
        Route::get('/admin/company', [CompanyController::class, 'show'])->name('company');
        Route::post('/admin/company', [CompanyController::class, 'save'])->name('save_company');
        // Floor
        Route::get('/floor', [FloorController::class, 'show'])->name('list_floor');
        Route::get('/floor/add', [FloorController::class, 'add'])->name('add_floor');
        Route::post('/floor/save', [FloorController::class, 'save'])->name('save_floor');
        Route::post('/floor/delete', [FloorController::class, 'delete'])->name('delete_floor');
        Route::get('/floor/edit/{id}', [FloorController::class, 'edit'])->name('edit_floor');
        // Project
        Route::get('/project', [ProjectController::class, 'show'])->name('list_project');
        Route::get('/project/add', [ProjectController::class, 'add'])->name('add_project');
        Route::post('/project/save', [ProjectController::class, 'save'])->name('save_project');
        Route::post('/project/delete', [ProjectController::class, 'delete'])->name('delete_project');
        Route::get('/project/edit/{id}', [ProjectController::class, 'edit'])->name('edit_project');
        // Panorama
        Route::get('/panorama', [PanoramaController::class, 'show'])->name('list_panorama');
        Route::get('/panorama/add', [PanoramaController::class, 'add'])->name('add_panorama');
        Route::post('/panorama/save', [PanoramaController::class, 'save'])->name('save_panorama');
        Route::post('/panorama/delete', [PanoramaController::class, 'delete'])->name('delete_panorama');
        Route::get('/panorama/edit/{id}', [PanoramaController::class, 'edit'])->name('edit_panorama');
        // Hotspot
        Route::get('/hotspot', [HotspotController::class, 'show'])->name('list_hotspot');
        Route::get('/hotspot/add', [HotspotController::class, 'add'])->name('add_hotspot');
        Route::post('/hotspot/save', [HotspotController::class, 'save'])->name('save_hotspot');
        Route::post('/hotspot/delete', [HotspotController::class, 'delete'])->name('delete_hotspot');
        Route::get('/hotspot/edit/{id}', [HotspotController::class, 'edit'])->name('edit_hotspot');
    });
    Route::group(['middleware' => [LoginAuth::class]], function () {
        Route::get('/admin/login', function () {return view('admin.login');})->name('login');
        Route::post('/admin/login', [AdminController::class, 'login'])->name('login');
    });
});

Route::group(['middleware' => [CheckSystemAuth::class]], function () {
    Route::get('/system', [SystemController::class, 'index'])->name('system');
    Route::post('/system', [SystemController::class, 'save'])->name('save_system');
});
