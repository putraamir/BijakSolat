<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/kemaskini', function () {
        return Inertia::render('UpdateClass');
    })->name('kemaskini');
});

Route::get('/kemaskini/tahun/{year}', function ($year) {
    return Inertia::render('ClassPage', [
        'year' => $year,
        'classes' => [
            ['id' => 1, 'name' => '1 Cemerlang', 'students' => '20 Pelajar'],
            ['id' => 2, 'name' => '1 Gemilang', 'students' => '23 Pelajar']
        ]
    ]);


})->name('kemaskini.tahun');

Route::get('/kemaskini/tahun/{year}/add-student', function ($year) {
    return Inertia::render('AddStudent', ['year' => $year]);
})->name('add.student');

require __DIR__ . '/auth.php';
