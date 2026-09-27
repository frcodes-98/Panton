<?php

use App\Http\Controllers\DesktopController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'show'])->name('landing');
Route::post('/login', [LandingController::class, 'login'])->name('login');
Route::post('/logout', [LandingController::class, 'logout'])->name('logout');

Route::middleware('demo.auth')->group(function () {
    Route::get('/desktop', [DesktopController::class, 'index'])->name('desktop');
    Route::get('/desktop/{workspace}', [DesktopController::class, 'placeholder'])->name('workspace.placeholder');

    Route::prefix('system')->name('system.')->group(function () {
        Route::get('/', [SystemController::class, 'welcome'])->name('welcome');
        Route::get('/users', [SystemController::class, 'users'])->name('users.index');
        Route::get('/users/create', [SystemController::class, 'createUser'])->name('users.create');
        Route::get('/users/{user}/edit', [SystemController::class, 'editUser'])->name('users.edit');
        Route::get('/groups', [SystemController::class, 'groups'])->name('groups');
        Route::get('/workspaces', [SystemController::class, 'workspaces'])->name('workspaces.index');
        Route::get('/workspaces/create', [SystemController::class, 'createWorkspace'])->name('workspaces.create');
        Route::get('/workspaces/{workspace}/edit', [SystemController::class, 'editWorkspace'])->name('workspaces.edit');
        Route::get('/modules', [SystemController::class, 'modules'])->name('modules');
        Route::get('/widgets', [SystemController::class, 'widgets'])->name('widgets.index');
        Route::get('/widgets/create', [SystemController::class, 'createWidget'])->name('widgets.create');
        Route::get('/widgets/{widget}/edit', [SystemController::class, 'editWidget'])->name('widgets.edit');
        Route::get('/settings', [SystemController::class, 'settings'])->name('settings');
        Route::get('/marquee', [SystemController::class, 'marquee'])->name('marquee');
    });
});
