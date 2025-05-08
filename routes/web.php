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
| SEMS Protected Routes (Requires Auth)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | SEMS Main Dashboard (Super Admin Only)
    |--------------------------------------------------------------------------
    */
    Route::get('/SEMS-dashboard', function () {
        return view('SEMS.SEMS-dashboard');
    })->name('SEMS.dashboard');

    /*
    |--------------------------------------------------------------------------
    | ExamController - Exam Lifecycle (All Roles As Needed)
    |--------------------------------------------------------------------------
    */
    Route::controller(ExamController::class)->group(function () {

        // Exam Creation (HOD)
        Route::get('/create-exam', 'showCreateExamForm')->name('exam.create');
        Route::post('/store-exam', 'store')->name('exam.store');

        // Question Management (CC)
        Route::get('/create-question', 'showCreateQuestionForm')->name('create.question');
        Route::post('/submit-question', 'submitQuestion')->name('exam.submit-question');

        // Assignments (HOD)
        Route::get('/assign-role', 'showAssignRoleForm')->name('assign.role.form');
        Route::post('/assign-role', 'assignRole')->name('assign.role');
        Route::post('/assign-vetter', 'assignVetter')->name('assign.vetter');

        // Dashboards
        Route::get('/HOD-dashboard', 'hodDashboard')->name('HOD.dashboard');
        Route::get('/CC-dashboard', 'ccDashboard')->name('CC.dashboard');
        Route::get('/vetters-dashboard', 'vetterDashboard')->name('vetters.dashboard');
        Route::get('/general-office-dashboard', 'generalOfficeDashboard')->name('generalOffice.dashboard');

        // Approval (HOD)
        Route::get('/approval-question', 'showApprovalQuestion')->name('approval.question');
        Route::post('/exam/approve', 'approveExam')->name('exam.approve');
        Route::post('/exam-deny', 'denyQuestion')->name('exam.deny');

        // View-only mode (All Roles)
        Route::get('/view-question', 'viewQuestion')->name('view.question');

        // Vetter Review
        Route::prefix('vetter')->group(function () {
            Route::get('/question-review', 'vetterReviewPage')->name('question.review');
            Route::post('/question-review', 'submitVetterReview')->name('question.review.submit');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | CourseController - Course Management (Super Admin + HOD)
    |--------------------------------------------------------------------------
    */
    Route::resource('courses', CourseController::class);

    /*
    |--------------------------------------------------------------------------
    | SemesterController - Semester Lifecycle (Super Admin Only)
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
| PDF Generator (Authenticated Users Only)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/general-office', [ExamController::class, 'generalOfficeDashboard'])
        ->name('general.office')
        ->middleware('role:7,1');
});






Route::get('/pdf/download/{id}', [PDFController::class, 'download'])->name('pdf.download');


Route::post('/generate-pdf', [PDFController::class, 'generate'])
    ->middleware('auth')
    ->name('pdf.generate');

/*
|--------------------------------------------------------------------------
| Jetstream/Breeze Dashboards
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


