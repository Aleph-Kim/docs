<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\VisualController as AdminVisualController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\VisualController;
use Illuminate\Support\Facades\Route;

// SEO / GEO / AEO
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/llms.txt', [SeoController::class, 'llms'])->name('llms.txt');
Route::get('/llms-full.txt', [SeoController::class, 'llmsFull'])->name('llms-full.txt');

// 공개 (로그인 불필요)
Route::get('/', [VisualController::class, 'index'])->name('visuals.index');
Route::get('/visuals/{visual:slug}', [VisualController::class, 'show'])->name('visuals.show');
Route::get('/visuals/{visual:slug}/render', [VisualController::class, 'render'])->name('visuals.render');

// 관리자 로그인
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// 관리자 전용
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::resource('visuals', AdminVisualController::class)->except('show');
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
});
