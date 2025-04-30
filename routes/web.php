<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\SemesterController;
use App\Models\Semester;
use App\Models\Exam;

/*
|--------------------------------------------------------------------------
| Public Route
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('auth.login');
});

/*
|--------------------------------------------------------------------------
| SEMS Routes (Smart Examination Management System)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | SEMS Dashboard (Super Admin Only)
    |--------------------------------------------------------------------------
    */
    Route::get('/SEMS-dashboard', function () {
        return view('SEMS.SEMS-dashboard');
    })->name('SEMS.dashboard');


    /*
    |--------------------------------------------------------------------------
    | Exam Management
    |--------------------------------------------------------------------------
    */
    Route::controller(ExamController::class)->group(function () {

        // Create Exam Slot (HOD + Admin only)
        Route::get('/create-exam', 'showCreateExamForm')->name('exam.create');
        Route::post('/store-exam', 'store')->name('exam.store');

        // Create and Submit Questions (CC only)
        Route::get('/create-question', 'showCreateQuestionForm')->name('create.question');
        Route::post('/submit-question', 'submitQuestion')->name('exam.submit-question');

        // Assign Coordinator and Vetter (HOD only)
        Route::get('/assign-role', 'showAssignRoleForm')->name('assign.role.form');
        Route::post('/assign-role', 'assignRole')->name('assign.role');
        Route::post('/assign-vetter', 'assignVetter')->name('assign.vetter');

        // HOD Dashboard
        Route::get('/HOD-dashboard', 'hodDashboard')->name('HOD.dashboard');

        // CC Dashboard
        Route::get('/CC-dashboard', 'ccDashboard')->name('CC.dashboard');

        // Vetter Dashboard
        Route::get('/vetters-dashboard', 'vetterDashboard')->name('vetters.dashboard');

        // Approval (HOD only)
        Route::get('/approval-question', 'showApprovalQuestion')->name('approval.question');
        Route::post('/exam/approve', 'approveExam')->name('exam.approve');
        Route::post('/exam-deny', 'denyQuestion')->name('exam.deny');

        Route::get('/view-question', [ExamController::class, 'viewQuestion'])->name('view.question');


        // Vetter Review Section
        Route::prefix('vetter')->group(function () {
            Route::get('/question-review', 'vetterReviewPage')->name('question.review');
            Route::post('/question-review', 'submitVetterReview')->name('question.review.submit');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Course Management (Super Admin + HOD)
    |--------------------------------------------------------------------------
    */
    Route::controller(CourseController::class)->prefix('courses')->group(function () {
        Route::get('/', 'index')->name('courses.index');
        Route::get('/create', 'create')->name('courses.create');
        Route::post('/', 'store')->name('courses.store');
    });

    /*
    |--------------------------------------------------------------------------
    | Semester Management (Super Admin Only)
    |--------------------------------------------------------------------------
    */
    Route::controller(SemesterController::class)->prefix('semesters')->group(function () {
        Route::get('/', 'index')->name('semesters.index');
        Route::post('/', 'store')->name('semesters.store');
        Route::patch('/{semester}/activate', 'activate')->name('semesters.activate');
    });
});

/*
|--------------------------------------------------------------------------
| PDF Generation (Available to Authenticated Users)
|--------------------------------------------------------------------------
*/
Route::post('/generate-pdf', [PDFController::class, 'generate'])->middleware('auth')->name('pdf.generate');

/*
|--------------------------------------------------------------------------
| Jetstream/Breeze Default Dashboards (Admin System)
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::view('/dashboard', 'admin/welcome-dashboard')->name('dashboard');
    Route::view('/admin-dashboard', 'admin/admin-dashboard')->name('admin.dashboard');
    Route::view('/teacher-dashboard', 'admin/teacher-dashboard')->name('teacher.dashboard');
    Route::view('/student-dashboard', 'admin/student-dashboard')->name('student.dashboard');
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


