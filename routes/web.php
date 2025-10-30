<?php

use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExamTypeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PatientHistoryController;
use App\Http\Controllers\SampleController;
use App\Http\Controllers\SampleTypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'welcome'])->name('welcome');
Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy-policy');

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::get('/home', [HomeController::class, 'home'])->name('home');

    Route::middleware(['role:teacher'])->group(function () {
        Route::get('/user/management', [UserManagementController::class, 'index'])->name('user-management.index');
        Route::get('/user/management/create', [UserManagementController::class, 'create'])->name('user-management.create');
        Route::post('/user/management', [UserManagementController::class, 'store'])->name('user-management.store');
        Route::get('/user/management/generate-password', [UserManagementController::class, 'generatePassword'])->name('user-management.generate-password');
        Route::get('/user/management/{id}', [UserManagementController::class, 'show'])->name('user-management.show');
        Route::get('/user/management/{id}/edit', [UserManagementController::class, 'edit'])->name('user-management.edit');
        Route::put('/user/management/{id}', [UserManagementController::class, 'update'])->name('user-management.update');
        Route::delete('/user/management/{id}', [UserManagementController::class, 'destroy'])->name('user-management.destroy');
        Route::patch('/user/management/{id}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('user-management.toggle-status');

        Route::get('/exam-type', [ExamTypeController::class, 'index'])->name('exam-type.index');
        Route::get('/exam-type/create', [ExamTypeController::class, 'create'])->name('exam-type.create');
        Route::post('/exam-type', [ExamTypeController::class, 'store'])->name('exam-type.store');
        Route::get('/exam-type/{examType}', [ExamTypeController::class, 'show'])->name('exam-type.show');
        Route::get('/exam-type/{examType}/edit', [ExamTypeController::class, 'edit'])->name('exam-type.edit');
        Route::put('/exam-type/{examType}', [ExamTypeController::class, 'update'])->name('exam-type.update');
        Route::delete('/exam-type/{examType}', [ExamTypeController::class, 'destroy'])->name('exam-type.destroy');

        Route::get('/sample-type', [SampleTypeController::class, 'index'])->name('sample-type.index');
        Route::get('/sample-type/create', [SampleTypeController::class, 'create'])->name('sample-type.create');
        Route::post('/sample-type', [SampleTypeController::class, 'store'])->name('sample-type.store');
        Route::get('/sample-type/{sampleType}', [SampleTypeController::class, 'show'])->name('sample-type.show');
        Route::get('/sample-type/{sampleType}/edit', [SampleTypeController::class, 'edit'])->name('sample-type.edit');
        Route::put('/sample-type/{sampleType}', [SampleTypeController::class, 'update'])->name('sample-type.update');
        Route::delete('/sample-type/{sampleType}', [SampleTypeController::class, 'destroy'])->name('sample-type.destroy');
    });

    Route::get('/user/{user}', [UserController::class, 'edit'])->name('user.edit');
    Route::get('/user/password/{user}', [UserController::class, 'editPassword'])->name('user.password-edit');

    Route::middleware(['role:teacher,student'])->group(function () {
        Route::get('/patient-histories', [PatientHistoryController::class, 'index'])->name('patient-histories.index');
        Route::get('/patient-histories/create', [PatientHistoryController::class, 'create'])->name('patient-histories.create');
        Route::post('/patient-histories', [PatientHistoryController::class, 'store'])->name('patient-histories.store');
        Route::get('/patient-histories/{id}', [PatientHistoryController::class, 'show'])->name('patient-histories.show');
        Route::get('/patient-histories/{id}/edit', [PatientHistoryController::class, 'edit'])->name('patient-histories.edit');
        Route::put('/patient-histories/{id}', [PatientHistoryController::class, 'update'])->name('patient-histories.update');
        Route::delete('/patient-histories/{id}', [PatientHistoryController::class, 'destroy'])->name('patient-histories.destroy');

        Route::get('/samples', [SampleController::class, 'index'])->name('samples.index');
        Route::get('/samples/create', [SampleController::class, 'create'])->name('samples.create');
        Route::post('/samples', [SampleController::class, 'store'])->name('samples.store');
        Route::get('/samples/{id}', [SampleController::class, 'show'])->name('samples.show');
        Route::get('/samples/{id}/edit', [SampleController::class, 'edit'])->name('samples.edit');
        Route::put('/samples/{id}', [SampleController::class, 'update'])->name('samples.update');
        Route::delete('/samples/{id}', [SampleController::class, 'destroy'])->name('samples.destroy');

        Route::get('/exam', [ExamController::class, 'index'])->name('exam.index');
        Route::get('/exam/create', [ExamController::class, 'create'])->name('exam.create');
        Route::post('/exam', [ExamController::class, 'store'])->name('exam.store');
        Route::get('/exam/{id}', [ExamController::class, 'show'])->name('exam.show');
        Route::get('/exam/{id}/edit', [ExamController::class, 'edit'])->name('exam.edit');
        Route::put('/exam/{id}', [ExamController::class, 'update'])->name('exam.update');
        Route::delete('/exam/{id}', [ExamController::class, 'destroy'])->name('exam.destroy');
    });
});
