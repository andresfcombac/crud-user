<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EnviarCodigoOtp extends Notification
{
    use Queueable;

    protected $codigo;

    public function __construct($codigo)
    {
        $this->codigo = $codigo;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Tu código de acceso de doble factor')
                    ->greeting('¡Hola, ' . $notifiable->nombres . '!')
                    ->line('Has solicitado iniciar sesión. Para completar el acceso, introduce el siguiente código de un solo uso:')
                    ->line('**' . $this->codigo . '**')
                    ->line('Este código tiene una validez estricta de 10 minutos.')
                    ->line('Si tú no solicitaste este acceso, por favor ignora este mensaje.');
    }
}
