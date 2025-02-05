<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherDashboardController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/teacher/dashboard-stats', [TeacherDashboardController::class, 'getStats']);
});
