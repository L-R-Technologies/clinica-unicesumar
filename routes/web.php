<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');


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
});
