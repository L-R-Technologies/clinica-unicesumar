<?php

namespace Tests\Feature\Auth;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EmailVerificationNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_the_email_verification_notification_when_a_user_registers(): void
    {
        Notification::fake();

        $user = (new CreateNewUser())->create([
            'name' => 'Maria',
            'email' => 'maria@example.com',
            'password' => 'Zx8kQwPmT3rL',
            'password_confirmation' => 'Zx8kQwPmT3rL',
            'birthday' => '1990-01-01',
            'ethnicity' => 'Branca',
            'sex' => 'female',
            'cpf' => '12345678909',
            'rg' => '1234567',
            'phone' => '11999998888',
            'lgpd_consent' => true,
            'street' => 'Rua das Flores',
            'number' => '123',
            'complement' => 'Apto 1',
            'neighborhood' => 'Centro',
            'city' => 'São Paulo',
            'state' => 'SP',
            'country' => 'Brasil',
            'zip_code' => '01000000',
        ]);

        Notification::assertSentTo($user, \Illuminate\Auth\Notifications\VerifyEmail::class);
    }
}
