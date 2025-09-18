<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserManagementController;
use App\Http\Livewire\Samples\CreateSample;
use App\Http\Livewire\Samples\SampleList;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'welcome'])->name('welcome');
Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy-policy');

// Grupo de rotas que exigem que o usuário esteja autenticado, verificado e ativo
Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::get('/home', [HomeController::class, 'home'])->name('home');

    // ROTA PARA O CRUD DE AMOSTRAS
    Route::get('/samples', SampleList::class)->name('samples.index');
    Route::get('/samples/create', CreateSample::class)->name('samples.create');
    Route::get('/samples/{sample}', \App\Http\Livewire\Samples\ShowSample::class)->name('samples.show');
    Route::get('/samples/{sample}/edit', \App\Http\Livewire\Samples\EditSample::class)->name('samples.edit');

    // Rotas para gerenciamento de usuários (apenas para professores)
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
    });

    // Rotas para o perfil do próprio usuário
    Route::get('/user/{user}', [UserController::class, 'edit'])->name('user.edit');
    Route::get('/user/password/{user}', [UserController::class, 'editPassword'])->name('user.password-edit');

    // Exemplos de rotas com middleware de validação de tipo de usuário

    // Rotas apenas para professores
    // Route::middleware(['role:teacher'])->group(function () {
    //     Route::get('/rota', [Controller::class, 'funcao']);
    // });

    // // Rotas apenas para estudantes
    // Route::middleware(['role:student'])->group(function () {
    //     Route::get('/rota', [Controller::class, 'funcao']);
    // });

    // // Rotas apenas para pacientes
    // Route::middleware(['role:patient'])->group(function () {
    //     Route::get('/rota', [Controller::class, 'funcao']);
    // });

    // // Rotas que permitem múltiplos tipos de usuário
    // Route::middleware(['role:teacher,student'])->group(function () {
    //     Route::get('/rota', [Controller::class, 'funcao']);
    // });
});
