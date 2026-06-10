<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Pago;

class ConfirmacionPagoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Pago $pago) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Pago confirmado — GestionAula');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.confirmacion_pago');
    }
}