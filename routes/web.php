<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy-policy');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/home', function () {
        return view('home');
    })->name('home');

    Route::get('/user/{user}', function (User $user) {
        $view = 'auth.update-profile';
        if ($user->role === 'teacher') {
            $view = 'auth.update-profile-teacher';
        } elseif ($user->role === 'student') {
            $view = 'auth.update-profile-student';
        } elseif ($user->role === 'patient') {
            $view = 'auth.update-profile-patient';
        }
        return view($view, ['user' => $user]);
    })->name('user.edit');

    Route::get('/user/password/{user}', function (User $user) {
        return view('auth.update-password', ['user' => $user]);
    })->name('user.password-edit');

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
