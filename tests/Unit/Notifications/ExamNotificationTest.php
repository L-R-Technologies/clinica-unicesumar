<?php

namespace Tests\Unit\Notifications;

use App\Models\Exam;
use App\Models\ExamType;
use App\Models\Patient;
use App\Models\User;
use App\Notifications\ExamApprovedNotification;
use App\Notifications\ExamRejectedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Tests\TestCase;

class ExamNotificationTest extends TestCase
{
    public function test_approved_notification_is_sent_through_mail_without_queueing(): void
    {
        $exam = new Exam(['id' => 1, 'date' => now()]);
        $exam->setRelation('examType', new ExamType(['name' => 'Hemograma']));
        $exam->setRelation('patient', new Patient());
        $exam->patient->setRelation('user', new User(['name' => 'João']));

        $notification = new ExamApprovedNotification($exam);
        $notifiable = new class
        {
            public $name = 'Maria';
            public $email = 'maria@example.com';
        };

        $this->assertNotInstanceOf(ShouldQueue::class, $notification);
        $this->assertSame(['mail'], $notification->via($notifiable));
    }

    public function test_rejected_notification_builds_a_mail_message_with_the_justification(): void
    {
        $exam = new Exam(['date' => now()]);
        $exam->id = 2;
        $exam->setRelation('examType', new ExamType(['name' => 'Urina']));
        $exam->setRelation('patient', new Patient());
        $exam->patient->setRelation('user', new User(['name' => 'Ana']));

        $notification = new ExamRejectedNotification($exam, 'O exame precisa ser refeito.');
        $notifiable = new class
        {
            public $name = 'Pedro';
            public $email = 'pedro@example.com';
        };

        $message = $notification->toMail($notifiable);

        $this->assertSame('Exame Rejeitado - Urina', $message->subject);
        $this->assertContains('Seu exame foi **rejeitado** e precisa ser corrigido.', $message->introLines);
        $this->assertContains('O exame precisa ser refeito.', $message->introLines);
    }
}
