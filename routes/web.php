<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\ExamController;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Set welcome-dashboard as the main dashboard
    Route::get('/dashboard', function () {
        return view('admin/welcome-dashboard');
    })->name('dashboard');

    // Route for admin dashboard
    Route::get('admin-dashboard', function () {
        return view('admin/admin-dashboard');
    })->name('admin.dashboard');

    // Route for teacher dashboard
    Route::get('teacher-dashboard', function () {
        return view('admin/teacher-dashboard');
    })->name('teacher.dashboard');

    // Route for student dashboard
    Route::get('student-dashboard', function () {
        return view('admin/student-dashboard');
    })->name('student.dashboard');
});


// Route for Academicians Dashboard
Route::get('SEMS-dashboard', function () {
    $user = Auth::user();
    return view('SEMS.SEMS-dashboard', ['role_id' => $user->role_id]);
})->middleware(['auth'])->name('SEMS.dashboard');

// Route for Course Coordinator Page
Route::get('create-question', function () {
    return view('SEMS.create-question');
})->name('create.question');

// Route for Course Coordinator Page
Route::get('vetters-page', function () {
    return view('SEMS.vetters-page');
})->name('vetters.page');

// Route for Course Coordinator Page
Route::get('assign-vetters', function () {
    return view('SEMS.assign-vetters');
})->name('assign.vetters');

// Route for Course Coordinator Page
Route::get('approval-question', function () {
    return view('SEMS.approval-question');
})->name('approval.question');

// Route for Course Coordinator Page
Route::get('question-review', function () {
    return view('SEMS.question-review');
})->name('question.review');

// Route for Course Coordinator Page
Route::get('CC-dashboard', function () {
    return view('SEMS.CC-dashboard');
})->name('CC.dashboard');

// Route for Course Coordinator Page
Route::get('edit-question', function () {
    return view('SEMS.edit-question');
})->name('edit.question');



// SSP
// Route for Main Welcome Page
Route::get('mainSSP-welcome', function () {
    return view('StudyPlanner.mainSSP-welcome');
})->name('mainSSP.welcome');

// Route for Student Welcome Page
Route::get('SSP-welcome', function () {
    return view('StudyPlanner.SSP-welcome');
})->name('SSP.welcome');

// Route for Student Dashboard
Route::get('SSP-dashboard', function () {
    return view('StudyPlanner.SSP-dashboard');
})->name('SSP.dashboard');

// Route for Update Profile
Route::get('update-profile', function () {
    return view('StudyPlanner.update-profile');
})->name('update.profile');

// Route for View Course Suggested
Route::get('view-course', function () {
    return view('StudyPlanner.view-course');
})->name('view.course');

// Route for CGPA Calculator
Route::get('cgpa-calculator', function () {
    return view('StudyPlanner.cgpa-calculator');
})->name('cgpa.calculator');

// Route for Admin Welcome Page
Route::get('admin-welcome', function () {
    return view('StudyPlanner.admin-welcome');
})->name('admin.welcome');

// Route for Admin Dashboard
Route::get('adminSSP-dashboard', function () {
    return view('StudyPlanner.adminSSP-dashboard');
})->name('adminSSP.dashboard');

// Route for Course
Route::get('list-course', function () {
    return view('StudyPlanner.list-course');
})->name('list.course');

// Route for Study Plan
Route::get('add-studyplan', function () {
    return view('StudyPlanner.add-studyplan');
})->name('add.studyplan');

// Route for Add Course
Route::get('add-course', function () {
    return view('StudyPlanner.add-course');
})->name('add.course');

Route::post('/generate-pdf', [PDFController::class, 'generate'])->name('pdf.generate');
Route::post('/store-exam', [ExamController::class, 'store'])->name('exam.store');







