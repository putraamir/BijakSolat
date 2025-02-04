<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\TeacherController;
use App\Models\User;
use App\Http\Controllers\StatistikController;

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
    Route::get('/login-page', function () {
        return Inertia::render('Login', [
            'canResetPassword' => true,
            'status' => session('status'),
        ]);
    })->name('login.page');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/kemaskini', function () {
        return Inertia::render('UpdateClass');
    })->name('kemaskini');

    Route::get('/forgot-password', function () {
        return Inertia::render('ForgotPassword');
    })->name('forgot.password');
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

Route::get('/kemaskini/tahun/{year}/class/{classId}', function ($year, $classId) {
    // In a real application, you would fetch the actual data from your database
    $className = $classId == 1 ? '1 Cemerlang' : '1 Gemilang';

    return Inertia::render('StudentList', [
        'year' => $year,
        'classId' => $classId,
        'className' => $className,
        'students' => [
            ['id' => 1, 'name' => 'Ahmad Bin Abdullah', 'tarikh' => '2024-01-12', 'tahap' => 1],
            ['id' => 2, 'name' => 'Sarah Binti Omar', 'tarikh' => '2024-01-12', 'tahap' => 1],
            // Add more sample data as needed
        ]
    ]);
})->name('class.students');

Route::get('/kemaskini/tahun/{year}/student/{studentId}/semak', function ($year, $studentId) {
    // Fetch student data from your database
    $student = [
        'id' => $studentId,
        'name' => 'Student Name',
        // Add other student details
    ];

    return Inertia::render('SemakPage', [
        'student' => $student,
        'year' => (int)$year
    ]);
})->name('student.semak');

Route::post('/submit-evaluation', function () {
    // Handle evaluation submission
    // Store the scores in your database
    return redirect()->back();
})->name('submit.evaluation');



Route::get('/kemaskini/tahun/{year}/add-student', function ($year) {
    return Inertia::render('AddStudent', ['year' => $year]);
})->name('add.student');

Route::middleware(['auth'])->group(function () {
    // Show all teachers
    Route::get('/guru', function () {
        return Inertia::render('Guru', [
            'teachers' => User::with('teacherClasses')->get()
        ]);
    })->name('guru');

    // Update teacher classes
    Route::post('/guru/update-classes', [TeacherController::class, 'updateClasses'])
        ->name('guru.updateClasses');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/statistik', [StatistikController::class, 'index'])->name('statistik');
});

require __DIR__ . '/auth.php';
