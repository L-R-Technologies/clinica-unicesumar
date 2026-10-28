<?php

namespace Database\Factories;

use App\Models\LabAgent;
use App\Models\Machine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LabAgent>
 */
class LabAgentFactory extends Factory
{
    protected $model = LabAgent::class;

    public const PLAIN_TOKEN = 'test-agent-token';

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'machine_id' => Machine::factory(),
            'name' => 'Agente bancada 1',
            'token_hash' => LabAgent::hashToken(self::PLAIN_TOKEN),
            'token_prefix' => substr(self::PLAIN_TOKEN, 0, LabAgent::TOKEN_PREFIX_LENGTH),
            'active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['active' => false]);
    }
}
