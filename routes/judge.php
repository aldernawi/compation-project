<?php

use App\Http\Controllers\Judge\CompetitionController;
use App\Http\Controllers\Judge\DashboardController;
use App\Http\Controllers\Judge\SubmissionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:judge'])->prefix('judge')->name('judge.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('competitions', [CompetitionController::class, 'index'])->name('competitions.index');

    Route::get('competitions/{competition}/submissions', [SubmissionController::class, 'index'])
        ->name('competitions.submissions.index');
    Route::get('submissions/{submission}/evaluate', [SubmissionController::class, 'evaluate'])->name('submissions.evaluate');
    Route::post('submissions/{submission}/evaluate', [SubmissionController::class, 'storeEvaluation'])
        ->name('submissions.evaluate.store');
});
