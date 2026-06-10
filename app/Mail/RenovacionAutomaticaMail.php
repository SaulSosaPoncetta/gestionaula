<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\PagoOnline;

class RenovacionAutomaticaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PagoOnline $pagoOnline) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Renovacion automatica procesada — GestionAula');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.renovacion_automatica');
    }
}