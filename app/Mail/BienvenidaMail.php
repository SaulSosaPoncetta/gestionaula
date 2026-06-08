<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Plan;

class BienvenidaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public ?Plan $plan,
        public string $activationUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Bienvenido a GestionAula — Activa tu cuenta');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.bienvenida');
    }
}