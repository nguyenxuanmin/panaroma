<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\LoginAuth;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\FloorController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\PanaromaController;
use App\Http\Controllers\Admin\HotspotController;

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
    //Route::get('/project/add', [ProjectController::class, 'add'])->name('add_project');
    Route::post('/project/save', [ProjectController::class, 'save'])->name('save_project');
    //Route::post('/project/delete', [ProjectController::class, 'delete'])->name('delete_project');
    Route::get('/project/edit/{id}', [ProjectController::class, 'edit'])->name('edit_project');
    Route::get('/project/change-password/{id}', [ProjectController::class, 'changePassword'])->name('change_password_project');
    // Panaroma
    Route::get('/panaroma', [PanaromaController::class, 'show'])->name('list_panaroma');
    Route::get('/panaroma/add', [PanaromaController::class, 'add'])->name('add_panaroma');
    Route::post('/panaroma/save', [PanaromaController::class, 'save'])->name('save_panaroma');
    Route::post('/panaroma/delete', [PanaromaController::class, 'delete'])->name('delete_panaroma');
    Route::get('/panaroma/edit/{id}', [PanaromaController::class, 'edit'])->name('edit_panaroma');
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
