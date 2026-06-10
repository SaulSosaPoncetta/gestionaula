<?php
namespace App\Console\Commands;

use App\Mail\RecordatorioPagoMail;
use App\Models\Suscripcion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class EnviarRecordatoriosPago extends Command
{
    protected $signature   = 'pagos:recordatorios';
    protected $description = 'Envia recordatorios de pago 7 dias antes del vencimiento';

    public function handle(): void
    {
        $fechaLimite = Carbon::now()->addDays(7)->toDateString();
        $hoy         = Carbon::now()->toDateString();

        // Suscripciones activas que vencen en exactamente 7 dias
        $suscripciones = Suscripcion::with(['user', 'plan'])
            ->where('estado', 'activa')
            ->whereDate('proximopago', $fechaLimite)
            ->get();

        foreach ($suscripciones as $sus) {
            if (!$sus->user || !$sus->user->email) continue;

            Mail::to($sus->user->email)->send(new RecordatorioPagoMail($sus));
            $this->info("Recordatorio enviado a: {$sus->user->email}");
        }

        $this->info("Total recordatorios enviados: {$suscripciones->count()}");
    }
}