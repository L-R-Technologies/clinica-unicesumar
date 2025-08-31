<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserManagementController;

Route::get('/', [HomeController::class, 'welcome'])->name('welcome');
Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy-policy');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/home', [HomeController::class, 'home'])->name('home');

    Route::middleware(['role:teacher'])->group(function () {
        Route::get('/user/management', [UserManagementController::class, 'index'])->name('user-management.index');
    });

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
