<?php

namespace App\Notifications;

use App\Models\Suggestion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NouvelleSuggestionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Suggestion $suggestion) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $type = ucfirst($this->suggestion->type);
        $prioriteBadge = $this->suggestion->priorite === 'haute' ? '[PRIORITE HAUTE] ' : '';

        return (new MailMessage)
            ->subject("{$prioriteBadge}Nouvelle {$type} — Réf. {$this->suggestion->reference}")
            ->greeting("Bonjour,")
            ->line("Une nouvelle {$this->suggestion->type} a été soumise.")
            ->line("**Référence :** {$this->suggestion->reference}")
            ->line("**Service :** {$this->suggestion->service->nom}")
            ->line("**Type :** {$type}")
            ->line("**Priorité :** " . ucfirst($this->suggestion->priorite))
            ->line("**Auteur :** {$this->suggestion->nom_affichage}")
            ->lineIf(! $this->suggestion->anonyme, "**Téléphone :** {$this->suggestion->telephone}")
            ->line("**Message :**")
            ->line($this->suggestion->message)
            ->action('Voir dans l\'administration', url('/admin/suggestions/' . $this->suggestion->id))
            ->salutation("SmartSuggest QR");
    }
}
