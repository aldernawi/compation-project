<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompetitionController;
use App\Http\Controllers\Api\SubmissionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/competitions', [CompetitionController::class, 'index']);
Route::get('/competitions/{competition}', [CompetitionController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/my/submissions', [SubmissionController::class, 'myIndex']);
    Route::post('/competitions/{competition}/submissions', [SubmissionController::class, 'store']);
    Route::put('/submissions/{submission}', [SubmissionController::class, 'update']);
});
