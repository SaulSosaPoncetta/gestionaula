<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nombre,
        public string $email,
        public string $telefono,
        public string $mensaje
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Confirmacion de contacto — GestionAula');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.contacto');
    }
}