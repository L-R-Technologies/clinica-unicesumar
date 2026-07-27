<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * ERS (RF023): encerra automaticamente sessões inativas após o tempo limite.
 *
 * Diferente do SESSION_LIFETIME (que apenas expira o cookie), este middleware
 * força o logout no servidor quando o usuário fica inativo por mais tempo que
 * o limite configurado em session.idle_timeout (minutos).
 */
class CheckSessionTimeout
{
    private const LAST_ACTIVITY_KEY = 'last_activity_at';

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $timeoutSeconds = (int) config('session.idle_timeout', 30) * 60;
            $lastActivity = $request->session()->get(self::LAST_ACTIVITY_KEY);

            if ($lastActivity !== null && (time() - $lastActivity) > $timeoutSeconds) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'Sua sessão expirou por inatividade. Faça login novamente.',
                ]);
            }

            $request->session()->put(self::LAST_ACTIVITY_KEY, time());
        }

        return $next($request);
    }
}
