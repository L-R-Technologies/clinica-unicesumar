<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function edit(User $user)
    {
        return view('auth.update-profile', ['user' => $user]);
    }

    public function editPassword(User $user)
    {
        return view('auth.update-password', ['user' => $user]);
    }
}
