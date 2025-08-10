<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/home', function () {
        return view('home');
    })->name('home');

    Route::get('/user/{user}', function (User $user) {
        return view('auth.update-profile', ['user' => $user]);
    })->name('user.edit');

    Route::get('/user/password/{user}', function (User $user) {
        return view('auth.update-password', ['user' => $user]);
    })->name('user.password-edit');
});
