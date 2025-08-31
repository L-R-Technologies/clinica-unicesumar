<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function edit(User $user)
    {
        $view = 'auth.update-profile';

        if ($user->role === 'teacher') {
            $view = 'auth.update-profile-teacher';
        } elseif ($user->role === 'student') {
            $view = 'auth.update-profile-student';
        } elseif ($user->role === 'patient') {
            $view = 'auth.update-profile-patient';
        }

        return view($view, ['user' => $user]);
    }

    public function editPassword(User $user)
    {
        return view('auth.update-password', ['user' => $user]);
    }
}
