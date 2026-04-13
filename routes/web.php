<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Viewer\DashboardController;
use App\Http\Controllers\Viewer\EducationController;
use App\Http\Controllers\Viewer\PredictionController;

/*
|--------------------------------------------------------------------------
| Viewer / Public Routes (no authentication required)
|--------------------------------------------------------------------------
*/
Route::get('/', [DashboardController::class, 'index'])->name('viewer.dashboard');
Route::get('/education', [EducationController::class, 'index'])->name('viewer.education');
Route::get('/prediction', [PredictionController::class, 'index'])->name('viewer.prediction');
