<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\CalibrationController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExamTypeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\PatientExamController;
use App\Http\Controllers\PatientHistoryController;
use App\Http\Controllers\SampleController;
use App\Http\Controllers\SampleTypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'welcome'])->name('welcome');
Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy-policy');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/home', [HomeController::class, 'home'])->name('home');

    // GRUPO: Apenas Professores (role: teacher)
    Route::middleware(['role:teacher'])->group(function () {
        // Gerenciamento de Usuários
        Route::get('/user/management', [UserManagementController::class, 'index'])->name('user-management.index');
        Route::get('/user/management/create', [UserManagementController::class, 'create'])->name('user-management.create');
        Route::post('/user/management', [UserManagementController::class, 'store'])->name('user-management.store');
        Route::get('/user/management/{id}', [UserManagementController::class, 'show'])->name('user-management.show');
        Route::get('/user/management/{id}/edit', [UserManagementController::class, 'edit'])->name('user-management.edit');
        Route::put('/user/management/{id}', [UserManagementController::class, 'update'])->name('user-management.update');
        Route::delete('/user/management/{id}', [UserManagementController::class, 'destroy'])->name('user-management.destroy');
        Route::patch('/user/management/{id}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('user-management.toggle-status');

        // Tipos de Exame
        Route::get('/exam-type', [ExamTypeController::class, 'index'])->name('exam-type.index');
        Route::get('/exam-type/create', [ExamTypeController::class, 'create'])->name('exam-type.create');
        Route::post('/exam-type', [ExamTypeController::class, 'store'])->name('exam-type.store');
        Route::get('/exam-type/{examType}', [ExamTypeController::class, 'show'])->name('exam-type.show');
        Route::get('/exam-type/{examType}/edit', [ExamTypeController::class, 'edit'])->name('exam-type.edit');
        Route::put('/exam-type/{examType}', [ExamTypeController::class, 'update'])->name('exam-type.update');
        Route::patch('/exam-type/{examType}/toggle-status', [ExamTypeController::class, 'toggleStatus'])->name('exam-type.toggle-status');
        Route::delete('/exam-type/{examType}', [ExamTypeController::class, 'destroy'])->name('exam-type.destroy');

        // Tipos de Amostra
        Route::get('/sample-type', [SampleTypeController::class, 'index'])->name('sample-type.index');
        Route::get('/sample-type/create', [SampleTypeController::class, 'create'])->name('sample-type.create');
        Route::post('/sample-type', [SampleTypeController::class, 'store'])->name('sample-type.store');
        Route::get('/sample-type/{sampleType}', [SampleTypeController::class, 'show'])->name('sample-type.show');
        Route::get('/sample-type/{sampleType}/edit', [SampleTypeController::class, 'edit'])->name('sample-type.edit');
        Route::put('/sample-type/{sampleType}', [SampleTypeController::class, 'update'])->name('sample-type.update');
        Route::patch('/sample-type/{sampleType}/toggle-status', [SampleTypeController::class, 'toggleStatus'])->name('sample-type.toggle-status');
        Route::delete('/sample-type/{sampleType}', [SampleTypeController::class, 'destroy'])->name('sample-type.destroy');

        // Gerenciamento de Máquinas
        Route::get('/machines', [MachineController::class, 'index'])->name('machines.index');
        Route::get('/machines/create', [MachineController::class, 'create'])->name('machines.create');
        Route::post('/machines', [MachineController::class, 'store'])->name('machines.store');
        Route::get('/machines/{machine}/pdf', [MachineController::class, 'exportPdf'])->name('machines.pdf');
        Route::get('/machines/{machine}', [MachineController::class, 'show'])->name('machines.show');
        Route::get('/machines/{machine}/edit', [MachineController::class, 'edit'])->name('machines.edit');
        Route::put('/machines/{machine}', [MachineController::class, 'update'])->name('machines.update');
        Route::delete('/machines/{machine}', [MachineController::class, 'destroy'])->name('machines.destroy');

        // Gerenciamento de Calibrações
        // A rota "export" precisa vir ANTES do wildcard {calibration}, senão o wildcard captura "export".
        Route::get('/calibrations', [CalibrationController::class, 'index'])->name('calibrations.index');
        Route::get('/calibrations/export', [CalibrationController::class, 'export'])->name('calibrations.export');
        Route::get('/calibrations/create/{machine}', [CalibrationController::class, 'create'])->name('calibrations.create');
        Route::post('/calibrations', [CalibrationController::class, 'store'])->name('calibrations.store');
        Route::get('/calibrations/{calibration}', [CalibrationController::class, 'show'])->name('calibrations.show');
        Route::get('/calibrations/{calibration}/edit', [CalibrationController::class, 'edit'])->name('calibrations.edit');
        Route::put('/calibrations/{calibration}', [CalibrationController::class, 'update'])->name('calibrations.update');
        Route::delete('/calibrations/{calibration}', [CalibrationController::class, 'destroy'])->name('calibrations.destroy');

        Route::resource('activity-logs', ActivityLogController::class);
    });

    // Perfil do Usuário
    Route::post('/user/anonymize', [UserController::class, 'anonymize'])->name('user.anonymize');
    Route::get('/user/password/{user}', [UserController::class, 'editPassword'])->name('user.password-edit');
    Route::get('/user/{user}', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/user/{user}/address', [UserController::class, 'updateAddress'])->name('user.address.update');

    // GRUPO: Professores e Alunos (role: teacher, student)
    Route::middleware(['role:teacher,student'])->group(function () {
        // Histórico de Pacientes
        Route::get('/patient-histories', [PatientHistoryController::class, 'index'])->name('patient-histories.index');
        Route::get('/patient-histories/create', [PatientHistoryController::class, 'create'])->name('patient-histories.create');
        Route::post('/patient-histories', [PatientHistoryController::class, 'store'])->name('patient-histories.store');
        Route::get('/patient-histories/{id}', [PatientHistoryController::class, 'show'])->name('patient-histories.show');
        Route::get('/patient-histories/{id}/edit', [PatientHistoryController::class, 'edit'])->name('patient-histories.edit');
        Route::put('/patient-histories/{id}', [PatientHistoryController::class, 'update'])->name('patient-histories.update');
        Route::delete('/patient-histories/{id}', [PatientHistoryController::class, 'destroy'])->name('patient-histories.destroy');

        // Amostras
        Route::get('/samples', [SampleController::class, 'index'])->name('samples.index');
        Route::get('/samples/create', [SampleController::class, 'create'])->name('samples.create');
        Route::post('/samples', [SampleController::class, 'store'])->name('samples.store');
        Route::get('/samples/{id}', [SampleController::class, 'show'])->name('samples.show');
        Route::get('/samples/{id}/edit', [SampleController::class, 'edit'])->name('samples.edit');
        Route::put('/samples/{id}', [SampleController::class, 'update'])->name('samples.update');
        Route::delete('/samples/{id}', [SampleController::class, 'destroy'])->name('samples.destroy');

        // Exames
        Route::get('/exam', [ExamController::class, 'index'])->name('exam.index');
        Route::get('/exam/create', [ExamController::class, 'create'])->name('exam.create');
        Route::post('/exam', [ExamController::class, 'store'])->name('exam.store');
        Route::get('/exam/{id}', [ExamController::class, 'show'])->name('exam.show');
        Route::get('/exam/{id}/edit', [ExamController::class, 'edit'])->name('exam.edit');
        Route::put('/exam/{id}', [ExamController::class, 'update'])->name('exam.update');
        Route::post('/exam/{id}/approve', [ExamController::class, 'approve'])->name('exam.approve');
        Route::post('/exam/{id}/reject', [ExamController::class, 'reject'])->name('exam.reject');
        Route::delete('/exam/{id}', [ExamController::class, 'destroy'])->name('exam.destroy');
    });

    Route::middleware(['role:patient'])->group(function () {
        Route::get('/my-exams', [PatientExamController::class, 'index'])->name('patient-exams.index');
        Route::get('/my-exams/{id}/pdf', [PatientExamController::class, 'exportPdf'])->name('patient-exams.pdf');
        Route::post('/my-exams/{id}/feedback', [PatientExamController::class, 'storeFeedback'])->name('patient-exams.feedback.store');
        Route::get('/my-exams/{id}', [PatientExamController::class, 'show'])->name('patient-exams.show');
    });
});
