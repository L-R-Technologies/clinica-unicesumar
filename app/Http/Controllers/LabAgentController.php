<?php

namespace App\Http\Controllers;

use App\Models\LabAgent;
use App\Models\Machine;
use App\Service\LabCapture\LabAgentService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LabAgentController extends Controller
{
    public const FLASH_TOKEN_KEY = 'lab_agent_token';

    public function __construct(private readonly LabAgentService $labAgentService)
    {
        $this->middleware('auth');
        $this->middleware('role:teacher');
    }

    public function store(Request $request, Machine $machine): RedirectResponse
    {
        try {
            $validated = $this->labAgentService->validateAgentData($request->all());
            $result = $this->labAgentService->createAgent($machine, $validated['name']);

            return redirect()
                ->route('machines.show', $machine->id)
                ->with('success', 'Agente criado. Copie o token agora: ele não será exibido novamente.')
                ->with(self::FLASH_TOKEN_KEY, $this->tokenFlash($result['agent'], $result['plain_token']));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            Log::error('Erro ao criar agente de captura', ['machine_id' => $machine->id, 'exception' => $e]);

            return back()->with('error', 'Não foi possível criar o agente. Tente novamente.');
        }
    }

    public function regenerateToken(LabAgent $labAgent): RedirectResponse
    {
        try {
            $plainToken = $this->labAgentService->regenerateToken($labAgent);

            return redirect()
                ->route('machines.show', $labAgent->machine_id)
                ->with('success', 'Token regenerado. O token anterior deixou de funcionar.')
                ->with(self::FLASH_TOKEN_KEY, $this->tokenFlash($labAgent, $plainToken));
        } catch (Exception $e) {
            Log::error('Erro ao regenerar token do agente', ['lab_agent_id' => $labAgent->id, 'exception' => $e]);

            return back()->with('error', 'Não foi possível regenerar o token. Tente novamente.');
        }
    }

    public function toggleStatus(LabAgent $labAgent): RedirectResponse
    {
        $agent = $this->labAgentService->toggleStatus($labAgent);

        return back()->with('success', $agent->active ? 'Agente ativado.' : 'Agente desativado. Ele não consegue mais enviar capturas.');
    }

    public function destroy(LabAgent $labAgent): RedirectResponse
    {
        try {
            $this->labAgentService->deleteAgent($labAgent);

            return back()->with('success', 'Agente removido. As capturas já recebidas foram removidas junto.');
        } catch (Exception $e) {
            Log::error('Erro ao remover agente de captura', ['lab_agent_id' => $labAgent->id, 'exception' => $e]);

            return back()->with('error', 'Não foi possível remover o agente. Tente novamente.');
        }
    }

    /**
     * @return array{agent_id: int, agent_name: string, token: string}
     */
    private function tokenFlash(LabAgent $agent, string $plainToken): array
    {
        return ['agent_id' => $agent->id, 'agent_name' => $agent->name, 'token' => $plainToken];
    }
}
