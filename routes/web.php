<?php

use App\Http\Controllers\ClassController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\TeacherController;
use App\Models\User;
use App\Http\Controllers\StatistikController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\YearController;
use App\Models\ClassRoom;
use App\Models\EvaluationItem;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Year;
use App\Http\Controllers\EvaluationItemController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    $teacherClasses = $user->teacherClasses()->with('classRoom')
        ->get()
        ->map(function($teacherClass) {
            $classRoom = $teacherClass->classRoom;
            return [
                'id' => $classRoom->id,
                'name' => $classRoom->name,
                'year_id' => $classRoom->year_id,
                'students_count' => $classRoom->students()->count(),
            ];
        });

    return Inertia::render('Dashboard', [
        'teacherClasses' => $teacherClasses
    ]);
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
    $class = ClassRoom::findOrFail($classId);
    $students = Student::where('class_id', $classId)
        ->select('id', 'name')
        ->get()
        ->map(function ($student) {
            return [
                'id' => $student->id,
                'name' => $student->name,
                'tarikh' => now()->format('Y-m-d'),
                'tahap' => 1
            ];
        });

    return Inertia::render('StudentList', [
        'year' => $class->year_id,
        'classId' => $class->id,
        'className' => $class->name,
        'students' => $students
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

// Settings (Tetapan) route
Route::get('/tetapan', function () {
    return Inertia::render('Tetapan', [
        'user' => Auth::user()
    ]);
})->middleware(['auth', 'verified'])->name('tetapan');// Settings route
Route::get('/settings', function () {
    return Inertia::render('Settings', [
        'user' => Auth::user()
    ]);
})->middleware(['auth', 'verified'])->name('settings');// Existing settings route
Route::get('/settings', function () {
    return Inertia::render('Settings', [
        'user' => Auth::user()
    ]);
})->middleware(['auth', 'verified'])->name('settings');


Route::get('/kemaskini/tahun/{year}/add-student', function ($year) {
    return Inertia::render('AddStudent', ['year' => $year]);
})->name('add.student');

Route::middleware(['auth'])->group(function () {
    Route::get('/guru', function () {
        return Inertia::render('Guru', [
            'teachers' => Teacher::with('classes')->get(),
            'availableClasses' => ClassRoom::where(function ($query) {
                $query->whereDoesntHave('teachers')
                    ->orWhereHas('teachers', function ($q) {
                        $q->where('teachers.id', request()->input('editing_teacher_id'));
                    });
            })->get(),
            'unassignedClasses' => ClassRoom::whereDoesntHave('teachers')->get()
        ]);
    })->name('guru');
});

Route::middleware(['auth'])->group(function () {
    Route::middleware(['auth'])->group(function () {
        Route::get('/statistik', function () {
            return Inertia::render('Statistik', [
                'years' => Year::orderBy('id')->get(),
                'classes' => ClassRoom::with('year')->get()
            ]);
        })->name('statistik');
    });
});

Route::post('/years', [YearController::class, 'store'])->name('years.store');
Route::get('/kemaskini', [YearController::class, 'index'])->name('years.index');
Route::get('/kemaskini/tahun/{year}', [ClassController::class, 'index'])->name('classes.index');
Route::post('/classes', [ClassController::class, 'store'])->name('classes.store');
Route::post('/students', [StudentController::class, 'store'])->name('students.store');
Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');
Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');

Route::post('logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');


Route::middleware('auth')->group(function () {
    Route::get('/objek-penilaian', [EvaluationItemController::class, 'index'])->name('evaluation.index');
    Route::post('/objek-penilaian/import', [EvaluationItemController::class, 'import'])->name('evaluation.import');
    Route::post('/objek-penilaian', [EvaluationItemController::class, 'store'])->name('evaluation.store');
    Route::delete('/objek-penilaian/{id}', [EvaluationItemController::class, 'destroy'])->name('evaluation.destroy');
});

Route::get('/kemaskini/tahun/{year}/student/{studentId}/semak', function ($year, $studentId) {
    // Fetch student data with relationships
    $student = Student::with(['class', 'class.year'])
        ->findOrFail($studentId);

    return Inertia::render('SemakPage', [
        'student' => $student,
        'year' => (int)$year
    ]);
})->name('student.semak');


Route::get('/stats/{year}/{class}', function ($year, $class) {
    // Base query for students in the selected year
    $query = Student::where('year_id', $year);

    // If a specific class is selected (not 'all'), filter by class
    if ($class !== 'all') {
        $query->where('class_name', $class);
    }

    // Get all relevant students
    $students = $query->get();

    // Initialize categories (assuming you have these categories)
    $categories = [
        'Akademik' => [
            'passed' => 0,
            'not_passed' => 0
        ],
        'Kokurikulum' => [
            'passed' => 0,
            'not_passed' => 0
        ],
        'Sahsiah' => [
            'passed' => 0,
            'not_passed' => 0
        ]
    ];

    // Count students for each category
    foreach ($students as $student) {
        foreach ($categories as $category => &$stats) {
            // Example logic - adjust according to your actual data structure
            $isPassed = $student->{"is_{$category}_passed"} ?? false;
            if ($isPassed) {
                $stats['passed']++;
            } else {
                $stats['not_passed']++;
            }
        }
    }

    // Format response data
    $responseData = [];
    foreach ($categories as $category => $stats) {
        $responseData[] = [
            'category' => $category,
            'passed' => $stats['passed'],
            'not_passed' => $stats['not_passed']
        ];
    }

    return response()->json([
        'total' => $students->count(),
        'data' => $responseData
    ]);
})->name('stats.fetch');

require __DIR__ . '/auth.php';
