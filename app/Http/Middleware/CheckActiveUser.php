<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if (! $user->active) {
                Auth::logout();

                return redirect()->route('login')->withErrors([
                    'email' => 'Sua conta foi desativada. Entre em contato com o administrador.',
                ]);
            }
        }

        return $next($request);
    }
}
