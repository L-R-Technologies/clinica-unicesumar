<?php

namespace App\Notifications;

use App\Models\Exam;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExamRejectedNotification extends Notification
{
    protected $exam;

    protected $justification;

    /**
     * Create a new notification instance.
     */
    public function __construct(Exam $exam, string $justification = '')
    {
        $this->exam = $exam;
        $this->justification = $justification;
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
        $message = (new MailMessage())
            ->subject('Exame Rejeitado - '.$this->exam->examType->name)
            ->greeting('Olá, '.$notifiable->name.'!')
            ->line('Seu exame foi **rejeitado** e precisa ser corrigido.')
            ->line('**Tipo de Exame:** '.$this->exam->examType->name)
            ->line('**Data do Exame:** '.$this->exam->date->format('d/m/Y H:i'))
            ->line('**Paciente:** '.$this->exam->patient->user->name);

        if ($this->justification) {
            $message->line('**Justificativa da Rejeição:**')
                ->line($this->justification);
        }

        return $message
            ->action('Visualizar Exame', route('exam.show', $this->exam->id))
            ->line('Por favor, revise o exame e reenvie para aprovação.')
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
            'justification' => $this->justification,
        ];
    }
}
