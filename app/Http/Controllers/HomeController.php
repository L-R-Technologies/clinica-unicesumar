<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function welcome()
    {
        return view('welcome');
    }

    public function privacyPolicy(): Response
    {
        return Inertia::render('privacy-policy');
    }

    public function home()
    {
        return view('home');
    }
}
