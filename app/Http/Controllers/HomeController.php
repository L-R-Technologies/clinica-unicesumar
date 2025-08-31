<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function welcome()
    {
        return view('welcome');
    }

    public function privacyPolicy()
    {
        return view('privacy-policy');
    }

    public function home()
    {
        return view('home');
    }
}
