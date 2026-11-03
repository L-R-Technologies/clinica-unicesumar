<?php

namespace Tests\Feature;

use App\Http\Controllers\LabAgentController;
use App\Models\LabAgent;
use App\Models\Machine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LabAgentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_creates_agent_and_receives_token_once(): void
    {
        $teacher = User::factory()->teacher()->create();
        $machine = Machine::factory()->create();

        $response = $this->actingAs($teacher)
            ->post(route('lab-agents.store', $machine->id), ['name' => 'PC da bancada'])
            ->assertRedirect(route('machines.show', $machine->id))
            ->assertSessionHas(LabAgentController::FLASH_TOKEN_KEY);

        $flash = $response->getSession()->get(LabAgentController::FLASH_TOKEN_KEY);
        $agent = LabAgent::firstOrFail();

        $this->assertSame($agent->id, $flash['agent_id']);
        $this->assertSame(LabAgent::TOKEN_LENGTH, strlen($flash['token']));
        $this->assertSame(LabAgent::hashToken($flash['token']), $agent->token_hash);
        $this->assertStringStartsWith($agent->token_prefix, $flash['token']);
        $this->assertNotSame($flash['token'], $agent->token_hash);
    }

    public function test_regenerating_token_invalidates_the_previous_one(): void
    {
        $teacher = User::factory()->teacher()->create();
        $agent = LabAgent::factory()->create();
        $oldHash = $agent->token_hash;

        $response = $this->actingAs($teacher)
            ->post(route('lab-agents.regenerate-token', $agent->id))
            ->assertRedirect(route('machines.show', $agent->machine_id));

        $newToken = $response->getSession()->get(LabAgentController::FLASH_TOKEN_KEY)['token'];
        $agent->refresh();

        $this->assertNotSame($oldHash, $agent->token_hash);
        $this->assertSame(LabAgent::hashToken($newToken), $agent->token_hash);
        $this->assertNull(LabAgent::findByPlainToken('test-agent-token'));
    }

    public function test_toggle_status_and_delete(): void
    {
        $teacher = User::factory()->teacher()->create();
        $agent = LabAgent::factory()->create();

        $this->actingAs($teacher)->patch(route('lab-agents.toggle-status', $agent->id))->assertRedirect();
        $this->assertFalse($agent->fresh()->active);

        $this->actingAs($teacher)->delete(route('lab-agents.destroy', $agent->id))->assertRedirect();
        $this->assertDatabaseMissing('lab_agents', ['id' => $agent->id]);
    }

    public function test_students_cannot_manage_agents(): void
    {
        $student = User::factory()->student()->create();
        $agent = LabAgent::factory()->create();

        $this->actingAs($student)
            ->post(route('lab-agents.store', $agent->machine_id), ['name' => 'x'])
            ->assertForbidden();
        $this->actingAs($student)
            ->delete(route('lab-agents.destroy', $agent->id))
            ->assertForbidden();
    }
}
