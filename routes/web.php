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
use App\Http\Controllers\StatsController;
use App\Http\Controllers\StudentEvaluationController;
use App\Models\Category;
use App\Models\StudentEvaluation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $teacherClasses = Auth::user()->classes()
        ->with('students') // Now this relationship will work
        ->get()
        ->map(function($class) {
            return [
                'id' => $class->id,
                'name' => $class->name,
                'year_id' => $class->year_id,
                'students_count' => $class->students->count()
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
    try {
        $student = Student::with('class.year')->findOrFail($studentId);

        $categories = Category::with(['evaluationItems' => function ($query) {
            $query->orderBy('sequence');
        }])->where('year_id', $year)->get();

        $existingEvaluations = StudentEvaluation::where('student_id', $studentId)
            ->get()
            ->keyBy('evaluation_item_id')
            ->map(function ($evaluation) {
                return $evaluation->status;
            });

        return Inertia::render('SemakPage', [
            'student' => $student,
            'categories' => $categories,
            'existingEvaluations' => $existingEvaluations
        ]);
    } catch (\Exception $e) {
        Log::error('Error loading semak page: ' . $e->getMessage());
        return redirect()->back()->withErrors(['error' => 'Failed to load data']);
    }
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
    Route::get('/guru', function () {
        return Inertia::render('Guru', [
            'teachers' => User::with('classes')->get(),
            'availableClasses' => ClassRoom::where(function ($query) {
                $query->whereDoesntHave('users', function ($q) {
                    $q->where('users.id', '!=', request()->input('editing_teacher_id'));
                })
                    ->orWhereHas('users', function ($q) {
                        $q->where('users.id', request()->input('editing_teacher_id'));
                    });
            })->get()
        ]);
    })->name('guru');
});

Route::middleware(['auth'])->group(function () {
    Route::middleware(['auth'])->group(function () {
        Route::get('/statistik', function () {
            return Inertia::render('Statistik', [
                'years' => Year::orderBy('name')->get(),
                'classes' => ClassRoom::with('year')
                    ->orderBy('name')
                    ->get()
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
Route::delete('/api/classes/{id}', [ClassController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('classes.destroy');

    Route::post('/api/evaluation-objects/import', [EvaluationItemController::class, 'import'])
    ->middleware(['auth'])
    ->name('evaluation-objects.import');

Route::post('logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/tetapan', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])
    ->name('profile.avatar.update')
    ->middleware(['auth', 'verified']);
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])
    ->name('profile.avatar.update')
    ->middleware('auth');
});


Route::middleware('auth')->group(function () {
    Route::get('/objek-penilaian', function (Request $request) {
        return Inertia::render('EvaluationObject', [
            'years' => Year::orderBy('id')->get(),
            'evaluationData' => Category::with('evaluationItems')
                ->where('year_id', $request->input('year', 1))
                ->get(),
            'existingCategories' => Category::where('year_id', $request->input('year', 1))
                ->select('id', 'name')
                ->get()
        ]);
    })->name('evaluation.index');
    Route::post('/objek-penilaian/import', [EvaluationItemController::class, 'import'])->name('evaluation.import');
    Route::post('/evaluation/store', [StudentEvaluationController::class, 'store'])->name('evaluation.store');
    Route::delete('/objek-penilaian/{id}', [EvaluationItemController::class, 'destroy'])->name('evaluation.destroy');
    Route::get('/stats/fetch', [StatsController::class, 'fetch'])
        ->name('stats.fetch');
});

Route::post('/evaluation', [EvaluationItemController::class, 'store'])->name('evaluation.store');
Route::delete('/evaluation-items/{item}', [EvaluationItemController::class, 'destroy'])->name('evaluation.destroy');
Route::delete('/class/{classId}/clear', [StudentController::class, 'clearClass'])->name('class.clear');

Route::put('/users/{user}/classes', function (User $user, Request $request) {
    $user->classes()->sync($request->classes);
    return redirect()->back();
})->name('users.update.classes');

Route::post('/evaluation/import', [EvaluationItemController::class, 'importCsv'])
    ->name('evaluation.import')
    ->middleware(['auth']);

    Route::get('/test-cloudinary', function() {
        try {
            $cloudinary = new \App\Services\CloudinaryService();
            dd([
                'cloud_name' => config('services.cloudinary.cloud_name'),
                'api_key_exists' => !empty(config('services.cloudinary.api_key')),
                'api_secret_exists' => !empty(config('services.cloudinary.api_secret')),
                'cloudinary_initialized' => $cloudinary !== null
            ]);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    });

require __DIR__ . '/auth.php';
