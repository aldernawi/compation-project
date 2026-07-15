<?php

use App\Http\Controllers\Admin\CompetitionTypeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class)->except(['show']);
    Route::patch('users/{user}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
    Route::patch('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');

    Route::resource('competition-types', CompetitionTypeController::class)->except(['show']);
});
