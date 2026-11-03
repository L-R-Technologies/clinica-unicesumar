<?php

namespace App\Http\Middleware;

use App\Models\LabAgent;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateLabAgent
{
    public const REQUEST_ATTRIBUTE = 'lab_agent';

    private const LAST_SEEN_REFRESH_SECONDS = 60;

    public function handle(Request $request, Closure $next): Response
    {
        $plainToken = $request->bearerToken();
        $agent = $plainToken ? LabAgent::findByPlainToken($plainToken) : null;

        if (! $agent) {
            Log::warning('Acesso a rota de agente com token inválido', [
                'route' => $request->route()?->getName() ?? $request->path(),
                'ip' => $request->ip(),
            ]);

            return response()->json(['message' => 'Token do agente inválido.'], Response::HTTP_UNAUTHORIZED);
        }

        if (! $agent->active) {
            return response()->json(['message' => 'Este agente está inativo.'], Response::HTTP_FORBIDDEN);
        }

        $this->touchLastSeen($agent, $request->query('agent_version') ?? $request->input('agent_version'));
        $request->attributes->set(self::REQUEST_ATTRIBUTE, $agent);

        return $next($request);
    }

    private function touchLastSeen(LabAgent $agent, mixed $agentVersion): void
    {
        $isStale = $agent->last_seen_at === null
            || $agent->last_seen_at->diffInSeconds(now()) >= self::LAST_SEEN_REFRESH_SECONDS;
        $version = is_string($agentVersion) ? mb_substr($agentVersion, 0, 20) : null;
        $hasNewVersion = $version !== null && $version !== $agent->agent_version;

        if (! $isStale && ! $hasNewVersion) {
            return;
        }

        $agent->forceFill([
            'last_seen_at' => now(),
            'agent_version' => $version ?? $agent->agent_version,
        ])->saveQuietly();
    }
}
