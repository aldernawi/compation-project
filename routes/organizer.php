<?php

use App\Http\Controllers\Organizer\CompetitionController;
use App\Http\Controllers\Organizer\DashboardController;
use App\Http\Controllers\Organizer\SubmissionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:organizer'])->prefix('organizer')->name('organizer.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('competitions', CompetitionController::class)->except(['show', 'destroy']);

    Route::get('competitions/{competition}/submissions', [SubmissionController::class, 'index'])
        ->name('competitions.submissions.index');
    Route::patch('competitions/{competition}/submissions/{submission}/accept', [SubmissionController::class, 'accept'])
        ->name('competitions.submissions.accept');
    Route::patch('competitions/{competition}/submissions/{submission}/reject', [SubmissionController::class, 'reject'])
        ->name('competitions.submissions.reject');
});
