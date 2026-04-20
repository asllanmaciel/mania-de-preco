<?php

namespace App\Notifications;

use App\Models\ChamadoSuporte;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChamadoSuporteAbertoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly ChamadoSuporte $chamado)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Recebemos seu chamado {$this->chamado->protocolo}")
            ->greeting("Olá, {$this->chamado->nome}.")
            ->line("Seu chamado foi aberto com o protocolo {$this->chamado->protocolo}.")
            ->line("Assunto: {$this->chamado->assunto}")
            ->line('Nossa equipe recebeu o contexto e vai priorizar o atendimento conforme categoria, urgência e impacto informado.')
            ->line('Guarde este protocolo para acompanhar ou complementar a solicitação.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'protocolo' => $this->chamado->protocolo,
            'assunto' => $this->chamado->assunto,
            'categoria' => $this->chamado->categoria,
            'prioridade' => $this->chamado->prioridade,
        ];
    }
}
