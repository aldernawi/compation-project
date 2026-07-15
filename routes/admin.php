<?php

use App\Http\Controllers\Admin\CompetitionController;
use App\Http\Controllers\Admin\CompetitionTypeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PrizeController;
use App\Http\Controllers\Admin\ResultsController;
use App\Http\Controllers\Admin\SubmissionController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class)->except(['show']);
    Route::patch('users/{user}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
    Route::patch('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');

    Route::resource('competition-types', CompetitionTypeController::class)->except(['show']);

    Route::resource('competitions', CompetitionController::class)->except(['show']);
    Route::resource('competitions.prizes', PrizeController::class)->except(['show'])->scoped();

    Route::resource('submissions', SubmissionController::class)->only(['index', 'destroy']);
    Route::patch('submissions/{submission}/accept', [SubmissionController::class, 'accept'])->name('submissions.accept');
    Route::patch('submissions/{submission}/reject', [SubmissionController::class, 'reject'])->name('submissions.reject');

    Route::get('competitions/{competition}/results', [ResultsController::class, 'show'])->name('competitions.results.show');
    Route::patch('competitions/{competition}/results/submissions/{submission}', [ResultsController::class, 'assignPrize'])
        ->name('competitions.results.assign-prize');
    Route::post('competitions/{competition}/results/publish', [ResultsController::class, 'publish'])
        ->name('competitions.results.publish');
});
