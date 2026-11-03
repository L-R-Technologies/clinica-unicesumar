<?php

namespace App\Service\LabCapture;

use App\Models\LabAgent;
use App\Models\Machine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LabAgentService
{
    /**
     * @param  array<string, mixed>  $data
     * @return array{name: string}
     *
     * @throws ValidationException
     */
    public function validateAgentData(array $data): array
    {
        return Validator::make($data, [
            'name' => 'required|string|max:100',
        ], [
            'name.required' => 'Informe um nome para o agente (ex.: computador da bancada 1).',
        ])->validate();
    }

    /**
     * @return array{agent: LabAgent, plain_token: string}
     */
    public function createAgent(Machine $machine, string $name): array
    {
        $plainToken = LabAgent::generatePlainToken();

        $agent = $machine->labAgents()->create([
            'name' => $name,
            'token_hash' => LabAgent::hashToken($plainToken),
            'token_prefix' => substr($plainToken, 0, LabAgent::TOKEN_PREFIX_LENGTH),
            'active' => true,
        ]);

        return ['agent' => $agent, 'plain_token' => $plainToken];
    }

    public function regenerateToken(LabAgent $agent): string
    {
        $plainToken = LabAgent::generatePlainToken();

        $agent->update([
            'token_hash' => LabAgent::hashToken($plainToken),
            'token_prefix' => substr($plainToken, 0, LabAgent::TOKEN_PREFIX_LENGTH),
        ]);

        return $plainToken;
    }

    public function toggleStatus(LabAgent $agent): LabAgent
    {
        $agent->update(['active' => ! $agent->active]);

        return $agent;
    }

    public function deleteAgent(LabAgent $agent): void
    {
        DB::transaction(fn () => $agent->delete());
    }
}
