<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Middleware\AuthenticateLabAgent;
use App\Models\LabAgent;
use App\Service\LabCapture\LabCaptureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AgentCaptureController extends Controller
{
    public function __construct(private readonly LabCaptureService $labCaptureService) {}

    public function ping(Request $request): JsonResponse
    {
        $agent = $this->agentFrom($request);
        $agent->loadMissing('machine');

        return response()->json([
            'data' => [
                'agent' => ['id' => $agent->id, 'name' => $agent->name],
                'machine' => $agent->machine
                    ? $agent->machine->only(['id', 'name', 'model', 'serial_number'])
                    : null,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $agent = $this->agentFrom($request);
        $payload = $this->labCaptureService->validateAgentPayload($request->all());
        $result = $this->labCaptureService->storeFromAgent($agent, $payload);

        return response()->json([
            'data' => [
                'id' => $result['capture']->id,
                'status' => $result['capture']->status,
                'duplicate' => $result['duplicate'],
            ],
        ], $result['duplicate'] ? Response::HTTP_OK : Response::HTTP_CREATED);
    }

    private function agentFrom(Request $request): LabAgent
    {
        $agent = $request->attributes->get(AuthenticateLabAgent::REQUEST_ATTRIBUTE);

        if (! $agent instanceof LabAgent) {
            abort(Response::HTTP_UNAUTHORIZED, 'Agente não autenticado.');
        }

        return $agent;
    }
}
