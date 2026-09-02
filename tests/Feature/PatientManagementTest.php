<?php

namespace Tests\Feature;

use App\Mail\PatientAccountCreated;
use App\Models\Patient;
use App\Models\User;
use App\Service\PatientService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PatientManagementTest extends TestCase
{
    use RefreshDatabase;

    private const VALID_CPF_DIGITS = '52998224725';

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake(PatientService::LGPD_TERM_DISK);
        Mail::fake();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPatientPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Maria da Silva',
            'email' => 'maria.silva@example.com',
            'birthday' => '1990-05-10',
            'ethnicity' => 'Parda',
            'sex' => 'female',
            'cpf' => '529.982.247-25',
            'rg' => 'MG1234567',
            'phone' => '(44) 99999-8888',
            'street' => 'Rua das Flores',
            'number' => '100',
            'complement' => 'Apto 2',
            'neighborhood' => 'Centro',
            'city' => 'Maringá',
            'state' => 'Paraná',
            'country' => 'Brasil',
            'zip_code' => '87000-000',
            'lgpd_term' => UploadedFile::fake()->create('termo.jpg', 200, 'image/jpeg'),
        ], $overrides);
    }

    private function registerPatientAs(User $staff): Patient
    {
        $this->actingAs($staff)
            ->post(route('patients.store'), $this->validPatientPayload())
            ->assertRedirect(route('patients.index'));

        return Patient::where('cpf', self::VALID_CPF_DIGITS)->firstOrFail();
    }

    public function test_teacher_can_register_patient_with_lgpd_term(): void
    {
        $patient = $this->registerPatientAs(User::factory()->teacher()->create());

        $this->assertDatabaseHas('users', ['email' => 'maria.silva@example.com', 'role' => 'patient']);
        $this->assertSame('44999998888', $patient->phone);
        $this->assertNotNull($patient->address_id);
        $this->assertNotNull($patient->lgpd_consent_at);
        $this->assertNotNull($patient->lgpd_term_path);
        Storage::disk(PatientService::LGPD_TERM_DISK)->assertExists($patient->lgpd_term_path);
    }

    public function test_patient_receives_temporary_password_by_email(): void
    {
        $patient = $this->registerPatientAs(User::factory()->teacher()->create());

        Mail::assertSent(PatientAccountCreated::class, function (PatientAccountCreated $mail) use ($patient): bool {
            return $mail->hasTo('maria.silva@example.com')
                && $mail->user->is($patient->user)
                && Hash::check($mail->temporaryPassword, $patient->user->password);
        });
    }

    public function test_no_email_is_sent_when_registration_fails(): void
    {
        $teacher = User::factory()->teacher()->create();
        $payload = $this->validPatientPayload(['cpf' => '111.111.111-11']);

        $this->actingAs($teacher)
            ->post(route('patients.store'), $payload)
            ->assertSessionHasErrors('cpf');

        Mail::assertNothingSent();
    }

    public function test_student_can_register_patient(): void
    {
        $patient = $this->registerPatientAs(User::factory()->student()->create());

        $this->assertSame('patient', $patient->user->role);
    }

    public function test_lgpd_term_is_required_when_registering(): void
    {
        $teacher = User::factory()->teacher()->create();
        $payload = $this->validPatientPayload();
        unset($payload['lgpd_term']);

        $this->actingAs($teacher)
            ->from(route('patients.create'))
            ->post(route('patients.store'), $payload)
            ->assertRedirect(route('patients.create'))
            ->assertSessionHasErrors('lgpd_term');

        $this->assertDatabaseMissing('users', ['email' => 'maria.silva@example.com']);
    }

    public function test_patient_cannot_register_other_patients(): void
    {
        $patient = User::factory()->patient()->create();

        $this->actingAs($patient)
            ->get(route('patients.index'))
            ->assertForbidden();

        $this->actingAs($patient)
            ->post(route('patients.store'), $this->validPatientPayload())
            ->assertForbidden();
    }

    public function test_staff_can_view_stored_lgpd_term(): void
    {
        $teacher = User::factory()->teacher()->create();
        $patient = $this->registerPatientAs($teacher);

        $this->actingAs($teacher)
            ->get(route('patients.lgpd-term', $patient->id))
            ->assertOk();
    }

    public function test_lgpd_term_returns_not_found_when_missing(): void
    {
        $teacher = User::factory()->teacher()->create();
        $patient = Patient::factory()->create();

        $this->actingAs($teacher)
            ->get(route('patients.lgpd-term', $patient->id))
            ->assertNotFound();
    }

    public function test_updating_patient_replaces_lgpd_term(): void
    {
        $teacher = User::factory()->teacher()->create();
        $patient = $this->registerPatientAs($teacher);
        $previousTermPath = $patient->lgpd_term_path;

        $payload = $this->validPatientPayload([
            'name' => 'Maria da Silva Souza',
            'lgpd_term' => UploadedFile::fake()->create('termo-novo.pdf', 200, 'application/pdf'),
        ]);

        $this->actingAs($teacher)
            ->put(route('patients.update', $patient->id), $payload)
            ->assertRedirect(route('patients.show', $patient->id));

        $patient->refresh();

        $this->assertSame('Maria da Silva Souza', $patient->user->name);
        $this->assertNotSame($previousTermPath, $patient->lgpd_term_path);
        Storage::disk(PatientService::LGPD_TERM_DISK)->assertMissing($previousTermPath);
        Storage::disk(PatientService::LGPD_TERM_DISK)->assertExists($patient->lgpd_term_path);
    }
}
