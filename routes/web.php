<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DomainController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\EmbedController;
use App\Http\Controllers\StreamController;
use App\Http\Controllers\WatchController;
use App\Http\Controllers\InstallController;
use Illuminate\Support\Facades\Route;

// Installation Routes
Route::middleware('check.not.installed')->group(function () {
    Route::get('/install', [InstallController::class, 'index'])->name('install.index');
    Route::post('/install/setup-env', [InstallController::class, 'setupEnv'])->name('install.setup-env');
    Route::post('/install/run-migrations', [InstallController::class, 'runMigrations'])->name('install.run-migrations');
    Route::post('/install/create-admin', [InstallController::class, 'createAdmin'])->name('install.create-admin');
    Route::post('/install/finalize', [InstallController::class, 'finalize'])->name('install.finalize');
});

Route::get('/', function () {
    return redirect()->route('login');
});

// Auth
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin Dashboard
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Videos
    Route::resource('videos', VideoController::class);
    Route::post('videos/{video}/upload-chunk', [VideoController::class, 'uploadChunk'])->name('videos.upload-chunk')->middleware('throttle:uploads');
    Route::get('videos/{video}/status', [VideoController::class, 'status'])->name('videos.status');
    Route::post('videos/{video}/domains', [VideoController::class, 'addDomain'])->name('videos.domains.store');
    Route::delete('videos/{video}/domains/{domainId}', [VideoController::class, 'removeDomain'])->name('videos.domains.destroy');

    // Users
    Route::resource('users', UserController::class)->except(['show']);

    // Allowed Domains (global)
    Route::get('domains', [DomainController::class, 'index'])->name('domains.index');
    Route::post('domains', [DomainController::class, 'store'])->name('domains.store');
    Route::delete('domains/{domain}', [DomainController::class, 'destroy'])->name('domains.destroy');

    // Analytics
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
});

// Streaming (signed & protected)
Route::get('/secure-stream/{token}', [StreamController::class, 'play'])
    ->name('stream.play')
    ->middleware(['prevent.hotlinking', 'throttle:streaming']);

Route::get('/hls-segment/{videoId}/{segment}', [StreamController::class, 'hlsSegment'])
    ->name('stream.hls-segment')
    ->middleware(['prevent.hotlinking', 'throttle:streaming']);

Route::get('/hls-key/{videoId}/{token}', [StreamController::class, 'hlsKey'])
    ->name('stream.hls-key')
    ->middleware(['prevent.hotlinking', 'throttle:streaming']);

// Embed
Route::get('/embed/{hash}', [EmbedController::class, 'show'])
    ->name('embed.show');

// Public Watch Page
Route::get('/watch/{hash}', [WatchController::class, 'show'])
    ->name('watch.show');
