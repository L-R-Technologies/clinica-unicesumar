<?php

namespace App\Notifications;

use App\Models\Exam;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExamApprovedNotification extends Notification
{
    protected $exam;

    /**
     * Create a new notification instance.
     */
    public function __construct(Exam $exam)
    {
        $this->exam = $exam;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Exame Aprovado - '.$this->exam->examType->name)
            ->greeting('Olá, '.$notifiable->name.'!')
            ->line('Seu exame foi **aprovado** com sucesso.')
            ->line('**Tipo de Exame:** '.$this->exam->examType->name)
            ->line('**Data do Exame:** '.$this->exam->date->format('d/m/Y H:i'))
            ->line('**Paciente:** '.$this->exam->patient->user->name)
            ->action('Visualizar Exame', route('exam.show', $this->exam->id))
            ->line('Obrigado por utilizar nosso sistema!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'exam_id' => $this->exam->id,
            'exam_type' => $this->exam->examType->name,
            'patient' => $this->exam->patient->user->name,
        ];
    }
}
