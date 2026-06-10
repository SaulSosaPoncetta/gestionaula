<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Suscripcion;

class RecordatorioPagoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Suscripcion $suscripcion) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Recordatorio de pago — GestionAula');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.recordatorio_pago');
    }
}