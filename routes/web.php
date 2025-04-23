<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\PDFController;
use App\Models\Exam;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('auth.login');
});

/*
|--------------------------------------------------------------------------
| SEMS Routes (Smart Exam Management System)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Super Admin dashboard with role-based switcher
    Route::get('/SEMS-dashboard', function () {
        $user = Auth::user();
        $exams = Exam::all(); // show all exams for Super Admin
        return view('SEMS.SEMS-dashboard', [
            'role_id' => $user->role_id,
            'exams' => $exams
        ]);
    })->name('SEMS.dashboard');

    // Create Exam Slot
    Route::get('/create-exam', function () {
        return view('SEMS.create-exam');
    })->name('exam.create');

    Route::post('/store-exam', [ExamController::class, 'store'])->name('exam.store');

    // Assign CC or Vetter
    Route::get('/assign-role', [ExamController::class, 'showAssignRoleForm'])->name('assign.role.form');
    Route::post('/assign-role', [ExamController::class, 'assignRole'])->name('assign.role');
    Route::post('/assign-vetter', [ExamController::class, 'assignVetter'])->name('assign.vetter');

    // HOD Dashboard
    Route::get('/HOD-dashboard', [ExamController::class, 'hodDashboard'])->name('HOD.dashboard');

    // CC Dashboard
    Route::get('/CC-dashboard', [ExamController::class, 'ccDashboard'])->name('CC.dashboard');

    Route::post('/submit-question', [ExamController::class, 'submitQuestion'])->name('exam.submit-question');



    // Vetter Dashboard
    Route::get('/vetters-dashboard', function () {
        return view('SEMS.vetters-page');
    })->name('vetters.dashboard');

    // Other SEMS pages (optional)
    Route::get('/create-question', [ExamController::class, 'showCreateQuestionForm'])->name('create.question');


    Route::get('/approval-question', function () {
        return view('SEMS.approval-question');
    })->name('approval.question');

    Route::get('/question-review', function () {
        return view('SEMS.question-review');
    })->name('question.review');

    Route::get('/edit-question', function () {
        return view('SEMS.edit-question');
    })->name('edit.question');
});

/*
|--------------------------------------------------------------------------
| PDF Generation
|--------------------------------------------------------------------------
*/

Route::post('/generate-pdf', [PDFController::class, 'generate'])->name('pdf.generate');

/*
|--------------------------------------------------------------------------
| Default Authenticated Dashboards (Jetstream / Breeze)
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        return view('admin/welcome-dashboard');
    })->name('dashboard');

    Route::get('/admin-dashboard', function () {
        return view('admin/admin-dashboard');
    })->name('admin.dashboard');

    Route::get('/teacher-dashboard', function () {
        return view('admin/teacher-dashboard');
    })->name('teacher.dashboard');

    Route::get('/student-dashboard', function () {
        return view('admin/student-dashboard');
    })->name('student.dashboard');
});


/*
|--------------------------------------------------------------------------
| SSP Routes (Study Planner)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Student view
    Route::get('/SSP-dashboard', function () {
        return view('StudyPlanner.SSP-dashboard');
    })->name('SSP.dashboard');

    Route::get('/update-profile', function () {
        return view('StudyPlanner.update-profile');
    })->name('update.profile');

    Route::get('/view-course', function () {
        return view('StudyPlanner.view-course');
    })->name('view.course');

    Route::get('/cgpa-calculator', function () {
        return view('StudyPlanner.cgpa-calculator');
    })->name('cgpa.calculator');

    // Admin view
    Route::get('/adminSSP-dashboard', function () {
        return view('StudyPlanner.adminSSP-dashboard');
    })->name('adminSSP.dashboard');

    Route::get('/admin-welcome', function () {
        return view('StudyPlanner.admin-welcome');
    })->name('admin.welcome');

    Route::get('/list-course', function () {
        return view('StudyPlanner.list-course');
    })->name('list.course');

    Route::get('/add-studyplan', function () {
        return view('StudyPlanner.add-studyplan');
    })->name('add.studyplan');

    Route::get('/add-course', function () {
        return view('StudyPlanner.add-course');
    })->name('add.course');

    Route::get('/SSP-welcome', function () {
        return view('StudyPlanner.SSP-welcome');
    })->name('SSP.welcome');

    Route::get('/mainSSP-welcome', function () {
        return view('StudyPlanner.mainSSP-welcome');
    })->name('mainSSP.welcome');
});


