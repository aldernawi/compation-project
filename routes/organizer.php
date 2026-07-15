<?php

use App\Http\Controllers\Organizer\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:organizer'])->prefix('organizer')->name('organizer.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});
