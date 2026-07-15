<?php

use App\Http\Controllers\Judge\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:judge'])->prefix('judge')->name('judge.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});
