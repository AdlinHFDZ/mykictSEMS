<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\SemesterController;

/*
|--------------------------------------------------------------------------
| Public Route
|--------------------------------------------------------------------------
*/
// Shows your welcome portal at the root URL
Route::get('/', function () {
    return view('welcome'); // Loads resources/views/welcome.blade.php
});

// Shows the login page at /login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

/*
|--------------------------------------------------------------------------
| SEMS Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // SEMS Dashboard (Super Admin)
    Route::get('/SEMS-dashboard', fn() => view('SEMS.SEMS-dashboard'))->name('SEMS.dashboard');

    /*
|--------------------------------------------------------------------------
| AI ROUTING
|--------------------------------------------------------------------------
*/

Route::post('/ask-ai', [App\Http\Controllers\AIController::class, 'ask']);
Route::post('/exam/check-similarity', [ExamController::class, 'checkSimilarity'])->name('exam.check-similarity');

    /*
    |--------------------------------------------------------------------------
    | Exam Lifecycle (ExamController)
    |--------------------------------------------------------------------------
    */
    Route::controller(ExamController::class)->group(function () {
        // Create Exam (HOD)
        Route::get('/create-exam', 'showCreateExamForm')->name('exam.create');
        Route::post('/store-exam', 'store')->name('exam.store');

        // Create / Submit Questions (CC)
        Route::get('/create-question', 'showCreateQuestionForm')->name('create.question');
        Route::post('/submit-question', 'submitQuestion')->name('exam.submit-question');

        // Role Assignment (HOD)
        Route::get('/assign-role', 'showAssignRoleForm')->name('assign.role.form');
        Route::post('/assign-role', 'assignRole')->name('assign.role');
        Route::post('/assign-vetter', 'assignVetter')->name('assign.vetter');

        // Dashboards
        Route::get('/HOD-dashboard', 'hodDashboard')->name('HOD.dashboard');
        Route::get('/CC-dashboard', 'ccDashboard')->name('CC.dashboard');
        Route::get('/vetters-dashboard', 'vetterDashboard')->name('vetters.dashboard');
        Route::get('/general-office-dashboard', 'generalOfficeDashboard')->name('generalOffice.dashboard');

        // Approval
        Route::get('/approval-question', 'showApprovalQuestion')->name('approval.question');
        Route::post('/exam/approve', 'approveExam')->name('exam.approve');
        Route::post('/exam-deny', 'denyQuestion')->name('exam.deny');

        // View-only (All Roles)
        Route::get('/view-question', 'viewQuestion')->name('view.question');

        // Vetter Review
        Route::prefix('vetter')->group(function () {
            Route::get('/question-review', 'vetterReviewPage')->name('question.review');
            Route::post('/question-review', 'submitVetterReview')->name('question.review.submit');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Course Management
    |--------------------------------------------------------------------------
    */
    Route::resource('courses', CourseController::class);

    /*
    |--------------------------------------------------------------------------
    | Semester Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('semesters')->controller(SemesterController::class)->group(function () {
        Route::get('/', 'index')->name('semesters.index');
        Route::post('/', 'store')->name('semesters.store');
        Route::patch('/{semester}/activate', 'activate')->name('semesters.activate');
    });

    /*
    |--------------------------------------------------------------------------
    | PDF (View / Download)
    |--------------------------------------------------------------------------
    */
    Route::get('/pdf/view/{id}', [PDFController::class, 'view'])->name('pdf.view');
    Route::get('/pdf/download/{id}', [PDFController::class, 'download'])->name('pdf.download');
    Route::post('/generate-pdf', [PDFController::class, 'generate'])->name('pdf.generate');

});

/*
|--------------------------------------------------------------------------
| Jetstream/Breeze Dashboards (Optional Admin System)
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


