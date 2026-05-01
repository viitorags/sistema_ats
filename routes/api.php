<?php

use App\Http\Controllers\InterviewController;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VacancieController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('vacancies', VacancieController::class);
    Route::apiResource('interviews', InterviewController::class);
    Route::apiResource('resumes', ResumeController::class);
    Route::apiResource('users', UserController::class);
});
